<?php
# @author Name: Jamel Boumazouna ,Student Number:x21122768

// make sure session is called so it can then be destroyed
session_start();

// Unset all session variables,cause just password and username wasnt enough
$_SESSION = [];

// Destroy the session completely meaning user must login again
session_destroy();
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

    <title>Logged Out</title>
    <!-- Link to external CSS stylesheet -->
    <link rel="stylesheet" href="Style.css">
</head>
<body>
<div class="login-box">
    <h2>You have been logged out.</h2>
    <p>Redirecting to login page...</p>
</div>

<!-- Automatically redirect to login after 2 seconds -->
<?php header("Refresh:2; url=Login.php"); ?>

</body>
</html>