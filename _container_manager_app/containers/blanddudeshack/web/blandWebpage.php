<!DOCTYPE HTML>  
<html>
<head>
    <!-- shows the title of the page in the browser tab -->
    <title>Bland Dudes Hack</title>
</head>
<body>  

<?php
# define the empty variables that will hold user input
$name = "";
$password = "";
# ask if the submit button has been pressed (and the form method is equal to POST) 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    # calls the function with user parameters from the form
  test_input($_POST["username"], $_POST["password"]);
}

function test_input($username, $password) {
    # checks if the username and password EXACTLY match the credentials
    if ($username == "cybercity" && $password == "cyb3rc1ty") {
        # if both fields are correct, get the CTF flag displayed in the broswer in large green text
        echo "<span style='color:green; font-size:30px;'>Access Granted:CTF{hackdocker}</span>";
        return true;
    } else {
        # if at least one field is incorrect, then get a large red error message displayed in the broswer
        echo "<span style='color:red; font-size:30px;'>Invalid credentials. Please try again.</span>";
        return false;
    }
}
?>
<div style="text-align: center;">
    <h2>Administrator Panel: Bland Dudes. Authorisation Required:</h2>
    <!-- creates the form element -->
    <form method="post">
        <!-- creates the text fields for username and password -->
        Username: <input type="text" name="username" value="<?php echo $name;?>"><br><br>
        Password: <input type="password" name="password" value="<?php echo $password;?>"><br><br>
        <!-- creates the physical submit button -->
        <input type="submit" name="submit" value="Submit">
    </form>
</div>
