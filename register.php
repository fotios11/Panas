<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - Budget Tracker</title>
  <link rel="stylesheet" href="site_theme.css" />
</head>
<body>
  <div class="register-container">
    <h2>Create a New Account</h2>
    <form action="register.php" method="POST">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required />
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>

      <div class="form-group">
        <label for="currency">Preferred Currency</label>
        <input type="text" id="currency" name="currency" required />
      </div>

      <button type="submit" class="register-button">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>
</body>
</html>
