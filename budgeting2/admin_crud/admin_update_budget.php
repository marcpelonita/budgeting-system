<?php
require '../config.php';
session_start();
$budget_id = isset($_GET['budget_id']) ? $_GET['budget_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $update_expenses_stmt = $db->prepare("UPDATE budgets SET status = 'pending' WHERE budget_id = :budget_id");
    $update_expenses_stmt->bindParam(':budget_id', $budget_id, PDO::PARAM_INT);
    $update_expenses_stmt->execute();
    

    if ($update_expenses_stmt) {
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
