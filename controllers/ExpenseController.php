<?php
class ExpenseController {
    private $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // Dashboard: danh sách + form thêm mới
    public function showExpenseDashboard(): void {
        if (!isset($_SESSION['username'])) {
            header('Location: index.php?route=login'); exit;
        }
        // Lấy user_id
        $stmt = $this->conn->prepare("SELECT id FROM registered_users WHERE username = ?");
        $stmt->bind_param("s", $_SESSION['username']);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $user_id = $user['id'];

        // Lấy danh sách chi tiêu
        $stmt = $this->conn->prepare(
            "SELECT id, date, expense, description FROM nhatkychitieu WHERE user_id = ? ORDER BY date DESC"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $expenses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        include __DIR__ . '/../expense_dashboard.html';
        if (!empty($_SESSION['error'])) {
            echo "<p class='error'>" . htmlspecialchars($_SESSION['error']) . "</p>";
            unset($_SESSION['error']);
        }
    }

    // Add new expense
    public function saveExpense(): void {
        $_BO_CONN = $this->conn;
        require __DIR__ . '/../bo/expense_note.php';
    }

    // Show edit form
    public function showEditForm(): void {
        if (!isset($_SESSION['username'])) { header('Location: index.php?route=login'); exit; }
        $id = intval($_GET['id'] ?? 0);
        $_BO_CONN = $this->conn;
        require __DIR__ . '/../bo/edit_expense.php'; // file BO hiển thị form edit và xử lý view
    }

    // Update expense
    public function updateExpense(): void {
        $_BO_CONN = $this->conn;
        require __DIR__ . '/../bo/update_expense.php';
    }

    // Delete expense
    public function deleteExpense(): void {
        $_BO_CONN = $this->conn;
        require __DIR__ . '/../bo/delete_expense.php';
    }
}

    // TODO: bạn có thể thêm các phương thức editExpense() và deleteExpense() tương tự
?>