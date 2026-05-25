<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

// Access Control - Allow both Admin and Supervisor
if (!isset($_SESSION["user_id"]) || !in_array($_SESSION["role"], ["admin", "supervisor"])) {
    header("Location: ../index.php");
    exit();
}

$submission_id = $_GET["id"] ?? null;
if (!$submission_id) {
    header("Location: review_queue.php");
    exit();
}

// Fetch Submission Details
$stmt = $pdo->prepare(
    "SELECT s.*, u.full_name as student_name FROM submissions s JOIN users u ON s.student_id = u.id WHERE s.id = ?",
);
$stmt->execute([$submission_id]);
$submission = $stmt->fetch();

if (!$submission) {
    header("Location: review_queue.php");
    exit();
}

// Fetch Existing Feedback (if any)
$stmt = $pdo->prepare("SELECT * FROM feedback WHERE submission_id = ?");
$stmt->execute([$submission_id]);
$existing_feedback = $stmt->fetch();

// Handle Feedback Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_feedback"])) {
    $comments = $_POST["comments"];
    $grade = $_POST["grade"];
    $status = $_POST["status"];
    $reviewer_id = $_SESSION["user_id"];

    try {
        $pdo->beginTransaction();

        // 1. Update Submission Status
        $stmt = $pdo->prepare("UPDATE submissions SET status = ? WHERE id = ?");
        $stmt->execute([$status, $submission_id]);

        // 2. Insert or Update Feedback
        if ($existing_feedback) {
            $stmt = $pdo->prepare(
                "UPDATE feedback SET comments = ?, grade = ?, supervisor_id = ? WHERE submission_id = ?",
            );
            $stmt->execute([$comments, $grade, $reviewer_id, $submission_id]);
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO feedback (submission_id, supervisor_id, comments, grade) VALUES (?, ?, ?, ?)",
            );
            $stmt->execute([$submission_id, $reviewer_id, $comments, $grade]);
        }

        $pdo->commit();
        $_SESSION["flash_message"] = "Review submitted successfully!";
        $_SESSION["flash_type"] = "success";
        
        // Redirect back to review queue
        header("Location: review_queue.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION["flash_message"] = "Error: " . $e->getMessage();
        $_SESSION["flash_type"] = "error";
    }
}
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="review_queue.php" style="text-decoration: none; color: var(--primary); display: flex; align-items: center; gap: 5px;">
            <i data-lucide="arrow-left" size="18"></i> Back to Review Queue
        </a>
    </div>

    <h2>Reviewing Thesis: <?php echo htmlspecialchars($submission["title"]); ?></h2>
    <p>Student: <strong><?php echo htmlspecialchars($submission["student_name"]); ?></strong></p>

    <div class="card" style="margin-top: 2rem;">
        <h3>Submission Details</h3>
        <p>Submitted on: <?php echo date("M d, Y", strtotime($submission["submitted_at"])); ?></p>
        <div style="display: flex; gap: 10px; margin-top: 1rem;">
            <a href="../<?php echo $submission["file_path"]; ?>" target="_blank" class="btn btn-outline"><i data-lucide="eye" class="btn-icon" aria-hidden="true"></i>View PDF</a>
            <a href="../<?php echo $submission["file_path"]; ?>" download class="btn btn-action"><i data-lucide="download" class="btn-icon" aria-hidden="true"></i>Download PDF</a>
        </div>
    </div>

    <div class="form-container" style="max-width: 100%; margin-top: 2rem;">
        <h3>Provide Feedback</h3>
        <form action="feedback.php?id=<?php echo $submission_id; ?>" method="POST">
            <div class="form-group">
                <label for="status">Update Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="pending" <?php echo $submission["status"] == "pending" ? "selected" : ""; ?>>Pending</option>
                    <option value="under_review" <?php echo $submission["status"] == "under_review" ? "selected" : ""; ?>>Under Review</option>
                    <option value="approved" <?php echo $submission["status"] == "approved" ? "selected" : ""; ?>>Approved</option>
                    <option value="rejected" <?php echo $submission["status"] == "rejected" ? "selected" : ""; ?>>Rejected</option>
                </select>
            </div>

            <div class="form-group">
                <label for="grade">Grade (Optional)</label>
                <input type="text" name="grade" id="grade" class="form-control" value="<?php echo $existing_feedback["grade"] ?? ""; ?>" placeholder="e.g. A, B+, 85%">
            </div>

            <div class="form-group">
                <label for="comments">Comments / Suggestions</label>
                <textarea name="comments" id="comments" class="form-control" rows="6" required placeholder="Provide detailed feedback..."><?php echo $existing_feedback["comments"] ?? ""; ?></textarea>
            </div>

            <button type="submit" name="submit_feedback" class="btn btn-primary" style="width: 100%;"><i data-lucide="save" class="btn-icon" aria-hidden="true"></i>Save Review</button>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
