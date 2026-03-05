<?php
// pages/user/login.php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../includes/config.php'; // must define $conn (PDO) and BASE_URL

// ---------- optional debug ----------
$DEBUG = isset($_GET['debug']) && $_GET['debug'] == '1';
if ($conn instanceof PDO) {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
// ------------------------------------

// Already logged in? Go home
if (!empty($_SESSION['username'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $errors[] = 'Please enter both username and password.';
    } else {
        try {
            // Aligns with your schema: ID, Username, HashedPassword (bcrypt), AccessLevel
            $stmt = $conn->prepare('SELECT ID, Username, HashedPassword, AccessLevel FROM Users WHERE Username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($DEBUG && $user) {
                echo "<pre style='background:#111;color:#0f0;padding:8px;border-radius:6px'>"
                    . "DEBUG: user loaded; hash present=" . (isset($user['HashedPassword']) ? 'yes' : 'no')
                    . "</pre>";
            }

            if ($user && isset($user['HashedPassword']) && password_verify($password, (string)$user['HashedPassword'])) {
                // Success: set session and redirect
                $_SESSION['username']     = (string)$user['Username'];
                $_SESSION['user_id']      = (int)$user['ID'];
                $_SESSION['access_level'] = (int)($user['AccessLevel'] ?? 1);

                $_SESSION['flash'] = ['type' => 'success', 'text' => 'Welcome back!'];
                header('Location: ' . BASE_URL . 'index.php');
                exit;
            } else {
                $errors[] = 'Invalid username or password.';
            }
        } catch (Throwable $e) {
            if ($DEBUG) {
                echo "<pre style='background:#111;color:#f66;padding:8px;border-radius:6px'>"
                    . "DEBUG ERROR: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
                    . "</pre>";
            }
            $errors[] = 'Login failed. Please try again shortly.';
        }
    }
}

// From here down it’s safe to output HTML (and include your template)
require_once __DIR__ . '/../../includes/template.php';
?>

<style>
    body {
        background-color: #f8f9fa;
    }

    .login-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-login {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
    }

    .card-login .card-header {
        background: none;
        border-bottom: none;
        padding-top: 2rem;
    }

    .btn-login {
        padding: 0.6rem;
        font-weight: 600;
        border-radius: 0.5rem;
    }
</style>

<div class="container login-container">
    <div class="card card-login">
        <div class="card-header text-center">
            <div class="mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-person-circle text-primary" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                </svg>
            </div>
            <h2 class="fw-bold">Welcome Back</h2>
            <p class="text-muted">Please enter your details</p>
        </div>

        <div class="card-body px-4 pb-4">
            <?php if (!empty($_SESSION['flash'])):
                $type = preg_replace('/[^a-z]/', '', $_SESSION['flash']['type']);
                $text = htmlspecialchars($_SESSION['flash']['text'], ENT_QUOTES, 'UTF-8');
                unset($_SESSION['flash']); ?>
                <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
                    <?= $text ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="alert alert-danger small" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . ($DEBUG ? '?debug=1' : '') ?>">
                <div class="form-floating mb-3">
                    <input type="text" name="username" class="form-control" id="floatingUsername" placeholder="Username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required autofocus>
                    <label for="floatingUsername">Username</label>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input id="password" name="password" type="password" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" id="eyeIcon" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 80 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-login">Sign In</button>
                </div>

                <?php if ($DEBUG): ?>
                    <div class="text-center">
                        <span class="badge rounded-pill text-bg-warning">Debug Mode Enabled</span>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <div class="card-footer text-center py-3 bg-light border-0" style="border-radius: 0 0 1rem 1rem;">
            <small class="text-muted">Forgot your password? Contact an administrator.</small>
        </div>
    </div>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function() {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle the eye icon (Optional: change SVG path to "eye-slash" if desired)
        this.classList.toggle('active');

        // Simple visual feedback: change button color when active
        if (type === 'text') {
            this.classList.replace('btn-outline-secondary', 'btn-secondary');
        } else {
            this.classList.replace('btn-secondary', 'btn-outline-secondary');
        }
    });
</script>
</body>