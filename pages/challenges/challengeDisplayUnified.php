<?php

/**
 * challengeDisplayUnified.php
 * Updated to include star rating for difficulty.
 */

ob_start();

include "../../includes/template.php";

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

$challengeID = $title = $challengeText = $pointsValue = $difficulty = $flag = $projectID = $files = null;
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

function renderDifficultyStars(int $rating): string
{
    $rating = max(1, min(5, $rating));
    $html = '<span class="text-warning fs-5 ms-2" title="Difficulty: ' . $rating . '/5">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $html .= '<i class="bi bi-star-fill"></i>';
        } else {
            $html .= '<i class="bi bi-star text-muted opacity-50"></i>';
        }
    }
    $html .= '</span>';
    return $html;
}

/* ------------ CORE LOGIC ------------- */

function loadChallengeData()
{
    global $conn, $challengeToLoad, $challengeID, $title, $challengeText, $pointsValue, $difficulty, $flag, $projectID, $files, $image, $isDockerChallenge;

    $cols = "ID, challengeTitle, challengeText, pointsValue, difficulty, flag, files";
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
        $difficulty    = (int)($row["difficulty"] ?? 1);
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

// Load Data & Handle Submission
loadChallengeData();
handleFlagSubmission();

$isRunning = false;
$deletionTime = "Container not initialised";
$ipAddress = $_SERVER['HTTP_HOST'] ?? 'localhost';
$port = null;

if ($isDockerChallenge) {
    $containerStmt = $conn->prepare("SELECT timeInitialised, port FROM DockerContainers WHERE userID = ? AND challengeID = ? LIMIT 1");
    $containerStmt->execute([$userID, $challengeID]);
    $container = $containerStmt->fetch(PDO::FETCH_ASSOC);

    $timeInitialised = $container['timeInitialised'] ?? null;
    $port            = $container['port'] ?? null;
    $isRunning       = !empty($timeInitialised);

    if ($isRunning && $timeInitialised) {
        $TIME_LIMIT_MINUTES = (int)(getenv('CYBER_DOCKER_TIME_LIMIT_MINUTES') ?: 10);
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
        .flag-input {
            background-color: white !important;
            color: black !important;
        }
    </style>
</head>

<body>
    <header class="container text-center mt-4">
        <h1 class="text-uppercase"><?= htmlspecialchars($title) ?></h1>
    </header>

<main class="container my-5">
    <div class="flag-container">
        <?php if (!empty($_SESSION["flash_message"])): ?>
            <?= $_SESSION["flash_message"]; unset($_SESSION["flash_message"]); ?>
        <?php endif; ?>
    </div>

    <!-- Info Section with Difficulty Stars -->
    <div class="card flag-container shadow-sm mb-4">
        <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
            <span class="fw-bold">Task Details</span>
            <div>
                <span class="badge bg-warning text-dark fw-bold me-2"><?= (int)$pointsValue ?> pts</span>
                <?= renderDifficultyStars($difficulty) ?>
            </div>
        </div>
        <div class="card-body">
            <p class="card-text lead"><?= nl2br(makeLinksClickable(htmlspecialchars($challengeText))) ?></p>
            <?php if ($files): ?>
                <a href="<?= htmlspecialchars($files) ?>" download class="btn btn-sm btn-outline-secondary">
                    Download Accompanying Files
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Flag Submission -->
    <div class="flag-container">
        <div class="card shadow-sm submission-card">
            <div class="card-body p-4">
                <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white"><i class="bi bi-flag-fill text-primary"></i></span>
                        <input type="text" name="hiddenflag" class="form-control flag-input border-start-0" placeholder="CTF{enter_flag_here}" required>
                        <button class="btn btn-primary px-5 fw-bold" type="submit">
                            <i class="bi bi-send-check-fill me-2"></i>Submit Flag
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
</body>
</html>