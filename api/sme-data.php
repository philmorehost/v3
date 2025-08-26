<?php
include("./../include/config.php");

$api_token = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["token"]));
$api_network = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["network"]));
$api_data_qty = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["qty"]));
$api_phone_number = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["phone_number"]));

if (!empty($api_token) && !empty($api_network) && !empty($api_data_qty) && !empty($api_phone_number)) {
	$token_owner_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'");
	if (mysqli_num_rows($token_owner_details) == 1) {
		if (in_array($api_network, array("mtn", "airtel", "9mobile"))) {
			$token_owner_details_array = mysqli_fetch_assoc($token_owner_details);
			//GET USER DETAILS
			$user_session = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM users WHERE apikey='$api_token'"))["email"];
			$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE apikey='$api_token'"));

			//GET EACH sme_data API WEBSITE
			$get_mtn_sme_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM sme_data_network_running_api WHERE network_name='mtn'"));
			$get_airtel_sme_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM sme_data_network_running_api WHERE network_name='airtel'"));
			$get_9mobile_sme_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM sme_data_network_running_api WHERE network_name='9mobile'"));

			//GET EACH sme_data APIKEY
			$mtn_api_website = $get_mtn_sme_data_running_api['website'];
			$airtel_api_website = $get_airtel_sme_data_running_api['website'];
			$etisalat_api_website = $get_9mobile_sme_data_running_api['website'];

			$get_mtn_sme_data_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM sme_data_api WHERE website='$mtn_api_website'"));
			$get_airtel_sme_data_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM sme_data_api WHERE website='$airtel_api_website'"));
			$get_9mobile_sme_data_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM sme_data_api WHERE website='$etisalat_api_website'"));

			//GET EACH sme_data NETWORK price
			$get_mtn_sme_data_network_qty_price = mysqli_query($conn_server_db, "SELECT sme_data_qty, sme_data_price_1, sme_data_price_2, sme_data_price_3, sme_data_price_4 FROM mtn_sme_data_network_qty_price WHERE sme_data_qty='$api_data_qty'");
			$get_airtel_sme_data_network_qty_price = mysqli_query($conn_server_db, "SELECT sme_data_qty, sme_data_price_1, sme_data_price_2, sme_data_price_3, sme_data_price_4 FROM airtel_sme_data_network_qty_price WHERE sme_data_qty='$api_data_qty'");
			$get_9mobile_sme_data_network_qty_price = mysqli_query($conn_server_db, "SELECT sme_data_qty, sme_data_price_1, sme_data_price_2, sme_data_price_3, sme_data_price_4 FROM etisalat_sme_data_network_qty_price WHERE sme_data_qty='$api_data_qty'");

			//GET EACH sme_data NETWORK STATUS
			$get_mtn_sme_data_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM sme_data_network_status WHERE network_name='mtn'"));
			$get_airtel_sme_data_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM sme_data_network_status WHERE network_name='airtel'"));
			$get_9mobile_sme_data_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM sme_data_network_status WHERE network_name='9mobile'"));

			$carrier = $api_network;
			$data_qty = $api_data_qty;
			$phone_number = $api_phone_number;

			if ($api_network == "mtn") {
				$apikey = $get_mtn_sme_data_apikey["apikey"];
				$site_name = $mtn_api_website;
				$amount = mysqli_fetch_assoc($get_mtn_sme_data_network_qty_price)["sme_data_price_4"];
				$check_data_qty_exist = $get_mtn_sme_data_network_qty_price;
				$network_state = $get_mtn_sme_data_network_status["network_status"];
			}

			if ($api_network == "airtel") {
				$apikey = $get_airtel_sme_data_apikey["apikey"];
				$site_name = $airtel_api_website;
				$amount = mysqli_fetch_assoc($get_airtel_sme_data_network_qty_price)["sme_data_price_4"];
				$check_data_qty_exist = $get_airtel_sme_data_network_qty_price;
				$network_state = $get_airtel_sme_data_network_status["network_status"];
			}

			if ($api_network == "9mobile") {
				$apikey = $get_9mobile_sme_data_apikey["apikey"];
				$site_name = $etisalat_api_website;
				$amount = mysqli_fetch_assoc($get_etisalat_sme_data_network_qty_price)["sme_data_price_4"];
				$check_data_qty_exist = $get_9mobile_sme_data_network_qty_price;
				$network_state = $get_9mobile_sme_data_network_status["network_status"];
			}

			if (mysqli_num_rows($check_data_qty_exist) == 1) {
				if ($network_state == "active") {
					if ($token_owner_details_array["wallet_balance"] >= $amount) {
						if ($site_name == "smartrecharge.ng") {
							include("./../include/sme-data-smartrecharge.php");
							if (in_array($dataJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}

						if ($site_name == "benzoni.ng") {
							include("./../include/sme-data-benzoni.php");
							if (in_array($dataJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}
						if ($site_name == "grecians.ng") {
							include("./../include/sme-data-grecians.php");
							if (in_array($dataJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}
						if ($site_name == "smartrechargeapi.com") {
							include("./../include/sme-data-smartrechargeapi.php");
							if (in_array($dataJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}
						if ($site_name == "rpidatang.com") {
							include("./../include/sme-data-rpidatang.php");
							if (in_array($dataJSONObj["Status"], array("successful", "pending"))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}
						if ($site_name == "subvtu.com") {
							include("./../include/sme-data-subvtu.php");
							if (in_array($dataJSONObj["Status"], array("successful", "pending"))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}
						if ($site_name == "legitdataway.com") {
							include("./../include/sme-data-legitdataway.php");
							if (in_array($dataJSONObj["status"], array("success"))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}
						if ($site_name == "mobileone.ng") {
							include("./../include/sme-data-mobileone.php");
							if (in_array($dataJSONObj["error_code"], array(1986, 1981))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}

						if ($site_name == "datagifting.com.ng") {
							include("./../include/sme-data-datagifting.php");
							if (in_array($dataJSONObj["status"], array("success", "pending"))) {
								$message = array("code" => 200, "ref" => $ref_id, "desc" => "Transaction Successful");
								echo json_encode($message, true);
							} else {
								$message = array("code" => 900, "desc" => "Transaction Failed! ");
								echo json_encode($message, true);
							}
						}

						if($site_name == "alrahuzdata.com.ng"){
							include("./../include/sme-data-alrahuzdata.php");
							if(in_array($dataJSONObj["Status"],array("successful","pending"))){
								$message = array("code"=>200,"ref"=>$ref_id,"desc"=>"Transaction Successful");
								echo json_encode($message,true);
							}else{
								$message = array("code"=>900,"desc"=>"Transaction Failed! ");
								echo json_encode($message,true);
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