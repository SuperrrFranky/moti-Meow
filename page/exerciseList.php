<?php
require_once '../header.php';
require_once '../back_end/business_logic_layer/ActivityService/activityService.php';
include '../back_end/presentation_layer/UserController/helpButton.php';


$userID = $_SESSION['user_id'] ?? 1;
$activityService = new ActivityService($userID);

$category = $_GET['category'] ?? '';
if (!$category) {
    echo "<h2>No category selected!</h2>";
    exit;
}

$exercises = $activityService->getUserProgress($category);
$hasProgress = false;
$firstUnfinishedExercise = null;

foreach ($exercises as $exercise) {
    // Check if this exercise has been started (i.e., not default state)
    if ($exercise['isComplete'] == 1 || $exercise['isSkip'] == 1 || $exercise['remainingTime'] < $exercise['time']) {
        $hasProgress = true;
    }

    // Find the first unfinished and unskipped exercise
    if (!$exercise['isComplete'] && !$exercise['isSkip'] && !$firstUnfinishedExercise) {
        $firstUnfinishedExercise = $exercise;
    }
}

?>

<style>
    .popup-window-exerciseList {
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

<div class="exercise-list-page">
    <div class="activity-pack-header">
        <div class="back-btn-head" id="back-to-packs"></div>
        <h1><?= htmlspecialchars($category) ?></h1>
    </div>

    <?php if (empty($exercises)) : ?>
        <p>No exercises found for this pack.</p>
    <?php else : ?>
        <div class="exercise-list-container">
            <?php foreach ($exercises as $exercise) : ?>
                <div class="exercise-item"
                    data-activity-id="<?= $exercise['activityID'] ?>"
                    data-name="<?= htmlspecialchars($exercise['name']) ?>"
                    data-description="<?= htmlspecialchars($exercise['description']) ?>"
                    data-video="<?= htmlspecialchars($exercise['videoPath']) ?>"
                    data-time="<?= (int)$exercise['time'] ?>">
                    <div class="image-container">
                        <img src="/res/Activity/Images/<?= $exercise['imagePath'] ?>" alt="img">

                    </div>
                    <div class="desc-container">
                        <h3><?= htmlspecialchars($exercise['name']) ?> (<?= (int)$exercise['time'] ?> sec)</h3>

                        <?php if ($exercise['isComplete'] && !$exercise['isSkip']) : ?>
                            <small style="color: green;">Completed</small>
                        <?php elseif ($exercise['isSkip']) : ?>
                            <small style="color: grey;">Skipped</small>
                        <?php elseif ((int)$exercise['remainingTime'] < (int)$exercise['time']) : ?>
                            <small style="color: orange;">In Progress</small>
                        <?php else : ?>
                            <small style="color: red;">Not Started</small>
                        <?php endif; ?>
                    </div>
                    <div class="arrow-container">
                        <img src="/res/Activity/Images/Icon-Next.png" alt="">
                    </div>


                </div>
            <?php endforeach; ?>
        </div>

        <div class="exercise-buttons">
            <?php
            $allSkipped = true;
            $allNotStarted = true;
            $hasUnfinished = false;

            foreach ($exercises as $ex) {
                if ($ex['isSkip'] == 0) {
                    $allSkipped = false;
                }
                if ($ex['isComplete'] == 1 || $ex['isSkip'] == 1 || $ex['remainingTime'] < $ex['time']) {
                    $allNotStarted = false;
                }
                if ($ex['isComplete'] == 0 && $ex['isSkip'] == 0) {
                    $hasUnfinished = true;
                }
            }
            ?>

            <?php if ($allNotStarted): ?>
                <button id="start-btn"
                    class="start-all-btn"
                    data-activity-id="<?= $exercises[0]['activityID'] ?>">
                    Start
                </button>

            <?php elseif ($hasUnfinished && isset($firstUnfinishedExercise['uActivityID'])): ?>
                <button id="resume-btn"
                    class="start-all-btn"
                    data-u-activity-id="<?= $firstUnfinishedExercise['uActivityID'] ?>"
                    data-activity-id="<?= $firstUnfinishedExercise['activityID'] ?>">
                    Resume
                </button>
                <button id="reset-progress-btn" class="back-btn">Reset Progress</button>

            <?php else: ?>
                <button id="reset-progress-btn" class="back-btn">Reset Progress</button>
            <?php endif; ?>
        </div>


    <?php endif; ?>
</div>

<!-- Popup -->
<div class="popup-window-exerciseList"></div>

<div id="exercise-detail-popup" class="popup hidden">
    <div class="popup-content">
        <h2 id="popup-exercise-name"></h2>
        <video id="popup-exercise-video" controls autoplay loop width="400"></video>
        <p id="popup-exercise-description"></p>
        <button id="close-detail-btn" class="back-btn">Close</button>
    </div>
</div>

<div class="popup-message" style="display:none;">
    <div class="content"></div>
    <div class="popup-buttons">
        <button class="close-popupmessage">Cancel</button>
        <button class="confirm-popupmessage">Confirm</button>
    </div>
</div>


<!-- Provide JS access to category -->
<script>
    const currentCategory = "<?= urlencode($category) ?>";
    const detailPopup = $("#exercise-detail-popup");
    const popupmessage = $(".popup-message");
    const popupWindow = $(".popup-window-exerciseList");
    
    $(document).ready(function() {
        setInterval(() => {
            if (detailPopup.is(":visible") || popupmessage.is(":visible")) {
                popupWindow.show();
            } else {
                popupWindow.hide();
            }
        }, 100); // check every 100ms (you can adjust to 200ms or 500ms for performance)
    });
</script>
</body>

</html>