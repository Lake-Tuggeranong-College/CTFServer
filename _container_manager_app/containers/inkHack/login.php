<?php
ob_start();
include "template.php"; 

// if (!isset($_SESSION["email_address"])) {
//     header("Location: login.php");
//     exit();
// }
// session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email_address'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email_address = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) { // Assuming passwords are stored in plain text for simplicity
        $_SESSION['email_address'] = $user['email_address'];
        $_SESSION['name'] = $user['name']; // Store the user's name in the session
        $_SESSION['user_id'] = $user['id']; 
        $_SESSION['isMember'] = $user['isMember'];
        $_SESSION['isVIP'] = $user['isVIP'];
        header("Location: index.php"); // Redirect to a protected page
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}

ob_end_flush();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Login</div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email_address" class="form-label">Email address</label>
                    <input type="email" class="form-control" name="email_address" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-success">Login</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>


<!-- 
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Cartoon Auth — Login / Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <main class="page">
    <div class="card">
      <header class="card-head">
        <div class="logo">✦</div>
        <h1>Cartoon Club</h1>
        <p class="tag">Friendly login & registration</p>
      </header>

      <nav class="switcher" role="tablist" aria-label="Auth switch">
        <button id="btnLogin" class="switch active" role="tab" aria-selected="true">Login</button>
        <button id="btnRegister" class="switch" role="tab" aria-selected="false">Register</button>
      </nav>

      <section class="forms">
        <form id="loginForm" class="form" autocomplete="on" aria-hidden="false">
          <label>Email
            <input id="loginEmail" type="email" required placeholder="you@example.com">
          </label>
          <label>Password
            <input id="loginPass" type="password" required placeholder="••••••••">
          </label>
          <div class="actions">
            <button type="submit" class="btn primary">Sign in</button>
            <button type="button" id="demoFill" class="btn ghost">Demo account</button>
          </div>
        </form>

        <form id="registerForm" class="form hidden" autocomplete="on" aria-hidden="true">
          <label>Display name
            <input id="regName" type="text" required placeholder="Your nickname">
          </label>
          <label>Email
            <input id="regEmail" type="email" required placeholder="you@example.com">
          </label>
          <label>Password
            <input id="regPass" type="password" required placeholder="At least 6 chars">
          </label>
          <label>Confirm
            <input id="regPass2" type="password" required placeholder="Repeat password">
          </label>
          <div class="pw-strength" id="pwStrength" aria-hidden="true"> </div>
          <div class="actions">
            <button type="submit" class="btn primary">Create account</button>
          </div>
        </form>
      </section>

      <div id="message" class="message" role="status" aria-live="polite"></div>

      <footer class="card-foot">
        <div id="welcome" class="welcome hidden">
          <div class="avatar">🙂</div>
          <div>
            <div id="welcomeText" class="welcome-text">Welcome!</div>
            <button id="logout" class="btn ghost small">Log out</button>
          </div>
        </div>
        <small class="legal">Demo only — don't use real passwords here</small>
      </footer>
    </div> -->

    <!-- Fun background shapes -->
    <!-- <svg class="bg-shapes" aria-hidden="true" viewBox="0 0 600 400" preserveAspectRatio="none">
      <circle cx="60" cy="60" r="60" fill="#ffd4e6" opacity="0.3"/>
      <circle cx="540" cy="320" r="90" fill="#c6fff8" opacity="0.18"/>
      <rect x="420" y="20" width="90" height="90" rx="18" fill="#d6e2ff" opacity="0.14"/>
    </svg>
  </main>

  <script src="script.js"></script>
</body>
</html> -->