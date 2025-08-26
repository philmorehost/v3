<?php

$wallet_balance = $all_user_details["wallet_balance"];
if($wallet_balance >= $amount){
$raw_number = "123456789012345678901234567890";
$reference = substr(str_shuffle($raw_number),0,15);

if($carrier == "waec"){
	$examTypeName = "waecdirect";
}

if($carrier == "neco"){
	$examTypeName = "";
}

if($carrier == "nabteb"){
	$examTypeName = "";
}

$clubKonnectUserID = array_filter(explode(":",trim($apikey)))[0];
$clubKonnectApikey = array_filter(explode(":",trim($apikey)))[1];


$examPurchase = curl_init();
$examApiUrl = "https://www.nellobytesystems.com/APIWAECV1.asp?UserID=".$clubKonnectUserID."&APIKey=".$clubKonnectApikey."&ExamType=".$examTypeName."&PhoneNo=08100000000&RequestID=".$reference."&CallBackURL=";
curl_setopt($examPurchase,CURLOPT_URL,$examApiUrl);
curl_setopt($examPurchase,CURLOPT_RETURNTRANSFER,1);
curl_setopt($examPurchase,CURLOPT_HTTPGET,1);
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
	
	if(in_array($examJSONObj["statuscode"],array(100,199,200,201,299))){
		$log_exam_message = $examJSONObj["ordertype"]." Successful";
			$checkout_amount = $amount;
			$remain_balance = $wallet_balance-$checkout_amount;
			$ref_id = $examJSONObj["orderid"];
			if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true){
				if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '".$examJSONObj["status"]."', '".$examJSONObj["ordertype"]." ".$examJSONObj["carddetails"]." @ N$checkout_amount', 'exam', '$site_name')")){
				
				}
			}
	}
	
	if(!in_array($examJSONObj["statuscode"],array(100,199,200,201,299))){
		$log_exam_message = "Error: ".$examJSONObj["status"];
	}
	
}else{
	$log_exam_message = "Server currently unavailable";
}
		}else{
$log_exam_message = "Insufficient Funds";
		}

?>