<?php
require_once(__DIR__ . '/../../data_access_layer/activityDAO.php');
require_once(__DIR__ . '/../InventoryService/inventoryService.php');
require_once(__DIR__ . '/../InventoryService/inventory.php');

class ActivityService
{
    private $activityDAO;
    private $userID;

    public function __construct($userID)
    {
        $this->userID = $userID;
        $this->activityDAO = new ActivityDAO($userID);
    }

    public function getExercises($category)
    {
        return $this->activityDAO->getExercises($category);
    }

    public function getExerciseDetail($activityID)
    {
        return $this->activityDAO->getExerciseDetail($activityID);
    }

    public function insertNewUserActivity($activityID)
    {
        $progress = $this->activityDAO->getUserActivityProgress($activityID);
        if (!$progress) {
            return $this->activityDAO->insertNewUserActivity($activityID);
        }
        return true;
    }

    public function saveProgress($uActivityID, $remainingTime)
    {
        return $this->activityDAO->saveRemainingTime($uActivityID, $remainingTime);
    }

    public function completeExercise($uActivityID)
    {
        return $this->activityDAO->completeExercise($uActivityID);
    }

    public function skipExercise($uActivityID)
    {
        return $this->activityDAO->skipExercise($uActivityID);
    }

    // ✅ FIXED METHOD — ensures all activities show, even if progress deleted
    public function getUserProgress($category)
    {
        $progress = $this->activityDAO->getProgressByCategory($category);
        if (empty($progress)) {
            $exercises = $this->activityDAO->getExercises($category);
            foreach ($exercises as $ex) {
                $this->activityDAO->insertNewUserActivity($ex['activityID']);
            }
            $progress = $this->activityDAO->getProgressByCategory($category);
        }
        return $progress;
    }

    public function resetProgress($category)
    {
        return $this->activityDAO->resetProgress($category);
    }

    public function getSkippedCount($category)
    {
        return $this->activityDAO->countSkippedExercises($category);
    }

    public function unlockReward()
    {
        $inventoryService = new InventoryService($this->userID);
        $reward = $inventoryService->rewardPlayer(); // can return Inventory OR string message
    
        // If a message is returned (e.g., stamina refill notice)
        if (is_string($reward)) {
            $reward = $inventoryService->getItemByID(47); // Assuming 47 is the ID for stamina refill
            return [
                'type' => 'reward',
                'data' => $reward
            ];
        }
    
        // If an Inventory object is returned
        if ($reward) {
            return [
                'type' => 'reward',
                'data' => [
                    'inventoryID' => $reward->getID(),
                    'category'    => $reward->getCategory(),
                    'name'        => $reward->getName(),
                    'imgPath'     => $reward->getImage(),
                    'unlockAt'    => $reward->getUnlockDate(),
                    'quantity'    => $reward->getQuantity()
                ]
            ];
        }
    
        // In case of failure
        return [
            'type' => 'fail',
            'data' => 'Unable to unlock any reward.'
        ];
    }
    
    
    
    public function getUserActivityProgressByActivityID($activityID)
    {
        return $this->activityDAO->getUserActivityProgress($activityID);
    }

    public function getNextExercise($currentActivityID, $category)
    {
        $progressList = $this->getUserProgress($category);
        $foundCurrent = false;
        foreach ($progressList as $exercise) {
            if ($foundCurrent) {
                if ($exercise['isComplete'] == 0 && $exercise['isSkip'] == 0) {
                    return $exercise;
                }
            }
            if ($exercise['activityID'] == $currentActivityID) {
                $foundCurrent = true;
            }
        }
        return null;
    }

    public function checkSession($activityID, $uActivityID) {
        $isActive = $this->activityDAO->getActiveUActivityID($this->userID);
    
        if ($isActive && $isActive != $uActivityID) {
            return [
                "status" => "active",
                "message" => "Another exercise is already in progress."
            ];
        } elseif (!empty($uActivityID) && is_numeric($uActivityID)) {
            $this->activityDAO->markSessionActive($uActivityID);
            return [
                "status" => "started",
                "message" => "Exercise started successfully.",
                "activityID" => $activityID,
                "uActivityID" => $uActivityID
            ];
        } else {
            return [
                "status" => "fail",
                "message" => "Invalid session ID."
            ];
        }
    }    
    

    public function unsetActiveExercise($uActivityID) {
        return $this->activityDAO->markSessionInactive($uActivityID);
    }
    
    public function setActiveExercise($uActivityID) {
        return $this->activityDAO->markSessionActive($uActivityID);
    }
    public function updateLastActive($uActivityID) {
        return $this->activityDAO->updateLastActive($uActivityID);
    }
    
}
