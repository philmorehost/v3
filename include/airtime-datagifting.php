<?php

$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "airtime", $phone_number), true);
if ($daily_transaction_counter["status"] == true) {
	$check_blocked_number_before_recharge = mysqli_query($conn_server_db, "SELECT * FROM blocked_phone WHERE phone_number='$phone_number'");
	if (mysqli_num_rows($check_blocked_number_before_recharge) === 0) {
		$wallet_balance = $all_user_details["wallet_balance"];
		if ($wallet_balance >= $amount) {
			if ($carrier == "mtn") {
				$carrier_id = "mtn";
				$running_api_network = $get_mtn_airtime_running_api;
			}
			if ($carrier == "airtel") {
				$carrier_id = "airtel";
				$running_api_network = $get_airtel_airtime_running_api;
			}
			if ($carrier == "glo") {
				$carrier_id = "glo";
				$running_api_network = $get_glo_airtime_running_api;
			}
			if ($carrier == "9mobile") {
				$carrier_id = "9mobile";
				$running_api_network = $get_9mobile_airtime_running_api;
			}

			$airtimePurchase = curl_init();
			$airtimeApiUrl = "https://v5.datagifting.com.ng/web/api/airtime.php";
			curl_setopt($airtimePurchase, CURLOPT_URL, $airtimeApiUrl);
			curl_setopt($airtimePurchase, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($airtimePurchase, CURLOPT_POST, 1);
			$pay_loads = json_encode(array(
				"api_key" => $apikey,
				"network" => $carrier_id,
				"phone_number" => $phone_number,
				"amount" => $amount
			));
			curl_setopt($airtimePurchase, CURLOPT_POSTFIELDS, $pay_loads);
			curl_setopt($airtimePurchase, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($airtimePurchase, CURLOPT_SSL_VERIFYPEER, false);

			$GetAirtimeJSON = curl_exec($airtimePurchase);
			$AirtimeJSONObj = json_decode($GetAirtimeJSON, true);

			if ($GetAirtimeJSON == true) {

				if (in_array($AirtimeJSONObj["status"], array("success", "pending"))) {
					$log_airtime_message = $AirtimeJSONObj["desc"];
					$checkout_amount = $amount - ($amount * floatval($airtime_discount) / 100);
					$remain_balance = $wallet_balance - $checkout_amount;
					$ref_id = $AirtimeJSONObj["ref"];
					userDailyProductUpdater($user_session, $ref_id, "airtime", $phone_number);
					if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
						if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $AirtimeJSONObj["status"] . "', '" . strtoupper($carrier) . " Airtime for $phone_number @ N$checkout_amount', 'airtime', '$site_name')")) {

						}
					}
				}

				if (!in_array($AirtimeJSONObj["status"], array("success", "pending"))) {
					$log_airtime_message = "Error: " . $AirtimeJSONObj["desc"];
				}

			} else {
				$log_airtime_message = "Server currently unavailable";
			}
		} else {
			$log_airtime_message = "Insufficient Funds";
		}
	} else {
		$log_airtime_message = "Error: Phone Number has been Blocked, Contact The Admin to Resolve it!";
	}
} else {
	$log_airtime_message = "Error: Daily Limit for airtime reached for " . $phone_number;
}
?>