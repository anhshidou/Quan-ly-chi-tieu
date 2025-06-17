<?php
// bo/expense_note.php

session_start();
// $_BO_CONN đã có kết nối từ controller
$conn = $_BO_CONN;

// Lấy user_id
$stmt = $conn->prepare("SELECT id FROM registered_users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date        = $_POST['date'] ?? date('Y-m-d');
    $amount      = $_POST['amount'];
    $description = $_POST['description'];

    $stmt = $conn->prepare(
      "INSERT INTO nhatkychitieu (user_id, date, expense, description) 
       VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("isds", $user_id, $date, $amount, $description);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Đã lưu chi tiêu thành công.';
    } else {
        $_SESSION['error'] = 'Lỗi khi lưu: ' . $stmt->error;
    }
    header('Location: index.php?route=expense');
    exit;
}
