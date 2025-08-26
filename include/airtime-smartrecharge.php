<?php

$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "airtime", $phone_number), true);
if ($daily_transaction_counter["status"] == true) {
	$check_blocked_number_before_recharge = mysqli_query($conn_server_db, "SELECT * FROM blocked_phone WHERE phone_number='$phone_number'");
	if (mysqli_num_rows($check_blocked_number_before_recharge) === 0) {
		$wallet_balance = $all_user_details["wallet_balance"];
		if ($wallet_balance >= $amount) {
			if ($carrier == "mtn") {
				$carrier_id = "mtn_custom";
				$running_api_network = $get_mtn_airtime_running_api;
			}
			if ($carrier == "airtel") {
				$carrier_id = "airtel_custom";
				$running_api_network = $get_airtel_airtime_running_api;
			}
			if ($carrier == "glo") {
				$carrier_id = "glo_custom";
				$running_api_network = $get_glo_airtime_running_api;
			}
			if ($carrier == "9mobile") {
				$carrier_id = "9mobile_custom";
				$running_api_network = $get_9mobile_airtime_running_api;
			}

			$airtimePurchase = curl_init();
			$airtimeApiUrl = "https://smartrecharge.ng/api/v2/airtime/?api_key=" . $apikey . "&product_code=" . $carrier_id . "&phone=" . $phone_number . "&amount=" . $amount;
			curl_setopt($airtimePurchase, CURLOPT_URL, $airtimeApiUrl);
			curl_setopt($airtimePurchase, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($airtimePurchase, CURLOPT_HTTPGET, 1);
			curl_setopt($airtimePurchase, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($airtimePurchase, CURLOPT_SSL_VERIFYPEER, false);

			$GetAirtimeJSON = curl_exec($airtimePurchase);
			$AirtimeJSONObj = json_decode($GetAirtimeJSON, true);

			/*1987 Verification Successful
				  1986 Transaction Successful
				  1985 This user does not exists or is not activated for API ACCESS
				  1984 User does not exist
				  1983 Insufficient Credit, Please fund your account and try again
				  1982 Transaction Failed
				  1981 Transaction Pending
				  1980 VERIFICATION FAILED, PLEASE TRY AGAIN OR CALL ADMIN
				  1979 SOME PARAMETERS ARE MISSING
				  1978 SERVICE NOT AVAILABLE AT THE MOMENT
				  1977 ORDER IS FRAUDULENT*/

			if ($GetAirtimeJSON == true) {

				if (in_array($AirtimeJSONObj["error_code"], array(1986, 1981))) {
					$log_airtime_message = $AirtimeJSONObj["server_message"];
					$checkout_amount = $amount - ($amount * floatval($airtime_discount) / 100);
					$remain_balance = $wallet_balance - $checkout_amount;
					$ref_id = $AirtimeJSONObj["data"]["recharge_id"];
					userDailyProductUpdater($user_session, $ref_id, "airtime", $phone_number);
					if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
						if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $AirtimeJSONObj["data"]["text_status"] . "', '" . strtoupper($carrier) . " Airtime for $phone_number @ N$checkout_amount', 'airtime', '$site_name')")) {

						}
					}
				}

				if (!in_array($AirtimeJSONObj["error_code"], array(1986, 1981))) {
					$log_airtime_message = "Error: " . $AirtimeJSONObj["server_message"];
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