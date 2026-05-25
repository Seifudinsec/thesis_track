<?php
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM deadlines WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['flash_message'] = "Deadline deleted successfully.";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Error deleting deadline.";
        $_SESSION['flash_type'] = "error";
    }
}

header("Location: manage_deadlines.php");
exit();
?>
