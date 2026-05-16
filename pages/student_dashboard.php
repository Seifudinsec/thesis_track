<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$query = "SELECT s.id, s.title, s.status, s.file_path, s.submitted_at, f.comments, f.grade 
          FROM submissions s 
          LEFT JOIN feedback f ON s.id = f.submission_id 
          WHERE s.student_id = ? 
          ORDER BY s.submitted_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$student_id]);
$submissions = $stmt->fetchAll();

$approved_count = count(array_filter($submissions, fn($s) => $s['status'] == 'approved'));
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
    <a href="upload.php" class="btn btn-primary">+ New Submission</a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h4>Total Submissions</h4>
        <div class="value"><?php echo count($submissions); ?></div>
    </div>
    <div class="stat-card" style="border-left-color: #10b981;">
        <h4>Approved</h4>
        <div class="value"><?php echo $approved_count; ?></div>
    </div>
    <div class="stat-card" style="border-left-color: #e65a5a;">
        <h4>Rejected</h4>
        <div class="value"><?php echo count(array_filter($submissions, fn($s) => $s['status'] == 'rejected')); ?></div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 1.5rem;">Your Academic Progress</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Thesis Title</th>
                    <th>Status</th>
                    <th>Grade</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $sub): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700;"><?php echo htmlspecialchars($sub['title']); ?></div>
                            <small style="color: #64748b;"><?php echo date('M d, Y', strtotime($sub['submitted_at'])); ?></small>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $sub['status']; ?>">
                                <?php echo ucfirst($sub['status']); ?>
                            </span>
                        </td>
                        <td><strong style="color: var(--primary);"><?php echo $sub['grade'] ?: '--'; ?></strong></td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="../<?php echo $sub['file_path']; ?>" target="_blank" class="btn btn-outline" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">View</a>
                                <?php if ($sub['status'] == 'pending'): ?>
                                    <a href="edit_submission.php?id=<?php echo $sub['id']; ?>" class="btn btn-primary" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">Edit</a>
                                    <a href="delete_submission.php?id=<?php echo $sub['id']; ?>" class="btn btn-accent" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;" onclick="return confirm('Delete this draft?')">Delete</a>
                                <?php endif; ?>
                            </div>
                            <?php if ($sub['comments']): ?>
                                <div class="feedback-content">
                                    <small><strong>Supervisor says:</strong><br><?php echo nl2br(htmlspecialchars($sub['comments'])); ?></small>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($submissions)): ?>
                    <tr><td colspan="4" style="text-align:center; padding: 3rem; color: #64748b;">No submissions found. Start by uploading your thesis!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
