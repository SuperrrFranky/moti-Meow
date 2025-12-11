<?php
require_once(__DIR__ . '../../../_base.php');

class UserDAO
{
    private $userID;
    private $db;
    public function __construct($userID)
    {
        $this->userID = $userID;
        global $_db;
        $this->db = $_db;
    }

    // Get user's last login date
    public function getUserLastLogin($userID)
    {
        $stmt = $this->db->prepare("SELECT lastLogin FROM user WHERE userID = ?");
        $stmt->execute([$userID]);
        return $stmt->fetchColumn();
    }

    // Update user's last login date
    public function updateUserLastLogin($userID)
    {
        $stmt = $this->db->prepare("UPDATE user SET lastLogin = ?, loginStreak = loginStreak + 1 WHERE userID = ?");
        $stmt->execute([date('Y-m-d'), $this->userID]);
    }

    //reset loginstreak if missed
    public function resetLoginStreakIfMissed()
    {
        $stmt = $this->db->prepare("SELECT lastLogin FROM user WHERE userID = ?");
        $stmt->execute([$this->userID]);
        $lastLogin = $stmt->fetchColumn();

        if ($lastLogin) {
            $lastDate = new DateTime($lastLogin);
            $today = new DateTime();
            $diff = $lastDate->diff($today)->days;

            if ($diff > 1) {
                // Missed a day → reset streak
                $stmt = $this->db->prepare("UPDATE user SET loginStreak = 0 WHERE userID = ?");
                $stmt->execute([$this->userID]);
            }
        }
    }

    public function getAllAchievementsWithStatus(): array
    {
        $sql = "
          SELECT
            a.achievementID,
            a.title,
            a.description,
            a.iconPath,
            COALESCE(ua.isUnlocked, 0) AS isUnlocked
          FROM achievement a
          LEFT JOIN userachievement ua
            ON ua.achievementID = a.achievementID
           AND ua.userID = :uid
          ORDER BY a.achievementID
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['uid' => $this->userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Has this user already unlocked a given achievement?
    public function hasUnlockedAchievement(int $achID): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM userachievement
              WHERE userID=? AND achievementID=? AND isUnlocked=1"
        );
        $stmt->execute([$this->userID, $achID]);
        return (bool)$stmt->fetchColumn();
    }

    //Unlocks (inserts) the achievement for this user.
    public function unlockAchievement(int $achID): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO userachievement
               (userID, achievementID, isUnlocked, UnlockAt)
             VALUES (?, ?, 1, NOW())"
        );
        $stmt->execute([$this->userID, $achID]);
    }

    //Returns the details of a single achievement.
    public function getAchievementDetail(int $achID): array
    {
        $stmt = $this->db->prepare(
            "SELECT achievementID, title, description, iconPath
               FROM achievement
              WHERE achievementID = ?"
        );
        $stmt->execute([$achID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC); //->array(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new Exception("Achievement #{$achID} not found");
        }
        return $row;
    }

    public function getLoginStreak(): int
    {
        $stmt = $this->db->prepare("SELECT loginStreak FROM user WHERE userID = ?");
        $stmt->execute([$this->userID]);
        return (int)$stmt->fetchColumn();
    }

    //Count how many accessories the user has unlocked.
    public function getUnlockedAccessoryCount(): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM userinventorylist
               WHERE userID = ? AND isUnlocked = 1"
        );
        $stmt->execute([$this->userID]);
        return (int)$stmt->fetchColumn();
    }
}
