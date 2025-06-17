<?php
// bo/delete_expense.php
session_start();

// 1. Lấy ID (GET hoặc POST)
$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = 'ID không hợp lệ.';
    header('Location: index.php?route=expense');
    exit;
}

// 2. Lấy user_id
$stmt = $_BO_CONN->prepare("
    SELECT id 
    FROM registered_users 
    WHERE username = ?
");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_id = $user['id'] ?? 0;

// 3. Xóa các receipts trước (nếu có)
$stmt = $_BO_CONN->prepare("
    DELETE FROM receipts 
    WHERE nhatkychitieu_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();

// 4. Xóa expense
$stmt = $_BO_CONN->prepare("
    DELETE FROM nhatkychitieu
    WHERE id = ? 
      AND user_id = ?
");
$stmt->bind_param("ii", $id, $user_id);
if ($stmt->execute()) {
    $_SESSION['success'] = 'Xóa chi tiêu thành công.';
} else {
    $_SESSION['error'] = 'Lỗi khi xóa: ' . $stmt->error;
}

// 5. Quay về dashboard
header('Location: index.php?route=expense');
exit;
