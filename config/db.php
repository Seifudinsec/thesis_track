<?php
$host = 'localhost';
$db   = 'thesis_track';
$user = 'root';
$pass = ''; // Default XAMPP password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Connection successful
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
