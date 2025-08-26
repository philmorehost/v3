<?php
$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "gifting-data", $phone_number), true);
if ($daily_transaction_counter["status"] == true) {
	$check_blocked_number_before_recharge = mysqli_query($conn_server_db, "SELECT * FROM blocked_phone WHERE phone_number='$phone_number'");
	if (mysqli_num_rows($check_blocked_number_before_recharge) === 0) {
		if ($carrier == "mtn") {
			$net_id = 1;
			$data_gifting_array_prices = array("500mb" => "42", "1gb" => "46", "2gb" => "47", "3gb" => "48", "5gb" => "49", "10gb" => "50");
		}

		if ($carrier == "airtel") {
			$net_id = 2;
			$data_gifting_array_prices = array("500mb" => "51", "1gb" => "52", "2gb" => "53", "5gb" => "54", "10gb" => "55");
		}

		if ($carrier == "glo") {
			$net_id = 3;
			$data_gifting_array_prices = array("500mb" => "71", "1gb" => "72", "2gb" => "73", "3gb" => "74", "5gb" => "75", "10gb" => "76");
		}

		if ($carrier == "9mobile") {
			$net_id = 4;
			$data_gifting_array_prices = array("500mb" => "85", "1gb" => "86", "2gb" => "87", "3gb" => "88", "5gb" => "90", "10gb" => "91");
		}

		if (!empty($data_gifting_array_prices[$data_qty])) {
			$wallet_balance = $all_user_details["wallet_balance"];
			if ($wallet_balance >= $amount) {
				$raw_number = "123456789012345678901234567890";
				$reference = substr(str_shuffle($raw_number), 0, 15);

				$bilalAccessToken = curl_init();
				$dataApiUrl = "https://legitdataway.com/api/user";
				curl_setopt($bilalAccessToken, CURLOPT_URL, $dataApiUrl);
				curl_setopt($bilalAccessToken, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($bilalAccessToken, CURLOPT_POST, true);
				$bilalTokenPostHeader = array("Authorization: Basic " . base64_encode($apikey));
				curl_setopt($bilalAccessToken, CURLOPT_HTTPHEADER, $bilalTokenPostHeader);

				curl_setopt($bilalAccessToken, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($bilalAccessToken, CURLOPT_SSL_VERIFYPEER, false);

				$GetBilalJSON = curl_exec($bilalAccessToken);
				$bilalJSONObj = json_decode($GetBilalJSON, true);

				if (($GetBilalJSON == true) && ($bilalJSONObj["status"] == "success")) {
					$dataPurchase = curl_init();
					$dataApiUrl = "https://legitdataway.com/api/data/";
					curl_setopt($dataPurchase, CURLOPT_URL, $dataApiUrl);
					curl_setopt($dataPurchase, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($dataPurchase, CURLOPT_POST, true);
					$dataPostHeader = array("Authorization: Token " . $bilalJSONObj["AccessToken"], "Content-Type: application/json");
					curl_setopt($dataPurchase, CURLOPT_HTTPHEADER, $dataPostHeader);

					$dataPostFields = json_encode(array("network" => $net_id, "data_plan" => $data_gifting_array_prices[$data_qty], "phone" => $phone_number, "bypass" => true, "request-id" => $reference), true);
					curl_setopt($dataPurchase, CURLOPT_POSTFIELDS, $dataPostFields);

					curl_setopt($dataPurchase, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($dataPurchase, CURLOPT_SSL_VERIFYPEER, false);

					$GetdataJSON = curl_exec($dataPurchase);
					$dataJSONObj = json_decode($GetdataJSON, true);

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
				}

				if ($GetdataJSON == true) {
					if (in_array($dataJSONObj["status"], array("success"))) {
						$log_gifting_data_message = strtoupper($data_qty) . " Sent to " . $phone_number . " Successfully ";
						$checkout_amount = $amount;
						$remain_balance = $wallet_balance - $checkout_amount;
						$ref_id = $dataJSONObj["request-id"];
						if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
							if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $dataJSONObj["status"] . "', '" . strtoupper($carrier) . " " . $data_qty . " Gifting Data for $phone_number @ N$checkout_amount','gifting-data', '$site_name')")) {

							}
						}
					}

					if (!in_array($dataJSONObj["status"], array("success"))) {
						$log_gifting_data_message = "Error: " . $dataJSONObj["status"];
					}

				} else {
					$log_gifting_data_message = "Server currently unavailable";
				}
			} else {
				$log_gifting_data_message = "Insufficient Funds";
			}
		} else {
			$log_gifting_data_message = "Sorry, This is Not Available";
		}
	} else {
		$log_gifting_data_message = "Error: Phone Number has been Blocked, Contact The Admin to Resolve it!";
	}
} else {
	$log_gifting_data_message = "Error: Daily Limit for gifting data reached for " . $phone_number;
}
?>