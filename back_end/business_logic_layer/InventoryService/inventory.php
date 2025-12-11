<?php

class Inventory {
    private $inventoryID;
    private $category;
    private $name;
    private $image;
    private $unlockAt;
    private $quantity;

    public function __construct($inventoryID,$category,$name,$image,$unlockAt,$quantity) {
        $this->inventoryID = $inventoryID;
        $this->category = $category;
        $this->name = $name;
        $this->image = $image;
        $this->unlockAt = $unlockAt;
        $this->quantity = $quantity;
    }


    public function serialize() {
        return serialize([
            $this->inventoryID, 
            $this->category, 
            $this->name, 
            $this->image, 
            $this->unlockAt, 
            $this->quantity
        ]);
    }

    public function unserialize($data) {
        list(
            $this->inventoryID, 
            $this->category, 
            $this->name, 
            $this->image, 
            $this->unlockAt, 
            $this->quantity
        ) = unserialize($data);
    }

    public function getID() {
        return $this->inventoryID;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getName() {
        return $this->name;
    }

    public function getImage() {
        return $this->image;
    }

    public function getUnlockDate() {
        return $this->unlockAt;
    }

    public function getQuantity() {
        return $this->quantity;
    }
    
    public function setQuantity($quantity) {
        $this->quantity=$quantity;
    }
}