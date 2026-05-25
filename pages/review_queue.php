<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'supervisor'])) {
    header("Location: ../index.php");
    exit();
}

$role = $_SESSION['role'];
$status_filter = $_GET['status'] ?? 'all';

$query = "SELECT s.*, u.full_name FROM submissions s JOIN users u ON s.student_id = u.id";
$params = [];

if ($status_filter !== 'all') {
    $query .= " WHERE s.status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY s.submitted_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$submissions = $stmt->fetchAll();
?>

<div class="dashboard-title-bar">
    <div>
        <h2>Thesis Review Queue</h2>
        <p style="color: var(--text-muted);">Monitor and evaluate student submissions across the system.</p>
    </div>
    <div class="filter-buttons" style="display: flex; gap: 10px;">
        <a href="review_queue.php?status=all" class="btn <?php echo $status_filter === 'all' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">All</a>
        <a href="review_queue.php?status=pending" class="btn <?php echo $status_filter === 'pending' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Pending</a>
        <a href="review_queue.php?status=under_review" class="btn <?php echo $status_filter === 'under_review' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Under Review</a>
        <a href="review_queue.php?status=approved" class="btn <?php echo $status_filter === 'approved' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Approved</a>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Thesis Title</th>
                    <th>Submitted On</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $sub): ?>
                    <tr>
                        <td data-label="Student"><div style="font-weight: 700;"><?php echo htmlspecialchars($sub['full_name']); ?></div></td>
                        <td data-label="Title"><?php echo htmlspecialchars($sub['title']); ?></td>
                        <td data-label="Date"><?php echo date('M d, Y', strtotime($sub['submitted_at'])); ?></td>
                        <td data-label="Status">
                            <span class="badge badge-<?php echo $sub['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $sub['status'])); ?>
                            </span>
                        </td>
                        <td data-label="Action">
                            <a href="feedback.php?id=<?php echo $sub['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                                <i data-lucide="clipboard-check" class="btn-icon" aria-hidden="true"></i>Review
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($submissions)): ?>
                    <tr><td colspan="5" style="text-align:center; padding: 3rem; color: var(--text-muted);">No submissions found matching the criteria.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
