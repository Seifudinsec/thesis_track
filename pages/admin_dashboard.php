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

$role_counts = $pdo->query("SELECT role, COUNT(*) AS total FROM users GROUP BY role")->fetchAll(PDO::FETCH_KEY_PAIR);
$status_counts = $pdo->query("SELECT status, COUNT(*) AS total FROM submissions GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$recent_submissions = $pdo->query(
    "SELECT s.title, s.status, s.submitted_at, u.full_name
     FROM submissions s
     JOIN users u ON s.student_id = u.id
     ORDER BY s.submitted_at DESC
     LIMIT 6"
)->fetchAll();

$student_count = $role_counts['student'] ?? 0;
$supervisor_count = $role_counts['supervisor'] ?? 0;
$admin_count = $role_counts['admin'] ?? 0;
$approved_count = $status_counts['approved'] ?? 0;
$under_review_count = $status_counts['under_review'] ?? 0;
$rejected_count = $status_counts['rejected'] ?? 0;
?>

<div class="dashboard-title-bar">
    <div>
        <h2>System Administration</h2>
        <p style="color: var(--text-muted);">Monitoring system health and user oversight.</p>
    </div>
    <div class="action-buttons">
        <a href="add_user.php" class="btn btn-primary"><i data-lucide="user-plus" class="btn-icon" aria-hidden="true"></i>Add New User</a>
        <a href="manage_users.php" class="btn btn-outline"><i data-lucide="users" class="btn-icon" aria-hidden="true"></i>Manage Users</a>
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

<div class="stats-grid">
    <div class="card">
        <h3 style="margin-bottom: 1rem;">User Roles</h3>
        <div class="metric-list">
            <div><span>Students</span><strong><?php echo $student_count; ?></strong></div>
            <div><span>Supervisors</span><strong><?php echo $supervisor_count; ?></strong></div>
            <div><span>Admins</span><strong><?php echo $admin_count; ?></strong></div>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 1rem;">Submission Status</h3>
        <div class="metric-list">
            <div><span>Pending</span><strong><?php echo $p_count; ?></strong></div>
            <div><span>Under Review</span><strong><?php echo $under_review_count; ?></strong></div>
            <div><span>Approved</span><strong><?php echo $approved_count; ?></strong></div>
            <div><span>Rejected</span><strong><?php echo $rejected_count; ?></strong></div>
        </div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 1.5rem;">Recent Submissions</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Thesis Title</th>
                    <th>Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_submissions as $submission): ?>
                    <tr>
                        <td data-label="Student" style="font-weight: 700;"><?php echo htmlspecialchars($submission['full_name']); ?></td>
                        <td data-label="Thesis Title"><?php echo htmlspecialchars($submission['title']); ?></td>
                        <td data-label="Status">
                            <span class="badge badge-<?php echo $submission['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $submission['status'])); ?>
                            </span>
                        </td>
                        <td data-label="Submitted"><?php echo date('M d, Y', strtotime($submission['submitted_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recent_submissions)): ?>
                    <tr><td colspan="4" style="text-align:center; padding: 3rem; color: var(--text-muted);">No submissions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
