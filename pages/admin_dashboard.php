<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<div style="background: white; padding: 2rem; border-radius: 8px;">
    <h1>Admin Overview</h1>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1rem;">
        <div class="card">
            <h4>Total Users</h4>
            <?php echo $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(); ?>
        </div>
        <div class="card">
            <h4>Submissions</h4>
            <?php echo $pdo->query("SELECT COUNT(*) FROM submissions")->fetchColumn(); ?>
        </div>
        <div class="card">
            <h4>System Status</h4>
            <span style="color: green;">Online</span>
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <h3>User List</h3>
        <?php
        $users = $pdo->query("SELECT * FROM users")->fetchAll();
        foreach ($users as $u) {
            echo "<p>".htmlspecialchars($u['full_name'])." (".$u['role'].") - ".$u['email']."</p>";
        }
        ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
