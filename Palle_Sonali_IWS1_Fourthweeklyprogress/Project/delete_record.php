<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$recordId = $input['id'] ?? null;
$recordType = $input['type'] ?? null;

if ($recordId && ($recordType === 'expense' || $recordType === 'income')) {
    $tableName = $recordType === 'expense' ? 'Expenses' : 'Incomes';
    $stmt = $pdo->prepare("DELETE FROM {$tableName} WHERE id = ? AND user_id = ?");
    $success = $stmt->execute([$recordId, $_SESSION['user_id']]);
    
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
exit;
?>
