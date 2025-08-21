<?php
include "../../includes/template.php";
/** @var $conn */

$challengeToLoad = null;
$challengeID = null;
$title = null;
$challengeText = null;
$pointsValue = null;
$flag = null;
$projectID = null;

if (!authorisedAccess(false, true, true)) {
    header("Location:../../index.php");
    exit; // Ensure script stops after redirect
}

if (isset($_GET["challengeID"])) {
    $challengeToLoad = $_GET["challengeID"];
} else {
    header("location:challengesList.php");
    exit; // Ensure script stops after redirect
}

function loadChallengeData()
{
    global $conn, $challengeToLoad, $challengeID, $title, $challengeText, $pointsValue, $flag, $projectID;

    $sql = $conn->query("SELECT ID, challengeTitle, challengeText, pointsValue, flag FROM Challenges WHERE ID=" . $challengeToLoad);

    while ($result = $sql->fetch()) {
        $challengeID = $result["ID"];
        $title = $result["challengeTitle"];
        $challengeText = $result["challengeText"];
        $pointsValue = $result["pointsValue"];
        $flag = $result["flag"];
    }

    // SQL query to fetch projectID
    $projectIDSQL = "SELECT project_id FROM ProjectChallenges WHERE challenge_id = :challengeID";
    $projectIDStmt = $conn->prepare($projectIDSQL);
    $projectIDStmt->bindParam(':challengeID', $challengeID, PDO::PARAM_INT);
    $projectIDStmt->execute();
    $projectIDResult = $projectIDStmt->fetch(PDO::FETCH_ASSOC);
    $projectID = $projectIDResult["project_id"];
}

function checkFlag()
{
    global $conn, $challengeToLoad, $challengeID, $title, $challengeText, $pointsValue, $flag, $projectID;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userEnteredFlag = sanitise_data($_POST['hiddenflag']);
        if ($userEnteredFlag == $flag) {
            $user = $_SESSION["user_id"];
            $query = $conn->query("SELECT * FROM `UserChallenges` WHERE `challengeID` ='$challengeID' and `userID` = '$user'");
            $row = $query->fetch();
            if ($query->rowCount() > 0) {
                $_SESSION["flash_message"] = "<div class='bg-warning'>Flag Success! Challenge already completed, no points awarded</div>";
                header("Location:./challengesList.php?projectID=$projectID");
                exit; // Ensure script stops after redirect
            } else {
                $insert = "INSERT INTO `UserChallenges` (userID, challengeID) VALUES ('$user', '$challengeID')";
                $insert = $conn->prepare($insert);
                $insert->execute();

                $userInformation = $conn->query("SELECT Score FROM Users WHERE ID='$user'");
                $userData = $userInformation->fetch();
                $addedScore = $userData["Score"] + $pointsValue;
                $sql1 = "UPDATE Users SET Score=? WHERE ID=?";
                $stmt = $conn->prepare($sql1);
                $stmt->execute([$addedScore, $user]);

                if ($challengeID == 19) {
                    $sql = "UPDATE Challenges SET moduleValue = 'On' WHERE ID='$challengeID'";
                } else {
                    $sql = "UPDATE Challenges SET moduleValue = moduleValue + 1 WHERE ID='$challengeID'";
                }

                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $_SESSION["flash_message"] = "<div class='bg-success'>Success!</div>";
                header("Location:./challengesList.php?projectID=$projectID");
                exit; // Ensure script stops after redirect
            }
        } else {
            $_SESSION["flash_message"] = "<div class='bg-danger'>Flag failed - try again</div>";
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit; // Ensure script stops after redirect
        }
    }
}

// Call functions before any output
loadChallengeData();
checkFlag();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Challenge Information</title>
</head>
<body>
    <header class="container-fluid d-flex align-items-center justify-content-center">
        <h1 class="text-uppercase">Challenge - <?= htmlspecialchars($title) ?></h1>
    </header>

    <section class="pt-4 pd-2" style="padding: 10px;">
        <div class="container-fluid text-center">
            <div class="row border border-dark-subtle border-2">
                <div class="col-2 border-start border-end border-dark-subtle border-2">Challenge Image</div>
                <div class="col-2 border-start border-end border-dark-subtle border-2">Challenge Name</div>
                <div class="col-7 border-start border-end border-dark-subtle border-2">Challenge Description</div>
                <div class="col-1 border-start border-end border-dark-subtle border-2">Challenge Points</div>
            </div>

            <div class="row border border-top-0 border-dark-subtle border-2">
                <div class="col-2 border-start border-end border-dark-subtle border-2">
                    <?= htmlspecialchars($title) ?>
                </div>
                <div class="col-7 border-start border-end border-dark-subtle border-2">
                    <?= htmlspecialchars($challengeText) ?>
                </div>
                <div class="col-1 d-flex align-items-center justify-content-center">
                    <?= htmlspecialchars($pointsValue) ?>
                </div>
            </div>

            <div class="row border border-top-0 border-dark-subtle border-2">
                <p class="text-success fw-bold pt-3">Good luck and have fun!</p>
            </div>

            <hr style="
                border: none; 
                position: relative; 
                margin: 1.5rem 0; 
                height: 4px;
                color: red;
                background-color: red;
            ">

            <form action="challengeDisplay.php?challengeID=<?= htmlspecialchars($challengeID) ?>" method="post" enctype="multipart/form-data">
                <div class="form-floating">
                    <input type="text" class="flag-input" id="flag" name="hiddenflag" placeholder="CTF{Flag_Here}">
                    <p id="functionAssistant" class="form-text text-start font-size-sm">
                        You'll have to hit the "Enter" key when finish entering the hidden flag.
                    </p>
                </div>
            </form>
        </div>
    </section>

    <footer style="padding: 10px;">
        <h2 class='ps-3'>Recent Data</h2>
        <div class="container-fluid">
            <div class="row border text-center">
                <div class="col border-end">Date & Time</div>
                <div class="col">Data</div>
            </div>

            <?php
            $sql = $conn->query("SELECT * FROM ModuleData WHERE ModuleID=" . $challengeID);
            while ($moduleIndividualData = $sql->fetch()) {
                echo "<div class='row border border-top-0'>";
                $moduleData = $moduleIndividualData["Data"];
                $moduleDateTime = $moduleIndividualData["DateTime"];
                echo "<div class='col border-end text-center'>" . htmlspecialchars($moduleDateTime) . "</div>";
                echo "<div class='col text-center'>" . htmlspecialchars($moduleData) . "</div>";
                echo "</div>";
            }
            ?>
        </div>
    </footer>
</body>
</html>