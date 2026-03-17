<?php
/**
 * challengeEdit.php
 * Administrative page for editing existing challenges.
 * Includes a challenge selection list if no ID is specified.
 */

ob_start();
require_once "../../includes/template.php";
/** @var PDO $conn */

// 1. Authorisation Check: Administrators only
if (!authorisedAccess(false, true, true)) {
    header("Location: ../../index.php");
    exit;
}

$message = "";
$messageType = "";

// 2. Handle Form Submission (Update)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_challenge'])) {
    $challengeID = filter_input(INPUT_POST, 'challengeID', FILTER_VALIDATE_INT);
    try {
        $challengeTitle    = $_POST["challengeTitle"];
        $challengeText     = $_POST["challengeText"];
        $flag              = $_POST["flag"];
        $pointsValue       = (int)$_POST["pointsValue"];
        $moduleName        = $_POST["moduleName"];
        $moduleValue       = $_POST["moduleValue"];
        $dockerChallengeID = !empty($_POST["dockerChallengeID"]) ? $_POST["dockerChallengeID"] : null;
        $container         = !empty($_POST["container"]) ? (int)$_POST["container"] : 0;
        $enabled           = (int)$_POST["enabled"];
        $categoryID        = (int)$_POST["categoryID"];
        $projectID         = (int)$_POST["projectID"];

        // Handle Image Upload
        $image = $_POST['existing_image'] ?? ''; 
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
            $targetDir = "/var/www" . BASE_URL . "assets/img/challengeImages/";
            $fileName = basename($_FILES["image"]["name"]);
            $targetFile = $targetDir . $fileName;
            
            if (getimagesize($_FILES["image"]["tmp_name"]) && $_FILES["image"]["size"] < 5000000) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    $image = $fileName;
                }
            }
        }

        $updateSql = "UPDATE Challenges SET 
                        challengeTitle = :title, challengeText = :text, flag = :flag, 
                        pointsValue = :points, moduleName = :mName, moduleValue = :mVal, 
                        dockerChallengeID = :dockerID, container = :container, 
                        Image = :image, Enabled = :enabled, categoryID = :catID 
                      WHERE ID = :id";
        
        $stmt = $conn->prepare($updateSql);
        $stmt->execute([
            ':title'     => $challengeTitle, ':text' => $challengeText, ':flag' => $flag,
            ':points'    => $pointsValue, ':mName' => $moduleName, ':mVal' => $moduleValue,
            ':dockerID'  => $dockerChallengeID, ':container' => $container, ':image' => $image,
            ':enabled'   => $enabled, ':catID' => $categoryID, ':id' => $challengeID
        ]);

        $conn->prepare("DELETE FROM ProjectChallenges WHERE challenge_id = ?")->execute([$challengeID]);
        $conn->prepare("INSERT INTO ProjectChallenges (challenge_id, project_id) VALUES (?, ?)")
             ->execute([$challengeID, $projectID]);

        $message = "Challenge '{$challengeTitle}' updated successfully.";
        $messageType = "success";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "danger";
    }
}

// 3. Determine View Mode
$challengeID = filter_input(INPUT_GET, 'challengeID', FILTER_VALIDATE_INT);

function e(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Manage Challenges</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap Icons (Optional fallback if not in template) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container py-5">
    
    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> shadow-sm"><?= e($message) ?></div>
    <?php endif; ?>

    <?php if (!$challengeID): ?>
        <!-- LIST VIEW: Select a challenge to edit -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold text-dark mb-1">Challenge Management</h1>
                <p class="text-muted">Select an existing challenge to modify its configuration.</p>
            </div>
            <a href="challengeRegister.php" class="btn btn-success fw-bold">
                <i class="bi bi-plus-lg me-1"></i> New Challenge
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Points</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT c.ID, c.challengeTitle, c.pointsValue, c.Enabled, cat.CategoryName 
                                  FROM Challenges c 
                                  LEFT JOIN Category cat ON c.categoryID = cat.id 
                                  ORDER BY c.ID DESC";
                        $stmt = $conn->query($query);
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                            <tr>
                                <td class="fw-bold"><?= $row['ID'] ?></td>
                                <td>
                                    <span class="fw-bold"><?= e($row['challengeTitle']) ?></span>
                                </td>
                                <td><span class="badge bg-secondary"><?= e($row['CategoryName'] ?? 'Uncategorized') ?></span></td>
                                <td><?= $row['pointsValue'] ?></td>
                                <td>
                                    <?php if ($row['Enabled']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Disabled</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="?challengeID=<?= $row['ID'] ?>" class="btn btn-sm btn-primary px-3">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>
        <!-- EDIT VIEW: Form for specific challenge -->
        <?php
        $stmt = $conn->prepare("
            SELECT c.*, pc.project_id 
            FROM Challenges c
            LEFT JOIN ProjectChallenges pc ON c.ID = pc.challenge_id
            WHERE c.ID = ?
        ");
        $stmt->execute([$challengeID]);
        $challenge = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$challenge):
            echo "<div class='alert alert-danger'>Challenge not found. <a href='challengeEdit.php'>Go back to list</a></div>";
        else:
        ?>
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="challengeEdit.php">Challenge List</a></li>
                    <li class="breadcrumb-item active">Edit: <?= e($challenge['challengeTitle']) ?></li>
                </ol>
            </nav>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white p-3">
                    <h4 class="mb-0">Update Configuration (ID: <?= $challengeID ?>)</h4>
                </div>
                <div class="card-body p-4">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="challengeID" value="<?= $challengeID ?>">
                        <input type="hidden" name="existing_image" value="<?= e($challenge['Image']) ?>">
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Challenge Title</label>
                                <input type="text" class="form-control" name="challengeTitle" value="<?= e($challenge['challengeTitle']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Flag</label>
                                <input type="text" class="form-control" name="flag" value="<?= e($challenge['flag']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Challenge Text</label>
                            <textarea class="form-control" name="challengeText" rows="6" required><?= e($challenge['challengeText']) ?></textarea>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Points Value</label>
                                <input type="number" class="form-control" name="pointsValue" value="<?= $challenge['pointsValue'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Container ID</label>
                                <input type="number" class="form-control" name="container" value="<?= $challenge['container'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select" name="enabled">
                                    <option value="1" <?= $challenge['Enabled'] == 1 ? 'selected' : '' ?>>Enabled</option>
                                    <option value="0" <?= $challenge['Enabled'] == 0 ? 'selected' : '' ?>>Disabled</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Module Name</label>
                                <input type="text" class="form-control" name="moduleName" value="<?= e($challenge['moduleName']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Module Value</label>
                                <input type="text" class="form-control" name="moduleValue" value="<?= e($challenge['moduleValue']) ?>" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Docker Challenge ID</label>
                                <input type="text" class="form-control" name="dockerChallengeID" value="<?= e($challenge['dockerChallengeID'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Category</label>
                                <select class="form-select" name="categoryID" required>
                                    <?php
                                    $cats = $conn->query("SELECT id, CategoryName FROM Category ORDER BY CategoryName ASC");
                                    while ($cat = $cats->fetch()): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $challenge['categoryID'] ? 'selected' : '' ?>>
                                            <?= e($cat['CategoryName']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Project Association</label>
                                <select class="form-select" name="projectID" required>
                                    <?php
                                    $projs = $conn->query("SELECT project_id, project_title FROM Projects ORDER BY project_title ASC");
                                    while ($proj = $projs->fetch()): ?>
                                        <option value="<?= $proj['project_id'] ?>" <?= $proj['project_id'] == $challenge['project_id'] ? 'selected' : '' ?>>
                                            <?= e($proj['project_title']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Update Image (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <?php if (!empty($challenge['Image'])): ?>
                                        <img src="<?= BASE_URL ?>assets/img/challengeImages/<?= e($challenge['Image']) ?>" style="width:30px; height:20px; object-fit:cover;" class="rounded">
                                    <?php else: ?>
                                        <i class="bi bi-image text-muted"></i>
                                    <?php endif; ?>
                                </span>
                                <input type="file" class="form-control" name="image">
                            </div>
                            <div class="form-text">
                                Current: <?= !empty($challenge['Image']) ? e($challenge['Image']) : '<span class="text-danger italic">No image set</span>' ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-4 mt-2">
                            <a href="challengeEdit.php" class="btn btn-outline-secondary px-4">Back to List</a>
                            <button type="submit" name="update_challenge" class="btn btn-primary px-5 fw-bold">Apply Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
<?php
ob_end_flush();
?>