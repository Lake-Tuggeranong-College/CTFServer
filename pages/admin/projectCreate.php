<?php
/**
 * projectCreate.php
 * Administrative page for creating and managing projects.
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

// 2. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_project'])) {
    $p_id    = trim($_POST['project_id'] ?? '');
    $p_name  = trim($_POST['project_name'] ?? '');
    $p_title = trim($_POST['project_title'] ?? '');
    $p_desc  = trim($_POST['project_description'] ?? '');

    if (empty($p_id) || empty($p_name) || empty($p_title)) {
        $message = "Project ID, Name, and Title are required.";
        $messageType = "danger";
    } elseif (!is_numeric($p_id)) {
        $message = "Project ID must be a numeric value.";
        $messageType = "danger";
    } else {
        try {
            // Check if ID already exists
            $check = $conn->prepare("SELECT 1 FROM Projects WHERE project_id = ?");
            $check->execute([$p_id]);
            
            if ($check->fetch()) {
                $message = "Error: Project ID {$p_id} already exists.";
                $messageType = "danger";
            } else {
                $stmt = $conn->prepare("INSERT INTO Projects (project_id, project_name, project_title, project_description) VALUES (?, ?, ?, ?)");
                $stmt->execute([$p_id, $p_name, $p_title, $p_desc]);
                
                $message = "Project #{$p_id} ('{$p_title}') created successfully!";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            $message = "Error creating project: " . $e->getMessage();
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
    <title>Admin - Create Project</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .admin-container { max-width: 900px; margin: 0 auto; }
        .form-card { border-left: 5px solid #0d6efd; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5 admin-container">


    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 fw-bold text-dark">Create New Project</h1>
        <span class="badge bg-primary px-3 py-2">Administrator Access</span>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= e($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Create Project Form -->
    <div class="card shadow-sm mb-5 form-card">
        <div class="card-header bg-white fw-bold">Project Configuration</div>
        <div class="card-body p-4">
            <form action="<?= e($_SERVER['PHP_SELF']) ?>" method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="project_id" class="form-label fw-bold">Project ID (Numeric)</label>
                        <input type="number" name="project_id" id="project_id" class="form-control" placeholder="e.g. 101" required>
                        <div class="form-text">Unique numerical identifier.</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="project_name" class="form-label fw-bold">Internal Ref Name</label>
                        <input type="text" name="project_name" id="project_name" class="form-control" placeholder="e.g. web_sec_01" required>
                        <div class="form-text">Code-friendly name.</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="project_title" class="form-label fw-bold">Display Title</label>
                        <input type="text" name="project_title" id="project_title" class="form-control" placeholder="e.g. Web Security" required>
                        <div class="form-text">Visible to students.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="project_description" class="form-label fw-bold">Project Description</label>
                    <textarea name="project_description" id="project_description" rows="4" class="form-control" placeholder="Describe the goals and context of this project..."></textarea>
                </div>

                <div class="text-end border-top pt-3 mt-4">
                    <button type="reset" class="btn btn-outline-secondary px-4 me-2">Clear Form</button>
                    <button type="submit" name="create_project" class="btn btn-primary px-5 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Create Project
                    </button>
                </div>
            </form>
        </div>
    </div>

    
</div>

</body>
</html>
<?php
ob_end_flush();
?>