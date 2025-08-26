<?php
$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "sme-data", $phone_number), true);
if ($daily_transaction_counter["status"] == true) {
	$check_blocked_number_before_recharge = mysqli_query($conn_server_db, "SELECT * FROM blocked_phone WHERE phone_number='$phone_number'");
	if (mysqli_num_rows($check_blocked_number_before_recharge) === 0) {
		if ($carrier == "mtn") {
			$net_id = 1;
			$smedata_array_prices = array("500mb" => "566", "1gb" => "567", "2gb" => "568", "3gb" => "569", "5gb" => "330", "10gb" => "");
		}

		if ($carrier == "airtel") {
			$net_id = 4;
			$smedata_array_prices = array();
		}

		if ($carrier == "9mobile") {
			$net_id = 3;
			$smedata_array_prices = array();
		}

		$wallet_balance = $all_user_details["wallet_balance"];
		if ($wallet_balance >= $amount) {
			$dataPurchase = curl_init();
			$dataApiUrl = "https://alrahuzdata.com.ng/api/data/";
			curl_setopt($dataPurchase, CURLOPT_URL, $dataApiUrl);
			curl_setopt($dataPurchase, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($dataPurchase, CURLOPT_POST, 1);
			$alrahuzdataTokenPostHeader = array("Authorization: Token " . $apikey, "Content-Type: application/json");
			curl_setopt($dataPurchase, CURLOPT_HTTPHEADER, $alrahuzdataTokenPostHeader);
			$pay_loads = json_encode(array(
				"network" => $net_id,
				"mobile_number" => $phone_number,
				"Ported_number" => true,
				"plan" => $smedata_array_prices[$data_qty]
			));
			curl_setopt($dataPurchase, CURLOPT_POSTFIELDS, $pay_loads);
			curl_setopt($dataPurchase, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($dataPurchase, CURLOPT_SSL_VERIFYPEER, false);

			$GetdataJSON = curl_exec($dataPurchase);
			$dataJSONObj = json_decode($GetdataJSON, true);

			//Debugger
			// fwrite(fopen("sme-alrahuzdata.txt", "a"), $GetdataJSON." ".$smedata_array_prices[$data_qty]." ".$data_qty."\n\n");

			if ($GetdataJSON == true) {

				if (in_array($dataJSONObj["Status"], array("successful", "pending"))) {
					$log_data_message = strtoupper($data_qty) . " Sent to " . $phone_number . " Successfully ";
					$checkout_amount = $amount;
					$remain_balance = $wallet_balance - $checkout_amount;
					$ref_id = $dataJSONObj["id"];
					if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
						if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $dataJSONObj["status"] . "', '" . strtoupper($carrier) . " " . str_replace("_", " ", $data_qty) . " SME Data for $phone_number @ N$checkout_amount','sme-data', '$site_name')")) {

						}
					}
				}

				if ($dataJSONObj["error"][0] == true) {
					$log_data_message = "Error: " . $dataJSONObj["error"][0];
				}

			} else {
				$log_data_message = "Server currently unavailable";
			}
		} else {
			$log_data_message = "Insufficient Funds";
		}
	} else {
		$log_data_message = "Error: Phone Number has been Blocked, Contact The Admin to Resolve it!";
	}
} else {
	$log_data_message = "Error: Daily Limit for sme data reached for " . $phone_number;
}
?>