<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../../../_base.php';
require_once '../../business_logic_layer/chatboxService/chatbotService.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$chatbotService = new ChatbotService();

$action = $_POST['action'] ?? '';

if ($action === 'startChat') {
    $chatbotService->startChatForUser();
    $chatLog = $chatbotService->getChatLog();
    echo json_encode(['status' => 'success', 'html' => generateChatHTML($chatLog)]);
} elseif ($action === 'getResponse') {
    $message = $_POST['message'] ?? '';
    $response = $chatbotService->getChatResponse($message);
    echo json_encode(['status' => 'success', 'response' => $response]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}

// ------------------ UI GENERATOR ------------------

function generateChatHTML($chatLog) {
    $html = '<div class="chat-container"><div class="chat-messages">';

    if (!empty($chatLog)) {
        foreach ($chatLog as $msg) {
            $role = $msg['role'];
            $content = htmlspecialchars($msg['content']);

            if ($role === 'user') {
                $html .= '<div class="chat-bubble user-message"><div class="bubble-text">' . $content . '</div></div>';
            } elseif ($role === 'assistant') {
                $html .= '<div class="chat-bubble bot-message">
                            <img src="/res/image/interaction/chatbot_avatar.png" alt="Cat" class="chat-avatar">
                            <div class="bubble-text">' . $content . '</div>
                          </div>';
            }
        }
    } else {
        $html .= '<div class="chat-bubble bot-message">
                    <img src="/res/image/interaction/chatbot_avatar.png" alt="Cat" class="chat-avatar">
                    <div class="bubble-text">Hi there! I\'m your cat companion 🐾<br>Let\'s chat and feel better together!</div>
                  </div>';
    }

    $html .= '</div> <!-- chat-messages -->
              <div class="chat-input-bar">
                <input type="text" id="chat-input" placeholder="Type your message..." />
                <button id="chat-send-btn">
                  <img src="/res/image/interaction/send_icon.png" alt="Send" />
                </button>
              </div></div>';

    return $html;
}
?>
