<?php
require_once '../../data_access_layer/catProfileDAO.php';

class NameService {
    public static function getCatName($userId) {
        return catProfileDAO::getName($userId);
    }

    public static function updateCatName($userId, $newName) {
        $dao = new catProfileDAO($userId);
        return $dao->updateName($newName);
    }
}
