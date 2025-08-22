<?php
	header("HTTP/1.1 200");
	include(__DIR__."/include/config.php");
	
	$catch_incoming_request = json_decode(file_get_contents("php://input"),true);
	$paystack_keys = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT * FROM payment_api WHERE website='paystack'"));
	$paystack_verify_transaction = json_decode(getAPIBalance("GET","https://api.paystack.co/transaction/verify/".$catch_incoming_request["data"]["reference"],["Authorization: Bearer ".$paystack_keys["secret_key"]],""),true);
	
	$customer_name = $catch_incoming_request["data"]["customer"]["first_name"];
	$customer_phone_number = $catch_incoming_request["data"]["customer"]["phone"];
	$customer_email = $catch_incoming_request["data"]["customer"]["email"];
	$charged_amount = (($catch_incoming_request["data"]["amount"]/100)-($catch_incoming_request["data"]["fees"]/100));
	$transaction_id = $catch_incoming_request["data"]["reference"];
	
	if(($paystack_verify_transaction["status"] == true) && ($catch_incoming_request["event"] == "charge.success") && ($catch_incoming_request["data"]["status"] == "success")){
		$select_transaction_history = mysqli_query($conn_server_db,"SELECT * FROM transaction_history WHERE id='$transaction_id'");
		
		$get_current_balance = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT wallet_balance FROM users WHERE email='$customer_email'"));
		$new_balance = trim($get_current_balance["wallet_balance"])+$charged_amount;
		$insert_transaction_data = "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$customer_email','$transaction_id','$charged_amount', '".$get_current_balance["wallet_balance"]."', '$new_balance', 'successful','Wallet Funding','wallet-funding','paystack.com')";
		
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