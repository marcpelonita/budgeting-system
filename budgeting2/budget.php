<?php
session_start();
require 'config.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$budget_id = isset($_SESSION['budget_id']) ? $_SESSION['budget_id'] : null;
$expense_category = isset($_POST['expense_category']) ? $_POST['expense_category'] : '';

if (!$user_id) {
    // Redirect to login page or handle the situation
    header("Location: login/login.php");
    exit();
}

$message = '';
$setBudgetButtonText = 'Set Budget';

// Check if the user has already set a budget
$sqlCheckBudget = "SELECT COUNT(*) as count FROM budgets WHERE user_id = :user_id";
$stmtCheckBudget = $db->prepare($sqlCheckBudget);
$stmtCheckBudget->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmtCheckBudget->execute();
$resultCheckBudget = $stmtCheckBudget->fetch(PDO::FETCH_ASSOC);
$hasBudget = ($resultCheckBudget['count'] > 0);

// Change the button text if the user has a budget
if ($hasBudget) {
    $setBudgetButtonText = 'Update Budget';
}

if (isset($_POST['Budget'])) {
    $amount = $_POST['amount'];

    if (empty($amount) || $amount < 0) {
        $message = "Please enter a valid budget amount.";
    } else {
        if ($hasBudget) {
            // Update the existing budget
            $sqlUpdateBudget = "UPDATE budgets SET amount = :amount, balance = :amount WHERE user_id = :user_id ORDER BY budget_id DESC LIMIT 1";
            $stmtUpdateBudget = $db->prepare($sqlUpdateBudget);
            $stmtUpdateBudget->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmtUpdateBudget->bindParam(':amount', $amount, PDO::PARAM_INT);
            if ($stmtUpdateBudget->execute()) {
                $message = "Budget updated successfully!";
            } else {
                $message = "Error updating budget.";
            }
        } else {
            // Insert a new budget
            $sqlInsertBudget = "INSERT INTO budgets (user_id, amount, balance) VALUES (:user_id, :amount, :amount)";
            $stmtInsertBudget = $db->prepare($sqlInsertBudget);
            $stmtInsertBudget->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmtInsertBudget->bindParam(':amount', $amount, PDO::PARAM_INT);
            $stmtInsertBudget->bindValue(':balance', $amount, PDO::PARAM_INT);
            if ($stmtInsertBudget->execute()) {
                $message = "Budget added successfully!";
            } else {
                $message = "Error adding budget.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Budget</title>
    <link rel="stylesheet" href="budget.css">
</head>

<body>
    <div class="container">
        <nav>
            <!-- Your navigation code -->
        </nav>

        <div class="wrapper">
            <div class="sub-container">
                <form action="" method="POST">
                    <div class="total-amount-container">
                        <h3><?php echo $setBudgetButtonText; ?></h3>
                        <p class="hide error" id="budget-error">
                            Value cannot be empty or negative</p>
                        <input class="input1" type="number" id="total-amount" name="amount" placeholder="Enter total amount">
                        <button class="submit" name="Budget" id="total-amount"><?php echo $setBudgetButtonText; ?></button>
                    </div>
                </form>
                <!-- ... Other form elements ... -->
            </div>
        </div><!-- wrapper -->
    </div> <!-- container -->
</body>
</html>
