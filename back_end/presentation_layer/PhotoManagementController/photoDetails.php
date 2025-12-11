<?php
require_once(__DIR__ . '/../../business_logic_layer/PhotoManagementService/PhotoManagementService.php');

$userId = $_SESSION['user_id'] ?? 1;
$galleryID = $_POST['galleryID'] ?? null;

if ($galleryID === null) {
    echo 'Invalid request.';
    exit;
}

$photoService = new PhotoManagementService($userId);
$photo = $photoService->getPhotoById($galleryID); 

if (!$photo) {
    echo 'Photo not found.';
    exit;
}

$html = '';

$html .= '<div class="photo-details-container">';
$html .= '<div class="photo-back-btn"><</div>';
$html .= '<div class="photo-details-header">Photo Details</div>';

$html .= '<div class="photo-details-body">';
$html .= '<img src="/res/image/photoGallery/' . htmlspecialchars($photo['imagePath']) . '" alt="Photo">';
$html .= '<div class="photo-text-box">';
$description = trim($photo['description'] ?? '');
$displayDescription = $description === '' ? 'No description' : nl2br(htmlspecialchars($description));
$html .= '<div class="photo-description-scroll">' . $displayDescription . '</div>';
$html .= '<div class="photo-date">' . htmlspecialchars($photo['date'] ?? '') . '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';


echo $html;
?>
