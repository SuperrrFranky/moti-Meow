<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
$userID = $_SESSION['user_id'];

require_once '../header.php';
require_once '../back_end/business_logic_layer/userService/AchievementCheckerService.php';
require_once __DIR__ . '/../back_end/presentation_layer/UserController/helpButton.php';

$service = new AchievementCheckerService($userID);
$achievements = $service->getAllWithStatus();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Moti Meow — Achievements</title>
    <link rel="stylesheet" href="/style/style.css">
    <style>
        /* popup wrapper — bump it up to 700px wide */
        #achievement-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #f1e0d6;
            border-radius: 16px;
            padding: 32px;
            width: 700px;
            max-height: 80vh;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            z-index: 900;
        }
        @media screen and (max-width: 431px) {
            #achievement-popup {
                width: 80%;
            }
        }

        /* close button */
        #achievement-popup .popup-close-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 32px;
            height: 32px;
            background: url('/res/image/popup_close_button.png') no-repeat center center;
            background-size: contain;
            cursor: pointer;
        }

        /* header */
        .achievement-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            justify-content: center;
        }

        .achievement-header img {
            width: 72px;
            height: 72px;
        }

        .achievement-header h1 {
            font-size: 2rem;
            margin: 0;
        }

        /* list */
        .achievement-list {
            max-height: calc(80vh - 200px);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding-right: 8px;
        }

        .achievement-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            border-radius: 8px;
            font-size: 1.25rem;
            font-weight: bold;
            background: #bbb;
        }

        .achievement-item.unlocked {
            background: #8fffae;
        }

        .achievement-item img {
            width: 40px;
            height: 40px;
        }
    </style>
</head>

<body>
    <div class="bodyContainer">

        <div id="achievement-popup">
            <!-- close btn -->
            <span class="popup-close-btn" onclick="window.history.back();"></span>


            <!-- header -->
            <div class="achievement-header">
                <img src="/res/image/achivement.png" alt="Achievement">
                <h1>Achievements</h1>
            </div>

            <!-- dynamic list -->
            <div class="achievement-list">
                <?php foreach ($achievements as $ach): ?>
                    <?php $cls = $ach['isUnlocked'] ? 'unlocked' : 'locked'; ?>
                    <div class="achievement-item <?= $cls ?>">
                        <img src="<?= htmlspecialchars($ach['iconPath']) ?>" alt="">
                        <span><?= htmlspecialchars($ach['title']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script src="/script/script.js"></script>
</body>

</html>