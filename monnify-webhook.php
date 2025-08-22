<?php
	include(__DIR__."/include/config.php");

	$catch_incoming_request = json_decode(file_get_contents("php://input"),true);
	$monnify_keys = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT * FROM payment_api WHERE website='monnify'"));
	$monnifyApiUrl = "https://api.monnify.com/api/v1/auth/login";
	$monnifyAPILogin = curl_init($monnifyApiUrl);
	curl_setopt($monnifyAPILogin,CURLOPT_URL,$monnifyApiUrl);
	curl_setopt($monnifyAPILogin,CURLOPT_POST,true);
	curl_setopt($monnifyAPILogin,CURLOPT_RETURNTRANSFER,true);
	$monnifyLoginHeader = array("Authorization: Basic ".base64_encode($monnify_keys["public_key"].':'.$monnify_keys["secret_key"]),"Content-Type: application/json","Content-Length: 0");
	curl_setopt($monnifyAPILogin,CURLOPT_HTTPHEADER,$monnifyLoginHeader);
	
	curl_setopt($monnifyAPILogin, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($monnifyAPILogin, CURLOPT_SSL_VERIFYPEER, false);
	
	$GetMonnifyJSON = curl_exec($monnifyAPILogin);
	$monnifyJSONObj = json_decode($GetMonnifyJSON,true);
	
	$access_token = $monnifyJSONObj["responseBody"]["accessToken"];
	if($catch_incoming_request["eventData"] == true){
		$monnify_verify_transaction = json_decode(getAPIBalance("GET","https://api.monnify.com/api/v2/transactions/".$catch_incoming_request["eventData"]["paymentReference"],["Authorization: Bearer ".$access_token],""),true);
	}else{
		$monnify_verify_transaction = json_decode(getAPIBalance("GET","https://api.monnify.com/api/v2/transactions/".$catch_incoming_request["paymentReference"],["Authorization: Bearer ".$access_token],""),true);
	}
if(($monnify_verify_transaction["responseBody"]["paymentStatus"] == "PAID") && ($catch_incoming_request["paymentMethod"] == "ACCOUNT_TRANSFER") OR ($catch_incoming_request["eventData"]["paymentMethod"] == "ACCOUNT_TRANSFER")){
	if($catch_incoming_request["eventData"] == true){
		$customer_name = $catch_incoming_request["eventData"]["customer"]["name"];
		$customer_email = $catch_incoming_request["eventData"]["customer"]["email"];
		$charged_amount = ($catch_incoming_request["eventData"]["totalPayable"]-($catch_incoming_request["eventData"]["totalPayable"]*(1.1/100)));
		$transaction_id = $catch_incoming_request["eventData"]["paymentReference"];
	}else{
		$customer_name = $catch_incoming_request["customer"]["name"];
		$customer_email = $catch_incoming_request["customer"]["email"];
		$charged_amount = ($catch_incoming_request["totalPayable"]-($catch_incoming_request["totalPayable"]*(1.1/100)));
		$transaction_id = $catch_incoming_request["paymentReference"];
	}
	
	$select_transaction_history = mysqli_query($conn_server_db,"SELECT * FROM transaction_history WHERE id='$transaction_id'");
	
	$get_current_balance = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT wallet_balance FROM users WHERE email='$customer_email'"));
	$new_balance = trim($get_current_balance["wallet_balance"])+$charged_amount;
	$insert_transaction_data = "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$customer_email','$transaction_id','$charged_amount', '".$get_current_balance["wallet_balance"]."', '$new_balance', 'successful','Wallet Funding Via Bank Transfer','wallet-funding','monnify.com')";
	
	if(($catch_incoming_request["paymentStatus"] == "PAID") OR ($catch_incoming_request["eventData"]["paymentStatus"] == "PAID")){
		if(mysqli_num_rows($select_transaction_history) == 0){
			if(mysqli_query($conn_server_db,$insert_transaction_data) == true){
				if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$new_balance' WHERE email='$customer_email'") == true){
				
				}
			}
		}
		
		if(mysqli_num_rows($select_transaction_history) == 1){
			if(mysqli_fetch_assoc($select_transaction_history)["status"] !== "successful"){
				if(mysqli_query($conn_server_db,$insert_transaction_data) == true){
					if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$new_balance' WHERE email='$customer_email'") == true){
					
					}
				}
			}
		}
		
	}
}

if(($monnify_verify_transaction["responseBody"]["paymentStatus"] == "PAID") && ($catch_incoming_request["paymentMethod"] == "CARD") OR ($catch_incoming_request["eventData"]["paymentMethod"] == "CARD")){
	if($catch_incoming_request["eventData"] == true){
		$customer_name = $catch_incoming_request["eventData"]["customer"]["name"];
		$customer_email = $catch_incoming_request["eventData"]["customer"]["email"];
		$charged_amount = $catch_incoming_request["eventData"]["settlementAmount"];
		$transaction_id = $catch_incoming_request["eventData"]["paymentReference"];
	}else{
		$customer_name = $catch_incoming_request["customer"]["name"];
		$customer_email = $catch_incoming_request["customer"]["email"];
		$charged_amount = ($catch_incoming_request["settlementAmount"]);
		$transaction_id = $catch_incoming_request["paymentReference"];
	}

	$select_transaction_history = mysqli_query($conn_server_db,"SELECT * FROM transaction_history WHERE id='$transaction_id'");
	
	$get_current_balance = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT wallet_balance FROM users WHERE email='$customer_email'"));
	$new_balance = trim($get_current_balance["wallet_balance"])+$charged_amount;
	$insert_transaction_data = "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$customer_email','$transaction_id','$charged_amount', '".$get_current_balance["wallet_balance"]."', '$new_balance','successful','Wallet Funding Via Card','wallet-funding','monnify.com')";
	
	if(($catch_incoming_request["paymentStatus"] == "PAID") OR ($catch_incoming_request["eventData"]["paymentStatus"] == "PAID")){
		if(mysqli_num_rows($select_transaction_history) == 0){
			if(mysqli_query($conn_server_db,$insert_transaction_data) == true){
				if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$new_balance' WHERE email='$customer_email'") == true){
				
				}
			}
		}
		
		if(mysqli_num_rows($select_transaction_history) == 1){
			if(mysqli_fetch_assoc($select_transaction_history)["status"] !== "successful"){
				if(mysqli_query($conn_server_db,$insert_transaction_data) == true){
					if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$new_balance' WHERE email='$customer_email'") == true){
					
					}
				}
			}
		}
		
	}
}

function getAPIBalance($method,$url,$header,$json){
	$apiwalletBalance = curl_init($url);
	$apiwalletBalanceUrl = $url;
	curl_setopt($apiwalletBalance,CURLOPT_URL,$apiwalletBalanceUrl);
	curl_setopt($apiwalletBalance,CURLOPT_RETURNTRANSFER,true);
	if($method == "POST"){
		curl_setopt($apiwalletBalance,CURLOPT_POST,true);
	}
	
	if($method == "GET"){
	curl_setopt($apiwalletBalance,CURLOPT_HTTPGET,true);
	}
	
	if($header == true){
		curl_setopt($apiwalletBalance,CURLOPT_HTTPHEADER,$header);
	}
	if($json == true){
		curl_setopt($apiwalletBalance,CURLOPT_POSTFIELDS,$json);
	}
	curl_setopt($apiwalletBalance, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($apiwalletBalance, CURLOPT_SSL_VERIFYPEER, false);
	
	$GetAPIBalanceJSON = curl_exec($apiwalletBalance);
	return $GetAPIBalanceJSON;
	}

?>