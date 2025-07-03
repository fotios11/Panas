<?php
require_once 'db.php';
requireLogin();

$user = getCurrentUser();
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST["date"];
    $description = trim($_POST["description"]);
    $amount = $_POST["amount"];
    $category = trim($_POST["category"]) ?: null;

    if (!$date || !$description || !is_numeric($amount) || $amount <= 0) {
        $error = "Please fill all fields correctly.";
    } else {
        $db = getDB();

        if (basename($_SERVER['PHP_SELF']) === 'addexpense.php') {
            // Check if expense doesn't exceed balance
            $balance = getUserBalance($user['id']);
            if ($amount > $balance) {
                $error = "Expense exceeds your current balance.";
            } else {
                $type = 'expense';
            }
        } else {
            $type = 'income';
        }

        $db = getDB();
        $stmt = $db->prepare("SELECT
          (SELECT IFNULL(SUM(amount),0) FROM transactions WHERE user_id = ? AND type = 'income') -
          (SELECT IFNULL(SUM(amount),0) FROM transactions WHERE user_id = ? AND type = 'expense')
          AS current_balance");
        $stmt->execute([$user_id, $user_id]);
        $current_balance = (float)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT preferred_minimum_balance FROM goals WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $preferred_minimum_balance = $stmt->fetchColumn();

        if ($preferred_minimum_balance === false) {
          $preferred_minimum_balance = 0; // default if not set
        }

        $new_balance = $current_balance - $amount;

        if ($new_balance < $preferred_minimum_balance) {

        $error = "Adding this expense would bring your balance below your preferred minimum balance of $preferred_minimum_balance.";
        echo "<p style='color:red;'>$error</p>";
        exit;
      }

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Expense - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="register-container">
    <h2>Add New Expense</h2>
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
    </form>

    <div class="footer">
      <a href="homepage.php">← Back to Home</a>
    </div>
  </div>
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST["date"];
    $description = $_POST["description"];
    $amount = $_POST["amount"];
    $category = $_POST["category"] ?? null;

    // TODO: Validate input and insert into database
}
?>
