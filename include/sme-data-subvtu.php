<?php
$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "sme-data", $phone_number), true);
if ($daily_transaction_counter["status"] == true) {
	$check_blocked_number_before_recharge = mysqli_query($conn_server_db, "SELECT * FROM blocked_phone WHERE phone_number='$phone_number'");
	if (mysqli_num_rows($check_blocked_number_before_recharge) === 0) {

		if ($carrier == "mtn") {
			$net_id = 1;
			$smedata_array_prices = array("500mb" => "212", "1gb" => "207", "2gb" => "208", "3gb" => "209", "5gb" => "210", "10gb" => "247");
		}

		/*if($carrier == "airtel"){
			$net_id = 4;
			$smedata_array_prices = array("100mb"=>"239","500mb"=>"232","1gb"=>"233","2gb"=>"234","5gb"=>"235","10gb"=>"150","15gb"=>"","20gb"=>"");
		}*/

		$wallet_balance = $all_user_details["wallet_balance"];
		if ($wallet_balance >= $amount) {

			$dataPurchase = curl_init();
			$dataApiUrl = "https://www.subvtu.com/api/data/";
			curl_setopt($dataPurchase, CURLOPT_URL, $dataApiUrl);
			curl_setopt($dataPurchase, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($dataPurchase, CURLOPT_POST, true);
			$dataPostHeader = array("Authorization: Token " . $apikey, "Content-Type: application/json");
			curl_setopt($dataPurchase, CURLOPT_HTTPHEADER, $dataPostHeader);

			$dataPostFields = json_encode(array("network" => $net_id, "plan" => $smedata_array_prices[$data_qty], "mobile_number" => $phone_number, "Ported_number" => true), true);
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
					$log_data_message = strtoupper($data_qty) . " Sent to " . $phone_number . " Successfully ";
					$checkout_amount = $amount;
					$remain_balance = $wallet_balance - $checkout_amount;
					$ref_id = $dataJSONObj["id"];
					if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
						if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $dataJSONObj["Status"] . "', '" . strtoupper($carrier) . " " . $data_qty . " SME Data for $phone_number @ N$checkout_amount','sme-data', '$site_name')")) {

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