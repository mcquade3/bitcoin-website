<?php
// Login info for MySQL.
$MySQLhost=""; // Host name
$MySQLusername=""; // Mysql username
$MySQLpassword=""; // Mysql password
$db_name="bitcoin"; // Database name

// Connect to server and select database.
$mysqli = mysqli_connect("$MySQLhost","$MySQLusername","$MySQLpassword","$db_name") or die("cannot connect");
?>

<html>
<head>
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script type="text/javascript" src="js/ticker.js"></script>
	<script type="text/javascript" src="js/sendText.js"></script>
        <script>
        function getPrice(){
                var btcPrice = document.getElementById("coin-last").innerHTML;
                $("#currentPriceBox").val(btcPrice.substring(1));
        }
	function checkPrice(){
		setTimeout(function(){
			getPrice();
			$('#sendToSelf').submit();
		},5000);
	}
	</script>
</head>
<body onload="btc_ticker();checkPrice();">
	<div id="bnc-ticker-8000000000001" data-coin="BTC"></div>
	<form id="sendToSelf" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" hidden>
		<input name="currentPrice" id="currentPriceBox" readonly>
	</form>

<?php
// Defines the method used to check the database for alerts to generate
function checkEmails($currentPrice){
        global $mysqli;

        // Protect against sql injection.
        $currentPrice = mysqli_real_escape_string($mysqli,stripslashes($currentPrice));

        // Prepare base SQL statement to return emails for alerts.
        $sql = "SELECT DISTINCT Alerts.AlertID FROM Alerts NATURAL JOIN `User Alerts` WHERE Alerts.AlertType = 'EMAIL' AND `User Alerts`.Valid AND ((AlertPrice >= $currentPrice AND HighOrLow = 'LOWER') OR (AlertPrice <= $currentPrice AND HighOrLow = 'HIGHER'));";

        // Send query to database.
        $result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

        // Iterate through the values in the returned query, then send emails to them, and mark the alerts as no longer valid.
        while ($row = mysqli_fetch_array($result)){
                // Extract values from query
                $alertID = $row["AlertID"];

                // Send email to user
                sendMail($alertID,$currentPrice);
        }
}

// Defines the method used to send emails.
function sendMail($alertID,$currentPrice){
        global $mysqli;

        // Protect against SQL injection.
        $alertID = mysqli_real_escape_string($mysqli,stripslashes($alertID));
        $currentPrice = mysqli_real_escape_string($mysqli,stripslashes($currentPrice));

        // Defines the SQL statement.
        $sql = "SELECT Email FROM Users NATURAL JOIN `User Alerts` WHERE AlertID=$alertID;";
        $result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
        $row = mysqli_fetch_array($result);

        // Contruct email parameters.
        $email = $row['Email'];
        $amount = $currentPrice;
        $timedate = date("h:ia m/d/Y");
        $subject = 'Bitcoin Price Alert';
        $message = 'Greetings, '.$email.'! As of '.$timedate.', the price of Bitcoin is now $'.$amount.'.';

        // This method sends the mail.
        mail($email, $subject, $message) or die('Error sending mail');

        // Add AlertID to "Sent Alerts" table.
        $sql = "INSERT INTO `Sent Alerts` VALUES ($alertID,NOW());";
        mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

        // Set alert in "User Alerts" to be no longer valid, so the same email isn't sent multiple times
        // if the price fluctuates.
        $sql = "UPDATE `User Alerts` SET Valid=0 WHERE AlertID=$alertID;";
        mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
}

// Defines the function to check the phone alerts
function checkPhones($currentPrice){
        global $mysqli;

        // Protect against sql injection.
        $currentPrice = mysqli_real_escape_string($mysqli,stripslashes($currentPrice));

        // Send query to database.
        $sql = "SELECT DISTINCT Alerts.AlertID,Users.PhoneNum FROM Alerts NATURAL JOIN `User Alerts` NATURAL JOIN Users WHERE Alerts.AlertType = 'PHONE' AND `User Alerts`.Valid AND ((AlertPrice >= $currentPrice AND HighOrLow = 'LOWER') OR (AlertPrice <= $currentPrice AND HighOrLow = 'HIGHER'));";
	$result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

        // Iterate through the values in the query.
        while ($row = mysqli_fetch_array($result)){
                // Extract values from query.
                $alertID = $row["AlertID"];
                $phone = $row["PhoneNum"];

                // Send text to user.
                sendText($alertID,$phone,$currentPrice);
        }
}

// Defines the function to send a text to a user
function sendText($alertID,$phone,$currentPrice){
        global $mysqli;

        // Protect against SQL injection.
        $alertID = mysqli_real_escape_string($mysqli,stripslashes($alertID));
        $phone = mysqli_real_escape_string($mysqli,stripslashes($phone));
        $currentPrice = mysqli_real_escape_string($mysqli,stripslashes($currentPrice));

        // Set message to be sent with text.
        $timedate = date("h:ia m/d/Y");
        $message = "As of $timedate, the current price of Bitcoin is $$currentPrice.";

	// Send text to user using AJAX request.
	echo "<script>sendSMS(".$phone.",'".$message."');</script>";

        // Add AlertID to "Sent Alerts" table.
        $sql = "INSERT INTO `Sent Alerts` VALUES ($alertID,NOW());";
        $result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

        // Set alert in "User Alerts" to be no longer valid, so the same email isn't sent multiple times
        // if the price fluctuates.
        $sql = "UPDATE `User Alerts` SET Valid='0' WHERE AlertID='$alertID';";
        $result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
}

// Checks the value the program sent to itself for the current price
if (isset($_REQUEST['currentPrice']) && $_SERVER["REQUEST_METHOD"] == "POST") {
	$currentPrice = mysqli_real_escape_string($mysqli,stripslashes($_REQUEST['currentPrice']));
	checkEmails($currentPrice);
	checkPhones($currentPrice);
}
?>
</body>
</html>
