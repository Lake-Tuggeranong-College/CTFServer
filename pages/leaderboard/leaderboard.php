<?php

$sec = 30;
$page = $_SERVER['PHP_SELF'];

header("Refresh:$sec; url=$page");
include_once "../../includes/template.php";

if (!authorisedAccess(true, true, true)) {
    header("Location:../../index.php");
    exit;
}

$currentUserID = $_SESSION["user_id"] ?? null;

// Query top users with score
$query = "SELECT ID, Username, Score FROM Users WHERE Enabled=1 ORDER BY Score DESC LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->execute();
$userScore = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extract top 3 and remaining users
$topThreeRaw = array_slice($userScore, 0, 3);
$restUsers   = array_slice($userScore, 3, 7);

// Reorder top 3 into Olympic Podium order: [2nd Place, 1st Place, 3rd Place]
$podiumOrder = [];
if (isset($topThreeRaw[1])) $podiumOrder[] = ['rank' => 2, 'class' => 'podium-second', 'badge' => 'bi-award-fill text-secondary', 'data' => $topThreeRaw[1]];
if (isset($topThreeRaw[0])) $podiumOrder[] = ['rank' => 1, 'class' => 'podium-first',  'badge' => 'bi-trophy-fill text-warning',   'data' => $topThreeRaw[0]];
if (isset($topThreeRaw[2])) $podiumOrder[] = ['rank' => 3, 'class' => 'podium-third',  'badge' => 'bi-award-fill text-bronze',    'data' => $topThreeRaw[2]];

// Helper to generate dynamic user initials
function getInitials($username) {
    return strtoupper(substr(trim($username), 0, 2));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Leaderboard - CTF Platform</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <style>
        .bronze-color { color: #cd7f32 !important; }
        
        /* Podium Container */
        .podium-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 1.5rem;
            margin-top: 2rem;
            margin-bottom: 3rem;
        }

        .podium-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
            border-radius: 1rem;
            background: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            width: 100%;
            max-width: 220px;
            text-align: center;
        }

        .podium-card:hover {
            transform: translateY(-5px);
        }

        /* Rank-specific Height & Styling */
        .podium-first {
            order: 2;
            border: 2px solid #ffc107 !important;
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.25);
            transform: scale(1.08);
            z-index: 2;
        }
        .podium-second {
            order: 1;
            border: 1px solid #6c757d !important;
            box-shadow: 0 6px 18px rgba(108, 117, 125, 0.15);
        }
        .podium-third {
            order: 3;
            border: 1px solid #cd7f32 !important;
            box-shadow: 0 6px 18px rgba(205, 127, 50, 0.15);
        }

        /* Avatar Box */
        .avatar-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .podium-first .avatar-circle {
            width: 76px;
            height: 76px;
            font-size: 1.5rem;
        }

        /* Highlight Current User Row */
        .user-highlight {
            background-color: rgba(13, 110, 253, 0.08) !important;
            border-left: 4px solid var(--bs-primary) !important;
        }
    </style>
</head>
<body>

<main class="container py-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-bold mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>CTF Leaderboard</h1>
            <p class="text-muted mb-0 small">Top hackers ranked by total challenge points</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
            <span class="badge bg-body-tertiary text-body border px-3 py-2">
                <i class="bi bi-arrow-clockwise me-1"></i>Auto-refreshing in <span id="refreshTimer"><?=$sec?></span>s
            </span>
        </div>
    </div>

    <!-- Top 3 Podium Section -->
    <section class="podium-container">
        <?php foreach ($podiumOrder as $podium): ?>
            <div class="podium-card <?= $podium['class'] ?> shadow-sm">
                <!-- Trophy / Crown Icon -->
                <div class="fs-3 mb-1">
                    <i class="bi <?= $podium['badge'] ?>"></i>
                </div>
                
                <!-- Rank Badge -->
                <span class="badge bg-dark mb-2">#<?= $podium['rank'] ?> Place</span>

                <!-- Avatar with Fallback Initials -->
                <div class="avatar-circle bg-primary text-white">
                    <?= getInitials($podium['data']['Username']) ?>
                </div>

                <!-- Username & Score -->
                <h5 class="fw-bold mb-1 text-truncate w-100" title="<?= htmlspecialchars($podium['data']['Username']) ?>">
                    <?= htmlspecialchars($podium['data']['Username']) ?>
                </h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-3">
                    <?= number_format($podium['data']['Score']) ?> pts
                </span>
            </div>
        <?php endforeach; ?>
    </section>

    <!-- Rest of Rankings Table (Ranks 4 to 10) -->
    <?php if (!empty($restUsers)): ?>
        <section class="card shadow-sm border-0">
            <div class="card-header bg-transparent py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list-ol me-2"></i>Rankings 4 – 10</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%" class="text-center">Rank</th>
                            <th style="width: 60%">Player</th>
                            <th style="width: 30%" class="text-end pe-4">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($restUsers as $index => $userData): 
                            $rank = $index + 4;
                            $isCurrentUser = ($currentUserID && $userData['ID'] == $currentUserID);
                        ?>
                            <tr class="<?= $isCurrentUser ? 'user-highlight fw-bold' : '' ?>">
                                <td class="text-center font-monospace fw-bold text-muted">
                                    #<?= $rank ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-secondary text-white me-3 mb-0" style="width:38px; height:38px; font-size:0.9rem;">
                                            <?= getInitials($userData['Username']) ?>
                                        </div>
                                        <span><?= htmlspecialchars($userData['Username']) ?></span>
                                        <?php if ($isCurrentUser): ?>
                                            <span class="badge bg-primary ms-2">You</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-end pe-4 font-monospace fw-bold text-primary">
                                    <?= number_format($userData['Score']) ?> pts
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>

</main>

<script>
    // Live Visual Countdown Timer
    let secondsLeft = <?= $sec ?>;
    const timerElement = document.getElementById('refreshTimer');
    
    if (timerElement) {
        setInterval(() => {
            secondsLeft--;
            if (secondsLeft >= 0) {
                timerElement.textContent = secondsLeft;
            }
        }, 1000);
    }
</script>

</body>
</html>