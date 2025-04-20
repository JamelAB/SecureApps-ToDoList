<?php
# @author Name: Jamel Boumazouna ,Student Number:x21122768

// left in to for bad DRY principle
session_start();

// connect to database but insecurely as its a new connection each time instead of using the connection in the includes folder
$pdo = new PDO('sqlite:includes/../database/database.sqlite3');

// Variable to store registration error msg
$msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // remove the trim so the inputs are not sanitisated
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if both passwords match but doesnt validate and aslong as username isnt empty its fine
    if (!empty($username) && $password === $confirm_password) {
        $sql = "INSERT INTO users (username, password_hash) VALUES ('$username', '$password')";
        $result = $pdo->exec($sql);
    }

        // if user exists give error message to user
        if ($result) {
            $msg = "account created";
        } else {
            // no password hash
            $msg = "Passwords do not match";

            // Redirect to login after successful registration
            //header("Location: Login.php");
            //exit();
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <!-- Link to external CSS stylesheet -->
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="login-box">
    <h2>Register</h2>
    <!-- Display error message if login fails -->
    <?php if (!empty($msg)) echo "<p class='error'>$msg</p>"; ?>
    <!-- removed the self submission and special chars that help prevent xss attacks -->
    <form action="" method="post">
        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" id="name" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
        </div>
        <section style="margin-left:2rem;">
            <button type="submit" name="register">Register</button>
        </section>
    </form>
</div>
</body>
</html>
