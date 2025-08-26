<?php

$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "cable", $cable_iuc_no), true);
if ($daily_transaction_counter["status"] == true) {
	$wallet_balance = $all_user_details["wallet_balance"];
	if ($wallet_balance >= $amount) {
		if ($carrier == "startimes") {
			$cable_type = "startimes_" . $package_name;
		}
		if ($carrier == "dstv") {
			$cable_type = "dstv_" . $package_name;
		}
		if ($carrier == "gotv") {
			$cable_type = "gotv_" . $package_name;
		}

		$cablePurchase = curl_init();
		$cableApiUrl = "https://mobileone.ng/api/v2/tv/?api_key=" . $apikey . "&product_code=" . $cable_type . "&smartcard_number=" . $cable_iuc_no;
		curl_setopt($cablePurchase, CURLOPT_URL, $cableApiUrl);
		curl_setopt($cablePurchase, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cablePurchase, CURLOPT_HTTPGET, 1);
		curl_setopt($cablePurchase, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($cablePurchase, CURLOPT_SSL_VERIFYPEER, false);

		$GetcableJSON = curl_exec($cablePurchase);
		$cableJSONObj = json_decode($GetcableJSON, true);

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

		if ($GetcableJSON == true) {

			if (in_array($cableJSONObj["error_code"], array(1986, 1981))) {
				$log_cable_message = $cableJSONObj["server_message"];
				$checkout_amount = $amount;
				$remain_balance = $wallet_balance - $checkout_amount;
				$ref_id = $cableJSONObj["data"]["recharge_id"];
				userDailyProductUpdater($user_session, $ref_id, "cable", $cable_iuc_no);
				if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
					if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $cableJSONObj["data"]["text_status"] . "', '" . ucwords($carrier) . " " . ucwords($package_name) . " Subscription to IUC No: " . $cable_iuc_no . " @ N$checkout_amount', 'cable', '$site_name')")) {

					}
				}
			}

			if (!in_array($cableJSONObj["error_code"], array(1986, 1981))) {
				$log_cable_message = "Error: " . $cableJSONObj["server_message"];
			}

		} else {
			$log_cable_message = "Server currently unavailable";
		}
	} else {
		$log_cable_message = "Insufficient Funds";
	}
} else {
	$log_cable_message = "Error: Daily Limit for cable reached for " . $cable_iuc_no;
}
?>