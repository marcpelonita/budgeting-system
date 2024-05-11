<?php
session_start();
require '../config.php';

if (isset($_POST['login'])) {
    $errMsg = '';
    $email = $_POST['email'];  // Updated to match the input name in the form
    $password = $_POST['Password'];  // Updated to match the input name in the form

    if ($email == '') {
        $errMsg = 'Enter email';
    } elseif ($password == '') {
        $errMsg = 'Enter Password';
    } else {
        $errMsg = loginUser($db, $email, $password);
    }
}

function loginUser($db, $email, $password) {
    $stmt = $db->prepare('SELECT user_id, username, password, role FROM user WHERE email = :email');
    $stmt->execute(array(':email' => $email));
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data == false) {

        return "email $email not found.";
    } else {
        if (password_verify($password, $data['password'])) {
            // Store user ID in the session
            $_SESSION['user_id'] = $data['user_id'];

            // Redirect based on user role
            if ($data['role'] == 'Admin') {
                header('Location: ../admin.php?action=joined');
                exit;
            } elseif ($data['role'] == 'Viewer') {
                header('Location: ../viewer.php?action=joined');
                exit;
            } elseif ($data['role'] == 'User') {
                header('Location: ../index.php?action=joined&id=' . $data['user_id']);
                exit;
            }
        } else {
            return 'Password not match.';
        }
    }
}
?>  


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<?php
if (isset($_SESSION['message'])) {
    echo '<script>alert("' . $_SESSION['message'] . '");</script>';
    unset($_SESSION['message']); // Clear the session message
}
?>

<form action="" method="post">
    <div class="box">
        <div>
            <h2>Sign Up</h2>
            <?php if (isset($errMsg)) : ?>
                <div id="error-message" style="color:#FF0000;text-align:center;font-size:15px; margin-top:10px">
                    <?php echo $errMsg; ?></div>
            <?php endif; ?>

            <div class="inputBox">
                <input type="email" name="email" autocomplete="off">
                <label>Email</label>
            </div>

            <div class="inputBox">
                <input type="password" name="Password" value="<?php if (isset($_POST['Password'])) echo $_POST['Password'] ?>">
                <label>Password</label>
            </div>

            <div class="forgot">
                <a href="register.php">
                    <button type="button">Create Account</button>
                </a>
                <a href="forgot.php">
                    <button class="button2" type="button">Forgot Password</button>
                </a>
            </div>

            <input type="submit" name="login" value="Log In" class="submit">
        </div>
    </div>
</form>
</body>
</html>
