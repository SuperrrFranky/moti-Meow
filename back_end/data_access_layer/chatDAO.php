<?php
require_once '../../../_base.php'; // access to $_db

class ChatDAO {
    private $db;

    public function __construct() {
        global $_db;
        $this->db = $_db;
    }

    // Get active conversation for user
    public function getActiveConversation($userId) {
        $stmt = $this->db->prepare("SELECT conversationID, summary FROM Conversation WHERE userID = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Create new conversation
    public function createConversation($userId) {
        $stmt = $this->db->prepare("INSERT INTO Conversation (userID, status, date) VALUES (?, 'active', CURDATE())");
        $stmt->execute([$userId]);
        return $this->db->lastInsertId();
    }

    // Update summary (after each message)
    public function updateConversationSummary($conversationId, $summary) {
        $stmt = $this->db->prepare("UPDATE Conversation SET summary = ? WHERE conversationID = ?");
        $stmt->execute([$summary, $conversationId]);
    }

    // End conversation
    public function endConversation($conversationId, $summary) {
        $stmt = $this->db->prepare("UPDATE Conversation SET status = 'end', summary = ?, date = CURDATE() WHERE conversationID = ?");
        $stmt->execute([$summary, $conversationId]);
    }
}
?>
