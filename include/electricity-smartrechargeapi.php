<?php

$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "electricity", $meter_no), true);
if ($daily_transaction_counter["status"] == true) {
	$wallet_balance = $all_user_details["wallet_balance"];
	if ($wallet_balance >= $amount) {

		$electricityPurchase = curl_init();
		$electricityApiUrl = "https://smartrechargeapi.com/api/v2/electric/?api_key=" . $apikey . "&product_code=" . $carrier . "_" . $meter_type . "_custom&meter_number=" . $meter_no . "&amount=" . $amount;
		curl_setopt($electricityPurchase, CURLOPT_URL, $electricityApiUrl);
		curl_setopt($electricityPurchase, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($electricityPurchase, CURLOPT_HTTPGET, 1);
		curl_setopt($electricityPurchase, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($electricityPurchase, CURLOPT_SSL_VERIFYPEER, false);

		$GetelectricityJSON = curl_exec($electricityPurchase);
		$electricityJSONObj = json_decode($GetelectricityJSON, true);

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

		if ($GetelectricityJSON == true) {

			if (in_array($electricityJSONObj["error_code"], array(1986, 1981))) {
				$log_electricity_message = $electricityJSONObj["server_message"];
				$checkout_amount = $amount - ($amount * $electricity_discount / 100);
				$remain_balance = $wallet_balance - $checkout_amount;
				$ref_id = $electricityJSONObj["data"]["recharge_id"];
				$customer_detail = "Name: " . $electricityJSONObj["data"]["customer_name"] . " Address: " . $electricityJSONObj["data"]["customer_address"] . " Reference: " . $electricityJSONObj["data"]["reference"] . " UNITS: " . $electricityJSONObj["data"]["units"] . " Token: " . $electricityJSONObj["data"]["token"];
				userDailyProductUpdater($user_session, $ref_id, "electricity", $meter_no);
				if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
					if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, meter_no, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $meter_no . "', '" . $electricityJSONObj["data"]["text_status"] . "', '" . strtoupper($carrier) . " " . $amount . " Electricity for $customer_detail Meter No: $meter_no @ N$checkout_amount', 'electricity', '$site_name')")) {

					}
				}
			}

			if (!in_array($electricityJSONObj["error_code"], array(1986, 1981))) {
				$log_electricity_message = "Error: " . $electricityJSONObj["server_message"];
			}

		} else {
			$log_electricity_message = "Server currently unavailable";
		}
	} else {
		$log_electricity_message = "Insufficient Funds";
	}
} else {
	$log_electricity_message = "Error: Daily Limit for electricty reached for " . $meter_no;
}
?>