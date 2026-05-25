<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php");
    exit();
}

$stmt = $pdo->query("SELECT s.*, u.full_name FROM submissions s JOIN users u ON s.student_id = u.id ORDER BY s.submitted_at DESC");
$submissions = $stmt->fetchAll();
$students = $pdo->query("SELECT * FROM users WHERE role = 'student' ORDER BY full_name ASC")->fetchAll();

// Fetch upcoming deadlines
$deadlines = $pdo->query("SELECT * FROM deadlines WHERE deadline_date >= NOW() ORDER BY deadline_date ASC LIMIT 3")->fetchAll();
?>

<div class="dashboard-title-bar">
    <div>
        <h2>Supervisor Control Panel</h2>
        <p style="color: var(--text-muted);">Reviewing student work and managing student accounts.</p>
    </div>
    <div class="action-buttons">
        <a href="add_user.php" class="btn btn-primary"><i data-lucide="user-plus" class="btn-icon" aria-hidden="true"></i>Add Student</a>
        <a href="manage_users.php" class="btn btn-outline"><i data-lucide="users" class="btn-icon" aria-hidden="true"></i>Manage Students</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h4>Pending Reviews</h4>
        <div class="value"><?php echo count(array_filter($submissions, fn($s) => $s['status'] == 'pending')); ?></div>
    </div>
    <div class="stat-card" style="border-left-color: var(--secondary);">
        <h4>Total Handled</h4>
        <div class="value"><?php echo count($submissions); ?></div>
    </div>
    <div class="stat-card" style="border-left-color: var(--accent);">
        <h4>Students</h4>
        <div class="value"><?php echo count($students); ?></div>
    </div>
</div>

<?php if (!empty($deadlines)): ?>
<div class="card" id="deadlines" style="margin-bottom: 2rem; border-left: 4px solid #f59e0b;">
    <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="bell" style="color: #f59e0b;"></i>
        Upcoming Deadlines & Milestones
    </h3>
    <div class="metric-list">
        <?php foreach ($deadlines as $d): ?>
            <div style="padding: 1rem; background: #fff; border-radius: 8px; margin-bottom: 0.5rem; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="display: block; font-size: 1.1rem; color: var(--primary);"><?php echo htmlspecialchars($d['title']); ?></strong>
                    <small style="color: var(--text-muted);"><?php echo htmlspecialchars($d['description']); ?></small>
                </div>
                <div style="text-align: right;">
                    <span style="display: block; font-weight: 700; color: #e65a5a;"><?php echo date('M d, Y', strtotime($d['deadline_date'])); ?></span>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Due at <?php echo date('H:i', strtotime($d['deadline_date'])); ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="card" id="review-queue">
    <h3 style="margin-bottom: 1.5rem;">Recent Submissions</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Thesis Title</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $sub): ?>
                    <tr>
                        <td data-label="Student Name"><div style="font-weight: 700;"><?php echo htmlspecialchars($sub['full_name']); ?></div></td>
                        <td data-label="Thesis Title"><?php echo htmlspecialchars($sub['title']); ?></td>
                        <td data-label="Status">
                            <span class="badge badge-<?php echo $sub['status']; ?>">
                                <?php echo ucfirst($sub['status']); ?>
                            </span>
                        </td>
                        <td data-label="Action">
                            <a href="feedback.php?id=<?php echo $sub['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem;"><i data-lucide="clipboard-check" class="btn-icon" aria-hidden="true"></i>Review Thesis</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($submissions)): ?>
                    <tr><td colspan="4" style="text-align:center; padding: 3rem; color: var(--text-muted);">No submissions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
