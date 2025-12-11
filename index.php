<!DOCTYPE html>
<html lang="en">

<?php
session_start();
if (isset($_SESSION['login_success']) && $_SESSION['login_success']) {
    echo "<script>alert('Login successful!');</script>";
    unset($_SESSION['login_success']); // Clear the flag
}
// Clear session manually for testing
if (isset($_GET['clear_session'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
if ((isset($_SESSION['adventure_in_progress']) && $_SESSION['adventure_in_progress'] === true) && ((isset($_SESSION['claimed_adventure'])) && $_SESSION['claimed_adventure'] == false)) {
    echo "<script>
    window.location.href='/page/adventureMode.php';
    </script>";
    // header("Location: page/adventureMode.php");
    exit;
}
include __DIR__ . '/back_end/presentation_layer/UserController/helpButton.php';

?>


<body>
    <?php if (!isset($_SESSION['user_id'])): ?>
        
        <div class="login-popup">
            <div class="login-container">
                <img src="/res/image/paw.png" alt="" class='paw'>
                <h2>Welcome </h2>

                <form action="/back_end/presentation_layer/UserController/loginController.php" method="POST">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Password :</label>
                    <input type="password" id="password" name="password" required>

                    <button type="submit">Log In</button>
                </form>
            </div>

        </div>
    <?php endif; ?>



    <?php
    require 'header.php';
    $bedPath = isset($_SESSION['equipped_images']['bed']) && !empty($_SESSION['equipped_images']['bed'])
        ? '/res/image/roomDeco/bed/' . $_SESSION['equipped_images']['bed']
        : '';
    $foodBowlPath = isset($_SESSION['equipped_images']['foodBowl']) && !empty($_SESSION['equipped_images']['foodBowl'])
        ? '/res/image/roomDeco/foodBowl/' . $_SESSION['equipped_images']['foodBowl']
        : '';
    $paintingPath = isset($_SESSION['equipped_images']['painting']) && !empty($_SESSION['equipped_images']['painting'])
        ? '/res/image/roomDeco/painting/' . $_SESSION['equipped_images']['painting']
        : '';

    $picture1Path = isset($_SESSION['equipped_images']['picture1']) && !empty($_SESSION['equipped_images']['picture1'])
        ? '/res/image/roomDeco/picture1/' . $_SESSION['equipped_images']['picture1']
        : '';

    $picture2Path = isset($_SESSION['equipped_images']['picture2']) && !empty($_SESSION['equipped_images']['picture2'])
        ? '/res/image/roomDeco/picture2/' . $_SESSION['equipped_images']['picture2']
        : '';

    $plantPath = isset($_SESSION['equipped_images']['plant']) && !empty($_SESSION['equipped_images']['plant'])
        ? '/res/image/roomDeco/plant/' . $_SESSION['equipped_images']['plant']
        : '';

    $window1Path = isset($_SESSION['equipped_images']['window1']) && !empty($_SESSION['equipped_images']['window1'])
        ? '/res/image/roomDeco/window1/' . $_SESSION['equipped_images']['window1']
        : '';

    $window2Path = isset($_SESSION['equipped_images']['window2']) && !empty($_SESSION['equipped_images']['window2'])
        ? '/res/image/roomDeco/window2/' . $_SESSION['equipped_images']['window2']
        : '';

    $window3Path = isset($_SESSION['equipped_images']['window3']) && !empty($_SESSION['equipped_images']['window3'])
        ? '/res/image/roomDeco/window3/' . $_SESSION['equipped_images']['window3']
        : '';

    $roomPath = isset($_SESSION['equipped_images']['room']) && !empty($_SESSION['equipped_images']['room'])
        ? '/res/image/roomDeco/room/' . $_SESSION['equipped_images']['room']
        : '';

    $waterPath = isset($_SESSION['equipped_images']['water']) && !empty($_SESSION['equipped_images']['water'])
        ? '/res/image/roomDeco/water/' . $_SESSION['equipped_images']['water']
        : '';

    $playground1Path = isset($_SESSION['equipped_images']['playground1']) && !empty($_SESSION['equipped_images']['playground1'])
        ? '/res/image/roomDeco/playground1/' . $_SESSION['equipped_images']['playground1']
        : '';

    $playground2Path = isset($_SESSION['equipped_images']['playground2']) && !empty($_SESSION['equipped_images']['playground2'])
        ? '/res/image/roomDeco/playground2/' . $_SESSION['equipped_images']['playground2']
        : '';

    $playground3Path = isset($_SESSION['equipped_images']['playground3']) && !empty($_SESSION['equipped_images']['playground3'])
        ? '/res/image/roomDeco/playground3/' . $_SESSION['equipped_images']['playground3']
        : '';
    ?>


    <div class=bodyContainer>
        <div class="mainContainer">
            <div class="room" style="
    background: url('<?php echo $roomPath; ?>') no-repeat center center;
    background-size: contain;
">
                <img
                    id="doorImage"
                    src="/res/image/door.png"
                    class="decoration door"
                    data-x="68"
                    data-y="45"
                    onclick="openAdventurePopUp(this.id)" />


                <!-- Background overlay -->
                <div id="adventure-popup-overlay" class="adventure-popup-overlay hidden"></div>

                <!-- Popup Container for going adventure-->
                <div id="adventurePopupContainer" class="popup hidden"></div>

                <!-- Window -->
                <img src="<?php echo $window1Path; ?>" class="decoration window1" data-x="40" data-y="35" />
                <img src="<?php echo $window2Path; ?>" class="decoration window2" data-x="26" data-y="44" />
                <img src="<?php echo $window3Path; ?>" class="decoration window3" data-x="76" data-y="41" />


                <div class="decoration photoSet" data-x="58" data-y="35">
                    <img src="<?php echo $picture1Path; ?>" class="decoration photoGallery1 picture1" />
                    <img src="<?php echo $paintingPath; ?>" class="decoration photoGallery2 painting" />
                    <img src="<?php echo $picture2Path; ?>" class="decoration photoGallery3 picture2" />
                    <p id="show-photoGallery" class="photoGalleryWord">Photo Gallery</p>
                    <div id="photoGallery-container">
                        <div id="photo-gallery"></div>
                    </div>
                </div>

                <!-- Cat Bed -->
                <img src="<?php echo $bedPath; ?>" class="decoration bed" data-x="50" data-y="42" />

                <!-- Plant -->
                <img src="<?php echo $plantPath; ?>" class="decoration plant" data-x="44" data-y="40" />

                <!-- Playground 3 (Left) -->
                <img src="<?php echo $playground3Path; ?>" class="decoration playground3" data-x="23" data-y="55" />

                <!-- Playground 2 (Right) -->
                <img src="<?php echo $playground2Path; ?>" class="decoration playground2" data-x="83" data-y="54" />

                <!-- foodBowl Bowl -->
                <img src="<?php echo $foodBowlPath; ?>" class="decoration foodBowl" data-x="47" data-y="63" />

                <!-- Water Bowl -->
                <img src="<?php echo $waterPath; ?>" class="decoration water" data-x="52" data-y="65" />

                <!-- Playground 1 (Bottom Left) -->
                <img src="<?php echo $playground1Path; ?>" class="decoration playground1" data-x="35" data-y="45" />


                <div class="cat" data-x="45" data-y="74">
                    <div class="interactionContainer">
                        <img
                            src="/res/image/interaction/fish-bones.png"
                            alt=""
                            class="feed interaction" />
                        <img
                            src="/res/image/interaction/chat-gpt.png"
                            alt=""
                            class="chat interaction" />
                        <img
                            src="/res/image/interaction/click.png"
                            alt=""
                            class="click interaction" />
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


        </div>

        <!-- Button Wrapper -->
        <div class="button-wrapper">
            <!-- Hidden Button Group (initially hidden) -->
            <div class="navButton" id="buttonGroup" style="display: none;">
                <button class="circle-button" id="claimRewardBtn"><img src="res/image/calendar.png"></button>
                <button class="circle-button" onclick="window.location.href='/page/SelectActivityPacks.php';">
                    <img src="res/image/exercise.png">
                </button>
                <button class="circle-button" id="show-homeDeco"><img src="res/image/decoration.png"></button>
                <button class="circle-button" id="adventureStoryPopUp"><img src="res/image/history.png"></button>
            </div>

            <!-- Toggle Button (ALWAYS visible at bottom) -->
            <button class="circle-toggle-button" id="toggleMenuBtn">+</button>
        </div>
    </div>

    </div>
    
    <div id="dailyRewardOverlay" class="dailyRewardOverlay-hidden"></div>
    <div id="dailyCheckinPopup" height: 100px; width: 100px; display: none;>
        <div id="popupContainer"></div>
    </div>

    <div id="dailyRewardPopup" class="popup hidden">
        <h1>Congratulations! You got</h1>
        <img id="dailyRewardImg" src="" alt="Your Reward" />
        <p id="dailyRewardName"></p>
        <button id="closeDailyReward">OKAY</button>
    </div>
    <div id="achievementOverlay" class="achievementOverlay-hidden"></div>
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
            <img src="res/image/adventure/warning-icon.png" alt="warning-icon" class="ins-warning-icon">
        </div>
        <button id="closeCheckedIn">OKAY</button>
    </div>

    <div id="popup-complete" class="popup hidden">
        <p>Adventure Completed!</p>
        <button onclick="showReward()">OKAY</button>
    </div>

    <div id="storyPopupOverlay" class="story-overlay hidden"></div>
    <div id="storyPopup" class="story-popup hidden">
        <div class="story-header">
            <h2>Adventure Stories</h2>
            <button class="close-btn-adventure" onclick="closeStoryPopup()">&times;</button>
        </div>
        <div class="story-list"></div>
    </div>

    <div id="helpOverlay" class="help-overlay hidden"></div>
    <script src="/script/script.js"></script>


</body>

</html>