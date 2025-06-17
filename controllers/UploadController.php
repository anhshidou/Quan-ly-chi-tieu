<?php
class UploadController {
    private $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function showUploadForm(): void {
        include __DIR__ . '/../upload_file.html';
        if (!empty($_SESSION['error'])) {
            echo "<p class='error'>" . htmlspecialchars($_SESSION['error']) . "</p>";
            unset($_SESSION['error']);
        }
    }

    public function upload(): void {
        $_BO_CONN = $this->conn;
        require __DIR__ . '/../bo/upload_file.php';
    }
}
?>
