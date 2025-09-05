<?php session_start();
if (!isset($_SESSION["admin"])) {
	header("Location: /admin/login.php");
} else {
	include("../include/admin-config.php");
	include("../include/admin-details.php");
}

if ($conn_server_db == true) {
	$rechargecard_table_name = "rechargecard_api";
	$rechargecard_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $rechargecard_table_name . "(website VARCHAR(225) NOT NULL, apikey VARCHAR(225))";
	if (mysqli_query($conn_server_db, $rechargecard_apikey_db_table) == true) {
	}


	$rechargecard_select_running_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $rechargecard_table_name . " LIMIT 1");
	if (mysqli_num_rows($rechargecard_select_running_api) > 0) {
		while ($rechargecard_api_running_list = mysqli_fetch_assoc($rechargecard_select_running_api)) {
			$first_rechargecard_api_website_row = $rechargecard_api_running_list["website"];
			$rechargecard_network_running_table_name = "rechargecard_network_running_api";
			$rechargecard_network_running_db_table = "CREATE TABLE IF NOT EXISTS " . $rechargecard_network_running_table_name . "(network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))";
			if (mysqli_query($conn_server_db, $rechargecard_network_running_db_table) == true) {
				if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $rechargecard_network_running_table_name)) == 0) {
					$insert_rechargecard_network_running_api = "INSERT INTO " . $rechargecard_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('mtn', '$first_rechargecard_api_website_row','1','1','1','1');";
					$insert_rechargecard_network_running_api .= "INSERT INTO " . $rechargecard_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('airtel', '$first_rechargecard_api_website_row','1','1','1','1');";
					$insert_rechargecard_network_running_api .= "INSERT INTO " . $rechargecard_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('glo', '$first_rechargecard_api_website_row','1','1','1','1');";
					$insert_rechargecard_network_running_api .= "INSERT INTO " . $rechargecard_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('9mobile', '$first_rechargecard_api_website_row','1','1','1','1')";
					if (mysqli_multi_query($conn_server_db, $insert_rechargecard_network_running_api) == true) {

					}
				}
			}
		}
	}


	$rechargecard_network_table_name = "rechargecard_network_status";
	$rechargecard_network_db_table = "CREATE TABLE IF NOT EXISTS " . $rechargecard_network_table_name . "(network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)";
	if (mysqli_query($conn_server_db, $rechargecard_network_db_table) == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $rechargecard_network_table_name)) == 0) {
			$insert_network_status = "INSERT INTO " . $rechargecard_network_table_name . " (network_name, network_status) VALUES ('mtn', 'active');";
			$insert_network_status .= "INSERT INTO " . $rechargecard_network_table_name . " (network_name, network_status) VALUES ('airtel', 'active');";
			$insert_network_status .= "INSERT INTO " . $rechargecard_network_table_name . " (network_name, network_status) VALUES ('glo', 'active');";
			$insert_network_status .= "INSERT INTO " . $rechargecard_network_table_name . " (network_name, network_status) VALUES ('9mobile', 'active')";

			if (mysqli_multi_query($conn_server_db, $insert_network_status) == true) {

			}
		}
	}
}


if (isset($_POST["install-api"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$rechargecard_api_website = "INSERT INTO " . $rechargecard_table_name . " (website) VALUES ('$api_name')";

	$check_rechargecard_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $rechargecard_table_name . " WHERE website='$api_name'");

	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_rechargecard_select_api) == 0) {
			if (mysqli_query($conn_server_db, $rechargecard_api_website) == true) {
				$add_api_message = ucwords($api_name) . " rechargecard API installed successfully! ";
			}
		} else {
			$add_api_message = ucwords($api_name) . " rechargecard API has already been Installed! ";
		}
	}

}

if (isset($_POST["update-apikey"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$apikey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["apikey"]));

	$rechargecard_api_website = "UPDATE " . $rechargecard_table_name . " SET apikey='$apikey' WHERE website='$api_name'";
	$check_rechargecard_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $rechargecard_table_name . " WHERE website='$api_name'");
	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_rechargecard_select_api) > 0) {
			if (mysqli_query($conn_server_db, $rechargecard_api_website) == true) {
				$update_api_message = ucwords($api_name) . " API key Updated successfully! ";
			}
		}
	} else {
		$update_api_message = "Error: Cannot Update Empty API Website";
	}

}

if (isset($_POST["update-network"])) {
	$mtn = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["mtn"]));
	$airtel = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["airtel"]));
	$glo = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["glo"]));
	$etisalat = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["9mobile"]));

	if ($mtn == "active") {
		$mtn_status = "active";
	} else {
		$mtn_status = "down";
	}

	if ($airtel == "active") {
		$airtel_status = "active";
	} else {
		$airtel_status = "down";
	}

	if ($glo == "active") {
		$glo_status = "active";
	} else {
		$glo_status = "down";
	}

	if ($etisalat == "active") {
		$etisalat_status = "active";
	} else {
		$etisalat_status = "down";
	}



	$update_mtn_network_status = "UPDATE " . $rechargecard_network_table_name . " SET network_status='$mtn_status' WHERE network_name='mtn'";
	$update_airtel_network_status = "UPDATE " . $rechargecard_network_table_name . " SET network_status='$airtel_status' WHERE network_name='airtel'";
	$update_glo_network_status = "UPDATE " . $rechargecard_network_table_name . " SET network_status='$glo_status' WHERE network_name='glo'";
	$update_etisalat_network_status = "UPDATE " . $rechargecard_network_table_name . " SET network_status='$etisalat_status' WHERE network_name='9mobile'";

	if (mysqli_query($conn_server_db, $update_mtn_network_status) == true) {
		$network_update_message = "All Network Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_airtel_network_status) == true) {
		$network_update_message = "All Network Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_glo_network_status) == true) {
		$network_update_message = "All Network Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_etisalat_network_status) == true) {
		$network_update_message = "All Network Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

}

if (isset($_POST["generate-rechargecard"])) {
	$api_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-web"]));
	$sp_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["sp-name"]));
	$sp_amount = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["sp-amount"]));
	$qty = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["qty"]));
	if (!empty($api_website) && !empty($sp_name) && !empty($sp_amount) && !empty($qty)) {

		//GET EACH exam API WEBSITE
		$get_rechargecard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM rechargecard_network_running_api WHERE network_name='$sp_name'"));

		$get_rechargecard_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM rechargecard_api WHERE website='$api_website'"));
		$apikey = $get_rechargecard_apikey["apikey"];
		$raw_number = "123456789012345678901234567890";
		$reference = substr(str_shuffle($raw_number), 0, 15);

		if ($api_website == "clubkonnect.com") {
			$clubKonnectUserID = array_filter(explode(":", trim($apikey)))[0];
			$clubKonnectApikey = array_filter(explode(":", trim($apikey)))[1];

			if ($sp_name == "mtn") {
				$mobileCode = "01";
			}

			if ($sp_name == "glo") {
				$mobileCode = "02";
			}

			if ($sp_name == "9mobile") {
				$mobileCode = "03";
			}

			if ($sp_name == "airtel") {
				$mobileCode = "04";
			}

			$rechargecardPurchase = curl_init();
			$rechargecardApiUrl = "https://www.nellobytesystems.com/APIEPINV1.asp?UserID=" . $clubKonnectUserID . "&APIKey=" . $clubKonnectApikey . "&MobileNetwork=" . $mobileCode . "&Value=" . $sp_amount . "&Quantity=" . $qty;
			curl_setopt($rechargecardPurchase, CURLOPT_URL, $rechargecardApiUrl);
			curl_setopt($rechargecardPurchase, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($rechargecardPurchase, CURLOPT_HTTPGET, 1);
			curl_setopt($rechargecardPurchase, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($rechargecardPurchase, CURLOPT_SSL_VERIFYPEER, false);

			$GetrechargecardJSON = curl_exec($rechargecardPurchase);
			$rechargecardJSONObj = json_decode($GetrechargecardJSON, true);
			if ($GetrechargecardJSON == true) {

				foreach ($rechargecardJSONObj["TXN_EPIN"] as $allCardArray) {
					$allGeneratedPINs .= $allCardArray["pin"] . ":" . $allCardArray["sno"] . "\n";
				}

				$fetch_all_rechargecard = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM admin_recharge_card WHERE network_name='$sp_name'"));
				$allCardsJoined = $fetch_all_rechargecard["card_" . $sp_amount] . "\n" . $allGeneratedPINs;
				if (count($rechargecardJSONObj["TXN_EPIN"]) >= 1) {

					$ref_id = $rechargecardJSONObj["TXN_EPIN"][0]["batchno"];
					if (mysqli_query($conn_server_db, "UPDATE admin_recharge_card SET card_" . $sp_amount . "='" . $allCardsJoined . "' WHERE network_name='" . $sp_name . "' ") == true) {
						$rechargecard_generate_message = "Recharge PINs Updated Successfully! ";
					} else {
						$rechargecard_generate_message = "Error: Recharge PINs fails to update " . mysqli_error($conn_server_db);
					}
				}

				if (count($rechargecardJSONObj["TXN_EPIN"]) < 1) {
					$rechargecard_generate_message = "Error: Cannot purchased Recharge PIN";
				}

			} else {
				$rechargecard_generate_message = "Server currently unavailable";
			}

		}

		if ($api_website == "epins.com.ng") {
			if ($sp_name == "mtn") {
				$mobileCode = "mtn";
				$ePinsRechargePlan = array("100" => 1, "200" => 2, "500" => 5);
			}

			if ($sp_name == "glo") {
				$mobileCode = "glo";
				$ePinsRechargePlan = array("100" => 1, "200" => 2, "500" => 5);
			}

			if ($sp_name == "9mobile") {
				$mobileCode = "etisalat";
				$ePinsRechargePlan = array("100" => 1, "200" => 2, "500" => 5);
			}

			if ($sp_name == "airtel") {
				$mobileCode = "airtel";
				$ePinsRechargePlan = array("100" => 1, "200" => 2, "500" => 5);
			}

			if (!empty($ePinsRechargePlan[$sp_amount])) {
				$rechargecardPurchase = curl_init();
				$rechargecardApiUrl = "https://api.epins.com.ng/v2/autho/epin/";
				curl_setopt($rechargecardPurchase, CURLOPT_URL, $rechargecardApiUrl);

				curl_setopt($rechargecardPurchase, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($rechargecardPurchase, CURLOPT_POST, true);
				$rechargecardPurchaseData = json_encode(array("apikey" => $apikey, "service" => "epin", "network" => $mobileCode, "pinQuantity" => $qty, "pinDenomination" => $ePinsRechargePlan[$sp_amount], "ref" => $reference), true);

				curl_setopt($rechargecardPurchase, CURLOPT_POSTFIELDS, $rechargecardPurchaseData);

				curl_setopt($rechargecardPurchase, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($rechargecardPurchase, CURLOPT_SSL_VERIFYPEER, false);

				$GetrechargecardJSON = curl_exec($rechargecardPurchase);
				$rechargecardJSONObj = json_decode($GetrechargecardJSON, true);
				if ($GetrechargecardJSON == true) {
					if (in_array($rechargecardJSONObj["code"], array(101))) {
						$explodedEPins = array_filter(explode("\r", trim($rechargecardJSONObj["description"]["PIN"])));
						foreach ($explodedEPins as $allCardArray) {
							$allGeneratedPINs .= $allCardArray . "\n";
						}
						$fetch_all_rechargecard = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM admin_recharge_card WHERE network_name='$sp_name'"));
						$allCardsJoined = $fetch_all_rechargecard["card_" . $sp_amount] . "\n" . $allGeneratedPINs;

						$ref_id = $reference;
						if (mysqli_query($conn_server_db, "UPDATE admin_recharge_card SET card_" . $sp_amount . "='$allCardsJoined' WHERE network_name='$sp_name'") == true) {
							$rechargecard_generate_message = "Recharge PINs Updated Successfully! ";
						} else {
							$rechargecard_generate_message = "Error: Recharge PINs fails to update " . mysqli_error($conn_server_db);
						}
					}

					if (!in_array($rechargecardJSONObj["code"], array(101))) {
						$rechargecard_generate_message = "Error: Cannot purchased Recharge PIN <b>" . $datacardJSONObj["description"]["response_description"] . "</b>";
					}

				} else {
					$rechargecard_generate_message = "Server currently unavailable";
				}
			} else {
				$rechargecard_generate_message = "Recharge Card Amount not Available";
			}

		}
	}
	header("refresh:5;url=" . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-rechargecard"])) {
	$company_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["company-name"]));
	$carrier = mysqli_real_escape_string($conn_server_db, strtolower(strip_tags($_POST["sp-name"])));
	$amount = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["sp-amount"]));
	$all_cards_pin = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace("\r\n", "\n", $_POST["all-rechargecard_" . $carrier . "_" . $amount . ""])));

	if (mysqli_query($conn_server_db, "UPDATE admin_recharge_card SET company_name='" . $company_name . "'") == true) {
		if (mysqli_query($conn_server_db, "UPDATE admin_recharge_card SET card_" . $amount . "='" . $all_cards_pin . "' WHERE network_name='" . $carrier . "' ") == true) {
			$rechargecard_update_message = "Recharge PINs Updated Successfully! ";
		} else {
			$rechargecard_update_message = "Error: Recharge PINs fails to update " . mysqli_error($conn_server_db);
		}
	}
	header("refresh:2;url=" . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["run-api"])) {
	$mtn_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["mtn"]));
	$mtn_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["mtn-discount-1"])));
	$mtn_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["mtn-discount-2"])));
	$mtn_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["mtn-discount-3"])));
	$mtn_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["mtn-discount-4"])));

	$mtn_network_table_injection = "UPDATE " . $rechargecard_network_running_table_name . " SET website='$mtn_website', discount_1='$mtn_discount_1', discount_2='$mtn_discount_2', discount_3='$mtn_discount_3', discount_4='$mtn_discount_4' WHERE network_name='mtn'";
	if (mysqli_query($conn_server_db, $mtn_network_table_injection) == true) {
	}

	$airtel_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["airtel"]));
	$airtel_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-1"])));
	$airtel_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-2"])));
	$airtel_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-3"])));
	$airtel_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-4"])));

	$airtel_network_table_injection = "UPDATE " . $rechargecard_network_running_table_name . " SET website='$airtel_website', discount_1='$airtel_discount_1', discount_2='$airtel_discount_2', discount_3='$airtel_discount_3', discount_4='$airtel_discount_4' WHERE network_name='airtel'";
	if (mysqli_query($conn_server_db, $airtel_network_table_injection) == true) {
	}

	$glo_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["glo"]));
	$glo_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-1"])));
	$glo_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-2"])));
	$glo_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-3"])));
	$glo_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-4"])));

	$glo_network_table_injection = "UPDATE " . $rechargecard_network_running_table_name . " SET website='$glo_website', discount_1='$glo_discount_1', discount_2='$glo_discount_2', discount_3='$glo_discount_3', discount_4='$glo_discount_4' WHERE network_name='glo'";
	if (mysqli_query($conn_server_db, $glo_network_table_injection) == true) {
	}

	$etisalat_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["9mobile"]));
	$etisalat_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-1"])));
	$etisalat_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-2"])));
	$etisalat_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-3"])));
	$etisalat_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-4"])));

	$etisalat_network_table_injection = "UPDATE " . $rechargecard_network_running_table_name . " SET website='$etisalat_website', discount_1='$etisalat_discount_1', discount_2='$etisalat_discount_2', discount_3='$etisalat_discount_3', discount_4='$etisalat_discount_4' WHERE network_name='9mobile'";
	if (mysqli_query($conn_server_db, $etisalat_network_table_injection) == true) {
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
</head>

<body>
	<?php include("../include/admin-header-html.php"); ?>

	<center>
		<div class="container-box bg-6 mobile-width-95 system-width-95 mobile-margin-top-2 system-margin-top-2">

			<fieldset>
				<legend>
					<span class="font-size-2 font-family-1">INSTALL RECHARGE CARD API</span>
				</legend>
				<?php
				if ($add_api_message == true) {
					?>
					<div id="font-color-1" class="message-box font-size-2"><?php echo $add_api_message; ?></div>
					<?php
				}
				?>

				<form method="post">
					<select name="api-name"
						class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-60">
						<option disabled hidden selected>Install RECHARGE CARD API</option>
						<option value='<?php echo $_SERVER["HTTP_HOST"]; ?>'><?php echo $_SERVER["HTTP_HOST"]; ?>
						</option>
						<option value="datagifting.com.ng">datagifting.com.ng</option>
						<option value="clubkonnect.com">clubkonnect.com</option>
						<option value="epins.com.ng">epins.com.ng</option>
						<option value="alrahuzdata.com.ng">alrahuzdata.com.ng</option>
					</select>
					<input name="install-api" type="submit"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25"
						value="Install API" />
				</form>
			</fieldset><br>

			<?php
			$check_count_rechargecard_table_name = mysqli_query($conn_server_db, "SELECT website FROM " . $rechargecard_table_name);
			if ($check_count_rechargecard_table_name == true) {
				if (mysqli_num_rows($check_count_rechargecard_table_name) > 0) {

					?>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPDATE RECHARGE CARD API KEY</span>
						</legend><br>
						<form method="post">
							<?php
							if ($update_api_message == true) {
								?>
								<div id="font-color-1" class="message-box font-size-2"><?php echo $update_api_message; ?></div>
								<?php
							}
							?>

							<select name="api-name" onchange="updateAPIkey()" id="update-api-key"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-25">
								<option disabled hidden selected>Add/Update API keys</option>
								<?php
								$rechargecard_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $rechargecard_table_name);
								if (mysqli_num_rows($rechargecard_select_api) > 0) {
									while ($rechargecard_api_list = mysqli_fetch_assoc($rechargecard_select_api)) {
										echo '<option data-apikey="' . $rechargecard_api_list["apikey"] . '" value="' . $rechargecard_api_list["website"] . '">' . $rechargecard_api_list["website"] . "</option>";
									}
								} else {
									echo '<option selected hidden value="">No RECHARGE CARD API was Installed!</option>';
								}

								?>
							</select>
							<input name="apikey" id="apikey" type="text"
								class="input-box mobile-width-93 system-width-60 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
								placeholder="User ID:Apikey or Apikey " />
							<input name="update-apikey" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Update API key" />
						</form>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">ENABLE/DISABLE RECHARGE CARD NETWORK</span>
						</legend>
						<?php
						$rechargecard_network_select_api = mysqli_query($conn_server_db, "SELECT network_name, network_status FROM " . $rechargecard_network_table_name);
						if (mysqli_num_rows($rechargecard_network_select_api) > 0) {
							while ($rechargecard_network_status_list = mysqli_fetch_assoc($rechargecard_network_select_api)) {
								;
								$all_network_status .= $rechargecard_network_status_list["network_status"] . ",";
							}
						}

						$exp_all_network_status = array_filter(explode(",", trim($all_network_status)));
						class allNetwork
						{
						}
						$mtn = "mtn";
						$airtel = "airtel";
						$glo = "glo";
						$etisalat = "9mobile";
						$allNetworkStatus = new allNetwork;
						$allNetworkStatus->$mtn = $exp_all_network_status[0];
						$allNetworkStatus->$airtel = $exp_all_network_status[1];
						$allNetworkStatus->$glo = $exp_all_network_status[2];
						$allNetworkStatus->$etisalat = $exp_all_network_status[3];

						$all_network_status_json = json_encode($allNetworkStatus, true);
						$decode_all_network_status_json = json_decode($all_network_status_json, true);

						?>
						<form method="post">
							<?php
							if ($network_update_message == true) {
								?>
								<div id="font-color-1" class="message-box font-size-2"><?php echo $network_update_message; ?></div>
								<?php
							}
							?>
							<span class="font-size-2 font-family-1"><b>MTN</b></span>
							<input <?php if ($decode_all_network_status_json['mtn'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="mtn" type="checkbox" class="check-box" /> -
							<span class="font-size-2 font-family-1"><b>Airtel</b></span>
							<input <?php if ($decode_all_network_status_json['airtel'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="airtel" type="checkbox" class="check-box" /> -
							<span class="font-size-2 font-family-1"><b>GLO</b></span>
							<input <?php if ($decode_all_network_status_json['glo'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="glo" type="checkbox" class="check-box" /> -
							<span class="font-size-2 font-family-1"><b>9mobile</b></span>
							<input <?php if ($decode_all_network_status_json['9mobile'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="9mobile" type="checkbox" class="check-box" />
							<input name="update-network" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Update Network Settings" />
						</form>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">GENERATE RECHARGE CARD</span>
						</legend>

						<form method="post">
							<?php
							if ($rechargecard_generate_message == true) {
								?>
								<div id="font-color-1" class="message-box font-size-2"><?php echo $rechargecard_generate_message; ?>
								</div>
								<?php
							}
							?>

							<select name="api-web" id="api-web-g"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-60">
								<option value="" disabled hidden selected>Choose RECHARGE CARD API</option>
								<?php
								$rechargecard_genselect_api = mysqli_query($conn_server_db, "SELECT website FROM " . $rechargecard_table_name);
								if (mysqli_num_rows($rechargecard_genselect_api) > 0) {
									while ($rechargecard_api_list = mysqli_fetch_assoc($rechargecard_genselect_api)) {
										if ($rechargecard_api_list["website"] !== $_SERVER["HTTP_HOST"]) {
											echo '<option value="' . $rechargecard_api_list["website"] . '">' . $rechargecard_api_list["website"] . "</option>";
										}
									}
								} else {
									echo '<option selected hidden value="">No RECHARGE CARD API was Installed!</option>';
								}
								?>
							</select>
							<select name="sp-name" id="rechargecard-sp-g"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-25">
								<option disabled hidden selected>Choose Network (Service Provider)</option>
								<option value="mtn">MTN</option>
								<option value="airtel">Airtel</option>
								<option value="glo">GLO</option>
								<option value="9mobile">9mobile</option>
							</select><br>
							<select name="sp-amount" id="rechargecard-sp-price-g"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-60">
								<option disabled hidden selected>Choose Amount</option>
								<option value="100">N100</option>
								<option value="200">N200</option>
								<option value="500">N500</option>
							</select>
							<input name="qty" id="rechargecard-qty-g" type="number"
								class="input-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-25"
								placeholder="Quantity e.g 10" value="" />
							<input style="display:none;" name="generate-rechargecard" id="generate-rechargecard-g" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Generate Recharge Card" />

						</form>
						<script>
							setInterval(function () {
								let rechargecard_sp_g = document.getElementById("rechargecard-sp-g").value;
								let rechargecard_sp_price_g = document.getElementById("rechargecard-sp-price-g").value;
								let rechargecard_qty_g = document.getElementById("rechargecard-qty-g").value;

								if ((document.getElementById("api-web-g").value !== "") && (rechargecard_sp_g == "mtn" || rechargecard_sp_g == "airtel" || rechargecard_sp_g == "glo" || rechargecard_sp_g == "9mobile") && (rechargecard_qty_g >= 1)) {
									document.getElementById("generate-rechargecard-g").style.display = "inline-block";
								} else {
									document.getElementById("generate-rechargecard-g").style.display = "none";
								}
							});
						</script>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPLOAD OR UPDATE RECHARGE CARD</span>
						</legend>
						<?php

						$selectrechargecard_company_name = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT company_name FROM admin_recharge_card WHERE 1"));
						$select_rechargecard_mtn_cards_100 = mysqli_query($conn_server_db, "SELECT card_100 FROM admin_recharge_card WHERE network_name='mtn'");
						$select_rechargecard_airtel_cards_100 = mysqli_query($conn_server_db, "SELECT card_100 FROM admin_recharge_card WHERE network_name='airtel'");
						$select_rechargecard_glo_cards_100 = mysqli_query($conn_server_db, "SELECT card_100 FROM admin_recharge_card WHERE network_name='glo'");
						$select_rechargecard_9mobile_cards_100 = mysqli_query($conn_server_db, "SELECT card_100 FROM admin_recharge_card WHERE network_name='9mobile'");

						$select_rechargecard_mtn_cards_200 = mysqli_query($conn_server_db, "SELECT card_200 FROM admin_recharge_card WHERE network_name='mtn'");
						$select_rechargecard_airtel_cards_200 = mysqli_query($conn_server_db, "SELECT card_200 FROM admin_recharge_card WHERE network_name='airtel'");
						$select_rechargecard_glo_cards_200 = mysqli_query($conn_server_db, "SELECT card_200 FROM admin_recharge_card WHERE network_name='glo'");
						$select_rechargecard_9mobile_cards_200 = mysqli_query($conn_server_db, "SELECT card_200 FROM admin_recharge_card WHERE network_name='9mobile'");

						$select_rechargecard_mtn_cards_500 = mysqli_query($conn_server_db, "SELECT card_500 FROM admin_recharge_card WHERE network_name='mtn'");
						$select_rechargecard_airtel_cards_500 = mysqli_query($conn_server_db, "SELECT card_500 FROM admin_recharge_card WHERE network_name='airtel'");
						$select_rechargecard_glo_cards_500 = mysqli_query($conn_server_db, "SELECT card_500 FROM admin_recharge_card WHERE network_name='glo'");
						$select_rechargecard_9mobile_cards_500 = mysqli_query($conn_server_db, "SELECT card_500 FROM admin_recharge_card WHERE network_name='9mobile'");


						?>
						<form method="post">
							<?php
							if ($rechargecard_update_message == true) {
								?>
								<div id="font-color-1" class="message-box font-size-2"><?php echo $rechargecard_update_message; ?>
								</div>
								<?php
							}
							?>
							<input style="display:none;" name="company-name" id="rechargecard-company-name" type="text"
								class="input-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="Company Name"
								value="<?php echo $selectrechargecard_company_name['company_name']; ?>" />
							<select onchange="updateRechargeCardManually(1);" name="sp-name" id="rechargecard-sp"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-60">
								<option disabled hidden selected>Choose Network (Service Provider)</option>
								<option value="mtn">MTN</option>
								<option value="airtel">Airtel</option>
								<option value="glo">GLO</option>
								<option value="9mobile">9mobile</option>
							</select>
							<select style="display:none;" onchange="updateRechargeCardManually(2);" name="sp-amount"
								id="rechargecard-sp-price"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-25">
								<option disabled hidden selected>Choose Amount</option>
								<option value="100">N100</option>
								<option value="200">N200</option>
								<option value="500">N500</option>
							</select><br>

							<textarea type="number" style="display:none;" name="all-rechargecard_mtn_100"
								id="rechargecard-textarea-mtn-100"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_mtn_cards_100) > 0) {
									while ($mtn_cardlists = mysqli_fetch_array($select_rechargecard_mtn_cards_100)) {
										echo trim($mtn_cardlists["card_100"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-rechargecard_mtn_200"
								id="rechargecard-textarea-mtn-200"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_mtn_cards_200) > 0) {
									while ($mtn_cardlists = mysqli_fetch_array($select_rechargecard_mtn_cards_200)) {
										echo trim($mtn_cardlists["card_200"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-rechargecard_mtn_500"
								id="rechargecard-textarea-mtn-500"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_mtn_cards_500) > 0) {
									while ($mtn_cardlists = mysqli_fetch_array($select_rechargecard_mtn_cards_500)) {
										echo trim($mtn_cardlists["card_500"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-rechargecard_airtel_100"
								id="rechargecard-textarea-airtel-100"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_airtel_cards_100) > 0) {
									while ($airtel_cardlists = mysqli_fetch_array($select_rechargecard_airtel_cards_100)) {
										echo trim($airtel_cardlists["card_100"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-rechargecard_airtel_200"
								id="rechargecard-textarea-airtel-200"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_airtel_cards_200) > 0) {
									while ($airtel_cardlists = mysqli_fetch_array($select_rechargecard_airtel_cards_200)) {
										echo trim($airtel_cardlists["card_200"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-rechargecard_airtel_500"
								id="rechargecard-textarea-airtel-500"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_airtel_cards_500) > 0) {
									while ($airtel_cardlists = mysqli_fetch_array($select_rechargecard_airtel_cards_500)) {
										echo trim($airtel_cardlists["card_500"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-rechargecard_glo_100"
								id="rechargecard-textarea-glo-100"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_glo_cards_100) > 0) {
									while ($glo_cardlists = mysqli_fetch_array($select_rechargecard_glo_cards_100)) {
										echo trim($glo_cardlists["card_100"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-rechargecard_glo_200"
								id="rechargecard-textarea-glo-200"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_glo_cards_200) > 0) {
									while ($glo_cardlists = mysqli_fetch_array($select_rechargecard_glo_cards_200)) {
										echo trim($glo_cardlists["card_200"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-rechargecard_glo_500"
								id="rechargecard-textarea-glo-500"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_glo_cards_500) > 0) {
									while ($glo_cardlists = mysqli_fetch_array($select_rechargecard_glo_cards_500)) {
										echo trim($glo_cardlists["card_500"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-rechargecard_9mobile_100"
								id="rechargecard-textarea-9mobile-100"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_9mobile_cards_100) > 0) {
									while ($etisalat_cardlists = mysqli_fetch_array($select_rechargecard_9mobile_cards_100)) {
										echo trim($etisalat_cardlists["card_100"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-rechargecard_9mobile_200"
								id="rechargecard-textarea-9mobile-200"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_9mobile_cards_200) > 0) {
									while ($etisalat_cardlists = mysqli_fetch_array($select_rechargecard_9mobile_cards_200)) {
										echo trim($etisalat_cardlists["card_200"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-rechargecard_9mobile_500"
								id="rechargecard-textarea-9mobile-500"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_rechargecard_9mobile_cards_500) > 0) {
									while ($etisalat_cardlists = mysqli_fetch_array($select_rechargecard_9mobile_cards_500)) {
										echo trim($etisalat_cardlists["card_500"]);
									}
								}
								?></textarea>
							<div id="font-color-1" class="message-box font-size-2"><span id="rechargecardCounter"></span></div>
							<input style="display:none;" name="update-rechargecard" type="submit" id="rechargecard-updatebtn"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Upload Card" /><br>
							<script>

								function updateRechargeCardManually(nub) {
									const rechargecardSP = document.getElementById("rechargecard-sp").value;
									const rechargecardSPPrice = document.getElementById("rechargecard-sp-price");
									const rechargecardMtnTA100 = document.getElementById("rechargecard-textarea-mtn-100");
									const rechargecardAirtelTA100 = document.getElementById("rechargecard-textarea-airtel-100");
									const rechargecardGloTA100 = document.getElementById("rechargecard-textarea-glo-100");
									const rechargecard9mobileTA100 = document.getElementById("rechargecard-textarea-9mobile-100");
									const rechargecardMtnTA200 = document.getElementById("rechargecard-textarea-mtn-200");
									const rechargecardAirtelTA200 = document.getElementById("rechargecard-textarea-airtel-200");
									const rechargecardGloTA200 = document.getElementById("rechargecard-textarea-glo-200");
									const rechargecard9mobileTA200 = document.getElementById("rechargecard-textarea-9mobile-200");
									const rechargecardMtnTA500 = document.getElementById("rechargecard-textarea-mtn-500");
									const rechargecardAirtelTA500 = document.getElementById("rechargecard-textarea-airtel-500");
									const rechargecardGloTA500 = document.getElementById("rechargecard-textarea-glo-500");
									const rechargecard9mobileTA500 = document.getElementById("rechargecard-textarea-9mobile-500");

									const rechargecardUB = document.getElementById("rechargecard-updatebtn");
									const rechargecardCompanyName = document.getElementById("rechargecard-company-name");
									if (nub == "1") {
										rechargecardSPPrice.style.display = "inline-block";
										rechargecardSPPrice.options[0].selected = true;
										rechargecardMtnTA100.style.display = "none";
										rechargecardAirtelTA100.style.display = "none";
										rechargecardGloTA100.style.display = "none";
										rechargecard9mobileTA100.style.display = "none";
										rechargecardMtnTA200.style.display = "none";
										rechargecardAirtelTA200.style.display = "none";
										rechargecardGloTA200.style.display = "none";
										rechargecard9mobileTA200.style.display = "none";
										rechargecardMtnTA500.style.display = "none";
										rechargecardAirtelTA500.style.display = "none";
										rechargecardGloTA500.style.display = "none";
										rechargecard9mobileTA500.style.display = "none";

										rechargecardUB.style.display = "none";
										rechargecardCompanyName.style.display = "none";
									}

									if (nub == "2") {
										if ((rechargecardSP == "mtn") || (rechargecardSP == "airtel") || (rechargecardSP == "glo") || (rechargecardSP == "9mobile")) {
											rechargecardSPPrice.style.display = "inline-block";
											rechargecardUB.style.display = "inline-block";
											rechargecardCompanyName.style.display = "inline-block";



											const countRemainingPIN = "rechargecard-textarea-" + rechargecardSP.toLowerCase() + "-" + rechargecardSPPrice.value;

											document.getElementById("rechargecardCounter").style.display = "inline-block";
											document.getElementById("rechargecardCounter").innerHTML = (document.getElementById(countRemainingPIN).value.split("\n").length) + " PIN Remaining";

											if (rechargecardSP == "mtn" && rechargecardSPPrice.value == "100") {
												rechargecardMtnTA100.style.display = "inline-block";
											} else {
												rechargecardMtnTA100.style.display = "none";
											}

											if (rechargecardSP == "airtel" && rechargecardSPPrice.value == "100") {
												rechargecardAirtelTA100.style.display = "inline-block";
											} else {
												rechargecardAirtelTA100.style.display = "none";
											}

											if (rechargecardSP == "glo" && rechargecardSPPrice.value == "100") {
												rechargecardGloTA100.style.display = "inline-block";
											} else {
												rechargecardGloTA100.style.display = "none";
											}

											if (rechargecardSP == "9mobile" && rechargecardSPPrice.value == "100") {
												rechargecard9mobileTA100.style.display = "inline-block";
											} else {
												rechargecard9mobileTA100.style.display = "none";
											}

											if (rechargecardSP == "mtn" && rechargecardSPPrice.value == "200") {
												rechargecardMtnTA200.style.display = "inline-block";
											} else {
												rechargecardMtnTA200.style.display = "none";
											}

											if (rechargecardSP == "airtel" && rechargecardSPPrice.value == "200") {
												rechargecardAirtelTA200.style.display = "inline-block";
											} else {
												rechargecardAirtelTA200.style.display = "none";
											}

											if (rechargecardSP == "glo" && rechargecardSPPrice.value == "200") {
												rechargecardGloTA200.style.display = "inline-block";
											} else {
												rechargecardGloTA200.style.display = "none";
											}

											if (rechargecardSP == "9mobile" && rechargecardSPPrice.value == "200") {
												rechargecard9mobileTA200.style.display = "inline-block";
											} else {
												rechargecard9mobileTA200.style.display = "none";
											}

											if (rechargecardSP == "mtn" && rechargecardSPPrice.value == "500") {
												rechargecardMtnTA500.style.display = "inline-block";
											} else {
												rechargecardMtnTA500.style.display = "none";
											}

											if (rechargecardSP == "airtel" && rechargecardSPPrice.value == "500") {
												rechargecardAirtelTA500.style.display = "inline-block";
											} else {
												rechargecardAirtelTA500.style.display = "none";
											}

											if (rechargecardSP == "glo" && rechargecardSPPrice.value == "500") {
												rechargecardGloTA500.style.display = "inline-block";
											} else {
												rechargecardGloTA500.style.display = "none";
											}

											if (rechargecardSP == "9mobile" && rechargecardSPPrice.value == "500") {
												rechargecard9mobileTA500.style.display = "inline-block";
											} else {
												rechargecard9mobileTA500.style.display = "none";
											}
										} else {
											rechargecardSPPrice.style.display = "none";
											rechargecardMtnTA100.style.display = "none";
											rechargecardAirtelTA100.style.display = "none";
											rechargecardGloTA100.style.display = "none";
											rechargecard9mobileTA100.style.display = "none";
											rechargecardMtnTA200.style.display = "none";
											rechargecardAirtelTA200.style.display = "none";
											rechargecardGloTA200.style.display = "none";
											rechargecard9mobileTA200.style.display = "none";
											rechargecardMtnTA500.style.display = "none";
											rechargecardAirtelTA500.style.display = "none";
											rechargecardGloTA500.style.display = "none";
											rechargecard9mobileTA500.style.display = "none";

											rechargecardUB.style.display = "none";
											document.getElementById("rechargecardCounter").style.display = "none";
										}
									}
								};

							</script>
						</form>
					</fieldset>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">CHOOSE API TO RUN</span>
						</legend>
						<form method="post">
							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">MTN RECHARGE CARD ROUTE</span><br>
								</legend>
								<?php
								$rechargecard_mtn_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $rechargecard_table_name);
								if (mysqli_num_rows($rechargecard_mtn_select_api) > 0) {
									while ($rechargecard_api_list = mysqli_fetch_assoc($rechargecard_mtn_select_api)) {
										$get_rechargecard_mtn_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $rechargecard_network_running_table_name . " WHERE network_name='mtn'"));
										if ($rechargecard_api_list["website"] !== $get_rechargecard_mtn_api_website["website"]) {
											echo $rechargecard_api_list["website"] . ' <input value="' . $rechargecard_api_list["website"] . '" name="mtn" type="radio" class="check-box"/><br>';
										} else {
											echo $rechargecard_api_list["website"] . ' <input checked value="' . $rechargecard_api_list["website"] . '" name="mtn" type="radio" class="check-box"/><br>';
										}
										$mtn_rechargecard_api_discount .= $get_rechargecard_mtn_api_website["discount_1"] . "," . $get_rechargecard_mtn_api_website["discount_2"] . "," . $get_rechargecard_mtn_api_website["discount_3"] . "," . $get_rechargecard_mtn_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No rechargecard API was Installed!';
								}

								$mtn_exp_rechargecard_api_discount = array_filter(explode(",", trim($mtn_rechargecard_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $mtn_exp_rechargecard_api_discount[0]; ?>" name="mtn-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $mtn_exp_rechargecard_api_discount[1]; ?>" name="mtn-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $mtn_exp_rechargecard_api_discount[2]; ?>" name="mtn-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $mtn_exp_rechargecard_api_discount[3]; ?>" name="mtn-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">AIRTEL RECHARGE CARD ROUTE</span><br>
								</legend>
								<?php
								$rechargecard_airtel_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $rechargecard_table_name);
								if (mysqli_num_rows($rechargecard_airtel_select_api) > 0) {
									while ($rechargecard_api_list = mysqli_fetch_assoc($rechargecard_airtel_select_api)) {
										$get_rechargecard_airtel_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $rechargecard_network_running_table_name . " WHERE network_name='airtel'"));
										if ($rechargecard_api_list["website"] !== $get_rechargecard_airtel_api_website["website"]) {
											echo $rechargecard_api_list["website"] . ' <input value="' . $rechargecard_api_list["website"] . '" name="airtel" type="radio" class="check-box"/><br>';
										} else {
											echo $rechargecard_api_list["website"] . ' <input checked value="' . $rechargecard_api_list["website"] . '" name="airtel" type="radio" class="check-box"/><br>';
										}
										$airtel_rechargecard_api_discount .= $get_rechargecard_airtel_api_website["discount_1"] . "," . $get_rechargecard_airtel_api_website["discount_2"] . "," . $get_rechargecard_airtel_api_website["discount_3"] . "," . $get_rechargecard_airtel_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No rechargecard API was Installed!';
								}

								$airtel_exp_rechargecard_api_discount = array_filter(explode(",", trim($airtel_rechargecard_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $airtel_exp_rechargecard_api_discount[0]; ?>"
										name="airtel-discount-1" type="text" class="input-box half-half-length"
										placeholder="Smart Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $airtel_exp_rechargecard_api_discount[1]; ?>"
										name="airtel-discount-2" type="text" class="input-box half-half-length"
										placeholder="VIP Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $airtel_exp_rechargecard_api_discount[2]; ?>"
										name="airtel-discount-3" type="text" class="input-box half-half-length"
										placeholder="VIP Vendor" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $airtel_exp_rechargecard_api_discount[3]; ?>"
										name="airtel-discount-4" type="text" class="input-box half-half-length"
										placeholder="API Earner" /><span class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>


							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">GLO RECHARGE CARD ROUTE</span><br>
								</legend>
								<?php
								$rechargecard_glo_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $rechargecard_table_name);
								if (mysqli_num_rows($rechargecard_glo_select_api) > 0) {
									while ($rechargecard_api_list = mysqli_fetch_assoc($rechargecard_glo_select_api)) {
										$get_rechargecard_glo_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $rechargecard_network_running_table_name . " WHERE network_name='glo'"));
										if ($rechargecard_api_list["website"] !== $get_rechargecard_glo_api_website["website"]) {
											echo $rechargecard_api_list["website"] . ' <input value="' . $rechargecard_api_list["website"] . '" name="glo" type="radio" class="check-box"/><br>';
										} else {
											echo $rechargecard_api_list["website"] . ' <input checked value="' . $rechargecard_api_list["website"] . '" name="glo" type="radio" class="check-box"/><br>';
										}
										$glo_rechargecard_api_discount .= $get_rechargecard_glo_api_website["discount_1"] . "," . $get_rechargecard_glo_api_website["discount_2"] . "," . $get_rechargecard_glo_api_website["discount_3"] . "," . $get_rechargecard_glo_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No rechargecard API was Installed!';
								}

								$glo_exp_rechargecard_api_discount = array_filter(explode(",", trim($glo_rechargecard_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $glo_exp_rechargecard_api_discount[0]; ?>" name="glo-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $glo_exp_rechargecard_api_discount[1]; ?>" name="glo-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $glo_exp_rechargecard_api_discount[2]; ?>" name="glo-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $glo_exp_rechargecard_api_discount[3]; ?>" name="glo-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">9MOBILE RECHARGE CARD ROUTE</span><br>
								</legend>
								<?php
								$rechargecard_9mobile_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $rechargecard_table_name);
								if (mysqli_num_rows($rechargecard_9mobile_select_api) > 0) {
									while ($rechargecard_api_list = mysqli_fetch_assoc($rechargecard_9mobile_select_api)) {
										$get_rechargecard_9mobile_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $rechargecard_network_running_table_name . " WHERE network_name='9mobile'"));
										if ($rechargecard_api_list["website"] !== $get_rechargecard_9mobile_api_website["website"]) {
											echo $rechargecard_api_list["website"] . ' <input value="' . $rechargecard_api_list["website"] . '" name="9mobile" type="radio" class="check-box"/><br>';
										} else {
											echo $rechargecard_api_list["website"] . ' <input checked value="' . $rechargecard_api_list["website"] . '" name="9mobile" type="radio" class="check-box"/><br>';
										}
										$etisalat_rechargecard_api_discount .= $get_rechargecard_9mobile_api_website["discount_1"] . "," . $get_rechargecard_9mobile_api_website["discount_2"] . "," . $get_rechargecard_9mobile_api_website["discount_3"] . "," . $get_rechargecard_9mobile_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No rechargecard API was Installed!';
								}

								$etisalat_exp_rechargecard_api_discount = array_filter(explode(",", trim($etisalat_rechargecard_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $etisalat_exp_rechargecard_api_discount[0]; ?>"
										name="9mobile-discount-1" type="text" class="input-box half-half-length"
										placeholder="Smart Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $etisalat_exp_rechargecard_api_discount[1]; ?>"
										name="9mobile-discount-2" type="text" class="input-box half-half-length"
										placeholder="VIP Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $etisalat_exp_rechargecard_api_discount[2]; ?>"
										name="9mobile-discount-3" type="text" class="input-box half-half-length"
										placeholder="VIP Vendor" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $etisalat_exp_rechargecard_api_discount[3]; ?>"
										name="9mobile-discount-4" type="text" class="input-box half-half-length"
										placeholder="API Earner" /><span class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>

							<input name="run-api" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-85 system-width-65"
								value="Run API" />
						</form>
					</fieldset><br>
					<?php
				}
			}
			?>
		</div>
	</center>
	<script>
		function updateAPIkey() {
			const apikey = document.getElementById("update-api-key");
			document.getElementById("apikey").value = apikey.options[apikey.selectedIndex].getAttribute("data-apikey");
		}
	</script>

	<?php include("../include/admin-footer-html.php"); ?>
</body>

</html>