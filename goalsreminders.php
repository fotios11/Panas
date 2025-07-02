<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Goals & Reminders - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="home-container">
    <h2>Set Your Goals & Reminders</h2>
    
    <form action="goals.php" method="POST">

      <div class="form-group">
        <label for="target_savings">Target Savings Amount (optional)</label>
        <input type="number" id="target_savings" name="target_savings" step="0.01" placeholder="e.g. 1000.00" />
      </div>

      <div class="form-group">
        <label for="prefered_minimum_balance">Preferred Minimum Balance (optional)</label>
        <input type="number" id="prefered_minimum_balance" name="prefered_minimum_balance" step="0.01" placeholder="e.g. 200.00" />
      </div>

      <button type="submit" class="action-button">Save Settings</button>
    </form>

    <div class="footer">
      <a href="homepage.php">← Back to Home</a>
    </div>
  </div>
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $target_savings = $_POST['target_savings'] ?? null;
    $prefered_minimum_balance = $_POST['prefered_minimum_balance'] ?? null;
}
?>