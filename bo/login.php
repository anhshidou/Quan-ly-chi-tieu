<?php
// bo/login.php

$conn = $_BO_CONN;

// Chỉ xử lý khi POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?route=login');
    exit;
}

session_start();
$errors = [];

// Lấy dữ liệu POST
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validate bắt buộc
if ($username === '' || $password === '') {
    $errors[] = 'Username and password are required.';
}

// Nếu có lỗi thì redirect
if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header('Location: index.php?route=login');
    exit;
}

// Kiểm tra trong DB
$stmt = $conn->prepare("SELECT password FROM registered_users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row || !password_verify($password, $row['password'])) {
    $_SESSION['error'] = 'Invalid username or password.';
    header('Location: index.php?route=login');
    exit;
}

// Đăng nhập thành công
$_SESSION['username'] = $username;
header('Location: index.php?route=expense');
exit;
