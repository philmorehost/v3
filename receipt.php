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
<script src="/scripts/auth.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>
<body>
<?php include(__DIR__."/include/header-html.php"); ?>

<?php if(strtolower($transaction_details["transaction_type"]) === "electricity"){ ?>
<div id="receiptDiv" style="margin-top:5%; margin-left:15%; padding:10%; width:50%;" class="container-box bg-5">
<center>
    <img class="mobile-width-30 system-width-25" src="/images/logo.png"/><br>
	<span class="mobile-font-size-15 system-font-size-20"><b><?php echo strtoupper(explode(".",trim($_SERVER["HTTP_HOST"]))[0]); ?> TRANSACTION RECEIPT</b></span><br><br>
</center>
    <span class="color-9 mobile-font-size-12 system-font-size-16"><b>Description</b>: <?php echo $transaction_desc; ?></span><br>
    <span class="color-9 mobile-font-size-12 system-font-size-16"><b>Reference ID</b>: <?php echo $transaction_details["id"]; ?></span><br>
    <span class="color-9 mobile-font-size-12 system-font-size-16"><b>Status</b>: <?php echo $transaction_details["status"]; ?></span><br>
    <span class="color-9 mobile-font-size-12 system-font-size-16"><b>Amount Recharged</b>: N<?php echo $transaction_details["amount"]; ?></span><br>
    <span class="color-9 mobile-font-size-12 system-font-size-16"><b>Transaction Type</b>: <?php echo $transaction_details["transaction_type"]; ?></span><br>
    <span class="color-9 mobile-font-size-12 system-font-size-16"><b>Transaction Date</b>: <?php echo $transaction_details["transaction_date"]; ?></span><br><br>
<center>
    <button type="button" id="print-btn" class="button-box color-8 bg-4 mobile-font-size-15 system-font-size-16 mobile-width-70 system-width-35" onclick="printPage();">Print Receipt</button>
</center>
</div>
<?php }else{ ?>
    <div class="container-box bg-8 mobile-margin-top-10 system-margin-top-10 mobile-margin-left-15 system-margin-left-15 mobile-padding-top-10 mobile-padding-left-10 mobile-padding-right-10 mobile-padding-bottom-10 system-padding-top-10 system-padding-left-10 system-padding-right-10 system-padding-bottom-10 mobile-width-50 system-width-50">
<center>
	<span class="color-9 mobile-font-size-12 system-font-size-16">Strictly for Electricity Transaction Receipt Only!</b></span><br><br>
</center>
</div>
<?php } ?>

<script>
    function printPage(){
        var mywindow = window.open();
        var receiptDiv = document.getElementById("receiptDiv").innerHTML;
        mywindow.document.write("<div style='display:inline-block; width: 50%; margin: 0 0 0 20%;'>"+receiptDiv+"</div>");
        mywindow.print();
       
    }
</script>

<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>