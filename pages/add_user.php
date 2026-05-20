<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Check if email exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['flash_message'] = "Error: Email already exists.";
        $_SESSION['flash_type'] = "error";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$full_name, $email, $password, $role])) {
            $_SESSION['flash_message'] = "User added successfully!";
            $_SESSION['flash_type'] = "success";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['flash_message'] = "Error: Could not add user.";
            $_SESSION['flash_type'] = "error";
        }
    }
}
?>

<div class="form-container">
    <h2>Add New User</h2>
    <form action="add_user.php" method="POST">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" class="form-control" required placeholder="e.g. John Doe">
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" required placeholder="name@university.edu">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
        </div>
        <div class="form-group">
            <label for="role">User Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="student">Student</option>
                <option value="supervisor">Supervisor</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" name="add_user" class="btn btn-primary" style="width: 100%;">Create User</button>
        <a href="admin_dashboard.php" style="display: block; text-align: center; margin-top: 1rem; color: #666; text-decoration: none;">Back to Admin Panel</a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
