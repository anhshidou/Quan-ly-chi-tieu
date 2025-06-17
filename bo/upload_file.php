<?php
// bo/upload_file.php
session_start();
$conn = $_BO_CONN;

// Lấy user_id
$stmt = $conn->prepare("SELECT id FROM registered_users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_id = $user['id'];

// Lấy entry_id từ form
$expense_id = intval($_POST['entry_id'] ?? 0);
if ($expense_id <= 0) {
    $_SESSION['error'] = 'Expense ID không hợp lệ.';
    header('Location: index.php?route=upload');
    exit;
}

// Kiểm tra expense thuộc user
$stmt = $conn->prepare(
  "SELECT id FROM nhatkychitieu WHERE id = ? AND user_id = ?"
);
$stmt->bind_param("ii", $expense_id, $user_id);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    $_SESSION['error'] = 'Không tìm thấy bản ghi chi tiêu phù hợp.';
    header('Location: index.php?route=upload');
    exit;
}

// Xử lý file upload
if (!isset($_FILES['file'])) {
    $_SESSION['error'] = 'Chưa chọn file.';
    header('Location: index.php?route=upload');
    exit;
}
$file = $_FILES['file'];
$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['pdf','jpg','jpeg','png'])) {
    $_SESSION['error'] = 'Định dạng không hợp lệ.';
    header('Location: index.php?route=upload');
    exit;
}
if ($file['error'] !== UPLOAD_ERR_OK || $file['size']>5*1024*1024) {
    $_SESSION['error'] = 'Lỗi khi upload hoặc kích thước vượt quá 5MB.';
    header('Location: index.php?route=upload');
    exit;
}
$uniq = uniqid('receipt_',true).'.'.$ext;
if (!is_dir('uploads')) mkdir('uploads',0777,true);
$dest = 'uploads/'.$uniq;
move_uploaded_file($file['tmp_name'], $dest);

// Lưu vào DB
$stmt = $conn->prepare(
  "INSERT INTO receipts (user_id, nhatkychitieu_id, file_name, file_path)
   VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("iiss", $user_id, $expense_id, $file['name'], $dest);
if ($stmt->execute()) {
    $_SESSION['success'] = 'Upload thành công.';
} else {
    $_SESSION['error'] = 'Lỗi DB: ' . $stmt->error;
}
header('Location: index.php?route=expense');
exit;
