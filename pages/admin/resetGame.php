<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();

include_once "../../includes/template.php";

/** @var $conn */
if (!authorisedAccess(false, false, true)) {
    header("Location:../../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Disable all regular users (AccessLevel 1)
    $query = "UPDATE Users SET Enabled = 0 WHERE AccessLevel = '1'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Redirect to self to prevent form resubmission on refresh
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<div class="container mt-5">
    <div class="alert alert-danger shadow-sm border-start border-5 border-danger">
        <h1 class="h3"><i class="bi bi-exclamation-triangle-fill me-2"></i> WARNING</h1>
        <p class="mb-0">Pressing the button below will set all regular users (Access Level 1) to <strong>Inactive</strong> status.</p>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-body text-center py-5">
            <form action="resetGame.php" method="post">
                <button type="submit" name="formSubmit" class="btn btn-danger btn-lg px-5">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset All User Access
                </button>
            </form>
        </div>
    </div>
</div>

<?php
// Flush the output buffer and turn off buffering
ob_end_flush();
?>