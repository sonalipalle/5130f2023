<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'You are not logged in.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM Expenses WHERE user_id = ? AND rem_category = 'future' ORDER BY date");
    $stmt->execute([$_SESSION['user_id']]);
    $reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'reminders' => $reminders]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
exit;
?>
