<?php
/**
 * projectEdit.php
 * Administrative page for selecting and editing existing projects.
 */

ob_start();

require_once "../../includes/template.php";
/** @var PDO $conn */

// 1. Authorisation Check: Ensure only administrators can access this page
if (!authorisedAccess(false, true, true)) {
    header("Location: ../../index.php");
    exit;
}

$message = "";
$messageType = "";
$project = null;
$allProjects = [];

// 2. Fetch all projects for the selection list
try {
    $stmtAll = $conn->query("SELECT project_id, project_name, project_title FROM Projects ORDER BY project_id DESC");
    $allProjects = $stmtAll->fetchAll();
} catch (PDOException $e) {
    $message = "Database error fetching list: " . $e->getMessage();
    $messageType = "danger";
}

// 3. Fetch Specific Project Data if ID is provided
$p_id_query = $_GET['project_id'] ?? '';

if (!empty($p_id_query) && is_numeric($p_id_query)) {
    try {
        $stmt = $conn->prepare("SELECT * FROM Projects WHERE project_id = ?");
        $stmt->execute([$p_id_query]);
        $project = $stmt->fetch();

        if (!$project) {
            $message = "Project #{$p_id_query} not found.";
            $messageType = "danger";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
        $messageType = "danger";
    }
}

// 4. Handle Form Submission (Update)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_project'])) {
    $p_id    = $_POST['project_id']; // Hidden field
    $p_name  = trim($_POST['project_name'] ?? '');
    $p_title = trim($_POST['project_title'] ?? '');
    $p_desc  = trim($_POST['project_description'] ?? '');

    if (empty($p_name) || empty($p_title)) {
        $message = "Internal Name and Display Title are required.";
        $messageType = "danger";
    } else {
        try {
            $update = $conn->prepare("UPDATE Projects SET project_name = ?, project_title = ?, project_description = ? WHERE project_id = ?");
            $update->execute([$p_name, $p_title, $p_desc, $p_id]);
            
            $message = "Project #{$p_id} updated successfully!";
            $messageType = "success";
            
            // Refresh local object for the form
            $project['project_name'] = $p_name;
            $project['project_title'] = $p_title;
            $project['project_description'] = $p_desc;

        } catch (PDOException $e) {
            $message = "Error updating project: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// Helper for safe HTML
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Edit Project</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .admin-container { max-width: 1000px; margin: 0 auto; }
        .form-card { border-left: 5px solid #ffc107; }
        .selection-card { border-left: 5px solid #0d6efd; }
        .project-row:hover { background-color: #f8f9fa; cursor: pointer; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5 admin-container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold text-dark">
                <?php if ($project): ?>
                    Editing Project #<?= e($project['project_id']) ?>
                <?php else: ?>
                    Select Project to Edit
                <?php endif; ?>
            </h1>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= e($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!$project): ?>
        <!-- STEP 1: Selection List -->
        <div class="card shadow-sm selection-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Current Projects</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Internal Name</th>
                                <th>Display Title</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($allProjects)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No projects found in database.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($allProjects as $proj): ?>
                                    <tr class="project-row" onclick="window.location.href='?project_id=<?= $proj['project_id'] ?>'">
                                        <td class="ps-4 fw-bold">#<?= e($proj['project_id']) ?></td>
                                        <td><code><?= e($proj['project_name']) ?></code></td>
                                        <td><?= e($proj['project_title']) ?></td>
                                        <td class="text-end pe-4">
                                            <a href="?project_id=<?= $proj['project_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                Select & Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- STEP 2: Edit Form -->
        <div class="card shadow-sm mb-4 form-card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Project Details</h5>
                <a href="projectEdit.php" class="btn btn-sm btn-secondary">Back to Selection</a>
            </div>
            <div class="card-body">
                <form action="projectEdit.php?project_id=<?= e($project['project_id']) ?>" method="POST">
                    <input type="hidden" name="project_id" value="<?= e($project['project_id']) ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Internal Module Name</label>
                            <input type="text" name="project_name" class="form-control" 
                                   value="<?= e($project['project_name']) ?>" required>
                            <div class="form-text">Used for MQTT topics (e.g., <code>Windmill</code>).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Display Title</label>
                            <input type="text" name="project_title" class="form-control" 
                                   value="<?= e($project['project_title']) ?>" required>
                            <div class="form-text">User-facing name on the website.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="project_description" class="form-control" rows="4"><?= e($project['project_description']) ?></textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="projectEdit.php" class="btn btn-light border">Cancel</a>
                        <button type="submit" name="update_project" class="btn btn-warning px-4 fw-bold">
                            Update Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

</div>

</body>
</html>