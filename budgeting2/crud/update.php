<?php
session_start();
require '../config.php';


$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$user_id) {
    header("Location: login/login.php");
    exit();
}


$expenses_id = isset($_GET['id']) ? $_GET['id'] : null;
$expense_category = '';
$amount = '';
$message = '';

if (isset($_POST['update'])) {
    $amount = $_POST['amount'];
    $expense_category = $_POST['expense_category'];

    if (empty($expenses_id) || empty($expense_category) || empty($amount) || $amount < 0) {
        $_SESSION['message'] = "Please enter valid values for the expense.";
        header('Location: update.php?id=' . $expenses_id);
        exit;
    } else {
        // Update the expense
        $sqlUpdateExpense = "UPDATE expenses SET expense_category = :expense_category, amount = :amount WHERE expenses_id = :expenses_id AND user_id = :user_id";
        $stmtUpdateExpense = $db->prepare($sqlUpdateExpense);
        $stmtUpdateExpense->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtUpdateExpense->bindParam(':expenses_id', $expenses_id, PDO::PARAM_INT);
        $stmtUpdateExpense->bindParam(':expense_category', $expense_category, PDO::PARAM_STR);
        $stmtUpdateExpense->bindParam(':amount', $amount, PDO::PARAM_INT);

        if ($stmtUpdateExpense->execute()) {

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

            $_SESSION['message'] = 'Update Successfully';
            header('Location: ../manage_expense.php?action=joined');
            exit;
        } else {
            echo "Failed to update expense. Please try again.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Update</title>
    <link rel="stylesheet" href="../budget.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
</head>

<body>  
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
          <img src="../image/user.jpg" alt="">
          <span class="nav-item"> <?php echo $result['username'];?></span>
        </a></li>
        <li><a href="../index.php">
          <i class="fas fa-home"></i>
          <span class="nav-item">Home</span>
        </a></li>
        <li><a href="../add_Expense.php">
        <i class="fas fa-plus"></i>
          <span class="nav-item">Add Expense</span>
        </a></li>
        <li><a href="../manage_expense.php">
        <i class="fas fa-tasks"></i>
          <span class="nav-item">Manage Expenses</span>
        </a></li>
        <li><a href="../analytics.php">
          <i class="fas fa-chart-bar"></i>
          <span class="nav-item">Analytics</span>
        </a></li>
        <li><a href="../notification_list.php">
          <i class="fas fa-bell"></i>
          <span class="nav-item">Notification</span>
        </a></li>
        <li><a href="../login/logout.php" class="logout">
          <i class="fas fa-sign-out-alt"></i>
          <span class="nav-item">Log out</span>
        </a></li>
      </ul>
    </nav>

        <div class="wrapper">
            <form action="" method="POST">
                <div class="user-amount-container">
                    <h3>Expenses</h3>
                    <?php
                 
                    if (isset($_SESSION['message'])) {
                        echo '<script>alert("' . $_SESSION['message'] . '");</script>';
                        unset($_SESSION['message']); 
                    }
                    ?>


                    <input class="input-2" type="number" id="user-amount" name="amount" placeholder="Enter cost">

                    <fieldset class="form-group">
                        <legend class="col"><b>Category</b></legend>
                        <div>
                           <div class="form-check">
                                <input class="form-check-input" type="radio" name="expense_category" id="expense_category1" value="Rent" <?php echo ($expense_category == 'Rent') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="expense_category1">Rent</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="expense_category" id="expense_category2" value="Food" <?php echo ($expense_category == 'Food') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="expense_category2">Food</label>
                            </div>
                            <div class="form-check">
                                    <input class="form-check-input" type="radio" name="expense_category" id="expense_category2" value="Transfortation" <?php echo ($expense_category == 'Transfortation') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="expense_category2">
                                        Transfortation
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="expense_category" id="expense_category2" value="Personal Expenses" <?php echo ($expense_category == 'Personal Expenses') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="expense_category2">
                                        Personal Expenses
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="expense_category" id="expense_category2" value=" Student Loan" <?php echo ($expense_category == ' Student Loan') ? 'checked' : '' ?>>
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

                    <button class="submit" name="update" id="check-amount">Update</button>
                </div>
            </form>
        </div><!-- sub container -->
    </div>
</body>
</html>
