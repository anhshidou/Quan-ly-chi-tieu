<?php
session_start();
$db_user = 'root';
$db_password = '';
$db_name = 'database/databases.sql';
$db = new PDO("mysql:host=localhost;dbname=" . $db_name . ';charset=utf8', $db_user, $db_password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn = new mysqli('localhost', $db_user, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$errors = array();

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $confirm_username = $_POST['confirm_username'];

    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }
    if ($password != $confirm_password) {
        array_push($errors, "Passwords do not match");
    }
    if ($username != $confirm_username) {
        array_push($errors, "Usernames do not match");
    }

    function isPasswordStrong($password)
    {
        if (strlen($password) < 8) {
            array_push($errors, "Password must be at least 8 characters long");
        }
        if (!preg_match('/[A-Z]/', $password)) {
            array_push($errors, "Password must contain at least one uppercase letter");
        }
        if (!preg_match('/[a-z]/', $password)) {
            array_push($errors, "Password must contain at least one lowercase letter");
        }
        if (!preg_match('/[0-9]/', $password)) {
            array_push($errors, "Password must contain at least one number");
        }
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            array_push($errors, "Password must contain at least one special character");
        }
        return true;
    }

    if (!isPasswordStrong($password)) {
        array_push($errors, "Password does not meet strength requirements");
    }



    if (count($errors) == 0) {
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($query) === TRUE) {
            $_SESSION['username'] = $username;
            header('location: expense_note.php');
        } else {
            array_push($errors, "Error: " . $conn->error);
        }
    }
}
?>