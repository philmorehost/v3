<?php session_start();
if (!isset($_SESSION["admin"])) {
	header("Location: /admin/login.php");
} else {
	include("../include/admin-config.php");
	include("../include/admin-details.php");
}

if ($conn_server_db == true) {
	$datacard_table_name = "datacard_api";
	$datacard_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $datacard_table_name . "(website VARCHAR(225) NOT NULL, apikey VARCHAR(225))";
	if (mysqli_query($conn_server_db, $datacard_apikey_db_table) == true) {
	}


	$datacard_select_running_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $datacard_table_name . " LIMIT 1");
	if (mysqli_num_rows($datacard_select_running_api) > 0) {
		while ($datacard_api_running_list = mysqli_fetch_assoc($datacard_select_running_api)) {
			$first_datacard_api_website_row = $datacard_api_running_list["website"];
			$datacard_network_running_table_name = "datacard_network_running_api";
			$datacard_network_running_db_table = "CREATE TABLE IF NOT EXISTS " . $datacard_network_running_table_name . "(network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))";
			if (mysqli_query($conn_server_db, $datacard_network_running_db_table) == true) {
				if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $datacard_network_running_table_name)) == 0) {
					$insert_datacard_network_running_api = "INSERT INTO " . $datacard_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('mtn', '$first_datacard_api_website_row','1','1','1','1');";
					$insert_datacard_network_running_api .= "INSERT INTO " . $datacard_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('airtel', '$first_datacard_api_website_row','1','1','1','1');";
					$insert_datacard_network_running_api .= "INSERT INTO " . $datacard_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('glo', '$first_datacard_api_website_row','1','1','1','1');";
					$insert_datacard_network_running_api .= "INSERT INTO " . $datacard_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('9mobile', '$first_datacard_api_website_row','1','1','1','1')";
					if (mysqli_multi_query($conn_server_db, $insert_datacard_network_running_api) == true) {

					}
				}
			}
		}
	}


	$datacard_network_table_name = "datacard_network_status";
	$datacard_network_db_table = "CREATE TABLE IF NOT EXISTS " . $datacard_network_table_name . "(network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)";
	if (mysqli_query($conn_server_db, $datacard_network_db_table) == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $datacard_network_table_name)) == 0) {
			$insert_network_status = "INSERT INTO " . $datacard_network_table_name . " (network_name, network_status) VALUES ('mtn', 'active');";
			$insert_network_status .= "INSERT INTO " . $datacard_network_table_name . " (network_name, network_status) VALUES ('airtel', 'active');";
			$insert_network_status .= "INSERT INTO " . $datacard_network_table_name . " (network_name, network_status) VALUES ('glo', 'active');";
			$insert_network_status .= "INSERT INTO " . $datacard_network_table_name . " (network_name, network_status) VALUES ('9mobile', 'active')";

			if (mysqli_multi_query($conn_server_db, $insert_network_status) == true) {

			}
		}
	}
}


if (isset($_POST["install-api"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$datacard_api_website = "INSERT INTO " . $datacard_table_name . " (website) VALUES ('$api_name')";

	$check_datacard_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $datacard_table_name . " WHERE website='$api_name'");

	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_datacard_select_api) == 0) {
			if (mysqli_query($conn_server_db, $datacard_api_website) == true) {
				$add_api_message = ucwords($api_name) . " datacard API installed successfully! ";
			}
		} else {
			$add_api_message = ucwords($api_name) . " datacard API has already been Installed! ";
		}
	}

}

if (isset($_POST["update-apikey"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$apikey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["apikey"]));

	$datacard_api_website = "UPDATE " . $datacard_table_name . " SET apikey='$apikey' WHERE website='$api_name'";
	$check_datacard_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $datacard_table_name . " WHERE website='$api_name'");
	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_datacard_select_api) > 0) {
			if (mysqli_query($conn_server_db, $datacard_api_website) == true) {
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



	$update_mtn_network_status = "UPDATE " . $datacard_network_table_name . " SET network_status='$mtn_status' WHERE network_name='mtn'";
	$update_airtel_network_status = "UPDATE " . $datacard_network_table_name . " SET network_status='$airtel_status' WHERE network_name='airtel'";
	$update_glo_network_status = "UPDATE " . $datacard_network_table_name . " SET network_status='$glo_status' WHERE network_name='glo'";
	$update_etisalat_network_status = "UPDATE " . $datacard_network_table_name . " SET network_status='$etisalat_status' WHERE network_name='9mobile'";

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

if (isset($_POST["generate-datacard"])) {
	$api_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-web"]));
	$sp_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["sp-name"]));
	$sp_datasize = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["sp-size"]));
	$qty = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["qty"]));
	if (!empty($api_website) && !empty($sp_name) && !empty($sp_datasize) && !empty($qty)) {

		//GET EACH exam API WEBSITE
		$get_datacard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM datacard_network_running_api WHERE network_name='$sp_name'"));

		$get_datacard_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM datacard_api WHERE website='$api_website'"));
		$apikey = $get_datacard_apikey["apikey"];
		$raw_number = "123456789012345678901234567890";
		$reference = substr(str_shuffle($raw_number), 0, 15);

		if ($api_website == "clubkonnect.com") {
			$clubKonnectUserID = array_filter(explode(":", trim($apikey)))[0];
			$clubKonnectApikey = array_filter(explode(":", trim($apikey)))[1];
		}
		if ($api_website == "epins.com.ng") {
			if ($sp_name == "mtn") {
				$mobileCode = "01";
				$ePinsDataPlan = array("1.5gb" => "1500");
			}

			if ($sp_name == "glo") {
				$mobileCode = "02";
				$ePinsDataPlan = array();
			}

			if ($sp_name == "9mobile") {
				$mobileCode = "03";
				$ePinsDataPlan = array();
			}

			if ($sp_name == "airtel") {
				$mobileCode = "04";
				$ePinsDataPlan = array();
			}

			if (!empty($ePinsDataPlan[$sp_datasize])) {
				$datacardPurchase = curl_init();
				$datacardApiUrl = "https://api.epins.com.ng/v2/autho/datacard/";
				curl_setopt($datacardPurchase, CURLOPT_URL, $datacardApiUrl);

				curl_setopt($datacardPurchase, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($datacardPurchase, CURLOPT_POST, true);
				$datacardPurchaseData = json_encode(array("apikey" => $apikey, "service" => "datacard", "network" => $mobileCode, "pinQuantity" => $qty, "DataPlan" => $ePinsDataPlan[str_replace("_", ".", $sp_datasize)], "ref" => $reference), true);
				curl_setopt($datacardPurchase, CURLOPT_POSTFIELDS, $datacardPurchaseData);

				curl_setopt($datacardPurchase, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($datacardPurchase, CURLOPT_SSL_VERIFYPEER, false);

				$GetdatacardJSON = curl_exec($datacardPurchase);
				$datacardJSONObj = json_decode($GetdatacardJSON, true);
				if ($GetdatacardJSON == true) {
					if (in_array($datacardJSONObj["code"], array(101))) {
						$explodedEPins = array_filter(explode("\n", trim($datacardJSONObj["description"]["PIN"])));
						foreach ($explodedEPins as $allCardArray) {
							$allGeneratedPINs .= $allCardArray . "\n";
						}
						$fetch_all_datacard = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM admin_data_card WHERE network_name='$sp_name'"));
						$allCardsJoined = $fetch_all_datacard["card_" . str_replace(".", "_", $sp_datasize)] . "\n" . $allGeneratedPINs;

						$ref_id = $reference;
						if (mysqli_query($conn_server_db, "UPDATE admin_data_card SET card_" . str_replace(".", "_", $sp_datasize) . "='$allCardsJoined' WHERE network_name='$sp_name'") == true) {
							$datacard_generate_message = "Data PINs Updated Successfully! ";
						} else {
							$datacard_generate_message = "Error: Data PINs fails to update " . mysqli_error($conn_server_db) . $allCardsJoined;
						}
					}

					if (!in_array($datacardJSONObj["code"], array(101))) {
						$datacard_generate_message = "Error: Cannot purchased Data PIN <b>" . $datacardJSONObj["description"]["response_description"] . "</b>";
					}

				} else {
					$datacard_generate_message = "Server currently unavailable";
				}
			} else {
				$datacard_generate_message = "Data Size not Available";
			}

		}
	}

	if ($api_website == "legitdataway.com") {
		if ($sp_name == "mtn") {
			$mobileCode = "1";
			$ePinsDataPlan = array("1gb" => "3", "1.5gb" => "1", "2gb" => "4");
		}

		if ($sp_name == "glo") {
			$mobileCode = "3";
			$ePinsDataPlan = array();
		}

		if ($sp_name == "9mobile") {
			$mobileCode = "4";
			$ePinsDataPlan = array();
		}

		if ($sp_name == "airtel") {
			$mobileCode = "2";
			$ePinsDataPlan = array();
		}

		$bilalAccessToken = curl_init();
		$dataApiUrl = "https://legitdataway.com/api/user";
		curl_setopt($bilalAccessToken, CURLOPT_URL, $dataApiUrl);
		curl_setopt($bilalAccessToken, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($bilalAccessToken, CURLOPT_POST, true);
		$bilalTokenPostHeader = array("Authorization: Basic " . base64_encode($apikey));
		curl_setopt($bilalAccessToken, CURLOPT_HTTPHEADER, $bilalTokenPostHeader);

		curl_setopt($bilalAccessToken, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($bilalAccessToken, CURLOPT_SSL_VERIFYPEER, false);

		$GetBilalJSON = curl_exec($bilalAccessToken);
		$bilalJSONObj = json_decode($GetBilalJSON, true);

		if (($GetBilalJSON == true) && ($bilalJSONObj["status"] == "success")) {
			if (!empty($ePinsDataPlan[$sp_datasize])) {
				$datacardPurchase = curl_init();
				$datacardApiUrl = "https://legitdataway.com/api/data_card";
				curl_setopt($datacardPurchase, CURLOPT_URL, $datacardApiUrl);

				curl_setopt($datacardPurchase, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($datacardPurchase, CURLOPT_POST, true);
				$datacardPostHeader = array("Authorization: Token " . $bilalJSONObj["AccessToken"], "Content-Type: application/json");
				curl_setopt($datacardPurchase, CURLOPT_HTTPHEADER, $datacardPostHeader);

				$datacardPurchaseData = json_encode(array("network" => $mobileCode, "quantity" => $qty, "plan_type" => $ePinsDataPlan[str_replace("_", ".", $sp_datasize)], "card_name" => $_SERVER["HTTP_HOST"]), true);
				curl_setopt($datacardPurchase, CURLOPT_POSTFIELDS, $datacardPurchaseData);

				curl_setopt($datacardPurchase, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($datacardPurchase, CURLOPT_SSL_VERIFYPEER, false);

				$GetdatacardJSON = curl_exec($datacardPurchase);
				$datacardJSONObj = json_decode($GetdatacardJSON, true);
				if ($GetdatacardJSON == true) {
					if (in_array($datacardJSONObj["status"], array("success"))) {
						$explodedEPins = array_filter(explode(",", trim($datacardJSONObj["pin"])));
						foreach ($explodedEPins as $allCardArray) {
							$allGeneratedPINs .= $allCardArray . "\n";
						}
						$fetch_all_datacard = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM admin_data_card WHERE network_name='$sp_name'"));
						$allCardsJoined = $fetch_all_datacard["card_" . str_replace(".", "_", $sp_datasize)] . "\n" . $allGeneratedPINs;

						$ref_id = $reference;
						if (mysqli_query($conn_server_db, "UPDATE admin_data_card SET card_" . str_replace(".", "_", $sp_datasize) . "='$allCardsJoined' WHERE network_name='$sp_name'") == true) {
							$datacard_generate_message = "Data PINs Updated Successfully! ";
						} else {
							$datacard_generate_message = "Error: Data PINs fails to update " . mysqli_error($conn_server_db) . $allCardsJoined;
						}
					}

					if (!in_array($datacardJSONObj["status"], array("success"))) {
						$datacard_generate_message = "Error: Cannot purchased Data PIN <b>" . $datacardJSONObj["status"] . " " . $datacardJSONObj["message"] . "</b>";
					}

				} else {
					$datacard_generate_message = "Server currently unavailable";
				}
			} else {
				$datacard_generate_message = "Data Size not Available";
			}
		} else {
			$datacard_generate_message = "Error: Cant Authenticate User";
		}

	}

	header("refresh:5;url=" . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-datacard"])) {
	$company_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["company-name"]));
	$carrier = mysqli_real_escape_string($conn_server_db, strtolower(strip_tags($_POST["sp-name"])));
	$datasize = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["sp-datasize"]));
	$all_cards_pin = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace("\r\n", "\n", $_POST["all-datacard_" . $carrier . "_" . str_replace(".", "_", $datasize) . ""])));

	if (mysqli_query($conn_server_db, "UPDATE admin_data_card SET company_name='" . $company_name . "'") == true) {
		if (mysqli_query($conn_server_db, "UPDATE admin_data_card SET card_" . str_replace(".", "_", $datasize) . "='" . $all_cards_pin . "' WHERE network_name='" . $carrier . "' ") == true) {
			$datacard_update_message = "Data PINs Updated Successfully! ";
		} else {
			$datacard_update_message = "Error: Data PINs fails to update " . mysqli_error($conn_server_db);
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

	$mtn_network_table_injection = "UPDATE " . $datacard_network_running_table_name . " SET website='$mtn_website', discount_1='$mtn_discount_1', discount_2='$mtn_discount_2', discount_3='$mtn_discount_3', discount_4='$mtn_discount_4' WHERE network_name='mtn'";
	if (mysqli_query($conn_server_db, $mtn_network_table_injection) == true) {
	}

	$airtel_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["airtel"]));
	$airtel_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-1"])));
	$airtel_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-2"])));
	$airtel_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-3"])));
	$airtel_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-4"])));

	$airtel_network_table_injection = "UPDATE " . $datacard_network_running_table_name . " SET website='$airtel_website', discount_1='$airtel_discount_1', discount_2='$airtel_discount_2', discount_3='$airtel_discount_3', discount_4='$airtel_discount_4' WHERE network_name='airtel'";
	if (mysqli_query($conn_server_db, $airtel_network_table_injection) == true) {
	}

	$glo_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["glo"]));
	$glo_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-1"])));
	$glo_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-2"])));
	$glo_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-3"])));
	$glo_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-4"])));

	$glo_network_table_injection = "UPDATE " . $datacard_network_running_table_name . " SET website='$glo_website', discount_1='$glo_discount_1', discount_2='$glo_discount_2', discount_3='$glo_discount_3', discount_4='$glo_discount_4' WHERE network_name='glo'";
	if (mysqli_query($conn_server_db, $glo_network_table_injection) == true) {
	}

	$etisalat_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["9mobile"]));
	$etisalat_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-1"])));
	$etisalat_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-2"])));
	$etisalat_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-3"])));
	$etisalat_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-4"])));

	$etisalat_network_table_injection = "UPDATE " . $datacard_network_running_table_name . " SET website='$etisalat_website', discount_1='$etisalat_discount_1', discount_2='$etisalat_discount_2', discount_3='$etisalat_discount_3', discount_4='$etisalat_discount_4' WHERE network_name='9mobile'";
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
					<span class="font-size-2 font-family-1">INSTALL DATA CARD API</span>
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
						<option disabled hidden selected>Install DATA CARD API</option>
						<option value='<?php echo $_SERVER["HTTP_HOST"]; ?>'><?php echo $_SERVER["HTTP_HOST"]; ?>
						</option>
						<option value="datagifting.com.ng">datagifting.com.ng</option>
						<option value="epins.com.ng">epins.com.ng</option>
						<option value="legitdataway.com">legitdataway.com</option>
					</select>
					<input name="install-api" type="submit"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25"
						value="Install API" />
				</form>
			</fieldset><br>

			<?php
			$check_count_datacard_table_name = mysqli_query($conn_server_db, "SELECT website FROM " . $datacard_table_name);
			if ($check_count_datacard_table_name == true) {
				if (mysqli_num_rows($check_count_datacard_table_name) > 0) {

					?>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPDATE DATA CARD API KEY</span>
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
								$datacard_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $datacard_table_name);
								if (mysqli_num_rows($datacard_select_api) > 0) {
									while ($datacard_api_list = mysqli_fetch_assoc($datacard_select_api)) {
										echo '<option data-apikey="' . $datacard_api_list["apikey"] . '" value="' . $datacard_api_list["website"] . '">' . $datacard_api_list["website"] . "</option>";
									}
								} else {
									echo '<option selected hidden value="">No DATA CARD API was Installed!</option>';
								}

								?>
							</select>
							<input name="apikey" id="apikey" type="text"
								class="input-box mobile-width-93 system-width-60 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
								placeholder="Apikey " />
							<input name="update-apikey" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Update API key" />
						</form>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">ENABLE/DISABLE DATA CARD NETWORK</span>
						</legend>
						<?php
						$datacard_network_select_api = mysqli_query($conn_server_db, "SELECT network_name, network_status FROM " . $datacard_network_table_name);
						if (mysqli_num_rows($datacard_network_select_api) > 0) {
							while ($datacard_network_status_list = mysqli_fetch_assoc($datacard_network_select_api)) {
								;
								$all_network_status .= $datacard_network_status_list["network_status"] . ",";
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
							<span class="font-size-2 font-family-1">GENERATE DATA CARD</span>
						</legend>

						<form method="post">
							<?php
							if ($datacard_generate_message == true) {
								?>
								<div id="font-color-1" class="message-box font-size-2"><?php echo $datacard_generate_message; ?>
								</div>
								<?php
							}
							?>

							<select name="api-web" id="api-web-g"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-60">
								<option disabled hidden selected>Choose DATA CARD API</option>
								<?php
								$datacard_genselect_api = mysqli_query($conn_server_db, "SELECT website FROM " . $datacard_table_name);
								if (mysqli_num_rows($datacard_genselect_api) > 0) {
									while ($datacard_api_list = mysqli_fetch_assoc($datacard_genselect_api)) {
										if ($datacard_api_list["website"] !== $_SERVER["HTTP_HOST"]) {
											echo '<option value="' . $datacard_api_list["website"] . '">' . $datacard_api_list["website"] . "</option>";
										}
									}
								} else {
									echo '<option selected hidden value="">No DATA CARD API was Installed!</option>';
								}
								?>
							</select>
							<select name="sp-name" id="datacard-sp-g"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-25">
								<option disabled hidden selected>Choose Network (Service Provider)</option>
								<option value="mtn">MTN</option>
								<!--<option value="airtel">Airtel</option>
					<option value="glo">GLO</option>
					<option value="9mobile">9mobile</option>-->
							</select><br>
							<select name="sp-size" id="datacard-sp-price-g"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-60">
								<option disabled hidden selected>Choose Amount</option>
								<option value="1gb">1gb</option>
								<option value="1.5gb">1.5gb</option>
								<option value="2gb">2gb</option>
							</select>
							<input name="qty" id="datacard-qty-g" type="number"
								class="input-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-25"
								placeholder="Quantity e.g 10" value="" />
							<input style="display:none;" name="generate-datacard" id="generate-datacard-g" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Generate Data Card" />

						</form>
						<script>
							setInterval(function () {
								let datacard_sp_g = document.getElementById("datacard-sp-g").value;
								let datacard_sp_price_g = document.getElementById("datacard-sp-price-g").value;
								let datacard_qty_g = document.getElementById("datacard-qty-g").value;

								if ((document.getElementById("api-web-g").value !== "") && (datacard_sp_g == "mtn" || datacard_sp_g == "airtel" || datacard_sp_g == "glo" || datacard_sp_g == "9mobile") && (datacard_qty_g >= 1)) {
									document.getElementById("generate-datacard-g").style.display = "inline-block";
								} else {
									document.getElementById("generate-datacard-g").style.display = "none";
								}
							});
						</script>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPLOAD OR UPDATE DATA CARD</span>
						</legend>
						<?php

						$selectdatacard_company_name = mysqli_fetch_array(mysqli_query($conn_server_db, "SELECT company_name FROM admin_data_card WHERE 1"));
						$select_datacard_mtn_cards_1gb = mysqli_query($conn_server_db, "SELECT card_1gb FROM admin_data_card WHERE network_name='mtn'");
						$select_datacard_mtn_cards_1_5gb = mysqli_query($conn_server_db, "SELECT card_1_5gb FROM admin_data_card WHERE network_name='mtn'");
						$select_datacard_mtn_cards_2gb = mysqli_query($conn_server_db, "SELECT card_2gb FROM admin_data_card WHERE network_name='mtn'");

						/*$select_datacard_airtel_cards_100 = mysqli_query($conn_server_db, "SELECT card_100 FROM admin_data_card WHERE network_name='airtel'");
										  $select_datacard_glo_cards_100 = mysqli_query($conn_server_db, "SELECT card_100 FROM admin_data_card WHERE network_name='glo'");
										  $select_datacard_9mobile_cards_100 = mysqli_query($conn_server_db, "SELECT card_100 FROM admin_data_card WHERE network_name='9mobile'");
										  
										  $select_datacard_mtn_cards_200 = mysqli_query($conn_server_db, "SELECT card_200 FROM admin_data_card WHERE network_name='mtn'");
										  $select_datacard_airtel_cards_200 = mysqli_query($conn_server_db, "SELECT card_200 FROM admin_data_card WHERE network_name='airtel'");
										  $select_datacard_glo_cards_200 = mysqli_query($conn_server_db, "SELECT card_200 FROM admin_data_card WHERE network_name='glo'");
										  $select_datacard_9mobile_cards_200 = mysqli_query($conn_server_db, "SELECT card_200 FROM admin_data_card WHERE network_name='9mobile'");
										  
										  $select_datacard_mtn_cards_500 = mysqli_query($conn_server_db, "SELECT card_500 FROM admin_data_card WHERE network_name='mtn'");
										  $select_datacard_airtel_cards_500 = mysqli_query($conn_server_db, "SELECT card_500 FROM admin_data_card WHERE network_name='airtel'");
										  $select_datacard_glo_cards_500 = mysqli_query($conn_server_db, "SELECT card_500 FROM admin_data_card WHERE network_name='glo'");
										  $select_datacard_9mobile_cards_500 = mysqli_query($conn_server_db, "SELECT card_500 FROM admin_data_card WHERE network_name='9mobile'");*/


						?>
						<form method="post">
							<?php
							if ($datacard_update_message == true) {
								?>
								<div id="font-color-1" class="message-box font-size-2"><?php echo $datacard_update_message; ?></div>
								<?php
							}
							?>
							<input style="display:none;" name="company-name" id="datacard-company-name" type="text"
								class="input-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="Company Name"
								value="<?php echo $selectdatacard_company_name['company_name']; ?>" />
							<select onchange="updatedatacardManually(1);" name="sp-name" id="datacard-sp"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-60">
								<option disabled hidden selected>Choose Network (Service Provider)</option>
								<option value="mtn">MTN</option>
								<!--<option value="airtel">Airtel</option>
						<option value="glo">GLO</option>
						<option value="9mobile">9mobile</option>-->
							</select>
							<select style="display:none;" onchange="updatedatacardManually(2);" name="sp-datasize"
								id="datacard-sp-datasize"
								class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-25">
								<option disabled hidden selected>Choose Amount</option>
								<option value="1gb">1gb</option>
								<option value="1_5gb">1.5gb</option>
								<option value="2gb">2gb</option>
							</select><br>

							<textarea type="number" style="display:none;" name="all-datacard_mtn_1gb"
								id="datacard-textarea-mtn-1gb"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_datacard_mtn_cards_1gb) > 0) {
									while ($mtn_cardlists = mysqli_fetch_array($select_datacard_mtn_cards_1gb)) {
										echo trim($mtn_cardlists["card_1gb"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-datacard_mtn_1_5gb"
								id="datacard-textarea-mtn-1_5gb"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_datacard_mtn_cards_1_5gb) > 0) {
									while ($mtn_cardlists = mysqli_fetch_array($select_datacard_mtn_cards_1_5gb)) {
										echo trim($mtn_cardlists["card_1_5gb"]);
									}
								}
								?></textarea>

							<textarea type="number" style="display:none;" name="all-datacard_mtn_2gb"
								id="datacard-textarea-mtn-2gb"
								class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85"
								placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
								if (mysqli_num_rows($select_datacard_mtn_cards_2gb) > 0) {
									while ($mtn_cardlists = mysqli_fetch_array($select_datacard_mtn_cards_2gb)) {
										echo trim($mtn_cardlists["card_2gb"]);
									}
								}
								?></textarea>
							<!--<textarea type="number" style="display:none;" name="all-datacard_mtn_200" id="datacard-textarea-mtn-200" class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85" placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
							if (mysqli_num_rows($select_datacard_mtn_cards_200) > 0) {
								while ($mtn_cardlists = mysqli_fetch_array($select_datacard_mtn_cards_200)) {
									echo trim($mtn_cardlists["card_200"]);
								}
							}
							?></textarea>
					
					<textarea type="number" style="display:none;" name="all-datacard_mtn_500" id="datacard-textarea-mtn-500" class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85" placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
					if (mysqli_num_rows($select_datacard_mtn_cards_500) > 0) {
						while ($mtn_cardlists = mysqli_fetch_array($select_datacard_mtn_cards_500)) {
							echo trim($mtn_cardlists["card_500"]);
						}
					}
					?></textarea>
					
					<textarea type="number" style="display:none;" name="all-datacard_airtel_100" id="datacard-textarea-airtel-100" class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85" placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
					if (mysqli_num_rows($select_datacard_airtel_cards_100) > 0) {
						while ($airtel_cardlists = mysqli_fetch_array($select_datacard_airtel_cards_100)) {
							echo trim($airtel_cardlists["card_100"]);
						}
					}
					?></textarea>
					
					<textarea type="number" style="display:none;" name="all-datacard_airtel_200" id="datacard-textarea-airtel-200" class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85" placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
					if (mysqli_num_rows($select_datacard_airtel_cards_200) > 0) {
						while ($airtel_cardlists = mysqli_fetch_array($select_datacard_airtel_cards_200)) {
							echo trim($airtel_cardlists["card_200"]);
						}
					}
					?></textarea>
					
					<textarea type="number" style="display:none;" name="all-datacard_airtel_500" id="datacard-textarea-airtel-500" class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85" placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
					if (mysqli_num_rows($select_datacard_airtel_cards_500) > 0) {
						while ($airtel_cardlists = mysqli_fetch_array($select_datacard_airtel_cards_500)) {
							echo trim($airtel_cardlists["card_500"]);
						}
					}
					?></textarea>
					
					<textarea type="number" style="display:none;" name="all-datacard_glo_100" id="datacard-textarea-glo-100" class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85" placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
					if (mysqli_num_rows($select_datacard_glo_cards_100) > 0) {
						while ($glo_cardlists = mysqli_fetch_array($select_datacard_glo_cards_100)) {
							echo trim($glo_cardlists["card_100"]);
						}
					}
					?></textarea>
					
					<textarea type="number" style="display:none;" name="all-datacard_glo_200" id="datacard-textarea-glo-200" class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85" placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
					if (mysqli_num_rows($select_datacard_glo_cards_200) > 0) {
						while ($glo_cardlists = mysqli_fetch_array($select_datacard_glo_cards_200)) {
							echo trim($glo_cardlists["card_200"]);
						}
					}
					?></textarea>
					
					<textarea type="number" style="display:none;" name="all-datacard_glo_500" id="datacard-textarea-glo-500" class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85" placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
					if (mysqli_num_rows($select_datacard_glo_cards_500) > 0) {
						while ($glo_cardlists = mysqli_fetch_array($select_datacard_glo_cards_500)) {
							echo trim($glo_cardlists["card_500"]);
						}
					}
					?></textarea>
					
					<textarea type="number" style="display:none;" name="all-datacard_9mobile_100" id="datacard-textarea-9mobile-100" class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85" placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
					if (mysqli_num_rows($select_datacard_9mobile_cards_100) > 0) {
						while ($etisalat_cardlists = mysqli_fetch_array($select_datacard_9mobile_cards_100)) {
							echo trim($etisalat_cardlists["card_100"]);
						}
					}
					?></textarea>
					
					<textarea type="number" style="display:none;" name="all-datacard_9mobile_200" id="datacard-textarea-9mobile-200" class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85" placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
					if (mysqli_num_rows($select_datacard_9mobile_cards_200) > 0) {
						while ($etisalat_cardlists = mysqli_fetch_array($select_datacard_9mobile_cards_200)) {
							echo trim($etisalat_cardlists["card_200"]);
						}
					}
					?></textarea>
					
					<textarea type="number" style="display:none;" name="all-datacard_9mobile_500" id="datacard-textarea-9mobile-500" class="textarea-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-85" placeholder="A Card Per Line e.g (PIN:Serial No)"><?php
					if (mysqli_num_rows($select_datacard_9mobile_cards_500) > 0) {
						while ($etisalat_cardlists = mysqli_fetch_array($select_datacard_9mobile_cards_500)) {
							echo trim($etisalat_cardlists["card_500"]);
						}
					}
					?></textarea>-->
							<div id="font-color-1" class="message-box font-size-2"><span id="datacardCounter"></span></div>
							<input style="display:none;" name="update-datacard" type="submit" id="datacard-updatebtn"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Upload Card" /><br>
							<script>

								function updatedatacardManually(nub) {
									const datacardSP = document.getElementById("datacard-sp").value;
									const datacardSPDataSize = document.getElementById("datacard-sp-datasize");
									const datacardMtnTA1gb = document.getElementById("datacard-textarea-mtn-1gb");
									const datacardMtnTA1_5gb = document.getElementById("datacard-textarea-mtn-1_5gb");
									const datacardMtnTA2gb = document.getElementById("datacard-textarea-mtn-2gb");

									const datacardUB = document.getElementById("datacard-updatebtn");
									const datacardCompanyName = document.getElementById("datacard-company-name");
									if (nub == "1") {
										datacardSPDataSize.style.display = "inline-block";
										datacardSPDataSize.options[0].selected = true;
										datacardMtnTA1_5gb.style.display = "none";

										datacardUB.style.display = "none";
										datacardCompanyName.style.display = "none";
									}

									if (nub == "2") {
										if ((datacardSP == "mtn") || (datacardSP == "airtel") || (datacardSP == "glo") || (datacardSP == "9mobile")) {
											datacardSPDataSize.style.display = "inline-block";
											datacardUB.style.display = "inline-block";
											datacardCompanyName.style.display = "inline-block";


											const countRemainingPIN = "datacard-textarea-" + datacardSP.toLowerCase() + "-" + datacardSPDataSize.value;

											document.getElementById("datacardCounter").style.display = "inline-block";
											if (document.getElementById(countRemainingPIN).value != "") {
												document.getElementById("datacardCounter").innerHTML = (document.getElementById(countRemainingPIN).value.split("\n").length) + " PIN Remaining";
											} else {
												document.getElementById("datacardCounter").innerHTML = "0 PIN Remaining";
											}
											if (datacardSP == "mtn" && datacardSPDataSize.value !== "") {
												if (datacardSPDataSize.value == "1gb") {
													datacardMtnTA1gb.style.display = "inline-block";
												} else {
													datacardMtnTA1gb.style.display = "none";
												}

												if (datacardSPDataSize.value == "1_5gb") {
													datacardMtnTA1_5gb.style.display = "inline-block";
												} else {
													datacardMtnTA1_5gb.style.display = "none";
												}

												if (datacardSPDataSize.value == "2gb") {
													datacardMtnTA2gb.style.display = "inline-block";
												} else {
													datacardMtnTA2gb.style.display = "none";
												}
											} else {
												datacardMtnTA1gb.style.display = "none";
												datacardMtnTA1_5gb.style.display = "none";
												datacardMtnTA2gb.style.display = "none";
											}

										} else {
											datacardSPDataSize.style.display = "none";
											datacardMtnTA1_5gb.style.display = "none";
											datacardUB.style.display = "none";
											document.getElementById("datacardCounter").style.display = "none";
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
									<span class="font-size-2 font-family-1">MTN DATA CARD ROUTE</span><br>
								</legend>
								<?php
								$datacard_mtn_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $datacard_table_name);
								if (mysqli_num_rows($datacard_mtn_select_api) > 0) {
									while ($datacard_api_list = mysqli_fetch_assoc($datacard_mtn_select_api)) {
										$get_datacard_mtn_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $datacard_network_running_table_name . " WHERE network_name='mtn'"));
										if ($datacard_api_list["website"] !== $get_datacard_mtn_api_website["website"]) {
											echo $datacard_api_list["website"] . ' <input value="' . $datacard_api_list["website"] . '" name="mtn" type="radio" class="check-box"/><br>';
										} else {
											echo $datacard_api_list["website"] . ' <input checked value="' . $datacard_api_list["website"] . '" name="mtn" type="radio" class="check-box"/><br>';
										}
										$mtn_datacard_api_discount .= $get_datacard_mtn_api_website["discount_1"] . "," . $get_datacard_mtn_api_website["discount_2"] . "," . $get_datacard_mtn_api_website["discount_3"] . "," . $get_datacard_mtn_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No datacard API was Installed!';
								}

								$mtn_exp_datacard_api_discount = array_filter(explode(",", trim($mtn_datacard_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $mtn_exp_datacard_api_discount[0]; ?>" name="mtn-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $mtn_exp_datacard_api_discount[1]; ?>" name="mtn-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $mtn_exp_datacard_api_discount[2]; ?>" name="mtn-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $mtn_exp_datacard_api_discount[3]; ?>" name="mtn-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>

							<!--<fieldset>
				<legend>
				<span class="font-size-2 font-family-1">AIRTEL DATA CARD ROUTE</span><br>
				</legend>
				<?php
				$datacard_airtel_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $datacard_table_name);
				if (mysqli_num_rows($datacard_airtel_select_api) > 0) {
					while ($datacard_api_list = mysqli_fetch_assoc($datacard_airtel_select_api)) {
						$get_datacard_airtel_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $datacard_network_running_table_name . " WHERE network_name='airtel'"));
						if ($datacard_api_list["website"] !== $get_datacard_airtel_api_website["website"]) {
							echo $datacard_api_list["website"] . ' <input value="' . $datacard_api_list["website"] . '" name="airtel" type="radio" class="check-box"/><br>';
						} else {
							echo $datacard_api_list["website"] . ' <input checked value="' . $datacard_api_list["website"] . '" name="airtel" type="radio" class="check-box"/><br>';
						}
						$airtel_datacard_api_discount .= $get_datacard_airtel_api_website["discount_1"] . "," . $get_datacard_airtel_api_website["discount_2"] . "," . $get_datacard_airtel_api_website["discount_3"] . "," . $get_datacard_airtel_api_website["discount_4"] . ",";
					}
				} else {
					echo 'No datacard API was Installed!';
				}

				$airtel_exp_datacard_api_discount = array_filter(explode(",", trim($airtel_datacard_api_discount)));
				?>
				
				<fieldset>
				<legend>
				<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
				</legend>
				<span class="font-size-1 font-family-1">Smart Earner</span> - 
				<input value="<?php echo $airtel_exp_datacard_api_discount[0]; ?>" name="airtel-discount-1" type="text" class="input-box half-half-length" placeholder="Smart Earner"/><span class="font-size-2 font-family-1">%</span><br>
				<span class="font-size-1 font-family-1">VIP Earner</span> - 
				<input value="<?php echo $airtel_exp_datacard_api_discount[1]; ?>" name="airtel-discount-2" type="text" class="input-box half-half-length" placeholder="VIP Earner"/><span class="font-size-2 font-family-1">%</span><br>
				<span class="font-size-1 font-family-1">VIP Vendor</span> - 
				<input value="<?php echo $airtel_exp_datacard_api_discount[2]; ?>" name="airtel-discount-3" type="text" class="input-box half-half-length" placeholder="VIP Vendor"/><span class="font-size-2 font-family-1">%</span><br>
				<span class="font-size-1 font-family-1">API Earner</span> - 
				<input value="<?php echo $airtel_exp_datacard_api_discount[3]; ?>" name="airtel-discount-4" type="text" class="input-box half-half-length" placeholder="API Earner"/><span class="font-size-2 font-family-1">%</span><br>
				</fieldset>
				</fieldset>
				
				
				<fieldset>
				<legend>
				<span class="font-size-2 font-family-1">GLO DATA CARD ROUTE</span><br>
				</legend>
				<?php
				$datacard_glo_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $datacard_table_name);
				if (mysqli_num_rows($datacard_glo_select_api) > 0) {
					while ($datacard_api_list = mysqli_fetch_assoc($datacard_glo_select_api)) {
						$get_datacard_glo_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $datacard_network_running_table_name . " WHERE network_name='glo'"));
						if ($datacard_api_list["website"] !== $get_datacard_glo_api_website["website"]) {
							echo $datacard_api_list["website"] . ' <input value="' . $datacard_api_list["website"] . '" name="glo" type="radio" class="check-box"/><br>';
						} else {
							echo $datacard_api_list["website"] . ' <input checked value="' . $datacard_api_list["website"] . '" name="glo" type="radio" class="check-box"/><br>';
						}
						$glo_datacard_api_discount .= $get_datacard_glo_api_website["discount_1"] . "," . $get_datacard_glo_api_website["discount_2"] . "," . $get_datacard_glo_api_website["discount_3"] . "," . $get_datacard_glo_api_website["discount_4"] . ",";
					}
				} else {
					echo 'No datacard API was Installed!';
				}

				$glo_exp_datacard_api_discount = array_filter(explode(",", trim($glo_datacard_api_discount)));
				?>
				
				<fieldset>
				<legend>
				<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
				</legend>
				<span class="font-size-1 font-family-1">Smart Earner</span> - 
				<input value="<?php echo $glo_exp_datacard_api_discount[0]; ?>" name="glo-discount-1" type="text" class="input-box half-half-length" placeholder="Smart Earner"/><span class="font-size-2 font-family-1">%</span><br>
				<span class="font-size-1 font-family-1">VIP Earner</span> - 
				<input value="<?php echo $glo_exp_datacard_api_discount[1]; ?>" name="glo-discount-2" type="text" class="input-box half-half-length" placeholder="VIP Earner"/><span class="font-size-2 font-family-1">%</span><br>
				<span class="font-size-1 font-family-1">VIP Vendor</span> - 
				<input value="<?php echo $glo_exp_datacard_api_discount[2]; ?>" name="glo-discount-3" type="text" class="input-box half-half-length" placeholder="VIP Vendor"/><span class="font-size-2 font-family-1">%</span><br>
				<span class="font-size-1 font-family-1">API Earner</span> - 
				<input value="<?php echo $glo_exp_datacard_api_discount[3]; ?>" name="glo-discount-4" type="text" class="input-box half-half-length" placeholder="API Earner"/><span class="font-size-2 font-family-1">%</span><br>
				</fieldset>
				</fieldset>
				
				<fieldset>
				<legend>
				<span class="font-size-2 font-family-1">9MOBILE DATA CARD ROUTE</span><br>
				</legend>
				<?php
				$datacard_9mobile_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $datacard_table_name);
				if (mysqli_num_rows($datacard_9mobile_select_api) > 0) {
					while ($datacard_api_list = mysqli_fetch_assoc($datacard_9mobile_select_api)) {
						$get_datacard_9mobile_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $datacard_network_running_table_name . " WHERE network_name='9mobile'"));
						if ($datacard_api_list["website"] !== $get_datacard_9mobile_api_website["website"]) {
							echo $datacard_api_list["website"] . ' <input value="' . $datacard_api_list["website"] . '" name="9mobile" type="radio" class="check-box"/><br>';
						} else {
							echo $datacard_api_list["website"] . ' <input checked value="' . $datacard_api_list["website"] . '" name="9mobile" type="radio" class="check-box"/><br>';
						}
						$etisalat_datacard_api_discount .= $get_datacard_9mobile_api_website["discount_1"] . "," . $get_datacard_9mobile_api_website["discount_2"] . "," . $get_datacard_9mobile_api_website["discount_3"] . "," . $get_datacard_9mobile_api_website["discount_4"] . ",";
					}
				} else {
					echo 'No datacard API was Installed!';
				}

				$etisalat_exp_datacard_api_discount = array_filter(explode(",", trim($etisalat_datacard_api_discount)));
				?>
				
				<fieldset>
				<legend>
				<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
				</legend>
				<span class="font-size-1 font-family-1">Smart Earner</span> - 
				<input value="<?php echo $etisalat_exp_datacard_api_discount[0]; ?>" name="9mobile-discount-1" type="text" class="input-box half-half-length" placeholder="Smart Earner"/><span class="font-size-2 font-family-1">%</span><br>
				<span class="font-size-1 font-family-1">VIP Earner</span> - 
				<input value="<?php echo $etisalat_exp_datacard_api_discount[1]; ?>" name="9mobile-discount-2" type="text" class="input-box half-half-length" placeholder="VIP Earner"/><span class="font-size-2 font-family-1">%</span><br>
				<span class="font-size-1 font-family-1">VIP Vendor</span> - 
				<input value="<?php echo $etisalat_exp_datacard_api_discount[2]; ?>" name="9mobile-discount-3" type="text" class="input-box half-half-length" placeholder="VIP Vendor"/><span class="font-size-2 font-family-1">%</span><br>
				<span class="font-size-1 font-family-1">API Earner</span> - 
				<input value="<?php echo $etisalat_exp_datacard_api_discount[3]; ?>" name="9mobile-discount-4" type="text" class="input-box half-half-length" placeholder="API Earner"/><span class="font-size-2 font-family-1">%</span><br>
				</fieldset>
				</fieldset>-->

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