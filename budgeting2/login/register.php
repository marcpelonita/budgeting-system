<?php
session_start();
require '../config.php';

if (isset($_POST['register'])) {
    $errMsg = '';

    // Get data from form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate input fields
    if (empty($username))
        $errMsg .= 'Enter your Username. ';
    if (empty($firstname))
        $errMsg .= 'Enter your First Name. ';
    if (empty($lastname))
        $errMsg .= 'Enter your Last Name. ';
    if (empty($password))
        $errMsg .= 'Enter Password. ';
    if (empty($role))
        $errMsg .= 'Enter Role. ';

    if (empty($errMsg)) {
        $errMsg = registration($db, $username,$email, $firstname, $lastname, $password, $role);
    }
}

function registration($db, $username,$email, $firstname, $lastname, $password, $role) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO user (username,email, firstname, lastname, password, role) VALUES (:username, :email, :firstname, :lastname, :password, :role)";
    $stmtInsert = $db->prepare($sql);

    $stmtInsert->bindParam(':username', $username, PDO::PARAM_STR);
    $stmtInsert->bindParam(':email', $email, PDO::PARAM_STR);
    $stmtInsert->bindParam(':firstname', $firstname, PDO::PARAM_STR);
    $stmtInsert->bindParam(':lastname', $lastname, PDO::PARAM_STR);
    $stmtInsert->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmtInsert->bindParam(':role', $role, PDO::PARAM_STR);

    if ($stmtInsert->execute()) {
        $_SESSION['message'] = 'Registration successful! You can now log in.';
        header('Location: login.php?action=joined');
        exit;
    } else {
        echo "Failed to add user. Please try again.";
    }
}
?>

<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
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
        <div id="logo" class="" title="">
            <h2>Registration</h2>
            <?php
            if (isset($errMsg)) {
                echo '<div style="color:#FF0000;text-align:center;font-size:15px; margin-top:10px">' . $errMsg . '</div>';
            }
            ?>
            <div class="inputBox">
                <input type="text" name="username" required=""  autocomplete="off">
                <label>User name</label>
            </div>
            <div class="inputBox">
                <input type="email" name="email" required="" autocomplete="off">
                <label>Email</label>
            </div>
            <div class="inputBox">
                <input type="text" name="firstname" required="" autocomplete="off">
                <label>First name</label>
            </div>
            <div class="inputBox">
                <input type="text" name="lastname" required=""  autocomplete="off">
                <label>Last name</label>
            </div>
            <div class="inputBox">
                <input type="password" name="password" required="" value="<?php if (isset($_POST['password'])) echo htmlspecialchars($_POST['password']); ?>">
                <label>Password</label>
            </div>
            <div class="inputBox">
                <select name="role" id="role" required="">
                    <option value="" selected hidden>Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Viewer">Viewer</option>
                    <option value="User">User</option>
                </select>
            </div>
            <div class="forgot">
                <a href="login.php">
                    <button type="button">Already have an Account??</button>
                </a>
                <input type="submit" name='register' value="Register" class='submit'/><br />
            </div>
        </div>
    </div>
</form>
</body>
</html>
