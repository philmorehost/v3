<?php session_start();
	include("./include/mailer.php");
	if(!isset($_SESSION["user"])){
		header("Location: /login.php");
	}else{
		include(__DIR__."/include/config.php");
		include(__DIR__."/include/user-details.php");
	}
	
	//GET USER DETAILS
	$user_session = $_SESSION["user"];
	$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
	
	if(isset($_POST["change-pin"])){
	
		$old_pin = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["old-pin"]));
		$new_pin = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["new-pin"]));
		$confirm_pin = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["confirm-pin"]));
		
		if($old_pin == $all_user_details["transaction_pin"]){
			if($new_pin == $confirm_pin){
				if(mysqli_query($conn_server_db,"UPDATE users SET transaction_pin='$new_pin' WHERE email='$user_session'") == true){
					$log_pin_message = "Transaction PIN Changed Successfully";
				}else{
					$log_pin_message = "Server Error: Contact The Website Admin";
				}
			}else{
				$log_pin_message = "New PIN doesn't Match Confirm PIN! ";
			}
		}else{
			$log_pin_message = "Incorrect Old PIN";
		}
	}
	
	if(isset($_POST["recover-pin"])){
		$recovery_code = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["rec-code"]));
		$rec_new_pin = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["rec-new-pin"]));
		$raw_number = "1234567890";
		$random_num = substr(str_shuffle($raw_number),0,6);
		$to = $all_user_details["email"];
		
		if(isset($_SESSION["rec-code"])){
			if($_SESSION["rec-code"] == $recovery_code){
				if(mysqli_query($conn_server_db,"UPDATE users SET transaction_pin='$rec_new_pin' WHERE email='$user_session'") == true){
					$log_pin_message = "Transaction PIN Changed Successfully";
					unset($_SESSION["rec-code"]);
				}else{
					$log_pin_message = "Server Error: Contact The Website Admin";
				}
			}else{
				$log_pin_message = "Invalid Recovery Code";
			}
		}else{
			$subject = "Transaction Pin Recovery Code";
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
			<span id="content"> Dear <b>'.$all_user_details["firstname"].' '.$all_user_details["lastname"].'</b>,<br>
			Your Pin Reset Code is '.$random_num.'<br>
			If you feel this is not you, then quickly login to change your account password!
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
			$log_pin_message = "Recovery Code Sent Successfully";
		}
		
	}
	
	if(isset($_POST["recover-cancel"])){
		unset($_SESSION["rec-code"]);
		header("Location: ".$_SERVER["REQUEST_URI"]);
	}
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM site_info WHERE 1"))["sitetitle"]; ?></title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; " />
<meta name="theme-color" content="skyblue" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="/css/site.css">
<script src="/scripts/auth.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>
<body>
<?php include(__DIR__."/include/header-html.php"); ?>

<center>
<div class="container-box bg-4 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
<?php
	if($log_pin_message == true){
?>
		<div id="font-color-1" class="message-box font-size-2"><?php echo $log_pin_message; ?></div>
<?php
	}
?>
<form method="post">
	<span style="text-align: left; font-weight: bolder;" class="color-8 mobile-font-size-16 system-font-size-18">OLD PIN</span><br>
	<input name="old-pin" pattern="[0-9]{4}" title="Pin must be Number and not more/less than 4 digit" type="text" class="input-box mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Old Pin"/><br>
	<span style="text-align: left; font-weight: bolder;" class="color-8 mobile-font-size-16 system-font-size-18">NEW PIN</span><br>
	<input name="new-pin" pattern="[0-9]{4}" title="Pin must be Number and not more/less than 4 digit" type="text" class="input-box mobile-width-43 system-width-45 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="New Pin" required/>
	<input name="confirm-pin" pattern="[0-9]{4}" title="Pin must be Number and not more/less than 4 digit" type="text" class="input-box mobile-width-43 system-width-45 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Confirm New Pin" required/><br>
	<input name="change-pin" type="submit" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Update Transaction Pin"/><br>
</form>
<form method="post">
	<span style="text-align: left; font-weight: bolder;" class="color-8 mobile-font-size-16 system-font-size-18">Forgot OLD PIN? Recover Now! </span><br>
	<?php if(isset($_SESSION["rec-code"])){ ?>
		<input name="rec-code" pattern="[0-9]{6}" title="Code must be Number and not more/less than 6 digit" type="text" class="input-box mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Recovery Code"/><br>
		<input name="rec-new-pin" pattern="[0-9]{4}" title="Pin must be Number and not more/less than 4 digit" type="text" class="input-box mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="New Pin" required/>
	<?php } ?>
	<input name="recover-pin" type="submit" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Recover PIN"/><br>
</form>
<form method="post">
	<?php if(isset($_SESSION["rec-code"])){ ?>
		<input name="recover-cancel" type="submit" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Restart Recovery (Cancel)"/><br>
	<?php } ?>
</form>
</div>
</center>

<script>
	alertPopUp("NB: Default PIN is <b>1234</b>");
</script>

<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>