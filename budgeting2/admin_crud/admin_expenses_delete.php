<?php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_id"]) && isset($_POST["expenses_id"])) {
    $user_id = $_POST["user_id"];
    $expenses_id = $_POST["expenses_id"];

    // Fetch existing expenses information based on user_id and expenses_id
    $query = "SELECT * FROM expenses WHERE user_id = :user_id AND expenses_id = :expenses_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':expenses_id', $expenses_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        // Handle error if expenses not found
        echo "Expenses not found!";
        exit();
    }

    // Get the amount of the expense to be deleted
    $deletedExpenseAmount = $result['amount'];

    // Close the statement
    $stmt->closeCursor();

    // Delete expenses
    $deleteQuery = "DELETE FROM expenses WHERE user_id = :user_id AND expenses_id = :expenses_id";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $deleteStmt->bindParam(':expenses_id', $expenses_id, PDO::PARAM_INT);

    // Check if the delete was successful
    if ($deleteStmt->execute()) {
        // Insert notification record
        $subject = "delete";
        $comment = "Your data is deleted by the admin";

        $query = "INSERT INTO notification (comment_subject, comment_text, user_id) VALUES (:subject, :comment, :user_id)";
        $stmtNotification = $db->prepare($query);
        $stmtNotification->bindParam(':subject', $subject, PDO::PARAM_STR);
        $stmtNotification->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmtNotification->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        if ($stmtNotification->execute()) {
            // Notification record inserted successfully

            // Calculate the new balance
            $sql = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch the amount in the budgets table
            $sqlBudgetDetails = "SELECT amount FROM budgets WHERE user_id = :user_id";
            $stmtBudgetDetails = $db->prepare($sqlBudgetDetails);
            $stmtBudgetDetails->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmtBudgetDetails->execute();
            $budgetDetails = $stmtBudgetDetails->fetch(PDO::FETCH_ASSOC);

            // Subtract the amount of the deleted expense from the total expenses
            $new_balance = $budgetDetails['amount'] - $result['total_expenses'];

            // Update the balance in budgets table
            $sqlUpdateBalance = "UPDATE budgets SET balance = :new_balance WHERE user_id = :user_id";
            $stmtUpdateBalance = $db->prepare($sqlUpdateBalance);
            $stmtUpdateBalance->bindParam(':new_balance', $new_balance, PDO::PARAM_INT);
            $stmtUpdateBalance->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmtUpdateBalance->execute()) {
                // Balance updated successfully
                echo "<script>alert('Expense deleted successfully'); window.location.href='../admin.php';</script>";
                exit();
            } else {
                echo "Error updating balance.";
            }
        } else {
            echo "Error inserting notification.";
        }
    } else {
        echo "Error deleting expense.";
    }

    // Close the statements
    $deleteStmt->closeCursor();
    $stmtNotification->closeCursor();
    $stmtUpdateBalance->closeCursor();
} else {
    echo "User ID or Expenses ID not provided.";
    exit();
}
?>
