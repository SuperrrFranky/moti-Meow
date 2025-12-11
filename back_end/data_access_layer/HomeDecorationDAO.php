<?php
require_once (__DIR__ .'/../business_logic_layer/InventoryService/inventory.php'); 
require_once (__DIR__ .'../../../_base.php');

class HomeDecorationDAO {
    private $userID;
    private $db;
    public function __construct($userID) {
        global $_db;
        $this->userID = $userID;
        $this->db= $_db;
    }

    public function getCurrentHomeDeco()
        {
            // get all unlock id
            $unlockSql = "SELECT bed,painting,picture1,picture2,plant,window1,window2,window3,room,foodBowl,water,playground1,playground2,playground3
            	 FROM room WHERE userID = ?";
            $stmtUserItem = $this->db->prepare($unlockSql);
            $stmtUserItem->execute([$this->userID]);
            $equipItems = $stmtUserItem->fetch(PDO::FETCH_ASSOC);

            return $equipItems;
        }
    
}
