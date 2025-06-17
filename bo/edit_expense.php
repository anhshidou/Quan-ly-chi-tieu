<?php

// 1. Lấy ID từ GET
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = 'ID không hợp lệ.';
    header('Location: index.php?route=expense');
    exit;
}

// 2. Lấy user_id từ session
$stmt = $_BO_CONN->prepare("
    SELECT id 
    FROM registered_users 
    WHERE username = ?
");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_id = $user['id'] ?? 0;

// 3. Lấy bản ghi chi tiêu
$stmt = $_BO_CONN->prepare("
    SELECT * 
    FROM nhatkychitieu 
    WHERE id = ? 
      AND user_id = ?
");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $_SESSION['error'] = 'Không tìm thấy chi tiêu phù hợp.';
    header('Location: index.php?route=expense');
    exit;
}

$expense = $result->fetch_assoc();

// 4. Include form edit (có thể là một file HTML/PHP riêng)
//    hoặc bạn đặt form ngay trong stub này.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Expense</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Edit Expense</h1>
  <?php if (!empty($_SESSION['error'])): ?>
    <p class="error"><?= $_SESSION['error'] ?></p>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>
  <form action="index.php?route=expense_update" method="POST">
    <input type="hidden" name="id" value="<?= $expense['id'] ?>">
    <label>Date:</label>
    <input type="date" name="date" value="<?= $expense['date'] ?>" required>
    <label>Amount:</label>
    <input type="number" name="amount" value="<?= $expense['expense'] ?>" required>
    <label>Description:</label>
    <input type="text" name="description" value="<?= htmlspecialchars($expense['description']) ?>" required>
    <input type="submit" value="Save">
  </form>
  <p><a href="index.php?route=expense">← Back to Dashboard</a></p>
</body>
</html>
