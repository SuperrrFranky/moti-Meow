<?php
class PhotoManagement {
    private $imageName;
    private $description;
    private $date;

    public function setImageName($imageName) {
        $this->imageName = $imageName;
    }

    public function getImageName() {
        return $this->imageName;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getDate() {
        return $this->date;
    }
}
?>
