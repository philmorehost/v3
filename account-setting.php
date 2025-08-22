<?php session_start();
	if(!isset($_SESSION["user"])){
		header("Location: /login.php");
	}else{
		include(__DIR__."/include/config.php");
		include(__DIR__."/include/user-details.php");
	}
	
	//GET USER DETAILS
	$user_session = $_SESSION["user"];
	$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
	
	if($conn_server_db == true){
		if(mysqli_query($conn_server_db,"CREATE TABLE IF NOT EXISTS user_bank (email VARCHAR(225) NOT NULL, account_name VARCHAR(225), account_number VARCHAR(225), bank_name VARCHAR(225), bank_code VARCHAR(30))") == true){
			$get_userBank_details = mysqli_query($conn_server_db,"SELECT * FROM user_bank WHERE email='$user_session'");
			if(mysqli_num_rows($get_userBank_details) == 0){
				if(mysqli_query($conn_server_db,"INSERT INTO user_bank (email, account_name, account_number, bank_name, bank_code) VALUES ('$user_session','','','','')") == true){
		
				}
			}
		}else{
			$get_userBank_details = mysqli_query($conn_server_db,"SELECT * FROM user_bank WHERE email='$user_session'");
			if(mysqli_num_rows($get_userBank_details) == 0){
				if(mysqli_query($conn_server_db,"INSERT INTO user_bank (email, account_name, account_number, bank_name, bank_code) VALUES ('$user_session','','','','')") == true){
					
				}
			}
		}
		$get_userBank_details = mysqli_query($conn_server_db,"SELECT * FROM user_bank WHERE email='$user_session'");
		$get_userBank_details_fetched = mysqli_fetch_assoc($get_userBank_details);
	}
	
	$monnify_keys = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT * FROM payment_api WHERE website='monnify'"));
	
	$monnifyApiUrl = "https://api.monnify.com/api/v1/auth/login";
	$monnifyAPILogin = curl_init($monnifyApiUrl);
	curl_setopt($monnifyAPILogin,CURLOPT_URL,$monnifyApiUrl);
	curl_setopt($monnifyAPILogin,CURLOPT_POST,true);
	curl_setopt($monnifyAPILogin,CURLOPT_RETURNTRANSFER,true);
	$monnifyLoginHeader = array("Authorization: Basic ".base64_encode($monnify_keys["public_key"].':'.$monnify_keys["secret_key"]),"Content-Type: application/json","Content-Length: 0");
	curl_setopt($monnifyAPILogin,CURLOPT_HTTPHEADER,$monnifyLoginHeader);
	
	curl_setopt($monnifyAPILogin, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($monnifyAPILogin, CURLOPT_SSL_VERIFYPEER, false);
	
	$GetMonnifyJSON = curl_exec($monnifyAPILogin);
	$monnifyJSONObj = json_decode($GetMonnifyJSON,true);
	if($GetMonnifyJSON == true){
		$access_token = $monnifyJSONObj["responseBody"]["accessToken"];
		
		$monnifyCheckAccountTransferDetailsApiUrl = "https://api.monnify.com/api/v1/banks";
		$monnifyCheckAccountTransferDetailsAPILogin = curl_init($monnifyCheckAccountTransferDetailsApiUrl);
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin,CURLOPT_URL,$monnifyCheckAccountTransferDetailsApiUrl);
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin,CURLOPT_HTTPGET,true);
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin,CURLOPT_RETURNTRANSFER,true);
		$monnifyCheckAccountTransferDetailsLoginHeader = array("Authorization: Bearer ".$access_token,"Content-Type: application/json");
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin,CURLOPT_HTTPHEADER,$monnifyCheckAccountTransferDetailsLoginHeader);
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin, CURLOPT_SSL_VERIFYPEER, false);
		$GetmonnifyCheckAccountTransferDetailsJSON = curl_exec($monnifyCheckAccountTransferDetailsAPILogin);
		$monnifyCheckAccountTransferDetailsJSONObj = json_decode($GetmonnifyCheckAccountTransferDetailsJSON,true);
	}
	
	
	if(isset($_POST["update-setting"])){
		$firstname = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["firstname"]));
		$lastname = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["lastname"]));
		$address = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["address"]));
		$account_name = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["account-name"]));
		$account_number = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["account-no"]));
		$bank_details = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["bank-details"]));
		$bank_code = array_filter(explode(":",trim($bank_details)))[0];
		$bank_name = array_filter(explode(":",trim($bank_details)))[1];
		
		if(!empty($firstname) && !empty($lastname) && !empty($address) && !empty($account_name) && !empty($account_number) && !empty($bank_code) && !empty($bank_name)){
			if(mysqli_query($conn_server_db, "UPDATE user_bank SET account_name='$account_name', account_number='$account_number', bank_name='$bank_name', bank_code='$bank_code' WHERE email='$user_session'")){
				if(mysqli_query($conn_server_db, "UPDATE users SET firstname='$firstname', lastname='$lastname', home_address='$address' WHERE email='$user_session'")){
					$_SESSION["settings_text"] = "Account Info Updated Successfully";
				}
			}
			
		}else{
			$_SESSION["settings_text"] = "Form Input must be filled! ";
		}
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
<?php if($_SESSION["settings_text"] == true){ ?>
	<div name="message" id="font-color-1" class="message-box font-size-2"><?php echo $_SESSION["settings_text"]; ?></div>
<?php } ?>

<span style="text-align: left; font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">ACCOUNT SETTING</span><br><br>
<form method="post">
	<input name="firstname" type="text" class="input-box mobile-width-40 system-width-40 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Firstname" value="<?php echo $all_user_details['firstname']; ?>" required/>
	<input name="lastname" type="text" class="input-box mobile-width-40 system-width-40 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Lastname" value="<?php echo $all_user_details['lastname']; ?>" required/><br>
	<input name="address" type="text" class="input-box mobile-width-40 system-width-40 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Home Address" value="<?php echo $all_user_details['home_address']; ?>" required/>
	<!--<input name="account-name" type="text" class="input-box full-length" placeholder="Bank Account Name" value="<?php echo $get_userBank_details_fetched['account_name']; ?>" required/><br>
	<input name="account-no" type="text" pattern="[0-9]{10,}" title="Only Numbers are Allowed" class="input-box full-length" placeholder="Bank Account Number" value="<?php echo $get_userBank_details_fetched['account_number']; ?>" required/><br>
	<select name="bank-details" class="select-box full-length">
		<option disabled hidden selected>Choose Your Bank</option>
		<?php
			foreach($monnifyCheckAccountTransferDetailsJSONObj["responseBody"] as $bank_details){
				if($bank_details["code"] == $get_userBank_details_fetched['bank_code']){
					$selected = "selected";
				}else{
					$selected = "";
				}
				echo '<option '.$selected.' value="'.$bank_details["code"].':'.$bank_details["name"].'">'.$bank_details["name"].'</option>';
			}
		?>
	</select>-->
	<input onclick="openAuth(<?php echo $all_user_details['transaction_pin']; ?>);" type="button" id="proceed" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-42 system-width-42 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Proceed"/>
	<input style="display:none;" name="update-setting" onclick="(this.style='pointer-events:none;background:lightgray;')()" type="submit" id="update-account" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-42 system-width-42 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Updating..."/><br>
</form>
</div>
</center>

<script>
	function authResponse(code){
		if(code == 200){
			document.getElementById("proceed").style.display = "none";
			document.getElementById("update-account").click();
		}else if(code == 201){
			alertPopUp("Incorrect Transaction Code");
		}
	}
</script>
<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>