<?php
	include("./../include/config.php");
	
	$api_token = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["token"]));
	$api_network = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["network"]));
	$api_phone_number = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["phone_number"]));
	$api_amount = str_replace(["-","+","/","*"],"",mysqli_real_escape_string($conn_server_db,strip_tags($_GET["amount"])));
	
	if(!empty($api_token) && !empty($api_network) && !empty($api_phone_number) && !empty($api_amount)){
		$token_owner_details = mysqli_query($conn_server_db,"SELECT * FROM users WHERE apikey='$api_token'");
		if(mysqli_num_rows($token_owner_details) == 1){
			if(in_array($api_network,array("mtn","airtel","glo","9mobile"))){
				if($api_amount >= 100){
					$token_owner_details_array = mysqli_fetch_assoc($token_owner_details);
					//GET USER DETAILS
					$user_session = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT * FROM users WHERE apikey='$api_token'"))["email"];
					$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE apikey='$api_token'"));
					
					//GET EACH AIRTIME API WEBSITE
					$get_mtn_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='mtn'"));
					$get_airtel_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='airtel'"));
					$get_glo_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='glo'"));
					$get_9mobile_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='9mobile'"));
					
					//GET EACH AIRTIME APIKEY
					$mtn_api_website = $get_mtn_airtime_running_api['website'];
					$airtel_api_website = $get_airtel_airtime_running_api['website'];
					$glo_api_website = $get_glo_airtime_running_api['website'];
					$etisalat_api_website = $get_9mobile_airtime_running_api['website'];
					
					$get_mtn_airtime_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM airtime_api WHERE website='$mtn_api_website'"));
					$get_airtel_airtime_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM airtime_api WHERE website='$airtel_api_website'"));
					$get_glo_airtime_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM airtime_api WHERE website='$glo_api_website'"));
					$get_9mobile_airtime_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM airtime_api WHERE website='$etisalat_api_website'"));
					
					//GET EACH AIRTIME NETWORK STATUS
					$get_mtn_airtime_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM airtime_network_status WHERE network_name='mtn'"));
					$get_airtel_airtime_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM airtime_network_status WHERE network_name='airtel'"));
					$get_glo_airtime_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM airtime_network_status WHERE network_name='glo'"));
					$get_9mobile_airtime_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM airtime_network_status WHERE network_name='9mobile'"));
					
					$carrier = $api_network;
					$phone_number = $api_phone_number;
					$amount = $api_amount;
					
					if($api_network == "mtn"){
					$apikey = $get_mtn_airtime_apikey["apikey"];
					$site_name = $mtn_api_website;
					$airtime_discount = $get_mtn_airtime_running_api["discount_4"];
					$network_state = $get_mtn_airtime_network_status["network_status"];
					}
					
					if($api_network == "airtel"){
					$apikey = $get_airtel_airtime_apikey["apikey"];
					$site_name = $airtel_api_website;
					$airtime_discount = $get_airtel_airtime_running_api["discount_4"];
					$network_state = $get_airtel_airtime_network_status["network_status"];
					}
					
					if($api_network == "glo"){
					$apikey = $get_glo_airtime_apikey["apikey"];
					$site_name = $glo_api_website;
					$airtime_discount = $get_glo_airtime_running_api["discount_4"];
					$network_state = $get_glo_airtime_network_status["network_status"];
					}
					
					if($api_network == "9mobile"){
					$apikey = $get_9mobile_airtime_apikey["apikey"];
					$site_name = $etisalat_api_website;
					$airtime_discount = $get_9mobile_airtime_running_api["discount_4"];
					$network_state = $get_9mobile_airtime_network_status["network_status"];
					}
					
					if($network_state == "active"){
					if($token_owner_details_array["wallet_balance"] >= $api_amount){
						if($site_name == "smartrecharge.ng"){
							include("./../include/airtime-smartrecharge.php");
							if(in_array($AirtimeJSONObj["error_code"],array(1986,1981))){
								$message = array("code"=>200,"ref"=>$ref_id,"desc"=>"Transaction Successful");
								echo json_encode($message,true);
							}else{
								$message = array("code"=>900,"desc"=>"Transaction Failed! ");
								echo json_encode($message,true);
							}
						}
						if($site_name == "smartrechargeapi.com"){
							include("./../include/airtime-smartrechargeapi.php");
							if(in_array($AirtimeJSONObj["error_code"],array(1986,1981))){
								$message = array("code"=>200,"ref"=>$ref_id,"desc"=>"Transaction Successful");
								echo json_encode($message,true);
							}else{
								$message = array("code"=>900,"desc"=>"Transaction Failed! ");
								echo json_encode($message,true);
							}
						}
					
						if($site_name == "benzoni.ng"){
							include("./../include/airtime-benzoni.php");
							if(in_array($AirtimeJSONObj["error_code"],array(1986,1981))){
								$message = array("code"=>200,"ref"=>$ref_id,"desc"=>"Transaction Successful");
								echo json_encode($message,true);
							}else{
								$message = array("code"=>900,"desc"=>"Transaction Failed! ");
								echo json_encode($message,true);
							}
						}
					
						if($site_name == "grecians.ng"){
							include("./../include/airtime-grecians.php");
							if(in_array($AirtimeJSONObj["error_code"],array(1986,1981))){
								$message = array("code"=>200,"ref"=>$ref_id,"desc"=>"Transaction Successful");
								echo json_encode($message,true);
							}else{
								$message = array("code"=>900,"desc"=>"Transaction Failed! ");
								echo json_encode($message,true);
							}
						}
						
						if($site_name == "mobileone.ng"){
							include("./../include/airtime-mobileone.php");
							if(in_array($AirtimeJSONObj["error_code"],array(1986,1981))){
								$message = array("code"=>200,"ref"=>$ref_id,"desc"=>"Transaction Successful");
								echo json_encode($message,true);
							}else{
								$message = array("code"=>900,"desc"=>"Transaction Failed! ");
								echo json_encode($message,true);
							}
						}

						if($site_name == "datagifting.com.ng"){
							include("./../include/airtime-datagifting.php");
							if(in_array($AirtimeJSONObj["status"],array("success","pending"))){
								$message = array("code"=>200,"ref"=>$ref_id,"desc"=>"Transaction Successful");
								echo json_encode($message,true);
							}else{
								$message = array("code"=>900,"desc"=>"Transaction Failed! ");
								echo json_encode($message,true);
							}
						}
					}else{
						$message = array("code"=>800,"desc"=>"Insufficient Fund, Fund Wallet And Try Again! ");
						echo json_encode($message,true);
					}
					}else{
						$message = array("code"=>700,"desc"=>"Error Service Locked");
						echo json_encode($message,true);
					}
				}else{
					$message = array("code"=>400,"desc"=>"Amount must be greater than N100");
					echo json_encode($message,true);
				}
			}else{
				$message = array("code"=>700,"desc"=>"Error Network Name");
				echo json_encode($message,true);
			}
		}else{
			if(mysqli_num_rows($token_owner_details) > 1){
				$message = array("code"=>600,"desc"=>"Regenerate APIKEY and try again! ");
				echo json_encode($message,true);
			}else{
				$message = array("code"=>300,"desc"=>"User doesn't Exists");
				echo json_encode($message,true);
			}
		}
	}else{
		$message = array("code"=>500,"desc"=>"Incomplete Parameters");
		echo json_encode($message,true);
	}
?>