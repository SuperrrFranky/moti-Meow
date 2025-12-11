<?php
require_once(__DIR__ . '/../../business_logic_layer/ActivityService/activityService.php');
require_once(__DIR__ . '/../../../_base.php');

$userID = $_SESSION['user_id'] ?? 1;
$activityService = new ActivityService($userID);

if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    switch ($action) {
        case 'saveProgress':
            $uActivityID = $_POST['uActivityID'] ?? '';
            $remainingTime = $_POST['remainingTime'] ?? '';
            $unsetSession = isset($_POST['unsetSession']) && $_POST['unsetSession'] == 1;

            if ($uActivityID !== '' && $remainingTime !== '') {
                $result = $activityService->saveProgress($uActivityID, $remainingTime);

                // 🔁 If requested, also unset this activity from active session
                if ($result && $unsetSession) {
                    $activityService->unsetActiveExercise($uActivityID); // ✅ Ensure this function exists
                }

                echo json_encode(['status' => $result ? 'success' : 'fail']);
            } else {
                echo json_encode(['status' => 'fail', 'message' => 'Missing uActivityID or remainingTime']);
            }
            break;


        case 'completeExercise':
            $uActivityID = $_POST['uActivityID'] ?? '';
            if ($uActivityID) {
                $result = $activityService->completeExercise($uActivityID);
                echo json_encode(['status' => $result ? 'success' : 'fail']);
            } else {
                echo json_encode(['status' => 'fail', 'message' => 'Missing uActivityID']);
            }
            break;

        case 'skipExercise':
            $uActivityID = $_POST['uActivityID'] ?? '';
            if ($uActivityID) {
                $result = $activityService->skipExercise($uActivityID);
                echo json_encode(['status' => $result ? 'success' : 'fail']);
            } else {
                echo json_encode(['status' => 'fail', 'message' => 'Missing uActivityID']);
            }
            break;

        case 'getNextExercise':
            $currentActivityID = $_POST['currentActivityID'] ?? '';
            $currentUActivityID = $_POST['uActivityID'] ?? '';
            $category = $_POST['category'] ?? '';
            
            if ($currentUActivityID) {
                $activityService->unsetActiveExercise($currentUActivityID);
            }


            if ($currentActivityID && $category) {
                $nextExercise = $activityService->getNextExercise($currentActivityID, $category);

                if ($nextExercise) {
                    // ✅ 1. Activate next
                    $activityService->setActiveExercise($nextExercise['uActivityID']);

                    echo json_encode([
                        'status' => 'success',
                        'nextActivityID' => $nextExercise['activityID'],
                        'uActivityID' => $nextExercise['uActivityID']
                    ]);
                } else {
                    echo json_encode(['status' => 'no_more']);
                }
            } else {
                echo json_encode(['status' => 'fail', 'message' => 'Missing currentActivityID or category']);
            }
            break;



        case 'resetProgress':
            $category = $_POST['category'] ?? '';
            $category = urldecode($category);
            $result = $activityService->resetProgress($category);

            if (!isset($_SESSION['rewarded_categories'])) {
                $_SESSION['rewarded_categories'] = [];
            }
            $_SESSION['rewarded_categories'][$category] = false;

            echo json_encode($result);
            break;


        case 'checkAndReward':
            $category = $_POST['category'] ?? '';
            if ($category) {
                if (!isset($_SESSION['rewarded_categories'])) {
                    $_SESSION['rewarded_categories'] = [];
                }

                if (isset($_SESSION['rewarded_categories'][$category]) && $_SESSION['rewarded_categories'][$category] === true) {
                    // Already rewarded
                    echo json_encode([
                        'status' => 'message',
                        'message' => '🎁 You’ve already claimed a reward for this category.'
                    ]);
                    break;
                }


                $skippedCount = $activityService->getSkippedCount($category);
                if ($skippedCount <= 3) {
                    $rewardResponse = $activityService->unlockReward();
                    if ($rewardResponse['type'] === 'reward') {
                        $_SESSION['rewarded_categories'][$category] = true;

                        echo json_encode([
                            'status' => 'reward',
                            'reward' => $rewardResponse['data']
                        ]);
                    } elseif ($rewardResponse['type'] === 'message') {
                        $_SESSION['rewarded_categories'][$category] = true;

                        echo json_encode([
                            'status' => 'message',
                            'message' => $rewardResponse['data']
                        ]);
                    } else {
                        $_SESSION['rewarded_categories'][$category] = false;

                        echo json_encode([
                            'status' => 'fail',
                            'message' => $rewardResponse['data']
                        ]);
                    }
                } else {
                    $_SESSION['rewarded_categories'][$category] = false;

                    echo json_encode(['status' => 'no_reward']);
                }
            } else {
                echo json_encode(['status' => 'fail', 'message' => 'Missing category']);
            }
            break;
            
            case 'heartbeat':
                $uActivityID = $_POST['uActivityID'] ?? '';
                if ($uActivityID) {
                    $activityService->updateLastActive($uActivityID);
                    echo json_encode(['status' => 'ok']);
                }
                break;
            

        default:
            echo json_encode(['status' => 'fail', 'message' => 'Invalid action']);
            break;
    }
    exit;
}
