<?php
include("./../include/config.php");

$api_token = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["token"]));
$api_tv = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["tv"]));
$api_package = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["package"]));
$api_smartcard_number = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["smartcard_number"]));
$api_task = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["task"]));

if (!empty($api_token) && !empty($api_tv) && !empty($api_package) && !empty($api_smartcard_number)) {
	$token_owner_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'");
	if (mysqli_num_rows($token_owner_details) == 1) {
		if (in_array($api_tv, array("startimes", "dstv", "gotv"))) {
			$token_owner_details_array = mysqli_fetch_assoc($token_owner_details);
			//GET USER DETAILS
			$user_session = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'"))["email"];
			$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE apikey='$api_token'"));

			//GET EACH cable API WEBSITE
			$get_startimes_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='startimes'"));
			$get_dstv_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='dstv'"));
			$get_gotv_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='gotv'"));

			//GET EACH cable APIKEY
			$startimes_api_website = $get_startimes_cable_running_api['website'];
			$dstv_api_website = $get_dstv_cable_running_api['website'];
			$gotv_api_website = $get_gotv_cable_running_api['website'];

			$get_startimes_cable_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM cable_api WHERE website='$startimes_api_website'"));
			$get_dstv_cable_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM cable_api WHERE website='$dstv_api_website'"));
			$get_gotv_cable_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM cable_api WHERE website='$gotv_api_website'"));

			$startimes_package_price = array("nova" => "1500", "basic" => "2600", "smart" => "3500", "classic" => "3800", "super" => "6500");
			$dstv_package_price = array("padi" => "2500", "yanga" => "3500", "confam" => "6200", "compact" => "10500", "compact_plus" => "16600", "premium" => "24500", "padi__extraview" => "6000", "yanga__extraview" => "6900", "confam__extraview" => "9600", "compact__extra_view" => "13900", "compact_plus__extra_view" => "20000", "premium__extra_view" => "27900");
			$gotv_package_price = array("smallie" => "1100", "jinja" => "2250", "jolli" => "3300", "max" => "4850", "super" => "6400");

			//GET EACH cable subscription STATUS
			$get_startimes_cable_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM cable_subscription_status WHERE subscription_name='startimes'"));
			$get_dstv_cable_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM cable_subscription_status WHERE subscription_name='dstv'"));
			$get_gotv_cable_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM cable_subscription_status WHERE subscription_name='gotv'"));

			$carrier = $api_tv;
			$package_name = $api_package;
			$cable_iuc_no = $api_smartcard_number;

			if ($api_tv == "startimes") {
				$apikey = $get_startimes_cable_apikey["apikey"];
				$site_name = $startimes_api_website;
				$amount = ($startimes_package_price[$api_package] - ($startimes_package_price[$api_package] * ($get_startimes_cable_running_api["discount_4"] / 100)));
				$network_state = $get_startimes_cable_subscription_status["subscription_status"];
			}

			if ($api_tv == "dstv") {
				$apikey = $get_dstv_cable_apikey["apikey"];
				$site_name = $dstv_api_website;
				$amount = ($dstv_package_price[$api_package] - ($dstv_package_price[$api_package] * ($get_dstv_cable_running_api["discount_4"] / 100)));
				$network_state = $get_dstv_cable_subscription_status["subscription_status"];
			}

			if ($api_tv == "gotv") {
				$apikey = $get_gotv_cable_apikey["apikey"];
				$site_name = $gotv_api_website;
				$amount = ($gotv_package_price[$api_package] - ($gotv_package_price[$api_package] * ($get_gotv_cable_running_api["discount_4"] / 100)));
				$network_state = $get_gotv_cable_subscription_status["subscription_status"];
			}

			if ($api_task == "verify") {
				if (strtolower($site_name) === "smartrecharge.ng") {
					$verifyCablePurchase = curl_init();
					$verifyCableApiUrl = "https://smartrecharge.ng/api/v2/tv/?api_key=" . $apikey . "&product_code=" . $carrier . "_" . $package_name . "&smartcard_number=" . $cable_iuc_no . "&task=verify";
					curl_setopt($verifyCablePurchase, CURLOPT_URL, $verifyCableApiUrl);
					curl_setopt($verifyCablePurchase, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($verifyCablePurchase, CURLOPT_HTTPGET, 1);
					curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYPEER, false);

					$GetverifyCableJSON = curl_exec($verifyCablePurchase);
					$verifyCableJSONObj = json_decode($GetverifyCableJSON, true);

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

					if ($GetverifyCableJSON == true) {

						if (in_array($verifyCableJSONObj["error_code"], array(1987))) {
							$message = array("code" => 200, "name" => $verifyCableJSONObj["data"]["name"], "desc" => "Verification Successful");
							echo json_encode($message, true);
						} else {
							$message = array("code" => 900, "desc" => "Verification Failed! ");
							echo json_encode($message, true);
						}

					}
				}

				if (strtolower($site_name) === "vtpass.com") {
					$verifyCablePurchase = curl_init();
					$verifyCableApiUrl = "https://vtpass.com/api/merchant-verify";
					curl_setopt($verifyCablePurchase, CURLOPT_URL, $verifyCableApiUrl);
					curl_setopt($verifyCablePurchase, CURLOPT_RETURNTRANSFER, true);
					$vtpassHeader = array("Authorization: Basic " . base64_encode($apikey), "Content-Type: application/json");
					curl_setopt($verifyCablePurchase, CURLOPT_HTTPHEADER, $vtpassHeader);
					$vtpassPurchaseData = json_encode(array("serviceID" => $carrier, "billersCode" => $cable_iuc_no), true);
					curl_setopt($verifyCablePurchase, CURLOPT_POSTFIELDS, $vtpassPurchaseData);
					curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYPEER, false);

					$GetverifyCableJSON = curl_exec($verifyCablePurchase);
					$verifyCableJSONObj = json_decode($GetverifyCableJSON, true);

					if ($GetverifyCableJSON == true) {

						if (in_array($verifyCableJSONObj["code"], array("000"))) {
							$message = array("code" => 200, "name" => $verifyCableJSONObj["content"]["Customer_Name"], "desc" => "Verification Successful");
							echo json_encode($message, true);
						} else {
							$message = array("code" => 900, "desc" => "Verification Failed! ");
							echo json_encode($message, true);
						}

					}

				}

				if (strtolower($site_name) === "datagifting.com.ng") {
					$verifyCablePurchase = curl_init();
					$verifyCableApiUrl = "https://v5.datagifting.com.ng/web/api/verify-cable.php";
					curl_setopt($verifyCablePurchase, CURLOPT_URL, $verifyCableApiUrl);
					curl_setopt($verifyCablePurchase, CURLOPT_RETURNTRANSFER, true);
					$vtpassHeader = array("Content-Type: application/json");
					curl_setopt($verifyCablePurchase, CURLOPT_HTTPHEADER, $vtpassHeader);
					if ($carrier == "startimes") {
						$cable_package_array = array("nova" => "nova", "basic" => "basic", "smart" => "smart", "classic" => "classic", "super" => "super");
					}
					if ($carrier == "dstv") {
						$cable_package_array = array("padi" => "padi", "yanga" => "yanga", "confam" => "comfam", "compact" => "compact", "compact_plus" => "compact_plus", "premium" => "premium", "padi__extraview" => "padi_extraview", "yanga__extraview" => "yanga_extraview", "confam__extraview" => "comfam_extraview", "compact__extra_view" => "compact_extra_view", "compact_plus__extra_view" => "compact_plus_extra_view", "premium__extra_view" => "premium_extra_view");
					}
					if ($carrier == "gotv") {
						$cable_package_array = array("smallie" => "smallie", "jinja" => "jinja", "jolli" => "jolli", "max" => "max", "super" => "super");
					}
					$vtpassPurchaseData = json_encode(array(
						"api_key" => $apikey,
						"type" => $carrier,
						"iuc_number" => $cable_iuc_no,
						"package" => $cable_package_array[$package_name]
					));
					curl_setopt($verifyCablePurchase, CURLOPT_POSTFIELDS, $vtpassPurchaseData);
					curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYPEER, false);

					$GetverifyCableJSON = curl_exec($verifyCablePurchase);
					$verifyCableJSONObj = json_decode($GetverifyCableJSON, true);

					if ($GetverifyCableJSON == true) {
						if (in_array($verifyCableJSONObj["status"], array("success", "pending"))) {
							$message = array("code" => 200, "name" => $verifyCableJSONObj["desc"], "desc" => "Verification Successful");
							echo json_encode($message, true);
						} else {
							$message = array("code" => 900, "desc" => "Verification Failed! ");
							echo json_encode($message, true);
						}
					}

				}

			} else {
				if ($network_state == "active") {
					if ($token_owner_details_array["wallet_balance"] >= $amount) {
						if ($site_name == "smartrecharge.ng") {
							include("./../include/cable-smartrecharge.php");
							if (in_array($cableJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}
						if ($site_name == "smartrechargeapi.com") {
							include("./../include/cable-smartrechargeapi.php");
							if (in_array($cableJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}
						if ($site_name == "vtpass.com") {
							include("./../include/cable-vtpass.php");
							if (in_array($cableJSONObj["code"], array("000", "001", "044", "099"))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}
						if ($site_name == "mobileone.ng") {
							include("./../include/cable-mobileone.php");
							if (in_array($cableJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}

						if ($site_name == "datagifting.com.ng") {
							include("./../include/cable-datagifting.php");
							if (in_array($cableJSONObj["status"], array("success", "pending"))) {
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