<?php
session_start();

// 1. Kết nối DB
$db_user   = 'root';
$db_pass   = '';
$db_name   = 'expensenote_website_db';
$conn      = new mysqli('localhost', $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// 2. Nạp Controllers (Controllers sẽ tự require BO bên trong)
require_once 'controllers/AuthController.php';
require_once 'controllers/ExpenseController.php';
require_once 'controllers/UploadController.php';

// 3. Khởi tạo Controllers, truyền $conn vào Constructor
$auth    = new AuthController($conn);
$expense = new ExpenseController($conn);
$upload  = new UploadController($conn);

// 4. Lấy route từ query string
$route = $_GET['route'] ?? 'login';

// 5. Dispatch theo route
switch ($route) {
    case 'register':
        $auth->showRegisterForm();
        break;

    case 'register_post':
        $auth->register();
        break;

    case 'login':
        $auth->showLoginForm();
        break;

    case 'login_post':
        $auth->login();
        break;

    case 'logout':
        $auth->logout();
        break;

    case 'expense':
        $expense->showExpenseDashboard();
        break;

    case 'expense_post':
        $expense->saveExpense();
        break;

    case 'upload':
        $upload->showUploadForm();
        break;

    case 'upload_post':
        $upload->upload();
        break;
    case 'expense_post':
        $expense->saveExpense();
        break;

    case 'expense_edit':
        $expense->showEditForm();
        break;

    case 'expense_update':
        $expense->updateExpense();
        break;

    case 'expense_delete':
        $expense->deleteExpense();
        break;

    case 'logout':
        $auth->logout();
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 – Không tìm thấy route</h1>";
        break;
}
