<?php
ob_start();
require_once "../../includes/template.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("UPDATE Challenges SET moduleValue = :val WHERE challengeTitle = :name");
    $stmt->execute([':val' => $_POST['train_active'] ? 1 : 0, ':name' => 'The Train']);
}
?>

<!DOCTYPE html>
<html lang="en">
    <div class="text-center" style = "margin-top: 70px;">
        <h1>emergency stop</h1>
        <p>the train is currently <?php echo $_POST["train_active"] == true ? "running" : "stopped"; ?></p>
        <p><?php echo $_POST["train_active"]?></p>
        <form method="post">
            <input type="hidden" name="train_active" value="false">
            <button type="submit">stop train</button>
        </form>
    </div>
</html>