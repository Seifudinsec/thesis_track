<?php
require_once 'config/db.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle Login Logic (MUST be before any HTML output)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        
        $_SESSION['flash_message'] = "Welcome back, " . $user['full_name'] . "!";
        $_SESSION['flash_type'] = "success";

        if ($user['role'] == 'student') {
            header("Location: pages/student_dashboard.php");
        } elseif ($user['role'] == 'supervisor') {
            header("Location: pages/supervisor_dashboard.php");
        } elseif ($user['role'] == 'admin') {
            header("Location: pages/admin_dashboard.php");
        }
        exit();
    } else {
        $_SESSION['flash_message'] = "Invalid Email or Password.";
        $_SESSION['flash_type'] = "error";
    }
}

// Now include the header (which starts HTML output) 
require_once 'includes/header.php';
?>

<div class="form-container">
    <h2>Secure Login</h2>
    <p>Access your ThesisTrack account</p>
    
    <form action="index.php" method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" required placeholder="name@university.edu">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
        </div>
        
        <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Sign In</button>
    </form>
    
    <!-- <div style="margin-top: 1.5rem; text-align: center; font-size: 0.85rem;">
        <p>Student Demo: student@test.com / 123456</p>
        <p>Supervisor Demo: supervisor@test.com / 123456</p>
        <p>Admin Demo: admin@test.com / 123456</p>
    </div> -->
</div>

<!-- <section class="student-explanation" style="margin-top: 3rem; background: #eee; padding: 1.5rem; border-radius: 8px;">
    <h3>🎓 Student Explanation: index.php</h3>
    <p><strong>Logic Flow:</strong> Notice that the login logic is now at the very top. In PHP, the <code>header()</code> function (used for redirection) <strong>must</strong> be called before any HTML is sent to the browser. If we put it after <code>header.php</code>, it would fail because the browser has already started receiving the page layout.</p>
    <p><strong>Security:</strong> We continue to use <strong>PDO Prepared Statements</strong> and <strong>password_verify()</strong> to ensure credentials are handled safely.</p>
</section> -->

<?php require_once 'includes/footer.php'; ?>
