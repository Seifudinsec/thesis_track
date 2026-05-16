<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$submission_id = $_GET['id'] ?? null;
$student_id = $_SESSION['user_id'];

// Verify submission exists and belongs to the student AND is still pending
$stmt = $pdo->prepare("SELECT * FROM submissions WHERE id = ? AND student_id = ?");
$stmt->execute([$submission_id, $student_id]);
$submission = $stmt->fetch();

if (!$submission || $submission['status'] !== 'pending') {
    $_SESSION['flash_message'] = "Cannot edit this submission.";
    $_SESSION['flash_type'] = "error";
    header("Location: student_dashboard.php");
    exit();
}

// Update Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $title = $_POST['title'];
    $update_file = !empty($_FILES['thesis_file']['name']);
    $upload_ok = true;
    $db_file_path = $submission['file_path'];

    if ($update_file) {
        $target_dir = "../uploads/";
        $file_name = time() . "_" . basename($_FILES["thesis_file"]["name"]);
        $target_file = $target_dir . $file_name;
        $db_file_path = "uploads/" . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($file_type != "pdf") {
            $_SESSION['flash_message'] = "Only PDF files are allowed.";
            $_SESSION['flash_type'] = "error";
            $upload_ok = false;
        }

        if ($_FILES["thesis_file"]["size"] > 5000000) {
            $_SESSION['flash_message'] = "File too large (Max 5MB).";
            $_SESSION['flash_type'] = "error";
            $upload_ok = false;
        }

        if ($upload_ok) {
            if (move_uploaded_file($_FILES["thesis_file"]["tmp_name"], $target_file)) {
                // Delete old file
                if (file_exists("../" . $submission['file_path'])) {
                    unlink("../" . $submission['file_path']);
                }
            } else {
                $upload_ok = false;
            }
        }
    }

    if ($upload_ok) {
        $stmt = $pdo->prepare("UPDATE submissions SET title = ?, file_path = ? WHERE id = ?");
        if ($stmt->execute([$title, $db_file_path, $submission_id])) {
            $_SESSION['flash_message'] = "Submission updated successfully!";
            $_SESSION['flash_type'] = "success";
            header("Location: student_dashboard.php");
            exit();
        }
    }
}
?>

<div class="form-container">
    <h2>Edit Submission</h2>
    <p>You can update the title or replace the PDF file.</p>

    <form action="edit_submission.php?id=<?php echo $submission_id; ?>" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem;">
        <div class="form-group">
            <label for="title">Thesis Title</label>
            <input type="text" name="title" id="title" class="form-control" required value="<?php echo htmlspecialchars($submission['title']); ?>">
        </div>

        <div class="form-group">
            <label for="thesis_file">Replace PDF File (Optional)</label>
            <input type="file" name="thesis_file" id="thesis_file" class="form-control" accept=".pdf">
            <small style="color: #666;">Leave blank to keep the current file.</small>
        </div>

        <button type="submit" name="update" class="btn btn-primary" style="width: 100%;">Save Changes</button>
        <a href="student_dashboard.php" style="display: block; text-align: center; margin-top: 1rem; color: #666; text-decoration: none;">Cancel</a>
    </form>
</div>

<!-- <section class="student-explanation" style="margin-top: 3rem; background: #eee; padding: 1.5rem; border-radius: 8px;"> -->
    <!-- <h3>🎓 Student Explanation: edit_submission.php</h3> -->
    <!-- <p><strong>CRUD Operation:</strong> This is the <strong>Update (U)</strong> phase. We are modifying an existing record.</p> -->
    <!-- <p><strong>Logic Flow:</strong> -->
        <!-- <ul> -->
            <!-- <li>We check if a new file is uploaded. If yes, we upload it and delete the old one using <code>unlink()</code> to save server space.</li> -->
            <!-- <li>If no file is uploaded, we only update the title in the database.</li> -->
        <!-- </ul> -->
    <!-- </p> -->
    <!-- <p><strong>Security:</strong> Notice the <code>WHERE id = ? AND student_id = ?</code>. This prevents one student from "guessing" another student's ID and editing their work (IDOR prevention).</p> -->
<!-- </section> -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
