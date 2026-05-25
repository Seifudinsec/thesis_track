<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    $title = $_POST['title'];
    $student_id = $_SESSION['user_id'];
    
    // Check for PHP upload errors
    if ($_FILES['thesis_file']['error'] !== UPLOAD_ERR_OK) {
        $error_codes = [
            1 => 'File exceeds upload_max_filesize',
            2 => 'File exceeds MAX_FILE_SIZE',
            3 => 'File only partially uploaded',
            4 => 'No file uploaded',
            6 => 'Missing temporary folder',
            7 => 'Failed to write file to disk',
            8 => 'A PHP extension stopped the file upload'
        ];
        $_SESSION['flash_message'] = "System Error: " . ($error_codes[$_FILES['thesis_file']['error']] ?? 'Unknown error');
        $_SESSION['flash_type'] = "error";
    } else {
        $target_dir = __DIR__ . "/../uploads/";
        $file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["thesis_file"]["name"]));
        $target_file = $target_dir . $file_name;
        $db_file_path = "uploads/" . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $upload_ok = true;
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
                $stmt = $pdo->prepare("INSERT INTO submissions (student_id, title, file_path) VALUES (?, ?, ?)");
                if ($stmt->execute([$student_id, $title, $db_file_path])) {
                    $_SESSION['flash_message'] = "Thesis uploaded successfully!";
                    $_SESSION['flash_type'] = "success";
                    header("Location: student_dashboard.php");
                    exit();
                }
            } else {
                $_SESSION['flash_message'] = "Permission Denied: Could not save file to the uploads folder.";
                $_SESSION['flash_type'] = "error";
            }
        }
    }
}
?>

<div class="form-container">
    <h2>Submit New Thesis</h2>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Thesis Title</label>
            <input type="text" name="title" id="title" class="form-control" required placeholder="e.g. AI in Agriculture">
        </div>
        <div class="form-group">
            <label for="thesis_file">Select PDF (Max 5MB)</label>
            <input type="file" name="thesis_file" id="thesis_file" class="form-control" required accept=".pdf">
        </div>
        <button type="submit" name="upload" class="btn btn-primary" style="width: 100%;"><i data-lucide="upload-cloud" class="btn-icon" aria-hidden="true"></i>Submit Thesis</button>
        <a href="student_dashboard.php" style="display: block; text-align: center; margin-top: 1rem; color: var(--text-muted); text-decoration: none;">Back to Dashboard</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
