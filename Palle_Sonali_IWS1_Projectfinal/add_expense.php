<?php
ob_start(); // Start output buffering
header('Content-Type: application/json'); // Specify the correct content type for JSON

session_start();
require_once "db.php"; // Make sure this path is correct

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'You are not logged in.']);
    exit;
}

// Extract the data from the POST request
$title = isset($_POST['expense-title']) ? trim($_POST['expense-title']) : null;
$amount = $_POST['expense-amount'] ?? null;
$date = $_POST['expense-date'] ?? null;
$category = $_POST['expense-category'] ?? null;
$rem_category = $_POST['rem-category'] ?? null;
$user_id = $_SESSION['user_id']; // Assuming this is the user's ID to associate with the expense

// Validate and sanitize the input data here
if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'The title for the expense cannot be empty.']);
    exit;
}

// Insert the data into the database
try {
    $stmt = $pdo->prepare("INSERT INTO Expenses (title, amount, date, category, rem_category, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $amount, $date, $category, $rem_category, $user_id]);

    // Fetch the last inserted expense
    $expenseId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM Expenses WHERE id = ?");
    $stmt->execute([$expenseId]);
    $expense = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if we got the result
    if ($expense) {
        echo json_encode(['success' => true, 'expense' => $expense]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Expense added but not retrieved.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
}
exit;
?>
