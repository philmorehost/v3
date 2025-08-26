<?php

$wallet_balance = $all_user_details["wallet_balance"];
if($wallet_balance >= $amount){

$sms_apiKey = array_filter(explode(":",trim($apikey)))[0];
$sms_apiToken = array_filter(explode(":",trim($apikey)))[1];

$smsPurchase = curl_init();
$smsApiUrl = "https://smsc.philmoresms.com/smsAPI?sendsms&apikey=".$sms_apiKey."&apitoken=".$sms_apiToken."&type=sms&from=".$senderID."&to=".$phone_number."&text=".str_replace(" ","+",$smsText)."&scheduledate=".str_replace(" ","+",$schedule_date)."&route=0";
curl_setopt($smsPurchase,CURLOPT_URL,$smsApiUrl);
curl_setopt($smsPurchase,CURLOPT_RETURNTRANSFER,1);
curl_setopt($smsPurchase,CURLOPT_HTTPGET,1);
curl_setopt($smsPurchase, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($smsPurchase, CURLOPT_SSL_VERIFYPEER, false);
	
$GetsmsJSON = curl_exec($smsPurchase);
$smsJSONObj = json_decode($GetsmsJSON,true);

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

if($GetsmsJSON == true){
	
	if(in_array($smsJSONObj["status"],array("success","queued"))){
		$log_sms_message = $smsJSONObj["status"];
			$checkout_amount = $amount;
			$remain_balance = $wallet_balance-$checkout_amount;
			$ref_id = $smsJSONObj["group_id"];
			if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true){
				if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', 'successful', 'SMS to $phone_number @ N$checkout_amount, Schedule Date: $schedule_date', 'sms', '$site_name')")){
				
				}
			}
	}
	
	if(!in_array($smsJSONObj["status"],array("success","queued"))){
		$log_sms_message = "Error: ".$smsJSONObj["message"];
	}
	
}else{
	$log_sms_message = "Server currently unavailable";
}
		}else{
$log_sms_message = "Insufficient Funds";
		}

?>