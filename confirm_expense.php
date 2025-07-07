<?php
require_once 'db.php';
requireLogin();

$user = getCurrentUser();
$db = getDB();

$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : null;
$description = $_GET['description'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');
$category = $_GET['category'] ?? '';

if ($amount === null || $amount <= 0) {
    die("Invalid amount");
}

// Fetch preferred minimum balance
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

$new_balance = $current_balance - $amount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Expense Warning - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="home-container">
    <h2>⚠️ Expense Warning</h2>
    <p>This expense of <strong><?php echo htmlspecialchars($amount); ?></strong> would reduce your balance below your preferred minimum of <strong><?php echo htmlspecialchars($min_balance); ?></strong>.</p>

    <p>
      Current balance: <?php echo number_format($current_balance, 2); ?><br>
      New balance if added: <?php echo number_format($new_balance, 2); ?>
    </p>

    <form action="addexpense.php" method="post">
      <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>" />
      <input type="hidden" name="description" value="<?php echo htmlspecialchars($description); ?>" />
      <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>" />
      <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>" />
      <input type="hidden" name="override" value="1" />
      <button type="submit" class="action-button">✅ Proceed Anyway</button>
    </form>

    <form action="addexpense.php" method="get" style="margin-top: 10px;">
      <button type="submit" class="cancel-button">❌ Cancel</button>
    </form>
  </div>
</body>
</html>
