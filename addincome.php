<?php
// Load dependencies and check login
require_once 'db.php';
requireLogin();

// Get current user and initialize error
$user = getCurrentUser();
$error = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Get and sanitize form data
  $date = $_POST["date"];
  $description = trim($_POST["description"]);
  $amount = $_POST["amount"];
  $category = trim($_POST["category"]) ?: null;

  // Validate form fields
  if (!$date || !$description || !is_numeric($amount) || $amount <= 0) {
    $error = "Please fill all fields correctly.";
  } else {
    // Connect to database
    $db = getDB();

    // Check if expense or income
    if (basename($_SERVER['PHP_SELF']) === 'addexpense.php') {
      // Check expense against balance
      $balance = getUserBalance($user['id']);
      if ($amount > $balance) {
        $error = "Expense exceeds your current balance.";
      } else {
        $type = 'expense';
      }
    } else {
      $type = 'income';
    }

    // Insert transaction if no error
    if (!$error) {
      $stmt = $db->prepare("INSERT INTO transactions (user_id, date, description, amount, category, type) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->execute([$user['id'], $date, $description, $amount, $category, $type]);
      header("Location: viewbudget.php");
      exit();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Page metadata and styles -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Income - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="register-container">
  <h2>Add New Income</h2>
  <!-- Income form -->
  <form action="addincome.php" method="POST">
    <div class="form-group">
    <label for="date">Date</label>
    <input type="date" id="date" name="date" required />
    </div>

    <div class="form-group">
    <label for="description">Description</label>
    <input type="text" id="description" name="description" required />
    </div>

    <div class="form-group">
    <label for="amount">Amount</label>
    <input type="number" id="amount" name="amount" step="0.01" required />
    </div>

    <div class="form-group">
    <label for="category">Category (optional)</label>
    <input type="text" id="category" name="category" />
    </div>

    <button type="submit" class="action-button">Add Income</button>
  </form>

  <!-- Footer navigation -->
  <div class="footer">
    <a href="homepage.php">← Back to Home</a>
  </div>
  </div>
</body>
</html>
<?php
//POST handler
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $date = $_POST["date"];
  $description = $_POST["description"];
  $amount = $_POST["amount"];
  $category = $_POST["category"] ?? null;
}
?>
