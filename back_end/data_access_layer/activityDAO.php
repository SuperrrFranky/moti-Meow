<?php
require_once(__DIR__ . '/../../_base.php');

class ActivityDAO
{
    private $userID;
    private $db;

    public function __construct($userID)
    {
        global $_db;
        $this->userID = $userID;
        $this->db = $_db;
    }

    public function getExercises($category)
    {
        $sql = "SELECT * FROM activity WHERE category = ? ORDER BY activityID ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExerciseDetail($activityID)
    {
        $sql = "SELECT a.*, ual.uActivity AS uActivityID, ual.remainingTime
                FROM activity a
                LEFT JOIN useractivitylist ual ON a.activityID = ual.activityID AND ual.userID = ?
                WHERE a.activityID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->userID, $activityID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserActivityProgress($activityID)
    {
        $sql = "SELECT * FROM useractivitylist WHERE userID = ? AND activityID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->userID, $activityID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertNewUserActivity($activityID)
    {
        // Step 1: Get activity time
        $sql = "SELECT time FROM activity WHERE activityID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$activityID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$row) {
            throw new Exception("Activity ID $activityID not found.");
        }
    
        $time = $row['time']; // Get the actual time value
    
        // Step 2: Insert with proper remainingTime
        $sql = "INSERT INTO useractivitylist (userID, activityID, isComplete, date, remainingTime, isSkip)
                VALUES (?, ?, 0, CURDATE(), ?, 0)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->userID, $activityID, $time]);
    }
    

    public function saveRemainingTime($uActivityID, $remainingTime)
    {
        $sql = "UPDATE useractivitylist SET remainingTime = ? WHERE uActivity = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$remainingTime, $uActivityID]);
    }

    public function completeExercise($uActivityID)
    {
        $sql = "UPDATE useractivitylist SET isComplete = 1, remainingTime = 0 WHERE uActivity = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$uActivityID]);
    }

    public function skipExercise($uActivityID)
    {
        $sql = "UPDATE useractivitylist SET isSkip = 1, isComplete = 1, remainingTime = 0 WHERE uActivity = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$uActivityID]);
    }

    public function getProgressByCategory($category)
    {
        $sql = "SELECT ual.uActivity AS uActivityID, ual.*, a.name, a.time, a.description, a.videoPath, a.imagePath
                FROM useractivitylist ual
                JOIN activity a ON ual.activityID = a.activityID
                WHERE ual.userID = ? AND a.category = ?
                ORDER BY a.activityID ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->userID, $category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countSkippedExercises($category)
    {
        $sql = "SELECT COUNT(*)
                FROM useractivitylist ual
                JOIN activity a ON ual.activityID = a.activityID
                WHERE ual.userID = ? AND a.category = ? AND ual.isSkip = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->userID, $category]);
        return (int)$stmt->fetchColumn();
    }

    public function resetProgress($category)
    {
        $sql = "UPDATE useractivitylist ual
                JOIN activity a ON ual.activityID = a.activityID
                SET ual.isComplete = 0,
                    ual.remainingTime = a.time,
                    ual.isSkip = 0
                WHERE ual.userID = ? AND a.category = ?";
    
        $stmt = $this->db->prepare($sql);
        $params = [$this->userID, $category];
        $success = $stmt->execute($params);
    
        if ($success) {
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0) {
                return [
                    'status' => 'success',
                    'message' => "$rowCount record(s) reset successfully",
                    'userID' => $this->userID,
                    'category' => $category
                ];
            } else {
                return [
                    'status' => 'warning',
                    'message' => 'No progress found to reset',
                    'userID' => $this->userID,
                    'category' => $category
                ];
            }
        } else {
            $error = $stmt->errorInfo();
            return [
                'status' => 'fail',
                'message' => 'Failed to reset progress: ' . $error[2],
                'userID' => $this->userID,
                'category' => $category
            ];
        }
    }
    



    public function getAllUserProgressByCategory($category)
    {

        $sql = "SELECT 
                a.activityID, a.name, a.time, a.description, a.videoPath,
                ual.uActivity AS uActivityID,
                ual.remainingTime,
                ual.isComplete,
                ual.isSkip
            FROM activity a
            LEFT JOIN useractivitylist ual 
                ON a.activityID = ual.activityID AND ual.userID = ?
            WHERE a.category = ?
            ORDER BY a.activityID ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->userID, $category]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure fields have default values if null
        foreach ($results as &$exercise) {
            $exercise['remainingTime'] = $exercise['remainingTime'] ?? null;
            $exercise['isComplete'] = $exercise['isComplete'] ?? 0;
            $exercise['isSkip'] = $exercise['isSkip'] ?? 0;
            $exercise['uActivityID'] = $exercise['uActivityID'] ?? null;
        }

        return $results;
    }

    public function checkUserProgressByCategory($category)
    {
        $sql = "SELECT COUNT(*) FROM useractivitylist ual JOIN  activity a ON a.activityID = ual.activityID  AND userID = ? WHERE a.category = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->userID, $category]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function isAnotherExerciseActive($userID) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM useractivitylist WHERE userID = ? AND isActive = 1");
        $stmt->execute([$userID]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function markSessionActive($uActivityID) {
        $stmt = $this->db->prepare("UPDATE useractivitylist SET isActive = 1, lastActiveAt = NOW() WHERE uActivity = ?");
        return $stmt->execute([$uActivityID]);
    }
    
    
    public function markSessionInactive($uActivityID) {
        $stmt = $this->db->prepare("UPDATE useractivitylist SET isActive = 0 WHERE uActivity = ?");
        return $stmt->execute([$uActivityID]);
    }

    public function getActiveUActivityID($userID) {
        // Auto-clear any expired active session (> 30 mins old)
        $this->clearExpiredSessions($userID);
    
        $stmt = $this->db->prepare("SELECT uActivity FROM useractivitylist WHERE userID = ? AND isActive = 1 LIMIT 1");
        $stmt->execute([$userID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['uActivity'] : null;
    }
    
    private function clearExpiredSessions($userID) {
        $stmt = $this->db->prepare("
            UPDATE useractivitylist 
            SET isActive = 0 
            WHERE userID = ? AND isActive = 1 AND lastActiveAt < (NOW() - INTERVAL 30 MINUTE)
        ");
        $stmt->execute([$userID]);
    }
    public function updateLastActive($uActivityID) {
        $stmt = $this->db->prepare("UPDATE useractivitylist SET lastActiveAt = NOW() WHERE uActivity = ?");
        return $stmt->execute([$uActivityID]);
    }
    
    
}
