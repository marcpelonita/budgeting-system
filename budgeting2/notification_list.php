<?php
require 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard | Index</title>
  <link rel="stylesheet" href="analytics.css" />
  <!-- Font Awesome Cdn Link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.z`/css/all.min.css"/>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
</head>
<style>
     .table-container {
      width: 100%;
      margin-top: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      border: 4px solid #ddd;
      height: 50px;
      padding: 8px; 
      text-align: left;
      box-sizing: border-box;
      cursor: pointer;
    }

    th {
      text-align: center;
      background-color: #f2f2f2;
    }

    th, td, .Edit, .Delete {
      text-align: center;
    }

    .course-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .id {
      width: 100%; /* Use 100% width for the table container */
    }
</style>
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

    <section class="main">
      <div class="main-top">
        <h1>User DashBoard</h1>
     
      </div>
    

      <section class="main-course">
          <h1>User Notification</h1>
                <div class="course-box">
                    <ul>
                        <li class="active">In progress</li>
                      
                    </ul>
                    <div class="course">
                    <div class="id" id="box">
                <table>
                    <thead>
                        <tr>
                            <th>Comment Subject</th>
                            <th>Comment Text</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sqlExpenses = "SELECT comment_subject, comment_text FROM notification WHERE user_id = :user_id";
                        $stmtExpenses = $db->prepare($sqlExpenses);
                        $stmtExpenses->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                        $stmtExpenses->execute();
                        $expensesResult = $stmtExpenses->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($expensesResult as $row) {
                        ?>
                            <tr class="expense-row">
                                <td><?php echo $row['comment_subject']; ?></td>
                                <td><?php echo $row['comment_text']; ?></td>
                              
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

                            </div>
                            
                    </div><!--course-->
                </div><!--course-box-->
      </section>
    </section>
  </div>


 
</body>
</html>

