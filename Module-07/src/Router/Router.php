<?php
class Router {
    private $userController;

    public function __construct($userController) {
        $this->userController = $userController;
    }

    public function delegate() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'home';

        switch ($action) {
            case 'home':
                require __DIR__ . '/../View/home.php';
                break;

            case 'register':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->userController->register();
                } else {
                    require __DIR__ . '/../View/register.php';
                }
                break;

            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->userController->loginUser();
                } else {
                    require __DIR__ . '/../View/login.php';
                }
                break;

            case 'logout':
                $this->userController->logout();
                break;

            case 'dashboard':
                if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['user_id'])) {
                    require __DIR__ . '/../View/dashboard.php';
                } else {
                    require __DIR__ . '/../View/error.php';
                }
                break;

            case 'update':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->userController->updateUserInfo();
                } else {
                    require __DIR__ . '/../View/update.php';
                }
                break;

            default:
                $message = 'Page not found';
                require __DIR__ . '/../View/error.php';
                break;
        }
    }
}
