<?php

$wallet_balance = $all_user_details["wallet_balance"];
if($wallet_balance >= $amount){
$exam_package_array = array("waec" => "result_checker", "neco" => "result_checker", "nabteb" => "result_checker");
$examPurchase = curl_init();
$examApiUrl = "https://v5.datagifting.com.ng/web/api/exam.php";
curl_setopt($examPurchase,CURLOPT_URL,$examApiUrl);
curl_setopt($examPurchase,CURLOPT_RETURNTRANSFER,true);
curl_setopt($examPurchase,CURLOPT_POST,true);
$pay_loads = json_encode(array(
	"api_key" => $apikey,
	"type" => $carrier,
	"quantity" => $exam_package_array[$package_name]
));
curl_setopt($examPurchase, CURLOPT_POSTFIELDS, $pay_loads);
curl_setopt($examPurchase, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($examPurchase, CURLOPT_SSL_VERIFYPEER, false);

$GetexamJSON = curl_exec($examPurchase);
$examJSONObj = json_decode($GetexamJSON,true);

if($GetexamJSON == true){
	
	if(in_array($examJSONObj["status"],array("success", "pending"))){
		$log_exam_message = $examJSONObj["desc"];
			$checkout_amount = $amount;
			$remain_balance = $wallet_balance-$checkout_amount;
			$ref_id = $examJSONObj["ref"];
			if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true){
				if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', 'successful', '".$examJSONObj["desc"]." @ N$checkout_amount', 'exam', '$site_name')")){
				
				}
			}
	}
	
	if(!in_array($examJSONObj["status"],array("success", "pending"))){
		$log_exam_message = "Error: ".$examJSONObj["desc"];
	}
	
}else{
	$log_exam_message = "Server currently unavailable";
}
		}else{
$log_exam_message = "Insufficient Funds";
		}

?>