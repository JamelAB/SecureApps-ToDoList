<?php
# @author Name: Jamel Boumazouna ,Student Number:x21122768

// connect to database
require_once 'includes/db.php';

// Variable to store registration error msg
$msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and trim inputs to try prevent any injection attempts
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if both passwords match
    if ($password !== $confirmPassword) {
        $msg = "Passwords do not match.";
    } else {
        // Check if username already exists in the database
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        // if user exists give error message to user
        if ($existingUser) {
            $msg = "Username already taken. Please choose another.";
        } else {
            // Hash the password before storing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Inserts the user into the database
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password)");
            $stmt->execute([
                'username' => $username,
                'password' => $hashedPassword
            ]);

            // Redirect to login after successful registration
            header("Location: Login.php");
            exit();
        }
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

    <title>Register</title>
    <!-- Link to external CSS stylesheet -->
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="login-box">
    <h2>Register</h2>
    <!-- Display error message if login fails -->
    <?php if (!empty($msg)) echo "<p class='error'>$msg</p>"; ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
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
