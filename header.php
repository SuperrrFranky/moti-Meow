
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moti Meow</title>
    <link rel="stylesheet" href="/style/style.css">
    <link rel="stylesheet" href="/style/mobile.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/script/script.js"></script>
</head>

<body>
    <nav>
        <div class="catProfile">
            <div class="catProfileIcon" id="catProfileIcon">
                <img src="/res/image/profile_pic.png" class="catProfilePic" alt="profile">
            </div>
            <h2 class="catName" id="catName">xxxx xxxx</h2>
        </div>
        <div class="staminaBar">
            <img src="/res/image/adventure/Mouse.gif" alt="">
            <h2 id="staminaValue">Loading...</h2>
            <h3 id="staminaCountDown">Loading...</h3>
            <img src="/res/image/helpLogo.png" alt="" class="helpLogo" id="helpLogo">
        </div>
    </nav>

    <div id="popup-general-overlay" class="popup-general-overlay hidden"></div>
    <div id="popup-window">
        <span id="popup-close-btn" onclick=closePopup()></span>
        <div id="popup-content"></div>
    </div>

    <div id="popup-catP-overlay" class="popup-catP-overlay hidden"></div>
    <div id="popup-catProfile">
        <span id="popup-close-btn2" onclick=closePopup2()></span>
        <div id="popup-content-profile"></div>
    </div>

    <div id="popup-deco-overlay" class="popup-decoration-overlay hidden"></div>
    <div id="popup-deco">
        <div class="inventory-header2">Inventory</div>
        <div id="popup-deco-container">
            <span id="popup-close-btn3" onclick=closePopup3()></span>
            <div id="popup-content-deco"></div>
        </div>
    </div>

    <!-- Page-specific content will start below this point -->