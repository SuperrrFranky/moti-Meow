<?php
require_once '../../../_base.php';
require_once '../../data_access_layer/inventoryDAO.php';
require_once '../../data_access_layer/catProfileDAO.php';
require_once '../../data_access_layer/HomeDecorationDAO.php';


class LoginService
{
    private $catProfileDAO;
    private $inventoryDAO;
    private $homeDecoDAO;

    public function __construct()
    {
        $this->catProfileDAO = new catProfileDAO($_SESSION['user_id']);
        $this->inventoryDAO = new InventoryDAO($_SESSION['user_id']);
        $this->homeDecoDAO = new HomeDecorationDAO($_SESSION['user_id']);
    }

    public function login() {
        $equipItems = $this->catProfileDAO->getCurrentEquipAccessory();
        $equipDecoration = $this->homeDecoDAO->getCurrentHomeDeco();

        $equippedImagePaths = [];
    
        foreach ($equipItems as $slot => $itemID) {
            if ($itemID) {
                $itemData = $this->inventoryDAO->getEquippedItemPath($itemID);
                $equippedImagePaths[$slot] = $itemData ? $itemData['imgPath'] : null;
            } else {
                $equippedImagePaths[$slot] = null; // No item equipped
            }
        }

        foreach ($equipDecoration as $slot => $itemID) {
            if ($itemID) {
                $itemData = $this->inventoryDAO->getEquippedItemPath($itemID);
                $equippedImagePaths[$slot] = $itemData ? $itemData['imgPath'] : null;
            } else {
                $equippedImagePaths[$slot] = null; // No item equipped
            }
        }
    
        // Store in session
        $_SESSION['equipped_images'] = $equippedImagePaths;
    }
    
}
