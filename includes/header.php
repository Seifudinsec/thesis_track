<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ThesisTrack | Centralized Management System</title>
    
    <!-- Robust Favicon Settings -->
    <link rel="icon" type="image/png" href="/thesis_track/assets/logo.png">
    <link rel="shortcut icon" href="/thesis_track/assets/logo.png" type="image/png">
    <link rel="apple-touch-icon" href="/thesis_track/assets/logo.png">
    
    <link rel="stylesheet" href="/thesis_track/assets/style.css">
</head>
<body>
    <header>
        <a href="/thesis_track/index.php" class="logo-container">
            <img src="/thesis_track/assets/logo.png" alt="ThesisTrack Logo" class="nav-logo">
            <span class="logo-text">ThesisTrack</span>
        </a>
        <nav>
            <ul>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'student'): ?>
                        <li><a href="/thesis_track/pages/student_dashboard.php">Dashboard</a></li>
                        <li><a href="/thesis_track/pages/upload.php">Upload Thesis</a></li>
                    <?php elseif ($_SESSION['role'] == 'supervisor'): ?>
                        <li><a href="/thesis_track/pages/supervisor_dashboard.php">Dashboard</a></li>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <li><a href="/thesis_track/pages/admin_dashboard.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="/thesis_track/logout.php" class="btn btn-accent" style="color: white;">Logout</a></li>
                <?php else: ?>
                    <li><a href="/thesis_track/index.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <?php
        if (isset($_SESSION['flash_message'])) {
            $type = $_SESSION['flash_type'] ?? 'success';
            echo "<div class='alert alert-$type'>{$_SESSION['flash_message']}</div>";
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
        }
        ?>
