<?php session_start();
include("../include/mailer.php");
if (!isset($_SESSION["admin"])) {
	header("Location: /admin/login.php");
} else {
	include("../include/admin-config.php");
	include("../include/admin-details.php");
}

$payment_name = array("monnify", "flutterwave", "paystack");

if ($conn_server_db == true) {
	$sms_table_name = "sms_api";
	$sms_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $sms_table_name . "(website VARCHAR(225) NOT NULL, apikey VARCHAR(225))";
	if (mysqli_query($conn_server_db, $sms_apikey_db_table) == true) {
	}

	if (mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS sms_sender_id (email VARCHAR(225) NOT NULL, sender_id VARCHAR(60) NOT NULL, status VARCHAR(60) NOT NULL)") == true) {
	}
	if (mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS upgrade_price (level VARCHAR(225) NOT NULL, amount INT NOT NULL)") == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM upgrade_price")) == 0) {
			$insert_upgrade_price = "INSERT INTO upgrade_price (level, amount) VALUES ('vip_earner','500');";
			$insert_upgrade_price .= "INSERT INTO upgrade_price (level, amount) VALUES ('vip_vendor','1000');";
			$insert_upgrade_price .= "INSERT INTO upgrade_price (level, amount) VALUES ('api_earner','1500')";
			if (mysqli_multi_query($conn_server_db, $insert_upgrade_price) == true) {

			} else {
				echo mysqli_error($conn_server_db);
			}
		}
	}

	if (mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS user_message (user_alert LONGTEXT NOT NULL, user_static LONGTEXT NOT NULL)") == true) {
		$get_userMessage_details = mysqli_query($conn_server_db, "SELECT * FROM user_message");
		if (mysqli_num_rows($get_userMessage_details) == 0) {
			if (mysqli_query($conn_server_db, "INSERT INTO user_message (user_alert, user_static) VALUES ('Welcome Back! ','Welcome Back! ')") == true) {

			}
		}
	}

	$payment_table_name = "payment_api";
	$payment_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $payment_table_name . "(website VARCHAR(225) NOT NULL, api_status BOOLEAN, public_key VARCHAR(225), secret_key VARCHAR(225), encrypt_key VARCHAR(225))";
	if (mysqli_query($conn_server_db, $payment_apikey_db_table) == true) {
	}

	foreach ($payment_name as $payment_name) {
		$check_if_payment_exist = mysqli_num_rows(mysqli_query($conn_server_db, "SELECT website FROM " . $payment_table_name . " WHERE website='" . $payment_name . "'"));
		$insert_payment_name = "INSERT INTO " . $payment_table_name . " (website) VALUES ('" . $payment_name . "')";
		if ($check_if_payment_exist == 0) {
			if (mysqli_query($conn_server_db, $insert_payment_name) == true) {

			}
		}
	}
}

$showFunction = trim(mysqli_real_escape_string($conn_server_db, strip_tags($_GET["page"])));
$showNum = trim(mysqli_real_escape_string($conn_server_db, strip_tags($_GET["num"])));
$showActive = trim(mysqli_real_escape_string($conn_server_db, strip_tags($_GET["active"])));
$showBlock = trim(mysqli_real_escape_string($conn_server_db, strip_tags($_GET["block"])));
$deleteUser = trim(mysqli_real_escape_string($conn_server_db, strip_tags($_GET["deleteuser"])));
$showSenderID = trim(mysqli_real_escape_string($conn_server_db, strip_tags($_GET["sender_id"])));
$loginasUser = trim(mysqli_real_escape_string($conn_server_db, strip_tags($_GET["loginuser"])));

if ($showNum == true) {
	if ($showNum > 0) {
		$pageNum = $showNum;
	} else {
		$pageNum = 1;
	}
} else {
	$pageNum = 1;
}

if (isset($_POST["update-dashboard"])) {
	$alert_message = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["alert-message"]));
	$static_message = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["static-message"]));

	if (mysqli_query($conn_server_db, "UPDATE user_message SET user_alert='$alert_message', user_static='$static_message'") == true) {

	}
	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if ($showFunction == "user") {
	if (empty(trim(strip_tags($_GET["search"])))) {
		$users_20_details = mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users LIMIT 20 OFFSET " . round(0 + (($pageNum - 1) * 20)));
	} else {
		$users_20_details = mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE ((email='" . trim(strip_tags($_GET["search"])) . "') OR (phone_number='" . trim(strip_tags($_GET["search"])) . "')) LIMIT 20 OFFSET " . round(0 + (($pageNum - 1) * 20)));
	}

}

$users_details = mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users");

$sms_20_details = mysqli_query($conn_server_db, "SELECT * FROM sms_sender_id LIMIT 20 OFFSET " . round(0 + (($pageNum - 1) * 20)));
$sms_details = mysqli_query($conn_server_db, "SELECT * FROM sms_sender_id");


if ($showFunction == "user" && !empty($showNum) && !empty($showActive)) {
	if (mysqli_query($conn_server_db, "UPDATE users SET account_status='active' WHERE email='$showActive'")) {
		$error_message = $showActive . " activated successfully";
	}
}

if ($showFunction == "user" && !empty($showNum) && !empty($showBlock)) {
	if (mysqli_query($conn_server_db, "UPDATE users SET account_status='blocked' WHERE email='$showBlock'")) {
		$error_message = $showBlock . " blocked successfully";
	}
}

if ($showFunction == "user" && !empty($showNum) && !empty($deleteUser)) {
	if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$deleteUser'")) >= 1) {
		if (mysqli_query($conn_server_db, "DELETE FROM users WHERE email='$deleteUser'") == true) {
			$transaction_history_user_del = mysqli_query($conn_server_db, "SELECT email FROM transaction_history WHERE email='$deleteUser'");
			$payment_order_user_del = mysqli_query($conn_server_db, "SELECT email FROM payment_order_history WHERE email='$deleteUser'");
			$recharge_card_history_user_del = mysqli_query($conn_server_db, "SELECT email FROM recharge_card_history WHERE email='$deleteUser'");
			$data_card_history_user_del = mysqli_query($conn_server_db, "SELECT email FROM data_card_history WHERE email='$deleteUser'");
			$data_card_autho_user_del = mysqli_query($conn_server_db, "SELECT email FROM authorized_datacard_user WHERE email='$deleteUser'");
			$recharge_card_autho_user_del = mysqli_query($conn_server_db, "SELECT email FROM authorized_rechargecard_user WHERE email='$deleteUser'");

			if (mysqli_num_rows($transaction_history_user_del) >= 1) {
				mysqli_query($conn_server_db, "DELETE FROM transaction_history WHERE email='$deleteUser'");
			}
			if (mysqli_num_rows($payment_order_user_del) >= 1) {
				mysqli_query($conn_server_db, "DELETE FROM payment_order_history WHERE email='$deleteUser'");
			}
			if (mysqli_num_rows($recharge_card_history_user_del) >= 1) {
				mysqli_query($conn_server_db, "DELETE FROM recharge_card_history WHERE email='$deleteUser'");
			}
			if (mysqli_num_rows($data_card_history_user_del) >= 1) {
				mysqli_query($conn_server_db, "DELETE FROM data_card_history WHERE email='$deleteUser'");
			}
			if (mysqli_num_rows($recharge_card_autho_user_del) >= 1) {
				mysqli_query($conn_server_db, "DELETE FROM authorized_rechargecard_user WHERE email='$deleteUser'");
			}
			if (mysqli_num_rows($data_card_autho_user_del) >= 1) {
				mysqli_query($conn_server_db, "DELETE FROM authorized_datacard_user WHERE email='$deleteUser'");
			}
			$error_message = $deleteUser . " Account deleted successfully";
		}
	} else {
		$error_message = $deleteUser . " Doesnt Exists or has been deleted!";
	}
}

if ($showFunction == "user" && !empty($showNum) && !empty($loginasUser)) {
	$_SESSION["user"] = $loginasUser;
	$error_message = $loginasUser . " login successfully";
	echo '<script> window.onload = function(){ window.open("http://' . $_SERVER["HTTP_HOST"] . '/dashboard.php","_blank"); }</script>';
}

$payment_order_approve_ref = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["approve"]));
if ($showFunction == "paymentorder" && !empty($payment_order_approve_ref)) {
	if (mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT status FROM payment_order_history WHERE id='$payment_order_approve_ref'"))["status"] == "pending") {
		$user_email = strtolower(mysqli_real_escape_string($conn_server_db, strip_tags(mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT email FROM payment_order_history WHERE id='$payment_order_approve_ref'"))["email"])));
		$amount = mysqli_real_escape_string($conn_server_db, strip_tags(mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT amount FROM payment_order_history WHERE id='$payment_order_approve_ref'"))["amount"]));
		$raw_number = "123456789012345678901234567890";
		$reference = substr(str_shuffle($raw_number), 0, 15);
		$site_name = $_SERVER["HTTP_HOST"];
		$type = "credit";

		$check_user_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$user_email'");
		$check_user = mysqli_fetch_assoc($check_user_details);
		if (mysqli_num_rows($check_user_details) == 1) {
			$new_balance = ($check_user["wallet_balance"] + $amount);
			if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$new_balance' WHERE email='$user_email'")) {
				if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_email','$reference','$amount', '" . $check_user["wallet_balance"] . "', '$new_balance', 'successful', 'Wallet Credit | Payment Order','$type', '$site_name')")) {
					$user_tran_message = $user_email . " has been CREDITED with N" . $amount . ", New Balance: N" . $new_balance;
				}
			}
		}

	}

	if (mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT status FROM payment_order_history WHERE id='$payment_order_approve_ref'"))["status"] == "approved") {
		$user_email = mysqli_real_escape_string($conn_server_db, strip_tags(mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT email FROM payment_order_history WHERE id='$payment_order_approve_ref'"))["email"]));
		$amount = mysqli_real_escape_string($conn_server_db, strip_tags(mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT amount FROM payment_order_history WHERE id='$payment_order_approve_ref'"))["amount"]));
		$site_name = $_SERVER["HTTP_HOST"];

		$check_user_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$user_email'");
		$check_user = mysqli_fetch_assoc($check_user_details);
		if (mysqli_num_rows($check_user_details) == 1) {
			$new_balance = ($check_user["wallet_balance"]);
			$user_tran_message = $user_email . " has been CREDITED before , New Balance: N" . $new_balance;
		}
	}
	if (mysqli_query($conn_server_db, "UPDATE payment_order_history SET status='approved' WHERE id='$payment_order_approve_ref'")) {
		$error_message = "Payment Order of Reference No: <b>" . $payment_order_approve_ref . "</b> Approved Successfully, " . $user_tran_message;
	}
}

$payment_order_cancel_ref = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["cancel"]));
if ($showFunction == "paymentorder" && !empty($payment_order_cancel_ref)) {
	if (mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT status FROM payment_order_history WHERE id='$payment_order_cancel_ref'"))["status"] == "pending") {
		$user_email = mysqli_real_escape_string($conn_server_db, strip_tags(mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT email FROM payment_order_history WHERE id='$payment_order_cancel_ref'"))["email"]));
		$amount = mysqli_real_escape_string($conn_server_db, strip_tags(mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT amount FROM payment_order_history WHERE id='$payment_order_cancel_ref'"))["amount"]));
		$check_user_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$user_email'");
		$check_user = mysqli_fetch_assoc($check_user_details);

		if (mysqli_num_rows($check_user_details) == 1) {
			if (mysqli_query($conn_server_db, "UPDATE payment_order_history SET status='cancelled' WHERE id='$payment_order_cancel_ref'")) {
				$error_message = $user_email . " - Payment Order of N" . $amount . " with Reference No: <b>" . $payment_order_cancel_ref . "</b> Cancelled Successfully, " . $user_tran_message;
			}
		}
	}
}

if ($showFunction == "sms" && !empty($showNum) && !empty($showActive) && !empty($showSenderID)) {
	if (mysqli_query($conn_server_db, "UPDATE sms_sender_id SET status='approved' WHERE email='$showActive' AND sender_id='$showSenderID'")) {
		$error_message = $showActive . " with senderID: <b>" . $showSenderID . "</b> activated successfully";
	}
}

if ($showFunction == "sms" && !empty($showNum) && !empty($showBlock) && !empty($showSenderID)) {
	if (mysqli_query($conn_server_db, "UPDATE sms_sender_id SET status='rejected' WHERE email='$showBlock' AND sender_id='$showSenderID'")) {
		$error_message = $showBlock . " with senderID: <b>" . $showSenderID . "</b> blocked successfully";
	}
}


if (isset($_POST["send-mail"])) {
	$subject = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["subject"]));
	$message = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["message"]));
	$all_user_email = mysqli_query($conn_server_db, "SELECT * FROM users");
	if (mysqli_num_rows($all_user_email) > 0) {
		while ($userDetails = mysqli_fetch_assoc($all_user_email)) {
			$to = $userDetails["email"];
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
						<img src="http://' . $_SERVER["HTTP_HOST"] . '/images/logo.png">
					</div>
				</center>
				<span id="content">Dear <b>' . $userDetails["firstname"] . '</b>,<br>
				' . str_replace('\r\n', "<br>", $message) . '
				</span><br>
				<center>
				<a href="' . $_SERVER["HTTP_HOST"] . '" id="web_link">Visit Website</a><br>
				<a href="mailto:" id="web_link">support@' . $_SERVER["HTTP_HOST"] . '</a>
				</center>
				<div id="footer">
				<center>
				<img src="http://' . $_SERVER["HTTP_HOST"] . '/images/logo.png"><br>
				<b>Visit Our Website:</b> <a href="' . $_SERVER["HTTP_HOST"] . '" id="web_link">' . $_SERVER["HTTP_HOST"] . '</a><br>
				<b>Contact Us:</b> <a href="mailto:support@' . $_SERVER["HTTP_HOST"] . '" id="web_link">support@' . $_SERVER["HTTP_HOST"] . '</a>
				</center>
				</div>
				
				</body>
				</html>';
			// Always set content-type when sending HTML email
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

			// More headers
			$headers .= 'From: <support@' . $_SERVER["HTTP_HOST"] . '>' . "\r\n";
			$headers .= 'Cc: support@' . $_SERVER["HTTP_HOST"] . "\r\n";

			smtpEMAIL('support@' . $_SERVER["HTTP_HOST"], $to, $subject, $html_message, $headers);
			$_SESSION["email-sent"] = " Email Successfully Sent To All Users";
		}
	} else {
		$_SESSION["email-sent"] = "Users are Empty, Mail cannot be sent to Empty User! ";
	}
	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["fund"])) {
	$user_email = strtolower(mysqli_real_escape_string($conn_server_db, strip_tags($_POST["email"])));
	$type = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["type"]));
	$amount = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["amount"]));
	$wallet_description = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["desc"]));
	$raw_number = "123456789012345678901234567890";
	$reference = substr(str_shuffle($raw_number), 0, 15);
	$site_name = $_SERVER["HTTP_HOST"];
	$transaction_pin = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["transaction_pin"]));
	$admin_sess = mysqli_real_escape_string($conn_server_db, strip_tags($_SESSION["admin"]));
	$get_admin_details = mysqli_query($conn_server_db, "SELECT transaction_pin FROM admin WHERE email='$admin_sess'");
	$check_admin = mysqli_fetch_assoc($get_admin_details);

	$check_user_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$user_email'");
	$check_user = mysqli_fetch_assoc($check_user_details);

	if (mysqli_num_rows($get_admin_details) == 1) {
		if ($check_admin["transaction_pin"] == $transaction_pin) {
			if (mysqli_num_rows($check_user_details) == 1) {

				if ($type == "credit") {
					$new_balance = ($check_user["wallet_balance"] + $amount);
					if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$new_balance' WHERE email='$user_email'")) {
						if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_email','$reference','$amount', '" . $check_user["wallet_balance"] . "', '$new_balance', 'successful', 'Wallet Credit | $wallet_description','$type', '$site_name')")) {
							$_SESSION["user_fund"] = $user_email . " has been CREDITED with N" . $amount . ", New Balance: N" . $new_balance;
						}
					}
				}

				if ($type == "debit") {
					$new_balance = ($check_user["wallet_balance"] - $amount);
					if ($check_user["wallet_balance"] >= $amount) {
						if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$new_balance' WHERE email='$user_email'")) {
							if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_email','$reference','$amount', '" . $check_user["wallet_balance"] . "', '$new_balance', 'successful', 'Wallet Debit | $wallet_description','$type', '$site_name')")) {
								$_SESSION["user_fund"] = $user_email . " has been DEBITED N" . $amount . ", New Balance: N" . $new_balance;
							}
						}
					} else {
						$_SESSION["user_fund"] = $user_email . " cannot be DEBITED, Account Balance is Too Low, Current Balance: N" . $check_user["wallet_balance"];
					}
				}

				if ($type == "refunded") {
					$new_balance = ($check_user["wallet_balance"] + $amount);
					if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$new_balance' WHERE email='$user_email'")) {
						if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_email','$reference','$amount', '" . $check_user["wallet_balance"] . "', '$new_balance', 'successful', 'Money Refunded | $wallet_description','$type', '$site_name')")) {
							$_SESSION["user_fund"] = $user_email . " has been REFUNDED with N" . $amount . ", New Balance: N" . $new_balance;
						}
					}
				}

				if ($type == "deduction") {
					$new_balance = ($check_user["wallet_balance"] - $amount);
					if ($check_user["wallet_balance"] >= $amount) {
						if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$new_balance' WHERE email='$user_email'")) {
							if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_email','$reference','$amount', '" . $check_user["wallet_balance"] . "', '$new_balance', 'successful', 'Money Deduction | $wallet_description','$type', '$site_name')")) {
								$_SESSION["user_fund"] = $user_email . " has been DEDUCTED N" . $amount . ", New Balance: N" . $new_balance;
							}
						}
					} else {
						$_SESSION["user_fund"] = $user_email . " cannot be DEBITED, Account Balance is Too Low, Current Balance: N" . $check_user["wallet_balance"];
					}
				}
			} else {
				$_SESSION["user_fund"] = "User Doesn't Exist! ";
			}
		} else {
			$_SESSION["user_fund"] = "Transaction Pin is Incorrect";
		}
	} else {
		$_SESSION["user_fund"] = "Admin Details Doesn't exist";
	}
	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-payment-key"])) {
	$monnify_public_key = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["monnify-apikey"]));
	$monnify_secret_key = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["monnify-secretkey"]));
	$monnify_contract_key = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["monnify-contractkey"]));
	$monnify_api_status_value = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["monnify-status"]));

	if ($monnify_api_status_value == 1) {
		$monnify_api_status = $monnify_api_status_value;
	} else {
		$monnify_api_status = 0;
	}

	$flutterwave_public_key = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["flutterwave-publickey"]));
	$flutterwave_secret_key = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["flutterwave-secretkey"]));
	$flutterwave_contract_key = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["flutterwave-encryptkey"]));
	$flutterwave_api_status_value = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["flutterwave-status"]));

	if ($flutterwave_api_status_value == 1) {
		$flutterwave_api_status = $flutterwave_api_status_value;
	} else {
		$flutterwave_api_status = 0;
	}

	$paystack_public_key = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["paystack-publickey"]));
	$paystack_secret_key = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["paystack-secretkey"]));
	$paystack_contract_key = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["paystack-encryptkey"]));
	$paystack_api_status_value = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["paystack-status"]));

	if ($paystack_api_status_value == 1) {
		$paystack_api_status = $paystack_api_status_value;
	} else {
		$paystack_api_status = 0;
	}

	$monnify_update_payment_name = "UPDATE " . $payment_table_name . " SET api_status='$monnify_api_status', public_key='$monnify_public_key',secret_key='$monnify_secret_key',encrypt_key='$monnify_contract_key' WHERE website='monnify'";
	if (mysqli_query($conn_server_db, $monnify_update_payment_name) == true) {
	}

	$flutterwave_update_payment_name = "UPDATE " . $payment_table_name . " SET api_status='$flutterwave_api_status', public_key='$flutterwave_public_key',secret_key='$flutterwave_secret_key',encrypt_key='$flutterwave_contract_key' WHERE website='flutterwave'";
	if (mysqli_query($conn_server_db, $flutterwave_update_payment_name) == true) {
	}

	$paystack_update_payment_name = "UPDATE " . $payment_table_name . " SET api_status='$paystack_api_status', public_key='$paystack_public_key',secret_key='$paystack_secret_key',encrypt_key='$paystack_contract_key' WHERE website='paystack'";
	if (mysqli_query($conn_server_db, $paystack_update_payment_name) == true) {
	}

	header("Location: " . $_SERVER["REQUEST_URI"]);
}


if (isset($_POST["update-upgrade-price"])) {
	$vip_earner = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["price-1"]));
	$vip_vendor = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["price-2"]));
	$api_earner = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["price-3"]));

	mysqli_query($conn_server_db, "UPDATE upgrade_price SET amount='$vip_earner' WHERE level='vip_earner'");
	mysqli_query($conn_server_db, "UPDATE upgrade_price SET amount='$vip_vendor' WHERE level='vip_vendor'");
	mysqli_query($conn_server_db, "UPDATE upgrade_price SET amount='$api_earner' WHERE level='api_earner'");


	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-recaptcha"])) {
	$logo_tmp_name = $_FILES["logo"]["tmp_name"];
	$sitetitle = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["sitetitle"]));
	$sitekey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["sitekey"]));
	$secretkey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["secretkey"]));
	if (mysqli_query($conn_server_db, "UPDATE site_info SET sitetitle='$sitetitle' WHERE 1") == true) {

	}
	if (mysqli_query($conn_server_db, "UPDATE recaptcha_setting SET sitekey='$sitekey',secretkey='$secretkey' WHERE 1") == true) {

	}
	if (!empty($_FILES["logo"]["name"])) {
		if (file_exists("./../images/logo.png")) {
			unlink("./../images/logo.png");
		}
		move_uploaded_file($logo_tmp_name, "./../images/logo.png");
	}
	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["upgrade-account"])) {
	$user_email = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["email"]));
	$all_details = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["upgrade-package"]));
	$upgrade_to = array_filter(explode(":", trim($all_details)))[0];
	$amount = array_filter(explode(":", trim($all_details)))[1];
	$site_name = $_SERVER["HTTP_HOST"];

	if ($upgrade_to == "smart_earner") {
		$user_account_level = "Smart Earner";
	}

	if ($upgrade_to == "vip_earner") {
		$user_account_level = "VIP Earner";
	}

	if ($upgrade_to == "vip_vendor") {
		$user_account_level = "VIP Vendor";
	}

	if ($upgrade_to == "api_earner") {
		$user_account_level = "Agent Vendor";
	}

	if (!empty($user_email) && !empty($upgrade_to) && !empty($amount)) {
		$check_user_exists = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$user_email'");
		if (mysqli_num_rows($check_user_exists) == 1) {
			if (mysqli_fetch_assoc($check_user_exists)["account_type"] !== $upgrade_to) {
				$raw_number = "123456789012345678901234567890";
				$reference = substr(str_shuffle($raw_number), 0, 15);
				if (mysqli_query($conn_server_db, "UPDATE users SET account_type='$upgrade_to' WHERE email='$user_email'") == true) {
					if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, status, description, transaction_type, website) VALUES ('$user_email','$reference','$amount', 'successful', 'Account Upgrading to " . ucwords(str_replace("_", " ", $upgrade_to)) . "','account-upgrade', '$site_name')")) {
						$get_referee_account = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='" . $all_user_details["referral"] . "'");
						if (mysqli_num_rows($get_referee_account) == 1) {
							$ref_amount = ($amount * 20 / 100);
							$ref_account_details = mysqli_fetch_assoc($get_referee_account);
							$ref_remain_balance = ($ref_account_details["wallet_balance"] + $ref_amount);
							$ref_reference = substr(str_shuffle($raw_number), 0, 15);
							if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$ref_remain_balance' WHERE email='" . $all_user_details["referral"] . "'") == true) {
								if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, status, description, transaction_type, website) VALUES ('" . $all_user_details["referral"] . "','$ref_reference','$ref_amount', 'successful', 'Referral Upgrade Commission of $user_session','commission', '$site_name')")) {

								}
							}
						}
					}
				}

				$message = "Successful, Account Upgraded to <b>" . $user_account_level . "</b>";
			} else {
				$message = "Account Level is already <b>" . $user_account_level . "</b>, Try to Upgrade To Higher Level! ";
			}
		} else {
			$message = "Account Can't be Upgraded, User doesn't Exists! ";
		}
	}
	$_SESSION["transaction_text"] = $message;
	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["change-admin-detail"])) {
	$fullname = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["fullname"]));
	$email = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["email"]));
	$password = md5(mysqli_real_escape_string($conn_server_db, strip_tags($_POST["password"])));
	$phone_number = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["phone-number"]));
	$home_address = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["home-address"]));
	$pin = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["pin"]));
	$reset_code = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["reset-code"]));

	if (mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT email FROM admin WHERE 1"))["email"] == "example@gmail.com") {
		if (mysqli_query($conn_server_db, "UPDATE admin SET fullname='$fullname', email='$email', password='$password', phone_number='$phone_number', home_address='$home_address', transaction_pin='$pin' WHERE 1")) {
			$_SESSION["admin"] = $email;
			$_SESSION["transaction_text"] = "Admin Details Changed Successfully";
			unset($_SESSION["admin-profile-code"]);
		}
	} else {
		$raw_number = "123456789012345678901234567890";
		$reference = substr(str_shuffle($raw_number), 0, 6);
		if (!isset($_SESSION["admin-profile-code"])) {
			$_SESSION["admin-profile-code"] = $reference;
			$to = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT email FROM admin WHERE 1"))["email"];
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
				<img src="http://' . $_SERVER["HTTP_HOST"] . '/images/logo.png">
				</div>
				<span id="content">Dear <b>' . mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM admin WHERE 1"))["fullname"] . '</b><br>
				Your Admin Reset Code is: ' . $_SESSION["admin-profile-code"] . '
				</span><br>
				<center>
				<a href="' . $_SERVER["HTTP_HOST"] . '" id="web_link">Visit Website</a><br>
				<a href="mailto:" id="web_link">support@' . $_SERVER["HTTP_HOST"] . '</a>
				</center>
				<div id="footer">
				<center>
				<img src="http://' . $_SERVER["HTTP_HOST"] . '/images/logo.png"><br>
				<b>Visit Our Website:</b> <a href="' . $_SERVER["HTTP_HOST"] . '" id="web_link">' . $_SERVER["HTTP_HOST"] . '</a><br>
				<b>Contact Us:</b> <a href="mailto:support@' . $_SERVER["HTTP_HOST"] . '" id="web_link">support@' . $_SERVER["HTTP_HOST"] . '</a>
				</center>
				</div>
				
				</body>
				</html>';
			// Always set content-type when sending HTML email
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

			// More headers
			$headers .= 'From: <support@' . $_SERVER["HTTP_HOST"] . '>' . "\r\n";
			$headers .= 'Cc: support@' . $_SERVER["HTTP_HOST"] . "\r\n";

			smtpEMAIL('support@' . $_SERVER["HTTP_HOST"], $to, $subject, $html_message, $headers);
			$_SESSION["email-sent"] = " Email Successfully Sent To All Users";
		}
		if ($_SESSION["admin-profile-code"] == $reset_code) {
			if (mysqli_query($conn_server_db, "UPDATE admin SET fullname='$fullname', email='$email', password='$password', phone_number='$phone_number', home_address='$home_address' WHERE 1")) {
				$_SESSION["admin"] = $email;
				$_SESSION["transaction_text"] = "Admin Details Changed Successfully";
				unset($_SESSION["admin-profile-code"]);
			}
		} else {
			$_SESSION["transaction_text"] = "Invalid Code! ";
		}
	}

	if (!isset($_SESSION["admin-profile-code"])) {
		unset($_SESSION["admin"]);
		unset($_SESSION["admin_password"]);
		header("refresh:2;url=/admin/logout.php");
	} else {
		header("Location: " . $_SERVER["REQUEST_URI"]);
	}
}

if (isset($_POST["change-admin-detail-cancel"])) {
	unset($_SESSION["admin-profile-code"]);
	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-user-info"])) {
	$email = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["email"]));
	$password = md5(mysqli_real_escape_string($conn_server_db, strip_tags($_POST["password"])));
	$pin = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["pin"]));
	$apikey_unshuffle_string = "abcdefghijklmnopqrstuvwxyz1234567890abcdefghijklmnopqrstuvwxyz";
	$shuffle_apikey = str_shuffle($apikey_unshuffle_string);
	$chopped_apikey = substr($shuffle_apikey, 0, 50);
	$apikey = $chopped_apikey;

	if (!empty($email) && !empty($password) && !empty($pin)) {
		if (mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$email'") == true) {
			if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$email'")) == 1) {

				if (mysqli_query($conn_server_db, "UPDATE users SET password='$password', transaction_pin='$pin',apikey='$apikey' WHERE email='$email'") == true) {
					$_SESSION["transaction_text"] = "User Info Successfully Changed! ";
				}
			} else {
				$_SESSION["transaction_text"] = "Account doesn't exist! ";
			}
		} else {
			$_SESSION["transaction_text"] = "Account doesn't exist! ";
		}
	} else {
		$_SESSION["transaction_text"] = "Empty Field";
	}

	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["reg"])) {

	$apikey_unshuffle_string = "abcdefghijklmnopqrstuvwxyz1234567890abcdefghijklmnopqrstuvwxyz";
	$shuffle_apikey = str_shuffle($apikey_unshuffle_string);
	$chopped_apikey = substr($shuffle_apikey, 0, 50);
	$apikey = $chopped_apikey;
	$firstname = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["firstname"]));
	$lastname = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["lastname"]));
	$email = mysqli_real_escape_string($conn_server_db, strip_tags(strtolower($_POST["email"])));
	$phone_number = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["phone_number"]));
	$password = md5(strip_tags($_POST["password"]));
	$confirm_password = md5(strip_tags($_POST["confirm_password"]));
	$home_address = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["home_address"]));
	$referral = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["referral"]));


	if (!empty(trim($firstname)) && !empty(trim($lastname)) && !empty(trim($email)) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty(trim($phone_number)) && !empty(trim($password)) && !empty(trim($home_address))) {
		if (trim($password) == trim($confirm_password)) {
			$check_user_info = mysqli_query($conn_server_db, "SELECT phone_number, email FROM " . $user_table_name . " WHERE email='$email' OR phone_number='$phone_number'");
			if (mysqli_num_rows($check_user_info) == 0) {
				$registration_data = "INSERT INTO " . $user_table_name . " (firstname, lastname, email, password, phone_number, referral, transaction_pin, home_address, wallet_balance, account_type, commission, apikey, account_status) VALUES ('$firstname','$lastname','$email','$password','$phone_number','$referral','1234','$home_address','0','smart_earner','0','$apikey','active')";
				if (mysqli_query($conn_server_db, $registration_data) == true) {
					$reg_message = "Registration Successful! User can now proceed to login";
				} else {
					$reg_message = "Registration Failed! Try Again Later! ";
				}
			} else {
				$reg_message = "User Exists with EMAIL or PHONE NUMBER you Entered! ";
			}

		} else {
			$reg_message = "Passwords are not EQUAL";
		}
	} else {
		$reg_message = "Required Information must not be BLANK";
	}
	$_SESSION["transaction_text"] = $reg_message;
}

if (isset($_POST["update-bank-details"])) {
	$acct_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["name"]));
	$acct_number = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["number"]));
	$bank_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["bank"]));
	if (mysqli_query($conn_server_db, "UPDATE admin_bank_details SET acct_name='$acct_name',acct_number='$acct_number',bank_name='$bank_name' WHERE 1") == true) {

	}
	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["addblockphone"])) {
	$phone_number = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["phone_number"]));
	$type = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["type"]));
	if ($type == "block") {
		if (mysqli_query($conn_server_db, "INSERT INTO blocked_phone (phone_number) VALUES ('$phone_number')") === TRUE) {
			$_SESSION["transaction_text"] = $phone_number . " added successfully to Blocked List";
		}
	}

	if ($type == "unblock") {
		if (mysqli_query($conn_server_db, "UPDATE blocked_phone SET phone_number='' WHERE phone_number='$phone_number'") === TRUE) {
			$_SESSION["transaction_text"] = $phone_number . " removed from Blocked List";
		} else {
			$_SESSION["transaction_text"] = "Phone Number Doesnt Exists!";
		}
	}

	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["addremoveRechargeCardEmail"])) {
	$email = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["email"]));
	$type = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["type"]));
	if ($type == "add") {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$email'")) == 1) {
			if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM authorized_rechargecard_user WHERE email='$email'")) < 1) {

				if (mysqli_query($conn_server_db, "INSERT INTO authorized_rechargecard_user (email) VALUES ('$email')") === TRUE) {
					$_SESSION["transaction_text"] = $email . " added successfully to Recharge Card Authorization Database";
				}
			} else {
				$_SESSION["transaction_text"] = "Email (User) Already Exists in Recharge Card Authorization Database!";
			}
		} else {
			$_SESSION["transaction_text"] = "Email (User) Doesnt Exists in Member Database!";
		}
	}

	if ($type == "remove") {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM authorized_rechargecard_user WHERE email='$email'")) >= 1) {
			if (mysqli_query($conn_server_db, "UPDATE authorized_rechargecard_user SET email='' WHERE email='$email'") === TRUE) {
				$_SESSION["transaction_text"] = $email . " removed from Recharge Card Authorization Database";
			}
		} else {
			$_SESSION["transaction_text"] = "Email (User) Doesnt Exists in Recharge Card Authorization Database!";
		}
	}

	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["addremoveDataCardEmail"])) {
	$email = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["email"]));
	$type = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["type"]));
	if ($type == "add") {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$email'")) == 1) {
			if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM authorized_datacard_user WHERE email='$email'")) < 1) {

				if (mysqli_query($conn_server_db, "INSERT INTO authorized_datacard_user (email) VALUES ('$email')") === TRUE) {
					$_SESSION["transaction_text"] = $email . " added successfully to Data Card Authorization Database";
				}
			} else {
				$_SESSION["transaction_text"] = "Email (User) Already Exists in Data Card Authorization Database!";
			}
		} else {
			$_SESSION["transaction_text"] = "Email (User) Doesnt Exists in Member Database!";
		}
	}

	if ($type == "remove") {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM authorized_datacard_user WHERE email='$email'")) >= 1) {
			if (mysqli_query($conn_server_db, "UPDATE authorized_datacard_user SET email='' WHERE email='$email'") === TRUE) {
				$_SESSION["transaction_text"] = $email . " removed from Data Card Authorization Database";
			}
		} else {
			$_SESSION["transaction_text"] = "Email (User) Doesnt Exists in Data Card Authorization Database!";
		}
	}

	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-minifund"])) {
	$amount = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["amount"]));
	if (!empty($amount)) {
		if (mysqli_query($conn_server_db, "UPDATE minimum_user_fund SET amount='" . $amount . "' WHERE 1") == true) {
			$_SESSION["transaction_text"] = "Minimum Amount updated successfully";
		} else {
			$_SESSION["transaction_text"] = "Error: " . mysqli_error($conn_server_db);
		}
	} else {
		$_SESSION["transaction_text"] = "Input is Empty";
	}

	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-daily-quota"])) {
	$number = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["number"]));
	if (!empty($number)) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM admin_daily_minimum_purchase_per_id LIMIT 1")) == 1) {
			if (mysqli_query($conn_server_db, "UPDATE admin_daily_minimum_purchase_per_id SET minimum_unit='" . $number . "' WHERE 1") == true) {
				$_SESSION["transaction_text"] = "Daily Quota updated successfully";
			} else {
				$_SESSION["transaction_text"] = "Error: " . mysqli_error($conn_server_db);
			}
		} else {
			if (mysqli_query($conn_server_db, "INSERT INTO admin_daily_minimum_purchase_per_id (minimum_unit) VALUES ('" . $number . "')") == true) {
				$_SESSION["transaction_text"] = "Daily Quota created successfully";
			} else {
				$_SESSION["transaction_text"] = "Error: " . mysqli_error($conn_server_db);
			}
		}
	} else {
		$_SESSION["transaction_text"] = "Input is Empty";
	}

	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-admin-bvnin"])) {
	$admin_bvn = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["bvn"]));
	$admin_nin = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["nin"]));
	if (!empty($admin_bvn) && !empty($admin_nin) && (strlen($admin_bvn) == "11" || strlen($admin_nin) == "11")) {
		if (mysqli_query($conn_server_db, "UPDATE `admin` SET bvn='" . $admin_bvn . "', nin='" . $admin_nin . "' WHERE 1") == true) {
			$_SESSION["transaction_text"] = "Admin BVN/NIN updated successfully";
		} else {
			$_SESSION["transaction_text"] = "Error: " . mysqli_error($conn_server_db);
		}
	} else {
		$_SESSION["transaction_text"] = "Input is Empty/BVN or NIN must be 11digit";
	}

	header("Location: " . $_SERVER["REQUEST_URI"]);
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>
		<?php echo mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM site_info WHERE 1"))["sitetitle"]; ?>
	</title>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; " />
	<meta name="theme-color" content="skyblue" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="/css/site.css">
	<script src="/scripts/auth.js"></script>
	<script>
		function activateUser(page, num, userEmail) {
			if (confirm("Do you want to ACTIVATE this User with Email: " + userEmail)) {
				window.location.href = "/admin/site-setting.php?page=" + page + "&num=" + num + "&active=" + userEmail;
			} else {
				alertPopUp("Activation Request <b>CANCELLED</b> ");
			}
		}

		function blockUser(page, num, userEmail) {
			if (confirm("Do you want to BLOCK this User with Email: " + userEmail)) {
				window.location.href = "/admin/site-setting.php?page=" + page + "&num=" + num + "&block=" + userEmail;
			} else {
				alertPopUp("Blocking Request <b>CANCELLED</b> ");
			}
		}

		function DeleteUser(page, num, userEmail) {
			if (confirm("Do you want to DELETE this User with Email: " + userEmail)) {
				window.location.href = "/admin/site-setting.php?page=" + page + "&num=" + num + "&deleteuser=" + userEmail;
			} else {
				alertPopUp("Delete Request <b>CANCELLED</b> ");
			}
		}

		function LoginUser(page, num, userEmail) {
			if (confirm("Do you want to LOGIN to this User Account with Email: " + userEmail)) {
				window.location.href = "/admin/site-setting.php?page=" + page + "&num=" + num + "&loginuser=" + userEmail;
			} else {
				alertPopUp("Login Request <b>CANCELLED</b> ");
			}
		}

		function approvedSms(page, num, userEmail, senderID) {
			if (confirm("Do you want to APPROVE this User Sender ID with Email: " + userEmail)) {
				window.location.href = "/admin/site-setting.php?page=" + page + "&num=" + num + "&active=" + userEmail + "&sender_id=" + senderID;
			} else {
				alertPopUp("Approval Request <b>CANCELLED</b> ");
			}
		}

		function rejectedSms(page, num, userEmail, senderID) {
			if (confirm("Do you want to REJECT this User Sender ID with Email: " + userEmail)) {
				window.location.href = "/admin/site-setting.php?page=" + page + "&num=" + num + "&block=" + userEmail + "&sender_id=" + senderID;
			} else {
				alertPopUp("Rejection Request <b>CANCELLED</b> ");
			}
		}


	</script>
</head>

<body>
	<?php include("../include/admin-header-html.php"); ?>

	<center>
		<div
			class="container-box color-8 bg-6 mobile-font-size-14 system-font-size-18 mobile-width-91 system-width-91 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-2 system-padding-top-2 mobile-padding-left-2 system-padding-left-2 mobile-padding-right-2 system-padding-right-2 mobile-padding-bottom-2 system-padding-bottom-2">
			<!-- BEGIN DASHBOARD CODE -->

			<?php if ($showFunction == "dashboard") {
				$get_userMessage_details = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM user_message"));
				?>
				<fieldset>
					<legend>
						<span class="color-8 mobile-font-size-14 system-font-size-18">DASHBOARD ONLOAD
							MESSAGE</b></span><br>
					</legend>

					<form method="post">
						<textarea name="alert-message"
							class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
							placeholder="Dashboard Alert Message"><?php echo $get_userMessage_details["user_alert"]; ?></textarea><br>
						<textarea name="static-message"
							class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
							placeholder="Dashboard Static Message"><?php echo $get_userMessage_details["user_static"]; ?></textarea><br>
						<input name="update-dashboard" type="submit"
							class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
							value="Update Message" /><br>
					</form>
				</fieldset><br>
			<?php } ?>



			<!-- END DASHBOARD CODE -->

			<!-- BEGIN USERS CODE -->
			<?php

			function myMethod($u_em)
			{
				global $conn_server_db;
				$user_email_acc = $u_em;
				$my_array = array();
				$wallet_total_spent = mysqli_query($conn_server_db, "SELECT SUM(amount) AS TotalAmountSpent FROM transaction_history WHERE email='$user_email_acc' && transaction_type != 'wallet-funding' && transaction_type != 'credit' && transaction_type != 'refunded' && transaction_type != 'commission'");

				if (mysqli_num_rows($wallet_total_spent) > 0) {
					$total_spent = mysqli_fetch_assoc($wallet_total_spent);
					return $total_spent["TotalAmountSpent"];
				} else {
					return 0;
				}
			}
			if ($showFunction == "user") {
				echo '<form method="get">
					<input hidden name="page" value="user">
					<input name="search" value="' . trim(strip_tags($_GET["search"])) . '" type="text" class="input-box mobile-width-40 system-width-30 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Search by: Email, Phone No">
					<button id="" class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Search</button><br>
					</form>
					<span class="color-8 mobile-font-size-14 system-font-size-18">USERS LIST [Table List Count: <b>' . mysqli_num_rows($users_20_details) . '</b>], Total User: <b>' . mysqli_num_rows($users_details) . ' USERS</b></span><br>
					<div class="scrollable-div">
						<table class="table-style-2 table-font-size-1">
					<tr>
						<th>Name</th><th>Email</th><th>Phone</th><th>Wallet Balance</th><th>Total Spent</th><th>Transaction Pin</th><th>Home Address</th><th>Account Level</th><th>Account Status</th><th>Action</th><th>Delete</th><th>User Login</th>
					</tr>';
				if (mysqli_num_rows($users_20_details) > 0) {
					while ($users = mysqli_fetch_assoc($users_20_details)) {
						if ($users["account_status"] == "active") {
							$account_status = "block";
						} else {
							$account_status = "activate";
						}



						echo '<tr>
								<td>' . $users["firstname"] . ' ' . $users["lastname"] . '</td><td>' . $users["email"] . '</td><td>' . $users["phone_number"] . '</td><td>N' . $users["wallet_balance"] . '</td><td>N' . myMethod($users["email"]) . '</td><td>' . $users["transaction_pin"] . '</td><td>' . $users["home_address"] . '</td><td>' . ucwords(str_replace("_", " ", $users["account_type"])) . '</td><td>' . ucwords($users["account_status"]) . '</td><td><button onclick="javascript:' . $account_status . 'User(`' . $showFunction . '`,`' . $pageNum . '`,`' . $users["email"] . '`);" class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85">' . strtoupper($account_status) . '</button></td><td><button onclick="javascript:DeleteUser(`' . $showFunction . '`,`' . $pageNum . '`,`' . $users["email"] . '`);" class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85">Delete User</button></td><td><button onclick="javascript:LoginUser(`' . $showFunction . '`,`' . $pageNum . '`,`' . $users["email"] . '`);" class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85">Login</button></td>
							</tr>';
					}
				} else {
					$error_message = "Empty User Table! ";
				}

				echo '<tr>
						<th>Name</th><th>Email</th><th>Phone</th><th>Wallet Balance</th><th>Total Spent</th><th>Transaction Pin</th><th>Home Address</th><th>Account Level</th><th>Account Status</th><th>Action</th><th>Delete</th><th>User Login</th>
					</tr>
						</table>
					</div><br>';
			}
			?>

			<?php if ($showFunction == "user") { ?>
				<a href="/admin/site-setting.php?page=user&num=<?php if ($pageNum > 1) {
					echo round($pageNum - 1);
				} else {
					echo 1;
				} ?>">
					<button name="num" type="button"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Prev</button>
				</a>

				<a href="/admin/site-setting.php?page=user&num=<?php if ($pageNum >= 1) {
					echo round($pageNum + 1);
				} else {
					echo 1;
				} ?>">
					<button name="num" type="button"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Next</button>
				</a><br>

				<span class="color-8 mobile-font-size-14 system-font-size-18">BLOCKED USER</span><br>
				<input id="userEmail" type="email"
					class="input-box mobile-width-40 system-width-30 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
					value="<?php echo trim(mysqli_real_escape_string($conn_server_db, strip_tags($_GET['block']))); ?>"
					placeholder="Email" required />
				<script>
					var userEmail = document.getElementById("userEmail").value;
				</script>
				<input onclick="blockUser('<?php echo $showFunction; ?>','<?php echo $pageNum; ?>',userEmail);"
					type="button"
					class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15"
					value="Block" /><br>
			<?php } ?>

			<!-- END USERS CODE -->

			<!-- BEGIN EMAIL CODE -->

			<?php if ($showFunction == "email") { ?>
				<fieldset>
					<legend>
						<span class="color-8 mobile-font-size-14 system-font-size-18">SEND BULK EMAIL TO
							USERS</b></span><br>
					</legend>

					<?php if ($_SESSION["email-sent"] == true) { ?>
						<div id="font-color-1" class="message-box font-size-2"><?php echo $_SESSION["email-sent"]; ?></div>
					<?php } ?>

					<form method="post">
						<input name="subject" type="text"
							class="input-box mobile-width-95 system-width-85 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
							placeholder="Mail Title" /><br>
						<textarea name="message"
							class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
							placeholder="Email Message... "></textarea><br>
						<input name="send-mail" type="submit"
							class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
							value="Send Mail" /><br>
					</form>
				</fieldset><br>
			<?php } ?>



			<!-- END EMAIL CODE -->

			<!-- BEGIN SMS CODE -->

			<?php
			if ($showFunction == "sms") {
				echo '<span class="color-8 mobile-font-size-14 system-font-size-18">SMS SENDER REQUEST LIST [Table List Count: <b>' . mysqli_num_rows($sms_20_details) . '</b>], Total SMS Sender ID: <b>' . mysqli_num_rows($sms_details) . ' </b></span><br>
				<div class="scrollable-div">
					<table class="table-style-2 table-font-size-1">
				<tr>
					<th>Email</th><th>Sender ID</th><th>Status</th><th>Action</th>
				</tr>';
				if (mysqli_num_rows($sms_20_details) > 0) {
					while ($sms = mysqli_fetch_assoc($sms_20_details)) {
						if ($sms["status"] == "approved") {
							$account_status = "rejected";
						} else {
							if ($sms["status"] == "pending") {
								$account_status = "approved";
							} else {
								if ($sms["status"] == "rejected") {
									$account_status = "approved";
								} else {
									$account_status = "rejected";
								}
							}
						}
						echo '<tr>
							<td>' . $sms["email"] . '</td><td>' . $sms["sender_id"] . '</td><td>' . ucwords($sms["status"]) . '</td><td><button onclick="javascript:' . $account_status . 'Sms(`' . $showFunction . '`,`' . $pageNum . '`,`' . $sms["email"] . '`,`' . $sms["sender_id"] . '`);" class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85">' . strtoupper($account_status) . '</button></td>
						</tr>';
					}
				} else {
					$error_message = "Empty SMS Table! ";
				}

				echo '<tr>
					<th>Email</th><th>Sender ID</th><th>Status</th><th>Action</th>
				</tr>
					</table>
				</div><br>';
			}
			?>

			<?php if ($showFunction == "sms") { ?>
				<a href="/admin/site-setting.php?page=sms&num=<?php if ($pageNum > 1) {
					echo round($pageNum - 1);
				} else {
					echo 1;
				} ?>">
					<button name="num" type="button"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Prev</button>
				</a>

				<a href="/admin/site-setting.php?page=sms&num=<?php if ($pageNum >= 1) {
					echo round($pageNum + 1);
				} else {
					echo 1;
				} ?>">
					<button name="num" type="button"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Next</button>
				</a><br>

				<span class="color-8 mobile-font-size-12 system-font-size-16">BLOCK SMS SENDER ID</span><br>
				<input id="smsEmail" type="email"
					class="input-box mobile-width-40 system-width-30 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
					value="<?php echo trim(mysqli_real_escape_string($conn_server_db, strip_tags($_GET['block']))); ?>"
					placeholder="Email" required />
				<script>
					var smsEmail = document.getElementById("smsEmail").value;
				</script>
				<input onclick="blocksms('<?php echo $showFunction; ?>','<?php echo $pageNum; ?>',smsEmail);" type="button"
					class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15"
					value="Block SMS Sender ID" /><br>
			<?php } ?>


			<!-- END SMS CODE -->

			<!-- BEGIN DEBIT/CREDIT CODE -->

			<?php if ($showFunction == "fund") { ?>
				<fieldset>
					<legend>
						<span class="color-8 mobile-font-size-14 system-font-size-18">DEBIT/CREDIT USERS</b></span><br>
					</legend>

					<?php if ($_SESSION["user_fund"] == true) { ?>
						<div name="message" id="font-color-1" class="message-box font-size-2">
							<?php echo $_SESSION["user_fund"]; ?>
						</div>
					<?php } ?>

					<form method="post">
						<select name="type"
							class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85">
							<option value="debit">DEBIT</option>
							<option value="credit">CREDIT</option>

							<option value="deduction">DEDUCT USER</option>
							<option value="refunded">REFUND USER</option>
						</select>
						<input name="email" type="email" class="input-box mobile-width-93 system-width-40"
							placeholder="User Email" />
						<input name="amount" type="number" class="input-box mobile-width-93 system-width-40"
							placeholder="Amount" /><br>
						<input name="desc" type="text" class="input-box mobile-width-93 system-width-40"
							placeholder="Description... " />
						<input name="transaction_pin" type="number" class="input-box mobile-width-93 system-width-40"
							placeholder="Transaction Pin" /><br>
						<input name="fund" type="submit"
							class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
							value="Proceed" /><br>
					</form>
				</fieldset><br>
			<?php } ?>



			<!-- END DEBIT/CREDIT CODE -->

			<!-- BEGIN PAYMENT CODE -->

			<?php if ($showFunction == "payment") {
				$monnify_keys = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM payment_api WHERE website='monnify'"));
				$flutterwave_keys = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM payment_api WHERE website='flutterwave'"));
				$paystack_keys = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM payment_api WHERE website='paystack'"));
				?>

				<span class="color-8 mobile-font-size-14 system-font-size-18">PAYMENT SETTING</span><br>
				<form method="post">
					<span class="color-8 mobile-font-size-14 system-font-size-18">MONNIFY</span><br>
					<input placeholder="Monnify Webhook" type="text" class="input-box mobile-width-85 system-width-85"
						value="<?php echo $w_host; ?>/monnify-webhook.php" readonly /><br>
					<input placeholder="API Key" name="monnify-apikey" type="text"
						class="input-box mobile-width-85 system-width-85"
						value="<?php echo $monnify_keys['public_key']; ?>" /><br>
					<input placeholder="Secret Key" name="monnify-secretkey" type="text"
						class="input-box mobile-width-85 system-width-85"
						value="<?php echo $monnify_keys['secret_key']; ?>" /><br>
					<input placeholder="Contract Key" name="monnify-contractkey" type="text"
						class="input-box mobile-width-85 system-width-85"
						value="<?php echo $monnify_keys['encrypt_key']; ?>" /><br>
					<input name="monnify-status" type="checkbox" class="checkbox" value="1" <?php if ($monnify_keys['api_status'] == true) {
						echo "checked";
					} ?> />Enable/Disabled Monnify<br>

					<span class="color-8 mobile-font-size-14 system-font-size-18">FLUTTERWAVE</span><br>
					<input placeholder="Flutterwave Webhook" type="text" class="input-box mobile-width-85 system-width-85"
						value="<?php echo $w_host; ?>/flutterwave-webhook.php" readonly /><br>
					<input placeholder="Public Key" name="flutterwave-publickey" type="text"
						class="input-box mobile-width-85 system-width-85"
						value="<?php echo $flutterwave_keys['public_key']; ?>" /><br>
					<input placeholder="Secret Key" name="flutterwave-secretkey" type="text"
						class="input-box mobile-width-85 system-width-85"
						value="<?php echo $flutterwave_keys['secret_key']; ?>" /><br>
					<input placeholder="Encrypt Key" name="flutterwave-encryptkey" type="text"
						class="input-box mobile-width-85 system-width-85"
						value="<?php echo $flutterwave_keys['encrypt_key']; ?>" /><br>
					<input name="flutterwave-status" type="checkbox" class="checkbox" value="1" <?php if ($flutterwave_keys['api_status'] == true) {
						echo "checked";
					} ?> />Enable/Disabled Flutterwave<br>

					<span class="color-8 mobile-font-size-14 system-font-size-18">PAYSTACK</span><br>
					<input placeholder="Paystack Webhook" type="text" class="input-box mobile-width-85 system-width-85"
						value="<?php echo $w_host; ?>/paystack-webhook.php" readonly /><br>
					<input placeholder="Public Key" name="paystack-publickey" type="text"
						class="input-box mobile-width-85 system-width-85"
						value="<?php echo $paystack_keys['public_key']; ?>" /><br>
					<input placeholder="Secret Key" name="paystack-secretkey" type="text"
						class="input-box mobile-width-85 system-width-85"
						value="<?php echo $paystack_keys['secret_key']; ?>" /><br>
					<input placeholder="Encrypt Key" name="paystack-encryptkey" type="text"
						class="input-box mobile-width-85 system-width-85"
						value="<?php echo $paystack_keys['encrypt_key']; ?>" /><br>
					<input name="paystack-status" type="checkbox" class="checkbox" value="1" <?php if ($paystack_keys['api_status'] == true) {
						echo "checked";
					} ?> />Enable/Disabled Paystack<br>

					<input name="update-payment-key" type="submit"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
						value="Update Payment Key" /><br>
				</form>
			<?php } ?>

			<!-- END PAYMENT CODE -->


			<!-- BEGIN TRANSACTION CODE -->

			<?php if ($showFunction == "transaction") {
				$select_total_transaction_history = mysqli_query($conn_server_db, "SELECT * FROM transaction_history");
				if (empty(trim(strip_tags($_GET["search"])))) {
					$select_transaction_history = mysqli_query($conn_server_db, "SELECT * FROM transaction_history ORDER BY transaction_date DESC LIMIT 20 OFFSET " . round(0 + (($pageNum - 1) * 20)));
				} else {
					$select_transaction_history = mysqli_query($conn_server_db, "SELECT * FROM transaction_history WHERE (id='" . trim(strip_tags($_GET["search"])) . "') OR (meter_no='" . trim(strip_tags($_GET["search"])) . "') ORDER BY transaction_date DESC LIMIT 20 OFFSET " . round(0 + (($pageNum - 1) * 20)));
				}
				?>
				<form method="get">
					<input hidden name="page" value="transaction">
					<input name="search" value="<?php echo trim(strip_tags($_GET["search"])); ?>" type="text"
						class="input-box mobile-width-35 system-width-30" placeholder="Search by: Reference, Meter No">
					<button id=""
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Search</button><br>
				</form>
				<span class="color-8 mobile-font-size-14 system-font-size-18">TRANSACTION HISTORY [Table List Count:
					<b><?php echo mysqli_num_rows($select_transaction_history); ?></b>], Total Transaction:
					<b><?php echo mysqli_num_rows($select_total_transaction_history); ?></b></span><br>
				<div class="scrollable-div">
					<table class="table-style-2">
						<tr>
							<th>Action</th>
							<th>Status</th>
							<th>Email</b>
							<th>Reference</th>
							<th>Amount (Naira)</th>
							<th>Balance Before</th>
							<th>Balance After</th>
							<th>Transaction Details</th>
							<th>Type</th>
							<th>API Website</th>
							<th>Date</th>
						</tr>
						<?php
						//requery
						include("../include/gateway-apikey.php");
						include("../include/requery-transaction.php");

						if (mysqli_num_rows($select_transaction_history) > 0) {
							while ($transaction_details = mysqli_fetch_assoc($select_transaction_history)) {
								if ((strtolower($transaction_details["status"]) !== "successful") or (strtolower($transaction_details["status"]) !== "completed") or (strtolower($transaction_details["status"]) !== "done") or (strtolower($transaction_details["status"]) !== "success")) {
									$requery_html = '<a style="color:inherit;" href="' . $_SERVER["REQUEST_URI"] . '&requery=' . $transaction_details["id"] . '">Requery</a>';
								} else {
									$requery_html = "Done";
								}
								echo "<tr>
						<td>" . $requery_html . "</td>
						<td>" . $transaction_details["status"] . "</td>
						<td>" . $transaction_details["email"] . "</td><td>" . $transaction_details["id"] . "</td><td>" . $transaction_details["amount"] . "</td><td>" . $transaction_details["w_bef"] . "</td><td>" . $transaction_details["w_aft"] . "</td><td>" . $transaction_details["description"] . "</td><td>" . ucwords(str_replace(["-", "_"], " ", $transaction_details["transaction_type"])) . "</td><td>" . $transaction_details["website"] . "</td><td>" . $transaction_details["transaction_date"] . "</td>
					</tr>";
							}
						}
						?>
						<tr>
							<th>Action</th>
							<th>Status</th>
							<th>Email</b>
							<th>Reference</th>
							<th>Amount (Naira)</th>
							<th>Balance Before</th>
							<th>Balance After</th>
							<th>Transaction Details</th>
							<th>Type</th>
							<th>API Website</th>
							<th>Date</th>
						</tr>
					</table>
				</div><br>

				<a href="/admin/site-setting.php?page=transaction&num=<?php if ($pageNum > 1) {
					echo round($pageNum - 1);
				} else {
					echo 1;
				} ?>">
					<button name="num" type="button"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Prev</button>
				</a>

				<a href="/admin/site-setting.php?page=transaction&num=<?php if ($pageNum >= 1) {
					echo round($pageNum + 1);
				} else {
					echo 1;
				} ?>">
					<button name="num" type="button"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Next</button>
				</a><br>
			<?php } ?>

			<!-- END TRANSACTION CODE -->

			<!-- BEGIN PROFILE CODE -->

			<?php if ($showFunction == "profile") {

				$get_profile_info = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT fullname, email, password, phone_number, transaction_pin, home_address FROM admin"));
				?>
				<fieldset>
					<?php if ($_SESSION["transaction_text"] == true) { ?>
						<div name="message" id="font-color-1" class="message-box font-size-2">
							<?php echo $_SESSION["transaction_text"]; ?>
						</div>
					<?php } ?>

					<legend>
						<span class="color-8 mobile-font-size-14 system-font-size-18">UPDATE ADMIN INFO</span><br>
					</legend>
					<form method="post">
						<input placeholder="Full Name" name="fullname" type="text"
							class="input-box mobile-width-85 system-width-85"
							value="<?php echo $get_profile_info['fullname']; ?>" /><br>
						<input placeholder="Phone Number" name="phone-number" type="tel"
							class="input-box mobile-width-85 system-width-85"
							value="<?php echo $get_profile_info['phone_number']; ?>" /><br>
						<input placeholder="Home Address" name="home-address" type="text"
							class="input-box mobile-width-85 system-width-85"
							value="<?php echo $get_profile_info['home_address']; ?>" /><br>
						<input placeholder="Email" name="email" type="email"
							class="input-box mobile-width-85 system-width-85"
							value="<?php echo $get_profile_info['email']; ?>" /><br>
						<input placeholder="Password" name="password" type="text" pattern="[0-9a-zA-Z]{8,}"
							title="Password must be alphanumeric and must be at least 8 character long"
							class="input-box mobile-width-85 system-width-40" value="" required />
						<input placeholder="Transaction Pin" name="pin" type="number"
							class="input-box mobile-width-85 system-width-40" value="" required /><br>

						<input name="change-admin-detail" type="submit"
							class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
							value="Change Admin Details" /><br>
						<?php if (isset($_SESSION["admin-profile-code"])) { ?>
							<input name="change-admin-detail-cancel" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
								value="Cancel" /><br>
						<?php } ?>
					</form>
				</fieldset><br>
			<?php } ?>

			<!-- END PROFILE CODE -->

			<!-- BEGIN USER UPGRADE CODE -->

			<?php if ($showFunction == "upgrade") {
				$get_vip_earner_upgrade_price = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM upgrade_price WHERE level='vip_earner'"));
				$get_vip_vendor_upgrade_price = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM upgrade_price WHERE level='vip_vendor'"));
				$get_api_earner_upgrade_price = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM upgrade_price WHERE level='api_earner'"));

				?>
				<fieldset>
					<legend>
						<span class="color-8 mobile-font-size-14 system-font-size-18">USER UPGRADE PRICE</span>
					</legend>
					<form method="post">
						<input placeholder="VIP Earner" name="price-1" type="tel"
							class="input-box mobile-width-85 system-width-85"
							value="<?php echo $get_vip_earner_upgrade_price['amount']; ?>" /><br>
						<input placeholder="VIP Vendor" name="price-2" type="tel"
							class="input-box mobile-width-85 system-width-85"
							value="<?php echo $get_vip_vendor_upgrade_price['amount']; ?>" /><br>
						<input placeholder="API Earner" name="price-3" type="tel"
							class="input-box mobile-width-85 system-width-85"
							value="<?php echo $get_api_earner_upgrade_price['amount']; ?>" /><br>
						<input name="update-upgrade-price" type="submit"
							class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
							value="Update Upgrade Price" /><br>
					</form>
				</fieldset>


				<fieldset>
					<?php if ($_SESSION["transaction_text"] == true) { ?>
						<div name="message" id="font-color-1" class="message-box font-size-2">
							<?php echo $_SESSION["transaction_text"]; ?>
						</div>
					<?php } ?>
					<legend>
						<span class="color-8 mobile-font-size-14 system-font-size-18"><b>Upgrade User Account</b></span>
					</legend>
					<form action="" method="post">
						<input placeholder="User Email" name="email" type="email"
							class="input-box mobile-width-85 system-width-85" /><br>
						<select name="upgrade-package" id="package"
							class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
							required>
							<option disabled hidden selected>Choose Package</option>
							<option value="smart_earner:1">Smart Earner @ N1</option>
							<option value="vip_earner:<?php echo $get_vip_earner_upgrade_price['amount']; ?>">VIP Earner @
								N<?php echo $get_vip_earner_upgrade_price["amount"]; ?></option>
							<option value="vip_vendor:<?php echo $get_vip_vendor_upgrade_price['amount']; ?>">VIP Vendor @
								N<?php echo $get_vip_vendor_upgrade_price["amount"]; ?></option>
							<option value="api_earner:<?php echo $get_api_earner_upgrade_price['amount']; ?>">Agent Vendor @
								N<?php echo $get_api_earner_upgrade_price["amount"]; ?></option>
						</select><br>
						<input name="upgrade-account" type="submit"
							class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
							value="Upgrade" />
					</form><br>
				</fieldset>

			<?php } ?>

			<!-- END USER UPGRADE CODE -->

			<!-- BEGIN RECAPTCHA CODE -->

			<?php if ($showFunction == "recaptcha") {
				$get_recaptcha_key = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM recaptcha_setting WHERE 1"));
				$get_sitetitle = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM site_info WHERE 1"));
				?>
				<fieldset>
					<legend>
						<span class="color-8 mobile-font-size-14 system-font-size-18">GOOGLE RECAPTCHA</span><br>
					</legend>
					<form method="post" enctype="multipart/form-data">
						<img src="./../images/logo.png" class="half-length" /><br>
						<input name="logo" type="file" class="input-box mobile-width-85 system-width-85" /><br>
						<input placeholder="Site Title" name="sitetitle" type="text"
							class="input-box mobile-width-85 system-width-85"
							value="<?php echo $get_sitetitle['sitetitle']; ?>" /><br>
						<input placeholder="Site Key" name="sitekey" type="text"
							class="input-box mobile-width-85 system-width-85"
							value="<?php echo $get_recaptcha_key['sitekey']; ?>" /><br>
						<input placeholder="Secret Key" name="secretkey" type="text"
							class="input-box mobile-width-85 system-width-85"
							value="<?php echo $get_recaptcha_key['secretkey']; ?>" /><br>
						<input name="update-recaptcha" type="submit"
							class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
							value="Update Settings" /><br>
					</form>
				</fieldset>
			<?php } ?>

			<!-- END RECAPTCHA CODE -->


			<!-- BEGIN EDIT USER CODE -->

			<?php if ($showFunction == "edituser") { ?>
				<fieldset>
					<legend>
						<span class="color-8 mobile-font-size-14 system-font-size-18">EDIT USER INFO</span><br>
					</legend>
					<?php if ($_SESSION["transaction_text"] == true) { ?>
						<div name="message" id="font-color-1" class="message-box font-size-2">
							<?php echo $_SESSION["transaction_text"]; ?>
						</div>
					<?php } ?>
					<form method="post">
						<input placeholder="User Email" name="email" type="text"
							class="input-box mobile-width-85 system-width-85" value="" /><br>
						<input placeholder="Password" name="password" type="text"
							class="input-box mobile-width-85 system-width-40" value="" />
						<input placeholder="Pin" name="pin" type="text" class="input-box mobile-width-85 system-width-40"
							value="" /><br>
						NB: New Apikey will be Generated! <br>
						<input name="update-user-info" type="submit"
							class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
							value="Update User Info" /><br>
					</form>
				</fieldset>
			<?php } ?>

			<!-- END EDIT USER CODE -->


			<!-- BEGIN USER REGISTRATION CODE -->

			<?php if ($showFunction == "userreg") { ?>
				<fieldset>
					<legend>
						<span class="color-8 mobile-font-size-14 system-font-size-18">USER REGISTRATION</span><br>
					</legend>
					<?php if ($_SESSION["transaction_text"] == true) { ?>
						<div name="message" id="font-color-1" class="message-box font-size-2">
							<?php echo $_SESSION["transaction_text"]; ?>
						</div>
					<?php } ?>
					<form action="" method="post">
						<input name="firstname" type="text" class="input-box mobile-width-85 system-width-85"
							placeholder="Firstname *" required />
						<input name="lastname" type="text" class="input-box mobile-width-85 system-width-85"
							placeholder="Lastname *" required />
						<input name="email" type="email" class="input-box mobile-width-85 system-width-85"
							placeholder="Email *" required />
						<input name="phone_number" type="text" pattern="[0-9]{11}"
							title="Phone Number must be Number and not more/less than 11 digits"
							class="input-box mobile-width-85 system-width-85" placeholder="Phone Number *" required />
						<input name="password" type="password" class="input-box mobile-width-85 system-width-85"
							placeholder="Password *" required />
						<input name="confirm_password" type="password" class="input-box mobile-width-85 system-width-85"
							placeholder="Confirm Password *" required />
						<input name="home_address" type="text" class="input-box mobile-width-85 system-width-85"
							placeholder="Home Address *" required />
						<input name="referral" type="text" class="input-box mobile-width-85 system-width-85"
							placeholder="Referral (Optional)" value="<?php if ($_GET['ref'] == true) {
								echo mysqli_real_escape_string($conn_server_db, strip_tags($_GET['ref']));
							} ?>" readonly />
						<input name="reg" type="submit"
							class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
							value="Register" /><br>
					</form>
				</fieldset>
			<?php } ?>

			<!-- END USER REGISTRATION CODE -->

			<!-- BEGIN PAYMENT ORDERS CODE -->

			<?php if ($showFunction == "paymentorder") {
				$select_total_payment_order_history = mysqli_query($conn_server_db, "SELECT email, id, amount, transaction_date FROM payment_order_history");
				if (empty(trim(strip_tags($_GET["search"])))) {
					$select_payment_order_history = mysqli_query($conn_server_db, "SELECT email, id, amount, status, transaction_date FROM payment_order_history ORDER BY transaction_date DESC LIMIT 20 OFFSET " . round(0 + (($pageNum - 1) * 20)));
				} else {
					$select_payment_order_history = mysqli_query($conn_server_db, "SELECT email, id, amount, status, transaction_date FROM payment_order_history WHERE (id='" . trim(strip_tags($_GET["search"])) . "') ORDER BY transaction_date DESC LIMIT 20 OFFSET " . round(0 + (($pageNum - 1) * 20)));
				}
				?>
				<form method="get">
					<input hidden name="page" value="paymentorder">
					<input name="search" value="<?php echo trim(strip_tags($_GET["search"])); ?>" type="text"
						class="input-box half-length" placeholder="Search by: Reference"> <button id=""
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Search</button><br>
				</form>
				<span class="color-8 mobile-font-size-14 system-font-size-18">PAYMENT ORDERS HISTORY [Table List Count:
					<b><?php echo mysqli_num_rows($select_payment_order_history); ?></b>], Total Payment Orders:
					<b><?php echo mysqli_num_rows($select_total_payment_order_history); ?></b></span><br>
				<div class="scrollable-div">
					<table class="table-style-2">
						<tr>
							<th>Email</b>
							<th>Reference</th>
							<th>Amount (Naira)</th>
							<th>Status</th>
							<th>Date</th>
							<th>Action</th>
						</tr>
						<?php
						if (mysqli_num_rows($select_payment_order_history) > 0) {
							while ($payment_order_details = mysqli_fetch_assoc($select_payment_order_history)) {
								if ((strtolower($payment_order_details["status"]) !== "approved") and (strtolower($payment_order_details["status"]) !== "cancelled")) {
									$approve_html = '<a style="color:inherit;" href="' . $_SERVER["REQUEST_URI"] . '&approve=' . $payment_order_details["id"] . '">Approve Payment</a> | <a style="color:inherit;" href="' . $_SERVER["REQUEST_URI"] . '&cancel=' . $payment_order_details["id"] . '">Cancel Order</a>';
								} else {
									$approve_html = "Done - " . ucwords($payment_order_details["status"]);
								}
								echo "<tr>
						<td>" . $payment_order_details["email"] . "</td><td>" . $payment_order_details["id"] . "</td><td>" . $payment_order_details["amount"] . "</td><td>" . $payment_order_details["status"] . "</td><td>" . $payment_order_details["transaction_date"] . "</td><td>" . $approve_html . "</td>
					</tr>";
							}
						}
						?>
						<tr>
							<th>Email</b>
							<th>Reference</th>
							<th>Amount (Naira)</th>
							<th>Status</th>
							<th>Date</th>
							<th>Action</th>
						</tr>
					</table>
				</div><br>

				<a href="/admin/site-setting.php?page=paymentorder&num=<?php if ($pageNum > 1) {
					echo round($pageNum - 1);
				} else {
					echo 1;
				} ?>">
					<button name="num" type="button"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Prev</button>
				</a>

				<a href="/admin/site-setting.php?page=paymentorder&num=<?php if ($pageNum >= 1) {
					echo round($pageNum + 1);
				} else {
					echo 1;
				} ?>">
					<button name="num" type="button"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Next</button>
				</a><br>
			<?php } ?>

			<!-- END PAYMENT ORDERS CODE -->

			<!-- BEGIN BANK DETAILS CODE -->
			<?php if ($_SESSION["admin"] == adminArray("email")[0]) { ?>
				<?php if ($showFunction == "bankdetails") {
					$get_admin_bank_details = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM admin_bank_details WHERE 1"));
					?>
					<fieldset>
						<legend>
							<span class="color-8 mobile-font-size-14 system-font-size-18">ADMIN BANK DETAilS</span><br>
						</legend>
						<?php if ($_SESSION["transaction_text"] == true) { ?>
							<div name="message" id="font-color-1" class="message-box font-size-2">
								<?php echo $_SESSION["transaction_text"]; ?>
							</div>
						<?php } ?>
						<form method="post">
							<input placeholder="Account Name" name="name" type="text"
								class="input-box mobile-width-85 system-width-85"
								value="<?php echo $get_admin_bank_details['acct_name']; ?>" required /><br>
							<input placeholder="Account Number" name="number" type="text"
								class="input-box mobile-width-85 system-width-40"
								value="<?php echo $get_admin_bank_details['acct_number']; ?>" required />
							<input placeholder="Bank Name" name="bank" type="text"
								class="input-box mobile-width-85 system-width-40"
								value="<?php echo $get_admin_bank_details['bank_name']; ?>" required /><br>
							<input name="update-bank-details" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
								value="Update Bank Details" /><br>
						</form>
					</fieldset>
				<?php } ?>
			<?php } ?>
			<!-- END BANK DETAILS CODE -->

			<!-- BEGIN BLOCK PHONE NUMBER CODE -->
			<?php if ($_SESSION["admin"] == adminArray("email")[0]) { ?>
				<?php if ($showFunction == "blockphone") {
					$get_admin_bank_details = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM admin_bank_details WHERE 1"));
					?>
					<fieldset>
						<legend>
							<span class="color-8 mobile-font-size-14 system-font-size-18">BLOCK/UNBLOCK PHONE NUMBER</span><br>
						</legend>
						<?php if ($_SESSION["transaction_text"] == true) { ?>
							<div name="message" id="font-color-1" class="message-box font-size-2">
								<?php echo $_SESSION["transaction_text"]; ?>
							</div>
						<?php } ?>
						<form method="post">
							<input name="phone_number" type="text" pattern="[0-9^+]{11,14}"
								title="Phone Number must be Number and atleast 11 digits to 14 digits"
								class="input-box mobile-width-85 system-width-40" placeholder="Phone Number *" required />
							<select name="type"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-40">
								<option value="block">BLOCK</option>
								<option value="unblock">UNBLOCK</option>
							</select>
							<input name="addblockphone" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
								value="UPDATE SETTING" /><br>
						</form>
					</fieldset>
				<?php } ?>
			<?php } ?>
			<!-- END BLOCK PHONE NUMBER CODE -->

			<!-- BEGIN ADD RECHARGECARD USER CODE -->
			<?php if ($_SESSION["admin"] == adminArray("email")[0]) { ?>
				<?php if ($showFunction == "activaterechargecardemail") {
					$get_admin_bank_details = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM admin_bank_details WHERE 1"));
					?>
					<fieldset>
						<legend>
							<span class="color-8 mobile-font-size-14 system-font-size-18">ADD/REMOVE USER FOR RECHARGE
								CARD</span><br>
						</legend>
						<?php if ($_SESSION["transaction_text"] == true) { ?>
							<div name="message" id="font-color-1" class="message-box font-size-2">
								<?php echo $_SESSION["transaction_text"]; ?>
							</div>
						<?php } ?>
						<form method="post">
							<input name="email" type="email" class="input-box mobile-width-85 system-width-40"
								placeholder="User Email *" required />
							<select name="type"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-40">
								<option value="add">ADD</option>
								<option value="remove">REMOVE</option>
							</select>
							<input name="addremoveRechargeCardEmail" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
								value="UPDATE SETTING" /><br>
						</form>
					</fieldset>
				<?php } ?>
			<?php } ?>
			<!-- END ADD RECHARGECARD USER CODE -->

			<!-- BEGIN ADD DATACARD USER CODE -->
			<?php if ($_SESSION["admin"] == adminArray("email")[0]) { ?>
				<?php if ($showFunction == "activatedatacardemail") {
					$get_admin_bank_details = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM admin_bank_details WHERE 1"));
					?>
					<fieldset>
						<legend>
							<span class="color-8 mobile-font-size-14 system-font-size-18">ADD/REMOVE USER FOR DATA
								CARD</span><br>
						</legend>
						<?php if ($_SESSION["transaction_text"] == true) { ?>
							<div name="message" id="font-color-1" class="message-box font-size-2">
								<?php echo $_SESSION["transaction_text"]; ?>
							</div>
						<?php } ?>
						<form method="post">
							<input name="email" type="email" class="input-box mobile-width-85 system-width-40"
								placeholder="User Email *" required />
							<select name="type"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-40">
								<option value="add">ADD</option>
								<option value="remove">REMOVE</option>
							</select>
							<input name="addremoveDataCardEmail" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
								value="UPDATE SETTING" /><br>
						</form>
					</fieldset>
				<?php } ?>
			<?php } ?>
			<!-- END ADD DATACARD USER CODE -->


			<!-- BEGIN USER MINIMUM FUND REQUIREMENT -->
			<?php if ($_SESSION["admin"] == adminArray("email")[0]) { ?>
				<?php if ($showFunction == "minifund") {
					$get_mini_user_fund_details = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT amount FROM minimum_user_fund WHERE 1"));
					?>
					<fieldset>
						<legend>
							<span class="color-8 mobile-font-size-14 system-font-size-18">USER MINIMUM FUND
								REQUIREMENT</span><br>
						</legend>
						<?php if ($_SESSION["transaction_text"] == true) { ?>
							<div name="message" id="font-color-1" class="message-box font-size-2">
								<?php echo $_SESSION["transaction_text"]; ?>
							</div>
						<?php } ?>
						<form method="post">
							<input name="amount" type="number" class="input-box mobile-width-85 system-width-60"
								placeholder="Amount *" value="<?php echo $get_mini_user_fund_details['amount']; ?>" required />
							<input name="update-minifund" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25"
								value="UPDATE" /><br>
						</form>
					</fieldset>
				<?php } ?>
			<?php } ?>
			<!-- END USER MINIMUM FUND REQUIREMENT CODE -->

			<!-- BEGIN USER DAILY QUOTA CONTROL -->
			<?php if ($_SESSION["admin"] == adminArray("email")[0]) { ?>
				<?php if ($showFunction == "dailyquotacontrol") {
					$get_user_daily_quota_transaction_details = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT minimum_unit FROM admin_daily_minimum_purchase_per_id WHERE 1"));
					?>
					<fieldset>
						<legend>
							<span class="color-8 mobile-font-size-14 system-font-size-18">USER DAILY TRANSACTION PER IDs (PHONE,
								CABLE, ELECTRIC)</span><br>
						</legend>
						<?php if ($_SESSION["transaction_text"] == true) { ?>
							<div name="message" id="font-color-1" class="message-box font-size-2">
								<?php echo $_SESSION["transaction_text"]; ?>
							</div>
						<?php } ?>
						<form method="post">
							<input name="number" type="number" class="input-box mobile-width-85 system-width-60"
								placeholder="No. of Times eg. 5 *"
								value="<?php echo $get_user_daily_quota_transaction_details['minimum_unit']; ?>" required />
							<input name="update-daily-quota" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25"
								value="UPDATE" /><br>
						</form>
					</fieldset>
				<?php } ?>
			<?php } ?>
			<!-- END USER DAILY QUOTA CONTROL CODE -->


			<!-- BEGIN ADMIN BVN/NIN -->
			<?php if ($_SESSION["admin"] == adminArray("email")[0]) { ?>
				<?php if ($showFunction == "adminbvnnin") {
					$get_admin_bvn_nin_details = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT bvn, nin FROM `admin` WHERE 1"));
					?>
					<fieldset>
						<legend>
							<span class="color-8 mobile-font-size-14 system-font-size-18">ADMIN BVN & NIN</span><br>
						</legend>
						<?php if ($_SESSION["transaction_text"] == true) { ?>
							<div name="message" id="font-color-1" class="message-box font-size-2">
								<?php echo $_SESSION["transaction_text"]; ?>
							</div>
						<?php } ?>
						<form method="post">
							<input name="bvn" type="number" class="input-box mobile-width-85 system-width-45"
								placeholder="BVN *" value="<?php echo $get_admin_bvn_nin_details['bvn']; ?>" required />
							<input name="nin" type="number" class="input-box mobile-width-85 system-width-45"
								placeholder="NIN *" value="<?php echo $get_admin_bvn_nin_details['nin']; ?>" required />

							<input name="update-admin-bvnin" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-93"
								value="UPDATE" /><br>
						</form>
					</fieldset>
				<?php } ?>
			<?php } ?>
			<!-- END ADMIN BVN/NIN CODE -->
		</div>
		<div class="container-box bg-5 mobile-width-95 system-width-95 mobile-margin-top-2 system-margin-top-2">
			<?php if ($error_message == true) { ?>
				<div name="message" id="font-color-1" class="message-box font-size-2"><?php echo $error_message; ?></div>
				<br>
			<?php } ?>

			<div name="message" id="font-color-1" class="message-box font-size-2">CRON Job:
				<b><?php echo "https://" . $_SERVER["HTTP_HOST"] . "/include/auto-requery.php"; ?></b>
			</div><br>
			<form method="get">
				<button name="page" value="dashboard" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Dashboard
					Announcement</button>
				<button name="page" value="user" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">View
					Users</button>
				<button name="page" value="email" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Send
					Emails</button>
				<button name="page" value="sms" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">SMS
					Sender ID Requests</button>
				<button name="page" value="fund" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Debit/Credit
					User</button>
				<button name="page" value="payment" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Payment
					Method</button>
				<button name="page" value="transaction" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">View
					All Transactions</button>
				<button name="page" value="profile" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Edit
					Profile</button>
				<button name="page" value="upgrade" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">User
					Upgrade Prices</button>
				<button name="page" value="recaptcha" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Update
					Recaptcha Settings</button>
				<button name="page" value="edituser" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Edit
					User</button>
				<button name="page" value="userreg" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">New
					User Registration</button>
				<button name="page" value="paymentorder" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">View
					All Payment Order</button>
				<button name="page" value="bankdetails" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Update
					Bank Details</button>
				<button name="page" value="blockphone" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Block
					Phone Number</button>
				<button name="page" value="activaterechargecardemail" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Activate
					User (Recharge Card Authorization)</button>
				<button name="page" value="activatedatacardemail" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Activate
					User (Data Card Authorization)</button>
				<button name="page" value="minifund" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Minimum
					User Fund Requirement</button>
				<button name="page" value="dailyquotacontrol" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">User
					(Daily Quota Control)</button>
				<button name="page" value="adminbvnnin" type="submit"
					class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-45">Admin
					(BVN/NIN Update)</button>
			</form>
		</div>
	</center>

	<?php include("../include/admin-footer-html.php"); ?>
</body>

</html>