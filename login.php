<?php
# @author Name: Jamel Boumazouna ,Student Number:x21122768

// Start the session to track logged-in users for session management parts
session_start();

// connect to database
require_once __DIR__ . '/includes/db.php';

// Variable to store login feedback meesage later if theres an error
$msg = '';

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare SQL statement to prevent SQL injection attempts
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and password matches the hashed password stored in sqlite3
    if ($user && password_verify($password, $user['password_hash'])) {
        // Set session variables for logged-in state
        $_SESSION['valid'] = true;
        $_SESSION['timeout'] = time();
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
<meta http-equiv="Content-Security-Policy" content="
  default-src 'self';
  script-src 'self';
  style-src 'self';
  img-src 'self' data:;
  object-src 'none';
  base-uri 'none';
  form-action 'self';
">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<meta http-equiv="X-Frame-Options" content="DENY">

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
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
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