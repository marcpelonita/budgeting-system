<?php
include 'config.php';

$query = "SELECT budget_id, user_id, amount, balance FROM budgets";
$stmt = $db->query($query);
$budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        th,
        td {
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
        <h1>Viewer Dashboard</h1>
    </header>

    <nav>
        <a href="viewer.php">Home</a>
        <a href="listbudget.php">Budgets</a>
        <a href="login/logout.php">Logout</a>
    </nav>

    <div class="container">
        <section>
            <div class="card">
                <h2>Budget Information</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Budget ID</th>
                            <th>User ID</th>
                            <th>Amount</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($budgets as $budget): ?>
                            <tr>
                                <td>
                                    <?php echo $budget['budget_id']; ?>
                                </td>
                                <td>
                                    <?php echo $budget['user_id']; ?>
                                </td>
                                <td>
                                    <?php echo $budget['amount']; ?>
                                </td>
                                <td>
                                    <?php echo $budget['balance']; ?>
                                </td>
                                <td>
                                <form action="retriction/viewerupdate.php" method="post">
                                        <input type="hidden" name="budget_id" value="<?php echo $budget_id; ?>">
                                        <button type="submit">Update</button>
                                    </form>

                                    <form action="retriction/viewerdelete.php" method="post">
                                        <input type="hidden" name="budget_id" value="<?php echo $budget_id; ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <footer>
        &copy; 2023 Admin Dashboard
    </footer>

</body>

</html>