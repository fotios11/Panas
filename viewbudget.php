<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Budget - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="home-container">
    <h2>Your Budget Overview</h2>
    <p class="balance">
      Current Balance:
      <?php
        // Example PHP logic – adjust this once you're pulling from DB
        // echo "$" . number_format($balance, 2);
        echo "$320.50";
      ?>
    </p>

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
          <?php
          // Sample PHP logic for dynamic rows
          // You’ll replace this with your DB fetch loop later
          $transactions = [
            ["date" => "2025-06-28", "description" => "Grocery shopping", "type" => "Expense", "amount" => 45.90],
            ["date" => "2025-06-27", "description" => "Freelance project", "type" => "Income", "amount" => 150.00],
            ["date" => "2025-06-25", "description" => "Monthly Rent", "type" => "Expense", "amount" => 500.00],
          ];

          if (!empty($transactions)) {
            foreach ($transactions as $t) {
              $typeClass = strtolower($t["type"]); // 'income' or 'expense'
              $sign = $t["type"] === "Income" ? "+" : "-";
              echo "<tr>
                      <td>{$t["date"]}</td>
                      <td>{$t["description"]}</td>
                      <td class=\"$typeClass\">{$t["type"]}</td>
                      <td class=\"$typeClass\">{$sign} $" . number_format($t["amount"], 2) . "</td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='4'>No transactions found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <div class="settings">
      <a href="homepage.php">← Back to Home</a>
    </div>
  </div>
</body>
</html>
