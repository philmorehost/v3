<?php session_start();
	include("./include/mailer.php");
	if(isset($_SESSION["user"])){
		header("Location: /dashboard.php");
	}
	include(__DIR__."/include/config.php");
	
	if(isset($_POST["recover-pin"])){
		
		
		//GET USER DETAILS
		$user_session = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["rec-email"]));
		$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
		
		$recovery_code = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["rec-code"]));
		$rec_new_pin = md5(strip_tags($_POST["rec-new-pin"]));
		$raw_number = "1234567890";
		$random_num = substr(str_shuffle($raw_number),0,6);
		$to = $all_user_details["email"];
		
		if($all_user_details["email"] == true){
		if(isset($_SESSION["rec-code"])){
			if($_SESSION["rec-code"] == $recovery_code){
				if(mysqli_query($conn_server_db,"UPDATE users SET password='$rec_new_pin' WHERE email='$user_session'") == true){
					header("Location: /login.php");
					$log_pin_message = "Transaction Password Changed Successfully, You will be redirected to login page shortly! ";
					unset($_SESSION["rec-code"]);
					unset($_SESSION["rec-email"]);
				}else{
					$log_pin_message = "Server Error: Contact The Website Admin";
				}
			}else{
				$log_pin_message = "Invalid Recovery Code";
			}
		}else{
			$subject = "Transaction Password Recovery Code";
			$html_message = '<!DOCTYPE html>
			<html>
			<head>
			<title></title>
			<meta name="theme-color" content="skyblue" />
			<meta name="viewport" content="width=device-width, initial-scale=1"/>
			<style type="text/css">
			body{
			font-size:14px;
			font-family:tahooma;
			}
			
			#header{
			width:100%;
			height:4rem;
			margin:-8px 0 8px -8px;
			padding:0 16px 0 0;
			background: skyblue;
			top:0;
			position:sticky;
			}
			
			#header img{
			width:auto;
			height:3.2rem;
			margin:5px 3px;
			}
			
			#content{
			color:black;
			font-size:14px;
			font-family:tahooma;
			}
			
			#web_link{
			font-size:14px;
			font-family:tahooma;
			text-align:left;
			}
			
			#footer{
			width:100%;
			height:10rem;
			margin:8px 0 -8px -8px;
			padding:0 16px 0 0;
			background: skyblue;
			}
			
			#footer img{
			width:auto;
			height:3.2rem;
			margin:5px 3px;
			}
			</style>
			</head>
			<body>
			<div id="header">
			<img src="http://'.$_SERVER["HTTP_HOST"].'/images/logo.png">
			</div>
			<span id="content">Hi '.ucwords($all_user_details["firstname"]).',<br>
				Your Account Password Reset code is: <b>'.$random_num.'</b>
			</span><br>
			<center>
			<a href="'.$_SERVER["HTTP_HOST"].'" id="web_link">Visit Website</a><br>
			<a href="mailto:" id="web_link">support@'.$_SERVER["HTTP_HOST"].'</a>
			</center>
			<div id="footer">
			<center>
			<img src="http://'.$_SERVER["HTTP_HOST"].'/images/logo.png"><br>
			<b>Visit Our Website:</b> <a href="'.$_SERVER["HTTP_HOST"].'" id="web_link">'.$_SERVER["HTTP_HOST"].'</a><br>
			<b>Contact Us:</b> <a href="mailto:support@'.$_SERVER["HTTP_HOST"].'" id="web_link">support@'.$_SERVER["HTTP_HOST"].'</a>
			</center>
			</div>
			
			</body>
			</html>';
			// Always set content-type when sending HTML email
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			
			// More headers
			$headers .= 'From: <support@'.$_SERVER["HTTP_HOST"].'>' . "\r\n";
			$headers .= 'Cc: support@'.$_SERVER["HTTP_HOST"] . "\r\n";
			
			smtpEMAIL('support@'.$_SERVER["HTTP_HOST"],$to,$subject,$html_message,$headers);
			$_SESSION["rec-code"] = $random_num;
			$_SESSION["rec-email"] = $to;
			$log_pin_message = "Recovery Code Sent Successfully";
		}
		
		}else{
			$log_pin_message = "User Doesn't Exists";
		}
		
	}
	
	if(isset($_POST["recover-cancel"])){
		unset($_SESSION["rec-code"]);
		unset($_SESSION["rec-email"]);
		header("Location: ".$_SERVER["REQUEST_URI"]);
	}
?>
<!DOCTYPE html>
<html>
<head>
<title>Forgotten Password</title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; " />
<meta name="theme-color" content="skyblue" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="/css/site.css">
<style>
	body{
		background: linear-gradient(to right, #00b0f0 10%, #818181 100%);
	}
</style>
<script src="/scripts/auth.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>
<body>

<center>
<div style="margin-left:-8px;" class="container-box bg-8 mobile-width-93 system-width-25 mobile-margin-top-37 system-margin-top-9 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-6 system-padding-left-3 mobile-padding-right-6 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
<?php
	if($log_pin_message == true){
?>
		<div class="message-box color-5 mobile-font-size-12 system-font-size-14"><?php echo $log_pin_message; ?></div>
<?php
	}
?>
<a style="text-decoration: none;" href="/login.php"><img style="float: left;" class="" src="/images/back-arrow.svg" /></a><br>
<img class="mobile-width-25 system-width-25 mobile-margin-bottom-2 system-margin-bottom-2" src="/images/logo.png" /><br>		
<form method="post">
	<span style="font-weight: bolder;" class="color-5 mobile-font-size-20 system-font-size-25">Reset Password</span><br><br>
	<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Email address</span><br>
	<input name="rec-email" pattern" type="email" class="input-box full-length" placeholder="User Email" <?php if(isset($_SESSION["rec-email"])){ echo "readonly"; } ?> value="<?php echo $_SESSION['rec-email']; ?>"/><br>
	<?php if(isset($_SESSION["rec-code"])){ ?>
		<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Recovery Code</span><br>
		<input name="rec-code" pattern="[0-9]{6}" title="Code must be Number and not more/less than 6 digit" type="text" class="input-box full-length" placeholder="123456" required/><br>
		<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">New Password</span><br>
		<input name="rec-new-pin" type="text" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="••••••••" required/>
	<?php } ?>
	<input name="recover-pin" type="submit" class="button-box color-8 bg-5 mobile-font-size-13 system-font-size-16 mobile-width-99 system-width-99 mobile-margin-top-2 system-margin-top-2 mobile-margin-bottom-15 system-margin-bottom-20" value="Recover Password"/><br>
	<span class="color-3 mobile-font-size-12 system-font-size-15">Suddenly Remember Password? <a style="text-decoration: none; font-size:inherit; font-weight:lighter;" class="color-5" href="/login.php">Sign In</a></span><br>
</form>
<form method="post">
	<?php if(isset($_SESSION["rec-code"])){ ?>
		<input name="recover-cancel" type="submit" class="button-box color-8 bg-5 mobile-font-size-13 system-font-size-16 mobile-width-99 system-width-99 mobile-margin-top-2 system-margin-top-2 mobile-margin-bottom-15 system-margin-bottom-20" value="Restart"/><br>
	<?php } ?>
</form>
</div>
</center>

</body>
</html>