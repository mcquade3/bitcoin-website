<?php
session_start();
if (!$_SESSION['authorized']){
        header('location:index.php');
}

function generateSalt($max = 100) {
	$characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $i = 0;
        $salt = "";
        while ($i <= $max) {
	    $salt .= $characterList{mt_rand(0,(strlen($characterList)-1))};
	    $i++;
        }
        return $salt;
}

// Login info for MySQL.
$MySQLhost=""; // Host name
$MySQLusername=""; // Mysql username
$MySQLpassword=""; // Mysql password
$db_name="bitcoin"; // Database name
$table_name="Users"; // Table name

// Connect to server and select database.
$mysqli = mysqli_connect("$MySQLhost","$MySQLusername","$MySQLpassword","$db_name") or die("cannot connect");

// Email and password sent from form.
$myusername=$_POST["RegisterEmail"];
$mypassword=$_POST["RegisterPassword"];
$myphone1=$_POST["RegisterPhone1"];
$myphone2=$_POST["RegisterPhone2"];
$myphone3=$_POST["RegisterPhone3"];
$myusername=trim($myusername);
$mypassword=trim($mypassword);

// To protect against SQL injection.
$myusername = mysqli_real_escape_string($mysqli,stripslashes($myusername));
$mypassword = mysqli_real_escape_string($mysqli,stripslashes($mypassword));
$myphone1 = mysqli_real_escape_string($mysqli,stripslashes($myphone1));
$myphone2 = mysqli_real_escape_string($mysqli,stripslashes($myphone2));
$myphone3 = mysqli_real_escape_string($mysqli,stripslashes($myphone3));

// Check the email and password for a length greater than 0.
if (strlen($myusername)==0 || strlen($mypassword)==0) {
	header('location:index.php');
}

// Combine all the phone parts together.
$myphone = $myphone1.$myphone2.$myphone3;

// Check the phone number for a valid 10-digit length.
// If the phone number is 10 digits long or is undefined,
// the program will continue to run.
// Otherwise, the user will be redirected back to the home page.
if (strlen($myphone)==0 || strlen($myphone)==10){//continue
} else {
        header('location:index.php');
}

// Protect the true password from peering eyes by storing an encrypted version of it.
$salt = generateSalt(); // Creates a salt for the password.
$mypassword = hash('sha512',$mypassword.$salt); // The password is salted and hashed so that
                                          	// the plain text is not visible to anyone.

// Query sent to database.
if (strlen($myphone) > 0){
	$sql="INSERT INTO $table_name (Email,Password,PassSalt,PhoneNum) VALUES ('$myusername','$mypassword','$salt','$myphone');";
} else {
	$sql="INSERT INTO $table_name (Email,Password,PassSalt) VALUES ('$myusername','$mypassword','$salt');";
}
$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
$_SESSION["username"] = $myusername;
$_SESSION["phone"] = $myphone;
$_SESSION["authorized"] = true;
header("location:userPage.php");
?>
