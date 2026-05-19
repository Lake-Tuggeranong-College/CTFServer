<?php
// Prevent "headers already sent" issues if template.php echoes anything
ob_start();

require_once "../../includes/template.php";
/** @var PDO $conn */

// Authorisation check (do this before we output anything ourselves)
if (!authorisedAccess(false, true, true)) {
    header("Location: ../../index.php");
    exit;
}

// Validate projectID early
$projectID = filter_input(INPUT_GET, 'projectID', FILTER_VALIDATE_INT);
if (!$projectID) {
    header("Location: ../../index.php");
    exit;
}

// Fetch Project Details for the header
$projectTitle = "Project Challenges";
$projectDescription = "";
$projectQuery = $conn->prepare("SELECT project_title, project_description FROM Projects WHERE project_id = ?");
$projectQuery->execute([$projectID]);
if ($pRow = $projectQuery->fetch(PDO::FETCH_ASSOC)) {
    $projectTitle = $pRow['project_title'];
    $projectDescription = $pRow['project_description'];
}

// Helper for safe HTML
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Render a single challenge card (always shows a card, even without image).
 */
function createChallengeCard(array $challengeData): void
{
    // Extract with sensible fallbacks
    $challengeID       = $challengeData['ID'];
    $challengeTitle    = $challengeData['challengeTitle'] ?? 'Untitled Challenge';
    $pointsValue       = isset($challengeData['pointsValue']) ? (int)$challengeData['pointsValue'] : 0;
    $imageFileName     = trim((string)($challengeData['Image'] ?? ''));
    $dockerChallengeId = $challengeData['dockerChallengeID'] ?? null;

    // Build target link: NOW ALWAYS points to challengeDisplayUnified.php
    $href = "challengeDisplayUnified.php?challengeID={$challengeID}";

    // If it's a docker challenge, append the docker ID for the display page to use
    if ($dockerChallengeId !== null && $dockerChallengeId !== '' && $dockerChallengeId !== 0) {
        $href .= "&dockerID=" . urlencode($dockerChallengeId);
    }

    // Pick image (fallback if missing)
    $imgSrc = $imageFileName !== ''
        ? BASE_URL . "assets/img/challengeImages/" . rawurlencode($imageFileName)
        : BASE_URL . "assets/img/challengeImages/Image%20Not%20Found.jpg";
    ?>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <img src="<?= e($imgSrc) ?>" class="card-img-top" alt="<?= e($challengeTitle) ?>"
                 width="100" height="200" style="object-fit: cover; border-top-left-radius: 0.375rem; border-top-right-radius: 0.375rem;">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title h6 mb-2 fw-bold"><?= e($challengeTitle) ?></h5>
                <p class="card-text text-muted small mb-3">Value: <span class="badge bg-light text-dark"><?= $pointsValue ?> Points</span></p>
                <a href="<?= e($href) ?>" class="btn btn-warning mt-auto fw-bold">Start Challenge</a>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Fetch and render challenges grouped by category.
 */
function displayResultsByCategory(PDO $conn, int $projectID): void
{
    $sql = "
        SELECT cat.CategoryName, ch.*
        FROM Category AS cat
        JOIN Challenges AS ch        ON cat.id = ch.categoryID
        JOIN ProjectChallenges AS pc ON ch.id = pc.challenge_id
        JOIN Projects AS p           ON pc.project_id = p.project_id
        WHERE p.project_id = :project_id AND Enabled = 1
        ORDER BY cat.CategoryName, ch.challengeTitle;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':project_id', $projectID, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo "<div class='alert alert-info'>No challenges have been assigned to this project yet. Check back soon!</div>";
        return;
    }

    $currentCategory = null;
    $openRow = false;

    foreach ($rows as $row) {
        // Start new category block when the name changes
        if ($currentCategory !== $row['CategoryName']) {
            if ($openRow) {
                echo "</div>"; // close previous .row
                $openRow = false;
            }
            $currentCategory = $row['CategoryName'];
            echo "<h3 class='mt-5 mb-3 border-bottom pb-2 text-primary'><i class='bi bi-folder2-open me-2'></i>" . e($currentCategory) . "</h3>";
            echo "<div class='row'>";
            $openRow = true;
        }

        createChallengeCard($row);
    }

    if ($openRow) {
        echo "</div>";
    }
}
?>

<head>
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>assets/css/moduleList.css">
    <style>
        .project-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 5px solid #ffc107;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 0.5rem;
        }
    </style>
</head>

<div class="container py-4">
    <!-- Project Header Section -->
    <div class="project-header shadow-sm">
       
        <h1 class="display-5 fw-bold"><?= e($projectTitle) ?></h1>
        <?php if ($projectDescription): ?>
            <p class="lead text-muted mb-0"><?= nl2br(e($projectDescription)) ?></p>
        <?php endif; ?>
    </div>

    <div class="mb-4">
        <h2 class="h4 text-uppercase tracking-wider text-secondary">Available Challenges</h2>
        <p class="text-muted">Select a task below to begin testing your skills.</p>
    </div>

    <div class="container-fluid px-0">
        <?php displayResultsByCategory($conn, $projectID); ?>
    </div>
</div>

<?php
// Flush the buffer only after we've done potential redirects above
ob_end_flush();
?>