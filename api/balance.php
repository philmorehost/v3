<?php
	include("./../include/config.php");
	
	$api_token = mysqli_real_escape_string($conn_server_db,strip_tags($_GET["token"]));
	
	if(!empty($api_token)){
		$token_owner_details = mysqli_query($conn_server_db,"SELECT * FROM users WHERE apikey='$api_token'");
		if(mysqli_num_rows($token_owner_details) == 1){
			$token_owner_details_array = mysqli_fetch_assoc($token_owner_details);
			//GET USER DETAILS
			$user_session = $token_owner_details_array["email"];
			$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
			$message = array("code"=>200,"balance"=>$all_user_details["wallet_balance"]);
			echo json_encode($message,true);
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