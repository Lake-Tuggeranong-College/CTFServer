<?php
// includes/template.php (top of file)

// Start session only if not already active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Optional: buffering can help avoid "headers already sent" if
// anything below echoes before redirects on other pages.
// ob_start();

require_once __DIR__ . '/config.php';  // keep this above any HTML output

define('USER_ACCESS_LEVEL', 1);
define('ADMIN_ACCESS_LEVEL', 2);

/* ---------------------- Utilities ---------------------- */
function set_flash(string $type, string $text): void
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}

function take_flash(): ?array
{
    if (empty($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

function sanitise_data(string $data): string
{
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Gatekeeper for page access.
 * Set the allows for this page below where it's called.
 */
function authorisedAccess(bool $allow_unauth, bool $allow_user, bool $allow_admin): bool
{
    if (!isset($_SESSION["username"])) {
        if (!$allow_unauth) {
            set_flash('danger', 'Access Denied');
            return false;
        }
        return true;
    }

    $level = $_SESSION["access_level"] ?? null;

    if ($level === USER_ACCESS_LEVEL && !$allow_user) {
        set_flash('danger', 'Access Denied');
        return false;
    }
    if ($level === ADMIN_ACCESS_LEVEL && !$allow_admin) {
        set_flash('danger', 'Access Denied');
        return false;
    }
    return true;
}


/**
 * Redirects reliably even if headers have already been sent.
 */
function smart_redirect($url) {
    if (!headers_sent()) {
        header("Location: " . $url);
        exit;
    } else {
        echo '<script type="text/javascript">';
        echo 'window.location.href="' . $url . '";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
        echo '</noscript>';
        exit;
    }
}


/* ---------------------- Page guard ---------------------- */
/*
   Choose one for this page:
   - Public:     authorisedAccess(true,  true,  true)
   - Members:    authorisedAccess(false, true,  true)
   - Admin only: authorisedAccess(false, false, true)
*/
if (!authorisedAccess(true, true, true)) { // change flags as needed
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/* ---------------------- Navbar helpers ---------------------- */
$userScore = 0;
if (isset($_SESSION['username'])) {
    try {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $stmt = $conn->prepare("SELECT Score FROM Users WHERE ID = ?");
            $stmt->execute([$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['Score'])) $userScore = (int)$row['Score'];
        }
    } catch (Throwable $e) {
        // Optional: log error; keep $userScore = 0
    }
}
$flash = take_flash();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber City</title>

    <script type="text/javascript">
        function doUnauthRedirect() {
            location.replace("http://10.177.200.71/index.html");
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Bootstrap + CSS -->
    <link href="<?= BASE_URL; ?>assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL; ?>assets/css/styles.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL; ?>assets/css/moduleList.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL; ?>assets/css/leaderboard.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL; ?>assets/css/editAccount.css">
    <link rel="icon" type="image/png" href="<?= BASE_URL; ?>assets/img/CCLogo.png">
</head>

<body class="bg-light text-black">
    <nav class="navbar navbar-expand-lg bg-body-tertiary py-2 shadow-sm sticky-top">
    <div class="container-fluid">
        <a href="<?= BASE_URL; ?>index.php" class="navbar-brand d-flex align-items-center ms-lg-4">
            <img src="<?= BASE_URL; ?>assets/img/logoGeneric.png" alt="CyberCity Home" style="height: 60px; width: auto;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNavbar" aria-controls="mainNavbar"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 text-dark fw-medium" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Project Version
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        <?php
                        try {
                            $stmt = $conn->query("SELECT project_id, project_name FROM CyberCity.Projects");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<li><a class="dropdown-item" href="' . BASE_URL . 'pages/challenges/challengesList.php?projectID=' . $row['project_id'] . '">' . htmlspecialchars($row['project_name']) . '</a></li>';
                            }
                        } catch (PDOException $e) {
                            echo '<li><span class="dropdown-item text-danger small">Error loading projects</span></li>';
                        }
                        ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a href="http://10.177.202.196/CyberCityDocs/welcome.html" class="dropdown-item" target="_blank">Tutorials</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL; ?>pages/leaderboard/leaderboard.php" class="nav-link px-3 text-dark fw-medium">Leaderboard</a>
                </li>

                 <li class="nav-item">
<a href="//<?= $_SERVER['SERVER_NAME'] ?>:8001" class="nav-link px-3 text-dark fw-medium" target="_blank">Documentation</a>                </li>
                

                <li class="nav-item">
                    <a href="https://forms.gle/jgYrmMZesgtVhBZ39" class="nav-link px-3 text-dark fw-medium" target="_blank">Feedback</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                
                <li class="nav-item dropdown me-2">
                    <a class="nav-link text-dark px-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Accessibility Features">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-universal-access" viewBox="0 0 16 16">
                            <path d="M9.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0M6 5.5l-4.535-.442A.531.531 0 0 1 1 4.531V3.92a.53.53 0 0 1 .597-.527l10.465.803a.53.53 0 0 1 .517.527v.511a.53.53 0 0 1-.453.524L9.5 6.5V9.5l3.5 7h-1.5L9 11l-1 5H6l-1-5-2.5 5.5h-1.5l3.5-7V5.5z"/>
                        </svg>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-3 shadow border-0" style="min-width: 280px;">
                        <li class="mb-2">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Fonts</small>
                            <div class="btn-group w-100 mt-1">
                                <button class="btn btn-sm btn-outline-secondary accessibility-font" data-size="small">Small</button>
                                <button class="btn btn-sm btn-outline-secondary accessibility-font" data-size="medium">Med</button>
                                <button class="btn btn-sm btn-outline-secondary accessibility-font" data-size="large">Large</button>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Theme Controls</small>
                            <button id="modeToggle" class="btn btn-sm btn-dark w-100 mt-2 mb-1">Toggle Dark Mode</button>
                            <button id="toggleContrast" class="btn btn-sm btn-outline-dark w-100">High Contrast</button>
                        </li>
                    </ul>
                </li>

                <?php if (isset($_SESSION['username'])): ?>
                    
                    <?php if (($_SESSION['access_level'] ?? null) == ADMIN_ACCESS_LEVEL): ?>
                        <li class="nav-item dropdown me-1">
                            <a class="nav-link text-danger px-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Admin Panel">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-shield-lock-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.8 11.8 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7 7 0 0 0 1.048-.625 11.8 11.8 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.54 1.54 0 0 0-1.044-1.263 63 63 0 0 0-2.887-.87C9.843.266 8.69 0 8 0m0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5"/>
                                </svg>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><h6 class="dropdown-header">System Admin</h6></li>
                                <li><a href="<?= BASE_URL; ?>pages/admin/userList.php" class="dropdown-item">User List</a></li>
                                <li><a href="<?= BASE_URL; ?>pages/admin/contactpage.php" class="dropdown-item">View Contacts</a></li>
                                <li><a href="<?= BASE_URL; ?>pages/admin/challengeRegister.php" class="dropdown-item">Create Challenges</a></li>
                                <li><a href="<?= BASE_URL; ?>pages/admin/createCategory.php" class="dropdown-item">Create Category</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a href="<?= BASE_URL; ?>pages/admin/resetGame.php" class="dropdown-item text-danger">Reset Game</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item dropdown me-3">
                        <a href="#" class="nav-link dropdown-toggle text-primary fw-bold" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a href="<?= BASE_URL; ?>pages/user/editAccount.php" class="dropdown-item">Edit Account</a></li>
                            <li><span class="dropdown-item-text text-muted small">Score: <?= htmlspecialchars((string)$userScore); ?></span></li>
                            <?php if (($_SESSION['access_level'] ?? null) == USER_ACCESS_LEVEL): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a href="<?= BASE_URL; ?>pages/contactUs/contact.php" class="dropdown-item">Contact Us</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= BASE_URL; ?>pages/user/logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">Logout</a>
                    </li>

                <?php else: ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL; ?>pages/user/register.php" class="btn btn-link btn-sm text-decoration-none text-muted px-2">Register</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL; ?>pages/user/login.php" class="btn btn-primary btn-sm rounded-pill px-4 ms-2">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

    <!-- Flash -->
    <?php if ($flash): ?>
        <?php
        $type = preg_replace('/[^a-z]/', '', $flash['type']); // simple whitelist
        $text = htmlspecialchars($flash['text'], ENT_QUOTES, 'UTF-8');
        ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $type ?> mb-3" role="alert"><?= $text ?></div>
        </div>
    <?php endif; ?>

    <!-- Your page content goes here -->

    <!-- Bootstrap JS -->
    <script src="<?= BASE_URL; ?>assets/js/bootstrap/bootstrap.bundle.min.js"></script>

    <script>
        // Accessibility: Theme Mode Toggle
        const modeToggleBtn = document.getElementById('modeToggle');
        const body = document.body;

        function updateWideBoxClasses(theme) {
            const wideBoxes = document.querySelectorAll(theme === 'light' ? '.wideBoxDark' : '.wideBox');
            wideBoxes.forEach(box => {
                box.classList.replace(theme === 'light' ? 'wideBoxDark' : 'wideBox',
                    theme === 'light' ? 'wideBox' : 'wideBoxDark');
            });
        }

        function applyTheme(theme) {
            if (theme === 'dark') {
                body.classList.add('bg-dark', 'text-white');
                body.classList.remove('bg-light', 'text-black');
                updateWideBoxClasses('dark');
                modeToggleBtn.textContent = 'Switch to Light Mode';
            } else {
                body.classList.add('bg-light', 'text-black');
                body.classList.remove('bg-dark', 'text-white');
                updateWideBoxClasses('light');
                modeToggleBtn.textContent = 'Switch to Dark Mode';
            }
            localStorage.setItem('theme', theme);
        }

        // On page load, apply saved theme or default to light
        const savedTheme = localStorage.getItem('theme') || 'light';
        applyTheme(savedTheme);

        modeToggleBtn.addEventListener('click', () => {
            const currentTheme = localStorage.getItem('theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(newTheme);
        });
    </script>

    <script>
        // Load saved preferences or set defaults for accessibility features
        const savedFont = localStorage.getItem('accessibilityFont') || 'medium';
        const savedLineSpacing = localStorage.getItem('accessibilityLineSpacing') || '1';
        const savedContrast = localStorage.getItem('accessibilityContrast') === 'true';

        function applyAccessibilitySettings() {
            document.body.classList.remove('font-small', 'font-medium', 'font-large');
            document.body.classList.add('font-' + savedFont);

            document.body.classList.remove('line-spacing-1', 'line-spacing-1-5', 'line-spacing-2');
            if (savedLineSpacing === '1') {
                document.body.classList.add('line-spacing-1');
            } else if (savedLineSpacing === '1.5') {
                document.body.classList.add('line-spacing-1-5');
            } else if (savedLineSpacing === '2') {
                document.body.classList.add('line-spacing-2');
            }

            if (savedContrast) {
                document.body.classList.add('high-contrast');
            } else {
                document.body.classList.remove('high-contrast');
            }
        }

        applyAccessibilitySettings();

        // Highlight active buttons
        document.querySelectorAll('.accessibility-font').forEach(button => {
            if (button.getAttribute('data-size') === savedFont) {
                button.classList.add('active');
            }
        });
        document.querySelectorAll('.accessibility-line').forEach(button => {
            if (button.getAttribute('data-spacing') === savedLineSpacing) {
                button.classList.add('active');
            }
        });
        if (savedContrast) {
            document.getElementById('toggleContrast').classList.add('active');
        }

        // Font size buttons
        document.querySelectorAll('.accessibility-font').forEach(button => {
            button.addEventListener('click', () => {
                const size = button.getAttribute('data-size');
                localStorage.setItem('accessibilityFont', size);
                location.reload(); // reload to apply changes cleanly
            });
        });

        // Line spacing buttons
        document.querySelectorAll('.accessibility-line').forEach(button => {
            button.addEventListener('click', () => {
                const spacing = button.getAttribute('data-spacing');
                localStorage.setItem('accessibilityLineSpacing', spacing);
                location.reload();
            });
        });

        // High contrast toggle
        document.getElementById('toggleContrast').addEventListener('click', () => {
            const current = localStorage.getItem('accessibilityContrast') === 'true';
            localStorage.setItem('accessibilityContrast', !current);
            location.reload();
        });
    </script>
</body>

</html>