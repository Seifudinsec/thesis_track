<?php
require_once __DIR__ . '/../config/db.php';
session_start();

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$submission_id = $_GET['id'] ?? null;
$student_id = $_SESSION['user_id'];

// Verify ownership and status
$stmt = $pdo->prepare("SELECT * FROM submissions WHERE id = ? AND student_id = ?");
$stmt->execute([$submission_id, $student_id]);
$submission = $stmt->fetch();

if ($submission && $submission['status'] === 'pending') {
    // 1. Delete the physical file from the server
    if (file_exists("../" . $submission['file_path'])) {
        unlink("../" . $submission['file_path']);
    }

    // 2. Delete the record from the database
    $stmt = $pdo->prepare("DELETE FROM submissions WHERE id = ?");
    if ($stmt->execute([$submission_id])) {
        $_SESSION['flash_message'] = "Submission deleted successfully.";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Error deleting from database.";
        $_SESSION['flash_type'] = "error";
    }
} else {
    $_SESSION['flash_message'] = "Unauthorized or submission is locked.";
    $_SESSION['flash_type'] = "error";
}

header("Location: student_dashboard.php");
exit();
?>
