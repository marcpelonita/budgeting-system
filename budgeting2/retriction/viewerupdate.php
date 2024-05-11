<?php
include '../config.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['budget_id'])) {
    $budget_id = $_POST['budget_id'];

    try {
        // Use COUNT(*) instead of rowCount() for better compatibility
        $checkinguser = $db->prepare("SELECT COUNT(*) FROM budgets WHERE budget_id = :budget_id AND user_id = :user_id");
        $checkinguser->bindParam(':budget_id', $budget_id, PDO::PARAM_INT);
        $checkinguser->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $checkinguser->execute();

        $count = $checkinguser->fetchColumn();

        if ($count > 0) {
            $deleteStmt = $db->prepare("DELETE FROM budgets WHERE budget_id = :budget_id");
            $deleteStmt->bindParam(':budget_id', $budget_id, PDO::PARAM_INT);
            $deleteStmt->execute();

            header('Location: viewer.php');
            exit();
        } else {
            echo "<script>alert('Don\'t have permission to Update!'); window.location.href='../listbudget.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>