<?php
require_once (__DIR__ .'/../../data_access_layer/photoManagementDAO.php'); 
class PhotoManagementService{
    private $dao;

    public function __construct($userID) {
        $this->dao = new PhotoManagementDAO($userID);
    }

    public function addPhoto($description,$f){
        
       
                if($f){
                    $photoDirectory = __DIR__ . '/../../../res/image/photoGallery';
                    $newPhoto = save_photo($f, $photoDirectory);
                };

                $this->dao->addPhoto($description,$newPhoto);
        
                temp('info', "Photo successfully added !(＾∀＾)");
    }

    public function getPhotos() {
        return $this->dao->getPhotos();
    }

    public function deletePhoto($galleryID){
        $photo = $this->getPhotoById($galleryID);

        if ($photo && isset($photo['imagePath'])) {
            $filePath = __DIR__ . '/../../../res/image/photoGallery/' . basename($photo['imagePath']);
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    
        return $this->dao->deletePhoto($galleryID);
    }

    public function getPhotoById($galleryID) {
        return $this->dao->getPhotoById($galleryID);
    }
    

    
}


