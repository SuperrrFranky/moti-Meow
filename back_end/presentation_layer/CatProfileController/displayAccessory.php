<?php
require_once(__DIR__ . '/../../business_logic_layer/InventoryService/inventoryService.php');
require_once(__DIR__ . '/../../business_logic_layer/InventoryService/inventory.php');
require_once(__DIR__ . '/../../data_access_layer/catProfileDAO.php');

$specificCategory = isset($_POST['specificCategory']) ? $_POST['specificCategory'] : 'all';
$sortDirection = isset($_POST['sortDirection']) ? $_POST['sortDirection'] : 'asc';
$categories = ['all', 'head', 'body', 'neck'];

$userID = $_SESSION['user_id'] ?? 1;

$catProfileDAO = new catProfileDAO($userID);
$equipItems = $catProfileDAO->getCurrentEquipAccessory();

$service = new InventoryService($userID);
$items = $service->getInventory('accessory',$specificCategory,$sortDirection);
$html ='';
$result = [];
$html .= '<div class="inventory-header">Inventory</div>';

$html .= '<div class="inventory-wrapper">';
$html.= '<div class="filter-inventory">';
foreach ($categories as $cat) {
    $active = ($cat == $specificCategory) ? 'active' : '';
    $sortSymbol = ($cat == $specificCategory) ? ($sortDirection == 'asc' ? '⬆' : '⬇') : '⬆'; 
    $currentSort = ($cat == $specificCategory) ? $sortDirection : 'asc';
    if($active=='active')
    {
    $html .= '<button class="filter-btn ' . $active . '" data-category="' . $cat . '" data-sort="' . $currentSort . '">' 
        . ucfirst($cat) . ' ' . $sortSymbol . '</button>';
    }else
    {
        $html .= '<button class="filter-btn ' . $active . '" data-category="' . $cat . '" data-sort="' . $currentSort . '">' 
        . ucfirst($cat) . '</button>';
    }
}
$html.= '</div>';
$html.= '<div class="inventory-container">';

foreach ($items as $item) {
    switch ($item->getCategory()) {
        case 'body':
            $folder = 'body accessories';
            $isEquipped = ($equipItems['body'] == $item->getID());
            break;
        case 'head':
            $folder = 'head accessories';
            $isEquipped = ($equipItems['head'] == $item->getID());
            break;
        case 'neck':
            $folder = 'neck accessories';
            $isEquipped = ($equipItems['neck'] == $item->getID());
            break;
    }
    $activeClass = $isEquipped ? 'equipped' : '';
    $html .= '<div class="item  '.$activeClass.' " data-item-id="' . $item->getID() . '" data-name="' . htmlspecialchars($item->getName()) . '" data-type="accessory">';
    $html .= '<img src="/res/image/accessories/' . $folder . '/' . $item->getImage() . '" alt="' . htmlspecialchars($item->getName()) . '">';
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