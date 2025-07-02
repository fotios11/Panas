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
    <form action="add_expense.php" method="POST">
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
