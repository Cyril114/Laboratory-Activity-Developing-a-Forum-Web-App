<?php
namespace App\Controllers;

use App\Models\User;
use App\Security;
use App\View;

class AuthController
{
    public function showRegister(): void
    {
        View::render('register');
    }

    public function register(): void
    {
        if (!Security::checkCsrf()) {
            View::error(400, 'Invalid CSRF token.');
            return;
        }

        $username = trim((string) ($_POST['username'] ?? ''));
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $error = null;
        if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
            $error = 'Username: 3–30 letters, numbers or _.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif (User::exists($username, $email)) {
            $error = 'Username or email is already taken.';
        }

        if ($error) {
            View::render('register', [
                'error' => $error,
                'old_username' => $username,
                'old_email' => $email,
            ], 422);
            return;
        }

        User::create($username, $email, $password); 
        header('Location: /login?registered=1', true, 303);
    }

    public function showLogin(): void
    {
        View::render('login', ['registered' => isset($_GET['registered'])]);
    }

    public function login(): void
    {
        if (!Security::checkCsrf()) {
            View::error(400, 'Invalid CSRF token.');
            return;
        }

        $userId = User::verify((string) ($_POST['username'] ?? ''), (string) ($_POST['password'] ?? ''));
        if (!$userId) {
            View::render('login', ['error' => 'Incorrect username or password.'], 401);
            return;
        }

        session_regenerate_id(true); 
        $_SESSION['user_id'] = $userId;
        unset($_SESSION['csrf']);   
        header('Location: /discussions/1', true, 303);
    }

    public function logout(): void
    {
        if (!Security::checkCsrf()) {
            View::error(400, 'Invalid CSRF token.');
            return;
        }
        $_SESSION = [];
        session_regenerate_id(true);
        header('Location: /discussions/1', true, 303);
    }
}