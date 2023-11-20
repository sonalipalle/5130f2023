<?php
session_start();



// Include database connection file
require_once "db.php";

// Query database for user details
$stmt = $pdo->prepare("SELECT username FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();

if(!$user){
    exit('Something went wrong, please log in again.');
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" type="text/css" href="dashboard_style.css">
    <!-- CSS to be done for Week 4 -->
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($user['username']); ?></b>. Welcome to your dashboard.</h1>
    </div>
    <p>
        <!-- Dashboard Content for Week 4 -->
    </p>
</body>
</html>
