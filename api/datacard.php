<?php
include("./../include/config.php");

$api_token = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["token"]));
$api_network = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["network"]));
$api_qty = str_replace(["-", "+", "/", "*"], "", mysqli_real_escape_string($conn_server_db, strip_tags($_GET["qty"])));
$api_size = str_replace(["-", "+", "/", "*"], "", mysqli_real_escape_string($conn_server_db, strip_tags($_GET["size"])));

if (!empty($api_token) && !empty($api_network) && !empty($api_qty) && !empty($api_size)) {
	$token_owner_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'");
	if (mysqli_num_rows($token_owner_details) == 1) {
		if (in_array($api_network, array("mtn", "airtel", "glo", "9mobile"))) {
			if ($api_network == "mtn") {
				$all_data_card_qtyprice_array = array("1gb" => "250", "1.5gb" => "320", "2gb" => "500");
			}

			if ($api_network == "airtel") {
				$all_data_card_qtyprice_array = array();
			}

			if ($api_network == "glo") {
				$all_data_card_qtyprice_array = array();
			}

			if ($api_network == "9mobile") {
				$all_data_card_qtyprice_array = array();
			}

			if (!empty($all_data_card_qtyprice_array[$api_size])) {
				$token_owner_details_array = mysqli_fetch_assoc($token_owner_details);
				//GET USER DETAILS
				$user_session = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'"))["email"];
				$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE apikey='$api_token'"));

				//GET EACH datacard API WEBSITE
				$get_mtn_datacard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM datacard_network_running_api WHERE network_name='mtn'"));
				$get_airtel_datacard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM datacard_network_running_api WHERE network_name='airtel'"));
				$get_glo_datacard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM datacard_network_running_api WHERE network_name='glo'"));
				$get_9mobile_datacard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM datacard_network_running_api WHERE network_name='9mobile'"));

				//GET EACH datacard APIKEY
				$mtn_api_website = $get_mtn_datacard_running_api['website'];
				$airtel_api_website = $get_airtel_datacard_running_api['website'];
				$glo_api_website = $get_glo_datacard_running_api['website'];
				$etisalat_api_website = $get_9mobile_datacard_running_api['website'];

				$get_mtn_datacard_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM datacard_api WHERE website='$mtn_api_website'"));
				$get_airtel_datacard_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM datacard_api WHERE website='$airtel_api_website'"));
				$get_glo_datacard_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM datacard_api WHERE website='$glo_api_website'"));
				$get_9mobile_datacard_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM datacard_api WHERE website='$etisalat_api_website'"));

				//GET EACH AIRTIME NETWORK STATUS
				$get_mtn_datacard_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM datacard_network_status WHERE network_name='mtn'"));
				$get_airtel_datacard_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM datacard_network_status WHERE network_name='airtel'"));
				$get_glo_datacard_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM datacard_network_status WHERE network_name='glo'"));
				$get_9mobile_datacard_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM datacard_network_status WHERE network_name='9mobile'"));

				$carrier = $api_network;
				$qty = $api_qty;
				$data_size = $api_size;
				$amount = $all_data_card_qtyprice_array[$api_size];

				if ($api_network == "mtn") {
					$apikey = $get_mtn_datacard_apikey["apikey"];
					$site_name = $mtn_api_website;
					$datacard_discount = $get_mtn_datacard_running_api["discount_4"];
					$network_state = $get_mtn_datacard_network_status["network_status"];
				}

				if ($api_network == "airtel") {
					$apikey = $get_airtel_datacard_apikey["apikey"];
					$site_name = $airtel_api_website;
					$datacard_discount = $get_airtel_datacard_running_api["discount_4"];
					$network_state = $get_airtel_datacard_network_status["network_status"];
				}

				if ($api_network == "glo") {
					$apikey = $get_glo_datacard_apikey["apikey"];
					$site_name = $glo_api_website;
					$datacard_discount = $get_glo_datacard_running_api["discount_4"];
					$network_state = $get_glo_datacard_network_status["network_status"];
				}

				if ($api_network == "9mobile") {
					$apikey = $get_9mobile_datacard_apikey["apikey"];
					$site_name = $etisalat_api_website;
					$datacard_discount = $get_9mobile_datacard_running_api["discount_4"];
					$network_state = $get_9mobile_datacard_network_status["network_status"];
				}

				$discounted_price_amount = (($amount - ($amount * ($datacard_discount / 100))) * $qty);
				if ($network_state == "active") {
					if ($token_owner_details_array["wallet_balance"] >= $discounted_price_amount) {
						if ($site_name == $_SERVER["HTTP_HOST"]) {
							include("./../include/datacard-manual.php");
							if (count($manual_datacard_pin_array) >= $qty) {
								$recharge_pin_in_comma = trim(str_replace("\n", " ", str_replace(" ", "", $purchased_pin_in_line_break)));
								$recharge_pin_in_comma = str_replace(" ", ",", $recharge_pin_in_comma);
								$recharge_pin_in_comma = array_filter(explode(",", trim($recharge_pin_in_comma)));
								foreach ($recharge_pin_in_comma as $pinserial) {
									$expPinSerial = array_filter(explode(":", trim($pinserial)));
									$allPINAndSerialArray = array("pin" => strval(trim($expPinSerial[0])), "serialno" => strval(trim($expPinSerial[1])));
									$allPINAndSerialJSONs[] = $allPINAndSerialArray;
								}

								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful", "card" => $allPINAndSerialJSONs);
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}

						if ($site_name == "datagifting.com.ng") {
							include("./../include/datacard-datagifting.php");
							if (in_array($datacardJSONObj["status"], array("success", "pending"))) {
								$recharge_pin_in_comma = trim(str_replace("\n", " ", str_replace(" ", "", $purchased_pin_in_line_break)));
								$recharge_pin_in_comma = str_replace(" ", ",", $recharge_pin_in_comma);
								$recharge_pin_in_comma = array_filter(explode(",", trim($recharge_pin_in_comma)));
								foreach ($recharge_pin_in_comma as $pinserial) {
									$expPinSerial = array_filter(explode(":", trim($pinserial)));
									$allPINAndSerialArray = array("pin" => strval(trim($expPinSerial[0])), "serialno" => strval(trim($expPinSerial[1])));
									$allPINAndSerialJSONs[] = $allPINAndSerialArray;
								}
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful", "card" => $allPINAndSerialJSONs);
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
				$message = array("code" => 400, "desc" => "Data Size is not Available");
				echo json_encode($message, true);
			}
		} else {
			$message = array("code" => 700, "desc" => "Error Network Name");
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