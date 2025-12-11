<?php
session_start();
require_once '../business_logic_layer/userService/AchievementCheckerService.php';
require_once '../../_base.php';

$_SESSION['user_id'] = 1;

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

$userID = $_SESSION['user_id'];

$checker = new AchievementCheckerService($userID);
$unlocked = $checker->checkAchievements();

echo json_encode([
    "status" => "success",
    "unlockedAchievements" => $unlocked
]);
?>