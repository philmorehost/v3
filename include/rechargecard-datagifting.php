<?php
$raw_number = "123456789012345678901234567890";
$reference = substr(str_shuffle($raw_number), 0, 15);
$check_authorized_user_before_recharge = mysqli_query($conn_server_db, "SELECT * FROM authorized_rechargecard_user WHERE email='$user_session'");
if (mysqli_num_rows($check_authorized_user_before_recharge) >= 1) {
    $wallet_balance = $all_user_details["wallet_balance"];
    if ($wallet_balance >= $discounted_price_amount) {

        $rechargecardPurchase = curl_init();
        $rechargecardApiUrl = "https://v5.datagifting.com.ng/web/api/card.php";
        curl_setopt($rechargecardPurchase, CURLOPT_URL, $rechargecardApiUrl);
        curl_setopt($rechargecardPurchase, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($rechargecardPurchase, CURLOPT_POST, true);
        $pay_loads = json_encode(array(
            "api_key" => $apikey,
            "network" => $carrier,
            "qty_number" => $qty,
            "type" => "rechargecard",
            "quantity" => $amount,
            "card_name" => $site_name
        ));
        curl_setopt($rechargecardPurchase, CURLOPT_POSTFIELDS, $pay_loads);
        curl_setopt($rechargecardPurchase, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($rechargecardPurchase, CURLOPT_SSL_VERIFYPEER, false);

        $GetrechargecardJSON = curl_exec($rechargecardPurchase);
        $rechargecardJSONObj = json_decode($GetrechargecardJSON, true);

        if ($GetrechargecardJSON == true) {

            if (in_array($rechargecardJSONObj["status"], array("success", "pending"))) {
                $purchased_pin_in_line_break .= implode("\n", array_filter(explode(",", trim($rechargecardJSONObj["cards"]))));

                if(mysqli_query($conn_server_db, "INSERT INTO recharge_card_history (email, id, network_name, card_quality, card_array) VALUES ('$user_session', '$reference', '$carrier', '$amount', '$purchased_pin_in_line_break')") == true){
                }
                $log_rechargecard_message = "Recharge Card PINs generated Successfully";
                $checkout_amount = floatval($discounted_price_amount);
                $original_price_amount = ($amount * $qty);
                $remain_balance = $wallet_balance - $checkout_amount;
                $ref_id = $reference;
                if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$remain_balance' WHERE email='$user_session'") == true){
                    if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, d_amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$ref_id','$original_price_amount', '$checkout_amount', '$wallet_balance', '$remain_balance', 'Successful', 'N$amount ".strtoupper($carrier)." Recharge Card Qty of $qty @ N$checkout_amount', 'recharge-card', '$site_name')")){
                    
                    }
                }
            }
            if (!in_array($rechargecardJSONObj["status"], array("success", "pending"))) {
                $log_rechargecard_message = "Error: " . $rechargecardJSONObj["desc"];
            }

        } else {
            $log_rechargecard_message = "Server currently unavailable";
        }

    } else {
        $log_rechargecard_message = "Insufficient Funds";
    }
} else {
    $log_rechargecard_message = "Error: Your Account Has not been Activated for Recharge Card Printing, Contact The Admin to Activate it!";
}
?>