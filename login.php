<?php
// Include Database Connection
require_once 'db.php';

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Retrieve and Sanitize User Input
  $username = trim($_POST["username"]);
  $password = $_POST["password"];

  // Validate Input
  if (!$username || !$password) {
    $error = "Please enter username and password.";
  } else {
    // Query Database for User
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify Credentials
    if ($user && $user['password'] === $password) {
      // Set Session and Redirect
      $_SESSION['user_id'] = $user['id'];
      header("Location: homepage.php");
      exit();
    } else {
      // Set Error for Invalid Credentials
      $error = "Invalid credentials.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Page Metadata and Styles -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="login-container">
    <h2>Login to Budget Tracker</h2>
    <!-- Login Form -->
    <form action="login.php" method="POST">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>

      <button type="submit" class="login-button">Login</button>
    </form>
    <!-- Registration Link -->
    <p>Don't have an account? <a href="register.php">Register here</a></p>
  </div>
</body>
</html>