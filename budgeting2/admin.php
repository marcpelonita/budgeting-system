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
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar" class="js-sidebar">
            <!-- Content For Sidebar -->
            <div class="h-100">
                <div class="sidebar-logo">
                    <a href="#">Admin Budgeting</a>
                </div>
                <ul class="sidebar-nav">
                    <li class="sidebar-header">
                        Admin Elements
                    </li>
                    <li class="sidebar-item">
                        <a href="admin.php" class="sidebar-link">
                            <i class="fa-solid fa-list pe-2"></i>
                            Dashboard
                        </a>
                    </li>

                    <!---User--->
                    <li class="sidebar-item">
                        <a href="#user" class="sidebar-link collapsed"><i class="fa-regular fa-user pe-2"></i>
                            User
                        </a>
                    </li>
                    <!---User--->

                    <!---Budget--->
                    <li class="sidebar-item">
                        <a href="#budget" class="sidebar-link collapsed"><i class="fas fa-wallet pe-2"></i>
                            Budget
                        </a>
                    </li>
                    <!---Budget--->

                    <!---expenses--->
                    <li class="sidebar-item">
                        <a href="#expenses" class="sidebar-link collapsed"><i class="fa fa-money pe-2"></i>
                        Expenses
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="login/logout.php" class="sidebar-link collapsed"><i class="fa fa-user pe-2"></i>
                        Logout
                        </a>
                    </li>
                    <!---expenses--->

                </ul>
            </div>
        </aside>
        <div class="main">
            <nav class="navbar navbar-expand px-3 border-bottom">
                <button class="btn" id="sidebar-toggle" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
               
            </nav>
            <main class="content px-3 py-2">
                <div class="container-fluid">
                    <div class="mb-3">
                        <h4>Admin Dashboard</h4>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 d-flex">
                            <div class="card flex-fill border-0 illustration">
                                <div class="card-body p-0 d-flex flex-fill">
                                    <div class="row g-0 w-100">
                                        <div class="col-6">
                                            <div class="p-3 m-1">
                                                <h4>Welcome Back, Admin</h4>
                                                <p class="mb-0">Admin Dashboard</p>
                                            </div>
                                        </div>
                                        <div class="col-6 align-self-end text-end">
                                            <img src="image/admin.png" class="img-fluid illustration-img"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users Information -->
                    <div class="card border-0" id="user">
                        <div class="card-header">
                            <h5 class="card-title">
                                Users Information
                            </h5>
                        </div>
                        

                <?php
                 
                    try {

                        $stmt = $db->prepare('SELECT user_id, username, email, firstname, lastname, password, role FROM user');
                        $stmt->execute();

                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $users = array();
                        echo "Error: " . $e->getMessage();
                    }
                ?>
                <section class="table-responsive">
                <div class="card">
                        <table class="table">
                    <thead>
                        <tr>
                            <th>USER_ID</th>
                            <th>USERNAME</th>
                            <th>EMAIL</th>
                            <th>FIRSTNAME</th>
                            <th>LASTNAME</th>
                            <th>PASSWORD</th>
                            <th>ROLE</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $row): ?>
                            <tr>
                                <td><?php echo isset($row['user_id']) ? $row['user_id'] : ''; ?></td>
                                <td><?php echo isset($row['username']) ? $row['username'] : ''; ?></td>
                                <td><?php echo isset($row['email']) ? $row['email'] : ''; ?></td>
                                <td><?php echo isset($row['firstname']) ? $row['firstname'] : ''; ?></td>
                                <td><?php echo isset($row['lastname']) ? $row['lastname'] : ''; ?></td>
                                <td><?php echo isset($row['password']) ? $row['password'] : ''; ?></td>
                                <td><?php echo isset($row['role']) ? $row['role'] : ''; ?></td>
                                <td>
                                    <form action="admin_crud/admin_user_update.php" method="post">
                                        <input type="hidden" name="user_id" value="<?php echo isset($row['user_id']) ? $row['user_id'] : ''; ?>">
                                        <button type="submit">Update</button>
                                    </form>

                                  
                                    <a href="#" onclick="confirmDeleteUser(<?php echo $row['user_id']; ?>)">Delete</a>
                                  
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

      <!-- Budget Information -->
        <div class="card border-0" id="budget">
                        <div class="card-header">
                            <h5 class="card-title">
                                Budget Pending
                            </h5>
                        </div>
                <?php
                    try {
                        $stmt = $db->prepare("SELECT budget_id, user_id, amount, balance, status FROM budgets WHERE status = 'Pending'");
                        $stmt->execute();
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $users = array();
                        echo "Error: " . $e->getMessage();
                    }
                ?>
                <section class="table-responsive">
                <div class="card">
                        <table class="table">
                    <thead>
                        <tr>
                            <th>BUDGET_ID</th>
                            <th>USER_ID</th>
                            <th>AMOUNT</th>
                            <th>BALANCE</th>
                            <th>Status</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $row): ?>
                            <tr>           
                                <td><?php echo isset($row['budget_id']) ? $row['budget_id'] : ''; ?></td>
                                <td><?php echo isset($row['user_id']) ? $row['user_id'] : ''; ?></td>
                                <td><?php echo isset($row['amount']) ? $row['amount'] : ''; ?></td>
                                <td><?php echo isset($row['balance']) ? $row['balance'] : ''; ?></td>
                                <td><?php echo isset($row['status']) ? $row['status'] : ''; ?></td>
                                <td>   
                                    <form action="#" method="post">
                                        <input type="hidden" name="budget_id" value="<?php echo isset($row['budget_id']) ? $row['budget_id'] : ''; ?>">
                                        <a href="#" onclick="confirmApprove(<?php echo $row['budget_id']; ?>)">Approve</a>
                                    </form>

                                       
                                    <a href="#" onclick="confirmDeleteBudget(<?php echo $row['budget_id']; ?>)">Delete</a>
                                    
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


            <div class="card border-0" id="budget">
                        <div class="card-header">
                            <h5 class="card-title">
                                Budget Approved
                            </h5>
                        </div>
                <?php
                    try {
                        $stmt = $db->prepare("SELECT budget_id, user_id, amount, balance, status FROM budgets WHERE status = 'approve'");
                        $stmt->execute();
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $users = array();
                        echo "Error: " . $e->getMessage();
                    }
                ?>
                <section class="table-responsive">
                <div class="card">
                        <table class="table">
                    <thead>
                        <tr>
                            <th>BUDGET_ID</th>
                            <th>USER_ID</th>
                            <th>AMOUNT</th>
                            <th>BALANCE</th>
                            <th>Status</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $row): ?>
                            <tr>           
                                <td><?php echo isset($row['budget_id']) ? $row['budget_id'] : ''; ?></td>
                                <td><?php echo isset($row['user_id']) ? $row['user_id'] : ''; ?></td>
                                <td><?php echo isset($row['amount']) ? $row['amount'] : ''; ?></td>
                                <td><?php echo isset($row['balance']) ? $row['balance'] : ''; ?></td>
                                <td><?php echo isset($row['status']) ? $row['status'] : ''; ?></td>
                                <td>   
                                    
                               
                                <a href="#" onclick="confirmUpdateBudget(<?php echo $row['budget_id']; ?>)">Update</a>


                                    <a href="#" onclick="confirmDeleteBudget(<?php echo $row['budget_id']; ?>)">Delete</a>
                                   

                                 
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>    
        </section>


         <!-- expenses information -->
         <div class="card border-0" id="expenses">
                        <div class="card-header">
                            <h5 class="card-title">
                             Expenses Information
                            </h5>
                            
                        </div>
                        

                <?php

                    try {

                        $stmt = $db->prepare('SELECT expenses_id, user_id, budget_id, expense_category,amount FROM expenses');
                        $stmt->execute();

                        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $expenses = array();
                        echo "Error: " . $e->getMessage();
                    }
                ?>
                <section class="table-responsive" >
                <div class="card">
                        <table class="table">
                    <thead>
                        <tr>
                            <th>EXPENSES_ID</th>
                            <th>USER_ID</th>
                            <th>BUDGET_ID</th>
                            <th>EXPENSE_CATEGORY</th>
                            <th>AMOUNT</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($expenses as $row): ?>
                        <tr>
                            <td><?php echo isset($row['expenses_id']) ? $row['expenses_id'] : ''; ?></td>
                            <td><?php echo isset($row['user_id']) ? $row['user_id'] : ''; ?></td>
                            <td><?php echo isset($row['budget_id']) ? $row['budget_id'] : ''; ?></td>
                            <td><?php echo isset($row['expense_category']) ? $row['expense_category'] : ''; ?></td>
                            <td><?php echo isset($row['amount']) ? $row['amount'] : ''; ?></td>
                            <td>
                                <form action="admin_crud/admin_expenses_update.php" method="post">
                                    <input type="hidden" name="user_id" value="<?php echo isset($row['user_id']) ? $row['user_id'] : ''; ?>">
                                    <input type="hidden" name="expenses_id" value="<?php echo isset($row['expenses_id']) ? $row['expenses_id'] : ''; ?>">
                                    <button type="submit">Update</button>
                                </form>
                           
                                <form method="post" action="admin_crud/admin_expenses_delete.php" id="delete_form_<?php echo htmlspecialchars($row['expenses_id']); ?>" onsubmit="return confirmDelete(<?php echo htmlspecialchars($row['expenses_id']); ?>, <?php echo htmlspecialchars($row['user_id']); ?>);">
                                    <input type="hidden" name="user_id" value="<?php echo isset($row['user_id']) ? $row['user_id'] : ''; ?>">
                                    <input type="hidden" name="expenses_id" value="<?php echo isset($row['expenses_id']) ? htmlspecialchars($row['expenses_id']) : ''; ?>">
                                    <input type="submit" name="post" class="btn btn-info" value="Delete" />
                                </form>

                                <script>
                                    function confirmDelete(expenses_id, user_id) {
                                        var result = confirm("Are you sure you want to delete this record?");
                                        if (result) {
                                            document.getElementById("delete_form_" + expenses_id).submit();
                                        }
                                        return false;
                                    }
                                </script>
                                                

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <script>
      $(document).ready(function () {

function load_unseen_notification(view = '') {
    $.ajax({
        url: "fetch.php",
        method: "POST",
        data: {
            view: view
        },
        dataType: "json",
        success: function (data) {
            $('.dropdown-menu').html(data.notification);
            if (data.unseen_notification > 0) {
                $('.count').html(data.unseen_notification);
            }
        }
    });
}

load_unseen_notification();



$(document).on('click', '.dropdown-toggle', function () {
    $('.count').html('');
    load_unseen_notification('yes');
});

setInterval(function () {
    load_unseen_notification();
}, 5000);

});
    </script>



                    </div>
                </div>
            </main>
            <a href="#" class="theme-toggle">
                <i class="fa-regular fa-moon"></i>
                <i class="fa-regular fa-sun"></i>
            </a>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <a href="#" class="text-muted">
                                    <strong>Admin Budgeting</strong>
                                </a>
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="#" class="text-muted">Contact</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="text-muted">About Us</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="text-muted">Terms</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>

</html>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


<script>
  function confirmApprove(budget_id) {
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
      
        approveBooking(budget_id);
      }
    });
  }

  function approveBooking(budget_id) {

    var xhr = new XMLHttpRequest();
    var url = 'approve.php?budget_id=' + budget_id;
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        var response = JSON.parse(xhr.responseText);
        if (response.status === 'success') {
          Swal.fire('Booking Approved Successfully', '', 'success');
          window.location.reload();
        } else {
          Swal.fire('Failed to approve booking', response.message, 'error');
        }
      }
    };


    xhr.send();
  }
</script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


<script>
    function confirmDelete(expenses_id, user_id) {
        var result = confirm("Are you sure you want to delete this record?");
        if (result) {
           
            document.getElementById("delete_form_" + expenses_id _ user_id).submit();
        }
       
        return false;
    }
</script>





<script>
function confirmDeleteUser(user_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "All your budget and expenses data will delete as well.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then(function (result) {
        if (result.isConfirmed) {
            window.location.href = `admin_crud/admin_user_delete.php?user_id=${user_id}`;
        }
    });
}
</script>


<script>
function confirmDeleteBudget(budget_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "All your expenses data will delete as well.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then(function (result) {
        if (result.isConfirmed) {
            window.location.href = `admin_crud/admin_budget_delete.php?budget_id=${budget_id}`;
        }
    });
}
</script>

<script>
function confirmUpdateBudget(budget_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "You want to change the Approve to Pending",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then(function (result) {
        if (result.isConfirmed) {
            window.location.href = `admin_crud/admin_update_budget.php?budget_id=${budget_id}`;
        }
    });
}
</script>
