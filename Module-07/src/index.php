<?php
session_start();

require_once __DIR__ . '/Database/Database.php';
require_once __DIR__ . '/Router/Router.php';
require_once __DIR__ . '/Model/UserModel.php';
require_once __DIR__ . '/Controller/UserController.php';
require_once __DIR__ . '/Utils/SecurityUtils.php';

$database = new Database();
$db = $database->getConnection();

$userModel = new UserModel($db);
$userController = new UserController($userModel);

$router = new Router($userController);
$router->delegate();
