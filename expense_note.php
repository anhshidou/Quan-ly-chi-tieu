<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

$db_user = 'root';
$db_password = '';
$db_name = 'your_database_name'; // Change this to your actual DB name (not .sql file)

$conn = new mysqli('localhost', $db_user, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from username
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? date('Y-m-d');
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO nhatkychitieu (user_id, date, expense, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $date, $amount, $description);

    if ($stmt->execute()) {
        echo "Expense recorded successfully.";
        echo "<br><a href='expense_note.html'>Back</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
