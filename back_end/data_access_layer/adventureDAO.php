<?php
require_once(__DIR__ . '/../../_base.php');

class AdventureDAO
{
    // HANDLE STAMINA //
    public static function getUserEnergy($userId)
    {
        global $_db;
        $stmt = $_db->prepare('SELECT energy FROM user WHERE userID = ?');
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public static function getRecoveryItemQuantity($userId)
    {
        global $_db;
        $stmt = $_db->prepare('SELECT quantity FROM userinventorylist WHERE userID = ? AND inventoryID = 47');
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public static function consumeRecoveryItem($userId, $inventoryID)
    {
        global $_db;
        $stmt = $_db->prepare('
            UPDATE userinventorylist
            SET quantity = quantity - 1
            WHERE userID = ? AND inventoryID = ? AND quantity > 0
        ');
        return $stmt->execute([$userId, $inventoryID]);
    }

    public static function increaseUserEnergy($userId, $amount)
    {
        global $_db;
        $stmt = $_db->prepare('
            UPDATE user
            SET energy = energy + ?
            WHERE userID = ?
        ');
        return $stmt->execute([$amount, $userId]);
    }

    // AUTO UPDATE STAMINA
    public static function getUserEnergyAndLastUpdate($userId)
    {
        global $_db;
        $stmt = $_db->prepare('SELECT energy, lastEnergyUpdate FROM user WHERE userID = ?');
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updateUserEnergyAndTimestamp($userId, $newEnergy, $now)
    {
        global $_db;
        $stmt = $_db->prepare('
            UPDATE user
            SET energy = ?, lastEnergyUpdate = ?
            WHERE userID = ?
        ');
        return $stmt->execute([$newEnergy, $now, $userId]);
    }

    public static function useStamina($userId, $staminaAfterUsed)
    {
        global $_db;

        $stmt = $_db->prepare(' UPDATE user 
                                SET energy = ?, 
                                lastEnergyUpdate = NOW()
                                WHERE userID = ? ');

        return $stmt->execute([$staminaAfterUsed, $userId]);
    }

    public static function updateUserStamina($userId, $recoveryItem)
    {
        global $_db;

        $stmt = $_db->prepare(' UPDATE user 
                                SET energy = energy + ?, 
                                lastEnergyUpdate = NOW()
                                WHERE userID = ? ');

        return $stmt->execute([$recoveryItem, $userId]);
    }

    // HANDLE ADVENTURE //
    public static function getOngoingAdventure($userId)
    {
        global $_db;
        $stmt = $_db->prepare(' SELECT a.adventureID, a.startingTime, a.endingTime, b.background_media
                                FROM adventure a
                                JOIN adventure_background b ON a.background_id = b.background_id
                                WHERE a.userID = ? AND a.reward_claimed = 0
                                ORDER BY a.startingTime DESC
                                LIMIT 1 ');
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // get random background
    public static function getRandomBackground()
    {
        global $_db;
        $stmt = $_db->query('   SELECT background_id, background_media, background_key
                                FROM adventure_background
                                ORDER BY RAND()
                                LIMIT 1 ');
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // create new adventure
    public static function createAdventure($userId, $backgroundId, $startingTime, $endingTime, $title, $story)
    {
        global $_db;
        $stmt = $_db->prepare(' INSERT INTO adventure (userID, background_id, startingTime, endingTime, adventure_title, story)
                                VALUES (?, ?, ?, ?, ?, ?) ');
        return $stmt->execute([$userId, $backgroundId, $startingTime, $endingTime, $title, $story]);
    }

    public static function getCurrentAdventure($userId)
    {
        global $_db;
        $stmt = $_db->prepare(' SELECT adventure_title 
                                FROM adventure
                                WHERE userID = ? 
                                AND endingTime < NOW()
                                ORDER BY startingTime DESC 
                                LIMIT 1 ');
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function updateReward($userId, $reward)
    {
        global $_db;
        $stmt = $_db->prepare(' UPDATE adventure 
                                SET reward = ?
                                WHERE userID = ? ');
        return $stmt->execute([$reward, $userId]);
    }

    public static function getAllAdventure($userId)
    {
        global $_db;
        $stmt = $_db->prepare(' SELECT a.*, b.background_key, b.background_media
                                FROM adventure a
                                JOIN adventure_background b ON a.background_id = b.background_id
                                WHERE a.userID = ? AND reward_claimed = 1
                                ORDER BY a.startingTime DESC ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAdventureByID($adventureId)
    {
        global $_db;
        $stmt = $_db->prepare(' SELECT a.*, b.background_key, b.background_media
                                FROM adventure a
                                JOIN adventure_background b ON a.background_id = b.background_id
                                WHERE a.adventureID = ? ');
        $stmt->execute([$adventureId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getLatestCompletedAdventure($userId)
    {
        global $_db;
        $stmt = $_db->prepare(' SELECT * FROM adventure
                                WHERE userID = ? AND endingTime < NOW()
                                ORDER BY endingTime DESC
                                LIMIT 1 ');
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updateAdventureReward($userId, $adventureId, $rewardId)
    {
        global $_db;
        $stmt = $_db->prepare(' UPDATE adventure
                                SET reward = ?
                                WHERE adventureID = ? AND userID = ? ');
        return $stmt->execute([$rewardId, $adventureId, $userId]);
    }

    public static function markRewardClaimed($adventureId)
    {
        global $_db;
        $stmt = $_db->prepare('UPDATE adventure SET reward_claimed = 1 WHERE adventureID = ?');
        return $stmt->execute([$adventureId]);
    }
}
