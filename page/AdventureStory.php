<?php
require '../back_end/business_logic_layer/AdventureService/adventureService.php';

$adventureId = $_GET['adventureID'] ?? null;

if ($adventureId) {
    $adventureStory = AdventureService::getAdventureByID($adventureId);
    $story = $adventureStory['story'] ?? '';
    $backgroundMedia = $adventureStory['background_media'] ?? '';
    $title = htmlspecialchars($adventureStory['adventure_title'] ?? '');
    $sentences = preg_split('/(?<=[.!?])\s+/', $story, -1, PREG_SPLIT_NO_EMPTY);
} else {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moti Meow</title>
    <link rel="stylesheet" href="/style/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('../res/image/adventure/<?php echo $backgroundMedia; ?>');
            background-size: cover;
            background-repeat: no-repeat;
            height: 100vh;
            position: relative;
            font-family: sans-serif;
            overflow: hidden;
        }

        nav {
            border-radius: 0 0 64px 64px;
            background: #F1E7DF;
            height: 10%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5% 2%;
        }

        .back-btn {
            background: none;
            border: none;
            font-size: 30px;
            cursor: pointer;
        }

        .story-title {
            font-size: 22px;
            font-weight: bold;
        }

        .story-scroll-container {
            position: absolute;
            top: 15%;
            bottom: 0;
            left: 0;
            right: 0;
            overflow-y: auto;
            padding: 30px;
            border: solid;
            margin: 0 10px 10px 10px;
        }

        .story-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 30px;
        }

        .story-row {
            display: flex;
            align-items: flex-start;
        }

        .story-cat {
            width: 72px;
            height: 72px;
            background: url('../res/image/cat/cat1.png') no-repeat;
            background-size: contain;
            margin-right: 10px;
        }

        .story-bubble {
            background-color: rgba(0, 0, 0, 0.67);
            color: white;
            padding: 15px 20px;
            border-radius: 25px;
            max-width: 70%;
            font-size: 25px;
            position: relative;
            font-weight: bolder;
            font-family: 'Comic Sans MS', cursive, sans-serif;

        }

        .story-bubble[data-has-next]::after {
            content: '...';
            color: #ccc;
            font-weight: bold;
            margin-left: 8px;
            animation: blink 1s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .click-hint {
            position: absolute;
            bottom: 5%;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(255, 255, 255, 0.85);
            color: #333;
            padding: 8px 16px;
            border-radius: 16px;
            font-size: 14px;
            animation: blink 1.5s infinite;
        }

        .ending-hint {
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
            color: #ddd;
            display: none;
            animation: fadeIn 1s ease forwards;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        #divider {
            border: none;
            border-top: 5px solid #444;
            border-color: #000000;
            border-radius: 10px;
            margin: 10px 0;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .popup-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #e1c8b3;
            padding: 30px;
            border-radius: 20px;
            width: 300px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 2000;
        }

        .popup-box p {
            margin-bottom: 20px;
            font-family: 'Comic Sans MS', cursive, sans-serif;
        }

        .popup-box button {
            background-color: #8fffae;
            color: #000;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;
        }

        .popup-box button:hover  {
            background-color: #7beaa3;
            color: #000;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <nav>
        <button class="back-btn" onclick="window.location.href='../index.php'">&#8592;</button>
        <div class="story-title"><?php echo $title; ?></div>
        <div style="width:40px"></div>
    </nav>

    <div class="click-hint" id="clickHint">Click anywhere to continue...</div>

    <div class="story-scroll-container">
        <div class="story-container" id="storyContainer"></div>
        <div class="ending-hint" id="endingHint">You’ve reached the end of the story.</div>
    </div>

    <div class="popup-overlay" id="popupOverlay">
        <div class="popup-box">
            <p>Story Complete!</p>
            <button onclick="goHome()">OKAY</button>
        </div>
    </div>

    <script>
        const sentences = <?php echo json_encode($sentences); ?>;
        let index = 0;

        document.body.addEventListener('click', function() {
            const hint = document.getElementById('clickHint');
            if (hint) hint.style.display = 'none';

            if (index < sentences.length) {
                const container = document.getElementById('storyContainer');
                const row = document.createElement('div');
                row.classList.add('story-row');

                const cat = document.createElement('div');
                cat.classList.add('unlock-adv-cat-icon');

                const bubble = document.createElement('div');
                bubble.classList.add('story-bubble');
                bubble.textContent = sentences[index];

                if (index < sentences.length - 1) {
                    bubble.setAttribute('data-has-next', 'true');
                } else {
                    document.getElementById('endingHint').style.display = 'block';
                    const divider = document.createElement('hr');
                    divider.id = 'divider';
                    document.getElementById('endingHint').appendChild(divider);
                }

                row.appendChild(cat);
                row.appendChild(bubble);
                container.appendChild(row);

                index++;
            } else {
                document.getElementById('popupOverlay').style.display = 'flex';
            }
        });

        function goHome() {
            window.location.href = '../index.php';
        }
    </script>
</body>

</html>