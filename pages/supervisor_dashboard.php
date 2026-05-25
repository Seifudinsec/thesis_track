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
?>

<div class="dashboard-title-bar">
    <div>
        <h2>Supervisor Control Panel</h2>
        <p style="color: #64748b;">Reviewing student work and managing student accounts.</p>
    </div>
    <a href="add_user.php" class="btn btn-primary">+ Add Student</a>
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
                            <a href="feedback.php?id=<?php echo $sub['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem;">Review Thesis</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($submissions)): ?>
                    <tr><td colspan="4" style="text-align:center; padding: 3rem; color: #64748b;">No submissions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card" id="student-management">
    <h3 style="margin-bottom: 1.5rem;">Student Management</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td data-label="Full Name" style="font-weight: 700;"><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td data-label="Email"><?php echo htmlspecialchars($student['email']); ?></td>
                        <td data-label="Joined Date"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="edit_user.php?id=<?php echo $student['id']; ?>" class="btn btn-outline" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">Edit</a>
                                <a href="delete_user.php?id=<?php echo $student['id']; ?>" class="btn btn-accent" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($students)): ?>
                    <tr><td colspan="4" style="text-align:center; padding: 3rem; color: #64748b;">No student accounts found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
