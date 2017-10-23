<?php
session_start();
if ($_SESSION['authorized']){
        header('location:userPage.php');
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
	<title>Bitcoin Checker</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/custom.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/ticker.js"></script>
        <script>
        function getPrice(){
		var btcPrice = document.getElementById("coin-last").innerHTML;
	}

	function checkPass(){
		//Store the password field objects into variables
		var pass1 = document.getElementById('RegisterPassword');
		var pass2 = document.getElementById('RegisterPassword2');
		var registerSubmit = document.getElementById('registerSubmit');
		
		//Set the colors we will be using
		var goodColor = "#66cc66";
		var badColor = "#ff6666";
		
		//Compare the values in the password field 
		//and the confirmation field
		if(pass1.value == pass2.value){
	        	//The passwords match. 
	        	//Set the color to the good color to inform
	        	//the user that they have entered the correct password.
	        	pass2.style.backgroundColor = goodColor;
			registerSubmit.disabled = false;
		}else{
	        	//The passwords do not match.
	        	//Set the color to the bad color.
	        	pass2.style.backgroundColor = badColor;
                        registerSubmit.disabled = true;
		}
	}

	function checkPhone(){
		// Store the values of the phone number input fields
		var num1 = document.getElementById('RegisterPhone1');
                var num2 = document.getElementById('RegisterPhone2');
                var num3 = document.getElementById('RegisterPhone3');

		// If all the fields are empty, then the fields are optional.
		if(num1.value.length == 0 && num2.value.length == 0 && num3.value.length == 0){
			num1.required = false;
			num2.required = false;
			num3.required = false;
		} else { // If at least one of the fields has input, then all three are required for submission.
			num1.required = true;
			num2.required = true;
			num3.required = true;
		}
	}
	</script>
</head>
<body onload=btc_ticker()>
	<!-- Bitcoin Graph -->
	<br><center id="bnc-ticker-8000000000001" data-coin="BTC"></center>

	<!-- Login Button -->
	<div class="col-md-6 col-sm-6 text-center">
		<br>
                <p>Returning user?</p>
                <p>Click here:</p>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#loginModal">
                  Login
                </button>
        </div>

        <!-- Login Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="loginModalLabel">Login</h4>
              </div>
              <div class="modal-body">
                <form method="post" action="check_login.php">
                  <div class="form-group">
                    <label for="InputEmail">Email address</label>
                    <input type="email" class="form-control" name="InputEmail" placeholder="Email">
                  </div>
                  <div class="form-group">
                    <label for="InputPassword">Password</label>
                    <input type="password" class="form-control" name="InputPassword" placeholder="Password">
                  </div>
                  <button id="loginSubmit" type="submit" class="btn btn-primary">Submit</button>
                </form>
              </div>
            </div>
          </div>
	</div>

	<!-- Registration Button -->
	<div class="col-md-6 col-sm-6 text-center">
		<br>
		<p>First time here?</p>
		<p>Click here:</p>
        	<!-- Button trigger modal -->
        	<button type="button" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#registerModal">
        	  Register
        	</button>
	</div>

	<!-- Registration Modal -->
	<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="registerModalLabel">Register</h4>
	      </div>
	      <div class="modal-body has-success has-feedback">
	        <form method="post" action="register.php">
        	  <div class="form-group">
        	    <label for="RegisterEmail">Email address</label>
        	    <input type="email" class="form-control" name="RegisterEmail" placeholder="Email" required>
        	  </div>
        	  <div class="form-group">
        	    <label for="RegisterPassword">Password</label>
        	    <input type="password" class="form-control" name="RegisterPassword" id="RegisterPassword" placeholder="Password" required>
        	  </div>
		  <div class="form-group">
		    <label for="RegisterPassword2">Re-enter Password</label>
		    <input type="password" class="form-control" name="RegisterPassword2" id="RegisterPassword2" placeholder="Re-enter password" onkeyup="checkPass(); return false;" required>
		  </div>
                  <div class="form-inline">
                    <label for="RegisterPhone1">Cell Phone Number (Optional)</label>
		    <br>
		    <label for="RegisterPhone1">(</label>
		    <input type="text" class="phone-num-3d" name="RegisterPhone1" id="RegisterPhone1" placeholder="###" title="Three-digit area code" pattern="\d{3}" maxlength="3" onkeyup="checkPhone(); return false;">
                    <label for="RegisterPhone2"> ) </label>
		    <input type="text" class="phone-num-3d" name="RegisterPhone2" id="RegisterPhone2" placeholder="###" title="Three-digit prefix" pattern="\d{3}" maxlength="3" onkeyup="checkPhone(); return false;">
                    <label for="RegisterPhone3"> - </label>
		    <input type="text" class="phone-num-4d" name="RegisterPhone3" id="RegisterPhone3" placeholder="####" title="Four-digit line number"  pattern="\d{4}" maxlength="4" onkeyup="checkPhone(); return false;">
		  </div>
        	  <button id="registerSubmit" type="submit" class="btn btn-primary" style="margin-top: 14px;" disabled>Submit</button>
        	</form>
	      </div>
	    </div>
	  </div>
	</div>
</body>
</html>
