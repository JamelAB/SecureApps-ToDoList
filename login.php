<?php
# @author Name: Jamel Boumazouna ,Student Number:x21122768

// Start the session to track logged-in users for session management parts
session_start();

// connect to database but insecurely as its a new connection each time instead of using the connection in the includes folder
$pdo = new PDO('sqlite:includes/../database/database.sqlite3');

// Variable to store login feedback meesage later if theres an error
$msg = '';

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // remove the trim so the inputs are not sanitisated
    $username = $_POST['username'];
    $password = $_POST['password'];

    // no prepared statements so its more vulnerable to sql injection
    $sql = "SELECT * FROM users WHERE username = '$username' AND password_hash = '$password'";
    $result = $pdo->query($sql);
    $user = $result->fetch(PDO::FETCH_ASSOC);

    // no password check means session created even if password wrong means sensitive data exposed
    if ($user) {
        // only checking the id and username is correct, if my notes are correct this exposes the password error message to an attacker
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];

        // Redirect to home page after successful login
        header("Location: Home.php");
        exit();
    } else {
        // Set error message if authentication fails for some reason
        $msg = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- Link to external CSS stylesheet -->
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>

        <!-- Display error message if login fails -->
        <?php if (!empty($msg)) echo "<p class='error'>$msg</p>"; ?>

        <!-- Login form -->
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
            <button type="submit" name="login">Login</button>
        </form>

        <!-- Link to registration page -->
        <p><a href="Register.php">Dont have an account? Register here</a></p>
    </div>
</body>
</html>