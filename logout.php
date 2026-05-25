<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();

session_start();
$_SESSION['flash_message'] = "You have been logged out successfully.";
$_SESSION['flash_type'] = "success";

header("Location: index.php");
exit();
?>
