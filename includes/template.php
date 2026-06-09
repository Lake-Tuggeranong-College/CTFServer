<?php
// includes/template.php (top of file)

// Start session only if not already active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/config.php';  // keep this above any HTML output

define('USER_ACCESS_LEVEL', 1);
define('ADMIN_ACCESS_LEVEL', 2);

/* ---------------------- Utilities ---------------------- */
function set_flash(string $type, string $text): void
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}

// Check for and consume flash notifications
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
function smart_redirect($url)
{
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
    <title>Cyber City Arena</title>

    <script type="text/javascript">
        function doUnauthRedirect() {
            location.replace("http://10.177.200.71/index.html");
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Google Fonts Connection & Embed for Monospace Only -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap + CSS -->
    <link href="<?= BASE_URL; ?>assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL; ?>assets/css/styles.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL; ?>assets/css/moduleList.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL; ?>assets/css/leaderboard.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL; ?>assets/css/editAccount.css">
    <link class="cc-favicon" rel="icon" type="image/png" href="<?= BASE_URL; ?>assets/img/CCLogo.png">

    <!-- Premium UI, Gamified Cybersecurity Elements, & Accessibility Layout Styles -->
    <style>
        /* Base Premium Cyber Typography - Standardizing on Helvetica Neue */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            letter-spacing: -0.015em;
            background-color: #0b0f12 !important; /* Cyber arena slate-black default */
            color: #cbd5e1; /* Premium soft silver-slate default instead of stark pure white */
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        code, pre, kbd, samp, .font-monospace, [style*="font-family: 'Courier New'"] {
            font-family: 'JetBrains Mono', SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
            letter-spacing: 0 !important;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Helvetica Neue', Helvetica, Arial, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #f8fafc; /* Crisp, bright slate-50 header color */
        }

        /* High Fidelity Dark Mode Base Typography Refinements */
        body.bg-dark, .bg-dark {
            color: #cbd5e1 !important; /* Sophisticated low-strain off-white */
        }
        body.bg-dark h1, body.bg-dark h2, body.bg-dark h3, body.bg-dark h4, body.bg-dark h5, body.bg-dark h6 {
            color: #f8fafc !important; /* Keep headings sharp */
        }
        body.bg-dark .text-muted, .bg-dark .text-muted {
            color: #94a3b8 !important; /* Premium readable slate muted values */
        }

        /* Dark Mode High-Contrast Overrides for Project Headers & Display Elements */
        body.bg-dark .project-header,
        body.bg-dark .project-header .display-5,
        body.bg-dark .display-5 {
            color: #f8fafc !important; /* Clear, readable bright off-white */
            text-shadow: 0 0 15px rgba(248, 250, 252, 0.15); /* Subtly defined soft text glow */
        }

        /* Responsive Accessibility Font Scaling overrides */
        body.font-small {
            font-size: 0.85rem !important;
        }
        body.font-small .navbar-brand img {
            height: 44px !important;
        }
        body.font-small .cc-nav-link {
            font-size: 0.8rem !important;
            padding: 0.35rem 0.65rem !important;
        }
        body.font-small .dropdown-item {
            font-size: 0.8rem !important;
        }

        body.font-medium {
            font-size: 1rem !important;
        }
        body.font-medium .navbar-brand img {
            height: 52px !important;
        }
        body.font-medium .cc-nav-link {
            font-size: 0.95rem !important;
            padding: 0.5rem 0.9rem !important;
        }
        body.font-medium .dropdown-item {
            font-size: 0.9rem !important;
        }

        body.font-large {
            font-size: 1.15rem !important;
        }
        body.font-large .navbar-brand img {
            height: 60px !important;
        }
        body.font-large .cc-nav-link {
            font-size: 1.1rem !important;
            padding: 0.65rem 1.1rem !important;
        }
        body.font-large .dropdown-item {
            font-size: 1.05rem !important;
        }
        body.font-large .btn {
            font-size: 1.05rem !important;
        }

        /* Line Spacing Configurations */
        body.line-spacing-1 {
            line-height: 1.25 !important;
        }
        body.line-spacing-1-5 {
            line-height: 1.6 !important;
        }
        body.line-spacing-2 {
            line-height: 2.0 !important;
        }

        /* Futuristic Gamified Navbar */
        .cc-navbar {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background-color: rgba(11, 15, 18, 0.85); /* Sophisticated translucent dark-slate */
            border-bottom: 2px solid rgba(0, 243, 255, 0.2); /* Cyan grid border */
            box-shadow: 0 4px 30px rgba(0, 243, 255, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Premium Light Theme Override */
        body.bg-light:not(.high-contrast) {
            background-color: #f8fafc !important;
            color: #0f172a;
        }
        body.bg-light:not(.high-contrast) h1, 
        body.bg-light:not(.high-contrast) h2, 
        body.bg-light:not(.high-contrast) h3, 
        body.bg-light:not(.high-contrast) h4, 
        body.bg-light:not(.high-contrast) h5, 
        body.bg-light:not(.high-contrast) h6 {
            color: #0f172a !important;
        }
        body.bg-light:not(.high-contrast) .cc-navbar {
            background-color: rgba(255, 255, 255, 0.85);
            border-bottom: 2px solid rgba(13, 110, 253, 0.15);
            box-shadow: 0 4px 30px rgba(13, 110, 253, 0.03);
        }
        body.bg-light:not(.high-contrast) .cc-nav-link {
            color: #334155 !important;
        }
        body.bg-light:not(.high-contrast) .cc-nav-link:hover,
        body.bg-light:not(.high-contrast) .cc-nav-link:focus {
            color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.05);
        }

        /* Cyberpunk Hover Animations for Links */
        .cc-nav-link {
            color: #94a3b8 !important;
            border-radius: 8px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 8px; /* Alignment of icons & text */
            font-weight: 600;
        }
        .cc-nav-link i {
            font-size: 1.15em;
            transition: transform 0.25s ease;
        }
        .cc-nav-link:hover i {
            transform: scale(1.15) rotate(-3deg);
        }
        
        /* Dark mode link active states */
        body:not(.bg-light) .cc-nav-link:hover,
        body:not(.bg-light) .cc-nav-link:focus,
        body:not(.bg-light) .cc-nav-link.active {
            color: #00f3ff !important; /* Glowing neon cyan */
            background-color: rgba(0, 243, 255, 0.08);
            box-shadow: inset 0 0 10px rgba(0, 243, 255, 0.1);
        }

        /* Animated under-bar indicator */
        .cc-nav-item {
            position: relative;
        }
        .cc-nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: -2px;
            left: 50%;
            background-color: #00f3ff;
            border-radius: 4px;
            box-shadow: 0 0 10px #00f3ff;
            transition: all 0.25s ease;
            transform: translateX(-50%);
        }
        body.bg-light .cc-nav-link::after {
            background-color: #0d6efd;
            box-shadow: none;
        }
        .cc-nav-link:hover::after,
        .cc-nav-link.active::after {
            width: 60%;
        }

        /* Advanced Centering and Sizing for Circular Icon-Only Buttons */
        .cc-circular-btn {
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 50% !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-sizing: border-box !important;
        }
        /* Disable slide-under line animation for circular badges */
        .cc-circular-btn::after {
            display: none !important;
        }

        /* Font Scaling Context boundaries for circular buttons to avoid displacement */
        body.font-small .cc-circular-btn {
            width: 36px !important;
            height: 36px !important;
        }
        body.font-medium .cc-circular-btn {
            width: 42px !important;
            height: 42px !important;
        }
        body.font-large .cc-circular-btn {
            width: 48px !important;
            height: 48px !important;
        }

        /* Circular button hover effects */
        body:not(.bg-light) .cc-circular-btn:hover,
        body:not(.bg-light) .cc-circular-btn:focus {
            background-color: rgba(0, 243, 255, 0.15) !important;
            border-color: #00f3ff !important;
            box-shadow: 0 0 12px rgba(0, 243, 255, 0.3) !important;
        }
        body.bg-light .cc-circular-btn:hover,
        body.bg-light .cc-circular-btn:focus {
            background-color: rgba(13, 110, 253, 0.1) !important;
            border-color: #0d6efd !important;
        }

        /* Beautiful Gamified Dropdown Menus */
        .dropdown-menu {
            border-radius: 14px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(0, 243, 255, 0.15);
            background: rgba(20, 26, 31, 0.95);
            backdrop-filter: blur(12px);
            padding: 0.6rem;
            animation: ccDropdownFade 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        body.bg-light .dropdown-menu {
            background: rgba(255, 255, 255, 0.98);
            border-color: rgba(0, 0, 0, 0.05);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
        }
        
        .dropdown-item {
            font-weight: 500;
            color: #94a3b8 !important;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dropdown-item i {
            font-size: 1.1em;
            color: #00ffcc;
        }
        body.bg-light .dropdown-item i {
            color: #0d6efd;
        }
        body.bg-light .dropdown-item {
            color: #334155 !important;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: rgba(0, 255, 204, 0.1) !important;
            color: #00ffcc !important;
            transform: translateX(4px);
        }
        body.bg-light .dropdown-item:hover,
        body.bg-light .dropdown-item:focus {
            background-color: rgba(13, 110, 253, 0.05) !important;
            color: #0d6efd !important;
        }

        @keyframes ccDropdownFade {
            from { opacity: 0; transform: translateY(10px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* High Tech User Profile Badges */
        .cc-user-badge {
            background: linear-gradient(135deg, rgba(0, 243, 255, 0.1), rgba(0, 255, 136, 0.1));
            color: #00ffcc !important;
            padding: 0.45rem 1.1rem !important;
            border-radius: 50px;
            border: 1.5px solid rgba(0, 255, 204, 0.3);
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 0 12px rgba(0, 255, 204, 0.08);
        }
        .cc-user-badge:hover {
            border-color: #00ffcc;
            box-shadow: 0 0 16px rgba(0, 255, 204, 0.2);
            color: #ffffff !important;
        }
        body.bg-light .cc-user-badge {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(13, 110, 253, 0.1));
            color: #0d6efd !important;
            border-color: rgba(13, 110, 253, 0.25);
            box-shadow: none;
        }

        .dropdown-header {
            font-weight: 800;
            font-size: 0.75rem;
            letter-spacing: 1px;
            color: #00ffcc !important;
            padding: 0.5rem 1rem;
        }
        body.bg-light .dropdown-header {
            color: #475569 !important;
        }

        .dropdown-divider {
            border-color: rgba(255, 255, 255, 0.1);
        }
        body.bg-light .dropdown-divider {
            border-color: rgba(0, 0, 0, 0.05);
        }

        /* Interactive Action Buttons for Teen Audience */
        .btn-cyber-login {
            background: linear-gradient(135deg, #00f3ff, #00ffcc);
            color: #050707 !important;
            font-weight: 700;
            border: none;
            box-shadow: 0 0 15px rgba(0, 243, 255, 0.25);
            transition: all 0.25s ease;
        }
        .btn-cyber-login:hover {
            box-shadow: 0 0 25px rgba(0, 255, 204, 0.45);
            transform: translateY(-1px);
        }

        .btn-cyber-register {
            color: #00ffcc !important;
            border: 1.5px solid #00ffcc;
            background: transparent;
            font-weight: 700;
            transition: all 0.25s ease;
        }
        .btn-cyber-register:hover {
            background: rgba(0, 255, 204, 0.1);
            box-shadow: 0 0 15px rgba(0, 255, 204, 0.2);
        }

        body.bg-light .btn-cyber-login {
            background: #0d6efd;
            color: #ffffff !important;
            box-shadow: none;
        }
        body.bg-light .btn-cyber-register {
            color: #0d6efd !important;
            border-color: #0d6efd;
        }

        .accessibility-dropdown {
            min-width: 320px !important;
        }

        /* Accessibility: High Contrast overrides */
        body.high-contrast {
            background-color: #000000 !important;
            color: #ffffff !important;
        }
        body.high-contrast .cc-navbar {
            background-color: #000000 !important;
            border-bottom: 3px solid #ffffff !important;
            box-shadow: none !important;
        }
        body.high-contrast .cc-nav-link, 
        body.high-contrast .dropdown-item,
        body.high-contrast .navbar-brand {
            color: #ffff00 !important;
            font-weight: 900 !important;
        }
        body.high-contrast .dropdown-menu {
            background-color: #000000 !important;
            border: 2px solid #ffffff !important;
        }
        body.high-contrast .dropdown-item:hover,
        body.high-contrast .dropdown-item:focus {
            background-color: #ffff00 !important;
            color: #000000 !important;
        }
        body.high-contrast .btn-primary,
        body.high-contrast .btn-cyber-login {
            background-color: #ffff00 !important;
            color: #000000 !important;
            border: 2px solid #ffffff !important;
            font-weight: bold !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg cc-navbar py-2 sticky-top">
        <div class="container-fluid px-lg-4">
            <!-- Dynamic Brand Logo with Cybercity branding -->
            <a href="<?= BASE_URL; ?>index.php" class="navbar-brand d-flex align-items-center me-4">
                <img src="<?= BASE_URL; ?>assets/img/logoGeneric.png" alt="CyberCity Home" style="transition: height 0.3s ease; height: 52px; width: auto; filter: drop-shadow(0 0 8px rgba(0, 243, 255, 0.15));">
            </a>

            <!-- High-tech responsive grid menu toggler -->
            <button class="navbar-toggler border-0 shadow-none text-info" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                aria-expanded="false" aria-label="Toggle navigation" style="background-color: rgba(0, 243, 255, 0.1); padding: 8px 12px; border-radius: 8px;">
                <i class="bi bi-grid-fill" style="font-size: 1.4rem;"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
                    
                    <!-- Dynamic project dropdown with Console indicator -->
                    <li class="nav-item dropdown cc-nav-item">
                        <a class="nav-link dropdown-toggle px-3 cc-nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-terminal-fill text-info"></i> Project Target
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0 mt-2">
                            <li><h6 class="dropdown-header text-uppercase"><i class="bi bi-cpu-fill me-2"></i>Virtual Nodes</h6></li>
                            <?php
                            try {
                                $stmt = $conn->query("SELECT project_id, project_name FROM CyberCity.Projects");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<li><a class="dropdown-item rounded" href="' . BASE_URL . 'pages/challenges/challengesList.php?projectID=' . $row['project_id'] . '"><i class="bi bi-chevron-right text-muted"></i> ' . htmlspecialchars($row['project_name']) . '</a></li>';
                                }
                            } catch (PDOException $e) {
                                echo '<li><span class="dropdown-item text-danger small"><i class="bi bi-exclamation-triangle"></i> Error loading projects</span></li>';
                            }
                            ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a href="http://10.177.202.196/CyberCityDocs/welcome.html" class="dropdown-item rounded" target="_blank"><i class="bi bi-journal-code me-2"></i>Welcome Guide</a></li>
                        </ul>
                    </li>

                    <!-- Leaderboard with Golden Trophy icon -->
                    <li class="nav-item cc-nav-item">
                        <a href="<?= BASE_URL; ?>pages/leaderboard/leaderboard.php" class="nav-link px-3 cc-nav-link">
                            <i class="bi bi-trophy-fill text-warning"></i> Leaderboard
                        </a>
                    </li>

                    <!-- Tech Docs with Book stack icon -->
                    <li class="nav-item cc-nav-item">
                        <a href="//<?= $_SERVER['SERVER_NAME'] ?>:8001" class="nav-link px-3 cc-nav-link" target="_blank">
                            <i class="bi bi-book-half text-primary"></i> Docs
                        </a>
                    </li>

                    <!-- Feedback with Signal Transmission icon -->
                    <li class="nav-item cc-nav-item">
                        <a href="https://forms.gle/jgYrmMZesgtVhBZ39" class="nav-link px-3 cc-nav-link" target="_blank">
                            <i class="bi bi-send-check text-success"></i> Feedback
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    
                    <!-- Accessibility Console Settings (Configured with dynamic cc-circular-btn rules) -->
                    <li class="nav-item dropdown d-flex align-items-center justify-content-center">
                        <a class="nav-link cc-nav-link cc-circular-btn" style="border: 1.5px solid rgba(0, 243, 255, 0.15);" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Accessibility Dashboard">
                            <i class="bi bi-universal-access" style="font-size: 1.25rem;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0 accessibility-dropdown mt-2">
                            <li class="mb-3">
                                <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><i class="bi bi-type me-1"></i> Font Scale</small>
                                <div class="btn-group btn-group-toggle w-100 mt-1">
                                    <button class="btn btn-sm btn-outline-secondary accessibility-font" data-size="small">Small</button>
                                    <button class="btn btn-sm btn-outline-secondary accessibility-font" data-size="medium">Medium</button>
                                    <button class="btn btn-sm btn-outline-secondary accessibility-font" data-size="large">Large</button>
                                </div>
                            </li>
                            <li class="mb-3">
                                <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><i class="bi bi-distribute-vertical me-1"></i> Line Spacing</small>
                                <div class="btn-group btn-group-toggle w-100 mt-1">
                                    <button class="btn btn-sm btn-outline-secondary accessibility-line" data-spacing="1">Compact</button>
                                    <button class="btn btn-sm btn-outline-secondary accessibility-line" data-spacing="1.5">Normal</button>
                                    <button class="btn btn-sm btn-outline-secondary accessibility-line" data-spacing="2">Relaxed</button>
                                </div>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li class="mt-2">
                                <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;"><i class="bi bi-palette me-1"></i> Environment Controls</small>
                                <button id="modeToggle" class="btn btn-sm btn-dark w-100 mb-2 py-1.5 fw-bold"><i class="bi bi-moon-stars me-1"></i> Toggle Dark Mode</button>
                                <button id="toggleContrast" class="btn btn-sm btn-outline-dark w-100 py-1.5 fw-bold"><i class="bi bi-eye me-1"></i> High Contrast</button>
                            </li>
                        </ul>
                    </li>

                    <?php if (isset($_SESSION['username'])): ?>
                        
                        <!-- System Admin Control Deck (Configured with dynamic cc-circular-btn rules) -->
                        <?php if (($_SESSION['access_level'] ?? null) == ADMIN_ACCESS_LEVEL): ?>
                            <li class="nav-item dropdown d-flex align-items-center justify-content-center">
                                <a class="nav-link cc-nav-link cc-circular-btn text-danger" style="border: 1.5px solid rgba(255, 51, 102, 0.15);" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="System Configuration">
                                    <i class="bi bi-fingerprint" style="font-size: 1.25rem;"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3">
                                    <li>
                                        <h6 class="dropdown-header text-uppercase text-danger"><i class="bi bi-shield-fill-exclamation me-1"></i> Access Deck</h6>
                                    </li>
                                    <li><a href="<?= BASE_URL; ?>pages/admin/userList.php" class="dropdown-item rounded"><i class="bi bi-people-fill text-danger"></i> Users Hub</a></li>
                                    <li><a href="<?= BASE_URL; ?>pages/admin/contactMessages.php" class="dropdown-item rounded"><i class="bi bi-envelope-open-fill text-danger"></i> Communications</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <h6 class="dropdown-header text-uppercase text-warning"><i class="bi bi-gear-wide-connected me-1"></i> Missions Database</h6>
                                    </li>
                                    <li><a href="<?= BASE_URL; ?>pages/admin/challengeCreate.php" class="dropdown-item rounded"><i class="bi bi-file-earmark-plus text-warning"></i> Deploy Challenge</a></li>
                                    <li><a href="<?= BASE_URL; ?>pages/admin/challengeEdit.php" class="dropdown-item rounded"><i class="bi bi-pencil-square text-warning"></i> Modify Challenges</a></li>
                                    <li><a href="<?= BASE_URL; ?>pages/admin/challengeManager.php" class="dropdown-item rounded"><i class="bi bi-cloud-arrow-down-fill text-warning"></i> Sync Manager (JSON)</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <h6 class="dropdown-header text-uppercase text-info"><i class="bi bi-tags-fill me-1"></i> Architecture</h6>
                                    </li>
                                    <li><a href="<?= BASE_URL; ?>pages/admin/categoryCreate.php" class="dropdown-item rounded"><i class="bi bi-folder-plus text-info"></i> Create Category</a></li>
                                    <li><a href="<?= BASE_URL; ?>pages/admin/categoryEdit.php" class="dropdown-item rounded"><i class="bi bi-folder-symlink text-info"></i> Edit Category</a></li>
                                    <li><a href="<?= BASE_URL; ?>pages/admin/projectCreate.php" class="dropdown-item rounded"><i class="bi bi-bounding-box-circles text-info"></i> Create Project Node</a></li>
                                    <li><a href="<?= BASE_URL; ?>pages/admin/projectEdit.php" class="dropdown-item rounded"><i class="bi bi-pencil-square text-info"></i> Edit Project Nodes</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a href="<?= BASE_URL; ?>pages/admin/resetGame.php" class="dropdown-item text-danger rounded fw-bold"><i class="bi bi-exclamation-octagon-fill"></i> PURGE ALL STATES</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!-- User Profile / Tactical Status Badge -->
                        <li class="nav-item dropdown d-flex align-items-center">
                            <a href="#" class="nav-link dropdown-toggle cc-user-badge d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-badge-fill me-2"></i> <?= htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3">
                                <li><a href="<?= BASE_URL; ?>pages/user/editAccount.php" class="dropdown-item rounded"><i class="bi bi-sliders2 me-2"></i> Account Control</a></li>
                                <li>
                                    <span class="dropdown-item-text text-muted small fw-bold">
                                        <i class="bi bi-award-fill text-warning me-2"></i> Score: <?= htmlspecialchars((string)$userScore); ?> pts
                                    </span>
                                </li>
                                <?php if (($_SESSION['access_level'] ?? null) == USER_ACCESS_LEVEL): ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a href="<?= BASE_URL; ?>pages/contactUs/contact.php" class="dropdown-item rounded"><i class="bi bi-telephone-outbound-fill me-2"></i> Helpdesk</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>

                        <!-- Interactive Logout Action -->
                        <li class="nav-item ms-lg-2 d-flex align-items-center">
                            <a href="<?= BASE_URL; ?>pages/user/logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-bold shadow-sm"><i class="bi bi-power me-1"></i> Disconnect</a>
                        </li>

                    <?php else: ?>
                        <!-- Public Unauthenticated States -->
                        <li class="nav-item d-flex align-items-center">
                            <a href="<?= BASE_URL; ?>pages/user/register.php" class="btn btn-sm btn-cyber-register rounded-pill px-3 py-1.5 me-2"><i class="bi bi-user-plus-fill me-1"></i> Sign Up</a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a href="<?= BASE_URL; ?>pages/user/login.php" class="btn btn-sm btn-cyber-login rounded-pill px-4 py-1.5"><i class="bi bi-box-arrow-in-right me-1"></i> Access Port</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Interactive Flash Notifications / Threat Alerts -->
    <?php if ($flash): ?>
        <?php
        $type = preg_replace('/[^a-z]/', '', $flash['type']); // simple whitelist
        $text = htmlspecialchars($flash['text'], ENT_QUOTES, 'UTF-8');
        ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $type ?> mb-3 shadow-lg alert-dismissible fade show" role="alert" style="border-radius: 12px; border-left: 5px solid;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $text ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Page Content Slot -->

    <!-- Bootstrap JS Bundle -->
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
                body.classList.add('bg-dark');
                body.classList.remove('bg-light', 'text-black');
                updateWideBoxClasses('dark');
                if (modeToggleBtn) modeToggleBtn.innerHTML = '<i class="bi bi-sun-fill me-1"></i> Switch to Light Mode';
            } else {
                body.classList.add('bg-light', 'text-black');
                body.classList.remove('bg-dark');
                updateWideBoxClasses('light');
                if (modeToggleBtn) modeToggleBtn.innerHTML = '<i class="bi bi-moon-stars me-1"></i> Switch to Dark Mode';
            }
            localStorage.setItem('theme', theme);
        }

        // On page load, apply saved theme or default to dark (since dark theme is ideal for CTFs/Cyber Arenas)
        const savedTheme = localStorage.getItem('theme') || 'dark';
        applyTheme(savedTheme);

        if (modeToggleBtn) {
            modeToggleBtn.addEventListener('click', () => {
                const currentTheme = localStorage.getItem('theme') || 'dark';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                applyTheme(newTheme);
            });
        }
    </script>

    <script>
        // Load saved preferences or set defaults for accessibility features
        // DEFAULT changed from '1.5' (Normal) to '1' (Compact Spacing)
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

        // Highlight active accessibility control buttons
        document.querySelectorAll('.accessibility-font').forEach(button => {
            if (button.getAttribute('data-size') === savedFont) {
                button.classList.add('active', 'btn-secondary');
                button.classList.remove('btn-outline-secondary');
            }
        });
        document.querySelectorAll('.accessibility-line').forEach(button => {
            if (button.getAttribute('data-spacing') === savedLineSpacing) {
                button.classList.add('active', 'btn-secondary');
                button.classList.remove('btn-outline-secondary');
            }
        });
        if (savedContrast) {
            const contrastBtn = document.getElementById('toggleContrast');
            if (contrastBtn) contrastBtn.classList.add('active', 'btn-dark');
        }

        // Font size buttons
        document.querySelectorAll('.accessibility-font').forEach(button => {
            button.addEventListener('click', () => {
                const size = button.getAttribute('data-size');
                localStorage.setItem('accessibilityFont', size);
                location.reload(); 
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
        const contrastToggleBtn = document.getElementById('toggleContrast');
        if (contrastToggleBtn) {
            contrastToggleBtn.addEventListener('click', () => {
                const current = localStorage.getItem('accessibilityContrast') === 'true';
                localStorage.setItem('accessibilityContrast', !current);
                location.reload();
            });
        }
    </script>
</body>

</html>