<?php
session_start();
if (!$_SESSION['authorized']){
	header('location:index.php');
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
	<title>Bitcoin Checker</title>
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/ticker.js"></script>
	<script type="text/javascript" src="js/sendAlert.js"></script>
        <script>
        function getPrice(){
                var btcPrice = document.getElementById("coin-last").innerHTML;
                return btcPrice.substring(1);
        }
	function lowChecked(){
		$("#lowAlert").prop("checked",true);
	}
        function highChecked(){
                $("#highAlert").prop("checked",true);
        }
	function checkFirstModal(){
		var email = $("#emailAlert");
                var phone = $("#phoneAlert");
		var nextModal = $("#nextModal");
		if (email.is(":checked") || phone.is(":checked")) {
			nextModal.removeAttr("disabled");
		} else {
			nextModal.attr("disabled","disabled");
		}
	}
	function checkSecondModal(){
		if ($("#lowAlert").is(":checked")) {
			$("#threshold-low").attr("required","required");
		} else {
			$("#threshold-low").removeAttr("required");
		}

		if ($("#highAlert").is(":checked")) {
			$("#threshold-high").attr("required","required");
		} else {
                        $("#threshold-high").removeAttr("required");
                }

		if ($("#lowAlert").is(":checked") || $("#highAlert").is(":checked")){
			$("#alertSubmit").removeAttr("disabled");
		} else {
                        $("#alertSubmit").attr("disabled","disabled");
		}
	}
	function hidePhone(){
        	$("#phoneAlert").prop("hidden",true);
		$("#phoneLabel").prop("hidden",true);
        	$("#emailAlert").prop("checked",true);
        	$("#emailAlert").prop("disabled",true);
	}
	</script>
</head>
<body class="text-center" onload="btc_ticker();checkFirstModal();">
	<div class="col-md-12 text-right">
		<br>
		<strong>Welcome, <?php echo $_SESSION["username"]; ?></strong>
		<a class="btn btn-danger text-right" href="logout.php" role="button">Logout</a>
	</div>
	<br/><center id="bnc-ticker-8000000000001" data-coin="BTC"></center><br/>

	<!-- Button trigger modal -->
	<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#alertModal1">
	  Set new alert
	</button>
	<br><br>
	<center class="table-responsive">
	    <table class="table table-bordered" style="width:33%">
		<tr>
		    <th>Alert Price</th>
		    <th>Alert When Bitcoin Price Is</th>
		    <th>Alert Type</th>
		    <th>Cancel Alert</th>
		</tr>
		<?php
		// Login info for MySQL.
		$MySQLhost=""; // Host name
		$MySQLusername=""; // Mysql username
		$MySQLpassword=""; // Mysql password
		$db_name="bitcoin"; // Database name

		// Connect to server and select database.
		$mysqli = mysqli_connect("$MySQLhost","$MySQLusername","$MySQLpassword","$db_name") or die("cannot connect");

		// Protect against SQL injection
		$email = mysqli_real_escape_string($mysqli,stripslashes($_SESSION['username']));
		
		// Define the SQL statement to send to the database
		$sql = "SELECT AlertPrice,HighOrLow,AlertType FROM Alerts NATURAL JOIN `User Alerts` NATURAL JOIN Users WHERE Users.UserID = (SELECT UserID FROM Users WHERE Email = '$email') AND `User Alerts`.Valid;";
		
		// Send query to database
		$result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		
		// Print out all the active alerts that the user has
		while ($row = mysqli_fetch_array($result)){
                	// Extract values from query.
			echo "<tr>";
			echo "<td>".$row['AlertPrice']."</td>";
                	echo "<td>".$row['HighOrLow']."</td>";
                	echo "<td>".$row['AlertType']."</td>";
			echo "<td><form class='text-center' action='deleteAlert.php' method='POST'>
					<input name='alertPrice' value='".$row['AlertPrice']."' readonly hidden>
					<input name='username' value='".$email."' readonly hidden>
					<input type='submit' value='Delete' class='btn btn-danger'>
				    </form>
			     </td>";
			echo "</tr>";
		}
		?>
	    </table>
	</center>

        <form name="alert-form" id="alert-form" action="addAlert.php" method="POST">
          <!-- First Modal -->
          <div class="modal fade" id="alertModal1" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="sendLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <h4 class="modal-title" id="sendLabel">Set New Alert</h4>
                </div>
                <div class="modal-body">
                  <!-- Multiple Checkboxes -->
                  <div class="form-group">
                    <label class="col-md-7 control-label" for="emailAlert">Send alert to:</label>
                    <div class="col-md-7">
                      <div class="checkbox" id="email">
                        <label for="emailAlert" id="emailLabel">
                          <input type="checkbox" name="emailAlert" id="emailAlert" onchange="checkFirstModal()">
                          Email (<?php echo $_SESSION["username"];?>)
                        </label>
                      </div>
                      <div class="checkbox" id="phone">
                        <label for="phoneAlert" id="phoneLabel">
                          <input type="checkbox" name="phoneAlert" id="phoneAlert" onchange="checkFirstModal()">
                          Phone (<?php echo $_SESSION["phone"];?>)
                        </label>
                      </div>
                    </div>
                  </div>
                  <!-- Button -->
                  <div class="form-group">
                    <div class="col-md-7">
                      <button type="button" id="nextModal" name="next" class="btn btn-primary" data-toggle="modal" data-target="#alertModal2" disabled>Next</button>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                </div>
              </div>
            </div>
          </div>

	  <!-- Second Modal -->
	  <div class="modal fade" id="alertModal2" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="amountLabel">
	    <div class="modal-dialog" role="document">
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		    <span aria-hidden="true">&times;</span>
		  </button>
	          <h4 class="modal-title" id="amountLabel">Set New Alert</h4>
	        </div>
	        <div class="modal-body">
	          <!-- Multiple Checkboxes -->
	          <div class="form-group">
	            <label class="col-md-7 control-label" for="lowAlert">Send alert when price:</label>
	            <div class="col-md-7">
	              <div class="checkbox" id="low">
                        <label for="lowAlert">
			  <input type="checkbox" name="lowAlert" id="lowAlert" value="drop" onchange="checkSecondModal()">
			  drops to: $
			</label>
                        <input id="threshold-low" name="threshold-low" type="text" size="8" maxlength="8" pattern="^\d*\.?\d{0,2}$" title="Please input the following format for your price: 0.00" onkeypress="lowChecked();checkSecondModal();">
                        <label for="lowAlert">USD</label>
	              </div>
	              <div class="checkbox" id="high">
                        <label for="highAlert">
			  <input type="checkbox" name="highAlert" id="highAlert" value="rise" onchange="checkSecondModal()">
			  rises to: $
			</label>
                        <input id="threshold-high" name="threshold-high" type="text" size="8" maxlength="8" pattern="^\d*\.?\d{0,2}$" title="Please input the following format for your price: 0.00" onkeypress="highChecked();checkSecondModal();">
                        <label for="highAlert">USD</label>
	              </div>
	            </div>
	          </div>
                  <!-- Button -->
                  <div class="form-group">
                    <div class="col-md-7">
                      <button id="alertSubmit" name="submit" class="btn btn-primary" disabled>Submit</button>
                    </div>
                  </div>
	        </div>
	        <div class="modal-footer">
	        </div>
	      </div>
	    </div>
	  </div>
	</form>
        <?php	
	// The option for a phone alert is hidden if the user does not have a phone number on file
	if (!$_SESSION["phone"]){
                echo "<script>hidePhone();</script>";
        }
	?>
</body>
</html>
