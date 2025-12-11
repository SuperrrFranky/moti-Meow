<?php
require '../_base.php';
require '../header.php';
require '../back_end/presentation_layer/UserController/helpButton.php';
if ((isset($_SESSION['adventure_in_progress']) && $_SESSION['adventure_in_progress'] === false) &&
    (isset($_SESSION['claimed_adventure']) && $_SESSION['claimed_adventure'] == true)
) {
    echo "<script>";
    echo "window.location.href = '/index.php';";
    echo "</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        /* Complete Adventure Pop UP */

        #popup-complete {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -30%);
            background-color: #e1c8b3;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            width: 300px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            font-family: 'Poppins', sans-serif;
            z-index: 999;
            /* make sure it is always on top */
        }

        #popup-complete p {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        #popup-complete button {
            background-color: #90f7b1;
            color: black;
        }

        #popup-complete button {
            background-color: #7beaa3;
        }

        #popup-complete button {
            padding: 10px 30px;
            border: none;
            border-radius: 15px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Adventure BAckground */
        body {
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
        }

        /* Advnture Countdown Timer font style*/
        @font-face {
            font-family: 'Digital7';
            src: url('../style/digital-7.ttf') format('truetype');
        }

        /* set cat walking area */
        .cat-area {
            position: relative;
            width: 1000px;
            height: 550px;
            margin: 300px 25% 40px 17%;
            overflow: hidden;
        }

        /* set cat walking */
        .adventureCat {
            width: 72px;
            height: 72px;
            background: url('/res/image/cat/walking.png') no-repeat;
            background-size: cover;
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            animation: _3-frame-cycle 1s steps(3) infinite;
            cursor: pointer;
            z-index: 2;
        }

        #adventureCountdown {
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 5em;
            font-family: 'Digital7', monospace;
            color: #000000;
            padding: 30px;
            border-radius: 25px;
            position: absolute;
            /* Needed for pseudo-element */
            z-index: 1;
        }

        #adventureCountdown::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: aliceblue;
            /* background-color: #1B1212; */
            opacity: 0.4;
            border-radius: 25px;
            z-index: -1;
            /* Behind the text */
        }



        /* Unlock Reward */
        #popup-reward {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -30%);
            background-color: #e1c8b3;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            width: 300px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            font-family: 'Poppins', sans-serif;
            z-index: 998;
        }

        .adventure-unlock-popup {
            background-color: #e8ccb5;
            padding: 20px;
            border-radius: 20px;
            width: 300px;
            text-align: center;
            font-family: 'Comic Sans MS', cursive, sans-serif;
        }

        .unlock-title {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .adventure-story {
            background-color: #a18b78;
            border-radius: 10px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            margin-bottom: 10px;
        }

        .unlock-adv-cat-icon {
            width: 72px;
            height: 72px;
            background: url('/res/image/cat/Surprised.png') no-repeat;
            background-size: cover;
            transform: translateY(-10%);
            animation: _12-frame-cycle 1s steps(12) infinite;
            cursor: pointer;
        }

        .story-text {
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .divider {
            border: none;
            border-top: 1px solid #444;
            margin: 10px 0;
        }

        .adventure-reward-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .reward-text {
            font-weight: bold;
        }

        .adventure-reward-icon {
            width: 100px;
            height: auto;
        }

        .okay-btn {
            background-color: #aaffaa;
            border: none;
            padding: 8px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 12px;
            cursor: pointer;
        }

        .popup {
            position: fixed;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            border-radius: 10px;
            z-index: 1000;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .hidden {
            display: none;
        }

        #popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 998;
            display: none;
        }

        .a_feed {
            top: 50%;
            left: 0%;
            transform: translate(-50%, -50%);
        }

        .a_chat {
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .a_click {
            top: 50%;
            left: 100%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body>
    <div class=bodyContainer>
        <div class="mainContainer">
            <div id="adventureCountdown">Loading...</div>
            <div class="cat-area">
                <div class="adventureCat" data-x="50" data-y="50">
                    <div class="interactionContainer">
                        <img
                            src="/res/image/interaction/fish-bones.png"
                            alt=""
                            class="a_feed interaction" />
                        <img
                            src="/res/image/interaction/chat-gpt.png"
                            alt=""
                            class="a_chat interaction" />
                        <img
                            src="/res/image/interaction/click.png"
                            alt=""
                            class="a_click interaction" />
                    </div>
                    <div class="play-chat-bubble"></div>
                    <?php
                    $headPath = isset($_SESSION['equipped_images']['head']) && !empty($_SESSION['equipped_images']['head'])
                        ? '/res/image/accessories/head accessories/' . $_SESSION['equipped_images']['head']
                        : '';

                    $bodyPath = isset($_SESSION['equipped_images']['body']) && !empty($_SESSION['equipped_images']['body'])
                        ? '/res/image/accessories/body accessories/' . $_SESSION['equipped_images']['body']
                        : '';

                    $neckPath = isset($_SESSION['equipped_images']['neck']) && !empty($_SESSION['equipped_images']['neck'])
                        ? '/res/image/accessories/neck accessories/' . $_SESSION['equipped_images']['neck']
                        : '';
                    ?>
                    <img src="<?php echo $headPath; ?>" class="head-layer" />
                    <img src="<?php echo $bodyPath; ?>" class="body-layer" />
                    <img src="<?php echo $neckPath; ?>" class="neck-layer" />
                </div>
            </div>

            <!-- Button Wrapper -->
            <div class="button-wrapper">
                <!-- Hidden Button Group (initially hidden) -->
                <div class="navButton" id="buttonGroup" style="display: none;">
                    <button class="circle-button" id="claimRewardBtn"><img src="/res/image/calendar.png"></button>
                    <button class="circle-button" onclick="window.location.href='SelectActivityPacks.php';">
                        <img src="/res/image/exercise.png">
                    </button>
                    <button class="circle-button" id="show-photoGallery"><img src="/res/image/photo-gallery.png"></button>
                    <button class="circle-button" id="adventureStoryPopUp"><img src="/res/image/history.png"></button>
                </div>

                <!-- Toggle Button (ALWAYS visible at bottom) -->
                <button class="circle-toggle-button" id="toggleMenuBtn">+</button>
            </div>


            <div id="popup-window">
                <span id="popup-close-btn" onclick=closePopup()></span>
                <div id="popup-content"></div>
            </div>

            <div id="popup-overlay"></div>
            ~
            <div id="popup-complete" class="popup hidden">
                <p>Adventure Completed!</p>
                <button onclick="showReward()">OKAY</button>
            </div>

            <div id="popup-reward" class="popup hidden">
                <h2 class="unlock-title">New Story</h2>

                <div class="adventure-story">
                    <div class="unlock-adv-cat-icon"></div>
                    <div class="story-text"></div>
                </div>

                <hr class="divider" />

                <div class="adventure-reward-section">
                    <span class="reward-text">Unlock New Reward:</span>
                    <img id="reward-img" src="" alt="reward" class="adventure-reward-icon" />
                    <p id="reward-text"></p>
                </div>

                <button onclick="redirectHome()" class="okay-btn">OKAY</button>
            </div>

            <div id="storyPopupOverlay" class="story-overlay hidden"></div>
            <div id="storyPopup" class="story-popup hidden">
                <div class="story-header">
                    <h2>Adventure Stories</h2>
                    <button class="close-btn-adventure" onclick="closeStoryPopup()">&times;</button>
                </div>
                <div class="story-list"></div>
            </div>

            <div id="popup-window">
                <span id="popup-close-btn" onclick=closePopup()></span>
                <div id="popup-content"></div>
            </div>

            <div id="popup-catProfile">
                <span id="popup-close-btn" onclick=closePopup2()></span>
                <div id="popup-content-profile"></div>
            </div>
        </div>

        <div id="dailyCheckinPopup" height: 100px; width: 100px; display: none;>
            <div id="popupContainer"></div>
        </div>

        <div id="dailyRewardPopup" class="popup hidden">
            <h1>Congratulations! You got</h1>
            <img id="dailyRewardImg" src="" alt="Your Reward" />
            <p id="dailyRewardName"></p>
            <button id="closeDailyReward">OKAY</button>
        </div>

        <!-- Achievement Reward Popup -->
        <div id="achievementRewardPopup" class="popup hidden">
            <h1>Congratulations!</h1>
            <p>You unlocked Achievement:</p>
            <img id="achievementRewardImg" src="" alt="Achievement Icon" style="width:64px;height:64px;">
            <p id="achievementRewardName" style="font-weight:bold;"></p>
            <button id="closeAchievementReward">OKAY</button>
        </div>

        <!-- “Already Checked-In” Popup -->
        <div id="checkedInPopup" class="popup hidden">
            <div class="ins-popup-header">
                <h1>You Already Checked In Today</h1>
                <img src="../res/image/adventure/warning-icon.png" alt="warning-icon" class="ins-warning-icon">
            </div>
            <button id="closeCheckedIn">OKAY</button>
        </div>
</body>
<script>
    $(document).ready(function() {
        const headLayer = $(".head-layer");
        const bodyLayer = $(".body-layer");
        const neckLayer = $(".neck-layer");
        const cat = $(".adventureCat");
        const feedBtn = $(".a_feed");
        const chatBtn = $(".a_chat");
        const clickBtn = $(".a_click");
        const playAnimations = [{
                img: "/res/image/cat/Dance.png",
                frames: 4
            },
            {
                img: "/res/image/cat/Excited.png",
                frames: 12
            },
            {
                img: "/res/image/cat/LayDown.png",
                frames: 12
            },
            {
                img: "/res/image/cat/Sleepy.png",
                frames: 8
            },
            {
                img: "/res/image/cat/Surprised.png",
                frames: 12
            },
        ];

        const playMessages = [
            "Let's have fun!",
            "Catch me if you can!",
            "I'm so excited! 😸",
            "Playtime is the best time!",
            "Yay! More toys!",
        ];
        const popupContent = $("#popup-content");
        const CHAT_API_URL = `${window.location.origin}/back_end/presentation_layer/InteractionController/interactionController.php`;



        cat.on("click", function() {
            const interactionContainer = $(this).find(".interactionContainer");

            if (!interactionContainer.is(":visible")) {
                interactionContainer.fadeIn(200, function() {
                    setTimeout(function() {
                        interactionContainer.fadeOut(200);
                    }, 2000);
                });
            } else {
                interactionContainer.fadeOut(200);
            }
        });

        feedBtn.on("click", function() {

            cat.css("background", "url('/res/image/cat/Eating.png') no-repeat");
            cat.css("background-size", "cover");
            cat.css("animation", "_15-frame-cycle 1s steps(15) infinite forwards");
            hideAccessory();

            setTimeout(function() {
                cat.css("background", "url('/res/image/cat/walking.png') no-repeat");
                cat.css("background-size", "cover");
                cat.css("animation", "_3-frame-cycle 1s steps(3) infinite");
                showAccessory();
            }, 3000);

        });

        clickBtn.on("click", function() {
            hideAccessory();

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

            const bubble = $(".play-chat-bubble");
            bubble.text(randomMsg).show();

            // ✅ Check if cat is flipped
            const catFlipped = cat.css("transform")?.includes("matrix(-1");

            if (catFlipped) {
                bubble.css("transform", "rotateY(180deg)");
            } else {
                bubble.css("transform", "rotateY(0deg)");
            }

            // ✅ Reset after 3 seconds
            setTimeout(function() {
                cat.css({
                    "background-image": "url('/res/image/cat/walking.png')",
                    "background-size": "cover",
                    animation: "_3-frame-cycle 1s steps(3) infinite forwards",
                });

                bubble.fadeOut(200);

                // ✅ Show equipped accessories again
                if (headLayer.attr("src")) headLayer.show();
                if (bodyLayer.attr("src")) headLayer.show();
                if (neckLayer.attr("src")) headLayer.show();
            }, 3000);
        });

        chatBtn.on("click", function() {
            function displayPopup() {
                const popup = $("#popup-window");
                popup.fadeIn(200);
            }

            function closePopup() {
                const popup = $("#popup-window");
                const popupContent = $("#popup-content");

                popup.fadeOut(200);
                popupContent.empty(); // Clear content when closing
            }
            console.log("1 Sending to:", CHAT_API_URL);

            $.ajax({
                url: CHAT_API_URL,
                method: "POST",
                data: {
                    action: "startChat"
                },
                dataType: "json",
                success: function(response) {
                    popupContent.append(response.html); // Insert returned HTML
                    displayPopup(); // Show popup function
                },
                error: function() {
                    alert("Failed to start chat. Please try again later.");
                    $("#popupContent").html("Error loading content.");
                    displayPopup();
                },
            });
        });
        $(document).on("click", "#chat-send-btn", function(e) {
            e.preventDefault(); // ✅ Prevent form submission
            e.stopPropagation(); // ⛔ prevent bubbling

            sendMessage();
        });

        // Listen to Enter key in input field
        $(document).on("keypress", "#chat-input", function(e) {
            if (e.which === 13) {
                e.preventDefault(); // ✅ Prevent form submission
                e.stopPropagation(); // ⛔ prevent bubbling

                console.log("2 Sending to :", CHAT_API_URL);

                sendMessage();
            }
        });

        function sendMessage() {
            const userInput = $("#chat-input").val().trim();
            const chatMessages = popupContent.find(".chat-messages");
            console.log("2 Sending to :", CHAT_API_URL);


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
                url: CHAT_API_URL,
                method: "POST",
                data: {
                    action: "getResponse",
                    message: userInput
                },
                dataType: "json",
                success: function(response) {
                    $(".typing-indicator").remove();
                    const botReply = response.response;

                    chatMessages.append(`
                <div class="chat-bubble bot-message">
                    <img src="/res/image/interaction/chatbot_avatar.png" alt="Cat" class="chat-avatar">
                    <div class="bubble-text">${botReply}</div>
                </div>
            `);
                    chatMessages.scrollTop(chatMessages[0].scrollHeight);
                },
                error: function() {
                    alert("Failed to connect to chatbot.");
                },
            });
        }


        function showAccessory() {
            if (headLayer.attr("src")) headLayer.show();
            if (bodyLayer.attr("src")) bodyLayer.show();
            if (neckLayer.attr("src")) neckLayer.show();
        }

        function hideAccessory() {
            headLayer.hide();
            bodyLayer.hide();
            neckLayer.hide();
        }
        showAccessory();

        $.ajax({
            url: '../back_end/presentation_layer/AdventureController/adventureController.php',
            method: 'POST',
            data: {
                action: 'startOrContinueAdventure'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.status === 'completed') {
                    // Set background
                    $('body').css('background-image', 'url(../res/image/adventure/' + response.backgroundImage + ')');

                    // Start countdown timer (even if it's 0)
                    startCountdown(response.secondsRemaining);
                } else {
                    alert('Error: ' + response.message);
                    window.location.href = '../index.php';
                }
            },
            error: function(xhr, status, error) {
                alert('AJAX Error: ' + error);
                window.location.href = '../index.php';
            }
        });

        if (localStorage.getItem('adventureCompleted') === 'true') {
            showCompletionPopup();
            return;
        }

        function startCountdown(seconds) {
            const countdownElement = $('#adventureCountdown');

            const timer = setInterval(function() {
                var minutes = Math.floor(seconds / 60);
                var secs = seconds % 60;
                countdownElement.text(minutes + ":" + (secs < 10 ? "0" : "") + secs);

                if (seconds <= 0) {
                    clearInterval(timer);
                    localStorage.setItem('adventureCompleted', 'true');
                    showCompletionPopup();
                }
                seconds--;
            }, 1000);
        }
    });

    function showCompletionPopup() {
        closePopup2();
        $('#closeAchievementReward').click();
        $('#closeUserManual').click();
        $("#dailyCheckIn-popup-close-btn").click();
        $('#popup-overlay').show();
        $('#popup-complete').show();
        disableUI();
        $('#popup-window').css('pointer-events', 'auto');
    }

    // when user click okay after adventure completed (counttimer end) 
    function showReward() {
        $('#popup-complete').hide();

        $.ajax({
            url: '../back_end/presentation_layer/AdventureController/adventureController.php',
            method: 'POST',
            data: {
                action: 'rewardPlayer'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#reward-img').attr('src', response.imgPath).show();
                    $('#reward-text').text(response.name);
                    $('.story-text').text(response.adventure_title);
                } else {
                    $('#reward-img').hide();
                    $('#reward-text').text("You received stamina refill!");
                }
                $('#popup-reward').show();
            },
            error: function() {
                $('#reward-text').text("Error fetching reward.");
                $('#popup-reward').show();
            }
        });
    }

    function redirectHome() {
        localStorage.removeItem('adventureCompleted');
        window.location.href = '../index.php';
    }

    function disableUI() {
        $('.circle-button').prop('disabled', true);
        $('body').css('pointer-events', 'none');
        $('#popup-complete, #popup-reward, #popup-overlay, #adventureCountdown').css('pointer-events', 'auto');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const cat = document.querySelector('.adventureCat');
        const area = document.querySelector('.cat-area');

        const areaWidth = area.clientWidth;
        const areaHeight = area.clientHeight;
        const catWidth = 72;
        const catHeight = 72;

        let positionX = 0;
        let positionY = areaHeight / 2;
        let targetX = 0;
        let targetY = 0;
        let speed = 0.5;
        let directionX = 1;

        function pickNewTarget() {
            targetX = Math.random() * (areaWidth - catWidth);
            targetY = Math.random() * (areaHeight - catHeight);
            directionX = targetX > positionX ? -1 : 1;
            cat.style.transform = `translateY(-50%) scaleX(${directionX})`;
        }

        function moveCat() {
            const dx = targetX - positionX;
            const dy = targetY - positionY;
            const distance = Math.sqrt(dx * dx + dy * dy);

            if (distance < 2) {
                pickNewTarget();
            } else {
                positionX += (dx / distance) * speed;
                positionY += (dy / distance) * speed;

                cat.style.left = `${positionX}px`;
                cat.style.top = `${positionY}px`;
            }

            requestAnimationFrame(moveCat);
        }

        pickNewTarget();
        moveCat();
    });
</script>

</html>