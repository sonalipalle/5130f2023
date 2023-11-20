<?php
require 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'], $_POST['password'])) {
    $username = trim($_POST['name']);
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        // Handle error - input is not valid
        exit('Invalid input');
    }

    // Fetch user from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Create session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = time();

        // Redirect to dashboard
        header('Location: dashboard.php');
        exit();
    } else {
        // Handle error - invalid credentials
        exit('Login failed');
    }
} else {
    // Handle error - invalid request
    exit('Invalid request');
}
?>
