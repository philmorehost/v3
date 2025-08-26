<?php

$wallet_balance = $all_user_details["wallet_balance"];
if($wallet_balance >= $amount){
$raw_number = "123456789012345678901234567890";
$reference = substr(str_shuffle($raw_number),0,15);

$examPurchase = curl_init();
$examApiUrl = "https://abumpay.com/api/education";
curl_setopt($examPurchase,CURLOPT_URL,$examApiUrl);
curl_setopt($examPurchase,CURLOPT_RETURNTRANSFER,true);
curl_setopt($examPurchase,CURLOPT_POST,true);
$examPurchaseData = json_encode(array("token" => $apikey,"service_id" => $carrier,"request_id" => $reference),true);
curl_setopt($examPurchase,CURLOPT_POSTFIELDS,$examPurchaseData);
curl_setopt($examPurchase, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($examPurchase, CURLOPT_SSL_VERIFYPEER, false);

$GetexamJSON = curl_exec($examPurchase);
$examJSONObj = json_decode($GetexamJSON,true);

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

if($GetexamJSON == true){
	
	if(in_array($examJSONObj["code"],array(200))){
		$log_exam_message = $examJSONObj["desc"];
			$checkout_amount = $amount;
			$remain_balance = $wallet_balance-$checkout_amount;
			$ref_id = $reference;
			if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true){
				if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', 'successful', '".$examJSONObj["desc"]." @ N$checkout_amount', 'exam', '$site_name')")){
				
				}
			}
	}
	
	if(!in_array($examJSONObj["code"],array(200))){
		$log_exam_message = "Error: ".$examJSONObj["desc"];
	}
	
}else{
	$log_exam_message = "Server currently unavailable";
}
		}else{
$log_exam_message = "Insufficient Funds";
		}

?>