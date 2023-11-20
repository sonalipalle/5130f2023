<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['name'], $_POST['password'])) {
    $email = trim($_POST['email']);
    $username = trim($_POST['name']);
    $password = $_POST['password'];

    if (empty($username) || empty($password) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Handle error - input is not valid
        exit('Invalid input');
    }

    // Check if the username/email is already taken
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetchColumn() > 0) {
        exit('User already exists with this username/email.');
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $hashed_password, $email])) {
        // Redirect to login page
        header('Location: Login.html');
        exit();
    } else {
        exit('Failed to register user.');
    }
} else {
    exit('Invalid request');
}
?>
