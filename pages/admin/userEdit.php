<?php
ob_start(); // Start output buffering to prevent "headers already sent" errors
include "../../includes/template.php";
/** @var $conn */

// 1. Authorization Check
if (!authorisedAccess(false, false, true)) {
    header("Location:../../index.php");
    exit;
}

// 2. Handle POST Request (Saving Data)
if (isset($_POST["formSubmit"])) {
    $userToUpdate = intval($_GET["UserID"]);
    $newUsername = $_POST["userName"];
    $newAccessLevel = intval($_POST["AccessLevel"]);
    $newEnabled = intval($_POST["Enabled"]);
    $newScore = intval($_POST["Score"]);

    // Using prepared statements to prevent SQL Injection
    $query = "UPDATE Users SET Username = ?, AccessLevel = ?, Enabled = ?, Score = ? WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $result = $stmt->execute([$newUsername, $newAccessLevel, $newEnabled, $newScore, $userToUpdate]);

    if ($result) {
        // Redirect back to list or show success
        header("Location:userList.php?msg=UserUpdated");
        exit;
    } else {
        $errorMessage = "Failed to update account details.";
    }
}

// 3. Handle GET Request (Loading Data)
if (isset($_GET["UserID"])) {
    $userToLoad = $_GET["UserID"];
    $sql = $conn->query("SELECT * FROM Users WHERE ID = " . intval($userToLoad));
    $userInformation = $sql->fetch();

    if ($userInformation) {
        $userID = $userInformation["ID"];
        $userName = $userInformation["Username"];
        $userAccessLevel = $userInformation["AccessLevel"];
        $userEnabled = $userInformation["Enabled"];
        $userScore = $userInformation["Score"];
    } else {
        header("Location:userList.php");
        exit;
    }
} else {
    header("Location:userList.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage User - <?= htmlspecialchars($userName) ?></title>
    <!-- Bootstrap & Icons are imported via template.php, but adding styles for refined UI -->
    <style>
        body {
            background-color: #f4f7f6;
        }
        .user-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .card-header {
            border-radius: 12px 12px 0 0 !important;
            padding: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
        }
        .btn-update {
            padding: 0.8rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.4em 0.8em;
            border-radius: 20px;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-7">
            
            <?php if (isset($errorMessage)): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?= $errorMessage ?></div>
                </div>
            <?php endif; ?>

            <div class="card user-card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Account Management</h4>
                        <p class="mb-0 small opacity-75">Editing details for: <strong><?= htmlspecialchars($userName) ?></strong></p>
                    </div>
                    <a href="userList.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <form action="userEdit.php?UserID=<?= $userID ?>" method="post">
                        
                        <div class="row">
                            <!-- Username -->
                            <div class="col-md-12 mb-4">
                                <label for="userName" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input type="text" name="userName" id="userName" class="form-control" required
                                           value="<?= htmlspecialchars($userName ?? '') ?>">
                                </div>
                            </div>

                            <!-- Access Level -->
                            <div class="col-md-6 mb-4">
                                <label for="AccessLevel" class="form-label">Permission Level</label>
                                <select name="AccessLevel" id="AccessLevel" class="form-select">
                                    <option value="1" <?= $userAccessLevel == 1 ? 'selected' : '' ?>>Standard User</option>
                                    <option value="2" <?= $userAccessLevel == 2 ? 'selected' : '' ?>>Administrator</option>
                                </select>
                            </div>

                            <!-- Account Status -->
                            <div class="col-md-6 mb-4">
                                <label for="Enabled" class="form-label">Account Status</label>
                                <select name="Enabled" id="Enabled" class="form-select">
                                    <option value="1" <?= $userEnabled == 1 ? 'selected' : '' ?>>Active</option>
                                    <option value="0" <?= $userEnabled == 0 ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>

                            <!-- Score -->
                            <div class="col-md-12 mb-4">
                                <label for="Score" class="form-label">Current Competition Score</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-trophy"></i></span>
                                    <input type="number" name="Score" id="Score" class="form-control" required
                                           value="<?= htmlspecialchars($userScore ?? '') ?>">
                                </div>
                                <div class="form-text text-muted">Manually overriding this value affects the leaderboard.</div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-10">

                        <div class="d-grid">
                            <button type="submit" name="formSubmit" class="btn btn-primary btn-update btn-lg">
                                <i class="bi bi-check-circle-fill me-2"></i> Save Changes
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <small class="text-muted">User unique identifier: <code>#<?= $userID ?></code></small>
            </div>
            
        </div>
    </div>
</div>

</body>
</html>
<?php
ob_end_flush(); // Send the buffer to the browser
?>