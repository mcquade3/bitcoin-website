<?php
session_start();
if (!$_SESSION['authorized']){
	header('location:index.php');
}

// Login info for MySQL.
$MySQLhost=""; // Host name
$MySQLusername=""; // Mysql username
$MySQLpassword=""; // Mysql password
$db_name="bitcoin"; // Database name

// Connect to server and select database.
$mysqli = mysqli_connect("$MySQLhost","$MySQLusername","$MySQLpassword","$db_name") or die("cannot connect");

// Get alert price from 
$alertPrice = mysqli_real_escape_string($mysqli,stripslashes($_POST['alertPrice']));
$username = mysqli_real_escape_string($mysqli,stripslashes($_POST['username']));

// Defines the SQL query to be sent to the database
$sql = "UPDATE `User Alerts` NATURAL JOIN Alerts SET `User Alerts`.Valid=0 WHERE Alerts.AlertPrice=$alertPrice AND `User Alerts`.UserID=(SELECT UserID FROM Users WHERE Email='$username');";

// Send query to database
mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

// Redirects back to the user page when query is completed
header('location:userPage.php');
?>
