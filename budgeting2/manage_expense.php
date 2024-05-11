<?php
session_start();
require 'config.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$user_id) {
    // Redirect to login page or handle the situation
    header("Location: login/login.php");
    exit();
}
$expenses_id = isset($_GET['expenses_id']) ? $_GET['expenses_id'] : null;
$budget_id = isset($_GET['budget_id']) ? $_GET['budget_id'] : null;
// Display a message when the post is added
if (isset($_SESSION['message'])) {
    echo '<script>alert("' . $_SESSION['message'] . '");</script>';
    unset($_SESSION['message']); // Clear the session error message
}

if (isset($_POST['btn_request'])) {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $status = "Pending";

    $sql = "UPDATE budgets SET status = :status WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status: " . $stmt->errorInfo()[2];
    }
}


if (isset($_POST['submit_search'])) {
    $expense_category = $_POST['expense_category'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if (!empty($expense_category)) {
        $sql = "SELECT expenses_id, expense_category, amount FROM expenses WHERE expense_category = :expense_category AND user_id = :user_id";    
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':expense_category', $expense_category, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    } else {     
        echo "Please select a category.";
    }
}

// Fetch user profile
$user_id = $_SESSION['user_id'];
$SelectUsername = "SELECT username FROM user WHERE user_id = :user_id";
$Print = $db->prepare($SelectUsername);
$Print->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$Print->execute();
$result = $Print->fetch(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | MANAGE EXPENSE</title>
    <link rel="stylesheet" href="manage_expense.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
</head>

<body>
<?php
// Check if the post content is empty
if (isset($_SESSION['error_message']) && empty($_POST['post'])) {
    echo '<p class="empty-post-message">' . $_SESSION['error_message'] . '</p>';
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
        
        </section>
        <div class="wrapper">
      
        <form action="" method="POST">
            <div class="nav-top-right">
                <div class="search-box">
                    <i class='fas fa-search'></i>
                    <select name="expense_category">
                        <option value="#">Display all</option>
                        <option value="Rent">Rent</option>
                        <option value="Food">Food</option>
                        <option value="Transportation">Transportation</option>
                        <option value="Personal Expenses">Personal Expenses</option>
                        <option value="Student Loan">Student Loan</option>
                        <option value="Health Care">Health Care</option>
                    </select>
                    <button class="search-btn" type="submit" name="submit_search">Search</button>
                    
                </div>
             
            </div>
           
        </form>
        <div class="cvs">
          <a href="export.php" class="export-link">
          <i class='fas fa-download'></i>
           Export Budget Data</a>
         </div>

                <form action="" method ="POST">
                <div class="cvs">       
                     <i class="fas fa-request"></i>
                        <button name ="btn_request" class="export-link">Request Budget</button>
                    </div>
                    </form>

            <div class="sub-container">
                <!-- Budget form -->
                <!-- Expenses form -->
               
            </div>

            <!-- Output section -->
            <div class="output-container flex-space">
                <div>
                    <p>Total Budget</p>
                    <?php
                     $user_id = $_SESSION['user_id'];
                     $sql = "SELECT SUM(amount) AS total_budget FROM budgets WHERE user_id = :user_id";
                     $stmt = $db->prepare($sql);
                     $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                     $stmt->execute();
                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                     echo '<span id="amount">' . $result['total_budget'] . '</span>';
                    ?>
                </div>

                <div>
                    <p>Total Expenses</p>
                    <?php
                     $user_id = $_SESSION['user_id'];
                     $sql = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE user_id = :user_id";
                     $stmt = $db->prepare($sql);
                     $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                     $stmt->execute();
                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                     echo '<span id="amount">' . $result['total_expenses'] . '</span>';
                    ?>
                </div>

                <div>
                    <p>Balance</p>
                    <?php
                   
                     $user_id = $_SESSION['user_id'];
                     $sql = "SELECT SUM(balance) AS total_balance FROM budgets WHERE user_id = :user_id";
                     $stmt = $db->prepare($sql);
                     $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                     $stmt->execute();
                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
                     echo '<span id="amount">' . $result['total_balance'] . '</span>';
                    ?>
                </div>
            </div>

            <!-- Expenses List -->
            <div class="list">
    <h3>Expenses List</h3>
    <div class="list-container" id="list">
        <table>
            <thead>
                <tr>
                    <th>Expense Category</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                 $sqlExpenses = "SELECT expenses_id, expense_category, amount FROM expenses WHERE user_id = :user_id";
                 $stmtExpenses = $db->prepare($sqlExpenses);
                 $stmtExpenses->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                 $stmtExpenses->execute();
                 $expensesResult = $stmtExpenses->fetchAll(PDO::FETCH_ASSOC);

                foreach ($expensesResult as $row) { ?>
                    <tr class="expense-row">
                        <td><?php echo $row['expense_category']; ?></td>
                        <td><?php echo $row['amount']; ?></td>
                        <td>
                        <a href="crud/update.php?id=<?php echo $row['expenses_id']; ?>" name="edit">Edit</a>

                            <a href="#" onclick="confirmDelete('<?php echo $row['expenses_id']; ?>' , '<?php echo $user_id; ?>')" name="Delete">Delete</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
               
        <div class="save">
            <?php  

                $sqlExpenses = "SELECT * FROM budgets WHERE user_id = :user_id";
                $stmtExpenses = $db->prepare($sqlExpenses);
                $stmtExpenses->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmtExpenses->execute();
                $expensesResult = $stmtExpenses->fetchAll(PDO::FETCH_ASSOC);

                foreach ($expensesResult as $row)  {}
                        

                $budget_id = isset($row['budget_id']) ? $row['budget_id'] : null;
                $expenses_id = isset($row['expenses_id']) ? $row['expenses_id'] : null;
                $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

               
            ?>
            <a href="savebudget.php?budget_id=<?php echo $budget_id; ?>&user_id=<?php echo $user_id; ?>">
                <button class="btn-save">SAVE</button>
            </a>
        </div>

    </div>
</div>


       
    </div> <!-- container -->
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>




<script>
  function confirmDelete(expenses_id, user_id) {
    console.log("Function called with user_id:", user_id, "and expenses_id:", expenses_id);
    Swal.fire({
      title: "Are you sure?",
      text: "You will be redirected to fill up the booking info",
      icon: "success",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes",
    }).then(function (result) {
      if (result.isConfirmed) {
        window.location.href = `crud/delete.php?user_id=${user_id}&expenses_id=${expenses_id}`;
      }
    });
  }
  document.addEventListener('DOMContentLoaded', function () {
        const searchForm = document.querySelector('form');
        const expenseRows = document.querySelectorAll('.expense-row');

        searchForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const selectedCategory = document.querySelector('select[name="expense_category"]').value;

            expenseRows.forEach(function (row) {
                const categoryCell = row.querySelector('td:first-child');
                if (selectedCategory === '#' || categoryCell.textContent.trim() === selectedCategory) {
                    row.style.display = ''; // Show the row
                } else {
                    row.style.display = 'none'; 
                  
                }
            });
        });
    });
</script>

