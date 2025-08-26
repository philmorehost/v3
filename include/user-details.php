<?php
$user_table_name = "users";
$user_session = $_SESSION["user"];
if (isset($_SESSION["password"])) {
	$user_account_password_c = $_SESSION["password"];
	$check_user_empass = mysqli_query($conn_server_db, "SELECT email, password, account_status FROM users WHERE email='$user_session'");
	if (mysqli_num_rows($check_user_empass) > 0) {
		while ($user_details = mysqli_fetch_assoc($check_user_empass)) {
			if (trim($user_account_password_c) == $user_details["password"]) {

			} else {
				header("Location: /logout.php");
			}
		}
	}
} else {
	if (isset($_SESSION["admin"]) && isset($_SESSION["admin_password"])) {
		$adm_em = $_SESSION["admin"];
		$adm_pass = $_SESSION["admin_password"];

		$check_admin_empass = mysqli_query($conn_server_db, "SELECT email, password FROM admin WHERE email='$adm_em'");
		if (mysqli_num_rows($check_admin_empass) > 0) {
			while ($admin_details = mysqli_fetch_assoc($check_user_empass)) {
				if (trim($adm_pass) == $admin_details["password"]) {

				} else {
					header("Location: /logout.php");
				}
			}
		} else {
			header("Location: /logout.php");
		}
	} else {
		header("Location: /logout.php");
	}
}

$alter_user_reg_date = "ALTER TABLE " . $user_table_name . " CHANGE reg_date reg_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
if (mysqli_query($conn_server_db, $alter_user_reg_date) == true) {
}

$minimum_ufund = "CREATE TABLE IF NOT EXISTS minimum_user_fund(amount VARCHAR(225) NOT NULL, transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
if (mysqli_query($conn_server_db, $minimum_ufund) == true) {
	if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM minimum_user_fund")) == 0) {
		if (mysqli_query($conn_server_db, "INSERT INTO minimum_user_fund(amount) VALUES ('50')") == true) {
		}
	}
}

$transaction_history = "CREATE TABLE IF NOT EXISTS transaction_history(email VARCHAR(225) NOT NULL, id VARCHAR(60) NOT NULL, amount INT, status VARCHAR(30) NOT NULL, description VARCHAR(225) NOT NULL, transaction_type VARCHAR(30) NOT NULL, website VARCHAR(30) NOT NULL, transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
if (mysqli_query($conn_server_db, $transaction_history) == true) {
}

$transaction_history_table_damount_alter = mysqli_query($conn_server_db, "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'transaction_history' AND COLUMN_NAME = 'd_amount'");
if (mysqli_num_rows($transaction_history_table_damount_alter) == 0) {
	mysqli_query($conn_server_db, "ALTER TABLE transaction_history ADD d_amount VARCHAR(225) AFTER amount");
}

// Check if `w_bef` and `w_aft` columns exist and add if they don't
$transaction_history_table_balba_alter = mysqli_query($conn_server_db, "SHOW COLUMNS FROM transaction_history LIKE 'w_bef'");
if (mysqli_num_rows($transaction_history_table_balba_alter) == 0) {
	mysqli_query($conn_server_db, "ALTER TABLE transaction_history ADD w_bef VARCHAR(225) AFTER d_amount");
	mysqli_query($conn_server_db, "ALTER TABLE transaction_history ADD w_aft VARCHAR(225) AFTER w_bef");
}

$transaction_history_table_meterno_alter = mysqli_query($conn_server_db, "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'transaction_history' AND COLUMN_NAME = 'meter_no'");
if (mysqli_num_rows($transaction_history_table_meterno_alter) == 0) {
	mysqli_query($conn_server_db, "ALTER TABLE transaction_history ADD meter_no VARCHAR(225) AFTER w_aft");
}

$alter_transaction_history_date = "ALTER TABLE transaction_history CHANGE transaction_date transaction_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
if (mysqli_query($conn_server_db, $alter_transaction_history_date) == true) {
}

$recaptcha_setting = "CREATE TABLE IF NOT EXISTS recaptcha_setting(sitekey VARCHAR(225) NOT NULL, secretkey VARCHAR(225) NOT NULL)";
if (mysqli_query($conn_server_db, $recaptcha_setting) == true) {
	if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM recaptcha_setting")) == 0) {
		if (mysqli_query($conn_server_db, "INSERT INTO recaptcha_setting(sitekey,secretkey) VALUES ('sitekey','secretkey')") == true) {
		}
	}
}

$site_info = "CREATE TABLE IF NOT EXISTS site_info (sitetitle VARCHAR(225) NOT NULL)";
if (mysqli_query($conn_server_db, $site_info) == true) {
	if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM site_info")) == 0) {
		if (mysqli_query($conn_server_db, "INSERT INTO site_info (sitetitle) VALUES ('Site Title')") == true) {
		}
	}
}

$blocked_phone_create = "CREATE TABLE IF NOT EXISTS blocked_phone (phone_number VARCHAR(225) NOT NULL)";
if (mysqli_query($conn_server_db, $blocked_phone_create) == true) {
}

$add_user_rechargecard_create = "CREATE TABLE IF NOT EXISTS authorized_rechargecard_user (email VARCHAR(225) NOT NULL)";
if (mysqli_query($conn_server_db, $add_user_rechargecard_create) == true) {
}

$add_user_datacard_create = "CREATE TABLE IF NOT EXISTS authorized_datacard_user (email VARCHAR(225) NOT NULL)";
if (mysqli_query($conn_server_db, $add_user_datacard_create) == true) {
}

$payment_order_history = "CREATE TABLE IF NOT EXISTS payment_order_history(email VARCHAR(225) NOT NULL, id VARCHAR(60) NOT NULL, amount INT, status VARCHAR(30) NOT NULL, transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
if (mysqli_query($conn_server_db, $payment_order_history) == true) {
}

$admin_recharge_card = "CREATE TABLE IF NOT EXISTS admin_recharge_card(company_name VARCHAR(30), network_name VARCHAR(30) NOT NULL, card_100 LONGTEXT, card_200 LONGTEXT, card_500 LONGTEXT, transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
if (mysqli_query($conn_server_db, $admin_recharge_card) == true) {
	if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM admin_recharge_card")) == 0) {
		$insert_recharge_card_rows = "INSERT INTO admin_recharge_card (company_name, network_name) VALUES ('DEMO LTD', 'mtn');";
		$insert_recharge_card_rows .= "INSERT INTO admin_recharge_card (company_name, network_name) VALUES ('DEMO LTD', 'airtel');";
		$insert_recharge_card_rows .= "INSERT INTO admin_recharge_card (company_name, network_name) VALUES ('DEMO LTD', 'glo');";
		$insert_recharge_card_rows .= "INSERT INTO admin_recharge_card (company_name, network_name) VALUES ('DEMO LTD', '9mobile');";
		if (mysqli_multi_query($conn_server_db, $insert_recharge_card_rows) == true) {
		}
	}
}

$recharge_card_history = "CREATE TABLE IF NOT EXISTS recharge_card_history(email VARCHAR(225) NOT NULL, id VARCHAR(60) NOT NULL, network_name VARCHAR(60) NOT NULL, card_quality VARCHAR(60) NOT NULL, card_array LONGTEXT NOT NULL, transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
if (mysqli_query($conn_server_db, $recharge_card_history) == true) {
}

$admin_data_card = "CREATE TABLE IF NOT EXISTS admin_data_card(company_name VARCHAR(30), network_name VARCHAR(30) NOT NULL, card_500mb LONGTEXT, card_1gb LONGTEXT, card_1_5gb LONGTEXT, card_2gb LONGTEXT, card_3gb LONGTEXT, card_5gb LONGTEXT, transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
if (mysqli_query($conn_server_db, $admin_data_card) == true) {
	if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM admin_data_card")) == 0) {
		$insert_data_card_rows = "INSERT INTO admin_data_card (company_name, network_name) VALUES ('DEMO LTD', 'mtn');";
		$insert_data_card_rows .= "INSERT INTO admin_data_card (company_name, network_name) VALUES ('DEMO LTD', 'airtel');";
		$insert_data_card_rows .= "INSERT INTO admin_data_card (company_name, network_name) VALUES ('DEMO LTD', 'glo');";
		$insert_data_card_rows .= "INSERT INTO admin_data_card (company_name, network_name) VALUES ('DEMO LTD', '9mobile');";
		if (mysqli_multi_query($conn_server_db, $insert_data_card_rows) == true) {
		}
	}
}

$data_card_history = "CREATE TABLE IF NOT EXISTS data_card_history(email VARCHAR(225) NOT NULL, id VARCHAR(60) NOT NULL, network_name VARCHAR(60) NOT NULL, data_size VARCHAR(60) NOT NULL, card_quality VARCHAR(60) NOT NULL, card_array LONGTEXT NOT NULL, transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
if (mysqli_query($conn_server_db, $data_card_history) == true) {
}

$collect_user_info = mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, transaction_pin, home_address, wallet_balance, account_type, commission, apikey, account_status, reg_date FROM " . $user_table_name . " WHERE email='$user_session'");
$collect_user_info_active = mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, transaction_pin, home_address, wallet_balance, account_type, commission, apikey, account_status, reg_date FROM " . $user_table_name . " WHERE email='$user_session'");

if (mysqli_num_rows($collect_user_info_active) > 0) {
	while ($user_details_active = mysqli_fetch_assoc($collect_user_info_active)) {
		if ($user_details_active["account_status"] !== "active") {
			header("Location: /logout.php");
		}
	}
}


mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS user_bank (email VARCHAR(225) NOT NULL, account_name VARCHAR(225), account_number VARCHAR(225), bank_name VARCHAR(225), bank_code VARCHAR(30), account_reference VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS user_daily_purchase_counter (email VARCHAR(225) NOT NULL, reference VARCHAR(225), product_type VARCHAR(225) NOT NULL, product_id VARCHAR(225) NOT NULL, purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS admin_daily_minimum_purchase_per_id (minimum_unit VARCHAR(225) NOT NULL, date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS user_message (user_alert LONGTEXT NOT NULL, user_static LONGTEXT NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS users (firstname VARCHAR(30) NOT NULL, lastname VARCHAR(30) NOT NULL, email VARCHAR(225), password VARCHAR(50) NOT NULL, phone_number VARCHAR(20), referral VARCHAR(30), transaction_pin INT, home_address VARCHAR(225) NOT NULL, wallet_balance INT, account_type VARCHAR(30) NOT NULL, commission INT, apikey VARCHAR(65) NOT NULL, account_status VARCHAR(30) NOT NULL, login_attempt INT, reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS sms_sender_id (email VARCHAR(225) NOT NULL, sender_id VARCHAR(60) NOT NULL, status VARCHAR(60) NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS airtime_api (website VARCHAR(225) NOT NULL, apikey VARCHAR(225))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS airtime_network_running_api (network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS airtime_network_status (network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS cable_api (website VARCHAR(225) NOT NULL, apikey VARCHAR(225))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS cable_subscription_running_api (subscription_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS cable_subscription_status (subscription_name VARCHAR(30) NOT NULL, subscription_status VARCHAR(30) NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS direct_data_api (website VARCHAR(225) NOT NULL, apikey VARCHAR(225))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS direct_data_network_running_api (network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS direct_data_network_status (network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS gifting_data_api (website VARCHAR(225) NOT NULL, apikey VARCHAR(225))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS gifting_data_network_running_api (network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS gifting_data_network_status (network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS sme_data_api (website VARCHAR(225) NOT NULL, apikey VARCHAR(225))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS sme_data_network_running_api (network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS sme_data_network_status (network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS electricity_api (website VARCHAR(225) NOT NULL, apikey VARCHAR(225))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS electricity_subscription_running_api (subscription_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS electricity_subscription_status (subscription_name VARCHAR(30) NOT NULL, subscription_status VARCHAR(30) NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS exam_api (website VARCHAR(225) NOT NULL, apikey VARCHAR(225))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS exam_pin_running_api (pin_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS exam_pin_status (pin_name VARCHAR(30) NOT NULL, pin_status VARCHAR(30) NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS insurance_api (website VARCHAR(225) NOT NULL, apikey VARCHAR(225))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS insurance_subscription_running_api (subscription_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS insurance_subscription_status (subscription_name VARCHAR(30) NOT NULL, subscription_status VARCHAR(30) NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS sms_api (website VARCHAR(225) NOT NULL, apikey VARCHAR(225))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS sms_network_running_api (network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, price_1 VARCHAR(30), price_2 VARCHAR(30), price_3 VARCHAR(30), price_4 VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS sms_network_status (network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS datacard_api (website VARCHAR(225) NOT NULL, apikey VARCHAR(225))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS datacard_network_running_api (network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS datacard_network_status (network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS rechargecard_api (website VARCHAR(225) NOT NULL, apikey VARCHAR(225))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS rechargecard_network_running_api (network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))");
mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS rechargecard_network_status (network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)");

//User Daily Product Updater
function userDailyProductUpdater($user_email, $reference, $product_type, $product_id)
{
	global $conn_server_db;
	$user_email = mysqli_real_escape_string($conn_server_db, trim(strip_tags($user_email)));
	$reference = mysqli_real_escape_string($conn_server_db, trim(strip_tags($reference)));
	$product_type = mysqli_real_escape_string($conn_server_db, trim(strip_tags($product_type)));
	$product_id = mysqli_real_escape_string($conn_server_db, trim(strip_tags($product_id)));

	if (!empty($user_email) && !empty($reference) && !empty($product_type) && !empty($product_id)) {
		$select_user = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$user_email'");
		$select_daily_counter_with_ref = mysqli_query($conn_server_db, "SELECT * FROM user_daily_purchase_counter WHERE email='$user_email' && reference='$reference' && product_type='$product_type' && product_id='$product_id' && purchase_date LIKE '%" . date("Y-m-d") . "%'");

		if (mysqli_num_rows($select_user) == 1 && mysqli_num_rows($select_daily_counter_with_ref) == 0) {
			mysqli_query($conn_server_db, "INSERT INTO user_daily_purchase_counter (email, reference, product_type, product_id) VALUES ('$user_email', '$reference', '$product_type', '$product_id')");
			return json_encode(array("status" => true, "message" => "Daily Transaction recorded"));
		} else {
			return json_encode(array("status" => false, "message" => "User not found/Transaction already Exists"));
		}
	} else {
		return json_encode(array("status" => false, "message" => "Empty Fields"));
	}
}

//User Daily Product Counter
function userDailyProductCounter($user_email, $product_type, $product_id)
{
	global $conn_server_db;
	$user_email = mysqli_real_escape_string($conn_server_db, trim(strip_tags($user_email)));
	$product_type = mysqli_real_escape_string($conn_server_db, trim(strip_tags($product_type)));
	$product_id = mysqli_real_escape_string($conn_server_db, trim(strip_tags($product_id)));

	if (!empty($user_email) && !empty($product_type) && !empty($product_id)) {
		$select_user = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$user_email'");
		$select_daily_counter_with_ref = mysqli_query($conn_server_db, "SELECT * FROM user_daily_purchase_counter WHERE email='$user_email' && product_type='$product_type' && product_id='$product_id' && purchase_date LIKE '%" . date("Y-m-d") . "%'");
		$select_admin_daily_minimum = mysqli_query($conn_server_db, "SELECT * FROM admin_daily_minimum_purchase_per_id LIMIT 1");
		if (mysqli_num_rows($select_admin_daily_minimum) == 1) {
			$get_admin_daily_minimum_detail = mysqli_fetch_array($select_admin_daily_minimum);
			if (is_numeric($get_admin_daily_minimum_detail["minimum_unit"]) && $get_admin_daily_minimum_detail["minimum_unit"] >= 0) {
				$daily_transaction_limit = $get_admin_daily_minimum_detail["minimum_unit"];
			} else {
				if ($get_admin_daily_minimum_detail["minimum_unit"] == "null") {
					$daily_transaction_limit = "unlimited";
				} else {
					$daily_transaction_limit = 0;
				}
			}
		} else {
			$daily_transaction_limit = 0;
		}
		if (mysqli_num_rows($select_user) == 1 && mysqli_num_rows($select_daily_counter_with_ref) >= 0 && ((is_numeric($daily_transaction_limit) && mysqli_num_rows($select_daily_counter_with_ref) < $daily_transaction_limit) || $daily_transaction_limit === "unlimited")) {
			return json_encode(array("status" => true, "message" => "Daily Transaction recorded", "counter" => mysqli_num_rows($select_daily_counter_with_ref)));
		} else {
			return json_encode(array("status" => false, "message" => "User not found"));
		}
	} else {
		return json_encode(array("status" => false, "message" => "Empty Fields"));
	}
}


//User Daily Product Remover
function userDailyProductRemover($user_email, $reference)
{
	global $conn_server_db;
	$user_email = mysqli_real_escape_string($conn_server_db, trim(strip_tags($user_email)));
	$reference = mysqli_real_escape_string($conn_server_db, trim(strip_tags($reference)));
	if (!empty($user_email) && !empty($reference)) {
		$select_user = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$user_email'");
		$select_daily_counter_with_ref = mysqli_query($conn_server_db, "SELECT * FROM user_daily_purchase_counter WHERE email='$user_email' && reference='$reference' && purchase_date LIKE '%" . date("Y-m-d") . "%'");

		if (mysqli_num_rows($select_user) == 1 && mysqli_num_rows($select_daily_counter_with_ref) == 1) {
			mysqli_query($conn_server_db, "DELETE FROM user_daily_purchase_counter WHERE email='$user_email' && reference='$reference' && purchase_date LIKE '" . date("Y-m-d") . "'");
			return json_encode(array("status" => true, "message" => "Daily Transaction with Ref: $reference removed"));
		} else {
			return json_encode(array("status" => false, "message" => "User not found/Transaction not Exists"));
		}
	} else {
		return json_encode(array("status" => false, "message" => "Empty Fields"));
	}
}

if (mysqli_num_rows($collect_user_info) > 0) {
	$user_details = mysqli_fetch_assoc($collect_user_info);
	$wallet_total_funding_forceuser = mysqli_query($conn_server_db, "SELECT * FROM transaction_history WHERE email='$user_session' AND (transaction_type='wallet-funding' OR transaction_type='credit' OR transaction_type='refunded' OR transaction_type='commission') ");
	if (mysqli_num_rows($wallet_total_funding_forceuser) > 0) {
		while ($total_funding_forceuser = mysqli_fetch_assoc($wallet_total_funding_forceuser)) {
			$user_total_funding_forceuser += $total_funding_forceuser["amount"];
		}
	}

	$get_mini_user_fund_details = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT amount FROM minimum_user_fund WHERE 1"));
	$amount_forced_on_user = $get_mini_user_fund_details["amount"];
	if (($user_total_funding_forceuser < $amount_forced_on_user) or ($user_total_funding_forceuser == "")) {
		if (!in_array($_SERVER["REQUEST_URI"], array("/dashboard.php", "/fund-wallet.php", "/change-password.php", "/account-setting.php", "/payment-order.php", "/place-payment-order.php"))) {
			echo '<script>alert("Fund yor wallet with atleast N' . $amount_forced_on_user . '")</script>';
			header("refresh:0;url=/fund-wallet.php");
		}
	}

} else {
	header("Location: /logout.php");
}
?>