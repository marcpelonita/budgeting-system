<?php
session_start();
require 'config.php';

$budget_id = isset($_GET['budget_id']) ? $_GET['budget_id'] : null;
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (isset($_POST['save'])) {
    $budget_name = $_POST['budget_name'];

    if($budget_name == ''){

    }else{
        $sqlInsertBudget = "INSERT INTO budget_list (user_id, budget_id, budget_name) VALUES (:user_id, :budget_id, :budget_name)";
        $stmtInsertBudget = $db->prepare($sqlInsertBudget);
        $stmtInsertBudget->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtInsertBudget->bindParam(':budget_id', $budget_id, PDO::PARAM_INT);
        $stmtInsertBudget->bindParam(':budget_name', $budget_name, PDO::PARAM_STR);
    
        if ($stmtInsertBudget->execute()) {
            $message = "Budget added successfully!";
            // Redirect to the same page to clear the form and allow adding a new budget
            header("Location: {$_SERVER['PHP_SELF']}?user_id=$user_id&budget_id=$budget_id");
            exit();
        } else {
            $message = "Error adding budget.";
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
                        <h3>Budget Name</h3>               
                    <input  class="input1" type="text" placeholder="Budget name" name="budget_name">
                    <button class="submit" name="save">Save</button>
      
                    </div>
                </form>
           
                
            </div><!-- sub container -->
        </div><!-- wrapper -->
    </div> <!-- container -->
</body>
</html>
