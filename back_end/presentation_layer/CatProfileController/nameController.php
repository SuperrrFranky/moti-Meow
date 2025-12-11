<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../business_logic_layer/CatProfileService/nameService.php';

$userId = $_SESSION['user_id'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    $action = $_POST['action'];

    switch ($action) {
        case 'getName':
            $name = NameService::getCatName($userId);
            echo json_encode(['name' => $name]);
            break;

        case 'updateName':
            if (!empty($_POST['name'])) {
                $newName = trim($_POST['name']);
                $result = NameService::updateCatName($userId, $newName);

                if ($result) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'fail']);
                }
            } else {
                echo json_encode(['status' => 'invalid']);
            }
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}
