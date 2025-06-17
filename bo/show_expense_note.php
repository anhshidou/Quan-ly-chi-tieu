<?php
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'expensenote_website_db'; 

    $conn = new mysqli('localhost', $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $expenseId = isset($_POST['expense_id']) ? intval($_POST['expense_id']) : 0;

    if ($expenseId > 0) {
        $stmt = $conn->prepare("SELECT * FROM nhatkychitieu WHERE id = ?");
        $stmt->bind_param("i", $expenseId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $expense = $result->fetch_assoc();
            echo "<h1>Expense Details</h1>";
            echo "<p><strong>ID:</strong> " . htmlspecialchars($expense['id']) . "</p>";
            echo "<p><strong>Description:</strong> " . htmlspecialchars($expense['description']) . "</p>";
            echo "<p><strong>Amount:</strong> $" . htmlspecialchars(number_format($expense['amount'], 2)) . "</p>";
            echo "<p><strong>Made by:</strong> " . htmlspecialchars($expense['made_by']) . "</p>";
            echo "<p><strong>Created Date:</strong> " . htmlspecialchars($expense['date']) . "</p>";
        } else {
            echo "<p>Expense not found.</p>";
        }
    } else {
        echo "<p>Invalid expense ID.</p>";
    }

    $conn->close();
}
?>