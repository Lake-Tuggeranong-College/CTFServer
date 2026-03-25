<?php
/**
 * categoryEdit.php
 * Administrative page for selecting and editing existing categories.
 */

ob_start();
include "../../includes/template.php";
/** @var PDO $conn */

// 1. Authorisation Check
if (!authorisedAccess(false, false, true)) {
    header("Location: ../../index.php");
    exit;
}

$message = "";
$messageType = "";
$category = null;
$allCategories = [];

// 2. Fetch all categories and their project names for the selection list
try {
    // Schema update: PK is 'id', FK is 'projectID'
    // Sorting updated to ProjectID followed by id
    $sql = "SELECT c.id, c.CategoryName, p.project_name, p.project_id 
            FROM Category c 
            JOIN Projects p ON c.projectID = p.project_id 
            ORDER BY c.projectID ASC, c.id ASC";
    $stmtAll = $conn->query($sql);
    $allCategories = $stmtAll->fetchAll();
} catch (PDOException $e) {
    $message = "Database error fetching list: " . $e->getMessage();
    $messageType = "danger";
}

// 3. Fetch Specific Category Data if ID is provided
$cat_id_query = $_GET['id'] ?? '';

if (!empty($cat_id_query) && is_numeric($cat_id_query)) {
    try {
        $stmt = $conn->prepare("SELECT * FROM Category WHERE id = ?");
        $stmt->execute([$cat_id_query]);
        $category = $stmt->fetch();

        if (!$category) {
            $message = "Category #{$cat_id_query} not found.";
            $messageType = "danger";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
        $messageType = "danger";
    }
}

// 4. Handle Form Submission (Update)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_category'])) {
    $c_id   = $_POST['id'];
    $c_name = trim($_POST['category_name'] ?? '');
    $p_id   = $_POST['projectID'] ?? '';

    if (empty($c_name) || empty($p_id)) {
        $message = "Category Name and Project are required.";
        $messageType = "danger";
    } else {
        try {
            $update = $conn->prepare("UPDATE Category SET CategoryName = ?, projectID = ? WHERE id = ?");
            $update->execute([$c_name, $p_id, $c_id]);
            
            $message = "Category updated successfully!";
            $messageType = "success";
            
            // Refresh local object for the form
            $category['CategoryName'] = $c_name;
            $category['projectID'] = $p_id;

            // Refresh the list to reflect changes in sorting if IDs or projects changed
            $stmtAll = $conn->query($sql);
            $allCategories = $stmtAll->fetchAll();

        } catch (PDOException $e) {
            $message = "Error updating category: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// Helper for safe HTML
function e($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Edit Category</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .admin-container { max-width: 1000px; margin: 0 auto; }
        .form-card { border-left: 5px solid #ffc107; }
        .selection-card { border-left: 5px solid #0d6efd; }
        .category-row:hover { background-color: #f8f9fa; cursor: pointer; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5 admin-container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 fw-bold text-dark">
            <?php if ($category): ?>
                Editing Category: <?= e($category['CategoryName']) ?>
            <?php else: ?>
                Select Category to Edit
            <?php endif; ?>
        </h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= e($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!$category): ?>
        <!-- STEP 1: Selection List -->
        <div class="card shadow-sm selection-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Current Categories</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Category Name</th>
                                <th>Belongs to Project</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($allCategories)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No categories found in database.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($allCategories as $row): ?>
                                    <tr class="category-row" onclick="window.location.href='?id=<?= $row['id'] ?>'">
                                        <td class="ps-4 fw-bold">#<?= e($row['id']) ?></td>
                                        <td><?= e($row['CategoryName']) ?></td>
                                        <td><span class="badge bg-secondary"><?= e($row['project_name']) ?></span></td>
                                        <td class="text-end pe-4">
                                            <a href="?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                Edit
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
                <h5 class="mb-0 fw-bold">Category Details</h5>
                <a href="categoryEdit.php" class="btn btn-sm btn-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <form action="categoryEdit.php?id=<?= e($category['id']) ?>" method="POST">
                    <input type="hidden" name="id" value="<?= e($category['id']) ?>">
                    
                    <div class="mb-3">
                        <label for="category_name" class="form-label fw-semibold">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" 
                               value="<?= e($category['CategoryName']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="projectID" class="form-label fw-semibold">Parent Project</label>
                        <select class="form-select" id="projectID" name="projectID" required>
                            <?php
                            $projectList = $conn->query("SELECT project_id, project_name FROM Projects ORDER BY project_name ASC");
                            while ($pRow = $projectList->fetch(PDO::FETCH_ASSOC)) {
                                $selected = ($pRow['project_id'] == $category['projectID']) ? 'selected' : '';
                                echo '<option value="' . e($pRow['project_id']) . '" ' . $selected . '>' . e($pRow['project_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="categoryEdit.php" class="btn btn-light border">Cancel</a>
                        <button type="submit" name="update_category" class="btn btn-warning px-4 fw-bold">
                            Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

</div>

</body>
</html>