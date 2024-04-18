<?php

class UserController {
    private $userModel;

    public function __construct(UserModel $userModel) {
        $this->userModel = $userModel;
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            SecurityUtils::verifyCsrfToken();
            $nom = SecurityUtils::sanitizeInput($_POST['nom']);
            $prenom = SecurityUtils::sanitizeInput($_POST['prenom']);
            $adresse = SecurityUtils::sanitizeInput($_POST['adresse']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            $errors = SecurityUtils::validateRegistrationForm($nom, $prenom, $adresse, $email, $password, $confirmPassword);

            if (!empty($errors)) {
                $data['errors'] = $errors;
                include_once 'View/register.php';
            } else {
                if ($this->userModel->doesEmailExist($email)) {
                    $data['error'] = "email_exists";
                    include_once 'View/register.php';
                } elseif ($password !== $confirmPassword) {
                    $data['error'] = "password_mismatch";
                    include_once 'View/register.php';
                } else {
                    $this->userModel->registerUser($nom, $prenom, $adresse, $email, $password, 2);
                    header("Location: index.php?action=login");
                    exit();
                }
            }
        } else {
            include_once 'View/register.php';
        }
    }

    public function loginUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
    
            $user = $this->userModel->getUserByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                header("Location: index.php?action=dashboard");
                exit();
            } else {
                $data['error'] = "wrong_email_password";
                include_once 'View/login.php';
            }
        } else {
            include_once 'View/login.php';
        }
    }
    

    public function getUserInfo($id) {
        return $this->userModel->getUserById($id);
    }

    public function updateUserInfo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            SecurityUtils::verifyCsrfToken();
    
            $id = $_SESSION['user_id'];
            $nom = SecurityUtils::sanitizeInput($_POST['nom']);
            $prenom = SecurityUtils::sanitizeInput($_POST['prenom']);
            $adresse = SecurityUtils::sanitizeInput($_POST['adresse']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
    
            $errors = SecurityUtils::validateRegistrationForm($nom, $prenom, $adresse, $email, $password, $confirmPassword);
    
            if (!empty($errors)) {
                $data['errors'] = $errors;
                include_once 'View/update.php';
            } else {
                if ($email !== $_SESSION['email'] && $this->userModel->doesEmailExist($email)) {
                    $data['error'] = "email_exists";
                    include_once 'View/update.php';
                } elseif ($password !== $confirmPassword) {
                    $data['error'] = "password_mismatch";
                    include_once 'View/update.php';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $this->userModel->updateUser($id, $nom, $prenom, $adresse, $email, $hashedPassword);
    
                    if ($email !== $_SESSION['email']) {
                        $_SESSION['email'] = $email;
                    }
    
                    header("Location: index.php?action=dashboard");
                    exit();
                }
            }
        } else {
            include_once 'View/update.php';
        }
    }
    

    public function closeAccount() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            SecurityUtils::verifyCsrfToken();
            $id = $_SESSION['user_id'];
            $this->userModel->deleteUser($id);
            session_destroy();
            header("Location: index.php");
            exit();
        }
    }
    
    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit();
    }    
}
