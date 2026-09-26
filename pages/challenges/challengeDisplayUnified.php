<?php

/**
 * challengeDisplayUnified.php
 * Enhanced CTF UI with full window width and {HOST_IP} placeholder replacement.
 */

// Start output buffering to prevent accidental whitespace from triggering header errors
ob_start();

include "../../includes/template.php";

// ---------------------------------------------------------
// Auth & Inputs
// ---------------------------------------------------------

if (!authorisedAccess(false, true, true)) {
    smart_redirect("../../index.php");
}

$challengeToLoad = $_GET["challengeID"] ?? null;
$dockerID        = $_GET["dockerID"] ?? null;

$isDockerChallenge = $dockerID !== null && $dockerID !== '' && $dockerID !== '0';

if (!$challengeToLoad) {
    smart_redirect("challengesList.php");
}

$userID = $_SESSION["user_id"] ?? null;
if (!$userID && $isDockerChallenge) {
    smart_redirect("../../index.php");
}

$challengeID = $title = $challengeText = $pointsValue = $difficulty = $flag = $projectID = $files = null;
$image = null;

// Build a self URL for redirects
$selfUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?challengeID=' . $challengeToLoad;
if ($isDockerChallenge) {
    $selfUrl .= "&dockerID=" . urlencode($dockerID);
}

// ---------------------------------------------------------
// IP Address Resolution
// ---------------------------------------------------------
$rawHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? gethostname();
$ipAddress = explode(':', $rawHost)[0];

if ($ipAddress === 'localhost' || $ipAddress === '127.0.0.1') {
    $resolvedIP = $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname());
    if (filter_var($resolvedIP, FILTER_VALIDATE_IP)) {
        $ipAddress = $resolvedIP;
    }
}

/* ------------ FUNCTIONS (Common) ------------- */

function makeLinksClickable($text)
{
    $pattern = '/(https?:\/\/[^\s]+)/i';
    return preg_replace_callback($pattern, function ($matches) {
        $url = htmlspecialchars($matches[0]);
        return "<a href=\"$url\" target=\"_blank\" rel=\"noopener noreferrer\" class=\"fw-semibold text-decoration-underline\">$url</a>";
    }, $text);
}

function renderDifficultyStars(int $rating): string
{
    $rating = max(1, min(5, $rating));
    $html = '<span class="text-warning fs-5 ms-1" title="Difficulty: ' . $rating . '/5">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= ($i <= $rating) ? '<i class="bi bi-star-fill me-1"></i>' : '<i class="bi bi-star text-muted opacity-50 me-1"></i>';
    }
    $html .= '</span>';
    return $html;
}

/* ------------ FUNCTIONS (Docker-specific) ------------- */

$TIME_LIMIT_MINUTES = (int) (getenv('CYBER_DOCKER_TIME_LIMIT_MINUTES') ?: 10);

function http_post_json(string $url, array $payload, int $timeout = 4): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_TIMEOUT        => $timeout,
    ]);
    $respBody = curl_exec($ch);
    $err      = curl_error($ch);
    $status   = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['ok' => $err === '' && $status >= 200 && $status < 300, 'status' => $status, 'error' => $err, 'body' => $respBody];
}

/* ------------ CORE LOGIC ------------- */

function loadChallengeData()
{
    global $conn, $challengeToLoad, $challengeID, $title, $challengeText, $pointsValue, $difficulty, $flag, $projectID, $files, $image, $isDockerChallenge, $ipAddress;

    $cols = "ID, challengeTitle, challengeText, pointsValue, difficulty, flag, files";
    if ($isDockerChallenge) {
        $cols .= ", Image";
    }

    $stmt = $conn->prepare("SELECT $cols FROM Challenges WHERE ID = ?");
    $stmt->execute([$challengeToLoad]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $challengeID   = $row["ID"];
        $title         = $row["challengeTitle"];
        
        // Replace {HOST_IP} placeholder with current server IP address
        $rawText       = $row["challengeText"] ?? '';
        $challengeText = str_replace('{HOST_IP}', $ipAddress, $rawText);

        $pointsValue   = $row["pointsValue"];
        $difficulty    = $row["difficulty"] ?? 1;
        $flag          = $row["flag"];
        $files         = $row["files"];
        if ($isDockerChallenge) {
            $image = $row["Image"];
        }
    } else {
        smart_redirect("challengesList.php");
    }

    $projectStmt = $conn->prepare("SELECT project_id FROM ProjectChallenges WHERE challenge_id = ?");
    $projectStmt->execute([$challengeID]);
    $result = $projectStmt->fetch(PDO::FETCH_ASSOC);
    $projectID = $result["project_id"] ?? null;
}

function handleFlagSubmission()
{
    global $conn, $challengeID, $flag, $projectID, $pointsValue, $userID, $isDockerChallenge, $selfUrl;

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["hiddenflag"])) {
        $userEnteredFlag = sanitise_data($_POST['hiddenflag']);

        $stopContainer = function () use ($challengeID, $userID) {
            if (!defined('BASE_URL')) return false;
            $stopUrl = rtrim(BASE_URL, '/') . '/pages/challenges/docker/stopContainer.php';
            $payload = ['challengeID' => $challengeID, 'userID' => $userID];
            $resJson = http_post_json($stopUrl, $payload);
            return $resJson['ok'];
        };

        if ($isDockerChallenge) {
            $_SESSION['AUTO_STOP_CONTAINER'] = ['challengeID' => $challengeID, 'userID' => $userID];
        }

        if ($userEnteredFlag === $flag) {
            $query = $conn->prepare("SELECT 1 FROM UserChallenges WHERE challengeID=? AND userID=?");
            $query->execute([$challengeID, $userID]);

            if ($query->fetch()) {
                $msg = 'Flag Success! Challenge already completed, no points awarded';
                if ($isDockerChallenge) {
                    $stopContainer();
                    $_SESSION["flash_message"] = "<div class='alert alert-warning shadow-sm d-flex align-items-center mb-4' role='alert'><i class='bi bi-exclamation-triangle-fill fs-4 me-3'></i><div>$msg. Container stopped.</div></div>";
                    smart_redirect($selfUrl);
                } else {
                    set_flash('warning', $msg);
                    smart_redirect("./challengesList.php");
                }
            }

            $insert = $conn->prepare("INSERT INTO UserChallenges (userID, challengeID) VALUES (?, ?)");
            $insert->execute([$userID, $challengeID]);

            $updateScore = $conn->prepare("UPDATE Users SET Score = Score + ? WHERE ID = ?");
            $updateScore->execute([$pointsValue, $userID]);

            if (!$isDockerChallenge) {
                $conn->exec("UPDATE Challenges SET moduleValue = 1 WHERE ID=$challengeID");
                set_flash('success', 'Success!');
                smart_redirect("./challengesList.php?projectID=$projectID");
            } else {
                $stopContainer();
                $_SESSION["flash_message"] = "<div class='alert alert-success shadow-sm d-flex align-items-center mb-4' role='alert'><i class='bi bi-check-circle-fill fs-4 me-3'></i><div>Success! Container stopped.</div></div>";
                smart_redirect($selfUrl);
            }
        } else {
            $msg = 'Flag failed - try again';
            if ($isDockerChallenge) {
                $_SESSION["flash_message"] = "<div class='alert alert-danger shadow-sm d-flex align-items-center mb-4' role='alert'><i class='bi bi-x-circle-fill fs-4 me-3'></i><div>$msg</div></div>";
                smart_redirect($selfUrl);
            } else {
                set_flash('danger', $msg);
                smart_redirect($_SERVER['REQUEST_URI']);
            }
        }
    }
}

// ---------------------------------------------------------
// Load Data & Handle Submission
// ---------------------------------------------------------
loadChallengeData();
handleFlagSubmission();

// ---------------------------------------------------------
// Docker Challenge State Check
// ---------------------------------------------------------
$isRunning = false;
$deletionTime = null;
$timeInitialised = null;
$port = null;

if ($isDockerChallenge) {
    $containerStmt = $conn->prepare("SELECT timeInitialised, port FROM DockerContainers WHERE userID = ? AND challengeID = ? LIMIT 1");
    $containerStmt->execute([$userID, $challengeID]);
    $container = $containerStmt->fetch(PDO::FETCH_ASSOC);

    $timeInitialised = $container['timeInitialised'] ?? null;
    $port            = $container['port'] ?? null;
    $isRunning       = !empty($timeInitialised);

    if ($isRunning && $timeInitialised) {
        $deletionTime = date('H:i:s', strtotime($timeInitialised) + ($TIME_LIMIT_MINUTES * 60));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= htmlspecialchars($title) ?> - Challenge</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if ($isDockerChallenge): ?>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <?php endif; ?>

    <style>
        /* Terminal Monospace Styling for Submission Inputs */
        .flag-input {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace !important;
            letter-spacing: 0.5px;
        }

        /* Interactive Button Micro-animations */
        .btn-interactive {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .btn-interactive:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Custom Left Accent Border for Task Details */
        .card-accent-primary {
            border: none;
            border-left: 5px solid var(--bs-primary) !important;
        }

        /* Pulsing Status Dot Indicator */
        .status-dot {
            height: 10px;
            width: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .status-dot-active {
            background-color: #198754;
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
            animation: pulse-green 1.8s infinite;
        }
        .status-dot-inactive {
            background-color: #dc3545;
        }

        @keyframes pulse-green {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 8px rgba(25, 135, 84, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
            }
        }

        <?php if ($isDockerChallenge): ?>
        .btn-wide {
            min-width: 170px;
        }

        pre.bg-body-tertiary {
            background-color: var(--bs-tertiary-bg) !important;
            color: var(--bs-body-color) !important;
            border-color: var(--bs-border-color) !important;
        }
        <?php endif; ?>
    </style>
</head>

<body>

<!-- Full Window Width Container -->
<main class="container-fluid px-4 px-md-5 my-4">

    <!-- Sub-Navigation Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="challengesList.php<?= $projectID ? '?projectID='.$projectID : '' ?>" class="text-decoration-none">
                    <i class="bi bi-grid-fill me-1"></i>Challenges
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($title) ?></li>
        </ol>
    </nav>

    <!-- Notifications -->
    <div class="flag-container">
        <?php if (!empty($_SESSION["flash_message"])): ?>
            <?= $_SESSION["flash_message"]; unset($_SESSION["flash_message"]); ?>
        <?php endif; ?>
    </div>

    <!-- Hero Header Section -->
    <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center pb-3 mb-4 border-bottom">
        <div>
            <span class="text-uppercase text-muted fw-bold small tracking-wider">CTF Lab Task</span>
            <h1 class="display-6 fw-bold mb-0"><?= htmlspecialchars($title) ?></h1>
        </div>
        <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
            <span class="badge bg-primary fs-6 px-3 py-2 shadow-sm">
                <i class="bi bi-star-fill me-1"></i><?= (int)$pointsValue ?> Points
            </span>
            <div class="bg-body-tertiary border rounded px-3 py-1 shadow-sm d-flex align-items-center">
                <span class="small fw-semibold text-muted me-1">Difficulty:</span>
                <?= renderDifficultyStars((int)$difficulty) ?>
            </div>
        </div>
    </header>

    <!-- Task Details Card -->
    <div class="card card-accent-primary shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex align-items-center py-3">
            <i class="bi bi-journal-text fs-5 text-primary me-2"></i>
            <h5 class="mb-0 fw-bold">Task Objective & Details</h5>
        </div>
        <div class="card-body">
            <p class="card-text lead fs-6 mb-4"><?= nl2br(makeLinksClickable(htmlspecialchars($challengeText))) ?></p>
            
            <?php if ($files): ?>
                <div class="pt-2 border-top">
                    <span class="small text-muted fw-semibold d-block mb-2">ATTACHMENTS & DOWNLOADS</span>
                    <a href="<?= htmlspecialchars($files) ?>" download class="btn btn-secondary btn-interactive d-inline-flex align-items-center">
                        <i class="bi bi-file-earmark-arrow-down-fill me-2 fs-5"></i>
                        <span>Download Accompanying Resources</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Docker Environment Controls -->
    <?php if ($isDockerChallenge): ?>
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-body-tertiary d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-box-seam-fill text-primary fs-5 me-2"></i>
                    <h5 class="mb-0 fw-bold">Docker Dynamic Environment</h5>
                </div>
                <div>
                    <?php if ($isRunning): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                            <span class="status-dot status-dot-active"></span> Active Instance
                        </span>
                    <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2">
                            <span class="status-dot status-dot-inactive"></span> Stopped
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body">
                <div class="row align-items-center">
                    <!-- Connection Box -->
                    <div class="col-lg-7 mb-3 mb-lg-0">
                        <div class="p-3 bg-body-tertiary border rounded">
                            <div class="text-muted small fw-bold mb-1">TARGET ACCESS / CONNECTION</div>
                            <?php if ($isRunning): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <code class="fs-5 fw-bold text-primary" id="dockerTargetCode">
                                        <?= htmlspecialchars($ipAddress) ?>:<?= htmlspecialchars((string)$port) ?>
                                    </code>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyConnection('<?= htmlspecialchars($ipAddress) ?>:<?= htmlspecialchars((string)$port) ?>')">
                                        <i class="bi bi-clipboard me-1"></i>Copy
                                    </button>
                                </div>
                                <div class="small text-muted mt-2">
                                    <i class="bi bi-clock-history me-1"></i>Auto-Shutdown Target: <strong><?= htmlspecialchars($deletionTime) ?></strong>
                                </div>
                            <?php else: ?>
                                <span class="text-muted italic">Environment not deployed. Click "Start Instance" to spin up target.</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Instance Actions -->
                    <div class="col-lg-5 text-lg-end">
                        <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                            <?php if ($isRunning): ?>
                                <button id="toggleBtn" class="btn btn-danger btn-interactive btn-wide" data-state="running" onclick="toggleContainer(<?= (int)$challengeID ?>, <?= (int)$userID ?>)">
                                    <i class="bi bi-stop-circle-fill me-1"></i>Stop Container
                                </button>
                            <?php else: ?>
                                <button id="toggleBtn" class="btn btn-success btn-interactive btn-wide" data-state="stopped" onclick="toggleContainer(<?= (int)$challengeID ?>, <?= (int)$userID ?>)">
                                    <i class="bi bi-play-circle-fill me-1"></i>Start Instance
                                </button>
                            <?php endif; ?>

                            <button type="button" class="btn btn-outline-secondary btn-interactive" data-bs-toggle="modal" data-bs-target="#sshHelpModal">
                                <i class="bi bi-terminal me-1"></i>SSH Guide
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top text-muted small">
                <i class="bi bi-info-circle me-1"></i>Docker containers stay active for max <?= (int)$TIME_LIMIT_MINUTES ?> minutes per session.
            </div>
        </div>
    <?php endif; ?>

    <!-- Terminal Flag Submission Section -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-flag-fill text-primary me-2"></i>Submit Flag</h5>
            <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-terminal text-muted"></i></span>
                    <input type="text" name="hiddenflag" class="form-control flag-input border-start-0" placeholder="CTF{enter_your_flag_here}" required autocomplete="off">
                    <button class="btn btn-primary px-5 fw-bold btn-interactive" type="submit">
                        <i class="bi bi-send-check-fill me-2"></i>Submit
                    </button>
                </div>
            </form>
            <div class="alert alert-secondary py-2 px-3 mt-3 mb-0 d-flex align-items-center" role="alert">
                <i class="bi bi-lightbulb-fill text-warning me-2 fs-5"></i>
                <span class="small">Enter the exact flag case-sensitively including wraps (e.g., <code>CTF{...}</code>).</span>
            </div>
        </div>
    </div>

    <!-- Recent Submissions / Module Data Footer Table -->
    <section class="mt-5">
        <h4 class="fw-bold mb-3"><i class="bi bi-activity me-2"></i>Module Logs & Data</h4>
        <div class="table-responsive shadow-sm rounded border">
            <table class="table table-hover table-striped align-middle theme-table mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th style="width:25%" class="ps-3">Date & Time</th>
                        <th style="width:75%">Logged Output</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = $conn->query("SELECT * FROM ModuleData WHERE ModuleID=" . $challengeID);
                    $rowCount = 0;
                    while ($row = $sql->fetch()) {
                        $rowCount++;
                        echo '<tr>';
                        echo '<td class="ps-3 font-monospace small text-muted">' . htmlspecialchars($row["DateTime"]) . '</td>';
                        echo '<td>' . makeLinksClickable(htmlspecialchars($row["Data"])) . '</td>';
                        echo '</tr>';
                    }

                    if ($rowCount === 0) {
                        echo '<tr><td colspan="2" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-1"></i>No module activity recorded yet.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

</main>

<?php if ($isDockerChallenge): ?>
    <!-- SSH Modal -->
    <div class="modal fade" id="sshHelpModal" tabindex="-1" aria-labelledby="sshHelpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="sshHelpModalLabel"><i class="bi bi-terminal me-2"></i>SSH Host Connection Guide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <ol class="mb-3">
                        <li><strong>Open your terminal emulator.</strong></li>
                        <li class="mt-2">
                            <strong>Create or update your local SSH configuration file:</strong>
                            <pre class="border rounded p-3 bg-body-tertiary"><code>nano ~/.ssh/config</code></pre>
                        </li>
                        <li class="mt-2">
                            <strong>Add rules to auto-bypass host checks in lab testing:</strong>
                            <pre class="border rounded p-3 bg-body-tertiary"><code>Host *
    StrictHostKeyChecking no
    UserKnownHostsFile=/dev/null</code></pre>
                        </li>
                        <li class="mt-2">
                            <strong>Set file permissions:</strong>
                            <pre class="border rounded p-3 bg-body-tertiary"><code>chmod 600 ~/.ssh/config</code></pre>
                        </li>
                    </ol>

                    <div class="alert alert-warning small mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Only use these settings inside trusted CTF environments.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    function copyConnection(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Target address copied to clipboard: ' + text);
        }).catch(err => {
            console.error('Copy failed', err);
        });
    }

    function syncBootstrapThemeFromBody() {
        const body = document.body;
        const theme = body.classList.contains('bg-dark') ? 'dark' : 'light';
        document.documentElement.setAttribute('data-bs-theme', theme);

        if (theme === 'dark') {
            body.classList.add('text-light');
            body.classList.remove('text-dark');
        } else {
            body.classList.add('text-dark');
            body.classList.remove('text-light');
        }
    }

    function applyTableTheme() {
        const theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
        const tables = document.querySelectorAll('.theme-table');
        tables.forEach(table => {
            table.classList.remove('table-dark', 'table-light');
            table.classList.add(theme === 'dark' ? 'table-dark' : 'table-light');
        });
        syncBootstrapThemeFromBody();
    }

    document.addEventListener('DOMContentLoaded', () => {
        applyTableTheme();

        <?php if ($isDockerChallenge && !empty($_SESSION['AUTO_STOP_CONTAINER']) && $isRunning):
            $auto = $_SESSION['AUTO_STOP_CONTAINER'];
            unset($_SESSION['AUTO_STOP_CONTAINER']);
            $cid = (int)$auto['challengeID'];
            $uid = (int)$auto['userID'];
            echo "setTimeout(() => toggleContainer($cid, $uid), 250);\n";
        endif; ?>
    });

    document.getElementById('modeToggle')?.addEventListener('click', () => {
        setTimeout(applyTableTheme, 60);
    });

    <?php if ($isDockerChallenge): ?>
        function setBtnBusy(busy, label) {
            const btn = document.getElementById('toggleBtn');
            if (!btn) return;
            btn.disabled = !!busy;
            if (label) btn.innerHTML = label;
        }

        function toggleContainer(challengeID, userID) {
            const btn = document.getElementById('toggleBtn');
            if (!btn) return;

            const currentState = btn.dataset.state;
            const isStarting = currentState === 'stopped';
            const url = isStarting ?
                '<?= BASE_URL ?>pages/challenges/docker/startContainer.php' :
                '<?= BASE_URL ?>pages/challenges/docker/stopContainer.php';

            setBtnBusy(true, isStarting ? '<span class="spinner-border spinner-border-sm me-1"></span>Starting…' : '<span class="spinner-border spinner-border-sm me-1"></span>Stopping…');

            axios.post(url, {
                challengeID: challengeID,
                userID: userID
            }).then(() => {
                setTimeout(() => location.reload(), 800);
            }).catch(err => {
                setBtnBusy(false, currentState === 'stopped' ? '<i class="bi bi-play-circle-fill me-1"></i>Start Instance' : '<i class="bi bi-stop-circle-fill me-1"></i>Stop Container');
                console.error(err);
                alert('Action failed. Please try again.');
            });
        }
    <?php endif; ?>
</script>

</body>
</html>
<?php ob_end_flush(); ?>