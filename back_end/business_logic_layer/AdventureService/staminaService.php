<?php
require_once '../../data_access_layer/adventureDAO.php';


class StaminaService
{
    public static function checkStamina($userId)
    {
        $energy = AdventureDAO::getUserEnergy($userId);

        if ($energy < 5) {
            return ['status' => 'low', 'message' => 'Stamina Insufficient'];
        }

        return ['status' => 'ok', 'message' => 'Enough stamina'];
    }

    public static function getRecoveryItemQty($userId)
    {
        $itemQty = AdventureDAO::getRecoveryItemQuantity($userId);

        if (!$itemQty || $itemQty < 1) {
            return 0;
        }

        return $itemQty;
    }

    public static function startAdventure($userId)
    {
        // Decrease stamina to 0 and update last_update_time to NOW()
        $success = AdventureDAO::useStamina($userId, 0); // assuming 5 stamina used

        if ($success) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'fail', 'message' => 'Failed to use stamina'];
        }
    }

    // UPDATE STAMINA AUTOMATIC
    public static function getCurrentStamina($userId)
    {
        $userData = AdventureDAO::getUserEnergyAndLastUpdate($userId);
        return (int)$userData['energy'];
    }

    public static function getUserEnergyWithAutoRecovery($userId)
    {
        date_default_timezone_set('Asia/Kuala_Lumpur'); // Set timezone

        $userData = AdventureDAO::getUserEnergyAndLastUpdate($userId);
        $energy = (int)$userData['energy'];
        $lastUpdate = $userData['lastEnergyUpdate'];

        if ($energy >= 5) {
            // already max energy
            return ['energy' => 5, 'lastUpdate' => null];
        }

        $now = new DateTime();
        if (!$lastUpdate) {
            // if no last update record then set it now
            AdventureDAO::updateUserEnergyAndTimestamp($userId, $energy, $now->format('Y-m-d H:i:s'));
            return ['energy' => $energy, 'lastUpdate' => $now->format('Y-m-d H:i:s')];
        }

        $lastUpdateTime = new DateTime($lastUpdate);
        $interval = $lastUpdateTime->diff($now);
        $minutesPassed = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

        $recoveredPoints = floor($minutesPassed / 120); // Every 120 minutes recover 1

        if ($recoveredPoints > 0) {
            $newEnergy = min(5, $energy + $recoveredPoints);
            AdventureDAO::updateUserEnergyAndTimestamp($userId, $newEnergy, $now->format('Y-m-d H:i:s'));
            return ['energy' => $newEnergy];
        }

        return ['energy' => $energy, 'lastUpdate' => $lastUpdate];
    }

    public static function updateStamina($userId, $recoveryQty)
    {
        $success = AdventureDAO::updateUserStamina($userId, $recoveryQty);

        if ($success) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'fail', 'message' => 'Failed update stamina'];
        }
    }
}
