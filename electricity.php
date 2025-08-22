<?php session_start();
if (!isset($_SESSION["user"])) {
	header("Location: /login.php");
} else {
	include(__DIR__ . "/include/config.php");
	include(__DIR__ . "/include/user-details.php");
}

//GET USER DETAILS
$user_session = $_SESSION["user"];
$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));

//GET EACH electricity_subscription API WEBSITE
$get_ekedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='ekedc'"));
$get_eedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='eedc'"));
$get_ikedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='ikedc'"));
$get_jedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='jedc'"));
$get_kano_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='kano'"));
$get_ibedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='ibedc'"));
$get_phed_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='phed'"));
$get_aedc_electricity_subscription_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api WHERE subscription_name='aedc'"));


//GET EACH electricity_subscription APIKEY
$ekedc_api_website = $get_ekedc_electricity_subscription_running_api['website'];
$eedc_api_website = $get_eedc_electricity_subscription_running_api['website'];
$ikedc_api_website = $get_ikedc_electricity_subscription_running_api['website'];
$jedc_api_website = $get_jedc_electricity_subscription_running_api['website'];
$kano_api_website = $get_kano_electricity_subscription_running_api['website'];
$ibedc_api_website = $get_ibedc_electricity_subscription_running_api['website'];
$phed_api_website = $get_phed_electricity_subscription_running_api['website'];
$aedc_api_website = $get_aedc_electricity_subscription_running_api['website'];

$get_ekedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$ekedc_api_website'"));
$get_eedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$eedc_api_website'"));
$get_ikedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$ikedc_api_website'"));
$get_jedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$jedc_api_website'"));
$get_kano_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$kano_api_website'"));
$get_ibedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$ibedc_api_website'"));
$get_phed_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$phed_api_website'"));
$get_aedc_electricity_subscription_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM electricity_api WHERE website='$aedc_api_website'"));

//GET EACH electricity_subscription STATUS
$get_ekedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='ekedc'"));
$get_eedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='eedc'"));
$get_ikedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='ikedc'"));
$get_jedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='jedc'"));
$get_kano_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='kano'"));
$get_ibedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='ibedc'"));
$get_phed_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='phed'"));
$get_aedc_electricity_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM electricity_subscription_status WHERE subscription_name='aedc'"));

if (isset($_POST["verify"])) {
	$carrier = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["carrier"]));
	if ($all_user_details["account_type"] == "smart_earner") {
		$electricity_discount = "$get_" . $carrier . '_electricity_subscription_running_api["discount_1"]';
	}

	if ($all_user_details["account_type"] == "vip_earner") {
		$electricity_discount = "$get_" . $carrier . '_electricity_subscription_running_api["discount_2"]';
	}

	if ($all_user_details["account_type"] == "vip_vendor") {
		$electricity_discount = "$get_" . $carrier . '_electricity_subscription_running_api["discount_3"]';
	}

	if ($all_user_details["account_type"] == "api_earner") {
		$electricity_discount = "$get_" . $carrier . '_electricity_subscription_running_api["discount_4"]';
	}

	$meter_no = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["meter-no"]));
	$meter_type = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["meter-type"]));
	$amount = str_replace(["-", "+", "/", "*"], "", mysqli_real_escape_string($conn_server_db, strip_tags($_POST["amount"])));

	if ($carrier == "ekedc") {
		$site_name = $ekedc_api_website;
		$apikey = $get_ekedc_electricity_subscription_apikey["apikey"];
	}
	if ($carrier == "eedc") {
		$site_name = $eedc_api_website;
		$apikey = $get_eedc_electricity_subscription_apikey["apikey"];
	}
	if ($carrier == "ikedc") {
		$site_name = $ikedc_api_website;
		$apikey = $get_ikedc_electricity_subscription_apikey["apikey"];
	}
	if ($carrier == "jedc") {
		$site_name = $jedc_api_website;
		$apikey = $get_jedc_electricity_subscription_apikey["apikey"];
	}
	if ($carrier == "kano") {
		$site_name = $kano_api_website;
		$apikey = $get_kano_electricity_subscription_apikey["apikey"];
	}
	if ($carrier == "ibedc") {
		$site_name = $ibedc_api_website;
		$apikey = $get_ibedc_electricity_subscription_apikey["apikey"];
	}
	if ($carrier == "phed") {
		$site_name = $phed_api_website;
		$apikey = $get_phed_electricity_subscription_apikey["apikey"];
	}
	if ($carrier == "aedc") {
		$site_name = $aedc_api_website;
		$apikey = $get_aedc_electricity_subscription_apikey["apikey"];
	}

	if (strtolower($site_name) === "smartrecharge.ng") {
		$verifyElectricityPurchase = curl_init();
		$verifyElectricityApiUrl = "https://smartrecharge.ng/api/v2/electric/?api_key=" . $apikey . "&product_code=" . $carrier . "_" . $meter_type . "_custom&meter_number=" . $meter_no . "&amount=" . $amount . "&task=verify";
		curl_setopt($verifyElectricityPurchase, CURLOPT_URL, $verifyElectricityApiUrl);
		curl_setopt($verifyElectricityPurchase, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($verifyElectricityPurchase, CURLOPT_HTTPGET, 1);
		curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYPEER, false);

		$GetverifyElectricityJSON = curl_exec($verifyElectricityPurchase);
		$verifyElectricityJSONObj = json_decode($GetverifyElectricityJSON, true);

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

		if ($GetverifyElectricityJSON == true) {

			if (in_array($verifyElectricityJSONObj["error_code"], array(1987))) {
				$_SESSION["meter_name"] = $verifyElectricityJSONObj["data"]["name"];
				$_SESSION["electricity_discount"] = $electricity_discount;
				$_SESSION["carrier"] = $carrier;
				$_SESSION["meter_no"] = $meter_no;
				$_SESSION["meter_type"] = $meter_type;
				$_SESSION["amount"] = $amount;
				$_SESSION["amount_charged"] = $amount - ($amount * floatval($electricity_discount) / 100);
				$_SESSION["site_name"] = $site_name;
				$_SESSION["apikey"] = $apikey;
			}

		}
	}

	if (strtolower($site_name) === "smartrechargeapi.com") {
		$verifyElectricityPurchase = curl_init();
		$verifyElectricityApiUrl = "https://smartrechargeapi.com/api/v2/electric/?api_key=" . $apikey . "&product_code=" . $carrier . "_" . $meter_type . "_custom&meter_number=" . $meter_no . "&amount=" . $amount . "&task=verify";
		curl_setopt($verifyElectricityPurchase, CURLOPT_URL, $verifyElectricityApiUrl);
		curl_setopt($verifyElectricityPurchase, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($verifyElectricityPurchase, CURLOPT_HTTPGET, 1);
		curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYPEER, false);

		$GetverifyElectricityJSON = curl_exec($verifyElectricityPurchase);
		$verifyElectricityJSONObj = json_decode($GetverifyElectricityJSON, true);

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

		if ($GetverifyElectricityJSON == true) {

			if (in_array($verifyElectricityJSONObj["error_code"], array(1987))) {
				$_SESSION["meter_name"] = $verifyElectricityJSONObj["data"]["name"];
				$_SESSION["electricity_discount"] = $electricity_discount;
				$_SESSION["carrier"] = $carrier;
				$_SESSION["meter_no"] = $meter_no;
				$_SESSION["meter_type"] = $meter_type;
				$_SESSION["amount"] = $amount;
				$_SESSION["amount_charged"] = $amount - ($amount * floatval($electricity_discount) / 100);
				$_SESSION["site_name"] = $site_name;
				$_SESSION["apikey"] = $apikey;
			}

		}
	}

	if (strtolower($site_name) === "mobileone.ng") {
		$verifyElectricityPurchase = curl_init();
		$verifyElectricityApiUrl = "https://mobileone.ng/api/v2/electric/?api_key=" . $apikey . "&product_code=" . $carrier . "_" . $meter_type . "_custom&meter_number=" . $meter_no . "&amount=" . $amount . "&task=verify";
		curl_setopt($verifyElectricityPurchase, CURLOPT_URL, $verifyElectricityApiUrl);
		curl_setopt($verifyElectricityPurchase, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($verifyElectricityPurchase, CURLOPT_HTTPGET, 1);
		curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYPEER, false);

		$GetverifyElectricityJSON = curl_exec($verifyElectricityPurchase);
		$verifyElectricityJSONObj = json_decode($GetverifyElectricityJSON, true);

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

		if ($GetverifyElectricityJSON == true) {

			if (in_array($verifyElectricityJSONObj["error_code"], array(1987))) {
				$_SESSION["meter_name"] = $verifyElectricityJSONObj["data"]["name"];
				$_SESSION["electricity_discount"] = $electricity_discount;
				$_SESSION["carrier"] = $carrier;
				$_SESSION["meter_no"] = $meter_no;
				$_SESSION["meter_type"] = $meter_type;
				$_SESSION["amount"] = $amount;
				$_SESSION["amount_charged"] = $amount - ($amount * floatval($electricity_discount) / 100);
				$_SESSION["site_name"] = $site_name;
				$_SESSION["apikey"] = $apikey;
			}

		}
	}

	if (strtolower($site_name) === "datagifting.com.ng") {
		$verifyElectricityPurchase = curl_init();
		$verifyElectricityApiUrl = "https://v5.datagifting.com.ng/web/api/verify-electric.php";
		curl_setopt($verifyElectricityPurchase, CURLOPT_URL, $verifyElectricityApiUrl);
		curl_setopt($verifyElectricityPurchase, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($verifyElectricityPurchase, CURLOPT_POST, 1);
		$dgPurchaseData = json_encode(array(
			"api_key" => $apikey,
			"type" => $meter_type,
			"meter_number" => $meter_no,
			"provider" => $carrier
		));
		curl_setopt($verifyElectricityPurchase,CURLOPT_POSTFIELDS,$dgPurchaseData);
		curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYPEER, false);

		$GetverifyElectricityJSON = curl_exec($verifyElectricityPurchase);
		$verifyElectricityJSONObj = json_decode($GetverifyElectricityJSON, true);

		if ($GetverifyElectricityJSON == true) {

			if (in_array($verifyElectricityJSONObj["status"], array("success"))) {
				$_SESSION["meter_name"] = $verifyElectricityJSONObj["desc"];
				$_SESSION["electricity_discount"] = $electricity_discount;
				$_SESSION["carrier"] = $carrier;
				$_SESSION["meter_no"] = $meter_no;
				$_SESSION["meter_type"] = $meter_type;
				$_SESSION["amount"] = $amount;
				$_SESSION["amount_charged"] = $amount - ($amount * floatval($electricity_discount) / 100);
				$_SESSION["site_name"] = $site_name;
				$_SESSION["apikey"] = $apikey;
			}

		}
	}

	if (strtolower($site_name) === "alrahuzdata.com.ng") {
		$disco_type_array = array("prepaid" => "1", "postpaid" => "2");
		$disco_name_array = array("ekedc" => "2", "eedc" => "5", "ikedc" => "1", "jedc" => "9", "kano" => "4", "ibedc" => "7", "phed" => "6", "aedc" => "3");
		$verifyElectricityPurchase = curl_init();
		$verifyElectricityApiUrl = "https://alrahuzdata.com.ng/api/validatemeter/?meter_number=".$meter_no."&disconame=".$disco_name_array[$carrier]."&mtype=".$disco_type_array[$meter_type];
		curl_setopt($verifyElectricityPurchase, CURLOPT_URL, $verifyElectricityApiUrl);
		curl_setopt($verifyElectricityPurchase, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($verifyElectricityPurchase, CURLOPT_HTTPGET, 1);
		$alrahuzdataverifyTokenPostHeader = array("Authorization: Token " . $apikey, "Content-Type: application/json");
		curl_setopt($verifyElectricityPurchase, CURLOPT_HTTPHEADER, $alrahuzdataverifyTokenPostHeader);
		curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($verifyElectricityPurchase, CURLOPT_SSL_VERIFYPEER, false);

		$GetverifyElectricityJSON = curl_exec($verifyElectricityPurchase);
		$verifyElectricityJSONObj = json_decode($GetverifyElectricityJSON, true);
//Debugger
			// fwrite(fopen("electricity-alrahuzdata.txt", "a"), $GetverifyElectricityJSON."\n\n");

		// if ($GetverifyElectricityJSON == true) {

		// 	if (in_array($verifyElectricityJSONObj["status"], array("success"))) {
		// 		$_SESSION["meter_name"] = $verifyElectricityJSONObj["desc"];
		// 		$_SESSION["electricity_discount"] = $electricity_discount;
		// 		$_SESSION["carrier"] = $carrier;
		// 		$_SESSION["meter_no"] = $meter_no;
		// 		$_SESSION["meter_type"] = $meter_type;
		// 		$_SESSION["amount"] = $amount;
		// 		$_SESSION["amount_charged"] = $amount - ($amount * floatval($electricity_discount) / 100);
		// 		$_SESSION["site_name"] = $site_name;
		// 		$_SESSION["apikey"] = $apikey;
		// 	}

		// }
	}

	header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["buy"])) {
	$carrier = $_SESSION["carrier"];
	$electricity_discount = $_SESSION["electricity_discount"];
	$meter_no = $_SESSION["meter_no"];
	$meter_type = $_SESSION["meter_type"];
	$amount = $_SESSION["amount"];
	$site_name = $_SESSION["site_name"];
	$apikey = $_SESSION["apikey"];

	if ($all_user_details["wallet_balance"] >= $amount) {
		if ($site_name == "smartrecharge.ng") {
			include("./include/electricity-smartrecharge.php");
		}
		if ($site_name == "smartrechargeapi.com") {
			include("./include/electricity-smartrechargeapi.php");
		}
		if ($site_name == "mobileone.ng") {
			include("./include/electricity-mobileone.php");
		}

		if($site_name == "datagifting.com.ng"){
			include("./include/electricity-datagifting.php");
		}

		if($site_name == "alrahuzdata.com.ng"){
			include("./include/electricity-data-alrahuzdata.php");
		}
	} else {
		$log_electricity_subscription_message = "Insufficient Fund, Fund Wallet And Try Again! ";
	}

	$_SESSION["transaction_text"] = $log_electricity_subscription_message;
	unset($_SESSION["meter_name"]);
	unset($_SESSION["electricity_discount"]);
	unset($_SESSION["carrier"]);
	unset($_SESSION["meter_no"]);
	unset($_SESSION["meter_type"]);
	unset($_SESSION["amount"]);
	unset($_SESSION["amount_charged"]);
	unset($_SESSION["site_name"]);
	unset($_SESSION["apikey"]);
	header("Location: " . $_SERVER["REQUEST_URI"]);

}

if (isset($_POST["cancel"])) {
	unset($_SESSION["meter_name"]);
	unset($_SESSION["electricity_discount"]);
	unset($_SESSION["carrier"]);
	unset($_SESSION["meter_no"]);
	unset($_SESSION["meter_type"]);
	unset($_SESSION["amount"]);
	unset($_SESSION["amount_charged"]);
	unset($_SESSION["site_name"]);
	unset($_SESSION["apikey"]);
	header("Location: " . $_SERVER["REQUEST_URI"]);
}


?>
<!DOCTYPE html>
<html>

<head>
	<title>
		<?php echo mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT * FROM site_info WHERE 1"))["sitetitle"]; ?>
	</title>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; " />
	<meta name="theme-color" content="skyblue" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="/css/site.css">
	<script src="/scripts/carrier.js"></script>
	<script src="/scripts/auth.js"></script>
	<script src="/scripts/trans-pass.js"></script>
	<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>

<body>
	<?php include(__DIR__ . "/include/header-html.php"); ?>
	<script type="text/javascript">
		setTimeout(function () {
			alertPopUp("Select Electricity by clicking the image that represents the Electricity Company");
		}, 1000);
	</script>

	<center>
		<div
			class="container-box bg-4 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
			<form method="post">
				<?php if ($_SESSION["transaction_text"] == true) { ?>
					<div name="message" id="font-color-1" class="message-box font-size-2">
						<?php echo $_SESSION["transaction_text"]; ?></div>
				<?php } ?>

				<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">SELECT
					ELECTRICITY COMPANY</span><br>
				<?php if (!isset($_SESSION["meter_name"])) { ?>
					<img onclick="carrierServiceName('ekedcServNetImg','ekedc');" id="ekedcServNetImg"
						src="/images/EKEDC.jpg" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
					<img onclick="carrierServiceName('eedcServNetImg','eedc');" id="eedcServNetImg" src="/images/EEDC.jpg"
						style="cursor: pointer;" class="mobile-width-15 system-width-15" />
					<img onclick="carrierServiceName('ikedcServNetImg','ikedc');" id="ikedcServNetImg"
						src="/images/IKEDC.jpg" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
					<img onclick="carrierServiceName('jedcServNetImg','jedc');" id="jedcServNetImg" src="/images/JEDC.jpg"
						style="cursor: pointer;" class="mobile-width-15 system-width-15" /><br />
					<img onclick="carrierServiceName('kedcoServNetImg','kedco');" id="kedcoServNetImg"
						src="/images/KANO.jpg" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
					<img onclick="carrierServiceName('ibedcServNetImg','ibedc');" id="ibedcServNetImg"
						src="/images/IBEDC.jpg" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
					<img onclick="carrierServiceName('phedServNetImg','phed');" id="phedServNetImg" src="/images/PHED.jpg"
						style="cursor: pointer;" class="mobile-width-15 system-width-15" />
					<img onclick="carrierServiceName('aedcServNetImg','aedc');" id="aedcServNetImg" src="/images/AEDC.jpg"
						style="cursor: pointer;" class="mobile-width-15 system-width-15" />

					<select name="carrier" onchange="updateCarrierAPIkey()" id="carrier-name" hidden>
						<option disabled hidden selected>Choose Carrier</option>
						<?php if ($get_ekedc_electricity_subscription_status["subscription_status"] == "active") { ?>
							<option value="ekedc">EKEDC</option>
						<?php } ?>
						<?php if ($get_eedc_electricity_subscription_status["subscription_status"] == "active") { ?>
							<option value="eedc">EEDC</option>
						<?php } ?>
						<?php if ($get_ikedc_electricity_subscription_status["subscription_status"] == "active") { ?>
							<option value="ikedc">IKEDC</option>
						<?php } ?>
						<?php if ($get_jedc_electricity_subscription_status["subscription_status"] == "active") { ?>
							<option value="jedc">JEDC</option>
						<?php } ?>
						<?php if ($get_kano_electricity_subscription_status["subscription_status"] == "active") { ?>
							<option value="kano">KEDCO</option>
						<?php } ?>
						<?php if ($get_ibedc_electricity_subscription_status["subscription_status"] == "active") { ?>
							<option value="ibedc">IBEDC</option>
						<?php } ?>
						<?php if ($get_phed_electricity_subscription_status["subscription_status"] == "active") { ?>
							<option value="phed">PHED</option>
						<?php } ?>
						<?php if ($get_aedc_electricity_subscription_status["subscription_status"] == "active") { ?>
							<option value="aedc">AEDC</option>
						<?php } ?>

					</select><br>
					<select name="meter-type"
						class="select-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
						<option disabled hidden selected>Choose Meter Type</option>
						<option value="prepaid">Prepaid</option>
						<option value="postpaid">Postpaid</option>
					</select><br>
					<input onkeydown="javascript: return nenterkey_function(event)" name="meter-no" id="meter-no" type="tel"
						class="input-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
						placeholder="Meter Number" />
					<input onkeydown="javascript: return nenterkey_function(event)" name="amount" id="amount" type="tel"
						class="input-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
						placeholder="Amount" />

				<?php } ?>
				<center>
					<span style="font-weight:bold;" id="electricity-error"
						class="color-8 mobile-font-size-12 system-font-size-14">
						<?php
						if (isset($_SESSION["meter_name"])) {
							echo "Electricity Company: <b>" . strtoupper($_SESSION["carrier"]) . "</b><br>
							<img style='pointer-events:none;' src='./images/" . strtoupper($_SESSION["carrier"]) . ".jpg' class='mobile-width-30 system-width-30'/><br>
							<b>Meter Name: " . $_SESSION["meter_name"] . "<br>
							Meter No: " . $_SESSION["meter_no"] . "<br>
							Meter Type: " . ucwords($_SESSION["meter_type"]) . "<br>
							Amount To Pay: N" . $_SESSION["amount"] . "
							Amount To Charge: N" . $_SESSION["amount_charged"] . "</b>";
						}
						?>
					</span>
					<span style="font-weight:bold;" id="product-error"
						class="color-8 blinker mobile-font-size-12 system-font-size-14"></span>
				</center>
				<?php if (!isset($_SESSION["meter_name"])) { ?>
					<input name="verify" type="submit"
						class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
						value="Verify Meter" />
				<?php } else { ?>
					<input onclick="openAuth(<?php echo $all_user_details['transaction_pin']; ?>);" type="button"
						id="proceed"
						class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
						value="Proceed" />
				<?php } ?>
				<input name="buy" onclick="(this.style='pointer-events:none;background:lightgray;')()"
					style="display:none;" type="submit" id="buyPRODUCT"
					class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
					value="Processing..." />

				<?php if (isset($_SESSION["meter_name"])) { ?>
					<input name="cancel" type="submit"
						class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
						value="Cancel Transaction" />
				<?php } ?>
			</form>
			<script>
				function carrierServiceName(serviceName, netName) {
					setTimeout(function () {
						updateCarrierAPIkey();
					}, 100);

					let listbox = document.getElementById("carrier-name");
					for (var i = 0; i < listbox.options.length; ++i) {
						if (listbox.options[i].value === netName) {
							listbox.options[i].selected = true;
						}
					}
					let servNetArray = ["ekedcServNetImg", "eedcServNetImg", "ikedcServNetImg", "jedcServNetImg", "kedcoServNetImg", "ibedcServNetImg", "phedServNetImg", "aedcServNetImg"];
					for (let x = 0; x < servNetArray.length; x++) {
						if (servNetArray[x] !== serviceName) {
							document.getElementById(servNetArray[x]).style = "filter: grayscale(100%);";
							if (servNetArray[x] == 'ekedcServNetImg') {
								document.getElementById(servNetArray[x]).src = "/images/EKEDC.jpg";
							}
							if (servNetArray[x] == 'eedcServNetImg') {
								document.getElementById(servNetArray[x]).src = "/images/EEDC.jpg";
							}
							if (servNetArray[x] == 'ikedcServNetImg') {
								document.getElementById(servNetArray[x]).src = "/images/IKEDC.jpg";
							}
							if (servNetArray[x] == 'jedcServNetImg') {
								document.getElementById(servNetArray[x]).src = "/images/JEDC.jpg";
							}
							if (servNetArray[x] == 'kedcoServNetImg') {
								document.getElementById(servNetArray[x]).src = "/images/KANO.jpg";
							}
							if (servNetArray[x] == 'ibedcServNetImg') {
								document.getElementById(servNetArray[x]).src = "/images/IBEDC.jpg";
							}
							if (servNetArray[x] == 'phedServNetImg') {
								document.getElementById(servNetArray[x]).src = "/images/PHED.jpg";
							}
							if (servNetArray[x] == 'aedcServNetImg') {
								document.getElementById(servNetArray[x]).src = "/images/AEDC.jpg";
							}
						} else {
							document.getElementById(servNetArray[x]).style = "filter: grayscale(0%);";
							if (servNetArray[x] == 'ekedcServNetImg') {
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "ekedc") {
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}

									if (listbox.value !== "ekedc") {
										document.getElementById("product-error").innerHTML = "Ekedc Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/ekedc-marked.jpg";
							}

							if (servNetArray[x] == 'eedcServNetImg') {
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "eedc") {
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}

									if (listbox.value !== "eedc") {
										document.getElementById("product-error").innerHTML = "Eedc Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/eedc-marked.jpg";
							}

							if (servNetArray[x] == 'ikedcServNetImg') {
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "ikedc") {
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}

									if (listbox.value !== "ikedc") {
										document.getElementById("product-error").innerHTML = "Ikedc Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/ikedc-marked.jpg";
							}

							if (servNetArray[x] == 'jedcServNetImg') {
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "jedc") {
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}

									if (listbox.value !== "jedc") {
										document.getElementById("product-error").innerHTML = "Jedc Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/jedc-marked.jpg";
							}

							if (servNetArray[x] == 'kedcoServNetImg') {
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "kedco") {
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}

									if (listbox.value !== "kedco") {
										document.getElementById("product-error").innerHTML = "Kedco Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/kano-marked.jpg";
							}

							if (servNetArray[x] == 'ibedcServNetImg') {
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "ibedc") {
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}

									if (listbox.value !== "ibedc") {
										document.getElementById("product-error").innerHTML = "Ibedc Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/ibedc-marked.jpg";
							}

							if (servNetArray[x] == 'phedServNetImg') {
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "phed") {
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}

									if (listbox.value !== "phed") {
										document.getElementById("product-error").innerHTML = "Phed Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/phed-marked.jpg";
							}

							if (servNetArray[x] == 'aedcServNetImg') {
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "aedc") {
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}

									if (listbox.value !== "aedc") {
										document.getElementById("product-error").innerHTML = "Aedc Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/aedc-marked.jpg";
							}
						}
					}

				}
				function updateCarrierAPIkey() {
					const carrier_name = document.getElementById("carrier-name");

				}

				/*setInterval(function(){
					if((document.getElementById("carrier-name").value !== "") && (document.getElementById("cable-iuc-no").value.trim().length > 0)){
						document.getElementById("proceed").style.pointerEvents = "auto";
					}
					
				});*/

			</script>
		</div>

		<?php
		include("./include/top-5-transaction.php");
		?>
	</center>

	<?php include(__DIR__ . "/include/footer-html.php"); ?>
</body>

</html>