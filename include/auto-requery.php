<?php include("config.php");
    $select_transaction_history = mysqli_query($conn_server_db,"SELECT id FROM transaction_history WHERE (transaction_type!='commission' AND transaction_type!='refunded' AND transaction_type!='deduction' AND transaction_type!='debit' AND transaction_type!='credit' AND transaction_type!='') LIMIT 5");
    if(mysqli_num_rows($select_transaction_history) > 0){
        while($transaction_details = mysqli_fetch_assoc($select_transaction_history)){
            
            $url = $_SERVER["HTTP_HOST"]."/include/requery-transaction.php?requery=".$transaction_details["id"];
            $apiTransactionRequery = curl_init($url);
            $apiTransactionRequeryUrl = $url;
            curl_setopt($apiTransactionRequery,CURLOPT_URL,$apiTransactionRequeryUrl);
            curl_setopt($apiTransactionRequery,CURLOPT_RETURNTRANSFER,true);
            
            curl_setopt($apiTransactionRequery,CURLOPT_HTTPGET,true);
            
            curl_setopt($apiTransactionRequery, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($apiTransactionRequery, CURLOPT_SSL_VERIFYPEER, false);
            
            $GetAPIRequeryJSON = curl_exec($apiTransactionRequery);
        }
    }else{
        echo mysqli_error($conn_server_db);
    }


?>