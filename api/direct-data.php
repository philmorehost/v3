<?php
include("./../include/config.php");

$api_token = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["token"]));
$api_network = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["network"]));
$api_data_qty = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["qty"]));
$api_phone_number = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["phone_number"]));

if (!empty($api_token) && !empty($api_network) && !empty($api_data_qty) && !empty($api_phone_number)) {
	$token_owner_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'");
	if (mysqli_num_rows($token_owner_details) == 1) {
		if (in_array($api_network, array("mtn", "airtel", "glo", "9mobile"))) {
			$token_owner_details_array = mysqli_fetch_assoc($token_owner_details);
			//GET USER DETAILS
			$user_session = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'"))["email"];
			$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE apikey='$api_token'"));

			//GET EACH direct_data API WEBSITE
			$get_mtn_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='mtn'"));
			$get_airtel_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='airtel'"));
			$get_glo_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='glo'"));
			$get_9mobile_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='9mobile'"));

			//GET EACH direct_data APIKEY
			$mtn_api_website = $get_mtn_direct_data_running_api['website'];
			$airtel_api_website = $get_airtel_direct_data_running_api['website'];
			$glo_api_website = $get_glo_direct_data_running_api['website'];
			$etisalat_api_website = $get_9mobile_direct_data_running_api['website'];

			$get_mtn_direct_data_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM direct_data_api WHERE website='$mtn_api_website'"));
			$get_airtel_direct_data_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM direct_data_api WHERE website='$airtel_api_website'"));
			$get_glo_direct_data_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM direct_data_api WHERE website='$glo_api_website'"));
			$get_9mobile_direct_data_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM direct_data_api WHERE website='$etisalat_api_website'"));

			//GET EACH direct_data NETWORK STATUS
			$get_mtn_direct_data_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM direct_data_network_status WHERE network_name='mtn'"));
			$get_airtel_direct_data_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM direct_data_network_status WHERE network_name='airtel'"));
			$get_glo_direct_data_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM direct_data_network_status WHERE network_name='glo'"));
			$get_9mobile_direct_data_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM direct_data_network_status WHERE network_name='9mobile'"));

			$carrier = $api_network;
			$data_qty = $api_data_qty;
			$phone_number = $api_phone_number;

			if ($api_network == "mtn") {
				$mtn_smartrecharge_1 = array("mtn_20gb_30_days" => "6000", "mtn_110gb_30days" => "20000", "mtn_2gb_30_days" => "1200", "mtn_40gb" => "10000", "mtn_75gb_30days" => "15000", "mtn_15gb_30_days" => "5000", "mtn_25mb_24hrs" => "50", "mtn_3gb_30days" => "1500", "mtn_120gb_60days" => "30000", "mtn_150gb_90_days" => "50000", "mtn_75mb_24hrs" => "100", "mtn_1gb_24hrs" => "300", "mtn_200mb_2days" => "200", "mtn_2gb_2days" => "500", "mtn_350mb_7days" => "300", "mtn_1gb_7days" => "500", "mtn_6gb_30days" => "2500", "mtn_15gb_30days" => "1000", "mtn_75gb_60days" => "20000", "mtn_250gb_90days" => "75000", "mtn_400gb_365days" => "120000", "mtn_1000gb_365days" => "250000", "mtn_2000gb_365days" => "450000", "mtn_45gb_30days" => "2000", "mtn_6gb_7_days" => "1500", "mtn_10gb_30days" => "3500", "mtn_750mb_14days" => "500", "mtn_2_5gb_2days" => "500", "mtn_8gb_30days" => "3000");
				$mtn_benzoni_2 = array("mtn_3gb30days" => "1500", "mtn_6gb30days" => "2500", "mtn_2_5gb2days" => "500", "mtn_1_5gb_30days" => "1000", "mtn_2gb_30days" => "1200", "mtn_4_5gb_30days" => "2000", "mtn_10gb_30days" => "3500", "mtn_15gb30days" => "5000", "mtn_75gb30days" => "15000", "mtn_75gb60days" => "20000", "mtn_750mb_14days" => "500", "mtn_40gb30days" => "10000", "mtn_120gb_60days" => "30000", "mtn_8gb30days" => "3000", "mtn_20gb30days" => "6000", "mtn_110gb30days" => "20000", "mtn_30gb60days" => "8000");
				$mtn_grecians_3 = array("mtn_3gb30days" => "1500", "mtn_6gb30days" => "2500", "mtn_2_5gb2days" => "500", "mtn_1_5gb_30days" => "1000", "mtn_2gb_30days" => "1200", "mtn_4_5gb_30days" => "2000", "mtn_10gb_30days" => "3500", "mtn_15gb30days" => "5000", "mtn_75gb30days" => "15000", "mtn_75gb60days" => "20000", "mtn_750mb_14days" => "500", "mtn_40gb30days" => "10000", "mtn_120gb_60days" => "30000", "mtn_8gb30days" => "3000", "mtn_20gb30days" => "6000", "mtn_110gb30days" => "20000", "mtn_30gb60days" => "8000");
				$available_product_merge = array_merge($mtn_smartrecharge_1, $mtn_benzoni_2, $mtn_grecians_3);
				$direct_discount = $get_mtn_direct_data_running_api["discount_4"];
				$apikey = $get_mtn_direct_data_apikey["apikey"];
				$site_name = $mtn_api_website;
				$network_state = $get_mtn_direct_data_network_status["network_status"];
			}

			if ($api_network == "airtel") {
				$airtel_smartrecharge_1 = array("airtel_1_5gb" => "1000", "airtel_3gb_30days" => "1500", "airtel_6gb_7days" => "1500", "airtel_4_5gb_30days" => "2000", "airtel_110gb_30days" => "20000", "airtel_750mb" => "500", "airtel_75mb10_extra_24hrs" => "100", "airtel_200mb_3days" => "200", "airtel_350mb__10_extra_7days" => "300", "airtel_40gb_30days" => "10000", "airtel_8gb_30days" => "3000", "airtel_11gb_30days" => "4000", "airtel_75gb_30days" => "15000", "airtel_1gb__1day" => "300", "airtel_2gb__2days" => "500", "airtel_2gb__30days" => "1200", "airtel_6gb__30days" => "2500", "airtel_15gb" => "5000");
				$airtel_benzoni_2 = array("airtel_1_5gb30days" => "1000", "airtel_15gb30days" => "5000", "airtel_40gb30days" => "10000", "airtel_6gb30days" => "2500", "airtel_8gb30days" => "3000", "airtel_11gb30days" => "4000", "airtel_4_5gb30days" => "2000", "airtel_750mb14days" => "500", "airtel_2gb30days" => "1200", "airtel_75gb30days" => "15000", "airtel_110gb30days" => "20000");
				$airtel_grecians_3 = array("airtel_1_5gb30days" => "1000", "airtel_15gb30days" => "5000", "airtel_40gb30days" => "10000", "airtel_6gb30days" => "2500", "airtel_8gb30days" => "3000", "airtel_11gb30days" => "4000", "airtel_4_5gb30days" => "2000", "airtel_750mb14days" => "500", "airtel_2gb30days" => "1200", "airtel_75gb30days" => "15000", "airtel_110gb30days" => "20000");
				$available_product_merge = array_merge($airtel_smartrecharge_1, $airtel_benzoni_2, $airtel_grecians_3);
				$direct_discount = $get_airtel_direct_data_running_api["discount_4"];
				$apikey = $get_airtel_direct_data_apikey["apikey"];
				$site_name = $airtel_api_website;
				$network_state = $get_airtel_direct_data_network_status["network_status"];
			}

			if ($api_network == "glo") {
				$glo_smartrecharge_1 = array("glo_2gb_2days" => "500", "glo_100mb_1_day" => "100", "glo_350mb_2_days" => "200", "glo_1_35gb_14days" => "500", "glo_2_5gb" => "1000", "glo_5_8_gb" => "2000", "glo_7_7_gb" => "2500", "glo_10gb" => "3000", "glo_13_5_gb" => "4000", "glo_1825gb" => "5000", "glo_295gb" => "8000", "glo_50gb" => "10000", "glo_93gb" => "15000", "glo_119gb" => "18000", "glo_50mb_1_day" => "50", "glo_138gb" => "20000", "glo_3_75gb" => "1500", "glo_special_1_gb_special1day" => "200", "glo__7_gb_special7days" => "1500", "glo__3_58_gb_oneoff30days" => "1500", "glo_225gb30days" => "30000", "glo_300gb30days" => "36000", "glo_425gb90days" => "50000", "glo_525gb90days" => "60000", "glo_675gb120days" => "75000", "glo_1024gb365days" => "100000");
				$glo_benzoni_2 = array("glo_2_5gb30days" => "1000", "glo_5_8gb30days" => "2000", "glo_7_7gb30days" => "2500", "glo_10gbdays" => "3000", "glo_13_25gb30days" => "4000", "glo_18_25gb30days" => "5000", "glo_50gb30days" => "10000", "glo_93gb30days" => "15000", "glo_119gb30days" => "18000", "glo_138gb30days" => "20000", "glo_29_5gb30days" => "8000", "glo_4_1gb30days" => "1500", "glo_1_05gb14days" => "500");
				$glo_grecians_3 = array("glo_2_5gb30days" => "1000", "glo_5_8gb30days" => "2000", "glo_7_7gb30days" => "2500", "glo_10gbdays" => "3000", "glo_13_25gb30days" => "4000", "glo_18_25gb30days" => "5000", "glo_50gb30days" => "10000", "glo_93gb30days" => "15000", "glo_119gb30days" => "18000", "glo_138gb30days" => "20000", "glo_29_5gb30days" => "8000", "glo_4_1gb30days" => "1500", "glo_1_05gb14days" => "500");
				$available_product_merge = array_merge($glo_smartrecharge_1, $glo_benzoni_2, $glo_grecians_3);
				$direct_discount = $get_glo_direct_data_running_api["discount_4"];
				$apikey = $get_glo_direct_data_apikey["apikey"];
				$site_name = $glo_api_website;
				$network_state = $get_glo_direct_data_network_status["network_status"];
			}

			if ($api_network == "9mobile") {
				$etisalat_smartrecharge_1 = array("9mobile_15gb_30days" => "5000", "9mobile_40_gb_30_days" => "10000", "9mobile_75_gb_30_days" => "15000", "9mobile_7gb_7_days" => "1500", "9mobile_120gb_365_days" => "110000", "9mobile_100mb_24hrs" => "100", "9mobile_1_5gb_30_days" => "1000", "9mobile_3gb_30_days" => "1500", "9mobile_2gb_30days" => "1200", "9mobile_100gb_100_days" => "84992", "9mobile_60gb_180_days" => "55000", "9mobile_500mb_30days" => "500", "9mobile_4_5gb_30_days" => "2000", "9mobile_30gb_90_days" => "27500", "9mobile_650mb_24hrs" => "200", "9mobile_25mb_24_hrs" => "50", "9mobile_11gb_30days" => "4000");
				$etisalat_benzoni_2 = array("9mobile_2gb30days" => "1200", "9mobile_4_5gb30days" => "2000", "9mobile_11gb30days" => "4000", "9mobile_75gb30days" => "15000", "9mobile_500mb30days" => "500", "9mobile_1_5gb30days" => "1000", "9mobile_40gb30days" => "10000", "9mobile_3gb30days" => "1500", );
				$etisalat_grecians_3 = array("9mobile_2gb30days" => "1200", "9mobile_4_5gb30days" => "2000", "9mobile_11gb30days" => "4000", "9mobile_75gb30days" => "15000", "9mobile_500mb30days" => "500", "9mobile_1_5gb30days" => "1000", "9mobile_40gb30days" => "10000", "9mobile_3gb30days" => "1500", );
				$available_product_merge = array_merge($etisalat_smartrecharge_1, $etisalat_benzoni_2, $etisalat_grecians_3);
				$direct_discount = $get_9mobile_direct_data_running_api["discount_4"];
				$apikey = $get_9mobile_direct_data_apikey["apikey"];
				$site_name = $etisalat_api_website;
				$network_state = $get_9mobile_direct_data_network_status["network_status"];
			}

			if ($available_product_merge[$api_data_qty] == true) {
				$amount = ($available_product_merge[$api_data_qty] - ($available_product_merge[$api_data_qty] * ($direct_discount / 100)));
				if ($network_state == "active") {
					if ($token_owner_details_array["wallet_balance"] >= $amount) {
						if ($site_name == "smartrecharge.ng") {
							include("./../include/direct-data-smartrecharge.php");
							if (in_array($dataJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}

						if ($site_name == "benzoni.ng") {
							include("./../include/direct-data-benzoni.php");
							if (in_array($dataJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}

						if ($site_name == "grecians.ng") {
							include("./../include/direct-data-grecians.php");
							if (in_array($dataJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}

						if ($site_name == "smartrechargeapi.com") {
							include("./../include/direct-data-smartrechargeapi.php");
							if (in_array($dataJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}

						if ($site_name == "mobileone.ng") {
							include("./../include/direct-data-mobileone.php");
							if (in_array($dataJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}

						if ($site_name == "datagifting.com.ng") {
							include("./../include/direct-data-datagifting.php");
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
				$message = array("code" => 700, "desc" => "Error Data Plan Code Name");
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