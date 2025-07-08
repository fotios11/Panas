<?php
// Load DB and Auth
require_once 'db.php';
requireLogin();

// Get User and DB
$user = getCurrentUser();
$db = getDB();

$user_id = $user['id'];

// Ensure Goals Row Exists
$stmt = $db->prepare("SELECT COUNT(*) FROM goals WHERE user_id = ?");
$stmt->execute([$user_id]);
$exists = $stmt->fetchColumn();

if (!$exists) {
  // Insert Default Goals Row
  $insert = $db->prepare("INSERT INTO goals (user_id, target_savings, preferred_minimum_balance) VALUES (?, 0, 0)");
  $insert->execute([$user_id]);
}

try {
  // Get User Currency
  $currency = htmlspecialchars($user['currency'] ?? '$');

  // Get All Transactions
  $stmt = $db->prepare("SELECT date, description, amount, type FROM transactions WHERE user_id = ? ORDER BY date DESC");
  $stmt->execute([$user['id']]);
  $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Calculate Totals
  $totalIncome = 0;
  $totalExpense = 0;

  foreach ($transactions as $t) {
    if ($t['type'] === 'income') {
      $totalIncome += $t['amount'];
    } else if ($t['type'] === 'expense') {
      $totalExpense += $t['amount'];
    }
  }

  // Calculate Balance
  $balance = $totalIncome - $totalExpense;

  // Get Target Savings
  $stmt = $db->prepare("SELECT target_savings FROM goals WHERE user_id = ?");
  $stmt->execute([$user['id']]);
  $target_savings = $stmt->fetchColumn();

  // Calculate Progress
  $progress = null;
  if ($target_savings !== false && $target_savings > 0) {
    $progress = min(100, ($balance / $target_savings) * 100);
  }

} catch (PDOException $e) {
  // Handle DB Error
  die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Page Head -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Budget - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="home-container">
  <!-- Budget Overview Title -->
  <h2>Your Budget Overview</h2>

  <!-- Show Balance -->
  <p class="balance"></p>
    Current Balance: <?php echo $currency . number_format($balance, 2); ?>
  </p>
  <?php if ($progress !== null): ?>
    <!-- Show Savings Goal -->
    <div style="margin-bottom: 30px; text-align: center;">
    <h3>Target Savings Goal</h3>
    <p>Your target: <?php echo $currency . number_format($target_savings, 2); ?></p>
    <p>Current balance: <?php echo $currency . number_format($balance, 2); ?></p>
    <?php if ($progress >= 100): ?>
      <p style="color: green; font-weight: bold;">🎉 Congratulations! You reached your savings goal!</p>
    <?php endif; ?>
    </div>
  <?php endif; ?>
  
  <!-- Show Totals -->
  <p>
    Total Income: <span class="income"><?php echo $currency . number_format($totalIncome, 2); ?></span><br />
    Total Expenses: <span class="expense"><?php echo $currency . number_format($totalExpense, 2); ?></span>
  </p>

  <!-- Transactions Table -->
  <div class="budget-table-section">
    <h3>Transactions</h3>
    <table class="budget-table">
    <thead>
      <tr>
      <th>Date</th>
      <th>Description</th>
      <th>Type</th>
      <th>Amount</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($transactions)): ?>
      <?php foreach ($transactions as $t): 
        $typeClass = strtolower($t['type']); // income or expense
        $sign = $t['type'] === 'income' ? '+' : '-';
      ?>
        <tr>
        <td><?php echo htmlspecialchars($t['date']); ?></td>
        <td><?php echo htmlspecialchars($t['description']); ?></td>
        <td class="<?php echo $typeClass; ?>"><?php echo ucfirst($t['type']); ?></td>
        <td class="<?php echo $typeClass; ?>"><?php echo $sign . ' ' . $currency . number_format($t['amount'], 2); ?></td>
        </tr>
      <?php endforeach; ?>
      <?php else: ?>
      <tr><td colspan="4">No transactions found.</td></tr>
      <?php endif; ?>
    </tbody>
    </table>
  </div>

  <!-- Back to Home Link -->
  <div class="settings">
    <a href="homepage.php">← Back to Home</a>
  </div>
  </div>
</body>
</html>