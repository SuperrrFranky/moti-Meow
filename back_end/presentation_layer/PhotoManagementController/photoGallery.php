<?php
require_once(__DIR__ . '/../../business_logic_layer/PhotoManagementService/PhotoManagementService.php');

$html = '';

$html .= '<div class="gallery-container">';
$html .= '<div class="gallery-header">';
$html .= '<h2>Photo</h2>';
$html .= '</div>'; // close gallery-header

$html .= '<div class="gallery">';
$html .= '<div class="gallery-scroll">';

$userId = $_SESSION['user_id'] ?? 1; 
$photoService = new PhotoManagementService($userId);
$photos = $photoService->getPhotos();

foreach ($photos as $photo) {
    $html .= '<div class="photo-box">';
    $html .= '<img src="/res/image/photoGallery/' . htmlspecialchars($photo['imagePath']) . '" alt="Photo">';
    $html .= '<div class="photo-actions">';
    $html .= '<button class="details-btn" data-id="' . htmlspecialchars($photo['galleryID']) . '">Details</button>';
    $html .= '<button class="delete-btn" data-id="' . htmlspecialchars($photo['galleryID']) . '">Delete</button>';
    $html .= '</div>';
    $html .= '</div>';
}

$html .= '<div class="add-btn" id="add-photo-btn">+</div>';
$html .= '</div>'; // close gallery-scroll
$html .= '</div>'; // close gallery

$html .= '</div>'; // close gallery-container

echo $html;
?>