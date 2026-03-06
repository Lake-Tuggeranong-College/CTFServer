<?php
// 1. DATABASE/AUTH CONFIG (Must happen before any output)
require_once "../../includes/template.php"; 

// 2. HANDLE STATUS TOGGLE (Must happen before template.php because it redirects)
if (isset($_GET['toggleID'])) {
    $targetID = intval($_GET['toggleID']);
    $stmt = $conn->prepare("SELECT Enabled FROM Users WHERE ID = ?");
    $stmt->execute([$targetID]);
    $currentUser = $stmt->fetch();

    if ($currentUser) {
        $newStatus = ($currentUser['Enabled'] == 1) ? 0 : 1;
        $updateStmt = $conn->prepare("UPDATE Users SET Enabled = ? WHERE ID = ?");
        $updateStmt->execute([$newStatus, $targetID]);
    }
    
    $limitUrl = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $redirectUrl = "userList.php?limit=" . $limitUrl;
    if (isset($newStatus) && $newStatus == 0) $redirectUrl .= "&pageD=1";

    header("Location: " . $redirectUrl);
    // JS Fallback if headers were somehow sent
    echo "<script>window.location.href='$redirectUrl';</script>";
    exit;
}

// 3. AUTHORIZATION CHECK
/** @var $conn */
if (!authorisedAccess(false, false, true)) {
    header("Location:../../index.php");
    exit;
}

// Pagination Logic
$availableLimits = [5, 10, 20, 50];
$limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $availableLimits) ? (int)$_GET['limit'] : 10;

$pageEnabled = isset($_GET['pageE']) ? (int)$_GET['pageE'] : 1;
$pageDisabled = isset($_GET['pageD']) ? (int)$_GET['pageD'] : 1;

$startEnabled = ($pageEnabled - 1) * $limit;
$startDisabled = ($pageDisabled - 1) * $limit;

$totalEnabled = $conn->query("SELECT COUNT(*) FROM Users WHERE Enabled=1")->fetchColumn();
$totalPagesEnabled = ceil($totalEnabled / $limit);

$totalDisabled = $conn->query("SELECT COUNT(*) FROM Users WHERE Enabled=0")->fetchColumn();
$totalPagesDisabled = ceil($totalDisabled / $limit);

$baseUrl = "userList.php?limit=$limit";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; font-size: 0.95rem; }
        .nav-tabs .nav-link { color: #6c757d; font-weight: 500; font-size: 0.9rem; }
        .nav-tabs .nav-link.active { color: #0d6efd; font-weight: 600; border-bottom: 3px solid #0d6efd; }
        .list-group-item { border: none; margin-bottom: 8px; border-radius: 10px !important; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: transform 0.2s; padding: 0.75rem 1.25rem; }
        .list-group-item:hover { transform: translateX(3px); }
        .access-badge { font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 6px; }
        .btn-action { padding: 0.5rem 0.75rem; font-size: 1.1rem; display: inline-flex; align-items: center; justify-content: center; line-height: 1; border-radius: 8px; }
        .pagination { font-size: 0.85rem; margin-bottom: 0; }
        .h3 { font-size: 1.6rem; font-weight: 700; }
        .limit-selector { width: 70px; font-size: 0.8rem; height: auto; padding: 0.2rem 0.4rem; margin-left: 12px; }
        .btn-toggle-active { color: #198754; }
        .btn-toggle-inactive { color: #dc3545; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Accounts</h1>
        <span class="badge bg-secondary opacity-75">Administrator View</span>
    </div>

    <ul class="nav nav-tabs border-0" id="userTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= !isset($_GET['pageD']) ? 'active' : '' ?>" id="enabled-tab" data-bs-toggle="tab" data-bs-target="#enabled" type="button" role="tab">
                <i class="bi bi-person-check-fill me-1"></i>Active (<?= $totalEnabled ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= isset($_GET['pageD']) ? 'active' : '' ?>" id="disabled-tab" data-bs-toggle="tab" data-bs-target="#disabled" type="button" role="tab">
                <i class="bi bi-person-x-fill me-1"></i>Inactive (<?= $totalDisabled ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content mt-3" id="userTabsContent">
        <div class="tab-pane fade <?= !isset($_GET['pageD']) ? 'show active' : '' ?>" id="enabled" role="tabpanel">
            <?php
            $enabledUsers = $conn->query("SELECT ID, Username, AccessLevel FROM Users WHERE Enabled=1 ORDER BY Username ASC LIMIT $startEnabled, $limit");
            if ($enabledUsers->rowCount() > 0) {
                echo '<div class="list-group">';
                while ($user = $enabledUsers->fetch()) {
                    $badgeClass = ($user["AccessLevel"] >= 2) ? 'bg-danger' : 'bg-info text-dark';
                    $roleName = ($user["AccessLevel"] >= 2) ? 'Admin' : 'User';
                    echo '<div class="list-group-item d-flex justify-content-between align-items-center">';
                    echo '<div><span class="fw-bold text-dark">' . htmlspecialchars($user["Username"]) . '</span><span class="badge access-badge ms-2 ' . $badgeClass . '">' . $roleName . '</span></div>';
                    echo '<div class="btn-group">';
                    echo '<a href="userList.php?toggleID=' . $user["ID"] . '&limit=' . $limit . '" class="btn btn-outline-light btn-action btn-toggle-active" title="Deactivate User"><i class="bi bi-toggle-on"></i></a>';
                    echo '<a href="userEdit.php?UserID=' . $user["ID"] . '" class="btn btn-outline-primary btn-action ms-1" title="Edit User"><i class="bi bi-pencil-square"></i></a>';
                    echo '<a href="userResetPassword.php?UserID=' . $user["ID"] . '" class="btn btn-outline-secondary btn-action ms-1" title="Reset Password"><i class="bi bi-key-fill"></i></a>';
                    echo '</div></div>';
                }
                echo '</div>';
                echo '<div class="d-flex justify-content-center align-items-center mt-4">';
                if ($totalPagesEnabled > 1) {
                    echo '<nav><ul class="pagination pagination-sm">';
                    for ($i = 1; $i <= $totalPagesEnabled; $i++) {
                        $activeClass = ($i == $pageEnabled) ? 'active' : '';
                        echo "<li class='page-item $activeClass'><a class='page-link' href='$baseUrl&pageE=$i'>$i</a></li>";
                    }
                    echo '</ul></nav>';
                }
                ?>
                <select class="form-select form-select-sm limit-selector" onchange="location = 'userList.php?limit=' + this.value;">
                    <?php foreach ($availableLimits as $l): ?>
                        <option value="<?= $l ?>" <?= $limit == $l ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
                </div>
                <?php
            } else { echo '<div class="text-center py-5"><i class="bi bi-people text-muted display-4"></i><p class="mt-2 text-muted">No active users found.</p></div>'; }
            ?>
        </div>

        <div class="tab-pane fade <?= isset($_GET['pageD']) ? 'show active' : '' ?>" id="disabled" role="tabpanel">
            <?php
            $disabledUsers = $conn->query("SELECT ID, Username, AccessLevel FROM Users WHERE Enabled=0 ORDER BY Username ASC LIMIT $startDisabled, $limit");
            if ($disabledUsers->rowCount() > 0) {
                echo '<div class="list-group">';
                while ($user = $disabledUsers->fetch()) {
                    echo '<div class="list-group-item d-flex justify-content-between align-items-center opacity-75">';
                    echo '<div><span class="text-muted fw-bold">' . htmlspecialchars($user["Username"]) . '</span><span class="badge access-badge ms-2 bg-secondary">Inactive</span></div>';
                    echo '<div class="btn-group">';
                    echo '<a href="userList.php?toggleID=' . $user["ID"] . '&limit=' . $limit . '" class="btn btn-outline-light btn-action btn-toggle-inactive" title="Activate User"><i class="bi bi-toggle-off"></i></a>';
                    echo '<a href="userEdit.php?UserID=' . $user["ID"] . '" class="btn btn-warning btn-action text-dark ms-1" title="Edit/Review User"><i class="bi bi-pencil-square"></i></a>';
                    echo '<a href="userResetPassword.php?UserID=' . $user["ID"] . '" class="btn btn-outline-secondary btn-action ms-1" title="Reset Password"><i class="bi bi-key-fill"></i></a>';
                    echo '</div></div>';
                }
                echo '</div>';
                echo '<div class="d-flex justify-content-center align-items-center mt-4">';
                if ($totalPagesDisabled > 1) {
                    echo '<nav><ul class="pagination pagination-sm">';
                    for ($i = 1; $i <= $totalPagesDisabled; $i++) {
                        $activeClass = ($i == $pageDisabled) ? 'active' : '';
                        echo "<li class='page-item $activeClass'><a class='page-link' href='$baseUrl&pageD=$i'>$i</a></li>";
                    }
                    echo '</ul></nav>';
                }
                ?>
                <select class="form-select form-select-sm limit-selector" onchange="location = 'userList.php?limit=' + this.value;">
                    <?php foreach ($availableLimits as $l): ?>
                        <option value="<?= $l ?>" <?= $limit == $l ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
                </div>
                <?php
            } else { echo '<div class="text-center py-5"><i class="bi bi-person-dash text-muted display-4"></i><p class="mt-2 text-muted">No inactive users found.</p></div>'; }
            ?>
        </div>
    </div>
</div>
</body>
</html>