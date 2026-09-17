<?php
ob_start();
require_once "../../includes/template.php";

$_POST["train_stopped"] = $_POST["train_stopped"] ?? "true";
$train_bool = $_POST["train_stopped"] == "true" ? 0 : 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("UPDATE Challenges SET moduleValue = :val WHERE challengeTitle = :name");
    $stmt->execute([":val" => $train_bool, ":name" => "Train"]);
}
?>

<!DOCTYPE html>
<html lang="en">
    <div class="text-center" style = "margin-top: 70px;">
        <h1>emergency stop</h1>
        <p>the train is currently <?php echo $_POST["train_stopped"] == "true" ? "stopped" : "running"; ?></p>
        <form method="post">
            <input type="hidden" name="train_stopped" value="true">
            <button type="submit">stop train</button>
        </form>
    </div>
</html>