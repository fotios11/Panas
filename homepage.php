<?php
// LOAD DEPENDENCIES
require_once 'db.php';
requireLogin();

// GET USER AND DATABASE
$user = getCurrentUser();
$db = getDB();

// GET USER CURRENCY
$currency = htmlspecialchars($user['currency']);

// CALCULATE BALANCE
$stmt = $db->prepare("
  SELECT 
    IFNULL(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) -
    IFNULL(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0)
  AS balance
  FROM transactions
  WHERE user_id = ?
");
$stmt->execute([$user['id']]);
$balance = number_format($stmt->fetchColumn(), 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- PAGE META AND STYLES -->
  <meta charset="UTF-8" />
  <title>Dashboard - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="home-container">
  <!-- USER GREETING -->
  <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
  <!-- DISPLAY BALANCE -->
  <span class="balance"><?php echo $balance . ' ' . $currency; ?></span>

  <!-- DASHBOARD ACTION BUTTONS -->
  <div class="dashboard-actions">
    <form action="addexpense.php" method="get"><button class="action-button">Add Expense</button></form>
    <form action="addincome.php" method="get"><button class="action-button">Add Income</button></form>
    <form action="viewbudget.php" method="get"><button class="action-button">View Budget</button></form>
    <form action="goalsreminders.php" method="get"><button class="action-button">Set Goals</button></form>
  </div>

  <!-- LOGOUT LINK -->
  <div class="settings">
    <a href="logout.php" class="logout-button">Logout</a>
  </div>
  </div>
</body>
</html>
