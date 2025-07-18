<?php
// Load dependencies and check login
require_once 'db.php';
requireLogin();
$user = getCurrentUser();
$db = getDB();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $target_savings = $_POST['target_savings'] ?? null;
  $preferred_minimum_balance = $_POST['preferred_minimum_balance'] ?? null;
  // Validate input values
  if (($target_savings !== null && (!is_numeric($target_savings) || $target_savings < 0)) ||
    ($preferred_minimum_balance !== null && (!is_numeric($preferred_minimum_balance) || $preferred_minimum_balance < 0))) {
    $error = "Please enter valid positive numbers or leave fields empty.";
  } else {
    // Check if goal exists
    $stmt = $db->prepare("SELECT id FROM goals WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    if ($goal = $stmt->fetch(PDO::FETCH_ASSOC)) {
      // Update existing goal
      $stmt = $db->prepare("UPDATE goals SET target_savings = ?, preferred_minimum_balance = ? WHERE user_id = ?");
      $stmt->execute([$target_savings, $preferred_minimum_balance, $user['id']]);
    } else {
      // Insert new goal
      $stmt = $db->prepare("INSERT INTO goals (user_id, target_savings, preferred_minimum_balance) VALUES (?, ?, ?)");
      $stmt->execute([$user['id'], $target_savings, $preferred_minimum_balance]);
    }
    $success = "Settings saved.";
  }
}

// Load current goals for form
$stmt = $db->prepare("SELECT * FROM goals WHERE user_id = ?");
$stmt->execute([$user['id']]);
$goal = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['target_savings' => '', 'preferred_minimum_balance' => ''];
?>

<?php
// Redundant POST handler (can be removed)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $target_savings = $_POST['target_savings'] ?? null;
  $prefered_minimum_balance = $_POST['preferred_minimum_balance'] ?? null;
  
  $target_savings = (is_numeric($target_savings) && $target_savings >= 0) ? floatval($target_savings) : null;
  $preferred_minimum_balance = (is_numeric($preferred_minimum_balance) && $preferred_minimum_balance >= 0) ? floatval($preferred_minimum_balance) : null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Page metadata and styles -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Goals & Reminders - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="home-container">
  <h2>Set Your Goals & Reminders</h2>
  
  <!-- Goals form -->
  <form action="goalsreminders.php" method="POST">

    <div class="form-group">
    <label for="target_savings">Target Savings Amount (optional)</label>
    <input type="number" id="target_savings" name="target_savings" step="0.01" placeholder="e.g. 1000.00" />
    </div>

    <div class="form-group">
    <label for="preferred_minimum_balance">Preferred Minimum Balance (optional)</label>
    <input type="number" id="preferred_minimum_balance" name="preferred_minimum_balance" step="0.01" placeholder="e.g. 200.00" />
    </div>

    <button type="submit" class="action-button">Save Settings</button>
  </form>

  <!-- Footer navigation -->
  <div class="footer">
    <a href="homepage.php">← Back to Home</a>
  </div>
  </div>
</body>
</html>
