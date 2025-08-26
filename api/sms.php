<?php
include("./../include/config.php");
$api_token = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["token"]));
$api_from = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["from"]));
$api_to = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["to"]));
$api_message = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["message"]));
$api_date = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["date"]));

if (!empty($api_token) && !empty($api_from) && !empty($api_to) && !empty($api_message)) {
	$token_owner_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'");
	if (mysqli_num_rows($token_owner_details) == 1) {
		$token_owner_details_array = mysqli_fetch_assoc($token_owner_details);
		//GET USER DETAILS
		$user_session = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'"))["email"];
		$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE apikey='$api_token'"));

		//GET EACH sms API WEBSITE
		$get_smsserver_sms_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, price_1, price_2, price_3, price_4 FROM sms_network_running_api WHERE network_name='smsserver'"));

		//GET EACH sms APIKEY
		$smsserver_api_website = $get_smsserver_sms_running_api['website'];

		$get_smsserver_sms_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM sms_api WHERE website='$smsserver_api_website'"));

		//GET EACH sms NETWORK STATUS
		$get_smsserver_sms_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM sms_network_status WHERE network_name='smsserver'"));

		$phone_number = $api_to;
		$apikey = $get_smsserver_sms_apikey["apikey"];
		$site_name = $smsserver_api_website;
		$amount = (count(array_filter(explode(",", $api_to))) * $get_smsserver_sms_running_api["price_4"]);
		$senderID = $api_from;
		$smsText = $api_message;
		$schedule_date = $api_date;
		$network_state = $get_smsserver_sms_network_status["network_status"];
		if ($network_state == "active") {
			if ($token_owner_details_array["wallet_balance"] >= $amount) {
				if ($site_name == "philmoresms.com") {
					include("./../include/sms-philmoresms.php");
					if (in_array($smsJSONObj["status"], array("success", "queued"))) {
						$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
						echo json_encode($message, true);
					} else {
						$message = array("code" => 900, "desc" => "Transaction Failed! ");
						echo json_encode($message, true);
					}
				}

				if ($site_name == "datagifting.com.ng") {
					include("./../include/sms-datagifting.php");
					if (in_array($dataJSONObj["status"], array("success", "pending"))) {
						$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
						echo json_encode($message, true);
					} else {
						$message = array("code" => 900, "desc" => "Transaction Failed! ");
						echo json_encode($message, true);
					}
				}
			} else {
				$message = array("code" => 800, "desc" => "Insufficient Fund, Fund Wallet And Try Again! ");
				echo json_encode($message, true);
			}
		} else {
			$message = array("code" => 700, "desc" => "Error Service Locked");
			echo json_encode($message, true);
		}
	} else {
		if (mysqli_num_rows($token_owner_details) > 1) {
			$message = array("code" => 600, "desc" => "Regenerate APIKEY and try again! ");
			echo json_encode($message, true);
		} else {
			$message = array("code" => 300, "desc" => "User doesn't Exists");
			echo json_encode($message, true);
		}
	}
} else {
	$message = array("code" => 500, "desc" => "Incomplete Parameters");
	echo json_encode($message, true);
}
?>