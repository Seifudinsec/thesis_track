<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php");
    exit();
}

$stmt = $pdo->query("SELECT s.*, u.full_name FROM submissions s JOIN users u ON s.student_id = u.id ORDER BY s.submitted_at DESC");
$submissions = $stmt->fetchAll();
?>

<div style="margin-bottom: 2rem;">
    <h2>Supervisor Control Panel</h2>
    <p style="color: #64748b;">Reviewing student work and providing academic guidance.</p>
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
</div>

<div class="card">
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
                        <td><div style="font-weight: 700;"><?php echo htmlspecialchars($sub['full_name']); ?></div></td>
                        <td><?php echo htmlspecialchars($sub['title']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $sub['status']; ?>">
                                <?php echo ucfirst($sub['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="feedback.php?id=<?php echo $sub['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem;">Review Thesis</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
