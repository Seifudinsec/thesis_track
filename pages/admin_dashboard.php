<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$u_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$s_count = $pdo->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
$p_count = $pdo->query("SELECT COUNT(*) FROM submissions WHERE status = 'pending'")->fetchColumn();
$users = $pdo->query("SELECT * FROM users ORDER BY role DESC")->fetchAll();
?>

<h2>System Administration</h2>
<p style="margin-bottom: 2rem; color: #64748b;">Monitoring system health and user oversight.</p>

<div class="stats-grid">
    <div class="stat-card" style="border-left-color: var(--primary);">
        <h4>Total Users</h4>
        <div class="value"><?php echo $u_count; ?></div>
    </div>
    <div class="stat-card" style="border-left-color: var(--accent);">
        <h4>Total Submissions</h4>
        <div class="value"><?php echo $s_count; ?></div>
    </div>
    <div class="stat-card" style="border-left-color: var(--highlight);">
        <h4>Pending Reviews</h4>
        <div class="value"><?php echo $p_count; ?></div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 1.5rem;">User Management</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td style="font-weight: 700;"><?php echo htmlspecialchars($u['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <span style="font-weight: 800; color: <?php echo $u['role'] == 'admin' ? 'var(--accent)' : 'var(--primary)'; ?>; text-transform: uppercase; font-size: 0.75rem;">
                                <?php echo $u['role']; ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
