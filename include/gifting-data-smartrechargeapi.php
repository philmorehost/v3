<?php
$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "gifting-data", $phone_number), true);
if ($daily_transaction_counter["status"] == true) {
	$check_blocked_number_before_recharge = mysqli_query($conn_server_db, "SELECT * FROM blocked_phone WHERE phone_number='$phone_number'");
	if (mysqli_num_rows($check_blocked_number_before_recharge) === 0) {
		if ($carrier == "mtn") {
			$data_qty_code = "mtn_corporate_data_" . $data_qty;
		}

		/*if($carrier == "airtel"){
			if(in_array($data_qty,array("100mb"))){
				$data_code_with_days = $data_qty."_7days";
			}
			
			if(in_array($data_qty,array("500mb","1gb","2gb","5gb","10gb","15gb","20gb"))){
				$data_code_with_days = $data_qty."_30days";
			}
			$data_qty_code = "data_share_airtel_".$data_code_with_days;
		}*/

		if ($carrier == "glo") {
			if (in_array($data_qty, array("200mb", "500mb", "1gb", "2gb", "3gb", "5gb", "10gb"))) {
				$data_qty_code = "glo_cg_" . $data_qty;
			}
		}


		$wallet_balance = $all_user_details["wallet_balance"];
		if ($wallet_balance >= $amount) {
			$dataPurchase = curl_init();
			$dataApiUrl = "https://smartrechargeapi.com/api/v2/datashare/?api_key=" . $apikey . "&product_code=" . $data_qty_code . "&phone=" . $phone_number;
			curl_setopt($dataPurchase, CURLOPT_URL, $dataApiUrl);
			curl_setopt($dataPurchase, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($dataPurchase, CURLOPT_HTTPGET, 1);
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
				if (in_array($dataJSONObj["error_code"], array(1986, 1981))) {
					$log_gifting_data_message = $dataJSONObj["data"]["server_message"];
					$checkout_amount = $amount;
					$remain_balance = $wallet_balance - $checkout_amount;
					$ref_id = $dataJSONObj["data"]["recharge_id"];
					if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
						if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $dataJSONObj["data"]["text_status"] . "', '" . strtoupper($carrier) . " " . $data_qty . " Gifting Data for $phone_number @ N$checkout_amount','gifting-data', '$site_name')")) {

						}
					}
				}

				if (!in_array($dataJSONObj["error_code"], array(1986, 1981))) {
					$log_gifting_data_message = "Error: " . $dataJSONObj["server_message"];
				}

			} else {
				$log_gifting_data_message = "Server currently unavailable";
			}
		} else {
			$log_gifting_data_message = "Insufficient Funds";
		}
	} else {
		$log_gifting_data_message = "Error: Phone Number has been Blocked, Contact The Admin to Resolve it!";
	}
} else {
	$log_gifting_data_message = "Error: Daily Limit for gifting data reached for " . $phone_number;
}
?>