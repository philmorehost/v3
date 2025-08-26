<?php
$raw_number = "123456789012345678901234567890";
$reference = substr(str_shuffle($raw_number),0,15);

$check_authorized_user_before_recharge = mysqli_query($conn_server_db, "SELECT * FROM authorized_rechargecard_user WHERE email='$user_session'");
if(mysqli_num_rows($check_authorized_user_before_recharge) >= 1){
$wallet_balance = $all_user_details["wallet_balance"];
if($wallet_balance >= $discounted_price_amount){

	$get_manual_rechagecard_pin = mysqli_query($conn_server_db, "SELECT card_".$amount." FROM admin_recharge_card WHERE network_name='$carrier'");
	$get_manual_rechagecard_pin_fetch = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT card_".$amount." FROM admin_recharge_card WHERE network_name='$carrier'"));
        
    $manual_rechargecard_pin_array = array_filter(explode("\n",trim($get_manual_rechagecard_pin_fetch["card_".$amount])));
    $needed_rechargecard_pin = array_slice($manual_rechargecard_pin_array,0,$qty);
    $remaining_rechargecard_pin_for_sale = array_slice($manual_rechargecard_pin_array,$qty);
        
    if(count($manual_rechargecard_pin_array) >= $qty){
        
        foreach($needed_rechargecard_pin as $purchased_pin){
            $purchased_pin_in_line_break .= $purchased_pin."\n";
        }

        foreach($remaining_rechargecard_pin_for_sale as $for_sale_rechargecard_pin){
            $remaining_for_sale_pin_in_line_break .= $for_sale_rechargecard_pin."\n";
        }

        if(mysqli_query($conn_server_db, "UPDATE admin_recharge_card SET card_".$amount."='$remaining_for_sale_pin_in_line_break' WHERE network_name='$carrier'") == true){
        }

        if(mysqli_query($conn_server_db, "INSERT INTO recharge_card_history (email, id, network_name, card_quality, card_array) VALUES ('$user_session', '$reference', '$carrier', '$amount', '$purchased_pin_in_line_break')") == true){
        }
		$log_rechargecard_message = "Recharge Card PINs generated Successfully";
			$checkout_amount = $discounted_price_amount;
            $original_price_amount = ($amount*$qty);
			$remain_balance = $wallet_balance-$checkout_amount;
			$ref_id = $reference;
			if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true){
				if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$original_price_amount', '$checkout_amount', '$wallet_balance', '$remain_balance', 'Successful', 'N$amount ".strtoupper($carrier)." Recharge Card Qty of $qty @ N$checkout_amount', 'recharge-card', '$site_name')")){
				
				}
			}
	}
	
	if(count($manual_rechargecard_pin_array) < $qty){
		$log_rechargecard_message = "Oooops: Recharge Card Stock is Lower than $qty quantity, Check Back Later";
	}
	
}else{
    $log_rechargecard_message = "Insufficient Funds";
}
}else{
	$log_rechargecard_message = "Error: Your Account Has not been Activated for Recharge Card Printing, Contact The Admin to Activate it!";
}

?>