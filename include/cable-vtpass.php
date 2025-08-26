<?php

$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "cable", $cable_iuc_no), true);
if ($daily_transaction_counter["status"] == true) {
	$wallet_balance = $all_user_details["wallet_balance"];
	if ($wallet_balance >= $amount) {
		$raw_number = "123456789012345678901234567890";
		$reference = date("YmdHis") . substr(str_shuffle($raw_number), 0, 15);
		$phone_number = "09011111111";
		if ($carrier == "startimes") {
			$variation = array("nova" => "nova", "basic" => "basic", "smart" => "smart", "classic" => "classic", "unique" => "", "super" => "super")[$package_name];
			$serviceID = "startimes";
		}
		if ($carrier == "dstv") {
			$variation = array("padi" => "dstv-padi", "yanga" => "dstv-yanga", "confam" => "dstv-confam", "compact" => "dstv79", "premium" => "dstv3", "asia" => "dstv6", "padi__extraview" => "padi-extra", "yanga__extraview" => "yanga-extra", "confam__extraview" => "confam-extra", "compact__extra_view" => "dstv30", "compact_plus" => "dstv7", "compact__asia__extraview" => "com-asia-extra", "compact_plus__extra_view" => "dstv45", "compact_plus__frenchplus__extra_view" => "complus-french-extraview", "compact_plus__asia__extraview" => "dstv48", "premium__extra_view" => "dstv33", "premium__asia__extra_view" => "dstv61", "premium__french__extra_view" => "dstv62")[$package_name];
			$serviceID = "dstv";
		}
		if ($carrier == "gotv") {
			$variation = array("smallie" => "gotv-smallie", "jinja" => "gotv-jinja", "jolli" => "gotv-jolli", "max" => "gotv-max", "super" => "gotv-supa")[$package_name];
			$serviceID = "gotv";
		}

		$cablePurchase = curl_init();
		$cableApiUrl = "https://vtpass.com/api/pay";
		curl_setopt($cablePurchase, CURLOPT_URL, $cableApiUrl);
		curl_setopt($cablePurchase, CURLOPT_RETURNTRANSFER, true);
		$vtpassHeader = array("Authorization: Basic " . base64_encode($apikey), "Content-Type: application/json");
		curl_setopt($cablePurchase, CURLOPT_HTTPHEADER, $vtpassHeader);
		$vtpassPurchaseData = json_encode(array("request_id" => $reference, "serviceID" => $serviceID, "billersCode" => $cable_iuc_no, "variation_code" => $variation, "phone" => $phone_number), true);
		curl_setopt($cablePurchase, CURLOPT_POSTFIELDS, $vtpassPurchaseData);
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

			if (in_array($cableJSONObj["code"], array("000", "001", "044", "099"))) {
				$log_cable_message = $cableJSONObj["response_description"];
				$checkout_amount = $amount;
				$remain_balance = $wallet_balance - $checkout_amount;
				$ref_id = $cableJSONObj["requestId"];
				userDailyProductUpdater($user_session, $ref_id, "cable", $cable_iuc_no);
				if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true) {
					if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '" . $cableJSONObj["content"]["transactions"]["status"] . "', '" . ucwords($carrier) . " " . ucwords($package_name) . " Subscription to IUC No: " . $cable_iuc_no . " @ N$checkout_amount', 'cable', '$site_name')")) {

					}
				}
			}

			if (!in_array($cableJSONObj["code"], array("000", "001", "044", "099"))) {
				$log_cable_message = "Error: " . $cableJSONObj["response_description"];
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