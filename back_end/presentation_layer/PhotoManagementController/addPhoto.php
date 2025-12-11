<?php
require_once(__DIR__ . '/../../business_logic_layer/PhotoManagementService/photoManagementService.php');
include '../../../_base.php';

$userID = $_SESSION['user_id'] ?? 1; 
$service = new PhotoManagementService($userID);
global $_err;

if (is_post()) {
    $description = req('description');
    $f = get_file('newPhoto');

    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['newPhoto'] = 'Must be image';
        } else if ($f->size > 1 * 1024 * 1024) {
            $_err['newPhoto'] = 'Maximum 1MB';
        }
    } else {
        $_err['newPhoto'] = 'Required';
    }

    if (!$_err) {
        $service->addPhoto($description, $f);

        include 'PhotoGallery.php'; 
        exit;
    }
}

// Show form
$html = '';


$html .= '<div class="addPhoto-container">';
$html .= '<div class="photo-back-btn"><</div>';
$html .= '<div class="addPhoto-title">Add Photo</div>';
$html .= '<form method="post" class="form" enctype="multipart/form-data" action="/back_end/presentation_layer/PhotoManagementController/AddPhoto.php">';
$html .= '<div class="addPhoto-bigContent">';
$html .= '<div class="addPhoto-content">';
$html .= '<div class="photo-error-container">';
$html .= '<label class="upload" tabindex="0">';
$html .= '<input type="file" id="newPhoto" name="newPhoto" accept="image/*" style="display:none" onchange="previewPhoto(this)">';
$html .= '<img src="/res/image/photoGallery/2813838.jpg" id="previewImage" style="cursor:pointer;">';
$html .= '</label>';
$html .= '<p id="errorMs"></p>';
$html .= '</div>'; 
$html .= '<div class="description-box">';
$html .= '<textarea name="description" placeholder="------------------------------------------------------------------------------------------------------------------------" maxlength="255" class="description-textarea">';
$html .= htmlspecialchars($description ?? '');
$html .= '</textarea>';
$html .= '</div>'; 
$html .= '</div>'; 
$html .= '<input type="submit" class="add-button" value="Add Photo" />';
$html .= '</div>'; 
$html .= '</form>';
$html .= '</div>'; 

echo $html;
