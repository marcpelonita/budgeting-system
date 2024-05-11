<?php
require '../config.php';
session_start();
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $delete_stmt = $db->prepare("DELETE FROM expenses WHERE user_id = :user_id");
    $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $delete_stmt->execute();

    
    $delete_stmt = $db->prepare("DELETE FROM budgets WHERE user_id = :user_id");
    $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $delete_stmt->execute();

    $delete_stmt = $db->prepare("DELETE FROM notification WHERE user_id = :user_id");
    $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $delete_stmt->execute();

    $delete_stmt = $db->prepare("DELETE FROM user WHERE user_id = :user_id");
    $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $delete_stmt->execute();



    if ($delete_stmt) {
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


