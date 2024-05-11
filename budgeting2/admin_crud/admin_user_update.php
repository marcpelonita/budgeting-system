<?php
include '../config.php';

session_start();

$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;

if (isset($_POST['update'])) {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute the update query
    $stmt = $db->prepare("UPDATE user SET 
                        username = :username, 
                        email = :email, 
                        firstname = :firstname, 
                        lastname = :lastname, 
                        password = :password, 
                        role = :role 
                        WHERE user_id = :user_id");

    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
    $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);

    // Pass $user_id by reference
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = 'Update Successful';
        header('Location: ../admin.php?status=success');
        exit;
    } else {
        $_SESSION['message'] = 'Failed to update';
        header('Location: ../admin.php?status=error');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User Information</title>
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

        form {
            max-width: 400px; /* Adjust the maximum width as needed */
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #343a40;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #6c757d;
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
        <h1>Update User Information</h1>
    </header>

    <nav>
        <a href="admin.php">Home</a>
    </nav>

    <div class="container">
        <section>
            <div class="card">
                <form action="" method="post">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    
                    <label for="username">User Name</label>
                    <input type="text" name="username" required>

                    <label for="email">Email</label>
                    <input type="email" name="email" required>

                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" required>

                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" required>

                    <label for="password">Password</label>
                    <input type="password" name="password" required>

                    <div class="inputBox">
                        <label for="role">Role</label>
                        <select name="role" id="role" required="">
                            <option value="" selected hidden>Role</option>
                            <option value="Admin">Admin</option>
                            <option value="Viewer">Viewer</option>
                            <option value="User">User</option>
                        </select>
                    </div>

                    <button type="submit" name="update">Update</button>
                </form>
            </div>
        </section>
    </div>

    <footer>
        <!-- Footer content -->
    </footer>
</body>

</html>
