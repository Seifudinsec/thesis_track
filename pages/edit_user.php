<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_to_edit = $stmt->fetch();

if (!$user_to_edit) {
    $_SESSION['flash_message'] = "Error: User not found.";
    $_SESSION['flash_type'] = "error";
    header("Location: admin_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $new_password = $_POST['password'];

    // Check if email exists for other users
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['flash_message'] = "Error: Email already exists for another user.";
        $_SESSION['flash_type'] = "error";
    } else {
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, password = ?, role = ? WHERE id = ?");
            $success = $stmt->execute([$full_name, $email, $hashed_password, $role, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE id = ?");
            $success = $stmt->execute([$full_name, $email, $role, $user_id]);
        }

        if ($success) {
            $_SESSION['flash_message'] = "User updated successfully!";
            $_SESSION['flash_type'] = "success";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['flash_message'] = "Error: Could not update user.";
            $_SESSION['flash_type'] = "error";
        }
    }
}
?>

<div class="form-container">
    <h2>Edit User</h2>
    <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" class="form-control" required value="<?php echo htmlspecialchars($user_to_edit['full_name']); ?>">
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" required value="<?php echo htmlspecialchars($user_to_edit['email']); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password (Leave blank to keep current)</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••">
        </div>
        <div class="form-group">
            <label for="role">User Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="student" <?php echo $user_to_edit['role'] == 'student' ? 'selected' : ''; ?>>Student</option>
                <option value="supervisor" <?php echo $user_to_edit['role'] == 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                <option value="admin" <?php echo $user_to_edit['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>
        <button type="submit" name="edit_user" class="btn btn-primary" style="width: 100%;">Update User</button>
        <a href="admin_dashboard.php" style="display: block; text-align: center; margin-top: 1rem; color: #666; text-decoration: none;">Back to Admin Panel</a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
