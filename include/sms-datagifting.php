<?php

$wallet_balance = $all_user_details["wallet_balance"];
if($wallet_balance >= $amount){

$smsPurchase = curl_init();
$smsApiUrl = "https://v5.datagifting.com.ng/web/api/sms.php";
curl_setopt($smsPurchase,CURLOPT_URL,$smsApiUrl);
curl_setopt($smsPurchase,CURLOPT_RETURNTRANSFER,1);
curl_setopt($smsPurchase,CURLOPT_POST,1);
$pay_loads = json_encode(array(
	"api_key" => $apikey,
	"network" => $carrier,
	"phone_number" => $phone_number,
	"sender_id" => $senderID,
	"type" => "standard_sms",
	"message" => str_replace(" ","+",$smsText),
	"date" => str_replace(" ","+",$schedule_date)
));
curl_setopt($smsPurchase, CURLOPT_POSTFIELDS, $pay_loads);
curl_setopt($smsPurchase, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($smsPurchase, CURLOPT_SSL_VERIFYPEER, false);
	
$GetsmsJSON = curl_exec($smsPurchase);
$smsJSONObj = json_decode($GetsmsJSON,true);

if($GetsmsJSON == true){
	
	if(in_array($smsJSONObj["status"],array("success","pending"))){
		$log_sms_message = $smsJSONObj["desc"];
			$checkout_amount = $amount;
			$remain_balance = $wallet_balance-$checkout_amount;
			$ref_id = $smsJSONObj["ref"];
			if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true){
				if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', 'successful', 'SMS to $phone_number @ N$checkout_amount, Schedule Date: $schedule_date', 'sms', '$site_name')")){
				
				}
			}
	}
	
	if(!in_array($smsJSONObj["status"],array("success","pending"))){
		$log_sms_message = "Error: ".$smsJSONObj["desc"];
	}
	
}else{
	$log_sms_message = "Server currently unavailable";
}
		}else{
$log_sms_message = "Insufficient Funds";
		}

?>