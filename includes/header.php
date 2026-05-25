<?php
// Session is now started in config/db.php which should be included before header.php
// But we keep this check for robustness if header.php is included without db.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? null;

// Determine relative path to root dynamically
$base_path = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';

$dashboard_path = $base_path . 'index.php';
if ($role == 'student') {
    $dashboard_path = $base_path . 'pages/student_dashboard.php';
} elseif ($role == 'supervisor') {
    $dashboard_path = $base_path . 'pages/supervisor_dashboard.php';
} elseif ($role == 'admin') {
    $dashboard_path = $base_path . 'pages/admin_dashboard.php';
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
    <link rel="icon" type="image/png" href="<?php echo $base_path; ?>assets/logo.png">
    <link rel="shortcut icon" href="<?php echo $base_path; ?>assets/logo.png" type="image/png">
    <link rel="apple-touch-icon" href="<?php echo $base_path; ?>assets/logo.png">
    
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/style.css?v=<?php echo time(); ?>">
</head>
<body class="<?php echo $is_logged_in ? 'app-layout' : 'auth-layout'; ?>">
    <?php if ($is_logged_in): ?>
        <div class="app-shell">
            <div class="mobile-topbar">
                <button type="button" class="sidebar-toggle" aria-label="Open navigation menu" aria-controls="app-sidebar" aria-expanded="false">
                    <i data-lucide="menu" aria-hidden="true"></i>
                </button>
                <a href="<?php echo $dashboard_path; ?>" class="mobile-brand">
                    <img src="<?php echo $base_path; ?>assets/logo.png" alt="ThesisTrack Logo">
                    <span>ThesisTrack</span>
                </a>
            </div>
            <button type="button" class="sidebar-overlay" aria-label="Close navigation menu"></button>
            <aside class="sidebar" id="app-sidebar">
                <a href="<?php echo $dashboard_path; ?>" class="sidebar-brand">
                    <img src="<?php echo $base_path; ?>assets/logo.png" alt="ThesisTrack Logo" class="nav-logo">
                    <span>ThesisTrack</span>
                </a>

                <nav class="sidebar-nav">
                    <?php if ($role == 'student'): ?>
                        <div class="nav-section">
                            <p class="nav-section-title">Workspace</p>
                            <a class="nav-item <?php echo nav_class($current_page, 'student_dashboard.php'); ?>" href="<?php echo $base_path; ?>pages/student_dashboard.php">
                                <i data-lucide="layout-dashboard" class="nav-icon" aria-hidden="true"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <div class="nav-section">
                            <p class="nav-section-title">Submissions</p>
                            <a class="nav-item <?php echo nav_class($current_page, ['upload.php', 'edit_submission.php']); ?>" href="<?php echo $base_path; ?>pages/upload.php">
                                <i data-lucide="upload-cloud" class="nav-icon" aria-hidden="true"></i>
                                <span>Upload Thesis</span>
                            </a>
                        </div>
                    <?php elseif ($role == 'supervisor'): ?>
                        <div class="nav-section">
                            <p class="nav-section-title">Review</p>
                            <a class="nav-item <?php echo nav_class($current_page, 'supervisor_dashboard.php'); ?>" href="<?php echo $base_path; ?>pages/supervisor_dashboard.php">
                                <i data-lucide="layout-dashboard" class="nav-icon" aria-hidden="true"></i>
                                <span>Dashboard</span>
                            </a>
                            <a class="nav-item <?php echo nav_class($current_page, 'feedback.php'); ?>" href="<?php echo $base_path; ?>pages/supervisor_dashboard.php#review-queue">
                                <i data-lucide="clipboard-check" class="nav-icon" aria-hidden="true"></i>
                                <span>Review Queue</span>
                            </a>
                        </div>
                        <div class="nav-section">
                            <p class="nav-section-title">Students</p>
                            <a class="nav-item <?php echo nav_class($current_page, ['add_user.php']); ?>" href="<?php echo $base_path; ?>pages/add_user.php">
                                <i data-lucide="user-plus" class="nav-icon" aria-hidden="true"></i>
                                <span>Add Student</span>
                            </a>
                            <a class="nav-item <?php echo nav_class($current_page, ['manage_users.php', 'edit_user.php', 'delete_user.php']); ?>" href="<?php echo $base_path; ?>pages/manage_users.php">
                                <i data-lucide="users" class="nav-icon" aria-hidden="true"></i>
                                <span>Manage Students</span>
                            </a>
                        </div>
                    <?php elseif ($role == 'admin'): ?>
                        <div class="nav-section">
                            <p class="nav-section-title">Administration</p>
                            <a class="nav-item <?php echo nav_class($current_page, 'admin_dashboard.php'); ?>" href="<?php echo $base_path; ?>pages/admin_dashboard.php">
                                <i data-lucide="layout-dashboard" class="nav-icon" aria-hidden="true"></i>
                                <span>Dashboard</span>
                            </a>
                            <a class="nav-item <?php echo nav_class($current_page, 'add_user.php'); ?>" href="<?php echo $base_path; ?>pages/add_user.php">
                                <i data-lucide="user-plus" class="nav-icon" aria-hidden="true"></i>
                                <span>Add User</span>
                            </a>
                            <a class="nav-item <?php echo nav_class($current_page, ['manage_users.php', 'edit_user.php', 'delete_user.php']); ?>" href="<?php echo $base_path; ?>pages/manage_users.php">
                                <i data-lucide="users" class="nav-icon" aria-hidden="true"></i>
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
                    <a href="<?php echo $base_path; ?>logout.php" class="logout-link"><i data-lucide="log-out" class="logout-icon" aria-hidden="true"></i>Logout</a>
                </div>
            </aside>

            <main class="app-main">
    <?php else: ?>
            <main class="auth-main">
    <?php endif; ?>
        <?php
        if (isset($_SESSION['flash_message'])) {
            $type = $_SESSION['flash_type'] ?? 'success';
            echo "<div class='toast-container'><div class='alert alert-$type'>" . htmlspecialchars($_SESSION['flash_message']) . "</div></div>";
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
        }
        ?>
