<?php
include("./../include/config.php");

$api_token = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["token"]));
$api_package = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["package"]));

if (!empty($api_token) && !empty($api_package)) {
	$token_owner_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'");
	if (mysqli_num_rows($token_owner_details) == 1) {
		if (in_array($api_package, array("waec", "neco", "nabteb"))) {
			$token_owner_details_array = mysqli_fetch_assoc($token_owner_details);
			//GET USER DETAILS
			$user_session = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'"))["email"];
			$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE apikey='$api_token'"));

			//GET EACH exam API WEBSITE
			$get_waec_exam_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM exam_pin_running_api WHERE pin_name='waec'"));
			$get_neco_exam_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM exam_pin_running_api WHERE pin_name='neco'"));
			$get_nabteb_exam_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM exam_pin_running_api WHERE pin_name='nabteb'"));

			//GET EACH exam APIKEY
			$waec_api_website = $get_waec_exam_running_api['website'];
			$neco_api_website = $get_neco_exam_running_api['website'];
			$nabteb_api_website = $get_nabteb_exam_running_api['website'];

			$get_waec_exam_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM exam_api WHERE website='$waec_api_website'"));
			$get_neco_exam_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM exam_api WHERE website='$neco_api_website'"));
			$get_nabteb_exam_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM exam_api WHERE website='$nabteb_api_website'"));

			//GET EACH exam pin STATUS
			$get_waec_exam_pin_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT pin_status FROM exam_pin_status WHERE pin_name='waec'"));
			$get_neco_exam_pin_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT pin_status FROM exam_pin_status WHERE pin_name='neco'"));
			$get_nabteb_exam_pin_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT pin_status FROM exam_pin_status WHERE pin_name='nabteb'"));

			$waec_package_price = array("waec" => "3100");
			$neco_package_price = array("neco" => "1100");
			$nabteb_package_price = array("nabteb" => "1100");

			$carrier = $api_package;

			if ($api_package == "waec") {
				$apikey = $get_waec_exam_apikey["apikey"];
				$site_name = $waec_api_website;
				$amount = ($waec_package_price[$api_package] - ($waec_package_price[$api_package] * ($get_waec_exam_running_api["discount_4"] / 100)));
				$network_state = $get_waec_exam_pin_status["pin_status"];
			}

			if ($api_package == "neco") {
				$apikey = $get_neco_exam_apikey["apikey"];
				$site_name = $neco_api_website;
				$amount = ($neco_package_price[$api_package] - ($neco_package_price[$api_package] * ($get_neco_exam_running_api["discount_4"] / 100)));
				$network_state = $get_neco_exam_pin_status["pin_status"];
			}

			if ($api_package == "nabteb") {
				$apikey = $get_nabteb_exam_apikey["apikey"];
				$site_name = $nabteb_api_website;
				$amount = ($nabteb_package_price[$api_package] - ($nabteb_package_price[$api_package] * ($get_nabteb_exam_running_api["discount_4"] / 100)));
				$network_state = $get_nabteb_exam_pin_status["pin_status"];
			}

			if ($network_state == "active") {
				if ($token_owner_details_array["wallet_balance"] >= $amount) {
					if ($site_name == "abumpay.com") {
						include("./../include/exam-abumpay.php");
						if (in_array($examJSONObj["code"], array(200))) {
							$message = array("code" => 200, "ref" => $ref_id, "desc" => $examJSONObj["desc"]);
							echo json_encode($message, true);
						} else {
							$message = array("code" => 900, "desc" => "Transaction Failed! ");
							echo json_encode($message, true);
						}
					}

					if ($site_name == "clubkonnect.com") {
						include("./../include/exam-clubkonnect.php");
						if (in_array($examJSONObj["statuscode"], array(100, 199, 200, 201, 299))) {
							$message = array("code" => 200, "ref" => $ref_id, "desc" => $examJSONObj["desc"]);
							echo json_encode($message, true);
						} else {
							$message = array("code" => 900, "desc" => "Transaction Failed! ");
							echo json_encode($message, true);
						}
					}

					if ($site_name == "datagifting.com.ng") {
						include("./../include/exam-datagifting.php");
						if (in_array($dataJSONObj["status"], array("success", "pending"))) {
							$message = array("code" => 200, "ref" => $ref_id, "desc" => $examJSONObj["response_desc"]);
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