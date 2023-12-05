<?php
ob_start(); // Start output buffering
header('Content-Type: application/json'); // Set correct content type for JSON response

session_start();
require_once "db.php"; // Make sure this path is correct

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'You are not logged in.']);
    exit;
}

// Extract the data from the POST request
$title = isset($_POST['income-title']) ? trim($_POST['income-title']) : null;
$amount = $_POST['income-amount'] ?? null;
$date = $_POST['income-date'] ?? null;
$category = $_POST['income-category'] ?? null;
$reference = $_POST['income-reference'] ?? null;
$user_id = $_SESSION['user_id']; // The user's ID to associate with the income

// Validate and sanitize the input data here
if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'The title for the income cannot be empty.']);
    exit;
}

// Insert the data into the database
try {
    $stmt = $pdo->prepare("INSERT INTO Incomes (title, amount, date, category, reference, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $amount, $date, $category, $reference, $user_id]);

    // Fetch the last inserted income
    $incomeId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM Incomes WHERE id = ?");
    $stmt->execute([$incomeId]);
    $income = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if we got the result
    if ($income) {
        echo json_encode(['success' => true, 'income' => $income]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Income added but not retrieved.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
exit;
?>
