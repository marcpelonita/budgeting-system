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

$sqlCheckBudget = "SELECT budget_id FROM budgets WHERE user_id = :user_id ORDER BY budget_id DESC LIMIT 1";
$stmtCheckBudget = $db->prepare($sqlCheckBudget);
$stmtCheckBudget->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmtCheckBudget->execute();
$resultCheckBudget = $stmtCheckBudget->fetch(PDO::FETCH_ASSOC);
$budget_id = ($resultCheckBudget) ? $resultCheckBudget['budget_id'] : null;
$hasBudget = ($budget_id !== null);

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


            //calculate the total expenses
            $sql = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            //fetch the amount in the budgets table
            $sqlBudgetDetails = "SELECT amount FROM budgets WHERE user_id = :user_id";
            $stmtBudgetDetails = $db->prepare($sqlBudgetDetails);
            $stmtBudgetDetails->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmtBudgetDetails->execute();
            $budgetDetails = $stmtBudgetDetails->fetch(PDO::FETCH_ASSOC);

            //minus the amount and the total expenses
            $new_balance = $budgetDetails['amount'] - $result['total_expenses'];

            // Update the balance in budgets table
            $sqlUpdateBalance = "UPDATE budgets SET balance = :new_balance WHERE user_id = :user_id";
            $stmtUpdateBalance = $db->prepare($sqlUpdateBalance);
            $stmtUpdateBalance->bindParam(':new_balance', $new_balance, PDO::PARAM_INT);
            $stmtUpdateBalance->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmtUpdateBalance->execute();
















            } else {
                $message = "Error updating budget.";
            }
        } else {
            // Insert a new budget
            $sqlInsertBudget = "INSERT INTO budgets (user_id, amount, balance) VALUES (:user_id, :amount, :amount)";
            $stmtInsertBudget = $db->prepare($sqlInsertBudget);
            $stmtInsertBudget->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmtInsertBudget->bindParam(':amount', $amount, PDO::PARAM_INT);
            if ($stmtInsertBudget->execute()) {
                $message = "Budget added successfully!";
            } else {
                $message = "Error adding budget.";
            }
        }
    }
}


if (isset($_POST['checkBudget'])) {
    $expense_category = isset($_POST['expense_category']) ? $_POST['expense_category'] : '';
  
      $amount = $_POST['amount'];
  
      if (empty($expense_category) || empty($amount) || $amount < 0) {
          $message = "Please enter valid values for expense.";
      } else {
          // Check if the expense amount is less than or equal to the available budget
          $sql = "SELECT balance FROM budgets WHERE user_id = :user_id ORDER BY budget_id DESC LIMIT 1";
          $stmtBalance = $db->prepare($sql);
          $stmtBalance->bindParam(':user_id', $user_id, PDO::PARAM_INT);
          $stmtBalance->execute();
          $balanceResult = $stmtBalance->fetch(PDO::FETCH_ASSOC);
  
          
           
              $sql = "INSERT INTO expenses (user_id, budget_id, expense_category, amount) VALUES (:user_id, :budget_id, :expense_category, :amount)";
              $stmtInsert = $db->prepare($sql);
  
              $stmtInsert->bindParam(':user_id', $user_id, PDO::PARAM_INT);
              $stmtInsert->bindParam(':budget_id', $budget_id, PDO::PARAM_INT);
              $stmtInsert->bindParam(':expense_category', $expense_category, PDO::PARAM_STR);
              $stmtInsert->bindParam(':amount', $amount, PDO::PARAM_INT);
  
              if ($stmtInsert->execute()) {
                  // Update the budget
                  $newBalance = $balanceResult['balance'] - $amount;
                  $sqlUpdate = "UPDATE budgets SET balance = :balance WHERE user_id = :user_id ORDER BY budget_id DESC LIMIT 1";
  
                  $stmtUpdate = $db->prepare($sqlUpdate);
                  $stmtUpdate->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                  $stmtUpdate->bindParam(':balance', $newBalance, PDO::PARAM_INT);
                  $stmtUpdate->execute();


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
  
                  $_SESSION['message'] = 'Added Successfully';
                  header('Location: add_Expense.php?action=joined');
                  exit;
              } else {
                  $message = "Error adding expense.";
              }
         
      }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ADD Expense</title>
    <link rel="stylesheet" href="budget.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
</head>

<body>
<?php

    if (isset($_SESSION['message'])) {
       echo '<script>alert("' . $_SESSION['message'] . '");</script>';
       unset($_SESSION['message']); 
      }
   ?>
<?php
 $SelectUsername = "SELECT username FROM user WHERE user_id = :user_id";
 $Print = $db->prepare($SelectUsername);
 $Print->bindParam(':user_id', $user_id, PDO::PARAM_INT);
 $Print->execute();
 $result = $Print->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="container">
    <nav>
      <ul>
        <li><a href="#" class="logo">
          <img src="image/user.jpg" alt="">
          <span class="nav-item"> <?php echo $result['username'];?></span>
        </a></li>
        <li><a href="index.php">
          <i class="fas fa-home"></i>
          <span class="nav-item">Home</span>
        </a></li>
        <li><a href="add_Expense.php">
        <i class="fas fa-plus"></i>
          <span class="nav-item">Add Expense</span>
        </a></li>
        <li><a href="manage_expense.php">
        <i class="fas fa-tasks"></i>
          <span class="nav-item">Manage Expenses</span>
        </a></li>
        <li><a href="analytics.php">
          <i class="fas fa-chart-bar"></i>
          <span class="nav-item">Analytics</span>
        </a></li>
        <li><a href="notification_list.php">
          <i class="fas fa-bell"></i>
          <span class="nav-item">Notification</span>
        </a></li>
        <li><a href="login/logout.php" class="logout">
          <i class="fas fa-sign-out-alt"></i>
          <span class="nav-item">Log out</span>
        </a></li>
      </ul>
    </nav>

        <div class="wrapper">
            <div class="sub-container">
                <!-- Budget form -->
                <form action="" method="POST">
                    <div class="total-amount-container">
                        <h3><?php echo $setBudgetButtonText; ?></h3>
                        <p class="hide error" id="budget-error">
                            Value cannot be empty or negative</p>
                        <input class="input1" type="number" id="total-amount" name="amount" placeholder="Enter total amount">
                        <button class="submit" name="Budget" id="total-amount"><?php echo $setBudgetButtonText; ?></button>
                    </div>
                </form>


                
                <!-- Expenses form -->
                <form action="" method="POST">
                    <div class="user-amount-container">
                        <h3>Expenses</h3>
                        <input class="input-2" type="number" id="user-amount" name="amount" placeholder="Enter cost">
                        <fieldset class="form-group">
                            <legend class="col"><b>Category</b></legend>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="expense_category" id="expense_category1" value="Rent" <?php echo ($expense_category == ' Rent') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="expense_category1">
                                        Rent
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="expense_category" id="expense_category2" value="Food" <?php echo ($expense_category == 'Food') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="expense_category2">
                                        Food
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="expense_category" id="expense_category2" value="Transportation" <?php echo ($expense_category == 'Transfortation') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="expense_category2">
                                        Transportation
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="expense_category" id="expense_category2" value="Personal Expenses" <?php echo ($expense_category == 'Personal Expenses') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="expense_category2">
                                        Personal Expenses
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="expense_category" id="expense_category2" value="Student Loan" <?php echo ($expense_category == ' Student Loan') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="expense_category2">
                                        Student Loan
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="expense_category" id="expense_category2" value="Health Care" <?php echo ($expense_category == 'Health Care') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="expense_category2">
                                        Health Care  
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        <button class="submit" name="checkBudget" id="check-amount">Check amount</button>
                    </div>
                </form>
                
            </div><!-- sub container -->
        </div><!-- wrapper -->
    </div> <!-- container -->
</body>
</html>
