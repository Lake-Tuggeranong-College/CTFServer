<?php include "../../includes/template.php";
/** @var $conn */
if (!authorisedAccess(false, false, true)) {
    header("Location:../../index.php");
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white p-3">
                    <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Register New Challenge</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $uploadOk = 1;
                        $targetFile = "default_challenge.png"; // Default image name

                        // Handle Image Upload logic
                        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
                            $targetDir = "/var/www" . BASE_URL . "html/assets/img/challengeImages/";
                            $fileName = basename($_FILES["image"]["name"]);
                            $targetFilePath = $targetDir . $fileName;

                            $check = getimagesize($_FILES["image"]["tmp_name"]);
                            if ($check !== false) {
                                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                                    $targetFile = $fileName;
                                }
                            }
                        }

                        // Prepare Data
                        $challengeTitle = $_POST["challengeTitle"];
                        $challengeText = $_POST["challengeText"];
                        $flag = $_POST["flag"];
                        $pointsValue = $_POST["pointsValue"];
                        $enabled = isset($_POST["enabled"]) ? 1 : 0;
                        $categoryID = $_POST["categoryID"];
                        $projectID = $_POST["projectID"];

                        // Conditional Logic for Module/Docker
                        $moduleName = (!empty($_POST['hasModule'])) ? $_POST["moduleName"] : null;
                        $moduleValue = (!empty($_POST['hasModule'])) ? $_POST["moduleValue"] : null;
                        $dockerChallengeID = (!empty($_POST['hasDocker'])) ? $_POST["dockerChallengeID"] : null;
                        $container = (!empty($_POST['hasDocker'])) ? $_POST["container"] : null;

                        $insertSql = "INSERT INTO Challenges (challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID) 
                                      VALUES (:title, :text, :flag, :points, :mName, :mVal, :dId, :cont, :img, :enabled, :catId)";

                        $stmt = $conn->prepare($insertSql);
                        $stmt->execute([
                            ':title' => $challengeTitle, ':text' => $challengeText, ':flag' => $flag,
                            ':points' => $pointsValue, ':mName' => $moduleName, ':mVal' => $moduleValue,
                            ':dId' => $dockerChallengeID, ':cont' => $container, ':img' => $targetFile,
                            ':enabled' => $enabled, ':catId' => $categoryID
                        ]);

                        $challengeID = $conn->lastInsertId();
                        $stmtProject = $conn->prepare("INSERT INTO ProjectChallenges (challenge_id, project_id) VALUES (?, ?)");
                        
                        if ($stmtProject->execute([$challengeID, $projectID])) {
                            echo "<div class='alert alert-success'>Challenge created successfully!</div>";
                        }
                    }
                    ?>

                    <form method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-9">
                                <label class="form-label fw-bold">Challenge Title *</label>
                                <input type="text" class="form-control form-control-lg" name="challengeTitle" placeholder="e.g. SQL Injection 101" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Points *</label>
                                <input type="number" class="form-control form-control-lg" name="pointsValue" placeholder="100" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Challenge Description *</label>
                            <textarea class="form-control" name="challengeText" rows="4" required placeholder="Describe the objective of the challenge..."></textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">Challenge Flag *</label>
                                <input type="text" class="form-control border-primary" name="flag" placeholder="CTF{secret_string}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Image Upload</label>
                                <input type="file" class="form-control" name="image">
                                <div class="form-text">Optional: Defaults to placeholder if empty.</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Associated Project *</label>
                                <select class="form-select" name="projectID" required>
                                    <option value="" selected disabled>Select a project...</option>
                                    <?php
                                    $projectList = $conn->query("SELECT project_id, project_name FROM CyberCity.Projects");
                                    while ($row = $projectList->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . $row['project_id'] . '">' . htmlspecialchars($row['project_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Category *</label>
                                <select class="form-select" name="categoryID" required>
                                    <option value="" selected disabled>Select a category...</option>
                                    <?php
                                    $categoryList = $conn->query("SELECT id, CategoryName FROM CyberCity.Category");
                                    while ($row = $categoryList->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['CategoryName']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted fw-bold">Additional Configuration</h6>
                                <div class="row text-center">
                                    <div class="col-md-4 mb-2 mb-md-0">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" id="moduleToggle" name="hasModule" onclick="toggleSection('moduleFields', this)">
                                            <label class="form-check-label" for="moduleToggle">Link Module?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2 mb-md-0">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" id="dockerToggle" name="hasDocker" onclick="toggleSection('dockerFields', this)">
                                            <label class="form-check-label" for="dockerToggle">Link Docker?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" name="enabled" value="1" checked>
                                            <label class="form-check-label">Challenge Enabled</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="moduleFields" style="display:none;" class="row g-3 mb-4 p-3 border rounded-3 bg-white mx-0">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Module Name *</label>
                                <input type="text" class="form-control" name="moduleName" id="moduleNameInput">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Module Value *</label>
                                <input type="text" class="form-control" name="moduleValue" id="moduleValueInput">
                            </div>
                        </div>

                        <div id="dockerFields" style="display:none;" class="row g-3 mb-4 p-3 border rounded-3 bg-white mx-0">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Docker Challenge ID *</label>
                                <input type="text" class="form-control" name="dockerChallengeID" id="dockerIdInput">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Container Port/ID *</label>
                                <input type="number" class="form-control" name="container" id="containerInput">
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="bi bi-cloud-arrow-up me-2"></i>Register Challenge
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSection(sectionId, checkbox) {
    const section = document.getElementById(sectionId);
    const inputs = section.querySelectorAll('input');
    
    if (checkbox.checked) {
        section.style.display = 'flex';
        inputs.forEach(input => input.setAttribute('required', ''));
    } else {
        section.style.display = 'none';
        inputs.forEach(input => {
            input.removeAttribute('required');
            input.value = '';
        });
    }
}
</script>