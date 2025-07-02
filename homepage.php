<?php
// Placeholder: Fetch username and balance from session or DB
$username = "User"; // Replace with $_SESSION['username'] etc.
$balance = 320.50;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Home - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="home-container">
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p class="balance">Your current balance: $<?php echo number_format($balance, 2); ?></p>

<div class="dashboard-actions">
  <a href="viewbudget.php"><button class="action-button">View My Budget</button></a>
  <a href="addexpense.php"><button class="action-button">Add Expense</button></a>
  <a href="addincome.php"><button class="action-button">Add Income</button></a>
  <a href="goalsreminders.php"><button class="action-button">Set Goals & Reminders</button></a>
</div>

    <div class="settings">
      <a href="#">Account Settings</a>
      <a href="logout.php" class="logout-button">Logout</a>
    </div>
  </div>
</body>
</html>
