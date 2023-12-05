<?php
session_start();
require_once "db.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'You are not logged in.']);
    exit;
}

// Retrieve updated data for the graph
try {
    // Example: Fetch total expenses and total incomes for the logged-in user
    $stmt = $pdo->prepare("SELECT SUM(amount) as total_expenses FROM Expenses WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $totalExpenses = $stmt->fetch(PDO::FETCH_ASSOC)['total_expenses'];

    $stmt = $pdo->prepare("SELECT SUM(amount) as total_incomes FROM Incomes WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $totalIncomes = $stmt->fetch(PDO::FETCH_ASSOC)['total_incomes'];

    // Format the data in a way that is suitable for the chart
    $data = [
        'labels' => ['Expenses', 'Incomes'],
        'expenses' => [$totalExpenses],
        'incomes' => [$totalIncomes],
    ];

    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
exit;
?>
