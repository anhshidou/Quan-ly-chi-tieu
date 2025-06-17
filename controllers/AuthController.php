<?php
// File: controllers/AuthController.php

class AuthController {
    private $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function showRegisterForm(): void {
        include __DIR__ . '/../register.html';
        if (!empty($_SESSION['error'])) {
            echo "<p class='error'>" . htmlspecialchars($_SESSION['error']) . "</p>";
            unset($_SESSION['error']);
        }
    }

    public function register(): void {
        $_BO_CONN = $this->conn;
        require __DIR__ . '/../bo/register.php';
    }

    public function showLoginForm(): void {
        include __DIR__ . '/../index.html';
        if (!empty($_SESSION['error'])) {
            echo "<p class='error'>" . htmlspecialchars($_SESSION['error']) . "</p>";
            unset($_SESSION['error']);
        }
        if (!empty($_SESSION['success'])) {
            echo "<p class='success'>" . htmlspecialchars($_SESSION['success']) . "</p>";
            unset($_SESSION['success']);
        }
    }

    public function login(): void {
        $_BO_CONN = $this->conn;
        require __DIR__ . '/../bo/login.php';
    }

 
    public function logout(){

        session_start();
        session_destroy();
        header('Location: index.php?route=login');
        exit();
    }
}
?>