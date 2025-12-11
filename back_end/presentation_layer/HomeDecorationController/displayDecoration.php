<?php
require_once(__DIR__ . '/../../business_logic_layer/InventoryService/inventoryService.php');
require_once(__DIR__ . '/../../business_logic_layer/InventoryService/inventory.php');
require_once(__DIR__ . '/../../data_access_layer/HomeDecorationDAO.php');

$specificCategory = isset($_POST['specificCategory']) ? $_POST['specificCategory'] : 'all';
$sortDirection = isset($_POST['sortDirection']) ? $_POST['sortDirection'] : 'asc';
$categories = ['all','bed',	'painting',	'picture1',	'picture2',	'plant',	'window1',	'window2',	'window3',	'room',	'foodBowl',	'water',	'playground1',	'playground2', 'playground3'	
];

$userID = $_SESSION['user_id'] ?? 1;

$HomeDecorationDAO = new HomeDecorationDAO($userID);
$equipItems = $HomeDecorationDAO->getCurrentHomeDeco();
$service = new InventoryService($userID);
$items = $service->getInventory('decoration',$specificCategory,$sortDirection);
$html ='';
$result = [];

$html .= '<div class="inventory-wrapper2">';

$html .= '<div class="inventory-header-filter">';
$html.= '<div class="filter-inventory2">';
foreach ($categories as $cat) {
    $active = ($cat == $specificCategory) ? 'active' : '';
    $sortSymbol = ($cat == $specificCategory) ? ($sortDirection == 'asc' ? '⬆' : '⬇') : '⬆'; 
    $currentSort = ($cat == $specificCategory) ? $sortDirection : 'asc';
    if($active=='active')
    {
    $html .= '<button class="filter-btn2 ' . $active . '" data-category="' . $cat . '" data-sort="' . $currentSort . '">' 
        . ucfirst($cat) . ' ' . $sortSymbol . '</button>';
    }else
    {
        $html .= '<button class="filter-btn2 ' . $active . '" data-category="' . $cat . '" data-sort="' . $currentSort . '">' 
        . ucfirst($cat) . '</button>';
    }
}
$html.= '</div>';
$html.= '</div>';

$html.= '<div class="inventory-container2">';

foreach ($items as $item) {
    $category = $item->getCategory();

    $folder = $category;

    // Check equipped status
    $isEquipped = isset($equipItems[$category]) && $equipItems[$category] == $item->getID();

    $activeClass = $isEquipped ? 'equipped' : '';
    $html .= '<div class="item item2  '.$activeClass.' " data-item-id="' . $item->getID() . '" data-name="' . htmlspecialchars($item->getName()) . '" data-type="decoration" data-category="' . $category . '">';
    $html .= '<img src="res/image/roomDeco/' . $folder . '/' . $item->getImage() . '" alt="' . htmlspecialchars($item->getName()) . '">';
    $html .= '</div>';
}

$html.= '</div>';
$html .= '</div>';  

$html.= '<div id="confirmation-section">';
$html.= '<p id="item-selection-message">';
$html.= '</p>';
$html.= '<div class="conf-button-group">';
$html.= '<button id="confirm-selection">Confirm</button>';
$html.= '<button id="cancel-selection">Cancel</button>';
$html.= '</div>';
$html.= '</div>';

echo $html;