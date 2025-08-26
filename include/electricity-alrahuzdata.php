<?php

$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "electricity", $meter_no), true);
if ($daily_transaction_counter["status"] == true) {
	$wallet_balance = $all_user_details["wallet_balance"];
	if ($wallet_balance >= $amount) {

		$disco_type_array = array("prepaid" => "1", "postpaid" => "2");
		$disco_name_array = array("ekedc" => "2", "eedc" => "5", "ikedc" => "1", "jedc" => "9", "kano" => "4", "ibedc" => "7", "phed" => "6", "aedc" => "3");
		$electricityPurchase = curl_init();
		$electricityApiUrl = "https://alrahuzdata.com.ng/api/billpayment/";
		curl_setopt($electricityPurchase, CURLOPT_URL, $electricityApiUrl);
		curl_setopt($electricityPurchase, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($electricityPurchase, CURLOPT_POST, 1);
		$alrahuzdataTokenPostHeader = array("Authorization: Token " . $apikey, "Content-Type: application/json");
		curl_setopt($electricityPurchase, CURLOPT_HTTPHEADER, $alrahuzdataTokenPostHeader);
		$pay_loads = json_encode(array(
			"MeterType" => $disco_type_array[$meter_type],
			"meter_number" => $meter_no,
			"disco_name" => $disco_name_array[$carrier],
			"amount" => $amount
		));
		curl_setopt($electricityPurchase, CURLOPT_POSTFIELDS, $pay_loads);
		curl_setopt($electricityPurchase, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($electricityPurchase, CURLOPT_SSL_VERIFYPEER, false);

		$GetelectricityJSON = curl_exec($electricityPurchase);
		$electricityJSONObj = json_decode($GetelectricityJSON, true);

		//Debugger
			fwrite(fopen("electricity-alrahuzdata.txt", "a"), $GetdataJSON."\n\n");

		if ($GetelectricityJSON == true) {

			if (in_array($electricityJSONObj["status"], array("success", "pending"))) {
				$log_electricity_message = $electricityJSONObj["desc"];
				$checkout_amount = $amount - ($amount * floatval($electricity_discount) / 100);
				$remain_balance = $wallet_balance - $checkout_amount;
				$ref_id = $electricityJSONObj["ref"];
				$customer_detail = $electricityJSONObj["response_desc"];
				userDailyProductUpdater($user_session, $ref_id, "electricity", $meter_no);
				if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
					if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, meter_no, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $meter_no . "', '" . $electricityJSONObj["status"] . "', '" . strtoupper($carrier) . " " . $amount . " Electricity for Meter No: $meter_no @ N$checkout_amount | $customer_detail', 'electricity', '$site_name')")) {

					}
				}
			}

			if (!in_array($electricityJSONObj["status"], haystack: array("success", "pending"))) {
				$log_electricity_message = "Error: " . $electricityJSONObj["desc"];
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