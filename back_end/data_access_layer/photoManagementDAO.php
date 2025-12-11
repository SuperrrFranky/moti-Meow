<?php
require_once(__DIR__ . '/../business_logic_layer/PhotoManagementService/PhotoManagementService.php');
require_once(__DIR__ . '../../../_base.php');
class PhotoManagementDAO{
    private $userID;
    private $db;
    public function __construct($userID) {
        global $_db;
        $this->userID = $userID;
        $this->db = $_db;
    }
    
    public function getPhotos() {
        $stm = $this->db->prepare('SELECT * FROM gallery WHERE userID = ? ORDER BY galleryID DESC');
        $stm->execute([$this->userID]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function addPhoto($description,$imagePath){
        $dateCreated = date("Y-m-d H:i:s");

        $sql="INSERT INTO gallery
        (userID,description,imagePath,date)
        VALUES(?,?,?,?)";
        $stm = $this->db->prepare($sql);
        $stm->execute([$this->userID,$description,$imagePath,$dateCreated]);
    }

    public function deletePhoto($galleryID){
        $stm = $this->db->prepare('DELETE FROM gallery WHERE galleryID = ? AND userID = ?');
        return $stm->execute([$galleryID, $this->userID]);
    }

    public function getPhotoById($galleryID) {
        $stm = $this->db->prepare('SELECT * FROM gallery WHERE galleryID = ? AND userID = ?');
        $stm->execute([$galleryID, $this->userID]);
        return $stm->fetch(PDO::FETCH_ASSOC);
    }
    

}
