<?php require_once 'config.php'; 
session_start();


function authorisedAccess(bool $allow_unauth, bool $allow_VIP, bool $allow_Members){
    if (!isset($_SESSION['email_address'])) {
        // If the user's not logged in send them to the log in page.
        // Do we need this????????
        header('Location: login.php');
        exit;
    }

    if (!$allow_unauth && !isset($_SESSION['email_address'])) {
        header('Location: login.php');
        exit;
    }

    if ($allow_VIP && $_SESSION['isVIP'] == 1) {
        return true;
    }

    if ($allow_Members && $_SESSION['isMember'] == 1) {
        return true;
    }

    // If we reach this point, the user is not authorized
    header('Location: login.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <!-- You can change the title to reflect the page contents. -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>

<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Secert Club</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link <?= $current === 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
            </li>

            <?php if (!isset($_SESSION["email_address"])) : ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current === 'register.php' ? 'active' : '' ?>" href="register.php">Registration</a>
                </li>
            <?php else: ?>
                <!-- VIP-only links -->
                <?php if (!empty($_SESSION['isVIP']) && $_SESSION['isVIP'] == 1): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current === 'VIP.php' ? 'active' : '' ?>" href="VIP.php">VIP</a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>

        <!-- Right side: Welcome dropdown or Log In -->
        <?php if (isset($_SESSION['email_address'])) : ?>
            <ul class="navbar-nav">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-success" href="#" id="userDropdown" role="button"
                   data-bs-toggle="dropdown" aria-expanded="false">
                  Welcome, <?= htmlspecialchars($_SESSION['name'] ?? $_SESSION['email_address'], ENT_QUOTES, 'UTF-8') ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                  <!-- <li><a class="dropdown-item <?= $current === 'passwordreset.php' ? 'active' : '' ?>" href="passwordreset.php">Password Reset</a></li> -->
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
              </li>
            </ul>
        <?php else : ?>
            <a class="nav-link text-danger <?= $current === 'login.php' ? 'active' : '' ?>" href="login.php">Log In</a>
        <?php endif; ?>
    </div>
  </div>
</nav>
