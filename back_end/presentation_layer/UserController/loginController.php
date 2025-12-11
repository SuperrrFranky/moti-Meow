<?php
require_once '../../business_logic_layer/userService/loginService.php';
require_once '../../../_base.php';
if(is_post()){
    $_SESSION['user_id'] = 1;

    $loginService = new LoginService();
    $loginService->login();

    $_SESSION['login_success'] = true;

    header('Location: ../../../index.php');
    exit;
}
?>