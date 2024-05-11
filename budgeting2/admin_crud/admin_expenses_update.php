<?php
include '../config.php';

$expense_category = isset($_POST['expense_category']) ? $_POST['expense_category'] : '';

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

    // Close the statement
    $stmt->closeCursor();
} else {
    echo "User ID or Expenses ID not provided.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateExpenses"])) {
    // Validate and sanitize inputs
    $expense_category = filter_input(INPUT_POST, 'expense_category', FILTER_SANITIZE_STRING);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_STRING);

    // Update expenses information
    $updateQuery = "UPDATE expenses 
                    SET expense_category = :expense_category, amount = :amount 
                    WHERE user_id = :user_id AND expenses_id = :expenses_id";

    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':expense_category', $expense_category, PDO::PARAM_STR);
    $updateStmt->bindParam(':amount', $amount, PDO::PARAM_STR);
    $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $updateStmt->bindParam(':expenses_id', $expenses_id, PDO::PARAM_INT);

    // Check if the update was successful
    if ($updateStmt->execute()) {

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



        echo "<script>alert('Update Successfully'); window.location.href='../admin.php';</script>";
        exit();
    } else {
        echo "Error updating user information.";
    }

    // Close the statement
    $updateStmt->closeCursor();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Expenses Information</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        header {
            background-color: #343a40;
            color: #fff;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav {
            background-color: #495057;
            color: #fff;
            padding: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px;
            margin: 0 10px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #6c757d;
        }

        section {
            padding: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #dee2e6;
            text-align: left;
        }

        th {
            background-color: #343a40;
            color: #fff;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>

    <header>
        <h1>Update Expenses Information</h1>
    </header>

    <nav>
        <a href="admin.php">Home</a>
    </nav>

    <div class="container">
        <section>
            <div class="card">
                <h2>Update Expenses Information</h2>
                <form action="" method="post">
                    <!-- Include necessary hidden fields -->
                    <input type="hidden" name="user_id" value="<?php echo $result['user_id']; ?>">
                    <input type="hidden" name="expenses_id" value="<?php echo $result['expenses_id']; ?>">
                    
                    <label for="LicenseNumber">Expenses Category:</label>
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
                    <!-- <input type="text" name="expense_category" value="<?php echo $result['expense_category']; ?>" required> -->

                    <label for="LicenseNumber">Amount:</label>
                    <input type="text" name="amount" value="<?php echo $result['amount']; ?>" required>

                    <button type="submit" name="updateExpenses">Update</button>
                </form>
            </div>
        </section>
    </div>


</body>

</html>