<?php
// bo/update_expense.php
session_start();

// 1. Kết nối DB (đã có từ controller truyền vào)
$conn = $_BO_CONN;

// 2. Lấy và validate dữ liệu từ form
$id          = intval($_POST['id'] ?? 0);
$date        = $_POST['date'] ?? date('Y-m-d');
$amount      = floatval($_POST['amount'] ?? 0);
$description = trim($_POST['description'] ?? '');

if ($id <= 0) {
    $_SESSION['error'] = 'ID không hợp lệ.';
    header('Location: index.php?route=expense');
    exit;
}

// 3. Lấy user_id từ session để đảm bảo chỉ update bản ghi của chính user
$stmt = $conn->prepare("
    SELECT id 
    FROM registered_users 
    WHERE username = ?
");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_id = $user['id'] ?? 0;

// 4. Thực hiện UPDATE
$stmt = $conn->prepare("
    UPDATE nhatkychitieu
    SET date = ?, expense = ?, description = ?
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("sdsii",
    $date,
    $amount,
    $description,
    $id,
    $user_id
);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Cập nhật chi tiêu thành công.';
} else {
    $_SESSION['error'] = 'Lỗi khi cập nhật: ' . $stmt->error;
}

// 5. Quay về dashboard
header('Location: index.php?route=expense');
exit;
