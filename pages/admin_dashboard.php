<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$u_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$s_count = $pdo->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
$p_count = $pdo->query("SELECT COUNT(*) FROM submissions WHERE status = 'pending'")->fetchColumn();
?>

<div class="dashboard-title-bar">
    <div>
        <h2>System Administration</h2>
        <p style="color: #64748b;">Monitoring system health and user oversight.</p>
    </div>
    <div class="action-buttons">
        <a href="add_user.php" class="btn btn-primary">+ Add New User</a>
        <a href="manage_users.php" class="btn btn-outline">Manage Users</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card" style="border-left-color: var(--primary);">
        <h4>Total Users</h4>
        <div class="value"><?php echo $u_count; ?></div>
    </div>
    <div class="stat-card" style="border-left-color: var(--accent);">
        <h4>Total Submissions</h4>
        <div class="value"><?php echo $s_count; ?></div>
    </div>
    <div class="stat-card" style="border-left-color: var(--highlight);">
        <h4>Pending Reviews</h4>
        <div class="value"><?php echo $p_count; ?></div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
