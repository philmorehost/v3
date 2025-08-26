<?php
	include("./../include/config.php");
	
	$api_token = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["token"]));
	$api_type = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["type"]));
	$api_variation = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["variation"]));
	$api_fullname = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["fullname"]));
	$api_engine_number = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["engine_number"]));
	$api_chasis_number = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["chasis_number"]));
	$api_plate_number = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["plate_number"]));
	$api_vehicle_make = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["vehicle_make"]));
	$api_vehicle_color = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["vehicle_color"]));
	$api_vehicle_model = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["vehicle_model"]));
	$api_year = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["year"]));
	$api_address = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["address"]));
	$api_phone_number = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["phone_number"]));
	
	if(!empty($api_token) && !empty($api_type) && !empty($api_variation) && !empty($api_fullname) && !empty($api_engine_number) && !empty($api_chasis_number) && !empty($api_plate_number) && !empty($api_vehicle_make) && !empty($api_vehicle_color) && !empty($api_vehicle_model) && !empty($api_year) && !empty($api_address) && !empty($api_phone_number)){
		$token_owner_details = mysqli_query($conn_server_db,"SELECT * FROM users WHERE apikey='$api_token'");
		if(mysqli_num_rows($token_owner_details) == 1){
			if(in_array($api_type,array("motor")) && in_array($api_variation,array("1","2","3"))){
					$token_owner_details_array = mysqli_fetch_assoc($token_owner_details);
					//GET USER DETAILS
					$user_session = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT * FROM users WHERE apikey='$api_token'"))["email"];
					$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE apikey='$api_token'"));
					
					//GET EACH insurance API WEBSITE
					$get_motor_insurance_insurance_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM insurance_subscription_running_api WHERE subscription_name='motor_insurance'"));
					
					//GET EACH insurance APIKEY
					$motor_insurance_api_website = $get_motor_insurance_insurance_running_api['website'];
					
					$get_motor_insurance_insurance_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM insurance_api WHERE website='$motor_insurance_api_website'"));
					
					//GET EACH insurance subscription STATUS
					$get_motor_insurance_insurance_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM insurance_subscription_status WHERE subscription_name='motor_insurance'"));
					
					$motor_insurance_price = array(1 => 3000,2 => 5000,3 => 1500);
					
					$plate_number = $api_plate_number;
					$variation = $api_variation;
					$phone_number = $api_phone_number;
					$fullname = $api_fullname;
					$engine_number = $api_engine_number;
					$chasis_number = $api_chasis_number;
					$plate_number = $api_plate_number;
					$vehicle_make = $api_vehicle_make;
					$vehicle_color = $api_vehicle_color;
					$vehicle_model = $api_vehicle_model;
					$year = $api_year;
					$address = $api_address;
					
					if($api_type == "motor"){
					$apikey = $get_motor_insurance_insurance_apikey["apikey"];
					$site_name = $motor_insurance_api_website;
					$amount = ($motor_insurance_price[$api_variation]-($motor_insurance_price[$api_variation]*($get_motor_insurance_insurance_running_api["discount_4"]/100)));
					$network_state = $get_motor_insurance_insurance_subscription_status["subscription_status"];
					}
					
					if($network_state == "active"){
					if($token_owner_details_array["wallet_balance"] >= $amount){
						if($site_name == "vtpass.com"){
							include("./../include/insurance-vtpass.php");
							if(in_array($insuranceJSONObj["code"],array("000","001","044","099"))){
								$message = array("code"=>200,"ref"=>$ref_id,"details"=>$insuranceJSONObj["content"]["transactions"]["status"]."', '".$insuranceJSONObj["content"]["transactions"]["product_name"]." with Unit Price: ".$insuranceJSONObj["content"]["transactions"]["unit_price"]." Unique: ".$insuranceJSONObj["content"]["transactions"]["unique_element"],"desc"=>"Transaction Successful");
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