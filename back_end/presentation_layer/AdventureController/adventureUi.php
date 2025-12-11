<?php
require_once 'adventureController.php';

$userId = $_SESSION['user_id'] ?? 1;

if ($_POST['action'] === 'getStoryList') {

    $stories = AdventureService::getAdventureList($userId);
    echo json_encode(['status' => 'success', 'html' => generateStoryHTML($stories)]);
    exit;
}

function generateStoryHTML($storyList)
{
    $html = '<div class="story-container">';

    if (!empty($storyList)) {
        foreach ($storyList as $story) {
            $title = htmlspecialchars($story['adventure_title']);
            $adventureId = $story['adventureID'];
            $html .= '<div class="story-item" onclick="location.href=\'../page/AdventureStory.php?adventureID=' . $adventureId . '\'">';
            $html .= '<div class="unlock-adv-cat-icon"></div>';
            $html .= '<div class="story-title">' . $title . '</div>';
            $html .= '</div>';
        }
    } else {
        $html .= '<div class="story-item">';
        $html .= '<img src="/res/image/cat/cat1.png" alt="cat-icon">';
        $html .= '<div class="story-title">No adventure stories found.</div>';
        $html .= '</div>';
    }

    $html .= '</div></div>';
    return $html;
}
