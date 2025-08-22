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


$monnify_keys = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM payment_api WHERE website='monnify'"));

$monnifyApiUrl = "https://api.monnify.com/api/v1/auth/login";
$monnifyAPILogin = curl_init($monnifyApiUrl);
curl_setopt($monnifyAPILogin, CURLOPT_URL, $monnifyApiUrl);
curl_setopt($monnifyAPILogin, CURLOPT_POST, true);
curl_setopt($monnifyAPILogin, CURLOPT_RETURNTRANSFER, true);
$monnifyLoginHeader = array("Authorization: Basic " . base64_encode($monnify_keys["public_key"] . ':' . $monnify_keys["secret_key"]), "Content-Type: application/json", "Content-Length: 0");
curl_setopt($monnifyAPILogin, CURLOPT_HTTPHEADER, $monnifyLoginHeader);

curl_setopt($monnifyAPILogin, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($monnifyAPILogin, CURLOPT_SSL_VERIFYPEER, false);

$GetMonnifyJSON = curl_exec($monnifyAPILogin);
$monnifyJSONObj = json_decode($GetMonnifyJSON, true);
if ($GetMonnifyJSON == true) {
	$access_token = $monnifyJSONObj["responseBody"]["accessToken"];
	$monnifyCheckReserveAccountDetailsApiUrl = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts/" . md5($user_details["email"]);
	$monnifyCheckReserveAccountDetailsAPILogin = curl_init($monnifyCheckReserveAccountDetailsApiUrl);
	curl_setopt($monnifyCheckReserveAccountDetailsAPILogin, CURLOPT_URL, $monnifyCheckReserveAccountDetailsApiUrl);
	curl_setopt($monnifyCheckReserveAccountDetailsAPILogin, CURLOPT_HTTPGET, true);
	curl_setopt($monnifyCheckReserveAccountDetailsAPILogin, CURLOPT_RETURNTRANSFER, true);
	$monnifyCheckReserveAccountDetailsLoginHeader = array("Authorization: Bearer " . $access_token, "Content-Type: application/json");
	curl_setopt($monnifyCheckReserveAccountDetailsAPILogin, CURLOPT_HTTPHEADER, $monnifyCheckReserveAccountDetailsLoginHeader);
	curl_setopt($monnifyCheckReserveAccountDetailsAPILogin, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($monnifyCheckReserveAccountDetailsAPILogin, CURLOPT_SSL_VERIFYPEER, false);
	$GetmonnifyCheckReserveAccountDetailsJSON = curl_exec($monnifyCheckReserveAccountDetailsAPILogin);
	$monnifyCheckReserveAccountDetailsJSONObj = json_decode($GetmonnifyCheckReserveAccountDetailsJSON, true);

	//CREATE RESERVED ACCOUNT
	if ($monnifyCheckReserveAccountDetailsJSONObj["responseMessage"] !== "success") {
		$monnifyReserveAccountApiUrl = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts";
		$monnifyReserveAccountAPILogin = curl_init($monnifyReserveAccountApiUrl);
		curl_setopt($monnifyReserveAccountAPILogin, CURLOPT_URL, $monnifyReserveAccountApiUrl);
		curl_setopt($monnifyReserveAccountAPILogin, CURLOPT_POST, true);
		curl_setopt($monnifyReserveAccountAPILogin, CURLOPT_RETURNTRANSFER, true);
		$monnifyReserveAccountLoginHeader = array("Authorization: Bearer " . $access_token, "Content-Type: application/json");
		curl_setopt($monnifyReserveAccountAPILogin, CURLOPT_HTTPHEADER, $monnifyReserveAccountLoginHeader);

		$full_name = ucwords($user_details["firstname"] . " " . $user_details["lastname"]);
		$get_admin_bvn_nin_details = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT bvn, nin FROM `admin` WHERE 1"));
		if ((!empty($get_admin_bvn_nin_details["bvn"]) && is_numeric($get_admin_bvn_nin_details["bvn"]) && strlen($get_admin_bvn_nin_details["bvn"]) == "11") || (!empty($get_admin_bvn_nin_details["nin"]) && is_numeric($get_admin_bvn_nin_details["nin"]) && strlen($get_admin_bvn_nin_details["nin"]) == "11")) {
			if (!empty($get_admin_bvn_nin_details["bvn"]) && is_numeric($get_admin_bvn_nin_details["bvn"]) && strlen($get_admin_bvn_nin_details["bvn"]) == "11") {
				$monnify_bvnnin_type = "bvn";
				$monnify_bvnnin_id = $get_admin_bvn_nin_details["bvn"];
			}

			if (!empty($get_admin_bvn_nin_details["nin"]) && is_numeric($get_admin_bvn_nin_details["nin"]) && strlen($get_admin_bvn_nin_details["nin"]) == "11") {
				$monnify_bvnnin_type = "nin";
				$monnify_bvnnin_id = $get_admin_bvn_nin_details["nin"];
			}
		} else {
			$monnify_bvnnin_type = "bvn";
			$monnify_bvnnin_id = "";
		}
		$monnifyReserveAccountPostData = json_encode(array("accountReference" => md5($user_details["email"]), "accountName" => $full_name, "currencyCode" => "NGN", "contractCode" => $monnify_keys["encrypt_key"], "customerEmail" => $user_details["email"], "customerName" => ucwords($user_details["firstname"] . " " . $user_details["lastname"]), $monnify_bvnnin_type => $monnify_bvnnin_id, "getAllAvailableBanks" => false, "preferredBanks" => ["035", "070", "232"]), true);

		curl_setopt($monnifyReserveAccountAPILogin, CURLOPT_POSTFIELDS, $monnifyReserveAccountPostData);
		curl_setopt($monnifyReserveAccountAPILogin, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($monnifyReserveAccountAPILogin, CURLOPT_SSL_VERIFYPEER, false);

		$GetmonnifyReserveAccountJSON = curl_exec($monnifyReserveAccountAPILogin);
	}
}


//GET RESERVED ACCOUNT DETAILS
$monnifyReserveAccountDetailsApiUrl = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts/" . md5($user_details["email"]);
$monnifyReserveAccountDetailsAPILogin = curl_init($monnifyReserveAccountDetailsApiUrl);
curl_setopt($monnifyReserveAccountDetailsAPILogin, CURLOPT_URL, $monnifyReserveAccountDetailsApiUrl);
curl_setopt($monnifyReserveAccountDetailsAPILogin, CURLOPT_HTTPGET, true);
curl_setopt($monnifyReserveAccountDetailsAPILogin, CURLOPT_RETURNTRANSFER, true);
$monnifyReserveAccountDetailsLoginHeader = array("Authorization: Bearer " . $access_token, "Content-Type: application/json");
curl_setopt($monnifyReserveAccountDetailsAPILogin, CURLOPT_HTTPHEADER, $monnifyReserveAccountDetailsLoginHeader);
curl_setopt($monnifyReserveAccountDetailsAPILogin, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($monnifyReserveAccountDetailsAPILogin, CURLOPT_SSL_VERIFYPEER, false);
$GetmonnifyReserveAccountDetailsJSON = curl_exec($monnifyReserveAccountDetailsAPILogin);
$monnifyReserveAccountDetailsJSONObj = json_decode($GetmonnifyReserveAccountDetailsJSON, true);


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
	<script src="/scripts/auth.js"></script>
	<script type="text/javascript" src="https://sdk.monnify.com/plugin/monnify.js"></script>
	<script src="https://checkout.flutterwave.com/v3.js"></script>
	<script src="https://js.paystack.co/v1/inline.js"></script>
	<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>

<body>
	<?php include(__DIR__ . "/include/header-html.php"); ?>




	<div style="text-align:center; display: inline-block;" id="bank_cont"
		class="container-box bg-4 mobile-font-size-12 system-font-size-14 mobile-width-85 system-width-80 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1 mobile-padding-top-3 system-padding-top-1 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">

		<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">Pay with Automated
			Bank Transfer</a></span><br>
		<input type="button" onclick="oEBank(1,'--bg-3');" style="font-weight: ;"
			class="button-box color-8 bg-3 mobile-font-size-12 system-font-size-16 mobile-width-32 system-width-30"
			value='<?php echo strtoupper($monnifyReserveAccountDetailsJSONObj["responseBody"]["accounts"][0]["bankName"]); ?>' />
		<input type="button" onclick="oEBank(2,'--bg-2');" style="font-weight: ;"
			class="button-box color-8 bg-2 mobile-font-size-12 system-font-size-16 mobile-width-32 system-width-30"
			value='<?php echo strtoupper($monnifyReserveAccountDetailsJSONObj["responseBody"]["accounts"][1]["bankName"]); ?>' />
		<input type="button" onclick="oEBank(3,'--bg-6');" style="font-weight: ;"
			class="button-box color-8 bg-6 mobile-font-size-12 system-font-size-16 mobile-width-32 system-width-30"
			value='<?php echo strtoupper($monnifyReserveAccountDetailsJSONObj["responseBody"]["accounts"][2]["bankName"]); ?>' /><br>
		<span style="display: none;" id="bankacc_1"
			class="color-8 mobile-font-size-12 system-font-size-14"><b><?php echo strtoupper($monnifyReserveAccountDetailsJSONObj["responseBody"]["accountName"]) . " - " . strtoupper($monnifyReserveAccountDetailsJSONObj["responseBody"]["accounts"][0]["accountNumber"]); ?></b></span>
		<span style="display: none;" id="bankacc_2"
			class="color-8 mobile-font-size-12 system-font-size-14"><b><?php echo strtoupper($monnifyReserveAccountDetailsJSONObj["responseBody"]["accountName"]) . " - " . strtoupper($monnifyReserveAccountDetailsJSONObj["responseBody"]["accounts"][1]["accountNumber"]); ?></b></span>
		<span style="display: none;" id="bankacc_3"
			class="color-8 mobile-font-size-12 system-font-size-14"><b><?php echo strtoupper($monnifyReserveAccountDetailsJSONObj["responseBody"]["accountName"]) . " - " . strtoupper($monnifyReserveAccountDetailsJSONObj["responseBody"]["accounts"][2]["accountNumber"]); ?></b></span><br>
		<span style="float:right;" class="mobile-font-size-12 system-font-size-14">1.1%</span>
	</div>
	<script>
		oEBank(1, '--bg-3');
		function oEBank(index, bgColor) {
			if (document.getElementById("bankacc_" + index).style.display == "none") {
				let bankArray = ['1', '2', '3'];
				for (let x = 0; x < 3; x++) {
					if (index !== bankArray[x]) {
						document.getElementById("bankacc_" + bankArray[x]).style.display = "none";
					}
				}
				document.getElementById("bankacc_" + index).style.display = "inline-block";
				document.getElementById("bank_cont").style = "display: inline-block; text-align:left;";
			}
		}
	</script>


	<?php include(__DIR__ . "/include/footer-html.php"); ?>
</body>

</html>     Q
  