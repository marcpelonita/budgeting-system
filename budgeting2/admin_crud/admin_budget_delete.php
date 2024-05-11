<?php
require '../config.php';
session_start();
$budget_id = isset($_GET['budget_id']) ? $_GET['budget_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $delete_expenses_stmt = $db->prepare("DELETE FROM expenses WHERE budget_id = :budget_id");
    $delete_expenses_stmt->bindParam(':budget_id', $budget_id, PDO::PARAM_INT);
    $delete_expenses_stmt->execute();

    $delete_expenses_stmt = $db->prepare("DELETE FROM budgets WHERE budget_id = :budget_id");
    $delete_expenses_stmt->bindParam(':budget_id', $budget_id, PDO::PARAM_INT);
    $delete_expenses_stmt->execute();

    if ($delete_expenses_stmt) {
        $_SESSION['message'] = 'Delete Successfully';
        header('Location: ../admin.php?status=success');
        exit;
    } else {
        $_SESSION['message'] = 'Failed to delete';
        header('Location: ../admin.php?status=error');
        exit;
    }
}
?>
