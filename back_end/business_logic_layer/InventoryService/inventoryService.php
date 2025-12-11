<?php
require_once (__DIR__ .'/../../data_access_layer/inventoryDAO.php'); 
require_once (__DIR__ .'/inventory.php'); 
class InventoryService {
    private $dao;
    private $inventory;
    public function __construct($userID) {
        $this->dao = new InventoryDAO($userID);
    }

    public function rewardPlayer(){
        if (empty($this->inventory)) {
            $this->inventory = $this->dao->getItem();
        }
        //get the not unlocked item
        $remainingItem=$this->dao->getRemainingItem();
        if(empty($remainingItem))
        {   // if obtained all item give stamina refill instead
            $this->dao->increaseConsumable(47,2,$this->inventory);
            return "you got them all";
        }else
        { //random generate award
            $randKey = array_rand($remainingItem);
            $randReward =$remainingItem[$randKey];
            $reward=$this->dao->unlockItem($randReward,$this->inventory);
            return $reward;
        }
    }
   
    public function rewardConsumable($amount){
        if (empty($this->inventory)) {
            $this->inventory = $this->dao->getItem();
        }
           $success= $this->dao->increaseConsumable(47,$amount,$this->inventory);
           return $success;
    }

    public function useItem($itemID,$consumableAmount=null){

    if (empty($this->inventory)) {
        $this->inventory = $this->dao->getItem();
    }
    foreach($this->inventory as $row)
    {
        if($row->getID()==$itemID)
        {
            if($row->getCategory() =='head'||$row->getCategory()=='body'||$row->getCategory()=='neck')
            { //update cat accessory 
                $this->dao->saveAccessory($row->getID(),$row->getCategory());
                return $row;
            }
            elseif($row->getCategory()=='consumable')
            { //find consumable.
                    if($row->getQuantity()>0&&$row->getQuantity()>=$consumableAmount){
                    $this->dao->decreaseQuantity($row->getID(),$row->getQuantity(),$consumableAmount);
                    $row->setQuantity($row->getQuantity()-$consumableAmount);
                    return $row;
                    }
            }
            else
            { //update furniture
                $this->dao->saveFurniture($row->getID(),$row->getCategory());
                return $row;
            }
        }
    }
    }

    public function getInventory($categoryType,$specificCategory,$sort)
{   
    if (empty($this->inventory)) {
        $this->inventory = $this->dao->getItem();
    }

    $filtered = [];
    
    foreach ($this->inventory as $item) {
        if ($categoryType == 'accessory') {
            if($specificCategory=='all'){
                if ($item->getCategory() == 'head' || $item->getCategory() == 'body' || $item->getCategory() == 'neck') {
                    $filtered[] = $item;
                }
            }else
            {
                if($item->getCategory() ==$specificCategory)
                {
                    $filtered[] = $item;
                }
            }

        }else if($categoryType == 'decoration'){
            if($specificCategory=='all'){
                if (
                    in_array($item->getCategory(), [
                        'bed',
                        'painting',
                        'picture1',
                        'picture2',
                        'plant',
                        'window1',
                        'window2',
                        'window3',
                        'room',
                        'foodBowl',
                        'water',
                        'playground1',
                        'playground2',
                        'playground3'
                    ])
                ) {
                    $filtered[] = $item;
                }
            }else
            {
                if($item->getCategory() ==$specificCategory)
                {
                    $filtered[] = $item;
                }
            }
        }
        else {
            if($specificCategory=='all'){
                if ($item->getCategory() != 'head' && $item->getCategory() != 'body' && $item->getCategory() != 'neck' && $item->getCategory() != 'consumable') {
                    $filtered[] = $item;
                }
            }else
            {
                if($item->getCategory() ==$specificCategory)
                {
                    $filtered[] = $item;
                }
            }
        }
    }
    if ($sort === 'asc') {
        usort($filtered, function ($a, $b) {
            return strcmp(strtolower($a->getName()), strtolower($b->getName()));
        });
    } elseif ($sort === 'desc') {
        usort($filtered, function ($a, $b) {
            return strcmp(strtolower($b->getName()), strtolower($a->getName()));
        });
    }

    return $filtered;
}

public function getItemByID($itemId) {
    if (empty($this->inventory)) {
        $this->inventory = $this->dao->getItem();
    }

    $itemData = $this->dao->getItemByID($itemId);

    if ($itemData) {
        return $itemData; // Assuming your Inventory class expects array data
    }

    return null;
}


}