<?php
require_once '../../business_logic_layer/PhotoManagementService/PhotoManagement.php';
require_once '../../data_access_layer/photoManagementDAO.php';
require_once '../../business_logic_layer/PhotoManagementService/photoManagementService.php';



// Handle uploaded image
if (isset($_FILES['newPhoto'])) {
    $img_name = $_FILES['newPhoto']['name'];
    $img_size = $_FILES['newPhoto']['size'];
    $tmp_name = $_FILES['newPhoto']['tmp_name'];
    $error = $_FILES['newPhoto']['error'];

    header('Content-Type: application/json');

    if ($error === 0) {
        if ($img_size > 1250000) {
            echo json_encode(["status" => "error", "message" => "Sorry, your file is too large."]);
            exit();
        } else {
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);

            $allowed_exs = array("jpg", "jpeg", "png");

            if (in_array($img_ex_lc, $allowed_exs)) {
                $new_img_name = uniqid("IMG-", true).'.'.$img_ex_lc;
                $img_upload_path = '../uploads/'.$new_img_name;

                move_uploaded_file($tmp_name, $img_upload_path);

                $description = $_POST['description'] ?? '';
                $date = date('Y-m-d H:i:s');

                $photo = new PhotoManagement();
                $photo->setImageName($new_img_name);
                $photo->setDescription($description);
                $photo->setDate($date);

                session_start();
                $userID = $_SESSION['userID'];

                $service = new PhotoManagementService($userID);
                $service->addPhoto($description, $new_img_name);

                echo json_encode(["status" => "success", "message" => "Photo uploaded successfully"]);
                exit();
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid file type"]);
                exit();
            }
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Unknown error occurred!"]);
        exit();
    }
}
?>
