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
$flutterwave_keys = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM payment_api WHERE website='flutterwave'"));
$paystack_keys = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM payment_api WHERE website='paystack'"));

$raw_number = "123456789012345678901234567890";
$reference = substr(str_shuffle($raw_number), 0, 15);

$monnify_account_reg_array = array();
foreach (array("035", "50515", "232") as $monnify_bank_codes) {
	$select_user_banks = mysqli_query($conn_server_db, "SELECT * FROM user_bank WHERE email='" . $user_details["email"] . "' && bank_code='$monnify_bank_codes'");
	if (mysqli_num_rows($select_user_banks) == 0) {
		array_push($monnify_account_reg_array, "1");
	} else {
		array_push($monnify_account_reg_array, "0");
	}

}

if (max($monnify_account_reg_array) == 1) {
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
			$monnifyReserveAccountPostData = json_encode(array("accountReference" => md5($user_details["email"]), "accountName" => $full_name, "currencyCode" => "NGN", "contractCode" => $monnify_keys["encrypt_key"], "customerEmail" => $user_details["email"], "customerName" => ucwords($user_details["firstname"] . " " . $user_details["lastname"]), "bvn" => $monnify_bvn, $monnify_bvnnin_type => $monnify_bvnnin_id, "getAllAvailableBanks" => false, "preferredBanks" => ["035", "50515", "232"]), true);

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

	foreach ($monnifyReserveAccountDetailsJSONObj["responseBody"]["accounts"] as $monnify_accounts) {
		$select_user_banks = mysqli_query($conn_server_db, "SELECT * FROM user_bank WHERE email='" . $user_details["email"] . "' && account_number='" . $monnify_accounts["accountNumber"] . "' && bank_code='" . $monnify_accounts["bankCode"] . "'");
		if (mysqli_num_rows($select_user_banks) == 0) {
			mysqli_query($conn_server_db, "INSERT INTO user_bank (email, account_name, account_number, bank_name, bank_code, account_reference) VALUES ('" . $user_details["email"] . "','" . $monnify_accounts["accountName"] . "','" . $monnify_accounts["accountNumber"] . "','" . $monnify_accounts["bankName"] . "','" . $monnify_accounts["bankCode"] . "', '".$monnifyReserveAccountDetailsJSONObj["responseBody"]["accountReference"]."')");
		}
	}
} else {
	$get_user_virtual_bank = array();
	$select_user_banks = mysqli_query($conn_server_db, "SELECT * FROM user_bank WHERE email='" . $user_details["email"] . "'");
	if (mysqli_num_rows($select_user_banks) > 0) {
		while($bank_details = mysqli_fetch_assoc($select_user_banks)){
			$each_account_details = 
			array(
				"bank_name" => $bank_details["bank_name"],
				"account_number" => $bank_details["account_number"],
				"account_name" => $bank_details["account_name"],
			);
			array_push($get_user_virtual_bank, $each_account_details);
		}	
	}
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


	<center>
		<div
			class="container-box bg-4 mobile-width-85 system-width-65 mobile-margin-top-2 system-margin-top-5 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
			<form>
				<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">Pay with ATM
					or USSD</a></span><br>
				<select onchange="chooseGateway();" id="paymentGateway"
					class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-97 system-width-60">
					<option selected disabled>Choose Payment Gateway</option>
					<option <?php if ($monnify_keys["api_status"] == false) {
						echo "hidden";
					} ?> value="monnify">Pay With
						Monnify</option>
					<option <?php if ($flutterwave_keys["api_status"] == false) {
						echo "hidden";
					} ?> value="flutterwave">
						Pay With Flutterwave</option>
					<option <?php if ($paystack_keys["api_status"] == false) {
						echo "hidden";
					} ?> value="paystack">Pay
						With
						Paystack</option>
				</select><br>
				<input hidden
					value="<?php echo $all_user_details['firstname'] . " " . $all_user_details['lastname']; ?>"
					id="fullname" type="text" />
				<input hidden value="<?php echo $all_user_details['email']; ?>" id="email" type="email" />
				<input hidden value="<?php echo $reference; ?>" id="ref" type="number" />
				<input hidden value="<?php echo $all_user_details['phone_number']; ?>" id="phone" type="number"
					placeholder="Phone Number" required />
				<input hidden id="public-key" type="text" placeholder="Public Key" required />
				<input hidden id="encrypt-key" type="text" placeholder="Encrypt Key" required />
				<input id="amount" type="number" placeholder="Amount" class="input-box mobile-width-95 system-width-58"
					required /><br>
				<span id="monnify-amount-div" class="color-8 mobile-font-size-12 system-font-size-16"></span>
				<span id="paystack-amount-div" class="color-8 mobile-font-size-12 system-font-size-16"></span><br>

				<button style="display:none;" type="button" id="monnify-btn"
					class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-59"
					onclick="payWithMonnify();">Pay With Monnify</button>
				<button style="display:none;" type="button" id="flutterwave-btn"
					class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-59"
					onclick="makePaymentFlutterwave();">Pay With Flutterwave</button>
				<button style="display:none;" type="button" id="paystack-btn"
					class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-95 system-width-59"
					onclick="makePaymentPaystack();">Pay With Paystack</button>

			</form>
		</div>
	</center>
	<script>

		setInterval(function () {
			if (document.getElementById("paymentGateway").value == "monnify") {
				document.getElementById("monnify-amount-div").innerHTML = "Amount To Pay minus Discount: N" + Number(Number(document.getElementById("amount").value.replace("-", "")) - Number(Number(document.getElementById("amount").value.replace("-", "")) * 1.5 / 100));
			} else {
				document.getElementById("monnify-amount-div").innerHTML = "";
			}

			if (document.getElementById("paymentGateway").value == "paystack") {
				document.getElementById("paystack-amount-div").innerHTML = "Amount To Pay minus Discount: N" + Number(Number(document.getElementById("amount").value.replace("-", "")) - Number(Number(document.getElementById("amount").value.replace("-", "")) * 1.5 / 100));
			} else {
				document.getElementById("paystack-amount-div").innerHTML = "";
			}

		});

		function chooseGateway() {
			if (document.getElementById("paymentGateway").value == "monnify") {
				document.getElementById("public-key").value = '<?php echo $monnify_keys["public_key"]; ?>';
				document.getElementById("encrypt-key").value = '<?php echo $monnify_keys["encrypt_key"]; ?>';
				document.getElementById("monnify-btn").style.display = "inline-block";
			} else {
				document.getElementById("monnify-btn").style.display = "none";
			}

			if (document.getElementById("paymentGateway").value == "flutterwave") {
				document.getElementById("public-key").value = '<?php echo $flutterwave_keys["public_key"]; ?>';
				document.getElementById("flutterwave-btn").style.display = "inline-block";
			} else {
				document.getElementById("flutterwave-btn").style.display = "none";
			}

			if (document.getElementById("paymentGateway").value == "paystack") {
				document.getElementById("public-key").value = '<?php echo $paystack_keys["public_key"]; ?>';
				document.getElementById("paystack-btn").style.display = "inline-block";
			} else {
				document.getElementById("paystack-btn").style.display = "none";
			}
		}

		//MONNIFY CHECKOUT GATEWAY
		function payWithMonnify() {
			MonnifySDK.initialize({
				amount: Number(document.getElementById("amount").value.replace("-", "").trim()),
				currency: "NGN",
				reference: Number(document.getElementById("ref").value.trim()),
				customerName: "",
				customerEmail: document.getElementById("email").value.trim(),
				apiKey: document.getElementById("public-key").value.trim(),
				contractCode: document.getElementById("encrypt-key").value,
				paymentDescription: "Wallet Funding",
				isTestMode: false,
				metadata: {
					"name": "",
					"age": "",
				},
				paymentMethods: ["CARD"],
				incomeSplitConfig: [],
				onComplete: function (response) {
					window.location.href = "./dashboard.php";
				},
				onClose: function (data) {
					window.location.href = "./dashboard.php";
				}
			});
		}

		//FLUTTERWAVE CHECKOUT GATEWAY
		function makePaymentFlutterwave() {
			FlutterwaveCheckout({
				public_key: document.getElementById("public-key").value.trim(),
				tx_ref: Number(document.getElementById("ref").value.trim()),
				amount: Number(document.getElementById("amount").value.replace("-", "").trim()),
				currency: "NGN",
				payment_options: "card, banktransfer, ussd",
				redirect_url: "",
				meta: {
					consumer_id: "",
					consumer_mac: "",
				},
				customer: {
					email: document.getElementById("email").value.trim(),
					phone_number: document.getElementById("phone").value.trim(),
					name: document.getElementById("fullname").value.trim(),
				},
				customizations: {
					title: "",
					description: "",
					logo: "",
				},
				callback: function (payment) {
					window.location.href = "./dashboard.php";
				}
			});
		}


		//PAYSTACK CHECKOUT GATEWAY
		function makePaymentPaystack() {

			let handler = PaystackPop.setup({
				key: document.getElementById("public-key").value.trim(), // Replace with your public key
				email: document.getElementById("email").value.trim(),
				amount: Number(document.getElementById("amount").value.replace("-", "").trim()) * 100,
				currency: 'NGN', // Use GHS for Ghana Cedis or USD for US Dollars
				ref: Number(document.getElementById("ref").value.trim()), // Replace with a reference you generated

				// label: "Optional string that replaces customer email"
				onClose: function () {
					alertPopUp("Transaction was not Successful, Window Closed");

				},
				callback: function (response) {
					alertPopUp("Wallet Funded Successfully with N" + amountToFund);
				}
			});
			handler.openIframe();
		}

	</script>
	<center>
		<div style="text-align:center; display: inline-block;" id="bank_cont"
			class="container-box bg-4 mobile-width-85 system-width-65 mobile-margin-top-2 system-margin-top-5 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">

			<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">Pay with
				Automated Bank Transfer</a></span><br>
			<input type="button" onclick="oEBank(1,'--bg-3');" style="font-weight: ;"
				class="button-box color-8 bg-3 mobile-font-size-12 system-font-size-16 mobile-width-32 system-width-30"
				value='<?php echo strtoupper($get_user_virtual_bank[0]["bank_name"]); ?>' />
			<input type="button" onclick="oEBank(2,'--bg-2');" style="font-weight: ;"
				class="button-box color-8 bg-2 mobile-font-size-12 system-font-size-16 mobile-width-32 system-width-30"
				value='<?php echo strtoupper($get_user_virtual_bank[1]["bank_name"]); ?>' />
			<input type="button" onclick="oEBank(3,'--bg-6');" style="font-weight: ;"
				class="button-box color-8 bg-6 mobile-font-size-12 system-font-size-16 mobile-width-32 system-width-30"
				value='<?php echo strtoupper($get_user_virtual_bank[2]["bank_name"]); ?>' /><br>
			<span style="display: none;" id="bankacc_1"
				class="color-8 mobile-font-size-18 system-font-size-24"><b><?php echo strtoupper($get_user_virtual_bank[0]["account_name"]) . " - " . strtoupper($get_user_virtual_bank[0]["account_number"]); ?></b></span>
			<span style="display: none;" id="bankacc_2"
				class="color-8 mobile-font-size-18 system-font-size-24"><b><?php echo strtoupper($get_user_virtual_bank[1]["account_name"]) . " - " . strtoupper($get_user_virtual_bank[1]["account_number"]); ?></b></span>
			<span style="display: none;" id="bankacc_3"
				class="color-8 mobile-font-size-18 system-font-size-24"><b><?php echo strtoupper($get_user_virtual_bank[2]["account_name"]) . " - " . strtoupper($get_user_virtual_bank[2]["account_number"]); ?></b></span><br><br>
			<span style="float:right;" class="mobile-font-size-12 system-font-size-14">1.1% Charges applicable, Select
				Any of the Banks to see the Bank Account number, please note that Bank transfer to any of the Banks
				Listed above will automatically credit your wallet.</span>
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

	</center>

	<?php include(__DIR__ . "/include/footer-html.php"); ?>
</body>

</html>