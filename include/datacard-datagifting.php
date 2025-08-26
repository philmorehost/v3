<?php
$raw_number = "123456789012345678901234567890";
$reference = substr(str_shuffle($raw_number), 0, 15);
if ($carrier == "mtn") {
	$all_data_card_qtyprice_array = array("1gb" => "250", "1.5gb" => "320", "2gb" => "500");
}

if ($carrier == "airtel") {
	$all_data_card_qtyprice_array = array();
}

if ($carrier == "glo") {
	$all_data_card_qtyprice_array = array();
}

if ($carrier == "9mobile") {
	$all_data_card_qtyprice_array = array();
}

if (!empty($all_data_card_qtyprice_array[$data_size])) {
	$check_authorized_user_before_recharge = mysqli_query($conn_server_db, "SELECT * FROM authorized_datacard_user WHERE email='$user_session'");
	if (mysqli_num_rows($check_authorized_user_before_recharge) >= 1) {
		$wallet_balance = $all_user_details["wallet_balance"];
		if ($wallet_balance >= $discounted_price_amount) {

			$datacardPurchase = curl_init();
			$datacardApiUrl = "https://v5.datagifting.com.ng/web/api/card.php";
			curl_setopt($datacardPurchase, CURLOPT_URL, $datacardApiUrl);
			curl_setopt($datacardPurchase, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($datacardPurchase, CURLOPT_POST, true);
			$pay_loads = json_encode(array(
				"api_key" => $apikey,
				"network" => $carrier,
				"qty_number" => $qty,
				"type" => "datacard",
				"quantity" => $data_size,
				"card_name" => $site_name
			));
			curl_setopt($datacardPurchase, CURLOPT_POSTFIELDS, $pay_loads);
			curl_setopt($datacardPurchase, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($datacardPurchase, CURLOPT_SSL_VERIFYPEER, false);

			$GetdatacardJSON = curl_exec($datacardPurchase);
			$datacardJSONObj = json_decode($GetdatacardJSON, true);

			if ($GetdatacardJSON == true) {

				if (in_array($datacardJSONObj["status"], array("success", "pending"))) {
					$purchased_pin_in_line_break .= implode("\n", array_filter(explode(",", trim($datacardJSONObj["cards"]))));

					if (mysqli_query($conn_server_db, "INSERT INTO data_card_history (email, id, network_name, data_size, card_quality, card_array) VALUES ('$user_session', '$reference', '$carrier', '" . str_replace(["-", "_"], ".", $data_size) . "', '$amount', '$purchased_pin_in_line_break')") == true) {
					}
					$log_datacard_message = "Recharge Card PINs generated Successfully";
					$checkout_amount = floatval($discounted_price_amount);
					$original_price_amount = ($amount * $qty);
					$remain_balance = $wallet_balance - $checkout_amount;
					$ref_id = $reference;
					if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
						if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$original_price_amount', '$checkout_amount', '$wallet_balance', '$remain_balance', 'Successful', 'N$amount " . strtoupper($carrier) . " Data Card Qty of $qty @ N$checkout_amount', 'data-card', '$site_name')")) {

						}
					}
				}

				if (!in_array($datacardJSONObj["status"], array("success", "pending"))) {
					$log_datacard_message = "Error: " . $datacardJSONObj["desc"];
				}

			} else {
				$log_datacard_message = "Server currently unavailable";
			}

		} else {
			$log_datacard_message = "Insufficient Funds";
		}
	} else {
		$log_datacard_message = "Error: Your Account Has not been Activated for Recharge Card Printing, Contact The Admin to Activate it!";
	}
} else {
	$log_datacard_message = "Error: Data Size is not Available";
}
?>