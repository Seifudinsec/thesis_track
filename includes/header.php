<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? null;
$dashboard_path = '/thesis_track/index.php';

if ($role == 'student') {
    $dashboard_path = '/thesis_track/pages/student_dashboard.php';
} elseif ($role == 'supervisor') {
    $dashboard_path = '/thesis_track/pages/supervisor_dashboard.php';
} elseif ($role == 'admin') {
    $dashboard_path = '/thesis_track/pages/admin_dashboard.php';
}

if (!function_exists('nav_class')) {
    function nav_class($current_page, $pages) {
        return in_array($current_page, (array) $pages, true) ? 'active' : '';
    }
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
    
    <link rel="stylesheet" href="/thesis_track/assets/style.css?v=<?php echo time(); ?>">
</head>
<body class="<?php echo $is_logged_in ? 'app-layout' : 'auth-layout'; ?>">
    <?php if ($is_logged_in): ?>
        <div class="app-shell">
            <aside class="sidebar">
                <a href="<?php echo $dashboard_path; ?>" class="sidebar-brand">
                    <img src="/thesis_track/assets/logo.png" alt="ThesisTrack Logo" class="nav-logo">
                    <span>ThesisTrack</span>
                </a>

                <nav class="sidebar-nav">
                    <?php if ($role == 'student'): ?>
                        <div class="nav-section">
                            <p class="nav-section-title">Workspace</p>
                            <a class="nav-item <?php echo nav_class($current_page, 'student_dashboard.php'); ?>" href="/thesis_track/pages/student_dashboard.php">
                                <span class="nav-icon">D</span>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <div class="nav-section">
                            <p class="nav-section-title">Submissions</p>
                            <a class="nav-item <?php echo nav_class($current_page, ['upload.php', 'edit_submission.php']); ?>" href="/thesis_track/pages/upload.php">
                                <span class="nav-icon">U</span>
                                <span>Upload Thesis</span>
                            </a>
                        </div>
                    <?php elseif ($role == 'supervisor'): ?>
                        <div class="nav-section">
                            <p class="nav-section-title">Review</p>
                            <a class="nav-item <?php echo nav_class($current_page, 'supervisor_dashboard.php'); ?>" href="/thesis_track/pages/supervisor_dashboard.php">
                                <span class="nav-icon">D</span>
                                <span>Dashboard</span>
                            </a>
                            <a class="nav-item <?php echo nav_class($current_page, 'feedback.php'); ?>" href="/thesis_track/pages/supervisor_dashboard.php#review-queue">
                                <span class="nav-icon">R</span>
                                <span>Review Queue</span>
                            </a>
                        </div>
                        <div class="nav-section">
                            <p class="nav-section-title">Students</p>
                            <a class="nav-item <?php echo nav_class($current_page, ['add_user.php']); ?>" href="/thesis_track/pages/add_user.php">
                                <span class="nav-icon">A</span>
                                <span>Add Student</span>
                            </a>
                            <a class="nav-item <?php echo nav_class($current_page, ['edit_user.php', 'delete_user.php']); ?>" href="/thesis_track/pages/supervisor_dashboard.php#student-management">
                                <span class="nav-icon">M</span>
                                <span>Manage Students</span>
                            </a>
                        </div>
                    <?php elseif ($role == 'admin'): ?>
                        <div class="nav-section">
                            <p class="nav-section-title">Administration</p>
                            <a class="nav-item <?php echo nav_class($current_page, 'admin_dashboard.php'); ?>" href="/thesis_track/pages/admin_dashboard.php">
                                <span class="nav-icon">D</span>
                                <span>Dashboard</span>
                            </a>
                            <a class="nav-item <?php echo nav_class($current_page, 'add_user.php'); ?>" href="/thesis_track/pages/add_user.php">
                                <span class="nav-icon">A</span>
                                <span>Add User</span>
                            </a>
                            <a class="nav-item <?php echo nav_class($current_page, ['edit_user.php', 'delete_user.php']); ?>" href="/thesis_track/pages/admin_dashboard.php#user-management">
                                <span class="nav-icon">M</span>
                                <span>Manage Users</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>

                <div class="sidebar-user">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
                    <div>
                        <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>
                        <span><?php echo ucfirst(htmlspecialchars($role)); ?></span>
                    </div>
                    <a href="/thesis_track/logout.php" class="logout-link">Logout</a>
                </div>
            </aside>

            <main class="app-main">
    <?php else: ?>
            <main class="auth-main">
    <?php endif; ?>
        <?php
        if (isset($_SESSION['flash_message'])) {
            $type = $_SESSION['flash_type'] ?? 'success';
            echo "<div class='alert alert-$type'>" . htmlspecialchars($_SESSION['flash_message']) . "</div>";
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
        }
        ?>
