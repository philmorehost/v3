<?php
include("./../include/config.php");

$api_token = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["token"]));
$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["name"]));
$api_meter_type = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["meter_type"]));
$api_meter_no = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["meter_no"]));
$api_amount = str_replace(["-", "+", "/", "*"], "", mysqli_real_escape_string($conn_server_db, strip_tags($_GET["amount"])));
$api_task = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["task"]));

if (!empty($api_token) && !empty($api_name) && !empty($api_meter_type) && !empty($api_meter_no) && !empty($api_amount)) {
	$token_owner_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'");
	if (mysqli_num_rows($token_owner_details) == 1) {
		if (in_array($api_name, array("ekedc", "eedc", "ikedc", "jedc", "kano", "ibedc", "phed", "aedc"))) {
			if ($api_amount >= 100) {
				$token_owner_details_array = mysqli_fetch_assoc($token_owner_details);
				//GET USER DETAILS
				$user_session = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'"))["email"];
				$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE apikey='$api_token'"));

				//GET EACH electricity_subscription API WEBSITE
				$get_ekedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='ekedc'"));
				$get_eedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='eedc'"));
				$get_ikedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='ikedc'"));
				$get_jedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='jedc'"));
				$get_kano_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='kano'"));
				$get_ibedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='ibedc'"));
				$get_phed_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='phed'"));
				$get_aedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='aedc'"));


				//GET EACH electricity_subscription APIKEY
				$ekedc_api_website = $get_ekedc_electricity_subscription_running_api['website'];
				$eedc_api_website = $get_eedc_electricity_subscription_running_api['website'];
				$ikedc_api_website = $get_ikedc_electricity_subscription_running_api['website'];
				$jedc_api_website = $get_jedc_electricity_subscription_running_api['website'];
				$kano_api_website = $get_kano_electricity_subscription_running_api['website'];
				$ibedc_api_website = $get_ibedc_electricity_subscription_running_api['website'];
				$phed_api_website = $get_phed_electricity_subscription_running_api['website'];
				$aedc_api_website = $get_aedc_electricity_subscription_running_api['website'];

				$get_ekedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$ekedc_api_website'"));
				$get_eedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$eedc_api_website'"));
				$get_ikedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$ikedc_api_website'"));
				$get_jedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$jedc_api_website'"));
				$get_kano_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$kano_api_website'"));
				$get_ibedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$ibedc_api_website'"));
				$get_phed_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$phed_api_website'"));
				$get_aedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$aedc_api_website'"));

				//GET EACH electricity_subscription STATUS
				$get_ekedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='ekedc'"));
				$get_eedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='eedc'"));
				$get_ikedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='ikedc'"));
				$get_jedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='jedc'"));
				$get_kano_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='kano'"));
				$get_ibedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='ibedc'"));
				$get_phed_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='phed'"));
				$get_aedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='aedc'"));

				$carrier = $api_name;
				$meter_type = $api_meter_type;
				$meter_no = $api_meter_no;
				$amount = $api_amount;

				if ($api_name == "ekedc") {
					$apikey = $get_ekedc_electricity_subscription_apikey["apikey"];
					$site_name = $ekedc_api_website;
					$electricity_discount = $get_ekedc_electricity_subscription_running_api["discount_4"];
					$network_state = $get_ekedc_electricity_subscription_status["subscription_status"];
				}

				if ($api_name == "eedc") {
					$apikey = $get_eedc_electricity_subscription_apikey["apikey"];
					$site_name = $eedc_api_website;
					$electricity_discount = $get_eedc_electricity_subscription_running_api["discount_4"];
					$network_state = $get_eedc_electricity_subscription_status["subscription_status"];
				}

				if ($api_name == "ikedc") {
					$apikey = $get_ikedc_electricity_subscription_apikey["apikey"];
					$site_name = $ikedc_api_website;
					$electricity_discount = $get_ikedc_electricity_subscription_running_api["discount_4"];
					$network_state = $get_ikedc_electricity_subscription_status["subscription_status"];
				}

				if ($api_name == "jedc") {
					$apikey = $get_jedc_electricity_subscription_apikey["apikey"];
					$site_name = $jedc_api_website;
					$electricity_discount = $get_jedc_electricity_subscription_running_api["discount_4"];
					$network_state = $get_jedc_electricity_subscription_status["subscription_status"];
				}

				if ($api_name == "kano") {
					$apikey = $get_kano_electricity_subscription_apikey["apikey"];
					$site_name = $kano_api_website;
					$electricity_discount = $get_kano_electricity_subscription_running_api["discount_4"];
					$network_state = $get_kano_electricity_subscription_status["subscription_status"];
				}

				if ($api_name == "ibedc") {
					$apikey = $get_ibedc_electricity_subscription_apikey["apikey"];
					$site_name = $ibedc_api_website;
					$electricity_discount = $get_ibedc_electricity_subscription_running_api["discount_4"];
					$network_state = $get_ibedc_electricity_subscription_status["subscription_status"];
				}

				if ($api_name == "phed") {
					$apikey = $get_phed_electricity_subscription_apikey["apikey"];
					$site_name = $phed_api_website;
					$electricity_discount = $get_phed_electricity_subscription_running_api["discount_4"];
					$network_state = $get_phed_electricity_subscription_status["subscription_status"];
				}

				if ($api_name == "aedc") {
					$apikey = $get_aedc_electricity_subscription_apikey["apikey"];
					$site_name = $aedc_api_website;
					$electricity_discount = $get_aedc_electricity_subscription_running_api["discount_4"];
					$network_state = $get_aedc_electricity_subscription_status["subscription_status"];
				}

				if ($api_task == "verify") {
					if (strtolower($site_name) === "smartrecharge.ng") {
						$verifyElectricityPurchase = curl_init();
						$verifyElectricityApiUrl = "https://smartrecharge.ng/api/v2/electric/?api_key=" . $apikey . "&product_code=" . $carrier . "_" . $meter_type . "_custom&meter_number=" . $meter_no . "&amount=" . $amount . "&task=verify";
						curl_setopt($verifyElectricityPurchase, CURLOPT_URL, $verifyElectricityApiUrl);
						curl_setopt($verifyElectricityPurchase, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($verifyElectricityPurchase, CURLOPT_HTTPGET, 1);

						$GetverifyElectricityJSON = curl_exec($verifyElectricityPurchase);
						$verifyElectricityJSONObj = json_decode($GetverifyElectricityJSON, true);

						if ($GetverifyElectricityJSON == true) {

							if (in_array($verifyElectricityJSONObj["error_code"], array(1987))) {
								$message = array("code" => 200, "name" => $verifyElectricityJSONObj["data"]["name"], "desc" => "Verification Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Verification Failed! ");
								echo json_encode($message, true);
							}
						}
					}

					if (strtolower($site_name) === "smartrechargeapi.com") {
						$verifyElectricityPurchase = curl_init();
						$verifyElectricityApiUrl = "https://smartrechargeapi.com/api/v2/electric/?api_key=" . $apikey . "&product_code=" . $carrier . "_" . $meter_type . "_custom&meter_number=" . $meter_no . "&amount=" . $amount . "&task=verify";
						curl_setopt($verifyElectricityPurchase, CURLOPT_URL, $verifyElectricityApiUrl);
						curl_setopt($verifyElectricityPurchase, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($verifyElectricityPurchase, CURLOPT_HTTPGET, 1);
						curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYPEER, false);

						$GetverifyElectricityJSON = curl_exec($verifyElectricityPurchase);
						$verifyElectricityJSONObj = json_decode($GetverifyElectricityJSON, true);

						if ($GetverifyElectricityJSON == true) {

							if (in_array($verifyElectricityJSONObj["error_code"], array(1987))) {
								$message = array("code" => 200, "name" => $verifyElectricityJSONObj["data"]["name"], "desc" => "Verification Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Verification Failed! ");
								echo json_encode($message, true);
							}

						}
					}

					if (strtolower($site_name) === "mobileone.ng") {
						$verifyElectricityPurchase = curl_init();
						$verifyElectricityApiUrl = "https://mobileone.ng/api/v2/electric/?api_key=" . $apikey . "&product_code=" . $carrier . "_" . $meter_type . "_custom&meter_number=" . $meter_no . "&amount=" . $amount . "&task=verify";
						curl_setopt($verifyElectricityPurchase, CURLOPT_URL, $verifyElectricityApiUrl);
						curl_setopt($verifyElectricityPurchase, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($verifyElectricityPurchase, CURLOPT_HTTPGET, 1);
						curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYPEER, false);

						$GetverifyElectricityJSON = curl_exec($verifyElectricityPurchase);
						$verifyElectricityJSONObj = json_decode($GetverifyElectricityJSON, true);

						if ($GetverifyElectricityJSON == true) {

							if (in_array($verifyElectricityJSONObj["error_code"], array(1987))) {
								$message = array("code" => 200, "name" => $verifyElectricityJSONObj["data"]["name"], "desc" => "Verification Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Verification Failed! ");
								echo json_encode($message, true);
							}

						}
					}

					if (strtolower($site_name) === "datagifting.com.ng") {
						$verifyElectricityPurchase = curl_init();
						$verifyElectricityApiUrl = "https://v5.datagifting.com.ng/web/api/verify-electric.php";
						curl_setopt($verifyElectricityPurchase, CURLOPT_URL, $verifyElectricityApiUrl);
						curl_setopt($verifyElectricityPurchase, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($verifyElectricityPurchase, CURLOPT_POST, 1);
						$dgPurchaseData = json_encode(array(
							"api_key" => $apikey,
							"type" => $meter_type,
							"meter_number" => $meter_no,
							"provider" => $carrier
						));
						curl_setopt($verifyElectricityPurchase, CURLOPT_POSTFIELDS, $dgPurchaseData);
						curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYPEER, false);

						$GetverifyElectricityJSON = curl_exec($verifyElectricityPurchase);
						$verifyElectricityJSONObj = json_decode($GetverifyElectricityJSON, true);

						if ($GetverifyElectricityJSON == true) {

							if (in_array($verifyElectricityJSONObj["status"], array("success", "pending"))) {
								$message = array("code" => 200, "name" => $verifyElectricityJSONObj["desc"], "desc" => "Verification Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Verification Failed! ");
								echo json_encode($message, true);
							}

						}
					}
				} else {
					if ($network_state == "active") {
						if ($token_owner_details_array["wallet_balance"] >= $api_amount) {
							if ($site_name == "smartrecharge.ng") {
								include("./../include/electricity-smartrecharge.php");
								if (in_array($electricityJSONObj["error_code"], array(1986, 1981))) {
									$message = array("code" => 200, "ref" => $ref_id, "name" => $electricityJSONObj["data"]["customer_name"], "address" => $electricityJSONObj["data"]["customer_address"], "units" => $electricityJSONObj["data"]["units"], "token" => $electricityJSONObj["data"]["token"], "desc" => "Transaction Successful");
									echo json_encode($message, true);
								} else {
									$message = array("code" => 900, "desc" => "Transaction Failed! ");
									echo json_encode($message, true);
								}
							}
							if ($site_name == "smartrechargeapi.com") {
								include("./../include/electricity-smartrechargeapi.php");
								if (in_array($electricityJSONObj["error_code"], array(1986, 1981))) {
									$message = array("code" => 200, "ref" => $ref_id, "name" => $electricityJSONObj["data"]["customer_name"], "address" => $electricityJSONObj["data"]["customer_address"], "units" => $electricityJSONObj["data"]["units"], "token" => $electricityJSONObj["data"]["token"], "desc" => "Transaction Successful");
									echo json_encode($message, true);
								} else {
									$message = array("code" => 900, "desc" => "Transaction Failed! ");
									echo json_encode($message, true);
								}
							}
							if ($site_name == "mobileone.ng") {
								include("./../include/electricity-mobileone.php");
								if (in_array($electricityJSONObj["error_code"], array(1986, 1981))) {
									$message = array("code" => 200, "ref" => $ref_id, "name" => $electricityJSONObj["data"]["customer_name"], "address" => $electricityJSONObj["data"]["customer_address"], "units" => $electricityJSONObj["data"]["units"], "token" => $electricityJSONObj["data"]["token"], "desc" => "Transaction Successful");
									echo json_encode($message, true);
								} else {
									$message = array("code" => 900, "desc" => "Transaction Failed! ");
									echo json_encode($message, true);
								}
							}

							if ($site_name == "datagifting.com.ng") {
								include("./../include/electricity-datagifting.php");
								if (in_array($electricityJSONObj["status"], array("success", "pending"))) {
									$message = array("code" => 200, "ref" => $ref_id, "name" => "", "address" => "", "units" => $electricityJSONObj["token_unit"], "token" => $electricityJSONObj["token"], "desc" => "Transaction Successful");
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
				}
			} else {
				$message = array("code" => 400, "desc" => "Amount must be greater than N100");
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