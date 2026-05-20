<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header("Location: admin_dashboard.php");
    exit();
}

// Prevent self-deletion
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['flash_message'] = "Error: You cannot delete your own account.";
    $_SESSION['flash_type'] = "error";
    header("Location: admin_dashboard.php");
    exit();
}

try {
    // Check for dependencies
    // 1. Submissions (for students)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE student_id = ?");
    $stmt->execute([$user_id]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['flash_message'] = "Error: Cannot delete user. They have active thesis submissions.";
        $_SESSION['flash_type'] = "error";
        header("Location: admin_dashboard.php");
        exit();
    }

    // 2. Feedback (for supervisors)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE supervisor_id = ?");
    $stmt->execute([$user_id]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['flash_message'] = "Error: Cannot delete user. They have provided feedback on submissions.";
        $_SESSION['flash_type'] = "error";
        header("Location: admin_dashboard.php");
        exit();
    }

    // If no dependencies, delete
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$user_id])) {
        $_SESSION['flash_message'] = "User deleted successfully!";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Error: Could not delete user.";
        $_SESSION['flash_type'] = "error";
    }
} catch (PDOException $e) {
    $_SESSION['flash_message'] = "Database Error: " . $e->getMessage();
    $_SESSION['flash_type'] = "error";
}

header("Location: admin_dashboard.php");
exit();
