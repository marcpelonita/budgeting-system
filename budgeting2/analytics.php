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
          <h1>Budget Stastistic</h1>
                <div class="course-box">
                    <ul>
                        <li class="active">In progress</li>
                      
                    </ul>
                    <div class="course">
                            <div class="id" id="box">
                               
                            </div>
                            <div class="box" id="box2">
                            
                            </div>
                
                    </div><!--course-->
                </div><!--course-box-->
      </section>
    </section>
  </div>


 
<?php
$user_id = $_SESSION['user_id'];


$query = "SELECT * FROM expenses WHERE user_id = :user_id"; // SQL query to fetch data

try {
    $getData = $db->prepare($query);
    $getData->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $getData->execute();


    if ($getData) {
        // Build the pie chart
        echo '<script>
            Highcharts.chart(\'box\', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: \'pie\',
                    width: 500,
                    height: 350,
                    plotBorderColor: \'#e0e0e0\', 
                    borderRadius: 5, 
                    shadow: {
                        color: \'rgba(0,0,0,0.5)\'
                    }
                },
                title: {
                    text: \'Budget\'
                }, tooltip: {
                    pointFormat: \'{series.name}: <b>{point.percentage:.1f}%</b>\'
                },
                accessibility: {
                    point: {
                        valueSuffix: \'%\'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: \'pointer\',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: \'Cost\',
                    colorByPoint: true,
                    data: [';
                
        $data = '';
        while ($row = $getData->fetch(PDO::FETCH_OBJ)) {
            $data .= '{ name:"' . $row->expense_category . '", y:' . $row->amount . '},';
        }

        echo rtrim($data, ','); 

        echo ']
                }]
            });
        </script>';
        
    } else {
        echo "Error in query execution.";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 


<?php
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM expenses WHERE user_id = :user_id"; 

try {
    $getData = $db->prepare($query);
      $getData->bindParam(':user_id', $user_id, PDO::PARAM_INT);
      $getData->execute();
    
    if ($getData) {
        
        echo '<script>
            Highcharts.chart(\'box2\', {
                chart: {
                    type: \'bar\', 
                    width: 500,
                    height: 350,
                    backgroundColor: \'#FFF\', 
                    plotBorderColor: \'#e0e0e0\', 
                    borderRadius: 5, 
                    shadow: {
                        color: \'rgba(0,0,0,0.5)\'
                    }
                },
                title: {
                    text: \'Budget\'
                },   
                xAxis: {
                    categories: [';

        // Build categories for X-axis
        $categories = '';
        $getData->execute(); // Reset the result set pointer
        while ($row = $getData->fetch(PDO::FETCH_OBJ)) {
            $categories .= '\'' . $row->expense_category . '\',';
        }
        echo rtrim($categories, ','); // Remove the trailing comma

        echo ']
                },
                yAxis: {
                    title: {
                        text: \'Cost\'
                    }
                },
                series: [{
                    name: \'Cost\',
                    data: [';

        // Build data for Y-axis
        $data = '';
        $getData->execute(); // Reset the result set pointer
        while ($row = $getData->fetch(PDO::FETCH_OBJ)) {
            $data .= $row->amount . ','; // Change to lowercase "amount"
        }

        echo rtrim($data, ','); 

        echo ']
                }]
            });
        </script>';
    } else {
        echo "Error in query execution.";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
</body>
</html>

