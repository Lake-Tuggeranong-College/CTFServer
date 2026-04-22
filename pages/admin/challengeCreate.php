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
                        $targetFile = "default_challenge.png";

                        // Unique Image Naming Logic
                        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
                            $targetDir = "/var/www" . BASE_URL . "html/assets/img/challengeImages/";
                            $safeTitle = preg_replace('/[^a-z0-9]+/', '-', strtolower($_POST["challengeTitle"]));
                            $uuid = bin2hex(random_bytes(4));
                            $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                            
                            $newFileName = $safeTitle . "-" . $uuid . "." . $extension;
                            $targetFilePath = $targetDir . $newFileName;

                            if (getimagesize($_FILES["image"]["tmp_name"]) !== false) {
                                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                                    $targetFile = $newFileName;
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

                        // Logic for Module/Docker (Container is now strictly NULL)
                        $moduleName = (!empty($_POST['hasModule'])) ? $_POST["moduleName"] : null;
                        $moduleValue = (!empty($_POST['hasModule'])) ? $_POST["moduleValue"] : null;
                        $dockerChallengeID = (!empty($_POST['hasDocker'])) ? $_POST["dockerChallengeID"] : null;
                        $container = null; // Forced to NULL as requested

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
                            echo "<div class='alert alert-success'>Challenge registered successfully!</div>";
                        }
                    }
                    ?>

                    <form method="post" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-9">
                                <label class="form-label fw-bold">Challenge Title *</label>
                                <input type="text" class="form-control form-control-lg" name="challengeTitle" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Points *</label>
                                <input type="number" class="form-control form-control-lg" name="pointsValue" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Challenge Description *</label>
                            <textarea class="form-control" name="challengeText" rows="4" required></textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">Challenge Flag *</label>
                                <input type="text" class="form-control border-primary" name="flag" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Image Upload</label>
                                <input type="file" class="form-control" name="image">
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Associated Project *</label>
                                <select class="form-select" name="projectID" required>
                                    <option value="" selected disabled>Select project...</option>
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
                                    <option value="" selected disabled>Select category...</option>
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
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" id="moduleToggle" name="hasModule" onclick="toggleSection('moduleFields', this)">
                                            <label class="form-check-label" for="moduleToggle">Link Module?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" id="dockerToggle" name="hasDocker" onclick="toggleSection('dockerFields', this)">
                                            <label class="form-check-label" for="dockerToggle">Link Docker?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" name="enabled" value="1" checked>
                                            <label class="form-check-label">Enabled</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="moduleFields" style="display:none;" class="row g-3 mb-4 p-3 border rounded-3 bg-white mx-0">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Module Name *</label>
                                <input type="text" class="form-control" name="moduleName">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Module Value *</label>
                                <input type="text" class="form-control" name="moduleValue">
                            </div>
                        </div>

                        <div id="dockerFields" style="display:none;" class="row g-3 mb-4 p-3 border rounded-3 bg-white mx-0">
                            <div class="col-12 text-center">
                                <label class="form-label fw-semibold">Docker Challenge ID *</label>
                                <input type="text" class="form-control w-50 mx-auto" name="dockerChallengeID">
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">Register Challenge</button>
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