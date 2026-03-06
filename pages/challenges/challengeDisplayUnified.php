<?php

/**
 * challengeDisplayUnified.php
 * Updated to use smart_redirect and avoid "headers already sent" errors.
 */

// Start output buffering to prevent accidental whitespace from triggering header errors
ob_start();

include "../../includes/template.php";

/**
 * Redirects reliably even if headers have already been sent.
 * This is now the primary redirect method for this page.
 */

// ---------------------------------------------------------
// Auth & Inputs
// ---------------------------------------------------------

// Authorisation check (always required)
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

$challengeID = $title = $challengeText = $pointsValue = $flag = $projectID = $files = null;
$image = null;

// Build a self URL for redirects
$selfUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?challengeID=' . $challengeToLoad;
if ($isDockerChallenge) {
    $selfUrl .= "&dockerID=" . urlencode($dockerID);
}

/* ------------ FUNCTIONS (Common) ------------- */

function makeLinksClickable($text)
{
    $pattern = '/(https?:\/\/[^\s]+)/i';
    return preg_replace_callback($pattern, function ($matches) {
        $url = htmlspecialchars($matches[0]);
        return "<a href=\"$url\" target=\"_blank\" rel=\"noopener noreferrer\">$url</a>";
    }, $text);
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
                    $_SESSION["flash_message"] = "<div class='bg-warning text-center p-2'>$msg. Container stopped.</div>";
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
                $_SESSION["flash_message"] = "<div class='bg-success text-center p-2'>Success! Container stopped.</div>";
                smart_redirect($selfUrl);
            }
        } else {
            $msg = 'Flag failed - try again';
            if ($isDockerChallenge) {
                $_SESSION["flash_message"] = "<div class='bg-danger text-center p-2'>$msg</div>";
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
$deletionTime = "Container not initialised";

/**
 * UPDATE: Get the Host IP address.
 * $_SERVER['SERVER_NAME'] or $_SERVER['HTTP_HOST'] returns the domain/IP 
 * the user is actually using to access the site.
 */
$ipAddress = $_SERVER['HTTP_HOST'] ?? 'localhost';
// Remove port if present in the host string (e.g., 192.168.1.5:8000 -> 192.168.1.5)
// $ipAddress = explode(':', $ipAddress)[0];
// $_SESSION['flash'] = ['type' => 'success', 'text' => "<!-- Debug: Detected IP Address for SSH: $ipAddress -->"];


$port = null;

if ($isDockerChallenge) {
    $containerStmt = $conn->prepare("SELECT timeInitialised, port FROM DockerContainers WHERE userID = ? AND challengeID = ? LIMIT 1");
    $containerStmt->execute([$userID, $challengeID]);
    $container = $containerStmt->fetch(PDO::FETCH_ASSOC);

    $timeInitialised = $container['timeInitialised'] ?? null;
    $port            = $container['port'] ?? null;
    $isRunning       = !empty($timeInitialised);

    if ($isRunning && $timeInitialised) {
        $deletionTime = date('G:i', strtotime($timeInitialised) + ($TIME_LIMIT_MINUTES * 60));
    }
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
        .flag-input {
            width: 100%;
            max-width: 420px;
        }

        .btn-wide {
            min-width: 170px;
        }

        pre.bg-body-tertiary {
            background-color: var(--bs-tertiary-bg) !important;
            color: var(--bs-body-color) !important;
            border-color: var(--bs-border-color) !important;
        }

        pre.bg-body-tertiary code {
            color: inherit;
        }

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
    <!-- Notifications -->
    <div class="flag-container">
        <?php if (!empty($_SESSION["flash_message"])): ?>
            <?= $_SESSION["flash_message"]; unset($_SESSION["flash_message"]); ?>
        <?php endif; ?>
    </div>

    <!-- Info Section -->
    <div class="card flag-container shadow-sm mb-4">
        <div class="card-header card-header-custom">Task Details</div>
        <div class="card-body">
            <p class="card-text lead"><?= nl2br(makeLinksClickable(htmlspecialchars($challengeText))) ?></p>
            <?php if ($files): ?>
                <a href="<?= htmlspecialchars($files) ?>" download class="btn btn-sm btn-outline-secondary">
                    Download Accompanying Files
                </a>
            <?php endif; ?>
        </div>
    </div>

     <!-- UNIFIED SUBMISSION UI -->
    <div class="flag-container">
        <div class="card shadow-sm submission-card">
            <div class="card-body p-4">
                <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white"><i class="bi bi-flag-fill text-primary"></i></span>
                        <input type="text" 
                               name="hiddenflag" 
                               class="form-control flag-input border-start-0" 
                               placeholder="CTF{enter_flag_here}" 
                               required>
                        <button class="btn btn-primary px-5 fw-bold" type="submit">
                            <i class="bi bi-send-check-fill me-2"></i>Submit Flag
                        </button>
                    </div>
                </form>
                <div class="d-flex align-items-center mt-3 text-muted">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <span class="small">Check your flag format carefully (e.g., CTF{...}) before clicking submit.</span>
                </div>
            </div>
        </div>
    </div>


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
                const url = isStarting ?
                    '<?= BASE_URL ?>pages/challenges/docker/startContainer.php' :
                    '<?= BASE_URL ?>pages/challenges/docker/stopContainer.php';

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