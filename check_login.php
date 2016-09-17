<?php
session_start();

// Login info for MySQL.
$MySQLhost=""; // Host name
$MySQLusername=""; // Mysql username
$MySQLpassword=""; // Mysql password
$db_name="bitcoin"; // Database name
$table_name="Users"; // Table name

// Connect to server and select database.
$mysqli = mysqli_connect("$MySQLhost","$MySQLusername","$MySQLpassword","$db_name") or die("cannot connect");

// Email and password sent from form.
$myusername=$_POST["InputEmail"];
$mypassword=$_POST["InputPassword"];

// To protect against SQL injection.
$myusername = mysqli_real_escape_string($mysqli,stripslashes($myusername));
$mypassword = mysqli_real_escape_string($mysqli,stripslashes($mypassword));

// First query sent to database for retrieving salt.
$sql = "SELECT PassSalt FROM $table_name WHERE Email='$myusername'";
$result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
$row = mysqli_fetch_array($result);
$salt = $row["PassSalt"];
$mypassword = hash('sha512',$mypassword.$salt); // The password is salted and hashed in order
						// to match the value stored in the database.

// Second query sent to database for login.
$sql="SELECT * FROM $table_name WHERE Email='$myusername' and Password='$mypassword'";
$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

// Mysqli_num_row is counting table row.
$count=mysqli_num_rows($result);
$row = mysqli_fetch_array($result);

// If result matched $myusername and $mypassword, table row must be 1 row.
if($count==1){
	// Register $myusername for the session
	// retrieve the user's phone number (if applicable) from the database,
	// set the super global "authorized" to true,
	// and redirect to userPage.php.
	$_SESSION["username"] = $myusername;
	$_SESSION["phone"] = $row["PhoneNum"];
	$_SESSION["authorized"] = true;
	header("location:userPage.php");
} else {
	echo "Wrong Username or Password";
}
?>
