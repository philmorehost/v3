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
	
	//GET EACH direct_data API WEBSITE
	$get_mtn_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='mtn'"));
	$get_airtel_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='airtel'"));
	$get_glo_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='glo'"));
	$get_9mobile_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='9mobile'"));
	
	//GET EACH direct_data APIKEY
	$mtn_api_website = $get_mtn_direct_data_running_api['website'];
	$airtel_api_website = $get_airtel_direct_data_running_api['website'];
	$glo_api_website = $get_glo_direct_data_running_api['website'];
	$etisalat_api_website = $get_9mobile_direct_data_running_api['website'];
	
	$get_mtn_direct_data_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM direct_data_api WHERE website='$mtn_api_website'"));
	$get_airtel_direct_data_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM direct_data_api WHERE website='$airtel_api_website'"));
	$get_glo_direct_data_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM direct_data_api WHERE website='$glo_api_website'"));
	$get_9mobile_direct_data_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM direct_data_api WHERE website='$etisalat_api_website'"));
	
	//GET EACH direct_data NETWORK STATUS
	$get_mtn_direct_data_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM direct_data_network_status WHERE network_name='mtn'"));
	$get_airtel_direct_data_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM direct_data_network_status WHERE network_name='airtel'"));
	$get_glo_direct_data_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM direct_data_network_status WHERE network_name='glo'"));
	$get_9mobile_direct_data_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM direct_data_network_status WHERE network_name='9mobile'"));
	
	if($all_user_details["account_type"] == "smart_earner"){
		$direct_discount = $get_mtn_direct_data_running_api["discount_1"];
	}
	
	if($all_user_details["account_type"] == "vip_earner"){
		$direct_discount = $get_mtn_direct_data_running_api["discount_2"];
	}
	
	if($all_user_details["account_type"] == "vip_vendor"){
		$direct_discount = $get_mtn_direct_data_running_api["discount_3"];
	}
	
	if($all_user_details["account_type"] == "api_earner"){
		$direct_discount = $get_mtn_direct_data_running_api["discount_4"];
	}
	
	if(isset($_POST["buy"])){
		$carrier = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["carrier"]));
		$phone_number = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["phone-number"]));
		$data_qty = strtolower(array_filter(explode(":",trim($_POST[$carrier])))[0]);
		$amount = (array_filter(explode(":",trim($_POST[$carrier])))[1]-(array_filter(explode(":",trim($_POST[$carrier])))[1]*$direct_discount/100));
		
		if($carrier == "mtn"){
			$site_name = $mtn_api_website;
			$apikey = $get_mtn_direct_data_apikey["apikey"];
		}
		
		if($carrier == "airtel"){
			$site_name = $airtel_api_website;
			$apikey = $get_airtel_direct_data_apikey["apikey"];
		}
		
		if($carrier == "glo"){
			$site_name = $glo_api_website;
			$apikey = $get_glo_direct_data_apikey["apikey"];
		}
		
		if($carrier == "9mobile"){
			$site_name = $etisalat_api_website;
			$apikey = $get_9mobile_direct_data_apikey["apikey"];
		}

		if($all_user_details["wallet_balance"] >= $amount){
			if($site_name == "smartrecharge.ng"){
				include("./include/direct-data-smartrecharge.php");
			}
		
			if($site_name == "benzoni.ng"){
				include("./include/direct-data-benzoni.php");
			}
		
			if($site_name == "grecians.ng"){
				include("./include/direct-data-grecians.php");
			}
			
			if($site_name == "smartrechargeapi.com"){
				include("./include/direct-data-smartrechargeapi.php");
			}
			
			if($site_name == "mobileone.ng"){
				include("./include/direct-data-mobileone.php");
			}

			if($site_name == "datagifting.com.ng"){
				include("./include/direct-data-datagifting.php");
			}
		}else{
			$log_direct_data_message = "Insufficient Fund, Fund Wallet And Try Again! ";
		}
		
		$_SESSION["transaction_text"] = $log_direct_data_message;
		header("Location: ".$_SERVER["REQUEST_URI"]);
	}
	
	
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM site_info WHERE 1"))["sitetitle"]; ?></title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; " />
<meta name="theme-color" content="skyblue" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="/css/site.css">
<script src="/scripts/carrier.js"></script>
<script src="/scripts/auth.js"></script>
<script src="/scripts/trans-pass.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>
<body>
<?php include(__DIR__."/include/header-html.php"); ?>

<center>
	<div class="container-box bg-4 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<form method="post">
			<?php if($_SESSION["transaction_text"] == true){ ?>
			<div name="message" id="font-color-1" class="message-box font-size-2"><?php echo $_SESSION["transaction_text"]; ?></div>
			<?php } ?>
			
			<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">BUY DIRECT DATA</span><br>
			<img onclick="carrierServiceName('mtnServNetImg','mtn');" id="mtnServNetImg" src="/images/mtn.png" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<img onclick="carrierServiceName('airtelServNetImg','airtel');" id="airtelServNetImg" src="/images/airtel.png" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<img onclick="carrierServiceName('gloServNetImg','glo');" id="gloServNetImg" src="/images/glo.png" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<img onclick="carrierServiceName('9mobileServNetImg','9mobile');" id="9mobileServNetImg" src="/images/9mobile.png" style="cursor: pointer;" class="mobile-width-15 system-width-15" /><br>
			<select name="carrier" onchange="updateCarrierAPIkey()" id="carrier-name" hidden>
				<option disabled hidden selected>Choose Carrier</option>
				<?php if($get_mtn_direct_data_network_status["network_status"] == "active"){ ?>
				<option value="mtn">MTN</option>
				<?php } ?>
				<?php if($get_airtel_direct_data_network_status["network_status"] == "active"){ ?>
				<option value="airtel">Airtel</option>
				<?php } ?>
				<?php if($get_glo_direct_data_network_status["network_status"] == "active"){ ?>
				<option value="glo">GLO</option>
				<?php } ?>
				<?php if($get_9mobile_direct_data_network_status["network_status"] == "active"){ ?>
				<option value="9mobile">9Mobile</option>
				<?php } ?>
			</select>
			<select style="display:none;" name="mtn" id="mtn" class="select-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
			<?php if($mtn_api_website == "datagifting.com.ng"){ ?>
				<option value="1gb_weekly:800">MTN 1GB 7Days @ <?php echo (800-(800*$direct_discount/100)); ?></option>
				<option value="11gb_weekly:3500">MTN 11GB 7Days @ <?php echo (3500-(3500*$direct_discount/100)); ?></option>
				<option value="2gb_monthly:1500">MTN 2GB 30Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="3.5gb_monthly:2500">MTN 3.5GB 30Days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="7gb_monthly:3500">MTN 7GB 30Days @ <?php echo (3500-(3500*$direct_discount/100)); ?></option>
				<option value="10gb_10mins_monthly:4500">MTN 10GB+10mins 30Days @ <?php echo (4500-(4500*$direct_discount/100)); ?></option>
				<option value="12.5gb_monthly:5500">MTN 12.5GB 30Days @ <?php echo (5500-(5500*$direct_discount/100)); ?></option>
				<option value="16.5gb_plus_10mins_monthly:6500">MTN 16.5GB+10mins 30Days @ <?php echo (6500-(6500*$direct_discount/100)); ?></option>
				<option value="20gb_monthly:7500">MTN 20GB 30Days @ <?php echo (7500-(7500*$direct_discount/100)); ?></option>
				<option value="36gb_30days:11000">MTN 36GB 30Days @ <?php echo (11000-(11000*$direct_discount/100)); ?></option>
				<option value="75gb_30days:18000">MTN 75GB 30days @ <?php echo (18000-(18000*$direct_discount/100)); ?></option>
				<option value="165gb_30days:35000">MTN 165GB 30Days @ <?php echo (35000-(35000*$direct_discount/100)); ?></option>
				<option value="150gb_60days:40000">MTN 150GB 60Days @ <?php echo (40000-(40000*$direct_discount/100)); ?></option>
				<option value="480gb_90days:90000">MTN 480GB 90days @ <?php echo (90000-(90000*$direct_discount/100)); ?></option>
				<option value="500mb_awoof_1day:350">MTN 500MB-Awoof 1day @ <?php echo (350-(350*$direct_discount/100)); ?></option>
				<option value="1gb_1.5mins_awoof_1day:500">MTN 1GB+1.5mins-Awoof 1days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="2.5gb_awoof_2days:900">MTN 2.5GB-Awoof 2days @ <?php echo (900-(100*$direct_discount/100)); ?></option>
				<option value="3.2gb_awoof_2days:1000">MTN 3.2GB-Awoof 2days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($mtn_api_website == "smartrecharge.ng"){ ?>
				<option value="mtn_20gb_30_days:6000">MTN 20GB 30 Days @ <?php echo (6000-(6000*$direct_discount/100)); ?></option>
				<option value="mtn_110gb_30days:20000">MTN 110GB 30days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="mtn_2gb_30_days:1200">MTN 2GB 30 Days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="mtn_40gb:10000">MTN 40GB @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="mtn_75gb_30days:15000">MTN 75GB 30days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="mtn_15gb_30_days:5000">MTN 15GB 30 Days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="mtn_25mb_24hrs:50">MTN 25MB 24hrs @ <?php echo (50-(50*$direct_discount/100)); ?></option>
				<option value="mtn_3gb_30days:1500">MTN 3GB 30days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="mtn_120gb_60days:30000">MTN 120GB 60Days @ <?php echo (30000-(30000*$direct_discount/100)); ?></option>
				<option value="mtn_150gb_90_days:50000">MTN 150GB 90 Days @ <?php echo (50000-(50000*$direct_discount/100)); ?></option>
				<option value="mtn_75mb_24hrs:100">  MTN 75MB 24hrs @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="mtn_1gb_24hrs:300">MTN 1GB 24hrs @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="mtn_200mb_2days:200">MTN 200MB 2days @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="mtn_2gb_2days:500">MTN 2GB 2days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_350mb_7days:300">MTN 350MB 7days @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="mtn_1gb_7days:500">MTN 1GB 7Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_6gb_30days:2500">MTN 6GB 30days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="mtn_15gb_30days:1000">MTN 1.5GB 30days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="mtn_75gb_60days:20000">MTN 75GB 60days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="mtn_250gb_90days:75000">MTN 250GB 90days @ <?php echo (75000-(75000*$direct_discount/100)); ?></option>
				<option value="mtn_400gb_365days:120000">MTN 400GB 365days @ <?php echo (120000-(120000*$direct_discount/100)); ?></option>
				<option value="mtn_1000gb_365days:250000">MTN 1000GB 365days @ <?php echo (250000-(250000*$direct_discount/100)); ?></option>
				<option value="mtn_2000gb_365days:450000">MTN 2000GB 365days @ <?php echo (450000-(450000*$direct_discount/100)); ?></option>
				<option value="mtn_45gb_30days:2000">MTN 4.5GB 30days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="mtn_6gb_7_days:1500">MTN 6GB 7 days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="mtn_10gb_30days:3500">MTN 10GB 30days @ <?php echo (3500-(3500*$direct_discount/100)); ?></option>
				<option value="mtn_750mb_14days:500">MTN 750MB 14days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_2_5gb_2days:500">MTN 2.5GB 2days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_8gb_30days:3000">MTN 8GB 30days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($mtn_api_website == "benzoni.ng"){ ?>
				<option value="mtn_3gb30days:1500">MTN 3GB/30Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="mtn_6gb30days:2500">MTN 6GB/30Days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="mtn_2_5gb2days:500">MTN 2.5GB/2Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_1_5gb_30days:1000">MTN 1.5GB 30DAYS @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="mtn_2gb_30days:1200">MTN 2GB 30DAYS @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="mtn_4_5gb_30days:2000">MTN 4.5GB 30DAYS @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="mtn_10gb_30days:3500">MTN 10GB 30DAYS @ <?php echo (3500-(3500*$direct_discount/100)); ?></option>
				<option value="mtn_15gb30days:5000">MTN 15GB/30DAYS @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="mtn_75gb30days:15000">MTN 75GB/30DAYS @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="mtn_75gb60days:20000">MTN 75GB/60DAYS @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="mtn_750mb_14days:500">MTN 750MB 14DAYS @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_40gb30days:10000">MTN 40GB/30DAYS @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="mtn_120gb_60days:30000">MTN 120GB 60DAYS @ <?php echo (30000-(30000*$direct_discount/100)); ?></option>
				<option value="mtn_8gb30days:3000">MTN 8GB/30Days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="mtn_20gb30days:6000">MTN 20GB/30Days @ <?php echo (6000-(6000*$direct_discount/100)); ?></option>
				<option value="mtn_110gb30days:20000">MTN 110GB/30Days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="mtn_30gb60days:8000">MTN 30GB/60days @ <?php echo (8000-(8000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($mtn_api_website == "grecians.ng"){ ?>
				<option value="mtn_3gb30days:1500">MTN 3GB/30Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="mtn_6gb30days:2500">MTN 6GB/30Days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="mtn_2_5gb2days:500">MTN 2.5GB/2Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_1_5gb_30days:1000">MTN 1.5GB 30DAYS @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="mtn_2gb_30days:1200">MTN 2GB 30DAYS @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="mtn_4_5gb_30days:2000">MTN 4.5GB 30DAYS @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="mtn_10gb_30days:3500">MTN 10GB 30DAYS @ <?php echo (3500-(3500*$direct_discount/100)); ?></option>
				<option value="mtn_15gb30days:5000">MTN 15GB/30DAYS @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="mtn_75gb30days:15000">MTN 75GB/30DAYS @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="mtn_75gb60days:20000">MTN 75GB/60DAYS @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="mtn_750mb_14days:500">MTN 750MB 14DAYS @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_40gb30days:10000">MTN 40GB/30DAYS @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="mtn_120gb_60days:30000">MTN 120GB 60DAYS @ <?php echo (30000-(30000*$direct_discount/100)); ?></option>
				<option value="mtn_8gb30days:3000">MTN 8GB/30Days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="mtn_20gb30days:6000">MTN 20GB/30Days @ <?php echo (6000-(6000*$direct_discount/100)); ?></option>
				<option value="mtn_110gb30days:20000">MTN 110GB/30Days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="mtn_30gb60days:8000">MTN 30GB/60days @ <?php echo (8000-(8000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($mtn_api_website == "smartrechargeapi.com"){ ?>
				<option value="mtn_20gb_30_days:6000">MTN 20GB 30 Days @ <?php echo (6000-(6000*$direct_discount/100)); ?></option>
				<option value="mtn_110gb_30days:20000">MTN 110GB 30days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="mtn_2gb_30_days:1200">MTN 2GB 30 Days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="mtn_40gb:10000">MTN 40GB @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="mtn_75gb_30days:15000">MTN 75GB 30days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="mtn_15gb_30_days:5000">MTN 15GB 30 Days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="mtn_25mb_24hrs:50">MTN 25MB 24hrs @ <?php echo (50-(50*$direct_discount/100)); ?></option>
				<option value="mtn_3gb_30days:1500">MTN 3GB 30days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="mtn_120gb_60days:30000">MTN 120GB 60Days @ <?php echo (30000-(30000*$direct_discount/100)); ?></option>
				<option value="mtn_150gb_90_days:50000">MTN 150GB 90 Days @ <?php echo (50000-(50000*$direct_discount/100)); ?></option>
				<option value="mtn_75mb_24hrs:100">  MTN 75MB 24hrs @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="mtn_1gb_24hrs:300">MTN 1GB 24hrs @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="mtn_200mb_2days:200">MTN 200MB 2days @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="mtn_2gb_2days:500">MTN 2GB 2days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_350mb_7days:300">MTN 350MB 7days @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="mtn_1gb_7days:500">MTN 1GB 7Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_6gb_30days:2500">MTN 6GB 30days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="mtn_15gb_30days:1000">MTN 1.5GB 30days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="mtn_75gb_60days:20000">MTN 75GB 60days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="mtn_250gb_90days:75000">MTN 250GB 90days @ <?php echo (75000-(75000*$direct_discount/100)); ?></option>
				<option value="mtn_400gb_365days:120000">MTN 400GB 365days @ <?php echo (120000-(120000*$direct_discount/100)); ?></option>
				<option value="mtn_1000gb_365days:250000">MTN 1000GB 365days @ <?php echo (250000-(250000*$direct_discount/100)); ?></option>
				<option value="mtn_2000gb_365days:450000">MTN 2000GB 365days @ <?php echo (450000-(450000*$direct_discount/100)); ?></option>
				<option value="mtn_45gb_30days:2000">MTN 4.5GB 30days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="mtn_6gb_7_days:1500">MTN 6GB 7 days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="mtn_10gb_30days:3500">MTN 10GB 30days @ <?php echo (3500-(3500*$direct_discount/100)); ?></option>
				<option value="mtn_750mb_14days:500">MTN 750MB 14days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_2_5gb_2days:500">MTN 2.5GB 2days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_8gb_30days:3000">MTN 8GB 30days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($mtn_api_website == "mobileone.ng"){ ?>
				<option value="mtn_20gb_30_days:6000">MTN 20GB 30 Days @ <?php echo (6000-(6000*$direct_discount/100)); ?></option>
				<option value="mtn_110gb_30days:20000">MTN 110GB 30days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="mtn_2gb_30_days:1200">MTN 2GB 30 Days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="mtn_40gb:10000">MTN 40GB @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="mtn_75gb_30days:15000">MTN 75GB 30days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="mtn_15gb_30_days:5000">MTN 15GB 30 Days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="mtn_25mb_24hrs:50">MTN 25MB 24hrs @ <?php echo (50-(50*$direct_discount/100)); ?></option>
				<option value="mtn_3gb_30days:1500">MTN 3GB 30days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="mtn_120gb_60days:30000">MTN 120GB 60Days @ <?php echo (30000-(30000*$direct_discount/100)); ?></option>
				<option value="mtn_150gb_90_days:50000">MTN 150GB 90 Days @ <?php echo (50000-(50000*$direct_discount/100)); ?></option>
				<option value="mtn_75mb_24hrs:100">  MTN 75MB 24hrs @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="mtn_1gb_24hrs:300">MTN 1GB 24hrs @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="mtn_200mb_2days:200">MTN 200MB 2days @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="mtn_2gb_2days:500">MTN 2GB 2days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_350mb_7days:300">MTN 350MB 7days @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="mtn_1gb_7days:500">MTN 1GB 7Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_6gb_30days:2500">MTN 6GB 30days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="mtn_15gb_30days:1000">MTN 1.5GB 30days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="mtn_75gb_60days:20000">MTN 75GB 60days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="mtn_250gb_90days:75000">MTN 250GB 90days @ <?php echo (75000-(75000*$direct_discount/100)); ?></option>
				<option value="mtn_400gb_365days:120000">MTN 400GB 365days @ <?php echo (120000-(120000*$direct_discount/100)); ?></option>
				<option value="mtn_1000gb_365days:250000">MTN 1000GB 365days @ <?php echo (250000-(250000*$direct_discount/100)); ?></option>
				<option value="mtn_2000gb_365days:450000">MTN 2000GB 365days @ <?php echo (450000-(450000*$direct_discount/100)); ?></option>
				<option value="mtn_45gb_30days:2000">MTN 4.5GB 30days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="mtn_6gb_7_days:1500">MTN 6GB 7 days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="mtn_10gb_30days:3500">MTN 10GB 30days @ <?php echo (3500-(3500*$direct_discount/100)); ?></option>
				<option value="mtn_750mb_14days:500">MTN 750MB 14days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_2_5gb_2days:500">MTN 2.5GB 2days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="mtn_8gb_30days:3000">MTN 8GB 30days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
			<?php } ?>
			</select>
			
			<select style="display:none;" name="airtel" id="airtel" class="select-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
			<?php if($airtel_api_website == "smartrecharge.ng"){ ?>
				<option value="airtel_1_5gb:1000">Airtel 1.5GB @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="airtel_3gb_30days:1500">Airtel 3GB 30days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="airtel_6gb_7days:1500">Airtel 6GB 7Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="airtel_4_5gb_30days:2000">Airtel 4.5GB 30days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="airtel_110gb_30days:20000">Airtel 110GB 30days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="airtel_750mb:500">Airtel 750MB @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="airtel_75mb10_extra_24hrs:100">Airtel 75MB+10% Extra 24hrs @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="airtel_200mb_3days:200">Airtel 200MB 3Days @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="airtel_350mb__10_extra_7days:300">Airtel 350MB + 10% Extra 7days @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="airtel_40gb_30days:10000">Airtel 40GB 30days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="airtel_8gb_30days:3000">Airtel 8GB 30days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="airtel_11gb_30days:4000">Airtel 11GB 30days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="airtel_75gb_30days:15000">Airtel 75GB 30days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="airtel_1gb__1day:300">Airtel 1GB  1day @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="airtel_2gb__2days:500">Airtel 2GB  2Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="airtel_2gb__30days:1200">Airtel 2GB  30days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="airtel_6gb__30days:2500">Airtel 6GB  30days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="airtel_15gb:5000">Airtel 15GB  @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($airtel_api_website == "benzoni.ng"){ ?>
				<option value="airtel_1_5gb30days:1000">Airtel 1.5GB/30Days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="airtel_15gb30days:5000">Airtel 15GB/30DAYS @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="airtel_40gb30days:10000">Airtel 40GB/30DAYS @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="airtel_6gb30days:2500">Airtel 6GB/30days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="airtel_8gb30days:3000">Airtel 8GB/30days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="airtel_11gb30days:4000">Airtel 11GB/30days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="airtel_4_5gb30days:2000">Airtel 4.5GB/30days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="airtel_750mb14days:500">Airtel 750MB/14days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="airtel_2gb30days:1200">Airtel 2GB/30days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="airtel_75gb30days:15000">Airtel 75GB/30days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="airtel_110gb30days:20000">Airtel 110GB/30days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($airtel_api_website == "datagifting.com.ng"){ ?>
				<option value="500mb_7days:500">Airtel 500MB/7Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="1gb_7days:800">Airtel 1GB/7DAYS @ <?php echo (800-(800*$direct_discount/100)); ?></option>
				<option value="1.5gb_7days:1000">Airtel 1.5GB/7DAYS @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="3.5gb_7days:1500">Airtel 3.5GB/7days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="6gb_7days:2500">Airtel 6GB/7days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="10gb_7days:3000">Airtel 10GB/7days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="18gb_7days:5000">Airtel 18GB/7days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="2gb_30days:1500">Airtel 2GB/30days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="3gb_30days:2000">Airtel 3GB/30days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="4gb_30days:2500">Airtel 4GB/30days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="8gb_30days:3000">Airtel 8GB/30days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="10gb_30days:4000">Airtel 10GB/30days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="13gb_30days:5000">Airtel 13GB/30days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="18gb_30days:6000">Airtel 18GB/30days @ <?php echo (6000-(6000*$direct_discount/100)); ?></option>
				<option value="25gb_30days:8000">Airtel 25GB/30days @ <?php echo (8000-(8000*$direct_discount/100)); ?></option>
				<option value="35gb_30days:10000">Airtel 35GB/30days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="60gb_30days:15000">Airtel 60GB/30days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="100gb_30days:20000">Airtel 100GB/30days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="160gb_30days:30000">Airtel 160GB/30days @ <?php echo (30000-(30000*$direct_discount/100)); ?></option>
				<option value="210gb_30days:40000">Airtel 210GB/30days @ <?php echo (40000-(40000*$direct_discount/100)); ?></option>
				<option value="300gb_30days:50000">Airtel 300GB/30days @ <?php echo (50000-(50000*$direct_discount/100)); ?></option>
				<option value="350gb_30days:60000">Airtel 350GB/30days @ <?php echo (60000-(60000*$direct_discount/100)); ?></option>
				<option value="1gb_awoof_2days:500">Airtel 500MB-Awoof/2days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="1.5gb_awoof_2days:600">Airtel 1.5GB-Awoof/2days @ <?php echo (600-(600*$direct_discount/100)); ?></option>
				<option value="2gb_awoof_2days:750">Airtel 2GB-Awoof/2days @ <?php echo (750-(750*$direct_discount/100)); ?></option>
				<option value="3gb_awoof_2days:1000">Airtel 3GB-Awoof/2days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="5gb_awoof_2days:1500">Airtel 3GB-Awoof/2days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($airtel_api_website == "grecians.ng"){ ?>
				<option value="airtel_1_5gb30days:1000">Airtel 1.5GB/30Days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="airtel_15gb30days:5000">Airtel 15GB/30DAYS @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="airtel_40gb30days:10000">Airtel 40GB/30DAYS @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="airtel_6gb30days:2500">Airtel 6GB/30days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="airtel_8gb30days:3000">Airtel 8GB/30days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="airtel_11gb30days:4000">Airtel 11GB/30days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="airtel_4_5gb30days:2000">Airtel 4.5GB/30days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="airtel_750mb14days:500">Airtel 750MB/14days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="airtel_2gb30days:1200">Airtel 2GB/30days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="airtel_75gb30days:15000">Airtel 75GB/30days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="airtel_110gb30days:20000">Airtel 110GB/30days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($airtel_api_website == "smartrechargeapi.com"){ ?>
				<option value="airtel_1_5gb:1000">Airtel 1.5GB @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="airtel_3gb_30days:1500">Airtel 3GB 30days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="airtel_6gb_7days:1500">Airtel 6GB 7Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="airtel_4_5gb_30days:2000">Airtel 4.5GB 30days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="airtel_110gb_30days:20000">Airtel 110GB 30days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="airtel_750mb:500">Airtel 750MB @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="airtel_75mb10_extra_24hrs:100">Airtel 75MB+10% Extra 24hrs @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="airtel_200mb_3days:200">Airtel 200MB 3Days @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="airtel_350mb__10_extra_7days:300">Airtel 350MB + 10% Extra 7days @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="airtel_40gb_30days:10000">Airtel 40GB 30days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="airtel_8gb_30days:3000">Airtel 8GB 30days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="airtel_11gb_30days:4000">Airtel 11GB 30days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="airtel_75gb_30days:15000">Airtel 75GB 30days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="airtel_1gb__1day:300">Airtel 1GB  1day @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="airtel_2gb__2days:500">Airtel 2GB  2Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="airtel_2gb__30days:1200">Airtel 2GB  30days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="airtel_6gb__30days:2500">Airtel 6GB  30days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="airtel_15gb:5000">Airtel 15GB  @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($airtel_api_website == "mobileone.ng"){ ?>
				<option value="airtel_1_5gb:1000">Airtel 1.5GB @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="airtel_3gb_30days:1500">Airtel 3GB 30days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="airtel_6gb_7days:1500">Airtel 6GB 7Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="airtel_4_5gb_30days:2000">Airtel 4.5GB 30days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="airtel_110gb_30days:20000">Airtel 110GB 30days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="airtel_750mb:500">Airtel 750MB @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="airtel_75mb10_extra_24hrs:100">Airtel 75MB+10% Extra 24hrs @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="airtel_200mb_3days:200">Airtel 200MB 3Days @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="airtel_350mb__10_extra_7days:300">Airtel 350MB + 10% Extra 7days @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="airtel_40gb_30days:10000">Airtel 40GB 30days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="airtel_8gb_30days:3000">Airtel 8GB 30days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="airtel_11gb_30days:4000">Airtel 11GB 30days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="airtel_75gb_30days:15000">Airtel 75GB 30days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="airtel_1gb__1day:300">Airtel 1GB  1day @ <?php echo (300-(300*$direct_discount/100)); ?></option>
				<option value="airtel_2gb__2days:500">Airtel 2GB  2Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="airtel_2gb__30days:1200">Airtel 2GB  30days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="airtel_6gb__30days:2500">Airtel 6GB  30days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="airtel_15gb:5000">Airtel 15GB  @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
			<?php } ?>
			</select>
			
			<select style="display:none;" name="glo" id="glo" class="select-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
			<?php if($glo_api_website == "datagifting.com.ng"){ ?>
				<option value="glo_875mb_awoof_weekend_sun:200">GLO 875MB-Awoof Weekend-Sun @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="glo_2.5gb_awoof_weekend_sat-sun:500">GLO 2.5GB-Awoof Weekend-Sat-Sun @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="glo_125mb_awoof_1day:100">GLO 125MB-Awoof 1Day @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="glo_2gb_awoof_1day:500">GLO 2GB-Awoof 1Day @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="glo_260mb_awoof_2days:10000">GLO 260MB-Awoof 2Days @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="glo_6gb_7days:1500">GLO 6GB 7Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo_1.5gb_14days:500">GLO 1.5GB 14Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="glo_2.6gb_30days:1000">GLO 2.6GB 30Days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="glo_5gb_30days:1500">GLO 5GB 30Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo_6.15gb_30days:2000">GLO 6.15GB 30Days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="glo_7.5gb_30days:2500">GLO 7.5GB 30Days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="glo_10gb_30days:3000">GLO 10GB 30Days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="glo_12.5gb_30days:4000">GLO 12.5GB 30Days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="glo_16gb_30days:5000">GLO 16GB 30Days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="glo_28gb_30days:8000">GLO 28GB 30Days @ <?php echo (8000-(8000*$direct_discount/100)); ?></option>
				<option value="glo_38gb_30days:10000">GLO 38GB 30Days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="glo_64gb_30days:15000">GLO 64GB 30Days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="glo_107gb_30days:20000">GLO 107GB 30Days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="glo_165gb_30days:30000">GLO 165GB 30Days @ <?php echo (30000-(30000*$direct_discount/100)); ?></option>
				<option value="glo_220gb_30days:40000">GLO 220GB 30Days @ <?php echo (40000-(40000*$direct_discount/100)); ?></option>
				<option value="glo_320gb_30days:50000">GLO 320GB 30Days @ <?php echo (50000-(50000*$direct_discount/100)); ?></option>
				<option value="glo_380gb_30days:60000">GLO 380GB 30Days @ <?php echo (60000-(60000*$direct_discount/100)); ?></option>
				<option value="glo_475gb_30days:75000">GLO 475GB 30Days @ <?php echo (75000-(75000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($glo_api_website == "smartrecharge.ng"){ ?>
				<option value="glo_2gb_2days:500">GLO 2GB 2Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="glo_100mb_1_day:100">GLO 100MB 1 Day @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="glo_350mb_2_days:200">GLO 350MB 2 DAYS @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="glo_1_35gb_14days:500">GLO 1.35GB 14Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="glo_2_5gb:1000">GLO 2.5GB @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="glo_5_8_gb:2000">GLO 5.8 GB @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="glo_7_7_gb:2500">GLO 7.7 GB @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="glo_10gb:3000">GLO 10GB @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="glo_13_5_gb:4000">GLO 13.5 GB @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="glo_1825gb:5000">GLO 18.25GB @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="glo_295gb:8000">GLO 29.5GB @ <?php echo (8000-(8000*$direct_discount/100)); ?></option>
				<option value="glo_50gb:10000">GLO 50GB @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="glo_93gb:15000">GLO 93GB @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="glo_119gb:18000">GLO 119GB @ <?php echo (18000-(18000*$direct_discount/100)); ?></option>
				<option value="glo_50mb_1_day:50">GLO 50MB 1 Day @ <?php echo (50-(50*$direct_discount/100)); ?></option>
				<option value="glo_138gb:20000">GLO 138GB @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="glo_3_75gb:1500">GLO 3.75GB @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo_special_1_gb_special1day:200">GLO SPECIAL 1 GB Special/1Day @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="glo__7_gb_special7days:1500">GLO  7 GB Special/7Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo__3_58_gb_oneoff30days:1500">GLO  3.58 GB Oneoff/30Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo_225gb30days:30000">GLO 225GB/30Days @ <?php echo (30000-(30000*$direct_discount/100)); ?></option>
				<option value="glo_300gb30days:36000">GLO 300GB/30Days @ <?php echo (36000-(36000*$direct_discount/100)); ?></option>
				<option value="glo_425gb90days:50000">GLO 425GB/90Days @ <?php echo (50000-(50000*$direct_discount/100)); ?></option>
				<option value="glo_525gb90days:60000">GLO 525GB/90Days @ <?php echo (60000-(60000*$direct_discount/100)); ?></option>
				<option value="glo_675gb120days:75000">GLO 675GB/120Days @ <?php echo (75000-(75000*$direct_discount/100)); ?></option>
				<option value="glo_1024gb365days:100000">GLO 1024GB/365Days @ <?php echo (100000-(100000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($glo_api_website == "benzoni.ng"){ ?>
				<option value="glo_2_5gb30days:1000">Glo 2.5GB/30Days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="glo_5_8gb30days:2000">Glo 5.8GB/30Days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="glo_7_7gb30days:2500">Glo 7.7GB/30Days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="glo_10gbdays:3000">Glo 10GB/30Days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="glo_13_25gb30days:4000">Glo 13.25GB/30Days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="glo_18_25gb30days:5000">Glo 18.25GB/30Days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="glo_50gb30days:10000">Glo 50GB/30Days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="glo_93gb30days:15000">Glo 93GB/30Days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="glo_119gb30days:18000">Glo 119GB/30Days @ <?php echo (18000-(18000*$direct_discount/100)); ?></option>
				<option value="glo_138gb30days:20000">Glo 138GB/30Days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="glo_29_5gb30days:8000">GLO 29.5GB/30Days @ <?php echo (8000-(8000*$direct_discount/100)); ?></option>
				<option value="glo_4_1gb30days:1500">GLO 4.1GB/30Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo_1_05gb14days:500">GLO 1.05GB/14Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($glo_api_website == "grecians.ng"){ ?>
				<option value="glo_2_5gb30days:1000">Glo 2.5GB/30Days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="glo_5_8gb30days:2000">Glo 5.8GB/30Days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="glo_7_7gb30days:2500">Glo 7.7GB/30Days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="glo_10gbdays:3000">Glo 10GB/30Days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="glo_13_25gb30days:4000">Glo 13.25GB/30Days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="glo_18_25gb30days:5000">Glo 18.25GB/30Days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="glo_50gb30days:10000">Glo 50GB/30Days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="glo_93gb30days:15000">Glo 93GB/30Days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="glo_119gb30days:18000">Glo 119GB/30Days @ <?php echo (18000-(18000*$direct_discount/100)); ?></option>
				<option value="glo_138gb30days:20000">Glo 138GB/30Days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="glo_29_5gb30days:8000">GLO 29.5GB/30Days @ <?php echo (8000-(8000*$direct_discount/100)); ?></option>
				<option value="glo_4_1gb30days:1500">GLO 4.1GB/30Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo_1_05gb14days:500">GLO 1.05GB/14Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($glo_api_website == "smartrechargeapi.com"){ ?>
				<option value="glo_2gb_2days:500">GLO 2GB 2Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="glo_100mb_1_day:100">GLO 100MB 1 Day @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="glo_350mb_2_days:200">GLO 350MB 2 DAYS @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="glo_1_35gb_14days:500">GLO 1.35GB 14Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="glo_2_5gb:1000">GLO 2.5GB @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="glo_5_8_gb:2000">GLO 5.8 GB @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="glo_7_7_gb:2500">GLO 7.7 GB @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="glo_10gb:3000">GLO 10GB @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="glo_13_5_gb:4000">GLO 13.5 GB @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="glo_1825gb:5000">GLO 18.25GB @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="glo_295gb:8000">GLO 29.5GB @ <?php echo (8000-(8000*$direct_discount/100)); ?></option>
				<option value="glo_50gb:10000">GLO 50GB @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="glo_93gb:15000">GLO 93GB @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="glo_119gb:18000">GLO 119GB @ <?php echo (18000-(18000*$direct_discount/100)); ?></option>
				<option value="glo_50mb_1_day:50">GLO 50MB 1 Day @ <?php echo (50-(50*$direct_discount/100)); ?></option>
				<option value="glo_138gb:20000">GLO 138GB @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="glo_3_75gb:1500">GLO 3.75GB @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo_special_1_gb_special1day:200">GLO SPECIAL 1 GB Special/1Day @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="glo__7_gb_special7days:1500">GLO  7 GB Special/7Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo__3_58_gb_oneoff30days:1500">GLO  3.58 GB Oneoff/30Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo_225gb30days:30000">GLO 225GB/30Days @ <?php echo (30000-(30000*$direct_discount/100)); ?></option>
				<option value="glo_300gb30days:36000">GLO 300GB/30Days @ <?php echo (36000-(36000*$direct_discount/100)); ?></option>
				<option value="glo_425gb90days:50000">GLO 425GB/90Days @ <?php echo (50000-(50000*$direct_discount/100)); ?></option>
				<option value="glo_525gb90days:60000">GLO 525GB/90Days @ <?php echo (60000-(60000*$direct_discount/100)); ?></option>
				<option value="glo_675gb120days:75000">GLO 675GB/120Days @ <?php echo (75000-(75000*$direct_discount/100)); ?></option>
				<option value="glo_1024gb365days:100000">GLO 1024GB/365Days @ <?php echo (100000-(100000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($glo_api_website == "mobileone.ng"){ ?>
				<option value="glo_2gb_2days:500">GLO 2GB 2Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="glo_100mb_1_day:100">GLO 100MB 1 Day @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="glo_350mb_2_days:200">GLO 350MB 2 DAYS @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="glo_1_35gb_14days:500">GLO 1.35GB 14Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="glo_2_5gb:1000">GLO 2.5GB @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="glo_5_8_gb:2000">GLO 5.8 GB @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="glo_7_7_gb:2500">GLO 7.7 GB @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="glo_10gb:3000">GLO 10GB @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="glo_13_5_gb:4000">GLO 13.5 GB @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="glo_1825gb:5000">GLO 18.25GB @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="glo_295gb:8000">GLO 29.5GB @ <?php echo (8000-(8000*$direct_discount/100)); ?></option>
				<option value="glo_50gb:10000">GLO 50GB @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="glo_93gb:15000">GLO 93GB @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="glo_119gb:18000">GLO 119GB @ <?php echo (18000-(18000*$direct_discount/100)); ?></option>
				<option value="glo_50mb_1_day:50">GLO 50MB 1 Day @ <?php echo (50-(50*$direct_discount/100)); ?></option>
				<option value="glo_138gb:20000">GLO 138GB @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="glo_3_75gb:1500">GLO 3.75GB @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo_special_1_gb_special1day:200">GLO SPECIAL 1 GB Special/1Day @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="glo__7_gb_special7days:1500">GLO  7 GB Special/7Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo__3_58_gb_oneoff30days:1500">GLO  3.58 GB Oneoff/30Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="glo_225gb30days:30000">GLO 225GB/30Days @ <?php echo (30000-(30000*$direct_discount/100)); ?></option>
				<option value="glo_300gb30days:36000">GLO 300GB/30Days @ <?php echo (36000-(36000*$direct_discount/100)); ?></option>
				<option value="glo_425gb90days:50000">GLO 425GB/90Days @ <?php echo (50000-(50000*$direct_discount/100)); ?></option>
				<option value="glo_525gb90days:60000">GLO 525GB/90Days @ <?php echo (60000-(60000*$direct_discount/100)); ?></option>
				<option value="glo_675gb120days:75000">GLO 675GB/120Days @ <?php echo (75000-(75000*$direct_discount/100)); ?></option>
				<option value="glo_1024gb365days:100000">GLO 1024GB/365Days @ <?php echo (100000-(100000*$direct_discount/100)); ?></option>
			<?php } ?>
			</select>
			
			<select style="display:none;" name="9mobile" id="9mobile" class="select-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
			<?php if($etisalat_api_website == "datagifting.com.ng"){ ?>
				<option value="100mb_awoof_1day:100">9mobile 10MB-AWOOF 1Day @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="180mb_awoof_1day:150">9Mobile 180MB-AWOOF 1Day @ <?php echo (150-(150*$direct_discount/100)); ?></option>
				<option value="250mb_awoof_1day:200">9Mobile 250MB-AWOOF 1Day @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="450mb_awoof_1day:350">9Mobile 450MB-AWOOF 1Day @ <?php echo (350-(350*$direct_discount/100)); ?></option>
				<option value="650mb_awoof_1day:500">9Mobile 650MB-AWOOF 2Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="1.75gb_7days:1500">9Mobile 1.75GB 7Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="650mb_14days:600">9Mobile 650MB 14Days @ <?php echo (600-(600*$direct_discount/100)); ?></option>
				<option value="1.1gb_30days:1000">9Mobile 1.1GB 30Days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="1.4gb_30days:1200">9Mobile 1.4GB 30Days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="2.44gb_30days:2000">9Mobile 2.44GB 30Days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="3.17gb_30days:2500">9Mobile 3.17GB 30Days @ <?php echo (2500-(2500*$direct_discount/100)); ?></option>
				<option value="3.91gb_30days:3000">9Mobile 3.91GB 30Days @ <?php echo (3000-(3000*$direct_discount/100)); ?></option>
				<option value="5.10gb_30days:4000">9Mobile 5.10GB 30Days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="6.5gb_30days:4000">9Mobile 6.5GB 30Days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="16gb_30days:12000">9Mobile 16GB 30Days @ <?php echo (12000-(12000*$direct_discount/100)); ?></option>
				<option value="24.3gb_30days:18000">9Mobile 24.3GB 30Days @ <?php echo (18000-(18000*$direct_discount/100)); ?></option>
				<option value="26.5gb_30days:20000">9Mobile 26.5GB 30Days @ <?php echo (20000-(20000*$direct_discount/100)); ?></option>
				<option value="39gb_60days:30000">9Mobile 39GB 60Days @ <?php echo (30000-(30000*$direct_discount/100)); ?></option>
				<option value="78gb_90days:60000">9Mobile 78GB 90Days @ <?php echo (60000-(60000*$direct_discount/100)); ?></option>
				<option value="190gb_180days:150000">9Mobile 190GB 180Days @ <?php echo (150000-(150000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($etisalat_api_website == "smartrecharge.ng"){ ?>
				<option value="9mobile_15gb_30days:5000">9mobile 15GB 30Days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="9mobile_40_gb_30_days:10000">9mobile 40 GB 30 Days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="9mobile_75_gb_30_days:15000">9mobile 75 GB 30 Days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="9mobile_7gb_7_days:1500">9mobile 7GB 7 Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="9mobile_120gb_365_days:110000">9Mobile 120GB 365 Days @ <?php echo (110000-(110000*$direct_discount/100)); ?></option>
				<option value="9mobile_100mb_24hrs:100">9mobile 100MB 24hrs @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="9mobile_1_5gb_30_days:1000">9mobile 1.5GB 30 Days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="9mobile_3gb_30_days:1500">9mobile 3GB 30 Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="9mobile_2gb_30days:1200">9mobile 2gb 30days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="9mobile_100gb_100_days:84992">9Mobile 100GB 100 Days @ <?php echo (84992-(84992*$direct_discount/100)); ?></option>
				<option value="9mobile_60gb_180_days:55000">9Mobile 60GB 180 Days @ <?php echo (55000-(55000*$direct_discount/100)); ?></option>
				<option value="9mobile_500mb_30days:500">9mobile 500MB 30Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="9mobile_4_5gb_30_days:2000">9mobile 4.5GB 30 Days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="9mobile_30gb_90_days:27500">9mobile 30GB 90 Days @ <?php echo (27500-(27500*$direct_discount/100)); ?></option>
				<option value="9mobile_650mb_24hrs:200">9mobile 650MB 24hrs @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="9mobile_25mb_24_hrs:50">9mobile 25MB 24 hrs @ <?php echo (50-(50*$direct_discount/100)); ?></option>
				<option value="9mobile_11gb_30days:4000">9Mobile 11GB 30days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($etisalat_api_website == "benzoni.ng"){ ?>
				<option value="9mobile_2gb30days:1200">9mobile 2GB/30Days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="9mobile_4_5gb30days:2000">9mobile 4.5GB/30Days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="9mobile_11gb30days:4000">9mobile 11GB/30Days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="9mobile_75gb30days:15000">9mobile 75GB/30Days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="9mobile_500mb30days:500">9mobile 500MB/30Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="9mobile_1_5gb30days:1000">9Mobile 1.5GB/30days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="9mobile_40gb30days:10000">9Mobile 40GB/30days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="9mobile_3gb30days:1500">9Mobile 3GB/30days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($etisalat_api_website == "grecians.ng"){ ?>
				<option value="9mobile_2gb30days:1200">9mobile 2GB/30Days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="9mobile_4_5gb30days:2000">9mobile 4.5GB/30Days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="9mobile_11gb30days:4000">9mobile 11GB/30Days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
				<option value="9mobile_75gb30days:15000">9mobile 75GB/30Days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="9mobile_500mb30days:500">9mobile 500MB/30Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="9mobile_1_5gb30days:1000">9Mobile 1.5GB/30days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="9mobile_40gb30days:10000">9Mobile 40GB/30days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="9mobile_3gb30days:1500">9Mobile 3GB/30days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($etisalat_api_website == "smartrechargeapi.com"){ ?>
				<option value="9mobile_15gb_30days:5000">9mobile 15GB 30Days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="9mobile_40_gb_30_days:10000">9mobile 40 GB 30 Days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="9mobile_75_gb_30_days:15000">9mobile 75 GB 30 Days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="9mobile_7gb_7_days:1500">9mobile 7GB 7 Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="9mobile_120gb_365_days:110000">9Mobile 120GB 365 Days @ <?php echo (110000-(110000*$direct_discount/100)); ?></option>
				<option value="9mobile_100mb_24hrs:100">9mobile 100MB 24hrs @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="9mobile_1_5gb_30_days:1000">9mobile 1.5GB 30 Days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="9mobile_3gb_30_days:1500">9mobile 3GB 30 Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="9mobile_2gb_30days:1200">9mobile 2gb 30days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="9mobile_100gb_100_days:84992">9Mobile 100GB 100 Days @ <?php echo (84992-(84992*$direct_discount/100)); ?></option>
				<option value="9mobile_60gb_180_days:55000">9Mobile 60GB 180 Days @ <?php echo (55000-(55000*$direct_discount/100)); ?></option>
				<option value="9mobile_500mb_30days:500">9mobile 500MB 30Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="9mobile_4_5gb_30_days:2000">9mobile 4.5GB 30 Days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="9mobile_30gb_90_days:27500">9mobile 30GB 90 Days @ <?php echo (27500-(27500*$direct_discount/100)); ?></option>
				<option value="9mobile_650mb_24hrs:200">9mobile 650MB 24hrs @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="9mobile_25mb_24_hrs:50">9mobile 25MB 24 hrs @ <?php echo (50-(50*$direct_discount/100)); ?></option>
				<option value="9mobile_11gb_30days:4000">9Mobile 11GB 30days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
			<?php } ?>
			<?php if($etisalat_api_website == "mobileone.ng"){ ?>
				<option value="9mobile_15gb_30days:5000">9mobile 15GB 30Days @ <?php echo (5000-(5000*$direct_discount/100)); ?></option>
				<option value="9mobile_40_gb_30_days:10000">9mobile 40 GB 30 Days @ <?php echo (10000-(10000*$direct_discount/100)); ?></option>
				<option value="9mobile_75_gb_30_days:15000">9mobile 75 GB 30 Days @ <?php echo (15000-(15000*$direct_discount/100)); ?></option>
				<option value="9mobile_7gb_7_days:1500">9mobile 7GB 7 Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="9mobile_120gb_365_days:110000">9Mobile 120GB 365 Days @ <?php echo (110000-(110000*$direct_discount/100)); ?></option>
				<option value="9mobile_100mb_24hrs:100">9mobile 100MB 24hrs @ <?php echo (100-(100*$direct_discount/100)); ?></option>
				<option value="9mobile_1_5gb_30_days:1000">9mobile 1.5GB 30 Days @ <?php echo (1000-(1000*$direct_discount/100)); ?></option>
				<option value="9mobile_3gb_30_days:1500">9mobile 3GB 30 Days @ <?php echo (1500-(1500*$direct_discount/100)); ?></option>
				<option value="9mobile_2gb_30days:1200">9mobile 2gb 30days @ <?php echo (1200-(1200*$direct_discount/100)); ?></option>
				<option value="9mobile_100gb_100_days:84992">9Mobile 100GB 100 Days @ <?php echo (84992-(84992*$direct_discount/100)); ?></option>
				<option value="9mobile_60gb_180_days:55000">9Mobile 60GB 180 Days @ <?php echo (55000-(55000*$direct_discount/100)); ?></option>
				<option value="9mobile_500mb_30days:500">9mobile 500MB 30Days @ <?php echo (500-(500*$direct_discount/100)); ?></option>
				<option value="9mobile_4_5gb_30_days:2000">9mobile 4.5GB 30 Days @ <?php echo (2000-(2000*$direct_discount/100)); ?></option>
				<option value="9mobile_30gb_90_days:27500">9mobile 30GB 90 Days @ <?php echo (27500-(27500*$direct_discount/100)); ?></option>
				<option value="9mobile_650mb_24hrs:200">9mobile 650MB 24hrs @ <?php echo (200-(200*$direct_discount/100)); ?></option>
				<option value="9mobile_25mb_24_hrs:50">9mobile 25MB 24 hrs @ <?php echo (50-(50*$direct_discount/100)); ?></option>
				<option value="9mobile_11gb_30days:4000">9Mobile 11GB 30days @ <?php echo (4000-(4000*$direct_discount/100)); ?></option>
			<?php } ?>
			</select>
			
<input onkeydown="javascript: return nenterkey_function(event)" name="phone-number" id="phone-number" type="tel" class="input-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Phone Number"/>
<center>
	<span style="font-weight:bold;" id="phone-error" class="color-8 mobile-font-size-10 system-font-size-12"></span>
	<span style="font-weight:bold;" id="product-error" class="color-8 blinker mobile-font-size-12 system-font-size-14"></span>
</center>
<input onkeydown="javascript: return nenterkey_function(event)" id="bypass" type="checkbox" class="check-box"/> <span class="color-8 mobile-font-size-10 system-font-size-12"><b>Bypass Phone Number Validator</b></span><br>
<input style="pointer-events:none;" onclick="openAuth(<?php echo $all_user_details['transaction_pin']; ?>);" type="button" id="proceed" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Proceed"/>
<input name="buy" onclick="(this.style='pointer-events:none;background:lightgray;')()" style="display:none;" type="submit" id="buyPRODUCT" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Processing..."/>
<script>
	function carrierServiceName(serviceName,netName){
		setTimeout(function(){
			updateCarrierAPIkey();
		},100);
		
		let listbox = document.getElementById("carrier-name");
		for (var i = 0; i < listbox.options.length; ++i) {
			if (listbox.options[i].value === netName){
				listbox.options[i].selected = true;
			}
		}
		let servNetArray = ['mtnServNetImg','airtelServNetImg','gloServNetImg','9mobileServNetImg'];
		for(let x=0; x<servNetArray.length; x++){
if(servNetArray[x] !== serviceName){
	document.getElementById(servNetArray[x]).style = "filter: grayscale(100%);";
	if(servNetArray[x] == 'mtnServNetImg'){
		document.getElementById(servNetArray[x]).src = "/images/mtn.png";
	}
	if(servNetArray[x] == 'airtelServNetImg'){
		document.getElementById(servNetArray[x]).src = "/images/airtel.png";
	}
	if(servNetArray[x] == 'gloServNetImg'){
		document.getElementById(servNetArray[x]).src = "/images/glo.png";
	}
	if(servNetArray[x] == '9mobileServNetImg'){
		document.getElementById(servNetArray[x]).src = "/images/9mobile.png";
	}
}else{
	document.getElementById(servNetArray[x]).style = "filter: grayscale(0%);";
	if(servNetArray[x] == 'mtnServNetImg'){
		for (var i = 0; i < listbox.options.length; ++i) {
			if (listbox.options[i].value === "mtn"){
				listbox.options[i].selected = true;
				document.getElementById("product-error").innerHTML = "";
			}
			
			if(listbox.value !== "mtn"){
				document.getElementById("product-error").innerHTML = "<br>MTN Service not available! Try again later";
			}
		}
		document.getElementById(servNetArray[x]).src = "/images/mtn-marked.png";
	}
	if(servNetArray[x] == 'airtelServNetImg'){
		for (var i = 0; i < listbox.options.length; ++i) {
			if (listbox.options[i].value === "airtel"){
				listbox.options[i].selected = true;
				document.getElementById("product-error").innerHTML = "";
			}
			
			if(listbox.value !== "airtel"){
				document.getElementById("product-error").innerHTML = "<br>Airtel Service not available! Try again later";
			}
		}
		document.getElementById(servNetArray[x]).src = "/images/airtel-marked.png";
	}
	if(servNetArray[x] == 'gloServNetImg'){
		for (var i = 0; i < listbox.options.length; ++i) {
			if (listbox.options[i].value === "glo"){
				listbox.options[i].selected = true;
				document.getElementById("product-error").innerHTML = "";
			}
			
			if(listbox.value !== "glo"){
				document.getElementById("product-error").innerHTML = "<br>Glo Service not available! Try again later";
			}
		}
		document.getElementById(servNetArray[x]).src = "/images/glo-marked.png";
	}
	if(servNetArray[x] == '9mobileServNetImg'){
		for (var i = 0; i < listbox.options.length; ++i) {
			if (listbox.options[i].value === "9mobile"){
				listbox.options[i].selected = true;
				document.getElementById("product-error").innerHTML = "";
			}
			
			if(listbox.value !== "9mobile"){
				document.getElementById("product-error").innerHTML = "<br>9mobile Service not available! Try again later";
			}
		}
		document.getElementById(servNetArray[x]).src = "/images/9mobile-marked.png";
	}
}
		}
		
	}
	function updateCarrierAPIkey(){
		const carrier_name = document.getElementById("carrier-name");
		if(document.getElementById("carrier-name").value == "mtn"){
document.getElementById("mtn").style.display = "inline-block";
		}else{
document.getElementById("mtn").style.display = "none";
		}
		
		if(document.getElementById("carrier-name").value == "airtel"){
document.getElementById("airtel").style.display = "inline-block";
		}else{
document.getElementById("airtel").style.display = "none";
		}
	
		if(document.getElementById("carrier-name").value == "glo"){
document.getElementById("glo").style.display = "inline-block";
		}else{
document.getElementById("glo").style.display = "none";
		}
		
		if(document.getElementById("carrier-name").value == "9mobile"){
document.getElementById("9mobile").style.display = "inline-block";
		}else{
document.getElementById("9mobile").style.display = "none";
		}
	}

	setInterval(function(){
		if(document.getElementById("bypass").checked == false){
			if(document.getElementById("phone-number").value.length == 11){
				if(carrierMTN.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
					carrierServiceName('mtnServNetImg','mtn');
					document.getElementById("phone-error").innerHTML = "Verified MTN Number";
				}
	
				if(carrierAirtel.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
					carrierServiceName('airtelServNetImg','airtel');
					document.getElementById("phone-error").innerHTML = "Verified Airtel Number";
				}
	
				if(carrierGlo.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
					carrierServiceName('gloServNetImg','glo');
					document.getElementById("phone-error").innerHTML = "Verified Glo Number";
				}
	
				if(carrier9mobile.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
					carrierServiceName('9mobileServNetImg','9mobile');
					document.getElementById("phone-error").innerHTML = "Verified 9mobile Number";
				}
			}
		}else{
			let fromSP = "";
				if(carrierMTN.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
					fromSP = "MTN";
				}
	
				if(carrierAirtel.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
					fromSP = "Airtel";
				}
	
				if(carrierGlo.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
					fromSP = "Glo";
				}
	
				if(carrier9mobile.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
					fromSP = "9mobile";
				}
				let toSP = document.getElementById("carrier-name").options[document.getElementById("carrier-name").selectedIndex].text;
				document.getElementById("phone-error").innerHTML = "Ported Number changed from "+fromSP+" to "+toSP;
		}
	
	}, 500);
	
	setInterval(function(){
		if((document.getElementById("carrier-name").value !== "") && (document.getElementById("phone-number").value.length == 11)){
if(document.getElementById("bypass").checked == false){
	if(document.getElementById("carrier-name").value == "mtn"){
		if(carrierMTN.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
			document.getElementById("proceed").style.pointerEvents = "auto";
		}else{
			document.getElementById("proceed").style.pointerEvents = "none";
		}
	}
	
	if(document.getElementById("carrier-name").value == "airtel"){
		if(carrierAirtel.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
			document.getElementById("proceed").style.pointerEvents = "auto";
		}else{
			document.getElementById("proceed").style.pointerEvents = "none";
		}
	}
	
	if(document.getElementById("carrier-name").value == "glo"){
		if(carrierGlo.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
			document.getElementById("proceed").style.pointerEvents = "auto";
		}else{
			document.getElementById("proceed").style.pointerEvents = "none";
		}
	}
	
	if(document.getElementById("carrier-name").value == "9mobile"){
		if(carrier9mobile.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
			document.getElementById("proceed").style.pointerEvents = "auto";
		}else{
			document.getElementById("proceed").style.pointerEvents = "none";
		}
	}
	
}else{
	document.getElementById("proceed").style.pointerEvents = "auto";
}
		}else{
document.getElementById("proceed").style.pointerEvents = "none";
		}
	});
</script>
		</form>
	</div><br>

<?php
	include("./include/top-5-transaction.php");
?>
</center>

<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>