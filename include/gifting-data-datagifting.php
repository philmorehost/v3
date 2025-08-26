<?php
$daily_transaction_counter = json_decode(userDailyProductCounter($user_session, "gifting-data", $phone_number), true);
if ($daily_transaction_counter["status"] == true) {
$check_blocked_number_before_recharge = mysqli_query($conn_server_db, "SELECT * FROM blocked_phone WHERE phone_number='$phone_number'");
if(mysqli_num_rows($check_blocked_number_before_recharge) === 0){
$wallet_balance = $all_user_details["wallet_balance"];
if($wallet_balance >= $amount){
$dataPurchase = curl_init();
$dataApiUrl = "https://v5.datagifting.com.ng/web/api/data.php";
curl_setopt($dataPurchase,CURLOPT_URL,$dataApiUrl);
curl_setopt($dataPurchase,CURLOPT_RETURNTRANSFER,1);
curl_setopt($dataPurchase,CURLOPT_POST,1);
$pay_loads = json_encode(array(
	"api_key" => $apikey,
	"network" => $carrier,
	"phone_number" => $phone_number,
	"type" => "cg-data",
	"quantity" => $data_qty
));
curl_setopt($dataPurchase, CURLOPT_POSTFIELDS, $pay_loads);
curl_setopt($dataPurchase, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($dataPurchase, CURLOPT_SSL_VERIFYPEER, false);

$GetdataJSON = curl_exec($dataPurchase);
$dataJSONObj = json_decode($GetdataJSON,true);

if($GetdataJSON == true){
	
	if(in_array($dataJSONObj["status"],array("success", "pending"))){
		$log_gifting_data_message = $dataJSONObj["desc"];
			$checkout_amount = $amount;
			$remain_balance = $wallet_balance-$checkout_amount;
			$ref_id = $dataJSONObj["ref"];
			if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true){
				if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '".$dataJSONObj["status"]."', '".strtoupper($carrier)." ".str_replace("_"," ",$data_qty)." Gifting Data for $phone_number @ N$checkout_amount','gifting-data', '$site_name')")){
				
				}
			}
	}
	
	if(!in_array($dataJSONObj["status"],array("success", "pending"))){
		$log_gifting_data_message = "Error: ".$dataJSONObj["desc"];
	}
	
}else{
	$log_gifting_data_message = "Server currently unavailable";
}
		}else{
$log_gifting_data_message = "Insufficient Funds";
		}
}else{
	$log_gifting_data_message = "Error: Phone Number has been Blocked, Contact The Admin to Resolve it!";
}
} else {
	$log_gifting_data_message = "Error: Daily Limit for gifting data reached for " . $phone_number;
}
?>