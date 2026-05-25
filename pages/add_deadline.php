<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'] ?? null;
$deadline = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM deadlines WHERE id = ?");
    $stmt->execute([$id]);
    $deadline = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_deadline'])) {
    $title = $_POST['title'];
    $date = $_POST['deadline_date'];
    $description = $_POST['description'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE deadlines SET title = ?, deadline_date = ?, description = ? WHERE id = ?");
        $success = $stmt->execute([$title, $date, $description, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO deadlines (title, deadline_date, description) VALUES (?, ?, ?)");
        $success = $stmt->execute([$title, $date, $description]);
    }

    if ($success) {
        $_SESSION['flash_message'] = $id ? "Deadline updated successfully!" : "Deadline added successfully!";
        $_SESSION['flash_type'] = "success";
        header("Location: manage_deadlines.php");
        exit();
    } else {
        $_SESSION['flash_message'] = "Error saving deadline.";
        $_SESSION['flash_type'] = "error";
    }
}
?>

<div class="form-container">
    <h2><?php echo $id ? 'Edit Deadline' : 'Add New Deadline'; ?></h2>
    <form action="add_deadline.php<?php echo $id ? '?id=' . $id : ''; ?>" method="POST">
        <div class="form-group">
            <label for="title">Milestone Title</label>
            <input type="text" name="title" id="title" class="form-control" required placeholder="e.g. Final Thesis Submission" value="<?php echo htmlspecialchars($deadline['title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="deadline_date">Deadline Date & Time</label>
            <input type="datetime-local" name="deadline_date" id="deadline_date" class="form-control" required value="<?php echo $deadline ? date('Y-m-d\TH:i', strtotime($deadline['deadline_date'])) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="description">Description (Optional)</label>
            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Briefly describe this milestone..."><?php echo htmlspecialchars($deadline['description'] ?? ''); ?></textarea>
        </div>
        
        <button type="submit" name="save_deadline" class="btn btn-primary" style="width: 100%;">
            <i data-lucide="save" class="btn-icon" aria-hidden="true"></i>
            <?php echo $id ? 'Update Milestone' : 'Create Milestone'; ?>
        </button>
        <a href="manage_deadlines.php" style="display: block; text-align: center; margin-top: 1rem; color: var(--text-muted); text-decoration: none;">Back to Management</a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
