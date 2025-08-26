<?php

$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "cable", $cable_iuc_no), true);
if ($daily_transaction_counter["status"] == true) {
	$wallet_balance = $all_user_details["wallet_balance"];
	if ($wallet_balance >= $amount) {
		if ($carrier == "startimes") {
			$cable_package_array = array("nova" => "nova", "basic" => "basic", "smart" => "smart", "classic" => "classic", "super" => "super");
		}
		if ($carrier == "dstv") {
			$cable_package_array = array("padi" => "padi", "yanga" => "yanga", "confam" => "comfam", "compact" => "compact", "compact_plus" => "compact_plus", "premium" => "premium", "padi__extraview" => "padi_extraview", "yanga__extraview" => "yanga_extraview", "confam__extraview" => "comfam_extraview", "compact__extra_view" => "compact_extra_view", "compact_plus__extra_view" => "compact_plus_extra_view", "premium__extra_view" => "premium_extra_view");
		}
		if ($carrier == "gotv") {
			$cable_package_array = array("smallie" => "smallie", "jinja" => "jinja", "jolli" => "jolli", "max" => "max", "super" => "super");
		}

		$cablePurchase = curl_init();
		$cableApiUrl = "https://v5.datagifting.com.ng/web/api/cable.php";
		curl_setopt($cablePurchase, CURLOPT_URL, $cableApiUrl);
		curl_setopt($cablePurchase, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($airtimePurchase, CURLOPT_POST, 1);
		$pay_loads = json_encode(array(
			"api_key" => $apikey,
			"type" => $carrier,
			"iuc_number" => $cable_iuc_no,
			"package" => $cable_package_array[$package_name]
		));
		curl_setopt($cablePurchase, CURLOPT_POSTFIELDS, $pay_loads);
		curl_setopt($cablePurchase, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($cablePurchase, CURLOPT_SSL_VERIFYPEER, false);

		$GetcableJSON = curl_exec($cablePurchase);
		$cableJSONObj = json_decode($GetcableJSON, true);

		if ($GetcableJSON == true) {

			if (in_array($cableJSONObj["status"], haystack: array("success", "pending"))) {
				$log_cable_message = $cableJSONObj["desc"];
				$checkout_amount = $amount;
				$remain_balance = $wallet_balance - $checkout_amount;
				$ref_id = $cableJSONObj["ref"];
				userDailyProductUpdater($user_session, $ref_id, "cable", $cable_iuc_no);
				if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
					if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $cableJSONObj["status"] . "', '" . ucwords($carrier) . " " . ucwords($package_name) . " Subscription to IUC No: " . $cable_iuc_no . " @ N$checkout_amount', 'cable', '$site_name')")) {

					}
				}
			}

			if (!in_array($cableJSONObj["status"], array("success", "pending"))) {
				$log_cable_message = "Error: " . $cableJSONObj["desc"];
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