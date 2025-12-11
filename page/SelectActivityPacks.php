<?php require_once '../header.php';
include '../back_end/presentation_layer/UserController/helpButton.php';
?>

<div class="select-activity-pack-page">
    <div class="activity-pack-header">
        <div class="back-btn-head" id="back-to-home"></div>
        <h1>Activity Packs</h1>
    </div>

    <div class="activity-pack-container">

        <div class="pack-card" data-category="" style="pointer-events: none; cursor: not-allowed;">
            <img src="/res/Activity/Images/Skipping at Activity Pack Page.png" alt="">
        </div>

        <div class="pack-card" data-category="Managing Stress and Anxiety Pack">
            <img src="/res/Activity/Images/Managing Stress & Anxiety Pack.png" alt="Managing Stress Pack">
            <h3>Managing Stress & Anxiety Pack</h3>
        </div>

        <div class="pack-card" data-category="Relaxation and Calm Pack">
            <img src="/res/Activity/Images/Relaxation & Calm Pack.png" alt="Relaxation Pack">
            <h3>Relaxation & Calm Pack</h3>
        </div>

        <div class="pack-card" data-category="Movement and Energy Pack">
            <img src="/res/Activity/Images/Movement & Energy Pack.png" alt="Movement Pack">
            <h3>Movement & Energy Pack</h3>
        </div>
    </div>

    <p class="motivational-quote">
        "Your journey to a happier, more fulfilled you starts with every exercise."
    </p>
</div>

<script src="/script/script.js"></script>

</body>

</html>