<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    public function login() {
        if (User::isLoggedIn()) {
            header("Location: " . app_url('dashboard'));
            exit;
        }

        $error = '';
        $notice = $_GET['notice'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = trim($_POST['email'] ?? $_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = User::authenticate($identifier, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role_name'] = $user['role_name'];

                if ($user['role_name'] === 'Admin') {
                    $redirect = $_SESSION['redirect_after_login'] ?? app_url('admin');
                } else {
                    $redirect = $_SESSION['redirect_after_login'] ?? app_url('dashboard');
                }
                unset($_SESSION['redirect_after_login']);
                header("Location: {$redirect}");
                exit;
            } else {
                $error = 'Invalid credentials. Please verify your username/email and password.';
            }
        }

        require __DIR__ . '/../../views/auth/login.php';
    }

    public function register() {
        if (User::isLoggedIn()) {
            header("Location: " . app_url('dashboard'));
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'mobile' => trim($_POST['mobile'] ?? ''),
                'company' => trim($_POST['company'] ?? ''),
                'designation' => trim($_POST['designation'] ?? ''),
                'password' => $_POST['password'] ?? ''
            ];

            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                $error = 'Please fill in all required fields.';
            } else {
                $result = User::register($data);
                if ($result['success']) {
                    $_SESSION['user_id'] = $result['user_id'];
                    $_SESSION['user_name'] = $data['name'];
                    $_SESSION['role_name'] = 'User';

                    header("Location: " . app_url('dashboard'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
        }

        require __DIR__ . '/../../views/auth/register.php';
    }

    public function forgotPassword() {
        if (User::isLoggedIn()) {
            header("Location: " . app_url('dashboard'));
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = trim($_POST['email'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($identifier) || empty($newPassword)) {
                $error = 'Please enter your registered email/username and new password.';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Passwords do not match. Please re-type your new password.';
            } elseif (strlen($newPassword) < 6) {
                $error = 'Password must be at least 6 characters in length.';
            } else {
                $user = User::findByEmailOrUsername($identifier);
                if ($user) {
                    if (User::resetPassword((int)$user['id'], $newPassword)) {
                        $success = 'Password has been successfully updated! You can now log in with your new password.';
                    } else {
                        $error = 'Could not update password. Please try again.';
                    }
                } else {
                    $error = 'No user account found matching this email or username.';
                }
            }
        }

        require __DIR__ . '/../../views/auth/forgot_password.php';
    }

    public function logout() {
        session_destroy();
        header("Location: " . app_url(''));
        exit;
    }
}
