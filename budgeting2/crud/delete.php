<?php
session_start();
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
   //get the id from the url
    $expenses_id = isset($_GET['expenses_id']) ? $_GET['expenses_id'] : null;
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

    // Delete the expense
    $stmtDeleteExpense = $db->prepare("DELETE FROM expenses WHERE expenses_id = :expenses_id");
    $stmtDeleteExpense->bindParam(':expenses_id', $expenses_id, PDO::PARAM_INT);
  
    if ($stmtDeleteExpense->execute()) {

        // Calculate the total expenses
        $sqlTotalExpenses = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE user_id = :user_id";
        $stmtTotalExpenses = $db->prepare($sqlTotalExpenses);
        $stmtTotalExpenses->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtTotalExpenses->execute();
        $resultTotalExpenses = $stmtTotalExpenses->fetch(PDO::FETCH_ASSOC);

        // Fetch the original budget amount
        $sqlBudgetDetails = "SELECT amount FROM budgets WHERE user_id = :user_id";
        $stmtBudgetDetails = $db->prepare($sqlBudgetDetails);
        $stmtBudgetDetails->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtBudgetDetails->execute();
        $budgetDetails = $stmtBudgetDetails->fetch(PDO::FETCH_ASSOC);

        // Calculate the new balance by subtracting amount and total expenses
        $newBalance = $budgetDetails['amount'] - $resultTotalExpenses['total_expenses'];

        // Update the balance in budgets table
        $sqlUpdateBalance = "UPDATE budgets SET balance = :new_balance WHERE user_id = :user_id";
        $stmtUpdateBalance = $db->prepare($sqlUpdateBalance);
        $stmtUpdateBalance->bindParam(':new_balance', $newBalance, PDO::PARAM_INT);
        $stmtUpdateBalance->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtUpdateBalance->execute();

        $_SESSION['message'] = 'Delete Successfully';
        header('Location: ../manage_expense.php?status=success');
        exit;
    } else {
        $_SESSION['message'] = 'Failed to delete';
        header('Location: ../manage_expense.php?status=error');
        exit;
    }
}
?>
