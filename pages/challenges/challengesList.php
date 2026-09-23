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

// Get logged in User ID
$userID = $_SESSION['user_id'] ?? $_SESSION['userID'] ?? null;

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
 * Render a single condensed challenge card.
 */
function createChallengeCard(array $challengeData, bool $isCompleted = false): void
{
    // Extract with sensible fallbacks
    $challengeID       = $challengeData['ID'];
    $challengeTitle    = $challengeData['challengeTitle'] ?? 'Untitled Challenge';
    $pointsValue       = isset($challengeData['pointsValue']) ? (int)$challengeData['pointsValue'] : 0;
    $imageFileName     = trim((string)($challengeData['Image'] ?? ''));
    $dockerChallengeId = $challengeData['dockerChallengeID'] ?? null;

    // Build target link
    $href = "challengeDisplayUnified.php?challengeID={$challengeID}";

    if ($dockerChallengeId !== null && $dockerChallengeId !== '' && $dockerChallengeId !== 0) {
        $href .= "&dockerID=" . urlencode($dockerChallengeId);
    }

    // Pick image (fallback if missing)
    $imgSrc = $imageFileName !== ''
        ? BASE_URL . "assets/img/challengeImages/" . rawurlencode($imageFileName)
        : BASE_URL . "assets/img/challengeImages/Image%20Not%20Found.jpg";
    ?>
    <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-3">
        <a href="<?= e($href) ?>" class="text-decoration-none challenge-card-link">
            <div class="card condensed-challenge-card h-100 shadow-sm border-0 position-relative overflow-hidden p-2 <?= $isCompleted ? 'challenge-completed' : '' ?>">
                
                <?php if ($isCompleted): ?>
                    <!-- Completed Badge Tag -->
                    <span class="position-absolute top-0 end-0 bg-success text-white px-2 py-1 rounded-bottom-start shadow-sm small fw-bold completion-tag" title="Challenge Completed">
                        <i class="bi bi-check-circle-fill me-1"></i> Completed
                    </span>
                <?php endif; ?>

                <div class="d-flex align-items-center">
                    <div class="challenge-thumb-wrapper me-3 flex-shrink-0 position-relative">
                        <img src="<?= e($imgSrc) ?>" alt="<?= e($challengeTitle) ?>" class="challenge-thumb rounded">
                    </div>
                    <div class="flex-grow-1 min-w-0 pe-2">
                        <h6 class="card-title fw-bold mb-2 pe-3 text-wrap-title" title="<?= e($challengeTitle) ?>">
                            <?= e($challengeTitle) ?>
                        </h6>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge bg-warning text-dark fw-bold points-badge">
                                <i class="bi bi-star-fill me-1"></i><?= $pointsValue ?> pts
                            </span>
                            <span class="btn-start-icon text-primary fw-bold small">
                                <?= $isCompleted ? 'Review' : 'Start' ?> <i class="bi bi-arrow-right-short"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php
}

/**
 * Fetch and render challenges grouped by category with a sticky category filter bar.
 */
function displayResultsByCategory(PDO $conn, int $projectID, ?int $userID): void
{
    // Fetch user's completed challenges into an array for fast lookup
    $completedChallengeIDs = [];
    if ($userID) {
        $compStmt = $conn->prepare("SELECT challengeID FROM UserChallenges WHERE userID = ?");
        $compStmt->execute([$userID]);
        $completedChallengeIDs = $compStmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    $sql = "
        SELECT cat.CategoryName, ch.*
        FROM Category AS cat
        JOIN Challenges AS ch        ON cat.id = ch.categoryID
        JOIN ProjectChallenges AS pc ON ch.id = pc.challenge_id
        JOIN Projects AS p           ON pc.project_id = p.project_id
        WHERE p.project_id = :project_id AND ch.Enabled = 1
        ORDER BY cat.CategoryName, ch.challengeTitle;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':project_id', $projectID, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo "<div class='alert alert-info shadow-sm'><i class='bi bi-info-circle me-2'></i>No challenges have been assigned to this project yet. Check back soon!</div>";
        return;
    }

    // Group challenges by category name
    $categories = [];
    foreach ($rows as $row) {
        $categories[$row['CategoryName']][] = $row;
    }
    ?>

    <!-- Category Filter Menu -->
    <div class="category-filter-bar sticky-top py-2.5 mb-4 shadow-sm bg-body rounded-3 border">
        <div class="d-flex align-items-center px-3 overflow-x-auto gap-2">
            <span class="text-muted small fw-bold me-2 text-uppercase d-none d-md-inline text-nowrap">
                <i class="bi bi-funnel-fill me-1"></i> Jump To:
            </span>
            <button class="btn btn-sm btn-primary rounded-pill px-3 filter-btn active" data-filter="all">All</button>
            <?php foreach (array_keys($categories) as $index => $catName): ?>
                <?php $slug = 'cat-' . md5($catName); ?>
                <a href="#<?= $slug ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 text-nowrap filter-btn" data-filter="<?= $slug ?>">
                    <?= e($catName) ?> <span class="badge bg-secondary-subtle text-body ms-1"><?= count($categories[$catName]) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Category Sections -->
    <div class="category-containers">
        <?php foreach ($categories as $catName => $catChallenges): ?>
            <?php $slug = 'cat-' . md5($catName); ?>
            <div class="category-group mb-4" id="<?= $slug ?>">
                <div class="d-flex align-items-center mb-3 border-bottom pb-2">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-folder2-open me-2"></i><?= e($catName) ?>
                    </h5>
                    <span class="badge bg-light text-muted border ms-2"><?= count($catChallenges) ?> Challenges</span>
                </div>
                <div class="row g-3">
                    <?php foreach ($catChallenges as $challenge): ?>
                        <?php 
                            $isCompleted = in_array($challenge['ID'], $completedChallengeIDs);
                            createChallengeCard($challenge, $isCompleted); 
                        ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const catGroups = document.querySelectorAll('.category-group');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    const filter = this.getAttribute('data-filter');

                    filterBtns.forEach(b => {
                        b.classList.remove('btn-primary', 'active');
                        b.classList.add('btn-outline-secondary');
                    });
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-primary', 'active');

                    if (filter === 'all') {
                        catGroups.forEach(group => group.style.display = 'block');
                    } else {
                        catGroups.forEach(group => {
                            if (group.id === filter) {
                                group.style.display = 'block';
                            } else {
                                group.style.display = 'none';
                            }
                        });
                    }
                });
            });
        });
    </script>
    <?php
}
?>

<head>
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>assets/css/moduleList.css">
    <style>
        .project-header {
            background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(0,0,0,0.05) 100%);
            border-left: 5px solid #ffc107;
            padding: 1.25rem 1.75rem;
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
        }

        /* Condensed Card Styling */
        .condensed-challenge-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            border-radius: 0.5rem;
            background-color: var(--bs-card-bg, #ffffff);
        }

        /* Visual cue for completed challenges */
        .condensed-challenge-card.challenge-completed {
            border-left: 4px solid #198754 !important;
            background: linear-gradient(to right, rgba(25, 135, 84, 0.03), transparent);
        }

        .completion-tag {
            font-size: 0.675rem;
            border-bottom-left-radius: 0.375rem;
            z-index: 5;
        }

        .challenge-card-link:hover .condensed-challenge-card {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12) !important;
            border-color: #ffc107 !important;
        }

        /* Image Thumbnail */
        .challenge-thumb-wrapper {
            width: 80px;
            height: 80px;
            overflow: hidden;
            border-radius: 0.5rem;
            background: #f8f9fa;
            border: 1px solid rgba(0,0,0,0.08);
        }

        .challenge-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .text-wrap-title {
            font-size: 0.975rem;
            color: var(--bs-body-color);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.25;
        }

        .points-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        .btn-start-icon {
            transition: transform 0.2s ease;
        }

        .challenge-card-link:hover .btn-start-icon {
            transform: translateX(3px);
        }

        .category-filter-bar {
            top: 70px;
            z-index: 1020;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .overflow-x-auto {
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: thin;
        }
    </style>
</head>

<div class="container-fluid px-3 px-lg-5 py-4">
    <!-- Project Header Section -->
    <div class="project-header shadow-sm">
        <h1 class="display-5 fw-bold"><?= e($projectTitle) ?></h1>
        <?php if ($projectDescription): ?>
            <p class="lead text-muted mb-0 small" style="font-size: 1.05rem;"><?= nl2br(e($projectDescription)) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <?php displayResultsByCategory($conn, $projectID, $userID); ?>
    </div>
</div>

<?php
// Flush the buffer only after we've done potential redirects above
ob_end_flush();
?>