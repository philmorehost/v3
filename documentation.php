<?php session_start();
	include(__DIR__."/include/config.php");
	if(isset($_SESSION["user"])){
		include(__DIR__."/include/user-details.php");
	}
	
	//GET USER DETAILS
	if(isset($_SESSION["user"])){
		$user_session = $_SESSION["user"];
		$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
	}
	if(isset($_POST["generate-new-apikey"])){
		$apikey_unshuffle_string = "abcdefghijklmnopqrstuvwxyz1234567890abcdefghijklmnopqrstuvwxyz";
		$shuffle_apikey = str_shuffle($apikey_unshuffle_string);
		$chopped_apikey = substr($shuffle_apikey,0,50);
		$apikey = $chopped_apikey;
		if(mysqli_query($conn_server_db, "UPDATE users SET apikey='$apikey' WHERE email='$user_session'") == true){
			
		}
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
<script src="/scripts/auth.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>
<body>
<?php include(__DIR__."/include/header-html.php"); ?>

<center>
<div class="container-box bg-2 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
  <span style="text-align: left;" class="color-8 mobile-font-size-14 system-font-size-18">This page contains the API Documentation, API KEY and Prices of each of the services we offer.</span><br>
  <span style="text-align: left;" class="color-8 mobile-font-size-14 system-font-size-18">Click on each of the service links to view the API Documentation and Prices.</span>
</div>
<div class="container-box bg-8 mobile-width-85 system-width-90 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-1 system-padding-top-1 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-1 system-padding-bottom-1">
	<?php if(isset($_SESSION["user"])){ ?>
		<form method="post">
			<span style="text-align: left; font-weight: bolder;" class="color-5 mobile-font-size-16 system-font-size-18"><b>Copy Developer APIKey:</b></span><br>
			<input class="input-box mobile-width-85 system-width-75 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="<?php echo $all_user_details['apikey']; ?>" readonly/>
			<button name="generate-new-apikey" class="button-box color-8 bg-5 mobile-font-size-14 system-font-size-16 mobile-width-40 system-width-20 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Re-Generate</button>
		</form>
	<?php } ?>
</div>

<a href="/APIdoc/balance.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Wallet Balance API Documentation</button><br>
</a>

<a href="/APIdoc/airtime.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Airtime API Documentation</button><br>
</a>

<a href="/APIdoc/direct-data.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Direct Data API Documentation</button><br>
</a>

<a href="/APIdoc/sme-data.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">SME Data API Documentation</button><br>
</a>

<a href="/APIdoc/data-gifting.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Data Gifting API Documentation</button><br>
</a>

<a href="/APIdoc/cable.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Cable Subscription API Documentation</button><br>
</a>

<a href="/APIdoc/electricity.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Electricity API Documentation</button><br>
</a>

<a href="/APIdoc/exam.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Examination Pin API Documentation</button><br>
</a>

<a href="/APIdoc/sms.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Bulk SMS API Documentation</button><br>
</a>

<a href="/APIdoc/insurance.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Third-party Motor Insurance API Documentation</button><br>
</a>

<a href="/APIdoc/recharge-card-printing.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Recharge Card API</button><br>
</a>

<a href="/APIdoc/data-card.php">
	<button class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Data Card API</button><br>
</a>

</center>

<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>