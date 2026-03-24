<?php
ob_start();
require_once "../../includes/template.php";
?>

<div class = "container">
    <h1 class = "text-center">Temperature Data Panel</h1>
    <p class = "text-center">Flag = CTF{sudo_random_*current temp*}</p>
    <div class = "text-center">
        <div class = "row">
            <div class = "col-md-6">
                <h2>Current temperature</h2>
                <p><span id = "current-temperature">NULL</span> degrees</p>
            </div>
            <div class = "col-md-6">
                <h2>Next refresh in:</h2>
                <p><span id="refresh-timer">60</span> seconds</p>
            </div>
        </div>
    </div>
</div>