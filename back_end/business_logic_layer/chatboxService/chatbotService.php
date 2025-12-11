<?php
require_once '../../../_base.php';
require_once '../../data_access_layer/chatDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ChatbotService
{
    private $chatDAO;

    public function __construct()
    {
        $this->chatDAO = new ChatDAO();
    }

    public function startChatForUser($userId = 1)
    {
        $active = $this->chatDAO->getActiveConversation($userId);

        if ($active) {
            $_SESSION['conversation_id'] = $active->conversationID;
            $_SESSION['chat_log'] = [];

            $lines = explode("\n", $active->summary);
            foreach ($lines as $line) {
                if (preg_match('/^\[(USER|ASSISTANT)\]\s(.*)$/', $line, $matches)) {
                    $_SESSION['chat_log'][] = [
                        'role' => strtolower($matches[1]),
                        'content' => $matches[2]
                    ];
                }
            }
        } else {
            $conversationId = $this->chatDAO->createConversation($userId);
            $_SESSION['conversation_id'] = $conversationId;
            $_SESSION['chat_log'] = [];
        }
    }

    public function getChatLog()
    {
        return $_SESSION['chat_log'] ?? [];
    }

    public function getChatResponse($message)
    {
        if (!isset($_SESSION['chat_log'])) {
            $this->startChatForUser();
        }

        if (trim($message) === '/end') {
            $this->endChatSession();
            return "Chat ended. Thank you!";
        }

        $validation = $this->validateMessage($message);
        if ($validation !== true) {
            return $validation;
        }

        $_SESSION['chat_log'][] = ["role" => "user", "content" => $message];

        $messages = array_merge(
            [[
                "role" => "system",
                "content" =>
                "You are Moti, a cheerful and caring virtual cat companion. Speak in a warm, supportive, and encouraging tone. Use short and positive sentences. Your goal is to comfort and uplift the user. Occasionally add playful emojis like 🐾😺💬 to make your replies feel friendly and personal."
            ]],
            $_SESSION['chat_log']
        );

        $reply = callAzureOpenAI($messages, 80);


        $_SESSION['chat_log'][] = ["role" => "assistant", "content" => $reply];

        $this->updateChatSummary();

        return $reply;
    }

    private function formatSessionLog()
    {
        $log = '';
        if (!empty($_SESSION['chat_log'])) {
            foreach ($_SESSION['chat_log'] as $msg) {
                $role = strtoupper($msg['role']);
                $content = trim($msg['content']);
                $log .= "[$role] $content\n";
            }
        }
        return $log;
    }

    private function updateChatSummary()
    {
        $conversationId = $_SESSION['conversation_id'];
        $summary = $this->formatSessionLog();
        $this->chatDAO->updateConversationSummary($conversationId, $summary);
    }

    private function endChatSession()
    {
        $conversationId = $_SESSION['conversation_id'];
        $summary = $this->formatSessionLog();
        $this->chatDAO->endConversation($conversationId, $summary);

        unset($_SESSION['chat_log'], $_SESSION['conversation_id']);
    }

    private function validateMessage($message)
    {
        $trimmed = trim($message);

        if ($trimmed === '') {
            return 'Message cannot be empty.';
        }
        if (strlen($trimmed) > 500) {
            return 'Message too long. Please limit to 500 characters.';
        }
        if (preg_match('/<[^>]*script[^>]*>/', strtolower($trimmed))) {
            return 'Invalid content detected.';
        }
        return true;
    }
}
