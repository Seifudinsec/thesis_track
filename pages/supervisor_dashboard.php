<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php");
    exit();
}
?>

<div style="background: white; padding: 2rem; border-radius: 8px;">
    <h1>Supervisor Control Panel</h1>
    <p>Logged in as: <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>

    <div style="margin-top: 2rem;">
        <h3>Submissions Pending Review</h3>
        <?php
        $stmt = $pdo->query("SELECT s.*, u.full_name FROM submissions s JOIN users u ON s.student_id = u.id");
        $subs = $stmt->fetchAll();
        
        if (count($subs) > 0) {
            echo "<table style='width:100%; border-collapse: collapse;'>";
            echo "<tr><th>Student</th><th>Title</th><th>Status</th><th>Action</th></tr>";
            foreach ($subs as $s) {
                echo "<tr>";
                echo "<td>".htmlspecialchars($s['full_name'])."</td>";
                echo "<td>".htmlspecialchars($s['title'])."</td>";
                echo "<td>".$s['status']."</td>";
                echo "<td><a href='feedback.php?id=".$s['id']."'>Review</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No submissions to review yet.</p>";
        }
        ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
