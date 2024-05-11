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
    <link rel="stylesheet" href="style.css" />
    <!-- Font Awesome Cdn Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <style>
        .notification-icon {
            position: absolute;
            right: 0;
            margin: 10px 30px;
            color: rgb(110, 109, 109);
            cursor: pointer;
        }
    </style>

  
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
      <a href="notification_list.php" class="dropdown-toggle notification-icon">
    <span class="label label-pill label-danger count"></span>
    <span class="fas fa-bell" style="font-size: 25px;"></span>
</a>



        <h1>User DashBoard</h1>
     
      </div>
    <div class="main-skills">
                <div class="card">
                    <i class="fas fa-dollar-sign"></i>
                        <h3>Add Budget</h3>
                        <p><i class="fas fa-plus"></i>Add Budget</p>
                    <a href="add_Expense.php">   <button>Get Started</button></a>
                </div>

                <div class="card">
                    <i class="fas fa-dollar-sign"></i>
                        <h3>Manage Budget</h3>
                        <p>  <i class="fas fa-tasks"></i>Manage Budget</p>
                    <a href="manage_expense.php">   <button>Get Started</button></a>
                </div>
    </div><!-- main-skills-->

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
                            
                            <script>
    $(document).ready(function () {
        function load_unseen_notification(view = '') {
            $.ajax({
                url: "fetch.php",
                method: "POST",
                data: { view: view },
                dataType: "json",
                success: function (data) {
                    $('.notification-dropdown').html(data.notification);
                    if (data.unseen_notification > 0) {
                        $('.count').html(data.unseen_notification);
                    }
                }
            });
        }

        load_unseen_notification();

        $('#comment_form').on('submit', function (event) {
            event.preventDefault();
            var form_data = $(this).serialize();
            $.ajax({
                url: "insert.php",
                method: "POST",
                data: form_data,
                success: function (data) {
                    $('#comment_form')[0].reset();
                    load_unseen_notification();
                }
            });
        });

        $(document).on('click', '.dropdown-toggle', function () {
            $('.count').html('');
            load_unseen_notification('yes');
            $('.notification-dropdown').toggle(); // Toggle the display of the dropdown
        });

        // Show notification data when a notification is clicked
        $(document).on('click', '.notification-item', function () {
            var notificationId = $(this).data('id');
            // You can handle the notification data here, e.g., display it in a modal
            alert('Notification Clicked! Notification ID: ' + notificationId);
        });

        setInterval(function () {
            load_unseen_notification();
        }, 5000);
    });
</script>

  
</body>
</html>

