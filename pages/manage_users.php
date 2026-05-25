<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'supervisor'], true)) {
    header("Location: ../index.php");
    exit();
}

$is_admin = $_SESSION['role'] === 'admin';
$page_title = $is_admin ? 'Manage Users' : 'Manage Students';
$page_description = $is_admin
    ? 'Create, update, and remove system users.'
    : 'Create, update, and remove student accounts.';

if ($is_admin) {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY role DESC, full_name ASC");
} else {
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'student' ORDER BY full_name ASC");
}

$users = $stmt->fetchAll();
?>

<div class="dashboard-title-bar">
    <div>
        <h2><?php echo $page_title; ?></h2>
        <p style="color: #64748b;"><?php echo $page_description; ?></p>
    </div>
    <a href="add_user.php" class="btn btn-primary"><?php echo $is_admin ? '+ Add User' : '+ Add Student'; ?></a>
</div>

<div class="card">
    <h3 style="margin-bottom: 1.5rem;"><?php echo $page_title; ?></h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <?php if ($is_admin): ?>
                        <th>Role</th>
                    <?php endif; ?>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td data-label="Full Name" style="font-weight: 700;"><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td data-label="Email"><?php echo htmlspecialchars($user['email']); ?></td>
                        <?php if ($is_admin): ?>
                            <td data-label="Role">
                                <span style="font-weight: 800; color: <?php echo $user['role'] == 'admin' ? 'var(--accent)' : 'var(--primary)'; ?>; text-transform: uppercase; font-size: 0.75rem;">
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                            </td>
                        <?php endif; ?>
                        <td data-label="Joined Date"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-outline" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">Edit</a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-accent" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="<?php echo $is_admin ? '5' : '4'; ?>" style="text-align:center; padding: 3rem; color: #64748b;">
                            <?php echo $is_admin ? 'No users found.' : 'No student accounts found.'; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
