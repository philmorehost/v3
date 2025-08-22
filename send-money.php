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
	}
	
	if(isset($_POST["fund"])){
		$user_email = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["email"]));
		$type = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["type"]));
		$amount = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["amount"]));
		$raw_number = "123456789012345678901234567890";
		$reference = substr(str_shuffle($raw_number),0,15);
		$site_name = $_SERVER["HTTP_HOST"];
		
		$check_user_details = mysqli_query($conn_server_db,"SELECT * FROM users WHERE email='$user_email'");
		$check_user = mysqli_fetch_assoc($check_user_details);
		$my_details = mysqli_query($conn_server_db,"SELECT * FROM users WHERE email='$user_session'");
		$collect_my_details = mysqli_fetch_assoc($my_details);
		
		if(mysqli_num_rows($check_user_details) == 1){
		if($collect_my_details["email"] !==$check_user["email"]){
			if($collect_my_details["wallet_balance"] >= $amount){
				$new_balance = ($check_user["wallet_balance"]+$amount);
				$your_new_balance = ($collect_my_details["wallet_balance"]-$amount);
				$y_email = $collect_my_details['email'];
					if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$your_new_balance' WHERE email='$y_email'")){
						if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$new_balance' WHERE email='$user_email'")){
							if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_email','$reference','$amount', '".$check_user["wallet_balance"]."', '$new_balance', 'successful', 'Wallet Credit Via Send Money','credit', '$site_name')")){
								if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('".$all_user_details["email"]."','$reference','$amount', '".$collect_my_details["wallet_balance"]."', '$your_new_balance', 'successful', 'Wallet Credit Shared to $user_email','debit', '$site_name')")){
									$_SESSION["transaction_text"] = $user_email." has been CREDITED with N".$amount.", Your New Balance: N".$your_new_balance;
								}
							}
						}else{
							$_SESSION["transaction_text"] = "Insufficient Fund! ";
						}
					}
				}
			}else{
				$_SESSION["transaction_text"] = "Error: Can't Send Money to yourself! ";
			}
			}else{
				$_SESSION["transaction_text"] = "User Doesn't Exist! ";
			}
		header("Location: ".$_SERVER["REQUEST_URI"]);
	}
	
	if(isset($_POST["transfer"])){
		$amount = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["amount"]));
		$account_number = $get_userBank_details_fetched['account_number'];
		$bank_code = $get_userBank_details_fetched['bank_code'];
		$narration = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["narration"]));
		
		$raw_number = "123456789012345678901234567890";
		$ref_id = date("YmdHis").substr(str_shuffle($raw_number),0,15);
		
		if($all_user_details["wallet_balance"] >= $amount){
		$monnifyCheckAccountTransferDetailsApiUrl = "https://api.monnify.com/api/v2/disbursements/single";
		$monnifyCheckAccountTransferDetailsAPILogin = curl_init($monnifyCheckAccountTransferDetailsApiUrl);
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin,CURLOPT_URL,$monnifyCheckAccountTransferDetailsApiUrl);
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin,CURLOPT_POST,true);
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin,CURLOPT_RETURNTRANSFER,true);
		$monnifyCheckAccountTransferDetailsLoginHeader = array("Authorization: Bearer ".$access_token,"Content-Type: application/json");
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin,CURLOPT_HTTPHEADER,$monnifyCheckAccountTransferDetailsLoginHeader);
		$monnifyCheckAccountTransferDetailsPostFields = json_encode(array("amount"=>$amount,"reference"=>$ref_id,"narration"=>$narration,"destinationBankCode"=>$bank_code,"destinationAccountNumber"=>$account_number,"currency"=>"NGN","sourceAccountNumber"=>""),true);
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin,CURLOPT_POSTFIELDS,$monnifyCheckAccountTransferDetailsPostFields);
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($monnifyCheckAccountTransferDetailsAPILogin, CURLOPT_SSL_VERIFYPEER, false);
		$GetmonnifyCheckAccountTransferDetailsJSON = curl_exec($monnifyCheckAccountTransferDetailsAPILogin);
		$monnifyCheckAccountTransferDetailsJSONObj = json_decode($GetmonnifyCheckAccountTransferDetailsJSON,true);
		
		if(($monnifyCheckAccountTransferDetailsJSONObj["responseMessage"] == "success") && (($monnifyCheckAccountTransferDetailsJSONObj["responseBody"]["status"] == "SUCCESS") OR ($monnifyCheckAccountTransferDetailsJSONObj["responseBody"]["status"] == "PENDING_AUTHORIZATION"))){
			$new_balance = ($all_user_details["wallet_balance"]-$amount);
			$site_name = $_SERVER["HTTP_HOST"];
			if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$new_balance' WHERE email='$user_session'")){
				if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', 'successful', 'Bank Transfer','bank-transfer', '$site_name')")){
					$_SESSION["transaction_text"] = "Transaction Initiated Successfully ".$GetmonnifyCheckAccountTransferDetailsJSON;
				}
			}
		}else{
			$_SESSION["transaction_text"] = "Transaction Failed! Try Again Later";
		}
		}else{
			$_SESSION["transaction_text"] = "Insufficient Wallet Fund! ";
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
<?php if(mysqli_real_escape_string($conn_server_db,strip_tags($_GET["page"])) == "user"){ ?>
	<div class="container-box bg-4 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
	<?php if($_SESSION["transaction_text"] == true){ ?>
	<div id="message" class="message-box color-5 mobile-font-size-12 system-font-size-14"><?php echo $_SESSION["transaction_text"]; ?></div>
	<?php } ?>
	<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">Share fund with other users</span><br>
<form method="post">
	<input onkeydown="javascript: return nenterkey_function(event)" name="email" type="email" style="display: inline-block;" class="input-box mobile-width-60 system-width-60 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="User Email"/>
	<input onkeydown="javascript: return nenterkey_function(event)" name="amount" type="text" pattern="[0-9]{3,}" title="Amount must be Number Only and Must be 3digits and above" style="display: inline-block;" class="input-box mobile-width-25 system-width-20 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Amount"/>
	<input onclick="openAuth(<?php echo $all_user_details['transaction_pin']; ?>);" type="button" id="proceed" style="display: inline-block;" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-30 system-width-15 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Proceed"/>
	<input style="display:none;" name="fund" type="submit" id="creditUser" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-30 system-width-15 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Credit User"/><br>
</form>
</div>
<?php } ?>

<?php if(mysqli_real_escape_string($conn_server_db,strip_tags($_GET["page"])) == "bankTransfer"){ ?>
<div class="container-box-3 full-length">
	<?php if($_SESSION["transaction_text"] == true){ ?>
	<div name="message" id="font-color-1" class="message-box font-size-2"><?php echo $_SESSION["transaction_text"]; ?></div>
	<?php } ?>
	
	<span class="font-size-2 font-family-1">BANK TRANSFER</b></span><br>
<form method="post">
	<span class="font-size-2 font-family-1">accountName: <b><?php echo $get_userBank_details_fetched['account_name']; ?></b><br> accountNumber: <b><?php echo $get_userBank_details_fetched['account_number']; ?></b><br> bankName: <b><?php echo $get_userBank_details_fetched['bank_name']; ?></b></span><br>
	<input onkeydown="javascript: return nenterkey_function(event)" name="amount" type="text" pattern="[0-9]{3,}" title="Amount must be Number Only and Must be 3 digits and above" class="input-box full-length" placeholder="Amount"/><br>
	<input onkeydown="javascript: return nenterkey_function(event)" name="narration" type="text" class="input-box full-length" placeholder="Narration"/><br>
	<input onclick="openAuth(<?php echo $all_user_details['transaction_pin']; ?>);" type="button" id="proceed" class="button-box full-length" value="Proceed"/>
	<input style="display:none;" name="transfer" type="submit" id="creditUser" class="button-box full-length" value="Credit User"/><br>
</form>
</div>
<?php } ?>

<form method="get">
	<button name="page" value="user" type="submit" class="button-box color-8 bg-5 mobile-font-size-13 system-font-size-16 mobile-width-90 system-width-95 mobile-margin-top-1 system-margin-top- mobile-margin-bottom-1 system-margin-bottom-1" >User to User</button>
	<!--<button name="page" value="bankTransfer" type="submit" class="button-box color-8 bg-5 mobile-font-size-13 system-font-size-16 mobile-width-85 system-width-85 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" >Wallet to Bank Account</button>-->
</form>

<?php
	include("./include/top-5-transaction.php");
?>
</center>

<script>
	function authResponse(code){
		if(code == 200){
			document.getElementById("proceed").style.display = "none";
			document.getElementById("creditUser").click();
			document.getElementById("creditUser").style.display = "inline-block";
		}else if(code == 201){
			alertPopUp("Incorrect Transaction Code");
		}
	}
</script>
<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>