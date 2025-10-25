<?php
include "../../includes/template.php";
/** @var PDO $conn */ // Ensure $conn is available

// ---------------------------------------------------------
// Auth & Inputs
// ---------------------------------------------------------

// Authorisation check (always required)
if (!authorisedAccess(false, true, true)) {
    header("Location:../../index.php");
    exit;
}

$challengeToLoad = $_GET["challengeID"] ?? null;
$dockerID        = $_GET["dockerID"] ?? null; // The presence of this GET param signals a Docker challenge

$isDockerChallenge = $dockerID !== null && $dockerID !== '' && $dockerID !== '0';

if (!$challengeToLoad) {
    header("location:challengesList.php");
    exit;
}

$userID = $_SESSION["user_id"] ?? null;
if (!$userID) {
    // If we're a docker challenge, we've already checked auth above, but good to ensure userID is set
    // For non-docker challenges, auth check is sufficient, but this ensures userID for scoring.
    if ($isDockerChallenge) {
         safe_redirect("../../index.php");
    } else {
        // Assume non-docker will handle via template.php or general session check later if needed
    }
}


$challengeID = $title = $challengeText = $pointsValue = $flag = $projectID = $files= null;
$image = null; // Used in docker version

// Build a self URL for redirects (stays on this page)
$selfUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?challengeID=' . $challengeToLoad;
if ($isDockerChallenge) {
    $selfUrl .= "&dockerID=" . urlencode($dockerID);
}


/* ------------ FUNCTIONS (Common to both) ------------- */

// Function to convert URLs in text to clickable links
function makeLinksClickable($text) {
    // Regex to find URLs (http, https)
    $pattern = '/(https?:\/\/[^\s]+)/i';
    // Replace URLs with anchor tags
    return preg_replace_callback($pattern, function($matches) {
        $url = htmlspecialchars($matches[0]);
        return "<a href=\"$url\" target=\"_blank\" rel=\"noopener noreferrer\">$url</a>";
    }, $text);
}


/* ------------ FUNCTIONS (Docker-specific) ------------- */

/**
 * Config: keep this in sync with the watcher
 */
$TIME_LIMIT_MINUTES = (int) (getenv('CYBER_DOCKER_TIME_LIMIT_MINUTES') ?: 10);

/**
 * HTTP POST helper for server-side stop attempts.
 */
function http_post_json(string $url, array $payload, int $timeout = 4): array {
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

/**
 * HTTP POST form helper for server-side stop attempts.
 */
function http_post_form(string $url, array $payload, int $timeout = 4): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => http_build_query($payload),
        CURLOPT_TIMEOUT        => $timeout,
    ]);
    $respBody = curl_exec($ch);
    $err      = curl_error($ch);
    $status   = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['ok' => $err === '' && $status >= 200 && $status < 300, 'status' => $status, 'error' => $err, 'body' => $respBody];
}

/* ------------ CORE LOGIC ------------- */

function loadChallengeData() {
    global $conn, $challengeToLoad, $challengeID, $title, $challengeText, $pointsValue, $flag, $projectID, $files, $image, $isDockerChallenge;

    $cols = "ID, challengeTitle, challengeText, pointsValue, flag, files";
    if ($isDockerChallenge) {
        $cols .= ", Image";
    }

    $stmt = $conn->prepare("SELECT $cols FROM Challenges WHERE ID = ?");
    $stmt->execute([$challengeToLoad]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $challengeID   = $row["ID"];
        $title         = $row["challengeTitle"];
        $challengeText = $row["challengeText"];
        $pointsValue   = $row["pointsValue"];
        $flag          = $row["flag"];
        $files         = $row["files"];
        if ($isDockerChallenge) {
            $image = $row["Image"];
        }
    } else {
        // Challenge not found, redirect
        header("location:challengesList.php");
        exit;
    }

    // Project ID (required for successful flag redirect)
    $projectStmt = $conn->prepare("SELECT project_id FROM ProjectChallenges WHERE challenge_id = ?");
    $projectStmt->execute([$challengeID]);
    $result = $projectStmt->fetch(PDO::FETCH_ASSOC);
    $projectID = $result["project_id"] ?? null;
}

function handleFlagSubmission() {
    global $conn, $challengeID, $flag, $projectID, $pointsValue, $userID, $isDockerChallenge, $selfUrl;

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["hiddenflag"])) {
        $userEnteredFlag = sanitise_data($_POST['hiddenflag']);

        // --- Docker: Container Stop Logic (Best Effort) ---
        $stopContainer = function() use ($challengeID, $userID) {
            if (!defined('BASE_URL')) return false;
            $stopUrl = rtrim(BASE_URL, '/') . '/pages/challenges/docker/stopContainer.php';
            $payload = ['challengeID' => $challengeID, 'userID' => $userID];

            $resJson = http_post_json($stopUrl, $payload);
            if ($resJson['ok']) return true;

            $resForm = http_post_form($stopUrl, $payload);
            return $resForm['ok'];
        };

        if ($isDockerChallenge) {
            // Always set client fallback to guarantee stop on reload
            $_SESSION['AUTO_STOP_CONTAINER'] = ['challengeID' => $challengeID, 'userID' => $userID];
        }
        // --- End Docker Logic ---

        if ($userEnteredFlag === $flag) {
            $user = $userID;
            $query = $conn->prepare("SELECT 1 FROM UserChallenges WHERE challengeID=? AND userID=?");
            $query->execute([$challengeID, $user]);

            if ($query->fetch()) {
                $msg = 'Flag Success! Challenge already completed, no points awarded';
                if ($isDockerChallenge) {
                    $ok = $stopContainer();
                    $msg .= ($ok ? ". Container stopped." : ". Stopping container…");
                    $_SESSION["flash_message"] = "<div class='bg-warning text-center p-2'>$msg</div>";
                    safe_redirect($selfUrl);
                } else {
                    set_flash('warning', $msg);
                    header("Location:./challengesList.php");
                    exit;
                }
            }

            // Insert into UserChallenges
            $insert = $conn->prepare("INSERT INTO UserChallenges (userID, challengeID) VALUES (?, ?)");
            $insert->execute([$user, $challengeID]);

            // Update user score
            $updateScore = $conn->prepare("UPDATE Users SET Score = Score + ? WHERE ID = ?");
            $updateScore->execute([$pointsValue, $user]);

            // Non-Docker specific post-solve actions
            if (!$isDockerChallenge) {
                // Increment module value
                $conn->exec("UPDATE Challenges SET moduleValue = 1 WHERE ID=$challengeID");
                shell_exec('./CyberCity/website/assets/30 sec timer.sh'); // Note: This command is platform-dependent

                set_flash('success', 'Success!');
                header("Location:./challengesList.php?projectID=$projectID");
                exit;
            } else {
                // Docker specific post-solve actions
                $ok = $stopContainer();
                $msg = 'Success!';
                $msg .= ($ok ? " Container stopped." : " Stopping container…");

                $_SESSION["flash_message"] = "<div class='bg-success text-center p-2'>$msg</div>";
                safe_redirect($selfUrl);
            }

        } else {
            $msg = 'Flag failed - try again';
            if ($isDockerChallenge) {
                $_SESSION["flash_message"] = "<div class='bg-danger text-center p-2'>$msg</div>";
                safe_redirect($selfUrl);
            } else {
                set_flash('danger', $msg);
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }
}

// ---------------------------------------------------------
// Load Data & Handle Submission
// ---------------------------------------------------------
loadChallengeData();
handleFlagSubmission(); // Process POST request before outputting HTML

// ---------------------------------------------------------
// Docker Challenge State Check (Only if required)
// ---------------------------------------------------------
$isRunning = false;
$deletionTime = "Container not initialised";
$ipAddress = "";
$port = null;
$sshCmd = "ssh -p <PORT> -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null (User)@<IP_ADDRESS>";
$scpCmd = "scp -P <PORT> -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null (User)@<IP_ADDRESS>:/home/(User)/Alarm.png ./(Filename and type)";

if ($isDockerChallenge) {
    // Hardcoded IP for now (as in original file)
    $ipAddress = "10.177.202.196"; // TODO: make dynamic if needed

    $containerStmt = $conn->prepare("
        SELECT timeInitialised, port
        FROM DockerContainers
        WHERE userID = ? AND challengeID = ?
        LIMIT 1
    ");
    $containerStmt->execute([$userID, $challengeID]);
    $container = $containerStmt->fetch(PDO::FETCH_ASSOC);

    $timeInitialised = $container['timeInitialised'] ?? null;
    $port            = $container['port'] ?? null;
    $isRunning       = !empty($timeInitialised);

    if ($isRunning) {
        $t0 = strtotime($timeInitialised);
        if ($t0 !== false) {
            $deletionTime = date('G:i', $t0 + ($TIME_LIMIT_MINUTES * 60));
        }
    }

    // Dynamic SSH/SCP snippets
    $sshPort = $isRunning && $port ? (string)$port : "<PORT>";
    $sshIp   = $isRunning ? $ipAddress : "<IP_ADDRESS>";
    $sshCmd  = "ssh -p {$sshPort} -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null (User)@{$sshIp}";
    $scpCmd  = "scp -P {$sshPort} -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null (User)@{$sshIp}:/home/(User)/Alarm.png ./(Filename and type)";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Challenge Information</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if ($isDockerChallenge): ?>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <?php endif; ?>

    <style>
        /* Keep the flag input textbox color consistent regardless of theme */
        .flag-input {
            background-color: white !important;
            color: black !important;
        }
        <?php if ($isDockerChallenge): ?>
        /* Docker-specific styles */
        .flag-input { width: 100%; max-width: 420px; }
        .btn-wide   { min-width: 170px; }
        pre.bg-body-tertiary {
            background-color: var(--bs-tertiary-bg) !important;
            color: var(--bs-body-color) !important;
            border-color: var(--bs-border-color) !important;
        }
        pre.bg-body-tertiary code { color: inherit; }
        /* Note: Original challengeDisplayDocker had a navbar override, which might conflict with template.php */
        /* I've removed the complex navbar override here assuming template.php handles it. */
        <?php endif; ?>
    </style>
</head>
<body>
<header class="container text-center mt-4">
    <h1 class="text-uppercase">Challenge - <?= htmlspecialchars($title) ?></h1>
</header>

<main class="container my-5">

    <?php if ($isDockerChallenge && !empty($_SESSION["flash_message"])): ?>
        <div class="mt-2">
            <?= $_SESSION["flash_message"]; unset($_SESSION["flash_message"]); ?>
        </div>
    <?php elseif (!$isDockerChallenge && !empty($_SESSION["flash_message"])): ?>
        <?php // Flash messages for non-docker challenges are handled by set_flash in template.php, so this might be redundant ?>
    <?php endif; ?>

    <div class="table-responsive my-4">
        <table class="table table-bordered table-hover text-center align-middle theme-table mb-0">
            <thead>
            <tr>
                <th style="width:50%">Description</th>
                <th style="width:20%">Files</th>
                <th style="width:10%">Points</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="text-start"><?= nl2br(makeLinksClickable(htmlspecialchars($challengeText))) ?></td>
                <td>
                    <?php if ($files): ?>
                        <a href="<?= htmlspecialchars($files) ?>" download class="btn btn-primary btn-sm">
                            Download File
                        </a>
                    <?php else: ?>
                        <span class="text-muted">No file required</span>
                    <?php endif; ?>
                </td>
                <td class="fw-bold"><?= htmlspecialchars($pointsValue) ?></td>
            </tr>
            </tbody>
        </table>
    </div>

    <p class="text-success fw-bold text-center mt-3">Good luck and have fun!</p>
    <hr class="my-4 border-2 border-danger opacity-100">

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?challengeID=<?= $challengeID ?><?php if ($isDockerChallenge): echo "&dockerID=" . urlencode($dockerID); endif; ?>" method="post" class="mt-3">
        <div class="form-floating mb-3">
            <input type="text"
                   class="form-control flag-input"
                   id="flag"
                   name="hiddenflag"
                   placeholder="CTF{Flag_Here}">
            <label for="flag">Enter Flag</label>
            <p class="form-text text-start small">
                Press <b>Enter</b> when finished entering the flag.
            </p>
        </div>
    </form>

    <?php if ($isDockerChallenge): ?>
        <h2 class="ps-1">Container Controls</h2>
        <div class="table-responsive my-4">
            <table class="table table-bordered table-striped text-center align-middle theme-table mb-0">
                <thead>
                <tr>
                    <th>Container Info</th>
                    <th>Controls</th>
                    <th>Shutdown Time</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td id="containerInfo">
                        <?=
                        $isRunning
                            ? "IP: " . htmlspecialchars($ipAddress) . "<br>Port: " . htmlspecialchars((string)$port)
                            : "Container not initialised"
                        ?>
                    </td>
                    <td>
                        <div class="d-grid gap-2">
                            <?php if ($isRunning): ?>
                                <button
                                        id="toggleBtn"
                                        class="btn btn-danger btn-wide"
                                        data-state="running"
                                        onclick="toggleContainer(<?= (int)$challengeID ?>, <?= (int)$userID ?>)">
                                    Stop Container
                                </button>
                            <?php else: ?>
                                <button
                                        id="toggleBtn"
                                        class="btn btn-success btn-wide"
                                        data-state="stopped"
                                        onclick="toggleContainer(<?= (int)$challengeID ?>, <?= (int)$userID ?>)">
                                    Start Container
                                </button>
                            <?php endif; ?>

                            <button type="button"
                                    class="btn btn-outline-secondary btn-wide"
                                    data-bs-toggle="modal"
                                    data-bs-target="#sshHelpModal">
                                SSH Connection Help
                            </button>
                        </div>
                    </td>
                    <td id="shutdownCell">
                        <?= htmlspecialchars($deletionTime) ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="small text-body-secondary mt-2">
                Note: Containers automatically stop <?= (int)$TIME_LIMIT_MINUTES ?> minutes after start.
            </div>
        </div>
    <?php endif; ?>

</main>

<footer class="container my-5">
    <h2 class="ps-1">Recent Data</h2>
    <div class="table-responsive my-4">
        <table class="table table-bordered table-striped text-center align-middle theme-table mb-0">
            <thead>
            <tr>
                <th style="width:30%">Date & Time</th>
                <th style="width:70%">Data</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $sql = $conn->query("SELECT * FROM ModuleData WHERE ModuleID=" . $challengeID);
            while ($row = $sql->fetch()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row["DateTime"]) . '</td>';
                echo '<td>' . makeLinksClickable(htmlspecialchars($row["Data"])) . '</td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</footer>

<?php if ($isDockerChallenge): ?>
<div class="modal fade" id="sshHelpModal" tabindex="-1" aria-labelledby="sshHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sshHelpModalLabel">SSH: Fix Host Key Prompts (Lab Use Only)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <h6 class="mb-2">Quick commands</h6>
                <div class="mb-3">
                    <div class="small text-body-secondary">SSH:</div>
                    <pre class="border rounded p-3 bg-body-tertiary"><code><?= htmlspecialchars($sshCmd) ?></code></pre>
                    <div class="small text-body-secondary">SCP (download example):</div>
                    <pre class="border rounded p-3 bg-body-tertiary"><code><?= htmlspecialchars($scpCmd) ?></code></pre>
                    <?php if (!$isRunning): ?>
                        <div class="small text-body-secondary">
                            Container is not running yet — the command shows <code>&lt;PORT&gt;</code> and <code>&lt;IP_ADDRESS&gt;</code>. Start the container to see the live details.
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="my-4">

                <ol class="mb-3">
                    <li><strong>Open your terminal.</strong></li>

                    <li class="mt-2">
                        <strong>Edit or create the SSH config file:</strong>
                        <pre class="border rounded p-3 bg-body-tertiary"><code>nano ~/.ssh/config</code></pre>
                        <div class="small text-body-secondary">If the file does not exist, this will open a blank file.</div>
                    </li>

                    <li class="mt-2">
                        <strong>Add this to the file:</strong>
                        <pre class="border rounded p-3 bg-body-tertiary"><code>Host *
    StrictHostKeyChecking no
    UserKnownHostsFile=/dev/null</code></pre>

                        <ul class="small mb-0">
                            <li><code>Host *</code> applies to all hosts.</li>
                            <li><code>StrictHostKeyChecking no</code> automatically accepts new host keys.</li>
                            <li><code>UserKnownHostsFile=/dev/null</code> prevents writing to your <code>known_hosts</code> file.</li>
                        </ul>
                    </li>

                    <li class="mt-2">
                        <strong>Save and exit</strong> in nano: press <code>CTRL+O</code>, then <code>ENTER</code>. Press <code>CTRL+X</code> to close.
                    </li>

                    <li class="mt-2">
                        <strong>Set the correct permissions:</strong>
                        <pre class="border rounded p-3 bg-body-tertiary"><code>chmod 600 ~/.ssh/config</code></pre>
                    </li>
                </ol>

                <div class="alert alert-warning small mb-0">
                    Only use this in a controlled lab. Disabling host key checks is not safe on production networks.
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
    // Sync Bootstrap color mode with the page's body classes (so modal follows dark mode)
    function syncBootstrapThemeFromBody() {
        const body = document.body;
        const theme = body.classList.contains('bg-dark') ? 'dark' : 'light';
        document.documentElement.setAttribute('data-bs-theme', theme);
        
        // Also sync body text color
        if (theme === 'dark') {
            body.classList.add('text-light');
            body.classList.remove('text-dark');
        } else {
            body.classList.add('text-dark');
            body.classList.remove('text-light');
        }
    }

    // Table theme that reads the canonical Bootstrap theme attribute
    function applyTableTheme() {
        const theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
        const tables = document.querySelectorAll('.theme-table');
        tables.forEach(table => {
            table.classList.remove('table-dark', 'table-light');
            table.classList.add(theme === 'dark' ? 'table-dark' : 'table-light');
        });
        syncBootstrapThemeFromBody(); // Call this to ensure body text color is also correct
    }

    // Initial call
    document.addEventListener('DOMContentLoaded', () => {
        applyTableTheme();
        
        // ---- Client-side fallback: auto-stop container once after redirect (Docker-specific) ----
        <?php if ($isDockerChallenge && !empty($_SESSION['AUTO_STOP_CONTAINER']) && $isRunning):
            $auto = $_SESSION['AUTO_STOP_CONTAINER'];
            // Clear the flag immediately so it only runs once
            unset($_SESSION['AUTO_STOP_CONTAINER']);
            $cid = (int)$auto['challengeID'];
            $uid = (int)$auto['userID'];
            // Wait 250ms for the flash message to appear, then attempt stop
            echo "setTimeout(() => toggleContainer($cid, $uid), 250);\n";
        endif; ?>
    });

    // Re-apply on theme toggle button click
    document.getElementById('modeToggle')?.addEventListener('click', () => {
        // Wait a tick for template’s toggle to flip body classes
        setTimeout(applyTableTheme, 60);
    });

    <?php if ($isDockerChallenge): ?>
    // --- Docker-specific JS functions ---
    
    // Disable/enable button + label
    function setBtnBusy(busy, label) {
        const btn = document.getElementById('toggleBtn');
        if (!btn) return;
        btn.disabled = !!busy;
        if (label) btn.textContent = label;
    }

    // Optimistic swap of button state
    function setBtnState(state) {
        const btn = document.getElementById('toggleBtn');
        if (!btn) return;
        btn.dataset.state = state;
        if (state === 'running') {
            btn.classList.remove('btn-success');
            btn.classList.add('btn-danger');
            btn.textContent = 'Stop Container';
        } else {
            btn.classList.remove('btn-danger');
            btn.classList.add('btn-success');
            btn.textContent = 'Start Container';
        }
    }

    function toggleContainer(challengeID, userID) {
        const btn = document.getElementById('toggleBtn');
        if (!btn) return;

        const currentState = btn.dataset.state; // 'running' | 'stopped'
        const isStarting = currentState === 'stopped';
        const url = isStarting
            ? '<?= BASE_URL ?>pages/challenges/docker/startContainer.php'
            : '<?= BASE_URL ?>pages/challenges/docker/stopContainer.php';

        // Prevent double-clicks + optimistic UI
        setBtnBusy(true, isStarting ? 'Starting…' : 'Stopping…');
        // setBtnState(isStarting ? 'running' : 'stopped'); // Temporarily removed optimistic state change for reload to handle

        axios.post(url, {
            challengeID: challengeID,
            userID: userID
        }).then(() => {
            // allow DB/binlog to settle, then sync UI
            setTimeout(() => location.reload(), 800);
        }).catch(err => {
            // revert on error
            setBtnState(currentState);
            setBtnBusy(false, currentState === 'stopped' ? 'Start Container' : 'Stop Container');
            console.error(err);
            alert('Action failed. Please try again.');
        });
    }
    // --- End Docker-specific JS functions ---
    <?php endif; ?>
</script>
</body>
</html>