<?php
// Includes and Authentication
require_once 'db.php';
requireLogin();

// User and DB Setup
$user = getCurrentUser();
$db = getDB();
$error = null;

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Get Form Data
  $date = $_POST["date"];
  $description = trim($_POST["description"]);
  $amount = $_POST["amount"];
  $category = trim($_POST["category"]) ?: null;
  $override = isset($_POST["override"]) ? (bool)$_POST["override"] : false;

  // Validate Input
  if (!$date || !$description || !is_numeric($amount) || $amount <= 0) {
    $error = "Please fill all fields correctly.";
  } else {
    $user_id = $user['id'];
    $type = 'expense';

    // Calculate Current Balance
    $stmt = $db->prepare("SELECT 
      (SELECT IFNULL(SUM(amount),0) FROM transactions WHERE user_id = ? AND type = 'income') -
      (SELECT IFNULL(SUM(amount),0) FROM transactions WHERE user_id = ? AND type = 'expense')
      AS current_balance");
    $stmt->execute([$user_id, $user_id]);
    $current_balance = (float)$stmt->fetchColumn();

    // Fetch Preferred Minimum Balance
    $stmt = $db->prepare("SELECT preferred_minimum_balance FROM goals WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $preferred_minimum_balance = $stmt->fetchColumn();
    if ($preferred_minimum_balance === false) {
      $preferred_minimum_balance = 0;
    }

    // Calculate New Balance
    $new_balance = $current_balance - $amount;

    // Check Minimum Balance and Redirect if Needed
    if (!$override && $new_balance < $preferred_minimum_balance) {
      // Redirect to Confirmation Page
      header("Location: confirm_expense.php?amount=" . urlencode($amount) .
           "&description=" . urlencode($description) .
           "&date=" . urlencode($date) .
           "&category=" . urlencode($category));
      exit;
    }

    // Insert Expense
    if (!$error) {
      $stmt = $db->prepare("INSERT INTO transactions (user_id, date, description, amount, category, type)
                  VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->execute([$user_id, $date, $description, $amount, $category, $type]);
      header("Location: viewbudget.php");
      exit();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Head Section -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Expense - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="register-container">
  <h2>Add New Expense</h2>

  <!-- Error Message -->
  <?php if ($error): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <!-- Expense Form -->
  <form action="addexpense.php" method="POST">
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
    <button type="submit" class="action-button">Add Expense</button>
  
  <!-- Back to Home Link -->
  <div class="settings">
    <a href="homepage.php">← Back to Home</a>
  </div>

  </form>
