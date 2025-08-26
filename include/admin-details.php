<?php

$user_table_name = "users";
$admin_table_name = "admin";
$admin_session = $_SESSION["admin"];

if ($conn_server_db == true) {
	$user_db_table = "CREATE TABLE IF NOT EXISTS " . $user_table_name . "(firstname VARCHAR(30) NOT NULL, lastname VARCHAR(30) NOT NULL, email VARCHAR(225), password VARCHAR(50) NOT NULL, phone_number VARCHAR(20), referral VARCHAR(30), transaction_pin INT, home_address VARCHAR(225) NOT NULL, wallet_balance INT, account_type VARCHAR(30) NOT NULL, commission INT, apikey VARCHAR(65) NOT NULL, account_status VARCHAR(30) NOT NULL, login_attempt INT, reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
	if (mysqli_query($conn_server_db, $user_db_table) == true) {
	}

	$alter_user_reg_date = "ALTER TABLE " . $user_table_name . " CHANGE reg_date reg_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
	if (mysqli_query($conn_server_db, $alter_user_reg_date) == true) {
	}

	$admin_db_table = "CREATE TABLE IF NOT EXISTS " . $admin_table_name . " (fullname VARCHAR(30) NOT NULL, email VARCHAR(225), password VARCHAR(50) NOT NULL, phone_number VARCHAR(20), home_address VARCHAR(225) NOT NULL, transaction_pin INT NOT NULL, status VARCHAR(30) NOT NULL, reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
	if (mysqli_query($conn_server_db, $admin_db_table) == true) {
		$select_admin_db_table = mysqli_query($conn_server_db, "SELECT * FROM  " . $admin_table_name);
		if (mysqli_num_rows($select_admin_db_table) == 0) {
			mysqli_query(
				$conn_server_db,
				"INSERT INTO " . $admin_table_name . " (fullname, email, phone_number, `password`, home_address, transaction_pin, `status`) VALUES ('VTU Admin', 'example@gmail.com', '08124232128', '" . md5('123456') . "', 'Ilorin', '1234', '1')"
			);
		}
	}

	// Check if `bvn` column exists and add if it doesn't
	$admin_table_bvn_alter = mysqli_query($conn_server_db, "SHOW COLUMNS FROM `admin` LIKE 'bvn'");
	if (mysqli_num_rows($admin_table_bvn_alter) == 0) {
		mysqli_query($conn_server_db, "ALTER TABLE `admin` ADD bvn VARCHAR(225) AFTER phone_number");
	}

	// Check if `nin` column exists and add if it doesn't
	$admin_table_nin_alter = mysqli_query($conn_server_db, "SHOW COLUMNS FROM `admin` LIKE 'nin'");
	if (mysqli_num_rows($admin_table_nin_alter) == 0) {
		mysqli_query($conn_server_db, "ALTER TABLE `admin` ADD nin VARCHAR(225) AFTER bvn");
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

	// Check if `d_amount` column exists and add if it doesn't
	$transaction_history_table_damount_alter = mysqli_query($conn_server_db, "SHOW COLUMNS FROM transaction_history LIKE 'd_amount'");
	if (mysqli_num_rows($transaction_history_table_damount_alter) == 0) {
		mysqli_query($conn_server_db, "ALTER TABLE transaction_history ADD d_amount VARCHAR(225) AFTER amount");
	}

	// Check if `w_bef` and `w_aft` columns exist and add if they don't
	$transaction_history_table_balba_alter = mysqli_query($conn_server_db, "SHOW COLUMNS FROM transaction_history LIKE 'w_bef'");
	if (mysqli_num_rows($transaction_history_table_balba_alter) == 0) {
		mysqli_query($conn_server_db, "ALTER TABLE transaction_history ADD w_bef VARCHAR(225) AFTER d_amount");
		mysqli_query($conn_server_db, "ALTER TABLE transaction_history ADD w_aft VARCHAR(225) AFTER w_bef");
	}

	// Check if `meter_no` column exists and add if it doesn't
	$transaction_history_table_meterno_alter = mysqli_query($conn_server_db, "SHOW COLUMNS FROM transaction_history LIKE 'meter_no'");
	if (mysqli_num_rows($transaction_history_table_meterno_alter) == 0) {
		mysqli_query($conn_server_db, "ALTER TABLE transaction_history ADD meter_no VARCHAR(225) AFTER w_aft");
	}

	// Update `transaction_date` column to ensure it's TIMESTAMP with default CURRENT_TIMESTAMP
	$alter_transaction_history_date = "ALTER TABLE transaction_history CHANGE transaction_date transaction_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
	if (mysqli_query($conn_server_db, $alter_transaction_history_date) == true) {
		// Successfully altered `transaction_date`
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

	$admin_db_table = "CREATE TABLE IF NOT EXISTS " . $admin_table_name . "(fullname VARCHAR(30) NOT NULL, email VARCHAR(225) NOT NULL, password VARCHAR(50) NOT NULL, phone_number VARCHAR(20), transaction_pin INT, home_address VARCHAR(225), reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
	if (mysqli_query($conn_server_db, $admin_db_table) == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $admin_table_name)) == 0) {
			$def_password = md5("123456");
			if (mysqli_query($conn_server_db, "INSERT INTO " . $admin_table_name . " (fullname, email, password, phone_number, transaction_pin, home_address) VALUES ('Admin Name','example@gmail.com','$def_password','08124232128','1234','')") == true) {

			}
		}
	}

	// $package_db_table = "CREATE TABLE IF NOT EXISTS " . $package_table_name . " (account_level VARCHAR(30), price VARCHAR(30))";
	// if (mysqli_query($conn_server_db, $package_db_table) == true) {
	// }

	$admin_bank_details = "CREATE TABLE IF NOT EXISTS admin_bank_details(acct_name VARCHAR(225) NOT NULL, acct_number VARCHAR(225) NOT NULL, bank_name VARCHAR(225) NOT NULL, last_update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
	if (mysqli_query($conn_server_db, $admin_bank_details) == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM admin_bank_details")) == 0) {
			if (mysqli_query($conn_server_db, "INSERT INTO admin_bank_details (acct_name, acct_number, bank_name) VALUES ('Account Name', '2656377262', 'UBA Bank')") == true) {
			}
		}
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

	$recharge_card_history = "CREATE TABLE IF NOT EXISTS recharge_card_history (email VARCHAR(225) NOT NULL, id VARCHAR(60) NOT NULL, network_name VARCHAR(60) NOT NULL, card_quality VARCHAR(60) NOT NULL, card_array LONGTEXT NOT NULL, transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
	if (mysqli_query($conn_server_db, $recharge_card_history) == true) {
	}

	$admin_data_card = "CREATE TABLE IF NOT EXISTS admin_data_card (company_name VARCHAR(30), network_name VARCHAR(30) NOT NULL, card_500mb LONGTEXT, card_1gb LONGTEXT, card_1_5gb LONGTEXT, card_2gb LONGTEXT, card_3gb LONGTEXT, card_5gb LONGTEXT, transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
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
}

if (isset($_SESSION["admin"])) {

	$collect_admin_info = mysqli_query($conn_server_db, "SELECT fullname, email, password, phone_number, transaction_pin, home_address, reg_date FROM " . $admin_table_name . " WHERE email='$admin_session'");

	if (mysqli_num_rows($collect_admin_info) > 0) {
		$admin_details = mysqli_fetch_assoc($collect_admin_info);
	} else {
		header("Location: /admin/logout.php");
	}

}

?>