<?php
require_once __DIR__ . '/../config/db.php';

// Ensure table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS deadlines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    deadline_date DATETIME NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM deadlines ORDER BY deadline_date ASC");
$deadlines = $stmt->fetchAll();
?>

<div class="dashboard-title-bar">
    <div>
        <h2>Manage Academic Deadlines</h2>
        <p style="color: var(--text-muted);">Set and oversee important milestones for the academic year.</p>
    </div>
    <a href="add_deadline.php" class="btn btn-primary"><i data-lucide="calendar-plus" class="btn-icon" aria-hidden="true"></i>Add New Deadline</a>
</div>

<div class="card">
    <h3 style="margin-bottom: 1.5rem;">Academic Milestones</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Milestone Title</th>
                    <th>Deadline Date</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deadlines as $deadline): ?>
                    <tr>
                        <td data-label="Title" style="font-weight: 700;"><?php echo htmlspecialchars($deadline['title']); ?></td>
                        <td data-label="Date">
                            <span style="color: var(--primary); font-weight: 600;">
                                <?php echo date('M d, Y | H:i', strtotime($deadline['deadline_date'])); ?>
                            </span>
                        </td>
                        <td data-label="Description">
                            <small><?php echo htmlspecialchars($deadline['description'] ?: 'No description provided.'); ?></small>
                        </td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="add_deadline.php?id=<?php echo $deadline['id']; ?>" class="btn btn-outline" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;"><i data-lucide="pencil" class="btn-icon" aria-hidden="true"></i>Edit</a>
                                <a href="delete_deadline.php?id=<?php echo $deadline['id']; ?>" class="btn btn-accent" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this deadline?')"><i data-lucide="trash-2" class="btn-icon" aria-hidden="true"></i>Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($deadlines)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 3rem; color: var(--text-muted);">
                            No deadlines set. Start by adding an academic milestone!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
