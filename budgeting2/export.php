<?php
session_start();
require 'config.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch budget data from the database
    $sql = "SELECT expenses.expense_category, expenses.amount, budgets.balance, budgets.amount as budget FROM expenses 
            INNER JOIN budgets ON expenses.budget_id = budgets.budget_id 
            WHERE expenses.user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate the total value of expenses
    $totalExpenses = 0;
    foreach ($budgets as $budget) {
        $totalExpenses += $budget['amount'];
    }

    // CSV header
    $csvData = "Expense Category,Amount\n";

    // CSV rows
    foreach ($budgets as $budget) {
        $csvData .= "{$budget['expense_category']},{$budget['amount']}\n";
    }

    // Add a row for the total value of expenses
    $csvData .= "\n\nTotal Expenses,{$totalExpenses},\n";

    // Add a row for the total balance
    $csvData .= "Total Balance,{$budgets[0]['balance']},\n";

    // Add a row for the total budget
    $csvData .= "Total Budget,{$budgets[0]['budget']},\n";

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="budget_data.csv"');

    // Output the CSV data
    echo $csvData;
    exit();
} else {
    // Unauthorized access
    echo "Unauthorized access!";
    exit();
}
?>
