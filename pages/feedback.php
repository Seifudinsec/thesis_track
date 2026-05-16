<?php
require_once "../config/db.php";
require_once "../includes/header.php";

// Access Control
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "supervisor") {
    header("Location: ../index.php");
    exit();
}

$submission_id = $_GET["id"] ?? null;
if (!$submission_id) {
    header("Location: supervisor_dashboard.php");
    exit();
}

// Fetch Submission Details
$stmt = $pdo->prepare(
    "SELECT s.*, u.full_name as student_name FROM submissions s JOIN users u ON s.student_id = u.id WHERE s.id = ?",
);
$stmt->execute([$submission_id]);
$submission = $stmt->fetch();

if (!$submission) {
    header("Location: supervisor_dashboard.php");
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
    $supervisor_id = $_SESSION["user_id"];

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
            $stmt->execute([$comments, $grade, $supervisor_id, $submission_id]);
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO feedback (submission_id, supervisor_id, comments, grade) VALUES (?, ?, ?, ?)",
            );
            $stmt->execute([$submission_id, $supervisor_id, $comments, $grade]);
        }

        $pdo->commit();
        $_SESSION["flash_message"] = "Review submitted successfully!";
        $_SESSION["flash_type"] = "success";
        header("Location: supervisor_dashboard.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION["flash_message"] = "Error: " . $e->getMessage();
        $_SESSION["flash_type"] = "error";
    }
}
?>

<div style="max-width: 800px; margin: 0 auto;">
    <h2>Reviewing Thesis: <?php echo htmlspecialchars(
        $submission["title"],
    ); ?></h2>
    <p>Student: <strong><?php echo htmlspecialchars(
        $submission["student_name"],
    ); ?></strong></p>

    <div class="card" style="margin-top: 2rem;">
        <h3>Submission Details</h3>
        <p>Submitted on: <?php echo date(
            "M d, Y",
            strtotime($submission["submitted_at"]),
        ); ?></p>
        <a href="../<?php echo $submission[
            "file_path"
        ]; ?>" target="_blank" class="btn btn-action" style="margin-top: 1rem; display: inline-block;">Download & Review PDF</a>
    </div>

    <div class="form-container" style="max-width: 100%; margin-top: 2rem;">
        <h3>Provide Feedback</h3>
        <form action="feedback.php?id=<?php echo $submission_id; ?>" method="POST">
            <div class="form-group">
                <label for="status">Update Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="under_review" <?php echo $submission[
                        "status"
                    ] == "under_review"
                        ? "selected"
                        : ""; ?>>Under Review</option>
                    <option value="approved" <?php echo $submission["status"] ==
                    "approved"
                        ? "selected"
                        : ""; ?>>Approved</option>
                    <option value="rejected" <?php echo $submission["status"] ==
                    "rejected"
                        ? "selected"
                        : ""; ?>>Rejected</option>
                </select>
            </div>

            <div class="form-group">
                <label for="grade">Grade (Optional)</label>
                <input type="text" name="grade" id="grade" class="form-control" value="<?php echo $existing_feedback[
                    "grade"
                ] ?? ""; ?>" placeholder="e.g. A, B+, 85%">
            </div>

            <div class="form-group">
                <label for="comments">Comments / Suggestions</label>
                <textarea name="comments" id="comments" class="form-control" rows="6" required placeholder="Provide detailed feedback..."><?php echo $existing_feedback[
                    "comments"
                ] ?? ""; ?></textarea>
            </div>

            <button type="submit" name="submit_feedback" class="btn btn-primary" style="width: 100%;">Save Review</button>
        </form>
    </div>
</div>

<!-- <section class="student-explanation" style="margin-top: 3rem; background: #eee; padding: 1.5rem; border-radius: 8px;">
    <h3>🎓 Student Explanation: feedback.php</h3>
    <p><strong>Logic Flow:</strong> This page performs a <strong>Database Transaction</strong>. This means it updates the status in the <code>submissions</code> table AND inserts/updates the <code>feedback</code> table simultaneously. If one fails, both are cancelled to keep data consistent.</p>
    <p><strong>CRUD Operation:</strong> This is an <strong>Update (U)</strong> and <strong>Create (C)</strong> operation combined. We are updating the status of an existing record and creating/updating a feedback record.</p>
    <p><strong>Security Measures:</strong>
        <ul>
            <li><strong>Atomic Transactions:</strong> <code>$pdo->beginTransaction()</code> ensures that we don't end up with an "Approved" thesis that has no feedback recorded.</li>
            <li><strong>GET Validation:</strong> We check for <code>$_GET['id']</code> and verify it exists in the database. This prevents errors if someone tries to visit the page with a fake ID.</li>
        </ul>
    </p>
</section> -->

<?php require_once "../includes/footer.php"; ?>
