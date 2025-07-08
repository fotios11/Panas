<?php
// Load dependencies and check login
require_once 'db.php';
requireLogin();

// Get current user and database connection
$user = getCurrentUser();
$db = getDB();

// Get expense details from request
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : null;
$description = $_GET['description'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');
$category = $_GET['category'] ?? '';

// Validate amount
if ($amount === null || $amount <= 0) {
  die("Invalid amount");
}

// Get preferred minimum balance
$stmt = $db->prepare("SELECT preferred_minimum_balance FROM goals WHERE user_id = ?");
$stmt->execute([$user['id']]);
$min_balance = $stmt->fetchColumn();
if ($min_balance === false) {
  $min_balance = 0;
}

// Calculate current balance
$stmt = $db->prepare("SELECT 
  SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) -
  SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END)
  AS balance
  FROM transactions
  WHERE user_id = ?");
$stmt->execute([$user['id']]);
$current_balance = $stmt->fetchColumn();
if ($current_balance === false) {
  $current_balance = 0;
}

// Calculate new balance after expense
$new_balance = $current_balance - $amount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Page metadata and styles -->
  <meta charset="UTF-8" />
  <title>Expense Warning - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="home-container">
  <!-- Warning headline -->
  <h2>Expense Warning</h2>
  <!-- Warning message -->
  <p>This expense of <strong><?php echo htmlspecialchars($amount); ?></strong> would reduce your balance below your preferred minimum of <strong><?php echo htmlspecialchars($min_balance); ?></strong>.</p>

  <!-- Show balances -->
  <p>
    Current balance: <?php echo number_format($current_balance, 2); ?><br>
    New balance if added: <?php echo number_format($new_balance, 2); ?>
  </p>

  <!-- Proceed anyway form -->
  <form action="addexpense.php" method="post">
    <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>" />
    <input type="hidden" name="description" value="<?php echo htmlspecialchars($description); ?>" />
    <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>" />
    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>" />
    <input type="hidden" name="override" value="1" />
    <button type="submit" class="action-button">Proceed Anyway</button>
  </form>

  <!-- Cancel form -->
  <form action="addexpense.php" method="get" style="margin-top: 10px;">
    <button type="submit" class="cancel-button">Cancel</button>
  </form>
  </div>
</body>
</html>
