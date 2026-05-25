<?php
require_once 'config/db.php';

// Session is already started in config/db.php

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

<div class="login-shell">
    <section class="login-panel">
        <a href="index.php" class="login-brand">
            <img src="assets/logo.png" alt="ThesisTrack Logo">
            <span>ThesisTrack</span>
        </a>

        <div class="login-copy">
            <h1>Welcome back!</h1>
            <p>Access thesis submissions, reviews, feedback, and academic progress from one secure workspace.</p>
        </div>

        <form action="index.php" method="POST" class="login-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="name@university.edu">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Enter your password">
            </div>

            <button type="submit" name="login" class="btn btn-primary login-submit"><i data-lucide="log-in" class="btn-icon" aria-hidden="true"></i>Sign in</button>
        </form>
    </section>

    <aside class="login-showcase" aria-label="ThesisTrack workspace summary">
        <div class="showcase-card">
            <div class="showcase-tags">
                <span>Students</span>
                <span>Supervisors</span>
                <span>Admins</span>
            </div>
            <blockquote>
                ThesisTrack keeps thesis submission, review, grading, and user management in one role-based academic system.
            </blockquote>
            <p>Centralized Management System</p>
        </div>
    </aside>
</div>
<?php require_once 'includes/footer.php'; ?>
