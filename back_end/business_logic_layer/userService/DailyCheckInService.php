<?php
require_once __DIR__ . '/../InventoryService/inventoryService.php';
require_once __DIR__ . '/../../data_access_layer/UserDAO.php';

class DailyCheckInService
{
    private $userID;
    private $UserDAO;
    private $inventoryService;

    public function __construct($userID)
    {
        $this->userID = $userID;
        $this->inventoryService = new InventoryService($userID);
        $this->UserDAO = new UserDAO($userID);
    }

    public function processDailyCheckIn()
    {
        try {
            // Step 1: Check if user already checked in today
            $lastLogin = $this->UserDAO->getUserLastLogin($this->userID);

            if ($lastLogin == date('Y-m-d')) {
                return ["status" => "already", "message" => "You already checked in today!"];
            }

            // Step 2: Reset streak if missed a day
            $this->UserDAO->resetLoginStreakIfMissed();

            // 3) get the Inventory object back, not just a string
            $rewardItem = $this->inventoryService->rewardPlayer();

            // 4) update last login
            $this->UserDAO->updateUserLastLogin($this->userID);

            // Build a clean response payload
            if (is_object($rewardItem)) {
                // normal reward case
                return [
                    'status'  => 'success',
                    'message' => 'Check-in successful!',
                    'reward'  => [
                        'category' => $rewardItem->getCategory(),
                        'image'    => $rewardItem->getImage(),
                        'name'     => $rewardItem->getName(),
                    ],
                ];
            }

            if (is_string($rewardItem)) {
                // “you got them all” consumable fall-back
                return [
                    'status'  => 'success',
                    'message' => 'Check-in successful!',
                    'reward'  => [
                        'category' => 'consumable',
                        'image'    => 'res/image/adventure/mouse.gif', 
                        'name'     => 'Stamina Refill',               
                    ],
                ];
            }
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
