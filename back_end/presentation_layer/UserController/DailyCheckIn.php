<?php 
session_start();
require_once '../../../_base.php';
require_once '../../business_logic_layer/userService/DailyCheckInService.php';
require_once '../../business_logic_layer/userService/AchievementCheckerService.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

$userID = $_SESSION['user_id'];

if (
    $_SERVER['REQUEST_METHOD']==='GET'
    && isset($_GET['action'])
    && $_GET['action']==='hasCheckedIn'
  ) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user_id'])) {
      echo json_encode(['checkedIn'=>false]);
      exit;
    }
    require_once '../../data_access_layer/UserDAO.php';
    $dao       = new UserDAO($_SESSION['user_id']);
    $lastLogin = $dao->getUserLastLogin($_SESSION['user_id']);
    $checkedIn = substr($lastLogin,0,10)===date('Y-m-d');
    echo json_encode(['checkedIn'=>$checkedIn]);
    exit;
  }

$checkInService = new DailyCheckInService($userID);
$result = $checkInService->processDailyCheckIn();

if ($result['status'] === 'success') { //Check for Achievement Unlocks
    $checker = new AchievementCheckerService($userID);
    $unlocked = $checker->checkAchievements();
    $result['unlockedAchievements'] = $unlocked;
}

header('Content-Type: application/json');
echo json_encode($result);
