<?php

$wallet_balance = $all_user_details["wallet_balance"];
if($wallet_balance >= $amount){
$raw_number = "123456789012345678901234567890";
$reference = date("YmdHis").substr(str_shuffle($raw_number),0,15);

$insurancePurchase = curl_init();
$insuranceApiUrl = "https://vtpass.com/api/pay";
curl_setopt($insurancePurchase,CURLOPT_URL,$insuranceApiUrl);
curl_setopt($insurancePurchase,CURLOPT_RETURNTRANSFER,true);
curl_setopt($insurancePurchase,CURLOPT_POST,true);
$insuranceHeader = array("Authorization: Basic ".base64_encode($apikey),"Content-Type: application/json");
curl_setopt($insurancePurchase,CURLOPT_HTTPHEADER,$insuranceHeader);
$insurancePurchaseData = json_encode(array("request_id"=>$reference,"serviceID"=>"ui-insure","billersCode"=>$plate_number,"variation_code"=>$variation,"phone"=>$phone_number,"Insured_Name"=>$fullname,"Engine_Number"=>$engine_number,"Chasis_Number"=>$chasis_number,"Plate_Number"=>$plate_number,"Vehicle_Make"=>$vehicle_make,"Vehicle_Color"=>$vehicle_color,"Vehicle_Model"=>$vehicle_model,"Year_of_Make"=>$year,"Contact_Address"=>$address),true);
curl_setopt($insurancePurchase,CURLOPT_POSTFIELDS,$insurancePurchaseData);
curl_setopt($insurancePurchase, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($insurancePurchase, CURLOPT_SSL_VERIFYPEER, false);

$GetinsuranceJSON = curl_exec($insurancePurchase);
$insuranceJSONObj = json_decode($GetinsuranceJSON,true);

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

if($GetinsuranceJSON == true){
	
	if(in_array($insuranceJSONObj["code"],array("000","001","044","099"))){
		$log_insurance_message = $insuranceJSONObj["content"]["transactions"]["status"];
			$checkout_amount = $amount;
			$remain_balance = $wallet_balance-$checkout_amount;
			$ref_id = $reference;
			if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true){
				if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$amount', '$checkout_amount', '$wallet_balance', '$remain_balance', '".$insuranceJSONObj["content"]["transactions"]["status"]."', '".$insuranceJSONObj["content"]["transactions"]["product_name"]." with Unit Price: ".$insuranceJSONObj["content"]["transactions"]["unit_price"]." Unique: ".$insuranceJSONObj["content"]["transactions"]["unique_element"]." Download Cert: ".$insuranceJSONObj["certUrl"]." @ N$checkout_amount', 'motor-insurance', '$site_name')")){
				
				}
			}
	}
	
	if(!in_array($insuranceJSONObj["code"],array("000","001","044","099"))){
		$log_insurance_message = "Error: Can't Subscribe for Motor Car Insurance, Check the information properly and try again".$GetinsuranceJSON;
	}
	
}else{
	$log_insurance_message = "Server currently unavailable";
}
		}else{
$log_insurance_message = "Insufficient Funds";
		}

?>