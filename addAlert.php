<?php
session_start();
if (!$_SESSION['authorized']){
	header('location:index.php');
}
if (!$_POST["phoneAlert"]){
	$_POST["emailAlert"] = true;
}

include_once("debug.php");
// Login info for MySQL.
$MySQLhost=""; // Host name
$MySQLusername=""; // Mysql username
$MySQLpassword=""; // Mysql password
$db_name="bitcoin"; // Database name

// Connect to server and select database.
$mysqli = mysqli_connect("$MySQLhost","$MySQLusername","$MySQLpassword","$db_name") or die("cannot connect");

// Connect UserID with AlertID in database "User Alerts" table.
function connectAlertToUser($alertPrice,$highOrLow,$alertType){
	global $mysqli;	

	// Sends a query to the database to retrieve the AlertID for the alert just posted.
	$tempSQL = "SELECT AlertID FROM Alerts WHERE AlertID=(SELECT MAX(AlertID) FROM Alerts);";
        $result = mysqli_query($mysqli,$tempSQL) or die(mysqli_error($mysqli));
	$row = mysqli_fetch_array($result);
        $alertID = $row["AlertID"];

	// Sends a query to the database to retrieve the UserID of the user.
	$email = mysqli_real_escape_string($mysqli,stripslashes($_SESSION['username'])); // Protect against SQL injection.
	$tempSQL = "SELECT UserID FROM Users WHERE Email='$email';";
	$result = mysqli_query($mysqli,$tempSQL) or die(mysqli_error($mysqli));
	$row = mysqli_fetch_array($result);
	$userID = $row["UserID"];

	// Sends a query to the database to set the UserID and AlertID together in the "User Alerts" table.
	$tempSQL = "INSERT INTO `User Alerts` VALUES ($alertID,$userID,NOW(),1);";
        mysqli_query($mysqli,$tempSQL) or die(mysqli_error($mysqli));
}

// Get inputs from form.
$highPrice = $_POST['threshold-high'];
$lowPrice = $_POST['threshold-low'];

// Sanitize inputs to prevent SQL injection.
$highPrice = mysqli_real_escape_string($mysqli,stripslashes($highPrice));
$lowPrice = mysqli_real_escape_string($mysqli,stripslashes($lowPrice));

// Sends a query to the database to store the user's alert(s).
if (isset($_POST['highAlert'])){
	$alertPrice = $highPrice;
	$highOrLow = 'HIGHER';
	if (isset($_POST['emailAlert'])){
		$sql = "INSERT INTO Alerts (AlertPrice,HighOrLow,AlertType) VALUES ($alertPrice,'$highOrLow','EMAIL');";
		$result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		connectAlertToUser($alertPrice,$highOrLow,$alertType);
	}
	if (isset($_POST['phoneAlert'])){
		$sql = "INSERT INTO Alerts (AlertPrice,HighOrLow,AlertType) VALUES ($alertPrice,'$highOrLow','PHONE');";
                $result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		connectAlertToUser($alertPrice,$highOrLow,$alertType);
	}
}
if (isset($_POST['lowAlert'])){
	$alertPrice = $lowPrice;
	$highOrLow = 'LOWER';
        if (isset($_POST['emailAlert'])){
		$sql = "INSERT INTO Alerts (AlertPrice,HighOrLow,AlertType) VALUES ($alertPrice,'$highOrLow','EMAIL');";
                $result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		connectAlertToUser($alertPrice,$highOrLow,$alertType);
	}
        if (isset($_POST['phoneAlert'])){
		$sql = "INSERT INTO Alerts (AlertPrice,HighOrLow,AlertType) VALUES ($alertPrice,'$highOrLow','PHONE');";
                $result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		connectAlertToUser($alertPrice,$highOrLow,$alertType);
        }
}
header('location:userPage');
?>
