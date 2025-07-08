<?php
// Load database connection
require_once 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Get form data
  $username = trim($_POST["username"]);
  $email = trim($_POST["email"]);
  $password = $_POST["password"];
  $currency = trim($_POST["currency"]);
  $starting_balance = $_POST["starting_balance"] ?? 0;

  // Validate input
  if (!$username || !$email || !$password || !$currency) {
    $error = "Please fill all required fields.";
  } elseif (!is_numeric($starting_balance) || $starting_balance < 0) {
    $error = "Starting balance must be a positive number or zero.";
  } else {
    try {
      // Connect to database
      $db = getDB();

      // Insert user record
      $stmt = $db->prepare("INSERT INTO users (username, email, password, currency) VALUES (?, ?, ?, ?)");
      $stmt->execute([$username, $email, $password, $currency]);

      // Get new user ID
      $user_id = $db->lastInsertId();

      // Insert starting balance transaction
      if ($starting_balance > 0) {
        $stmt = $db->prepare("INSERT INTO transactions (user_id, date, description, amount, category, type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
          $user_id,
          date('Y-m-d'),
          'Starting Balance',
          $starting_balance,
          'Initial Deposit',
          'income'
        ]);
      }

      // Redirect to login
      header("Location: login.php");
      exit();
    } catch (PDOException $e) {
      // Handle duplicate user
      if (strpos($e->getMessage(), 'UNIQUE') !== false) {
        $error = "Username or email already exists.";
      } else {
        // Handle other database errors
        $error = "Database error: " . $e->getMessage();
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Page metadata -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="register-container">
  <h2>Create a New Account</h2>
  <!-- Registration form -->
  <form action="register.php" method="POST">
    <div class="form-group">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required />
    </div>

    <div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required />
    </div>

    <div class="form-group">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required />
    </div>

    <div class="form-group">
    <label for="currency">Preferred Currency</label>
    <input type="text" id="currency" name="currency" required />
    </div>

    <div class="form-group">
    <label for="starting_balance">Starting Balance (optional)</label>
    <input type="number" id="starting_balance" name="starting_balance" step="0.01" min="0" placeholder="0.00" />
    </div>

    <button type="submit" class="register-button">Register</button>
  </form>
  
  <!-- Show error message -->
  <?php if (!empty($error)) : ?>
    <p style="color: #ff6666;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <!-- Link to login -->
  <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>
</body>
</html>
