<?php
require_once (__DIR__ .'/../business_logic_layer/InventoryService/inventory.php'); 
require_once (__DIR__ .'../../../_base.php');

class catProfileDAO {
    private $userID;
    private $db;
    public function __construct($userID) {
        global $_db;
        $this->userID = $userID;
        $this->db= $_db;
    }

    public function getCurrentEquipAccessory()
        {
            // get all unlock id
            $unlockSql = "SELECT head,body,neck FROM pet WHERE userID = ?";
            $stmtUserItem = $this->db->prepare($unlockSql);
            $stmtUserItem->execute([$this->userID]);
            $equipItems = $stmtUserItem->fetch(PDO::FETCH_ASSOC);

            return $equipItems;
        }

        public static function getName($userId)
        {
            global $_db;
            $catNameSql = "SELECT name FROM pet WHERE userID = ?";
            $stmtCatName = $_db->prepare($catNameSql);
            $stmtCatName->execute([$userId]);

            $result = $stmtCatName->fetch(PDO::FETCH_ASSOC);
             return $result ? $result['name'] : 'Unnamed Cat';
        }

        public function updateName($newName) {
            $sql = "UPDATE pet SET name = ? WHERE userID = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$newName, $this->userID]);
        }
    
    
}
