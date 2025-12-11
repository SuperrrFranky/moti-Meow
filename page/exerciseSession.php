<?php
require_once '../header.php';
require_once '../back_end/business_logic_layer/ActivityService/activityService.php';
require_once '../_base.php';
include '../back_end/presentation_layer/UserController/helpButton.php';


$userID = $_SESSION['user_id'] ?? 1;
$activityService = new ActivityService($userID);

$activityID = $_GET['activityID'] ?? '';
$uActivityID = $_GET['uActivityID'] ?? 0;
$category = $_GET['category'] ?? '';

$exercise = $activityService->getExerciseDetail($activityID);
if (!$exercise) {
    echo "<h2>Exercise not found!</h2>";
    exit;

}

$uActivityID=$exercise['uActivityID'] ?? 0;
$activeResponse = $activityService->checkSession($activityID, $uActivityID);

if ($activeResponse['status'] === 'active') {
    echo "<script>alert('⚠️ You already have an active session.'); window.location.href = '/page/SelectActivityPacks.php';</script>";
    exit;
} elseif ($activeResponse['status'] === 'fail') {
    echo "<h2>Session invalid. Please try again.</h2>";
    echo "<script>console.log('Session check response: " . json_encode($activeResponse) . "');</script>";
    echo "<script>console.log('".$activityID .$uActivityID."');</script>";
    exit;
}




if (!$activityID || !$category) {
    echo "<h2>No exercise or category selected!</h2>";
    exit;
}



$remainingTime = $exercise['remainingTime'] ?? (int)$exercise['time'];
$originalTime = (int)$exercise['time'];
?>

<style>
    .popup-window-exerciseSession {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: none;
        z-index: 888;
    }
</style>

<div class="exercise-session-page">
    <div class="exercise-session-header">
        <h2 id="exercise-name"><?= htmlspecialchars($exercise['name']) ?></h2>
    </div>

    <div class="exercise-content">
        <video id="exercise-video" src="/res/Activity/Videos/<?= htmlspecialchars($exercise['videoPath']) ?>" autoplay muted loop controls></video>
        <p id="exercise-description"><?= htmlspecialchars($exercise['description']) ?></p>

        <div id="countdown-timer"
            data-original-time="<?= (int)$originalTime ?>"
            data-time="<?= (int)$remainingTime ?>"
            data-category="<?= htmlspecialchars($category) ?>"
            data-current-activity-id="<?= (int)$exercise['activityID'] ?>"
            data-u-activity-id="<?= (int)($exercise['uActivityID'] ?? 0) ?>">
            Loading...
        </div>

        <div class="session-controls">
            <button id="pause-btn" class="pause-btn">
                <img src="/res/Activity/Images/pause_button.png" alt="Pause">
            </button>
            <button id="skip-btn" class="skip-btn">
                <img src="/res/Activity/Images/next_button.png" alt="Next">
            </button>
        </div>
    </div>
</div>

<div class="popup-window-exerciseSession"></div>

<div id="pause-popup" class="pause-popup hidden">
    <h3>Exercise Paused</h3>
    <div class="pause-buttons">
        <button id="resume-btn" class="resume-btn">Resume</button>
        <button id="restart-btn" class="restart-btn">Restart</button>
        <button id="exit-btn" class="exit-btn">Exit to List</button>
    </div>
</div>

<div id="rest-popup" class="pause-popup hidden">
    <h3>Resting Time</h3>
    <div id="rest-timer">10</div>
    <div class="pause-buttons">
        <button id="skip-rest-btn" class="resume-btn">Skip Rest</button>
    </div>
</div>

<div class="popup-message" style="display:none;">
    <div class="content"></div>
    <div class="reward-detail" style="text-align:center; margin-top: 10px;"></div>
    <div class="popup-buttons">
        <button class="goto-list">Back to List</button>
        <button class="goto-home">Back to Home</button>
    </div>
</div>

<script>
    const popupWindow = $(".popup-window-exerciseSession");
    const pausePopup = $("#pause-popup");
    const restPopup = $("#rest-popup");
    const popupMessage = $(".popup-message");

    $(document).ready(function() {
        setInterval(() => {
            if (popupMessage.is(":visible") || pausePopup.is(":visible") || restPopup.is(":visible")) {
                popupWindow.show();
            } else {
                popupWindow.hide();
            }
        }, 100); // check every 100ms (you can adjust to 200ms or 500ms for performance)
    });


</script>

</body>

</html>