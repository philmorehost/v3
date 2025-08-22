<?php session_start();
	include(__DIR__."/include/mailer.php");
	if(isset($_SESSION["user"])){
		header("Location: /dashboard.php");
	}
	include(__DIR__."/include/config.php");
	$user_table_name = "users";
	if($conn_server_db == true){
		$user_db_table = "CREATE TABLE IF NOT EXISTS ".$user_table_name."(firstname VARCHAR(30) NOT NULL, lastname VARCHAR(30) NOT NULL, email VARCHAR(225), password VARCHAR(50) NOT NULL, phone_number VARCHAR(20), referral VARCHAR(30), transaction_pin INT, home_address VARCHAR(225) NOT NULL, wallet_balance INT, account_type VARCHAR(30) NOT NULL, commission INT, apikey VARCHAR(65) NOT NULL, account_status VARCHAR(30) NOT NULL, login_attempt INT, reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
		if(mysqli_query($conn_server_db,$user_db_table) == true){
		}
	}
	
	$get_recaptcha_key = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM recaptcha_setting WHERE 1"));
	
	if(isset($_POST["veribtn"])){
		$apikey_unshuffle_string = "abcdefghijklmnopqrstuvwxyz1234567890abcdefghijklmnopqrstuvwxyz";
		$shuffle_apikey = str_shuffle($apikey_unshuffle_string);
		$chopped_apikey = substr($shuffle_apikey,0,50);
		$apikey = $chopped_apikey;

		$firstname = $_SESSION["firname_raw"];
		$lastname = $_SESSION["lasname_raw"];
		$email = $_SESSION["umail_raw"];
		$phone_number = $_SESSION["pnumber_raw"];
		$password = $_SESSION["pass_raw"];
		$confirm_password = $_SESSION["cpass_raw"];
		$home_address = $_SESSION["addr_raw"];
		$referral = $_SESSION["refer_raw"];
		$veri_code = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["veri_code"]));
			
				if(!empty(trim($firstname)) && !empty(trim($lastname)) && !empty(trim($email)) && !empty(trim($phone_number)) && !empty(trim($password)) && !empty(trim($home_address))){
					if(trim($password) == trim($confirm_password)){
						$check_user_info = mysqli_query($conn_server_db,"SELECT phone_number, email FROM ".$user_table_name." WHERE email='$email' OR phone_number='$phone_number'");
						if(mysqli_num_rows($check_user_info) == 0){
							if(isset($_SESSION["veri_code"]) && ($veri_code == $_SESSION["veri_code"])){
								$registration_data = "INSERT INTO ".$user_table_name." (firstname, lastname, email, password, phone_number, referral, transaction_pin, home_address, wallet_balance, account_type, commission, apikey, account_status) VALUES ('$firstname','$lastname','$email','$password','$phone_number','$referral','1234','$home_address','0','smart_earner','0','$apikey','active')";
								if(mysqli_query($conn_server_db,$registration_data) == true){
									$to = mysqli_fetch_assoc($conn_server_db, "SELECT * FROM admin WHERE ".$_SESSION['admin']."") ;
										$subject = Ucwords($email)." Registion Approval";
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
										<img style="width:30%; height:auto;" src="http://'.$_SERVER["HTTP_HOST"].'/images/logo.png">
										</div>
										<span id="content">Dear <b>'.mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT * FROM admin WHERE 1"))["fullname"].'</b><br>
										'.$email.' is waiting for your Approval on his/her newly registered account on '.$_SERVER["HTTP_HOST"].'<br><br>
											<b>Customer Details is listed below:</b><br>
												Fullname: '.$firstname.' '.$lastname.'<br>
												Phone Number: '.$phone_number.'<br>
												Home Address: '.$home_address.'<br>
												Api Key: '.$apikey.'<br>
												Referred By: '.$referral.'<br><br>

												<a href="http://'.$_SERVER["HTTP_HOST"].'/admin/site-setting.php?page=user&num=1&active='.$email.'">Approve Account</a> | <a href="http://'.$_SERVER["HTTP_HOST"].'/admin/site-setting.php?page=user&num=1&block='.$email.'">Reject Account</a>
										</span><br>
										<center>
										<a href="'.$_SERVER["HTTP_HOST"].'" id="web_link">Visit Website</a><br>
										<a href="mailto:" id="web_link">support@'.$_SERVER["HTTP_HOST"].'</a>
										</center>
										<div id="footer">
										<center>
										<img style="width:30%; height:auto;" src="http://'.$_SERVER["HTTP_HOST"].'/images/logo.png"><br>
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
										
									$reg_message = "Registration Successful! You can proceed to <a href='/login.php'>login</a>";
									unset($_SESSION["firname_raw"]);
									unset($_SESSION["lasname_raw"]);
									unset($_SESSION["umail_raw"]);
									unset($_SESSION["pnumber_raw"]);
									unset($_SESSION["pass_raw"]);
									unset($_SESSION["cpass_raw"]);
									unset($_SESSION["addr_raw"]);
									unset($_SESSION["refer_raw"]);
									unset($_SESSION["veri_code"]);
								}else{
									$reg_message = "Registration Failed! Try Again Later! ";
								}
							}else{
								$reg_message = "Invalid Email Code";
							}
						}else{
							$reg_message = "User Exists with EMAIL or PHONE NUMBER you Entered! ";
						}
						
					}else{
						$reg_message = "Passwords are not EQUAL";
					}
				}else{
					$reg_message = "Required Information must not be BLANK";
				}
            
        
	}
	if(isset($_POST["reg"])){
		if(!isset($_SESSION["veri_code"])){
			$firstname_raw = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["firstname"]));
			$lastname_raw = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["lastname"]));
			$email_raw = mysqli_real_escape_string($conn_server_db, strip_tags(strtolower($_POST["email"])));
			$phone_number_raw = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["phone_number"]));
			$password_raw = md5(mysqli_real_escape_string($conn_server_db, strip_tags($_POST["password"])));
			$confirm_password_raw = md5(mysqli_real_escape_string($conn_server_db, strip_tags($_POST["confirm_password"])));
			$home_address_raw = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["home_address"]));
			$referral_raw = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["referral"]));
			
			$_SESSION["firname_raw"] = $firstname_raw;
			$_SESSION["lasname_raw"] = $lastname_raw;
			$_SESSION["umail_raw"] = $email_raw;
			$_SESSION["pnumber_raw"] = $phone_number_raw;
			$_SESSION["pass_raw"] = $password_raw;
			$_SESSION["cpass_raw"] = $confirm_password_raw;
			$_SESSION["addr_raw"] = $home_address_raw;
			$_SESSION["refer_raw"] = $referral_raw;
		}

		if(isset($_POST['g-recaptcha-response'])){
			$captcha = $_POST['g-recaptcha-response'];
		
			if(!$captcha){
				$reg_message = "Please Check The Captcha Form! ";
			}
				
			$responseCaptcha = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$get_recaptcha_key["secretkey"]."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
			if($responseCaptcha['success'] == true){

				$veri_code_unshuffle_string = "1234567890";
				$shuffle_veri_code = str_shuffle($veri_code_unshuffle_string);
				$chopped_veri_code = substr($shuffle_veri_code,0,6);
				$session_veri_code = $chopped_veri_code;

				$_SESSION["veri_code"] = $session_veri_code;
				$subject = ucwords($_SERVER["HTTP_HOST"])." Registration Verification Code";
				$message = "Dear Customer, Your registration verification code is ".$_SESSION["veri_code"]."<br/> Do not disclose it to anyone, ".$_SERVER["HTTP_HOST"]." will not ask your for OTP, Password or sny confidential details<br/> Thank you";
				
				$to = $_SESSION["umail_raw"];
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
				height:3rem;
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
				<center>
					<div id="header">
						<img src="http://'.$_SERVER["HTTP_HOST"].'/images/logo.png">
					</div>
				</center>
				<span id="content">Dear <b>Customer</b>,<br>
				'.str_replace('\r\n',"<br>",$message).'
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
				$reg_message = " Email Successfully Sent To ".$_SESSION["umail_raw"];
			}
		}
	}

	if(isset($_POST["reset_reg"])){
		unset($_SESSION["firname_raw"]);
		unset($_SESSION["lasname_raw"]);
		unset($_SESSION["umail_raw"]);
		unset($_SESSION["pnumber_raw"]);
		unset($_SESSION["pass_raw"]);
		unset($_SESSION["cpass_raw"]);
		unset($_SESSION["addr_raw"]);
		unset($_SESSION["refer_raw"]);
		unset($_SESSION["veri_code"]);
		header("Location: ".$_SERVER["REQUEST_URI"]);
	}
?>
<!DOCTYPE html>
<html>
<head>
<title>Account Registration</title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; " />
<meta name="theme-color" content="skyblue" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="/css/site.css">
<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
<style>
	body{
		background: linear-gradient(to right, #00b0f0 10%, #818181 100%);
	}
</style>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src=""></script>
</head>
<body>

<center>
<div style="margin-left:-8px;" class="container-box bg-8 mobile-width-93 system-width-29 mobile-margin-top-37 system-margin-top-9 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-6 system-padding-left-3 mobile-padding-right-6 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<?php
			if($reg_message == true){
		?>
				<div class="message-box color-5 mobile-font-size-12 system-font-size-14"><?php echo $reg_message; ?></div>
		<?php
			}
		?>
		<a style="text-decoration: none;" href="/login.php"><img style="float: left;" class="" src="/images/back-arrow.svg" /></a><br>
		<img class="mobile-width-25 system-width-25 mobile-margin-bottom-2 system-margin-bottom-2" src="/images/logo.png" /><br>
		<?php
			if(!isset($_SESSION["veri_code"])){
		?>
		<span style="font-weight: bolder;" class="color-3 mobile-font-size-20 system-font-size-25">Create an account</span><br><br><br>
		<span class="font-size-1 font-family-1"></span><br>
		<?php }else{ ?>
		<span style="font-weight: bolder;" class="color-3 mobile-font-size-20 system-font-size-25">Account Verification</span><br><br><br>
		<span class="font-size-1 font-family-1"></span><br>
		<?php } ?>
		<form action="" method="post">
		<?php
			if(!isset($_SESSION["veri_code"])){
		?>
			<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Firstname</span><br>
			<input name="firstname" type="text" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="" required/>
			<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Lastname</span><br>
			<input name="lastname" type="text" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="" required/>
			<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Email</span><br>
			<input name="email" type="email" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="" required/>
			<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Phone Number</span><br>
			<input name="phone_number" type="text" pattern="[0-9]{11}" title="Phone Number must be Number and not more/less than 11 digits" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="" required/>
			<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Password</span><br>
			<input name="password" type="password" pattern="[a-zA-Z0-9]{8,}" title="Password must be Alphanumeric and not less than 8 character (No Special Character)" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="" required/>
			<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Confirm Password</span><br>
			<input name="confirm_password" pattern="[a-zA-Z0-9]{8,}" title="Password must be Alphanumeric and not less than 8 character (No Special Character)" type="password" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="" required/>
			<input name="home_address" type="text" placeholder="" value="Nil" hidden required/>
			<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Referral Email</span><br>
			<input name="referral" type="text" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="" value="<?php if($_GET['ref'] == true){ echo mysqli_real_escape_string($conn_server_db, strip_tags($_GET['ref'])); } ?>" readonly/>
			<div class="g-recaptcha" data-sitekey="<?php echo $get_recaptcha_key['sitekey']; ?>"></div>
			<input name="reg" type="submit"  style="font-weight: bolder;" class="button-box color-8 bg-5 mobile-font-size-13 system-font-size-16 mobile-width-99 system-width-99 mobile-margin-top-2 system-margin-top-2 mobile-margin-bottom-15 system-margin-bottom-20" value="Create your account"/><br>
			<span class="color-3 mobile-font-size-12 system-font-size-15">Have an account? <a style="text-decoration: none; font-size:inherit; font-weight:lighter;" class="color-5" href="/login.php"> Sign in</a></span>
		<?php }else{ ?>
			<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Verification Code</span><br>
			<input name="veri_code" type="text" pattern="[0-9]{6}" title="Verification Code must be Number and not more/less than 6 digits" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="e.g 123456" required/>
			<input name="veribtn" type="submit" class="button-box color-8 bg-5 mobile-font-size-13 system-font-size-16 mobile-width-99 system-width-99 mobile-margin-top-2 system-margin-top-2 mobile-margin-bottom-15 system-margin-bottom-20" value="Verify Account"/><br>
		<?php } ?>
		</form>
		<?php
			if(isset($_SESSION["veri_code"])){
		?>
			<form action="" method="post">
				<input name="reset_reg" type="submit" class="button-box color-8 bg-5 mobile-font-size-13 system-font-size-16 mobile-width-99 system-width-99 mobile-margin-top-2 system-margin-top-2 mobile-margin-bottom-15 system-margin-bottom-20" value="Reset Registration"/><br>
			</form>
		<?php } ?>
	</div>
</center>

</body>
</html>