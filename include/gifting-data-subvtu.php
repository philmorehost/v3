<?php
$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "gifting-data", $phone_number), true);
if ($daily_transaction_counter["status"] == true) {
	$check_blocked_number_before_recharge = mysqli_query($conn_server_db, "SELECT * FROM blocked_phone WHERE phone_number='$phone_number'");
	if (mysqli_num_rows($check_blocked_number_before_recharge) === 0) {
		if ($carrier == "mtn") {
			$net_id = 1;
			$data_gifting_array_prices = array("500mb" => "208", "1gb" => "248", "2gb" => "250", "3gb" => "266", "5gb" => "265", "10gb" => "211", "15gb" => "263", "20gb" => "261");
		}

		if ($carrier == "airtel") {
			$net_id = 4;
			$data_gifting_array_prices = array("300mb" => "290", "500mb" => "246", "1gb" => "213", "2gb" => "214", "5gb" => "215", "10gb" => "291");
		}

		if ($carrier == "glo") {
			$net_id = 2;
			$data_gifting_array_prices = array("500mb" => "294", "1.0gb" => "295", "2.0gb" => "296", "3.0gb" => "297", "5gb" => "298", "10gb" => "299");
		}

		if ($carrier == "9mobile") {
			$net_id = 3;
			$data_gifting_array_prices = array("500mb" => "308", "1gb" => "309", "2gb" => "311", "3gb" => "312", "5gb" => "314", "10gb" => "315", "15gb" => "317", "20gb" => "318", "40gb" => "321", "50gb" => "322", "75gb" => "323", "100gb" => "324");
		}

		if (!empty($data_gifting_array_prices[$data_qty])) {
			$wallet_balance = $all_user_details["wallet_balance"];
			if ($wallet_balance >= $amount) {
				$dataPurchase = curl_init();
				$dataApiUrl = "https://www.subvtu.com/api/data/";
				curl_setopt($dataPurchase, CURLOPT_URL, $dataApiUrl);
				curl_setopt($dataPurchase, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($dataPurchase, CURLOPT_POST, true);
				$dataPostHeader = array("Authorization: Token " . $apikey, "Content-Type: application/json");
				curl_setopt($dataPurchase, CURLOPT_HTTPHEADER, $dataPostHeader);

				$dataPostFields = json_encode(array("network" => $net_id, "plan" => $data_gifting_array_prices[$data_qty], "mobile_number" => $phone_number, "Ported_number" => true), true);
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

				if ($GetdataJSON == true) {
					if (in_array($dataJSONObj["Status"], array("successful", "pending"))) {
						$log_gifting_data_message = strtoupper($data_qty) . " Sent to " . $phone_number . " Successfully ";
						$checkout_amount = $amount;
						$remain_balance = $wallet_balance - $checkout_amount;
						$ref_id = $dataJSONObj["id"];
						if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
							if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $dataJSONObj["Status"] . "', '" . strtoupper($carrier) . " " . $data_qty . " Gifting Data for $phone_number @ N$checkout_amount','gifting-data', '$site_name')")) {

							}
						}
					}

					if ($dataJSONObj["error"][0] == true) {
						$log_gifting_data_message = "Error: " . $dataJSONObj["error"][0];
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