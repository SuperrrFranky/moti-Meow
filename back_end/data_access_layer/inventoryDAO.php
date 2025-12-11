<?php
require_once(__DIR__ . '/../business_logic_layer/InventoryService/inventory.php');
require_once(__DIR__ . '../../../_base.php');
class InventoryDAO
{
    private $userID;
    private $db;
    public function __construct($userID)
    {
        global $_db;
        $this->userID = $userID;
        $this->db = $_db;
    }

    public function getRemainingItem()
    {
        // get all unlock id
        $unlockSql = "SELECT inventoryID FROM userinventorylist WHERE userID = ?";
        $stmtUserItem = $this->db->prepare($unlockSql);
        $stmtUserItem->execute([$this->userID]);
        $userItems = $stmtUserItem->fetchAll(PDO::FETCH_COLUMN);

        $allItemSql = "SELECT inventoryID FROM inventory";
        $stmtAllItem = $this->db->query($allItemSql);
        $allItem = $stmtAllItem->fetchAll(PDO::FETCH_COLUMN);

        $itemsNotUnlocked = array_diff($allItem, $userItems);
        return $itemsNotUnlocked;
    }

    public function getItem()
    {
        //get everything from user
        $unlockSql = "
            SELECT i.inventoryID, i.category, i.name, i.imgPath,uil.unlockAt, uil.quantity
            FROM userinventorylist uil
            JOIN inventory i ON uil.inventoryID = i.inventoryID
            WHERE uil.userID = ?
            ";
        $stmtUserItem = $this->db->prepare($unlockSql);
        $stmtUserItem->execute([$this->userID]);
        $userItems = $stmtUserItem->fetchAll(PDO::FETCH_OBJ);
        $inventory = [];
        foreach ($userItems as $row) {
            $inventory[] = new Inventory($row->inventoryID, $row->category, $row->name, $row->imgPath, $row->unlockAt, $row->quantity);
        }
        return  $inventory;
    }

    public function unlockItem($itemID,&$inventory)
    {
        // Unlock item
        $currentDate = date('Y-m-d');
        $sql = "INSERT into userinventorylist(userID,inventoryID,isUnlocked,unlockAt,quantity) values(?,?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->userID, $itemID, 1, $currentDate, 1]);
        $unlockSql = "
        SELECT i.inventoryID, i.category, i.name, i.imgPath,uil.unlockAt, uil.quantity
        FROM userinventorylist uil
        JOIN inventory i ON uil.inventoryID = i.inventoryID
        WHERE uil.userID = ? AND uil.inventoryID = ?
        ";
        $stmtNewItem = $this->db->prepare($unlockSql);
        $stmtNewItem->execute([$this->userID, $itemID]);
        $newItem = $stmtNewItem->fetch(PDO::FETCH_ASSOC);
        $reward = new Inventory($newItem['inventoryID'], $newItem['category'], $newItem['name'], $newItem['imgPath'], $newItem['unlockAt'], $newItem['quantity']);
        $inventory[] = $reward;
        return $reward;
    }

    // this function is for rewarding consumable
    public function increaseConsumable($itemID, $amount,&$inventory)
    {

        // check if the consumable exist
        $sqlCheck = "SELECT COUNT(*) FROM userinventorylist WHERE userID = ? AND inventoryID = ?";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([$this->userID, $itemID]);
        $exists = $stmtCheck->fetchColumn();

        if ($exists > 0) {
            // If it exists, update the quantity (increment by 1)
            $sqlUpdate = "UPDATE userinventorylist SET quantity = quantity+ $amount WHERE userID = ? AND inventoryID = ?";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $stmtUpdate->execute([$this->userID, $itemID]);
            foreach ($inventory as $row) {
                if ($row->getID() == $itemID) {
                    $row->setQuantity($row->getQuantity()+$amount);
                    break;
                }
            }
            }
         else {
            // If it does not exist, insert a new row with quantity 
            $currentDate = date('Y-m-d');
            $consumable = "SELECT * FROM inventory WHERE inventoryID = ?";
            $stmtConsumable = $this->db->prepare($consumable);
            $stmtConsumable->execute([$itemID]);
            $newConsumable = $stmtConsumable->fetch();
            $sqlInsert = "INSERT INTO userinventorylist (userID, inventoryID,isUnlocked,unlockAt, quantity) VALUES (?, ?, ?,?,?)";
            $stmtInsert = $this->db->prepare($sqlInsert);
            $stmtInsert->execute([$this->userID, $itemID, TRUE, $currentDate, $amount]);
            $inventory[] = new Inventory(
                $newConsumable['inventoryID'],
                $newConsumable['category'],
                $newConsumable['name'],
                $newConsumable['imgPath'],
                $currentDate,
                $amount
            );
        }
            return true;
    }

    public function saveAccessory($itemID, $category)
    { //save the accessory
        $sql = "UPDATE pet SET $category = ? WHERE userID = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$itemID, $this->userID]);
    }

    public function saveFurniture($itemID, $category)
    { // save the furniture
        $sql = "UPDATE room SET $category = ? WHERE userID = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$itemID, $this->userID]);
    }

    public function decreaseQuantity($itemID, $quantity, $consumableAmount)
    { //decrease consumable quantity
        $sql = "UPDATE userinventorylist SET quantity= ? WHERE userID = ? AND inventoryID=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([($quantity-$consumableAmount), $this->userID, $itemID]);
    }

    public function getEquippedItemPath($itemID)
    { //get equipped item
        $sql = "SELECT inv.imgPath FROM userinventorylist uil
                JOIN inventory inv ON uil.inventoryID = inv.inventoryID
                WHERE uil.userID = ? AND uil.inventoryID = ? ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->userID, $itemID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getItemByID($itemId) {
        $sql = "SELECT * 
                FROM userinventorylist uil
                JOIN inventory inv ON uil.inventoryID = inv.inventoryID
                WHERE uil.userID = ? AND uil.inventoryID = ? ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->userID, $itemId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

}