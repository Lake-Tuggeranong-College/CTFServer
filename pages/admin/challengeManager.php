<?php
/**
 * challengeManager.php
 * Admin page for exporting and importing challenges via JSON.
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

// 2. Handle Export Logic
if (isset($_POST['export_selected'])) {
    $selectedIds = $_POST['challenge_ids'] ?? [];
    if (!empty($selectedIds)) {
        $placeholders = str_repeat('?,', count($selectedIds) - 1) . '?';
        // Select all fields based on the specified schema
        $stmt = $conn->prepare("SELECT id, challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID, files FROM Challenges WHERE id IN ($placeholders)");
        $stmt->execute($selectedIds);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $jsonOutput = json_encode($data, JSON_PRETTY_PRINT);
        $filename = "challenges_full_export_" . date('Y-m-d_H-i') . ".json";

        // Clear any previously buffered HTML from template.php
        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($jsonOutput));
        echo $jsonOutput;
        exit; 
    } else {
        $message = "Please select at least one challenge to export.";
        $messageType = "warning";
    }
}

// 3. Handle Import Logic (Updated to always create new entries)
if (isset($_POST['import_file']) && isset($_FILES['json_file'])) {
    if ($_FILES['json_file']['error'] === UPLOAD_ERR_OK) {
        $fileData = file_get_contents($_FILES['json_file']['tmp_name']);
        $challenges = json_decode($fileData, true);

        if (is_array($challenges)) {
            $importedCount = 0;
            try {
                $conn->beginTransaction();

                foreach ($challenges as $item) {
                    // Remove 'id' from the item to allow DB auto-increment to handle it
                    if (isset($item['id'])) {
                        unset($item['id']);
                    }

                    // INSERT NEW ENTRY
                    $keys = array_keys($item);
                    $cols = implode('`, `', $keys);
                    $placeholders = str_repeat('?,', count($keys) - 1) . '?';
                    
                    $sql = "INSERT INTO Challenges (`$cols`) VALUES ($placeholders)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(array_values($item));
                    $importedCount++;
                }
                
                $conn->commit();
                $message = "Import Successful: $importedCount new challenges created.";
                $messageType = "success";
            } catch (Exception $e) {
                if ($conn->inTransaction()) $conn->rollBack();
                $message = "Import Failed: " . $e->getMessage();
                $messageType = "danger";
            }
        } else {
            $message = "Invalid JSON format.";
            $messageType = "danger";
        }
    } else {
        $message = "Error uploading file.";
        $messageType = "danger";
    }
}

// 4. Fetch All Challenges for View
$challengesList = [];
try {
    $challengesList = $conn->query("SELECT id, challengeTitle, pointsValue, moduleName FROM Challenges ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    $message = "Could not load challenges: " . $e->getMessage();
    $messageType = "danger";
}

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Challenge Manager</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .admin-container { max-width: 1200px; margin: 0 auto; }
        .export-card { border-left: 5px solid #0dc6fd; }
        .import-card { border-left: 5px solid #6610f2; }
        .sticky-top { background: white; z-index: 10; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5 admin-container">

    <div class="mb-4">
        <h1 class="fw-bold text-primary">Challenge Data Manager</h1>
        <p class="text-muted">Export selected challenges to JSON or import them back into the database. 
           <strong>Note:</strong> Importing always creates new entries using your database's auto-incrementing IDs.</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= e($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Export Section -->
        <div class="col-lg-8">
            <form method="POST">
                <div class="card shadow-sm export-card h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Select Challenges</h5>
                        <button type="submit" name="export_selected" class="btn btn-primary btn-sm px-4 shadow-sm">
                            <i class="bi bi-download me-2"></i>Export Selected (.json)
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-4" width="40">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th width="80">ID</th>
                                        <th>Title</th>
                                        <th>Module</th>
                                        <th class="text-center">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($challengesList)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted italic">No challenges found in database.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($challengesList as $ch): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <input type="checkbox" name="challenge_ids[]" value="<?= $ch['id'] ?>" class="form-check-input chk-item">
                                                </td>
                                                <td><span class="text-muted small">#</span><?= e($ch['id']) ?></td>
                                                <td class="fw-semibold text-dark"><?= e($ch['challengeTitle']) ?></td>
                                                <td><code class="small bg-light p-1 border rounded"><?= e($ch['moduleName'] ?: 'None') ?></code></td>
                                                <td class="text-center">
                                                    <span class="badge bg-info text-dark border-info border-opacity-25"><?= e($ch['pointsValue']) ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Import Section -->
        <div class="col-lg-4">
            <div class="card shadow-sm import-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Import Data</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-4">
                        Upload a challenge JSON file. This will <strong>create new copies</strong> of the challenges in your database.
                    </p>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Select JSON File</label>
                            <input type="file" name="json_file" class="form-control form-control-sm" accept=".json" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="import_file" class="btn btn-dark fw-bold shadow-sm">
                                Import as New Entries
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm bg-white">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Import Logic</h6>
                    <p class="small text-muted mb-0">
                        The system now automatically unsets the ID field from imported data, allowing your database's <code>AUTO_INCREMENT</code> logic to assign fresh IDs to every imported challenge.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle Select All
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.chk-item');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>

</body>
</html>