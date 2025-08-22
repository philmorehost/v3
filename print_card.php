<?php session_start();
	if(!isset($_SESSION["user"])){
		header("Location: /login.php");
	}else{
		include(__DIR__."/include/config.php");
		include(__DIR__."/include/user-details.php");
	}
	
	//GET USER DETAILS
	$user_session = $_SESSION["user"];
	$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
    
    $transaction_ref = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["ref"]));
    $transaction_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT * FROM transaction_history WHERE email='$user_session' AND id='$transaction_ref'"));
    $transaction_desc = str_replace("@ N","<br><b>Amount Charged: </b>N",str_replace("Meter No:","<br><b>Meter Number:</b>",str_replace("Token:","<br><b>Token:</b>",str_replace("UNITS:","<br><b>Units Recharged:</b>",str_replace("Reference:","<br><b>Transaction Id:</b>",str_replace("Address:","<br><b>Customer Address:</b>",str_replace("Name:","<br><b>Customer Name:</b>",$transaction_details["description"])))))));

    $selectrechargecard_company_name = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT company_name FROM admin_recharge_card WHERE 1"));
    $get_purchased_rechargecard_pin_fetch = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_name, card_quality, card_array FROM recharge_card_history WHERE id='$transaction_ref'"));
        
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM site_info WHERE 1"))["sitetitle"]; ?> | Transaction Receipt</title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; " />
<meta name="theme-color" content="skyblue" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="/css/site.css">

<style>

.airtime-card-container{
	width: 98%;
	height: 132px;
	background: white;
	display: inline-block;
	border: 1px dashed black;
}

.airtime-carrier-logo{
	width:10%;
	height:auto;
	margin:10px 15px 5px 5px;
	float: right;
	border-radius: 30%;
}

.airtime-company-name{
	font-weight: bold;
	float: left;
	margin: 15px 5px 10px 5%;
	font-size:16px;
}

.airtime-amount{
	font-weight: bold;
	float: right;
	margin: 15px 5px 10px 0px;
	font-size:16px;
}

.airtime-ref{
	float: left;
	font-size:12px;
	clear:both;
	margin: 0px 0px 0.5px 15px;
}

.airtime-serial-no{
	float: left;
	font-size:12px;
	clear:both;
	margin: 0px 0px 0.5px 15px;
}

.airtime-pin{
	float: left;
	font-size:12px;
	clear:both;
	margin: 0px 0px 0.5px 15px;
}

.airtime-date{
	float: left;
	font-size:12px;
	clear:both;
	margin: 0px 0px 0.5px 15px;
}
.airtime-dial{
	float: left;
	font-size:12px;
	clear:both;
	margin: 0px 0px 0.5px 15px;
}

@media only screen and (min-width:980px) and (max-width:1600px){
.airtime-card-container{
	width: 24%;
	height: 110px;
	background: white;
	display: inline-block;
	border: 1px dashed black;
    padding:0 0 4px 0;
	margin: 0 0.4% 0 0;
}

.airtime-carrier-logo{
	width:10%;
	height:auto;
	margin:10px 15px 5px 5px;
	float: right;
	border-radius: 30%;
}

.airtime-company-name{
	font-weight: bold;
	float: left;
	margin: 15px 5px 3px 5%;
	font-size:10px;
}

.airtime-amount{
	font-weight: bold;
	float: right;
	margin: 15px 5px 3px 0px;
	font-size:9px;
}

.airtime-ref{
	float: left;
	font-size:9px;
	clear:both;
	margin: 0px 0px 0px 15px;
}

.airtime-serial-no{
	float: left;
	font-size:9px;
	clear:both;
	margin: 0px 0px 0px 15px;
}

.airtime-pin{
	float: left;
	font-size:9px;
	clear:both;
	margin: 0px 0px 0px 15px;
}

.airtime-date{
	float: left;
	font-size:9px;
	clear:both;
	margin: 0px 0px 0px 15px;
}
.airtime-dial{
	float: left;
	font-size:9px;
	clear:both;
	margin: 0px 0px 0px 15px;
}
}

</style>
<script src="/scripts/auth.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>
<body>
<?php include(__DIR__."/include/header-html.php"); ?>

<?php if(strtolower($transaction_details["transaction_type"]) === "recharge-card"){ ?>
<div id="receiptDiv" style="margin-top:5%; margin-left:2%; padding:3% 0% 3% 0%; width:96%; background: transparent; height: auto;">
    <?php
        $purchased_rechargecard_pin_array = array_filter(explode("\n",trim($get_purchased_rechargecard_pin_fetch["card_array"])));
        
        foreach($purchased_rechargecard_pin_array as $pins){
            if(strtolower($get_purchased_rechargecard_pin_fetch["network_name"]) == "mtn"){
                $net_code = "*555*PIN# or *311*PIN#";
            }

            if(strtolower($get_purchased_rechargecard_pin_fetch["network_name"]) == "airtel"){
                $net_code = "*126*PIN# or *311*PIN#";
            }

            if(strtolower($get_purchased_rechargecard_pin_fetch["network_name"]) == "glo"){
                $net_code = "*123*PIN# or *311*PIN#";
            }

            if(strtolower($get_purchased_rechargecard_pin_fetch["network_name"]) == "9mobile"){
                $net_code = "*222*PIN# or *311*PIN#";
            }

			$pin_and_serial = array_filter(explode(":",trim($pins)));

            echo '<div class="airtime-card-container ">
            <img class="airtime-carrier-logo " src="images/'.strtolower($get_purchased_rechargecard_pin_fetch["network_name"]).'.png">
            <span class="airtime-company-name ">'.substr($selectrechargecard_company_name["company_name"],0,20).'</span>
            <span class="airtime-amount "><strike>N</strike>'.$get_purchased_rechargecard_pin_fetch["card_quality"].'</span><br>
            <span class="airtime-ref ">Ref No: <strong>'.$transaction_ref.'</strong></span><br>
            <span class="airtime-serial-no ">Serial No: '.$pin_and_serial[1].'</span><br>
            <span class="airtime-pin ">PIN: <span style="font-size: 12px; font-weight: bold;">'.$pin_and_serial[0].'</span></span><br>
            <span class="airtime-date ">Date: '.$transaction_details["transaction_date"].'</span><br>
            <span class="airtime-date ">Dial: '.$net_code.'</span><br>
            
        </div>';
        }
    ?>
</div>
<center>
    <button type="button" id="print-btn" class="button-box color-8 bg-4 mobile-font-size-15 system-font-size-16 mobile-width-50 system-width-30" onclick="printPage();">Print Recharge Card</button>
</center>
<?php }else{ ?>
    <div class="container-box bg-8 mobile-margin-top-10 system-margin-top-10 mobile-margin-left-15 system-margin-left-15 mobile-padding-top-10 mobile-padding-left-10 mobile-padding-right-10 mobile-padding-bottom-10 system-padding-top-10 system-padding-left-10 system-padding-right-10 system-padding-bottom-10 mobile-width-50 system-width-50">
<center>
	<span class="mobile-font-size-20 system-font-size-25">Strictly for Recharge Card Printing Transaction Receipt Only!</b></span><br><br>
</center>
</div>
<?php } ?>

<script>
    function printPage(){
        
        var receiptDiv = document.getElementById("receiptDiv").innerHTML;
        const html = [];
        html.push('<html><head>');
        html.push('<link rel="stylesheet" href="/css/recharge.css">');
        html.push('</head><body onload="window.focus(); window.print()"><div>');
        html.push(receiptDiv);
        html.push('</div></body></html>');
        
        var mywindow = window.open('', '', 'width=640,height=480');
        mywindow.document.open("text/html");
        mywindow.document.write(html.join(""));
        mywindow.document.close();
        
    }
</script>

<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>