<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'supervisor'], true)) {
    header("Location: ../index.php");
    exit();
}

$is_admin = $_SESSION['role'] === 'admin';
$dashboard_url = $is_admin ? 'admin_dashboard.php' : 'supervisor_dashboard.php';
$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header("Location: $dashboard_url");
    exit();
}

// Prevent self-deletion
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['flash_message'] = "Error: You cannot delete your own account.";
    $_SESSION['flash_type'] = "error";
    header("Location: $dashboard_url");
    exit();
}

try {
    if (!$is_admin) {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $target_role = $stmt->fetchColumn();

        if ($target_role !== 'student') {
            $_SESSION['flash_message'] = "Error: Supervisors can only delete student accounts.";
            $_SESSION['flash_type'] = "error";
            header("Location: $dashboard_url");
            exit();
        }
    }

    // Check for dependencies
    // 1. Submissions (for students)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE student_id = ?");
    $stmt->execute([$user_id]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['flash_message'] = "Error: Cannot delete user. They have active thesis submissions.";
        $_SESSION['flash_type'] = "error";
        header("Location: $dashboard_url");
        exit();
    }

    // 2. Feedback (for supervisors)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE supervisor_id = ?");
    $stmt->execute([$user_id]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['flash_message'] = "Error: Cannot delete user. They have provided feedback on submissions.";
        $_SESSION['flash_type'] = "error";
        header("Location: $dashboard_url");
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

header("Location: $dashboard_url");
exit();
