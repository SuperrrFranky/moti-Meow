// $(document).on('click', function (event) {
//   console.log("Clicked element:", event.target);
// });

$(document).ready(function () {
  const cat = $(".cat");
  const interactionContainer = $(".interactionContainer");
  const room = $(".room");
  const headLayer = $(".head-layer");
  const bodyLayer = $(".body-layer");
  const neckLayer = $(".neck-layer");

  // ----------------- Walkable area % -----------------
  const walkableArea = {
    xMin: 40, // %
    xMax: 75, // %
    yMin: 55, // %
    yMax: 75, // %
  };

  // ----------------- State Flags -----------------
  let isMoving = true;
  let isFeedMode = false;
  let isPlaying = false;
  let foodPosition = null;
  let timeoutId = null;

  // ----------------- Cat Random Movement -----------------
  function getRandomPositionInRoom() {
    const roomWidth = room.width();
    const roomHeight = room.height();
    const catWidth = cat.width();
    const catHeight = cat.height();

    const x =
      ((walkableArea.xMin +
        Math.random() * (walkableArea.xMax - walkableArea.xMin)) /
        100) *
        roomWidth -
      catWidth / 2;
    const y =
      ((walkableArea.yMin +
        Math.random() * (walkableArea.yMax - walkableArea.yMin)) /
        100) *
        roomHeight -
      catHeight / 2;

    return { left: x, top: y };
  }

  function moveCat() {
    if (!isMoving || isPlaying) return;

    const nextTarget = getRandomPositionInRoom();

    cat.animate(nextTarget, 2500, function () {
      if (isMoving && !isPlaying) {
        timeoutId = setTimeout(moveCat, 1000);
      }
    });
  }

  // ----------------- Cat Move to Food -----------------
  function moveCatToFood() {
    if (!foodPosition) return;

    const catLeft = parseFloat(cat.css("left"));
    const catTop = parseFloat(cat.css("top"));

    const deltaX = foodPosition.left - catLeft;
    const deltaY = foodPosition.top - catTop;
    const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);

    const speed = 0.13;
    const duration = distance / speed;

    cat.animate(
      {
        left: foodPosition.left + "px",
        top: foodPosition.top + "px",
      },
      duration,
      function () {
        checkIfCatReachedFood();
      }
    );
  }

  // ----------------- Enable Feed Mode -----------------
  function enableFeedMode() {
    isFeedMode = true;
    $("body").addClass("feed-mode"); // Change cursor
    isMoving = false;
    cat.stop(true);
    clearTimeout(timeoutId);

    // ✅ Bind the feed click again
    $(document)
      .off("click.feed")
      .on("click.feed", function (event) {
        const roomOffset = room.offset();
        const roomWidth = room.width();
        const roomHeight = room.height();

        const clickX = event.pageX - roomOffset.left;
        const clickY = event.pageY - roomOffset.top;

        const clickPercentX = (clickX / roomWidth) * 100;
        const clickPercentY = (clickY / roomHeight) * 100;

        if (
          clickPercentX < walkableArea.xMin ||
          clickPercentX > walkableArea.xMax ||
          clickPercentY < walkableArea.yMin ||
          clickPercentY > walkableArea.yMax
        ) {
          return; // Click outside walkable area, ignore
        }

        $(".food").remove();

        const food = $("<img>", {
          src: "/res/image/interaction/catfood.png",
          class: "food",
          css: {
            position: "absolute",
            width: "50px",
            height: "50px",
            left: clickX + "px",
            top: clickY + "px",
            transform: "translate(-50%, -50%)",
            zIndex: 1000,
          },
        });
        room.append(food);

        foodPosition = {
          left: clickX - cat.width() / 2,
          top: clickY - cat.height() / 2,
        };

        moveCatToFood();
      });
  }

  function checkIfCatReachedFood() {
    const catCenter = {
      x: cat.offset().left + cat.width() / 2,
      y: cat.offset().top + cat.height() / 2,
    };

    const food = $(".food");
    const foodCenter = {
      x: food.offset().left + food.width() / 2,
      y: food.offset().top + food.height() / 2,
    };

    const dx = catCenter.x - foodCenter.x;
    const dy = catCenter.y - foodCenter.y;
    const realDistance = Math.sqrt(dx * dx + dy * dy);

    const eatingRange = 75; // (px) allowed distance to start eating

    if (realDistance <= eatingRange) {
      // ✅ Stop random movement immediately
      isMoving = false;
      cat.stop(true); // Stop any current animation

      setTimeout(() => {
        disableFeedMode();
      }, 1500); // Wait for eating time
    } else {
      // Still far? Retry checking after small delay
      setTimeout(checkIfCatReachedFood, 300);
    }
  }

  // ----------------- Disable Feed Mode -----------------
  function disableFeedMode() {
    isFeedMode = false;
    $("body").removeClass("feed-mode");
    $(document).off("click.feed");
    $(".food").remove();

    isMoving = false;
    cat.stop(true); // Stop any current animation

    // Switch to eating animation
    cat.css({
      "background-image": "url('/res/image/cat/Eating.png')",
      animation: "_15-frame-cycle 1s steps(15) infinite forwards", // new keyframe for eating
    });

    //After eating for 5 seconds, return to walking
    setTimeout(() => {
      cat.css({
        "background-image": "url('/res/image/cat/walking.png')",
        animation: "_3-frame-cycle 1s steps(3) infinite forwards",
      });
      isMoving = true;
      moveCat();
    }, 5000);
  }

  // ----------------- Feed Click Behavior -----------------
  const feedBtn = $(".feed");
  feedBtn.on("click", function () {
    if (isFeedMode) return; // already in feed mode, do nothing

    enableFeedMode(); // only enable mode
  });

  // ----------------- Click Cat to Toggle Interaction -----------------
  const playAnimations = [
    { img: "/res/image/cat/Dance.png", frames: 4 },
    { img: "/res/image/cat/Excited.png", frames: 12 },
    { img: "/res/image/cat/LayDown.png", frames: 12 },
    { img: "/res/image/cat/Sleepy.png", frames: 8 },
    { img: "/res/image/cat/Surprised.png", frames: 12 },
  ];

  const playMessages = [
    "Let's have fun!",
    "Catch me if you can!",
    "I'm so excited! 😸",
    "Playtime is the best time!",
    "Yay! More toys!",
  ];

  let lastTapTime = 0;

  cat.off("click").on("click", function () {
    const currentTime = new Date().getTime();
    const tapGap = currentTime - lastTapTime;

    // 🔁 Detect double-tap to exit play mode
    if (isPlaying && tapGap < 300) {
      endPlayMode();
      lastTapTime = 0; // reset
      return;
    }

    lastTapTime = currentTime;

    // 🎮 Play mode behavior (single tap = animate cat)
    if (isPlaying) {
      headLayer.hide();
      bodyLayer.hide();
      neckLayer.hide();

      const randomPick =
        playAnimations[Math.floor(Math.random() * playAnimations.length)];

      const animationName = `_${randomPick.frames}-frame-cycle`;

      cat.css({
        "background-image": `url('${randomPick.img}')`,
        "background-size": `${72 * randomPick.frames}px 72px`,
        animation: `${animationName} 1s steps(${randomPick.frames}) infinite forwards`,
      });

      const randomMsg =
        playMessages[Math.floor(Math.random() * playMessages.length)];

      $(".play-chat-bubble").text(randomMsg).show();

      return; // prevent interaction bubble
    }

    // 🐾 Normal interaction mode
    if (!isFeedMode) {
      interactionContainer.fadeToggle(1000, function () {
        if (interactionContainer.is(":visible")) {
          isMoving = false;
          cat.stop(true);
          clearTimeout(timeoutId);

          setTimeout(function () {
            interactionContainer.fadeOut(200, function () {
              isMoving = true;
              moveCat();
            });
          }, 2000);
        }
      });
    }
  });

  // ----------------- Resize Behavior -----------------
  $(window).on("resize", function () {
    cat.css(getRandomPositionInRoom());
  });

  // ----------------- Decoration Positioning -----------------
  function repositionDecorations() {
    const rect = room[0].getBoundingClientRect();
    const decorations = room.find("[data-x][data-y]");

    decorations.each(function () {
      const x = parseFloat($(this).data("x"));
      const y = parseFloat($(this).data("y"));
      const left = (x / 100) * rect.width;
      const top = (y / 100) * rect.height;

      $(this).css({
        position: "absolute",
        left: `${left}px`,
        top: `${top}px`,
      });
    });
  }
  // ----------------- play with cat -----------------
  const playBtn = $(".click");

  playBtn.on("click", function () {
    if (isPlaying || isFeedMode) return;

    isPlaying = true;
    isMoving = false;
    clearTimeout(timeoutId);
    cat.stop(true, true);
    interactionContainer.fadeOut(200);

    $("body").addClass("play-mode");
    cat.css({
      cursor: "url('/res/image/interaction/CatToy.gif'), auto",
      "pointer-events": "auto",
    });
  });


  function endPlayMode() {
    $("body").removeClass("play-mode");
    $(".play-chat-bubble").hide();

    cat.css({
      "background-image": "url('/res/image/cat/walking.png')",
      "background-size": "cover",
      animation: "_3-frame-cycle 1s steps(3) infinite forwards",
      cursor: "pointer",
    });

    if (headLayer.attr("src")) headLayer.show();
    if (bodyLayer.attr("src")) bodyLayer.show();
    if (neckLayer.attr("src")) neckLayer.show();

    isPlaying = false;
    isMoving = true;
    moveCat();
  }

  $(window).on("load resize", repositionDecorations);

  // ----------------- Initialize -----------------
  cat.css(getRandomPositionInRoom());
  moveCat();
  repositionDecorations();
  if (headLayer.attr("src")) headLayer.show();
  if (bodyLayer.attr("src")) bodyLayer.show();
  if (neckLayer.attr("src")) neckLayer.show();
});

$(document).ready(function () {
  const chatBtn = $(".chat");
  const popupContent = $("#popup-content");

  chatBtn.off("click").on("click", function () {
    $.ajax({
      // ✅ Use jQuery's ajax, not chatBtn.ajax
      url: "back_end/presentation_layer/InteractionController/interactionController.php",
      method: "POST",
      data: { action: "startChat" },
      dataType: "json",
      success: function (response) {
        popupContent.append(response.html); // Insert returned HTML
        displayPopup(); // Show popup function
      },
      error: function () {
        alert("Failed to start chat. Please try again later.");
        $("#popupContent").html("Error loading content.");
        displayPopup();
      },
    });
  });
});

$(document).ready(function () {
  const popupContent = $("#popup-content");
  let isWaiting = false; // Flag to prevent multiple clicks

  // Listen to send button click
  $(document).on("click", "#chat-send-btn", function () {
    if (!isWaiting) {
      isWaiting = true; // Set flag to true
      sendMessage();
    }
  });

  $(document).on("keypress", "#chat-input", function (e) {
    if (e.which === 13 && !isWaiting) {
      isWaiting = true;
      sendMessage();
    }
  });

  function sendMessage() {
    const userInput = $("#chat-input").val().trim();
    const chatMessages = popupContent.find(".chat-messages");

    if (userInput === "") return;

    // Append user's message to chat
    chatMessages.append(`
          <div class="chat-bubble user-message">
              <div class="bubble-text">${userInput}</div>
          </div>
      `);
    $("#chat-input").val("");

    // Scroll to bottom
    chatMessages.scrollTop(chatMessages[0].scrollHeight);

    // Append typing indicator
    const typingIndicator = `
<div class="chat-bubble bot-message typing-indicator">
    <img src="/res/image/interaction/chatbot_avatar.png" alt="Cat" class="chat-avatar">
    <div class="bubble-text typing-dots">
        <span>.</span><span>.</span><span>.</span>
    </div>
</div>
`;
    chatMessages.append(typingIndicator);
    chatMessages.scrollTop(chatMessages[0].scrollHeight);

    // AJAX call to get chatbot reply
    $.ajax({
      url: "/back_end/presentation_layer/InteractionController/interactionController.php",
      method: "POST",
      data: { action: "getResponse", message: userInput },
      dataType: "json",
      success: function (response) {
        $(".typing-indicator").remove();
        const botReply = response.response;

        chatMessages.append(`
                <div class="chat-bubble bot-message">
                    <img src="/res/image/interaction/chatbot_avatar.png" alt="Cat" class="chat-avatar">
                    <div class="bubble-text">${botReply}</div>
                </div>
            `);
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
        isWaiting = false; // Reset flag after response
      },
      error: function () {
        alert("Failed to connect to chatbot.");
        isWaiting = false; // Reset flag on error
      },
    });
  }
});

//----Index Buttons----//
$(document).ready(function () {
  $("#toggleMenuBtn")
    .off("click")
    .on("click", function (e) {
      $("#buttonGroup").slideToggle(200);
      const currentText = $(this).text();
      $(this).text(currentText === "+" ? "x" : "+");
    });
});

function displayPopup() {
  const popup = $("#popup-window");

  popup.fadeIn(200);
  $("#popup-general-overlay").removeClass("hidden");
  $("#popup-window").removeClass("hidden");
}

function displayPopupProfile() {
  const popup = $("#popup-catProfile");

  popup.fadeIn(200);
  $("#popup-catP-overlay").removeClass("hidden");
  $("#popup-catProfile").removeClass("hidden");
}

function displayPopupDeco() {
  const popup = $("#popup-deco");

  popup.fadeIn(200);
  $("#popup-deco-overlay").removeClass("hidden");
  $("#popup-deco").removeClass("hidden");
}

function closePopup() {
  const popup = $("#popup-window");
  const popupContent = $("#popup-content");

  popup.fadeOut(200);
  popupContent.empty(); // Clear content when closing
  $("#popup-general-overlay").addClass("hidden");
  $("#popup-window").addClass("hidden");
}

function closePopup2() {
  const popup = $("#popup-catProfile");
  const popupContent = $("#popup-content-profile");

  popup.fadeOut(200);
  popupContent.empty(); // Clear content when closing
  $("#popup-catP-overlay").addClass("hidden");
  $("#popup-catProfile").addClass("hidden");
}

function closePopup3() {
  const popup = $("#popup-deco");
  const popupContent = $("#popup-content-deco");

  popup.fadeOut(200);
  popupContent.empty(); // Clear content when closing
  $("#popup-deco").addClass("hidden");
  $("#popup-deco-overlay").addClass("hidden");
}

$(document).on("click", "#show-accessory", function () {
  $.ajax({
    url: "/back_end/presentation_layer/CatProfileController/displayAccessory.php",
    type: "POST",
    dataType: "html",
    success: function (html) {
      closePopup2();
      $("#popup-content").html(html);
      displayPopup(); // make sure this function is defined somewhere
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(
        "Error fetching accessory inventory:",
        textStatus,
        errorThrown
      );
      console.log(jqXHR.responseText);
    },
  });
});

$(document)
  .off("click", "#show-homeDeco")
  .on("click", "#show-homeDeco", function () {
    $.ajax({
      url: "back_end/presentation_layer/HomeDecorationController/displayDecoration.php",
      type: "POST",
      dataType: "html",
      success: function (html) {
        closePopup3();
        $("#popup-content-deco").html(html);
        displayPopupDeco();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error(
          "Error fetching accessory inventory:",
          textStatus,
          errorThrown
        );
        console.log(jqXHR.responseText);
      },
    });
  });

$(document).on(
  "click",
  "#show-photoGallery, .photoGallery1, .photoGallery2, .photoGallery3",
  function () {
    $.ajax({
      url: "/back_end/presentation_layer/PhotoManagementController/photoGallery.php",
      type: "POST",
      dataType: "html",
      success: function (html) {
        $("#popup-content").html(html);
        displayPopup();

        $("#popup-content").on("click", "#add-photo-btn", function () {
          $.ajax({
            url: "/back_end/presentation_layer/PhotoManagementController/addPhoto.php",
            type: "POST",
            dataType: "html",
            success: function (formHtml) {
              $("#popup-content").html(formHtml);
              bindPhotoFormAjax(); 
            },
          });
        });
      },
    });
  }
);

// Bind AJAX submission logic to photo form
function bindPhotoFormAjax() {
  $(".form").off("submit").on("submit", function (e) {
    const form = this;
    const formData = new FormData(form);
    const description = form.querySelector("textarea[name='description']").value.trim();
    const fileInput = form.querySelector("input[type='file']");
    const errorMs = $("#errorMs");

    // Reset error message
    errorMs.text("");

    // Check if no file selected
    if (!fileInput.files.length) {
      e.preventDefault();
      errorMs.text("Please upload a photo.");
      alert("You must upload a photo before submitting.");
      return;
    }

    // Allow submission via AJAX if valid
    e.preventDefault();
    $.ajax({
      url: $(form).attr("action"),
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (responseHtml) {
        $("#popup-content").html(responseHtml);
        bindPhotoFormAjax(); // Re-bind for future use
      },
      error: function (xhr, status, err) {
        console.error("Photo upload failed:", status, err);
        errorMs.text("Upload failed. Try again.");
      },
    });
  });
}

$(document).on("click", "#catProfileIcon,#catName", function () {
  closePopup();
  $.ajax({
    url: "/back_end/presentation_layer/CatProfileController/catProfile.php",
    type: "POST",
    dataType: "html",
    success: function (html) {
      $("#popup-content-profile").html(html);
      displayPopupProfile();
    },

    error: function (jqXHR, textStatus, errorThrown) {
      console.error("Error fetching cat profile:", textStatus, errorThrown);
      console.log(jqXHR.responseText);
    },
  });
});

$(document).ready(function () {
  $(document)
    .off("click", ".delete-btn")
    .on("click", ".delete-btn", function () {
      const galleryId = $(this).data("id");
      if (!confirm("Are you sure you want to delete this photo?")) return;

      $.ajax({
        type: "POST",
        url: "/back_end/presentation_layer/PhotoManagementController/delete_photo.php",
        data: { galleryID: galleryId },
        success: function (response) {
          if (response.trim() === "success") {
            $(this).closest(".photo-box").remove();
          } else {
            alert("Delete failed.");
          }
        }.bind(this),
        error: function () {
          alert("Server error.");
        },
      });
    });
});

$(document).on("click", ".details-btn", function () {
  const galleryId = $(this).data("id");

  $.ajax({
    type: "POST",
    url: "/back_end/presentation_layer/PhotoManagementController/photoDetails.php",
    data: { galleryID: galleryId },
    dataType: "html",
    success: function (response) {
      $("#popup-content").html(response);
      $("#popup-window").fadeIn();
    },
    error: function () {
      alert("Failed to load photo details.");
    },
  });
});

$(document).on("click", ".photo-back-btn", function () {
  $.ajax({
    url: "/back_end/presentation_layer/PhotoManagementController/photoGallery.php",
    type: "POST",
    dataType: "html",
    success: function (html) {
      $("#popup-content").html(html);
    },
    error: function () {
      alert("Failed to reload gallery.");
    },
  });
});

$(document).on("keydown", function (e) {
  if ($("#popup-content").is(":visible")) {
    if (e.key === "Escape" || e.key === "ArrowLeft") {
      $(".photo-back-btn").click();
    }
  }
});

$(function () {
  let selectedItemID = null;
  let selectedItemName = null;
  let selectedItemType = null; // 'accessory' or 'decoration'

  $(document).on("click", ".item", function () {
    selectedItemID = $(this).data("item-id");
    selectedItemName = $(this).data("name");
    selectedItemType = $(this).data("type");
    $("#item-selection-message").text(
      `Do you want to equip ${selectedItemName}`
    );
    $("#confirmation-section").fadeIn();
  });

  // Confirm Selection
  $(document)
    .off("click", "#confirm-selection")
    .on("click", "#confirm-selection", function () {
      if (selectedItemID && selectedItemType) {
        console.log("Confirming selection...");
        let url = "";

        if (selectedItemType == "accessory") {
          url =
            "/back_end/business_logic_layer/CatProfileService/manageCatAccessory.php";
        } else if (selectedItemType == "decoration") {
          url =
            "back_end/business_logic_layer/HomeDecorationService/manageHomeDeco.php";
        }

        $.ajax({
          url: url,
          method: "POST",
          data: { itemID: selectedItemID },
          dataType: "json",
          success: function (data) {
            if (data.status === "success") {
              const category = data.category;
              const image = data.image;

              if (selectedItemType === "accessory") {
                const folderMap = {
                  head: "head accessories",
                  body: "body accessories",
                  neck: "neck accessories",
                };

                $(`.${category}-layer`).attr(
                  "src",
                  `/res/image/accessories/${folderMap[category]}/${image}`
                );
                $(`.${category}-layer`).css("display", "block");

                $(`.item[data-type="accessory"]`).each(function () {
                  const itemCategory = $(this).find("img").attr("src");
                  if (
                    itemCategory &&
                    itemCategory.includes(folderMap[category])
                  ) {
                    $(this).removeClass("equipped");
                  }
                });

                $(`.item[data-item-id="${selectedItemID}"]`).addClass(
                  "equipped"
                );
              } else if (selectedItemType === "decoration") {
                if (category === "room") {
                  $(".room").css({
                    "background-image": `url('res/image/roomDeco/room/${image}')`,
                    "background-repeat": "no-repeat",
                    "background-position": "center center",
                    "background-size": "contain", 
                  });
                } else {
                  $(`.decoration.${category}`).attr(
                    "src",
                    `res/image/roomDeco/${category}/${image}`
                  );
                  $(`.decoration.${category}`).css("display", "block");
                }

                $(`.item[data-type="decoration"]`).each(function () {
                  const itemCategory = $(this).data("category");
                  if (itemCategory === category) {
                    $(this).removeClass("equipped");
                  }
                });

                $(`.item[data-item-id="${selectedItemID}"]`).addClass(
                  "equipped"
                );
              }
            } else {
              alert("Error: " + data.message);
            }
          },
          error: function (xhr, status, error) {
            console.error("AJAX error:", error);
            alert("Something went wrong.");
          },
        });

        $("#confirmation-section").hide();
      }
    });

  // Handle Cancel
  $(document).on("click", "#cancel-selection", function () {
    selectedItemID = null;
    selectedItemName = null;
    selectedItemType = null;
    $("#confirmation-section").hide();
  });
});
//filter inventory section
$(document)
  .off("click", ".filter-btn")
  .on("click", ".filter-btn", function () {
    const $btn = $(this);
    const category = $btn.data("category");
    let currentSort = $btn.data("sort") || "asc";
    let sort = currentSort === "asc" ? "desc" : "asc";
    if (!$btn.hasClass("active")) {
      sort = "asc";
    }

    $(".filter-btn").removeClass("active");
    $(this).addClass("active");

    $btn.data("sort", sort);
    $btn.html(
      `${category.charAt(0).toUpperCase() + category.slice(1)} ${
        sort == "asc" ? "⬆" : "⬇"
      }`
    );

    $.ajax({
      url: "/back_end/presentation_layer/CatProfileController/displayAccessory.php",
      type: "POST",
      dataType: "html",
      data: {
        specificCategory: category,
        sortDirection: sort,
      },
      success: function (html) {
        $("#popup-content").html(html);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error(
          "Error fetching accessory inventory:",
          textStatus,
          errorThrown
        );
        console.log(jqXHR.responseText);
      },
    });
  });

$(document)
  .off("click", ".filter-btn2")
  .on("click", ".filter-btn2", function () {
    const $btn = $(this);
    const category = $btn.data("category");
    let currentSort = $btn.data("sort") || "asc";
    let sort = currentSort === "asc" ? "desc" : "asc";
    if (!$btn.hasClass("active")) {
      sort = "asc";
    }

    $(".filter-btn").removeClass("active");
    $(this).addClass("active");

    $btn.data("sort", sort);
    $btn.html(
      `${category.charAt(0).toUpperCase() + category.slice(1)} ${
        sort == "asc" ? "⬆" : "⬇"
      }`
    );

    $.ajax({
      url: "back_end/presentation_layer/HomeDecorationController/displayDecoration.php",
      type: "POST",
      dataType: "html",
      data: {
        specificCategory: category,
        sortDirection: sort,
      },
      success: function (html) {
        $("#popup-content-deco").html(html);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error(
          "Error fetching decoration inventory:",
          textStatus,
          errorThrown
        );
        console.log(jqXHR.responseText);
      },
    });
  });

// ADVENTURE USE
function openAdventurePopUp(clickedId) {
  const popup = document.getElementById("adventurePopupContainer");

  fetch(
    "../back_end/presentation_layer/AdventureController/staminaController.php",
    {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "action=checkStamina",
    }
  )
    .then((response) => response.json())
    .then((data) => {
      console.log("Stamina check:", data);

      let popupType = data.status === "ok" ? "adventure" : "insufficient";

      fetch(
        `../back_end/presentation_layer/AdventureController/staminaController.php?popupType=${popupType}`
      )
        .then((response) => response.text())
        .then((html) => {
          popup.innerHTML = html;
          popup.classList.remove("hidden");
          document
            .getElementById("adventure-popup-overlay")
            .classList.remove("hidden");
        })
        .catch((error) => {
          console.error("Error loading popup HTML:", error);
        });
    })
    .catch((error) => {
      console.error("Error checking stamina:", error);
    });
}

$(function () {
  let staminaTimerInterval;
  let lastUpdateTime;

  function updateStaminaBar() {
    fetch(
      "../back_end/presentation_layer/AdventureController/staminaController.php",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "action=getEnergy",
      }
    )
      .then((response) => response.text())
      .then((text) => {
        // console.log("Response text:", text);
        try {
          const data = JSON.parse(text);
          if (data.energy !== undefined) {
            document.getElementById("staminaValue").textContent =
              data.energy + " / 5";

            if (data.energy < 5) {
              lastUpdateTime = new Date(data.lastUpdate);
              startStaminaCountdown();
            } else {
              clearInterval(staminaTimerInterval);
              document.getElementById("staminaCountDown").textContent = "Full";
            }
          } else {
            document.getElementById("staminaValue").textContent = "Error";
          }
        } catch (e) {
          console.error("JSON parse error:", e);
          document.getElementById("staminaValue").textContent =
            "Error parsing server";
        }
      })
      .catch((error) => {
        console.error("Fetch error:", error);
        document.getElementById("staminaValue").textContent = "Fetch Error";
      });
  }

  function startStaminaCountdown() {
    clearInterval(staminaTimerInterval);

    staminaTimerInterval = setInterval(() => {
      let now = new Date();
      let timeDiff = (now - lastUpdateTime) / 1000; // in seconds
      let timeLeft = 2 * 60 * 60 - timeDiff;

      if (timeLeft <= 0) {
        clearInterval(staminaTimerInterval);
        updateStaminaBar();
        return;
      }

      let hours = Math.floor(timeLeft / 3600);
      let minutes = Math.floor((timeLeft % 3600) / 60);
      let seconds = Math.floor(timeLeft % 60);

      document.getElementById(
        "staminaCountDown"
      ).textContent = `Left time: ${String(hours).padStart(2, "0")}:${String(
        minutes
      ).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
    }, 1000);
  }

  // Initial load
  updateStaminaBar();

  // Refresh stamina every 1 minute
  setInterval(updateStaminaBar, 10000);
});

$(document).ready(function () {
  // check if user has already checked in today
  // if so, hide the button
  $.getJSON("/back_end/presentation_layer/UserController/DailyCheckIn.php", {
    action: "hasCheckedIn",
  }).done(function (resp) {
    if (resp.checkedIn) {
      $("#claimRewardBtn").hide();
    }
  });

  $("#claimRewardBtn")
    .off("click")
    .on("click", function (e) {
      loadDailyCheckInPopup();
    });

  // close the reward popup
  $("#closeDailyReward").click(function () {
    $("#dailyRewardPopup").fadeOut();
  });

  // wire up the “already checked in” popup close
  $("#closeCheckedIn").on("click", function () {
    $("#checkedInPopup").fadeOut().addClass("hidden");
  });

  // wire up the help button
  $("#helpLogo").on("click", function () {
    $("#helpOverlay, #userManualPopup")
      .removeClass("hidden")
      .addClass("visible")
      .fadeIn(200);
  });

  // close the manual popup
  $(document).on("click", "#closeUserManual", function () {
    $("#userManualPopup, #helpOverlay").fadeOut(200, function () {
      $(this).addClass("hidden").removeClass("visible");
    });
  });
});

function loadDailyCheckInPopup() {
  $.ajax({
    url: "/back_end/presentation_layer/UserController/DailyCheckInUI.php",
    method: "POST",
    dataType: "html",
    success: function (html) {
      $("#popupContainer").html(html);
      $("#dailyCheckinPopup").fadeIn();
      $("#dailyRewardOverlay").addClass("visible").fadeIn();

      // Close button handler
      $("#closeDailyCheckinPopup").click(function () {
        $("#dailyCheckinPopup").fadeToggle();
        $("#dailyRewardOverlay").fadeOut(200, function () {
          $(this).removeClass("visible");
        });
      });

      // Reward button handler
      $("#claimRewardNow").click(function () {
        claimDailyReward();
      });
    },
    error: function () {
      alert("Failed to load check-in popup.");
    },
  });
}

function claimDailyReward() {
  $.ajax({
    url: "/back_end/presentation_layer/UserController/DailyCheckIn.php",
    method: "POST",
    dataType: "json",
  })
    .done(function (response) {
      if (response.status === "success") {
        $("#popupContainer, #dailyCheckinPopup").fadeToggle(); // hide the daily checkin popup
        // decide image path
        // const r = response.reward; // { category, image, name }

        let path;
        const cat = response.reward.category;
        const img = response.reward.image;
        if (["head", "body", "neck"].includes(cat)) {
          path = `/res/image/accessories/${cat} accessories/${img}`;
        } else if (cat === "consumable") {
          path = img.startsWith("/") ? img : `/${img}`;
        } else {
          path = `/res/image/roomDeco/${cat}/${img}`;
        }

        // show daily reward popup
        $("#dailyRewardImg").attr("src", path);
        $("#dailyRewardName").text(response.reward.name);
        $("#dailyRewardPopup").removeClass("hidden").fadeIn();
        $("#dailyRewardOverlay").addClass("visible").fadeIn();

        // disable the claim button
        $("#claimRewardBtn, #claimRewardNow").fadeOut();

        //newly unlocked achievement
        const unlocked = response.unlockedAchievements || [];
        if (unlocked.length > 0) {
          showAchievementQueue(unlocked);
        }
        return;
      }

      // 3) some other error
      alert(response.message || "Check-in error.");
    })
    .fail(function () {
      alert("Network error — please try again.");
    });
}

//OKAY button:
$(document).on("click", "#closeDailyReward", function () {
  $("#dailyRewardPopup").fadeOut().addClass("hidden");
  $("#dailyRewardOverlay").fadeOut(200, function () {
    $(this).removeClass("visible");
  });
});


// Displays one achievement modal at a time
function showAchievementQueue(achievements) {
  const queue = achievements.slice(); // copy array
  function showNext() {
    if (!queue.length) return; //if queue is empty, return
    const ach = queue.shift(); //removes the first element of the array and returns it.

    $("#achievementOverlay")
      .removeClass("hidden")
      .addClass("visible")
      .fadeIn(200);
    $("#achievementRewardImg").attr("src", ach.iconPath);
    $("#achievementRewardName").text(ach.title);
    $("#achievementRewardPopup").removeClass("hidden").fadeIn();

    $("#closeAchievementReward")
      .off("click")
      .on("click", function () {
        $("#achievementRewardPopup").fadeOut(() =>
          $("#achievementRewardPopup").addClass("hidden")
        );
        $("#achievementOverlay")
          .addClass("hidden")
          .removeClass("visible")
          .fadeOut(200);
        showNext();
      });
  }

  showNext();
}

function useRecoveryItem() {
  fetch(
    "../back_end/presentation_layer/AdventureController/staminaController.php",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "action=useRecoveryItem",
    }
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        // alert("Stamina updated successfully!");
        // location.reload();
        showAdventurePopup("adventure");
      } else {
        alert(
          "Failed to use recovery item: " + (data.message || "Unknown error")
        );
      }
    })
    .catch((error) => console.error("Error:", error));
}

function showAdventurePopup(popupType) {
  const popup = document.getElementById("adventurePopupContainer");

  fetch(
    `../back_end/presentation_layer/AdventureController/staminaController.php?popupType=${popupType}`
  )
    .then((response) => response.text())
    .then((html) => {
      popup.innerHTML = html;
      popup.classList.remove("hidden");
      document
        .getElementById("adventure-popup-overlay")
        .classList.remove("hidden");
    })
    .catch((error) => {
      console.error("Error loading popup HTML:", error);
    });
}

// start adventure
function startAdventure() {
  fetch(
    "../back_end/presentation_layer/AdventureController/staminaController.php",
    {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "action=startAdventure",
    }
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        window.location.href = "../page/adventureMode.php"; // Redirect
      } else {
        alert("Error starting adventure: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

// pop up adventure story
$(document).ready(function () {
  $("#adventureStoryPopUp").click(function () {
    closePopup2();
    $.ajax({
      url: "/back_end/presentation_layer/AdventureController/adventureUi.php",
      method: "POST",
      data: { action: "getStoryList" },
      dataType: "json",
      success: function (res) {
        if (res.status === "success") {
          $(".story-list").html(res.html);
          $("#storyPopupOverlay").show();
          $("#storyPopup").show();
        }
      },
      error: function () {
        alert("Failed to load story list.");
      },
    });
  });
});

function closeStoryPopup() {
  $("#storyPopupOverlay").hide();
  $("#storyPopup").hide();
}

// Photo preview
$("label.upload input[type=file]").on("change", (e) => {
  const f = e.target.files[0];
  const img = $(e.target).siblings("img")[0];

  if (!img) return;

  img.dataset.src ??= img.src;

  if (f?.type.startsWith("image/")) {
    img.src = URL.createObjectURL(f);
  } else {
    img.src = img.dataset.src;
    e.target.value = "";
  }
});

document.addEventListener("DOMContentLoaded", function () {
  document
    .querySelector("#previewImage")
    .addEventListener("click", function () {
      document.querySelector("#newPhoto").click();
    });
});

$(document).ready(function () {
  // Confirmation message
  $("[data-confirm]").on("click", (e) => {
    const text = e.target.dataset.confirm || "Are you sure?";
    if (!confirm(text)) {
      e.preventDefault();
      e.stopImmediatePropagation();
    }
  });

  // Initiate POST request
  $("[data-post]").on("click", (e) => {
    e.preventDefault();
    const url = e.target.dataset.post;
    const f = $("<form>").appendTo(document.body)[0];
    f.method = "POST";
    f.action = url || location;
    f.submit();
  });

  // Photo preview
  $("label.upload input[type=file]").on("change", (e) => {
    const f = e.target.files[0];
    const img = $(e.target).siblings("img")[0];

    if (!img) return;

    img.dataset.src ??= img.src;

    if (f?.type.startsWith("image/")) {
      img.src = URL.createObjectURL(f);
    } else {
      img.src = img.dataset.src;
      e.target.value = "";
    }
  });
});

function previewPhoto(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      document.getElementById("previewImage").src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

$(document).ready(function () {
  /* ========== Select Activity Pack Page ========== */
  $("#back-to-home").click(function () {
    window.location.href = "/index.php";
  });

  // Navigate to exercise list when a pack is clicked
  $(document).on("click", ".pack-card", function () {
    const category = $(this).data("category");
    if (category) {
      const encoded = encodeURIComponent(category);
      window.location.href = `/page/exerciseList.php?category=${encoded}`;
    }
  });

  // ========== Exercise List Page ==========
  if ($(".exercise-list-page").length > 0) {
    // Go back to SelectActivityPacks
    $("#back-to-packs").click(function () {
      window.location.href = "/page/SelectActivityPacks.php";
    });

    // Show popup with exercise info
    $(".exercise-item").click(function () {
      const name = $(this).data("name");
      const description = $(this).data("description");
      const video = $(this).data("video");

      $("#popup-exercise-name").text(name);
      $("#popup-exercise-description").text(description);
      $("#popup-exercise-video").attr("src", "/res/Activity/Videos/" + video);
      $("#exercise-detail-popup").fadeIn();
    });

    // Close the popup
    $("#close-detail-btn").click(function () {
      $("#exercise-detail-popup").fadeOut();
    });

    // Start button (first exercise)
    $("#start-btn").click(function () {
      const activityID = $(this).data("activity-id");
      window.location.href = `/page/exerciseSession.php?activityID=${activityID}&category=${currentCategory}`;
    });

    // Resume button
    $("#resume-btn").click(function () {
      const uActivityID = $(this).data("u-activity-id");
      const activityID = $(this).data("activity-id");
      window.location.href = `/page/exerciseSession.php?activityID=${activityID}&uActivityID=${uActivityID}&category=${currentCategory}`;
    });

    // Reset Progress
    $("#reset-progress-btn").click(function () {
      showConfirmPopup(
        "Are you sure you want to reset your progress?",
        function () {
          $.ajax({
            url: "/back_end/presentation_layer/ActivityController/ActivityController.php",
            method: "POST",
            data: {
              action: "resetProgress",
              category: currentCategory,
            },
            dataType: "json",
            success: function (response) {
              if (response.status === "success") {
                showAlertPopup("✅ Progress reset successfully!");
                setTimeout(() => location.reload(), 1800);
              } else {
                showAlertPopup("⚠️ Failed to reset progress.");
              }
            },
            error: function () {
              showAlertPopup("❌ Error contacting server.");
            },
          });
        }
      );
    });

    function showAlertPopup(message) {
      $(".popup-message .content").text(message);
      $(".popup-buttons").hide(); // hide confirm buttons
      $(".popup-message").fadeIn(200).delay(3000).fadeOut(200);
    }

    function showConfirmPopup(message, onConfirm) {
      $(".popup-message .content").text(message);
      $(".popup-buttons").show(); // show confirm buttons
      $(".popup-message").fadeIn(200);

      $(".confirm-popupmessage")
        .off("click")
        .on("click", function () {
          $(".popup-message").fadeOut(200);
          if (typeof onConfirm === "function") onConfirm();
        });

      $(".close-popupmessage")
        .off("click")
        .on("click", function () {
          $(".popup-message").fadeOut(200);
        });
    }
  }

  // ========== Exercise Session Page ==========
  if ($(".exercise-session-page").length > 0) {
    let countdownInterval;
    let restInterval;
    let remainingTime = parseInt($("#countdown-timer").data("time"));
    const originalTime = parseInt($("#countdown-timer").data("original-time"));
    const category = decodeURIComponent($("#countdown-timer").data("category"));
    const currentActivityID = parseInt(
      $("#countdown-timer").data("current-activity-id")
    );
    const uActivityID = parseInt($("#countdown-timer").data("u-activity-id"));
    let paused = false;
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("rest") === "true") {
      showRestPopup();
    }

    startCountdown();
    $("html, body").animate({ scrollTop: 175 }, "slow");

    function startCountdown() {
      clearInterval(countdownInterval);
      updateTimerDisplay(remainingTime);
      countdownInterval = setInterval(() => {
        if (!paused) {
          remainingTime--;
          updateTimerDisplay(remainingTime);
          if (remainingTime <= 0) {
            clearInterval(countdownInterval);
            completeExercise();
          }
        }
      }, 1000);
    }

    function updateTimerDisplay(time) {
      $("#countdown-timer").text(time > 0 ? `${time} seconds` : "Time's up!");
    }

    $("#pause-btn").click(function () {
      paused = true;
      clearInterval(countdownInterval);
      $("#exercise-video")[0].pause();
      $("#pause-popup").fadeIn();
      saveProgress();
    });

    $("#resume-btn").click(function () {
      paused = false;
      $("#pause-popup").fadeOut();
      $("#exercise-video")[0].play();
      startCountdown();
    });

    $("#restart-btn").click(function () {
      paused = false;
      remainingTime = originalTime;
      $("#pause-popup").fadeOut();
      $("#exercise-video")[0].currentTime = 0;
      $("#exercise-video")[0].play();
      startCountdown();
    });

    $("#exit-btn").click(function () {
      paused = true;
      clearInterval(countdownInterval);
      saveProgress(() => {
        window.location.href = `/page/exerciseList.php?category=${encodeURIComponent(category)}`;
      }, true); // 🔁 true = unset the session
    });

    $("#skip-btn").click(function () {
      paused = true;
      clearInterval(countdownInterval);
      skipExercise();
    });

    $("#skip-rest-btn").click(function () {
      clearInterval(restInterval);
      $("#rest-popup").fadeOut();
      paused = false;
    });

    setInterval(() => {
      $.post("/back_end/presentation_layer/ActivityController/ActivityController.php", {
        action: "heartbeat",
        uActivityID: uActivityID
      });
    }, 120000); // every 2 mins
    

    function saveProgress(callback = null, shouldUnset = false) {
      if (uActivityID > 0) {
        $.post(
          "/back_end/presentation_layer/ActivityController/ActivityController.php",
          {
            action: "saveProgress",
            uActivityID: uActivityID,
            remainingTime: remainingTime,
            unsetSession: shouldUnset ? 1 : 0
          },
          function () {
            if (callback) callback();
          },
          "json"
        );
      }
    }
    

    function completeExercise() {
      $.post(
        "/back_end/presentation_layer/ActivityController/ActivityController.php",
        {
          action: "completeExercise",
          uActivityID: uActivityID,
        },
        goToNextExercise(),
        "json"
      );
    }

    function skipExercise() {
      $.post(
        "/back_end/presentation_layer/ActivityController/ActivityController.php",
        {
          action: "skipExercise",
          uActivityID: uActivityID,
        },
        goToNextExercise(),
        "json"
      );
    }

    function showRestPopup() {
      const video = document.getElementById("exercise-video");
      if (video && !video.paused) {
        video.pause();
      }

      paused = true;
      let restTime = 10;
      $("#rest-timer").text(`${restTime} seconds`);
      $("#rest-popup").fadeIn();

      restInterval = setInterval(() => {
        restTime--;
        $("#rest-timer").text(`${restTime} seconds`);
        if (restTime <= 0) {
          clearInterval(restInterval);
          $("#rest-popup").fadeOut();
          paused = false;
        }
      }, 1000);
    }

    function goToNextExercise() {
      $.post(
        "/back_end/presentation_layer/ActivityController/ActivityController.php",
        {
          action: "getNextExercise",
          currentActivityID: currentActivityID,
          category: category,
          uActivityID: uActivityID, 

        },
        function (response) {
          if (response.status === "success") {
            window.location.href = `/page/exerciseSession.php?activityID=${
              response.nextActivityID
            }&uActivityID=${response.uActivityID}&category=${encodeURIComponent(
              category
            )}&rest=true`;
          } else {
            showAlertPopup("No more exercises in this pack.", checkReward);
          }
        },
        "json"
      );
    }

    function checkReward() {
      $.post(
        "/back_end/presentation_layer/ActivityController/ActivityController.php",
        {
          action: "checkAndReward",
          category: category,
        },
        function (rewardResponse) {
          if (rewardResponse.status === "reward") {
            showAlertPopup(
              "🎉 Congratulations! You've earned a reward!",
              null,
              rewardResponse.reward
            );
          } else if (rewardResponse.status === "message") {
            showAlertPopup(rewardResponse.message, function () {
              window.location.href = "/page/SelectActivityPacks.php";
            });
          } else {
            showAlertPopup(
              "✅ All exercises completed — no reward due to skips.",
              function () {
                window.location.href = "/page/SelectActivityPacks.php";
              }
            );
          }
        },
        "json"
      );
    }

    function showAlertPopup(message, callback = null, rewardData = null) {
      $(".content").text(message);

      if (rewardData) {
        let imagePath = "";
        if (["head", "body", "neck"].includes(rewardData.category)) {
          imagePath = `/res/image/accessories/${rewardData.category} accessories/${rewardData.imgPath}`;
        } else if (rewardData.category === "consumable") {
          imagePath = "/res/image/adventure/Mouse.gif";
        } else {
          imagePath = `/res/image/roomDeco/${rewardData.category}/${rewardData.imgPath}`;
        }
        const rewardHtml = `
          <p><strong>Category:</strong> ${rewardData.category}</p>
          <p><strong>Name:</strong> ${rewardData.name}</p>
          <img src="${imagePath}" 
               alt="${rewardData.name}" 
               style="width:100px; height:100px; margin-top:10px; border-radius: 10px;">
        `;
        $(".reward-detail").html(rewardHtml).show();
        $(".popup-buttons").show();
        $(".popup-message").fadeIn(200); // just fade in — wait for user action
      } else {
        $(".reward-detail").hide();
        $(".popup-buttons").hide();
        $(".popup-message")
          .fadeIn(200)
          .delay(1500)
          .fadeOut(200, function () {
            if (typeof callback === "function") callback();
          });
      }
    }

    $(".close-popupmessage").on("click", function () {
      $(".popup-message").fadeOut(200);
    });

    $(".goto-list").on("click", function () {
      window.location.href = "/page/SelectActivityPacks.php";
    });

    $(".goto-home").on("click", function () {
      window.location.href = "/index.php";
    });
  }
});

function getName() {
  fetch(
    "../back_end/presentation_layer/CatProfileController/nameController.php",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "action=getName",
    }
  )
    .then((response) => response.text())
    .then((text) => {
      // console.log("Name response:", text);
      try {
        const data = JSON.parse(text);
        if (data.name) {
          document.getElementById("catName").textContent = data.name;
        } else {
          document.getElementById("catName").textContent = "No name found";
        }
      } catch (e) {
        console.error("JSON parse error:", e);
        document.getElementById("catName").textContent = "Error parsing name";
      }
    })
    .catch((error) => {
      console.error("Fetch error:", error);
      document.getElementById("catName").textContent = "Fetch Error";
    });
}

$(document).ready(function () {
  getName();
});

// Delegate click event for #editCatName
$(document).on("click", "#editCatName", function () {
  const nameDisplay = $("#catNameDisplay");
  const nameInput = $("#catNameInput");
  const confirmBtn = $("#confirmNameBtn");

  nameDisplay.hide();
  $(this).hide();
  nameInput.show().focus();
  confirmBtn.show();
});

// Delegate click event for #confirmNameBtn
$(document).on("click", "#confirmNameBtn", function () {
  const nameInput = $("#catNameInput");
  const newName = nameInput.val().trim();

  if (newName === "") {
    alert("Name cannot be empty.");
    return;
  }

  fetch(
    "../back_end/presentation_layer/CatProfileController/nameController.php",
    {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "action=updateName&name=" + encodeURIComponent(newName),
    }
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        $("#catNameDisplay").text(newName).show();
        $("#editCatName").show();
        nameInput.hide();
        $("#confirmNameBtn").hide();
        getName();
      } else {
        alert("Failed to update name.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Server error.");
    });
});

$(document).on("keydown", "#catNameInput", function (event) {
  if (event.key === "Enter" || event.keyCode === 13) {
    $("#confirmNameBtn").click(); // Trigger the confirm button
  }
});
