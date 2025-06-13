
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

$db_user = 'root';
$db_password = '';
$db_name = 'your_database_name'; // <-- Change to your actual DB name

$conn = new mysqli('localhost', $db_user, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM registered_users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("User not found.");
}
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Get latest expense entry by user
$stmt = $conn->prepare("SELECT id FROM nhatkychitieu WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$expense_id = null;
if ($row = $result->fetch_assoc()) {
    $expense_id = $row['id'];
} else {
    die("No expense entry found to attach file to.");
}

// Handle file upload
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $file_name = basename($file['name']);
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];

    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_extensions)) {
        die("Invalid file type.");
    }

    if ($file_error !== 0) {
        die("File upload error.");
    }

    if ($file_size > 5000000) {
        die("File too large (max 5MB).");
    }

    $unique_name = uniqid('receipt_', true) . '.' . $file_ext;
    $upload_path = 'uploads/' . $unique_name;

    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    if (move_uploaded_file($file_tmp, $upload_path)) {
        $stmt = $conn->prepare("INSERT INTO receipts (user_id, nhatkychitieu_id, file_name, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $expense_id, $file_name, $upload_path);

        if ($stmt->execute()) {
            echo "File uploaded and linked to your latest expense note.";
        } else {
            echo "Database error: " . $stmt->error;
        }
    } else {
        echo "Failed to move uploaded file.";
    }
} else {
    echo "No file uploaded.";
}
?>
