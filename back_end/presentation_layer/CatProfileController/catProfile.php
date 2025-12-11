<?php
require_once '../../business_logic_layer/CatProfileService/nameService.php';

$userId = $_SESSION['user_id'] ?? 1; 
$catName = NameService::getCatName($userId);  

$html = '';

$html .= '<div class="card-profile">';

$html .= '<div class="profile-headerr">'; 
$html .= '<div class="profile-header">'; 
$html .= '<div class="avatar">';
$html .= '<img src="../../../res/image/profile_pic.png" alt="Cat Avatar" />';
$html .= '</div>'; // close avatar

$html .= '<div class="name-section" id="nameSection">';
$html .= '<span id="catNameDisplay">' . htmlspecialchars($catName) . '</span>';
$html .= '<input type="text" id="catNameInput" class="name-edit-input value="' . htmlspecialchars($catName) . '" style="display:none;">';
$html .= '<button id="confirmNameBtn" class="confirm-button" style="display:none;">Confirm</button>';
$html .= '<img src="../../../res/image/edit.png" alt="Edit Icon" id="editCatName" style="cursor:pointer;">';
$html .= '</div>';// close name-section
$html .= '</div>'; // close profile-header
$html .= '</div>'; // close profile-headerr
            
$html .= '<div class="buttons">';

$html .= '<div class="circle-button" id="show-accessory">';
$html .= '<img src="../../../res/image/bowpink.png" alt="Change Outfit">';
$html .= '<div id="accessory-container">';
$html .= '<div id="accessory-inventory"></div>';
$html .='</div>';
$html .= 'Change Outfit';
$html .= '</div>'; // close circle-button

$html .= '<div class="circle-button">';
$html .= '<img id="achivement" src="../../../res/image/achivement.png" onclick="window.location.href=\'/page/AchievementPage.php\';" alt="Achievement">';
$html .= 'Achievement';
$html .= '</div>'; // close circle-button
$html .= '</div>'; // close button

$html .= '</div>'; // close card-profile

echo $html;
