<?php
require_once(__DIR__ . '/../../business_logic_layer/PhotoManagementService/PhotoManagementService.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['galleryID'])) {
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo 'unauthorized';
        exit;
    }

    $galleryID = intval($_POST['galleryID']);
    $photoService = new PhotoManagementService($userId);
    $deleted = $photoService->deletePhoto($galleryID);

    echo $deleted ? 'success' : 'fail';
    exit;
}

echo 'invalid';
