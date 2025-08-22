<?php session_start();
	if(isset($_SESSION["user"])){
		header("Location: /dashboard.php");
	}
	include(__DIR__."/include/config.php");
	$user_table_name = "users";
	$get_admin_details = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM admin WHERE 1"));

	if(isset($_POST["log"])){
		$email = mysqli_real_escape_string($conn_server_db, strip_tags(strtolower($_POST["email"])));
		$password = md5(strip_tags($_POST["password"]));
		
		if(!empty(trim($email)) && filter_var($email,FILTER_VALIDATE_EMAIL) && !empty(trim($password))){
				$check_user_info = mysqli_query($conn_server_db,"SELECT email, password, account_status FROM ".$user_table_name." WHERE email='$email'");
				if(mysqli_num_rows($check_user_info) > 0){
					while($user_details = mysqli_fetch_assoc($check_user_info)){
						if(trim($password) == $user_details["password"]){
							if($user_details["account_status"] == "active"){
								$_SESSION["user"] = $user_details["email"];
								$_SESSION["password"] = $password;
								header("Location: /dashboard.php");
							}else{
								$log_message = "This Account is yet to be APPROVED or BLOCKED!, contact the Admin for help through WhatsApp: https://wa.me/".$get_admin_details["phone_number"];
							}
						}else{
							$log_message = "Password Doesn't Match! ";
						}
					}
				}else{
					$log_message = "User Doesn't Exists! ";
				}
		}else{
			$reg_message = "Required Information must not be BLANK";
		}
		
	}
?>
<!DOCTYPE html>
<html>
<head>
<title>Account Login</title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; " />
<meta name="theme-color" content="black" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="/css/site.css">
<style>
	body{
		background: linear-gradient(to right, #00b0f0 10%, #818181 100%);
	}
</style>
<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>
<body>

<center>
	<div style="margin-left:-8px;" class="container-box bg-8 mobile-width-93 system-width-25 mobile-margin-top-37 system-margin-top-9 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-6 system-padding-left-3 mobile-padding-right-6 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<?php
			if($log_message == true){
		?>
				<div class="message-box color-5 mobile-font-size-12 system-font-size-14"><?php echo $log_message; ?></div>
		<?php
			}
		?>
		<img class="mobile-width-25 system-width-25 mobile-margin-bottom-20 system-margin-bottom-20" src="/images/logo.png" /><br>
		<span style="font-weight: bolder;" class="color-5 mobile-font-size-20 system-font-size-25">Let's get started.</span><br><br>
		<span class="mobile-font-size-14 system-font-size-14 font-family-1"></span><br>
		<form action="" method="post">
			<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Email address/Phone number</span><br>
			<input name="email" type="email" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="abc@gmail.com" required/>
			<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Password</span><br>
			<input name="password" type="password" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="••••••••" required/><br>
			<span class="color-3 mobile-font-size-12 system-font-size-14 font-weight: bolder;"><a style="text-decoration: none; color: inherit; font-size:inherit; font-weight: bolder; float: right;" href="/forgot.php"> Forgot your Password?</a></span><br><br><br>
			<input name="log" type="submit" style="font-weight: bolder;" class="button-box color-8 bg-5 mobile-font-size-13 system-font-size-16 mobile-width-99 system-width-99 mobile-margin-top-2 system-margin-top-2 mobile-margin-bottom-15 system-margin-bottom-20" value="Login"/><br>
			<span class="color-3 mobile-font-size-12 system-font-size-15">Don't have an Account? <a style="text-decoration: none; font-size:inherit; font-weight:lighter;" class="color-5" href="/register.php">Sign Up</a></span><br>
		</form>
	</div>
</center>

<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>