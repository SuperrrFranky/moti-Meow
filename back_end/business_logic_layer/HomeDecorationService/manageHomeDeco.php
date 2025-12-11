<?php
require_once '../../../_base.php';
header('Content-Type: application/json');
if (is_post()) {
    require_once(__DIR__ . '/../InventoryService/inventoryService.php');
    require_once(__DIR__ . '/../InventoryService/inventory.php');
    
    // Get the itemID from the POST request
    $itemID = isset($_POST['itemID']) ? $_POST['itemID'] : null;

    $inventory = new InventoryService(1);
    $usedItem = $inventory->useItem($itemID);
    // Check if the item was successfully used
    $_SESSION['equipped_images'][$usedItem->getCategory()] = $usedItem->getImage();
    if ($usedItem) {
        echo json_encode([
            'status' => 'success',
            'category' => $usedItem->getCategory(),
            'image' => $usedItem->getImage(),
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Item cannot be found or used',
        ]);
    }
} else {
    // If the request is not a POST, return an error
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>