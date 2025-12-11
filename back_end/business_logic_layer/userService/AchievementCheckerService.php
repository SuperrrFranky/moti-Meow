<?php
require_once __DIR__ . '/../../data_access_layer/UserDAO.php';
require_once __DIR__ . '/../InventoryService/inventoryService.php';

class AchievementCheckerService
{
    private UserDAO $dao;
    private int     $userID;

    public function __construct(int $userID)
    {
        $this->userID = $userID;
        $this->dao = new UserDAO($userID);
    }

    public function checkAchievements(): array
    {
        $unlocked = [];

        // 1) check streak‐based
        $streak = $this->dao->getLoginStreak();
        foreach ([1=>5, 2=>10, 3=>15] as $achID => $needed) {
            if ($streak >= $needed
             && ! $this->dao->hasUnlockedAchievement($achID))
            {
                $this->dao->unlockAchievement($achID);
                $unlocked[] = $this->dao->getAchievementDetail($achID);
                // reward player for achievement:
                (new InventoryService($this->userID))->rewardPlayer();
            }
        }

        // 2) accessories‐based
        $count = $this->dao->getUnlockedAccessoryCount();
        foreach ([4=>10, 5=>20] as $achID => $needed) {
            if ($count >= $needed
             && ! $this->dao->hasUnlockedAchievement($achID))
            {
                $this->dao->unlockAchievement($achID);
                $unlocked[] = $this->dao->getAchievementDetail($achID);
                (new InventoryService($this->userID))->rewardPlayer();
            }
        }

        return $unlocked;
    }

    public function getAllWithStatus(): array
    {
        return $this->dao->getAllAchievementsWithStatus();
    }
}