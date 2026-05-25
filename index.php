<?php
require_once 'config/db.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($_SESSION['role'] == 'student') {
        header("Location: pages/student_dashboard.php");
    } elseif ($_SESSION['role'] == 'supervisor') {
        header("Location: pages/supervisor_dashboard.php");
    } elseif ($_SESSION['role'] == 'admin') {
        header("Location: pages/admin_dashboard.php");
    }
    exit();
}

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
  
</div>
<?php require_once 'includes/footer.php'; ?>
