<?php
ob_start();
require_once "../../includes/template.php";

$temperature = 1; // default
$message = "";
$auth_attempted = false;

if (!isset($_SESSION['verified'])) {
    $_SESSION['verified'] = false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $auth_attempted = true;
    if (!empty($_POST['ukg_password'])) {
        if ($_POST['ukg_password'] === 'UKGADMIN26') {
            $_SESSION['verified'] = true;
        } else {
            $_SESSION['verified'] = false;
            $message = "Incorrect password. Please try again or contact an administrator for help";
        }  
    }
    // $temperature = random_int(10, 30); // placeholder

    $command = "mosquitto_sub -h CTF-MQTT-Broker -t 'challenges/TrafficLights' -C 1 -W 5"; 
    $currentData = shell_exec($command);
    $temperature = $currentData ? trim($currentData) : 1; // Default to 1 if no data
    if (!empty($_POST['user_input'])) {
        $_SESSION['user_input'] = (int)$_POST['user_input'];
    } else {
        unset($_SESSION['user_input']);
    }
}

$user_input = isset($_SESSION['user_input']) ? (int)$_SESSION['user_input'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<?php if ($_SESSION['verified']): ?>
<body>
    <div class = "container" style = "margin-top: 20vh;">
        <h1 class = "text-center">Single use unique key generator</h1>
        <p class = "text-center">A sudo-random passkey generator for administrative purposes. Keys will automatically terminate prior to use.</p>
        <p class = "text-center" style = "font-style: italic;">Formula: Key = Time * (Temperature^((Input + 3) % 5) + Input^2) % 10^7</p>
        <form method = "post">
            <div class = "text-center">
                <div class = "row" style = "margin-top: 40px;">
                    <div class = "col-md-4">
                        <h2>Current temperature</h2>
                        <p><?php echo $temperature; ?> degrees</p>
                    </div>
                    <div class = "col-md-4">
                        <h2>Integer of choice</h2>
                        <p><input type="number" name="user_input" class="form-control" placeholder="Input integer between 1 and 100" style = "text-align: center;" min="1" max="100" step="1" value="<?php echo $user_input; ?>" required></p>
                    </div>
                    <div class = "col-md-4">
                        <h2>Time of generation</h2>
                        <p><?php echo date("dHis"); ?></p>
                    </div>
                </div>
                <h2>Generate new key</h2>
                <p><button type="submit">Generate</button></p>
            </div>
        </form>
    </div>
</body>
<?php else: ?>
<body>
    <div class = "container" style = "margin-top: 25vh;">
        <h1 class = "text-center">Welcome to the UKG Panel</h1>
        <form method = "post" style = "display: flex; flex-direction: column; align-items: center; margin-top: 20px;">
            <input type="text" name="ukg_password" class="form-control" placeholder="Enter password" style = "text-align: center; max-width: 300px; padding: 10px;" required>
            <button type="submit">Submit</button>
        </form>
        <p class = "text-center" style = "margin-top: 15px; font-style: italic;"><?php echo ($auth_attempted && $message) ? $message : "Please enter the UKG password to proceed."; ?></p>
    </div>
</body>
<?php endif; ?>

</html>