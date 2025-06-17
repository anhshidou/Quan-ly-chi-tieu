<?php
// bo/register.php

// Kết nối được truyền vào từ Controller
$conn = $_BO_CONN;

// Chỉ xử lý khi POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?route=register');
    exit;
}

session_start();
$errors = [];

// Lấy dữ liệu POST
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$email    = trim($_POST['email'] ?? '');

// Validate bắt buộc
if ($username === '' || $password === '' || $email === '') {
    $errors[] = 'Please fill in all fields.';
}

// Validate email
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}

// Kiểm tra tồn tại username/email
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT id FROM registered_users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        $errors[] = 'Username or email already exists.';
    }
}

// Nếu có lỗi, lưu vào session và redirect
if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header('Location: index.php?route=register');
    exit;
}

// Hash mật khẩu và insert
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO registered_users (username, password, email) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hash, $email);
if (!$stmt->execute()) {
    $_SESSION['error'] = 'Registration failed: ' . $stmt->error;
    header('Location: index.php?route=register');
    exit;
}

// Thành công
$_SESSION['success'] = 'Registration successful! Please log in.';
header('Location: index.php?route=login');
exit;
