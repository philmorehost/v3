<?php session_start();
if (!isset($_SESSION["admin"])) {
	header("Location: /admin/login.php");
} else {
	include("../include/admin-config.php");
	include("../include/admin-details.php");
}

if ($conn_server_db == true) {
	$sme_data_table_name = "sme_data_api";
	$sme_data_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $sme_data_table_name . "(website VARCHAR(225) NOT NULL, apikey VARCHAR(225))";
	if (mysqli_query($conn_server_db, $sme_data_apikey_db_table) == true) {
	}


	$sme_data_select_running_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sme_data_table_name . " LIMIT 1");
	if (mysqli_num_rows($sme_data_select_running_api) > 0) {
		while ($sme_data_api_running_list = mysqli_fetch_assoc($sme_data_select_running_api)) {
			$first_sme_data_api_website_row = $sme_data_api_running_list["website"];
			$sme_data_network_running_table_name = "sme_data_network_running_api";
			$sme_data_network_running_db_table = "CREATE TABLE IF NOT EXISTS " . $sme_data_network_running_table_name . "(network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL)";
			if (mysqli_query($conn_server_db, $sme_data_network_running_db_table) == true) {
				if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $sme_data_network_running_table_name)) == 0) {
					$insert_sme_data_network_running_api = "INSERT INTO " . $sme_data_network_running_table_name . " (network_name, website) VALUES ('mtn', '$first_sme_data_api_website_row');";
					$insert_sme_data_network_running_api .= "INSERT INTO " . $sme_data_network_running_table_name . " (network_name, website) VALUES ('airtel', '$first_sme_data_api_website_row');";
					$insert_sme_data_network_running_api .= "INSERT INTO " . $sme_data_network_running_table_name . " (network_name, website) VALUES ('9mobile', '$first_sme_data_api_website_row');";

					if (mysqli_multi_query($conn_server_db, $insert_sme_data_network_running_api) == true) {

					}
				}
			}
		}
	}


	$sme_data_network_table_name = "sme_data_network_status";
	$sme_data_network_db_table = "CREATE TABLE IF NOT EXISTS " . $sme_data_network_table_name . "(network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)";
	if (mysqli_query($conn_server_db, $sme_data_network_db_table) == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $sme_data_network_table_name)) == 0) {
			$insert_network_status = "INSERT INTO " . $sme_data_network_table_name . " (network_name, network_status) VALUES ('mtn', 'active');";
			$insert_network_status .= "INSERT INTO " . $sme_data_network_table_name . " (network_name, network_status) VALUES ('airtel', 'active');";
			$insert_network_status .= "INSERT INTO " . $sme_data_network_table_name . " (network_name, network_status) VALUES ('9mobile', 'active');";

			if (mysqli_multi_query($conn_server_db, $insert_network_status) == true) {

			}
		}
	}

	$mtn_sme_data_network_price_table_name = "mtn_sme_data_network_qty_price";
	$mtn_sme_data_network_price_db_table = "CREATE TABLE IF NOT EXISTS " . $mtn_sme_data_network_price_table_name . "(sme_data_qty VARCHAR(30) NOT NULL, sme_data_price_1 INT NOT NULL, sme_data_price_2 INT NOT NULL, sme_data_price_3 INT NOT NULL, sme_data_price_4 INT NOT NULL)";
	if (mysqli_query($conn_server_db, $mtn_sme_data_network_price_db_table) == true) {
	}

	$airtel_sme_data_network_price_table_name = "airtel_sme_data_network_qty_price";
	$airtel_sme_data_network_price_db_table = "CREATE TABLE IF NOT EXISTS " . $airtel_sme_data_network_price_table_name . "(sme_data_qty VARCHAR(30) NOT NULL, sme_data_price_1 INT NOT NULL, sme_data_price_2 INT NOT NULL, sme_data_price_3 INT NOT NULL, sme_data_price_4 INT NOT NULL)";
	if (mysqli_query($conn_server_db, $airtel_sme_data_network_price_db_table) == true) {
	}

	$etisalat_sme_data_network_price_table_name = "etisalat_sme_data_network_qty_price";
	$etisalat_sme_data_network_price_db_table = "CREATE TABLE IF NOT EXISTS " . $etisalat_sme_data_network_price_table_name . "(sme_data_qty VARCHAR(30) NOT NULL, sme_data_price_1 INT NOT NULL, sme_data_price_2 INT NOT NULL, sme_data_price_3 INT NOT NULL, sme_data_price_4 INT NOT NULL)";
	if (mysqli_query($conn_server_db, $etisalat_sme_data_network_price_db_table) == true) {
	}

}

if (isset($_POST["install-api"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$sme_data_api_website = "INSERT INTO " . $sme_data_table_name . " (website) VALUES ('$api_name')";

	$check_sme_data_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sme_data_table_name . " WHERE website='$api_name'");

	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_sme_data_select_api) == 0) {
			if (mysqli_query($conn_server_db, $sme_data_api_website) == true) {
				$add_api_message = ucwords($api_name) . " SME Data API installed successfully! ";
			}
		} else {
			$add_api_message = ucwords($api_name) . " SME Data API has already been Installed! ";
		}
	}

}

if (isset($_POST["update-apikey"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$apikey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["apikey"]));

	$sme_data_api_website = "UPDATE " . $sme_data_table_name . " SET apikey='$apikey' WHERE website='$api_name'";
	$check_sme_data_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sme_data_table_name . " WHERE website='$api_name'");
	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_sme_data_select_api) > 0) {
			if (mysqli_query($conn_server_db, $sme_data_api_website) == true) {
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

	if ($etisalat == "active") {
		$etisalat_status = "active";
	} else {
		$etisalat_status = "down";
	}

	$update_mtn_network_status = "UPDATE " . $sme_data_network_table_name . " SET network_status='$mtn_status' WHERE network_name='mtn'";
	$update_airtel_network_status = "UPDATE " . $sme_data_network_table_name . " SET network_status='$airtel_status' WHERE network_name='airtel'";
	$update_9mobile_network_status = "UPDATE " . $sme_data_network_table_name . " SET network_status='$etisalat_status' WHERE network_name='9mobile'";

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

	if (mysqli_query($conn_server_db, $update_9mobile_network_status) == true) {
		$network_update_message = "All Network Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

}

if (isset($_POST["run-api"])) {
	$mtn_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["mtn"]));
	$airtel_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["airtel"]));
	$etisalat_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["9mobile"]));

	$mtn_network_table_injection = "UPDATE " . $sme_data_network_running_table_name . " SET website='$mtn_website' WHERE network_name='mtn'";
	if (mysqli_query($conn_server_db, $mtn_network_table_injection) == true) {
	}

	$airtel_network_table_injection = "UPDATE " . $sme_data_network_running_table_name . " SET website='$airtel_website' WHERE network_name='airtel'";
	if (mysqli_query($conn_server_db, $airtel_network_table_injection) == true) {
	}

	$etisalat_network_table_injection = "UPDATE " . $sme_data_network_running_table_name . " SET website='$etisalat_website' WHERE network_name='9mobile'";
	if (mysqli_query($conn_server_db, $etisalat_network_table_injection) == true) {
	}
}

if (isset($_POST["update-sme_data-price"])) {
	$mtn_sme_data_qty_array = $_POST["mtn-qty"];
	$mtn_sme_data_price_1_array = $_POST["mtn-price-1"];
	$mtn_sme_data_price_2_array = $_POST["mtn-price-2"];
	$mtn_sme_data_price_3_array = $_POST["mtn-price-3"];
	$mtn_sme_data_price_4_array = $_POST["mtn-price-4"];

	if (isset($mtn_sme_data_qty_array)) {
		foreach ($mtn_sme_data_qty_array as $key => $mtn_sme_data_qty) {
			$sme_data_price_1 = mysqli_real_escape_string($conn_server_db, strip_tags($mtn_sme_data_price_1_array[$key]));
			$sme_data_price_2 = mysqli_real_escape_string($conn_server_db, strip_tags($mtn_sme_data_price_2_array[$key]));
			$sme_data_price_3 = mysqli_real_escape_string($conn_server_db, strip_tags($mtn_sme_data_price_3_array[$key]));
			$sme_data_price_4 = mysqli_real_escape_string($conn_server_db, strip_tags($mtn_sme_data_price_4_array[$key]));

			$mtn_price_update = "UPDATE " . $mtn_sme_data_network_price_table_name . " SET sme_data_price_1='$sme_data_price_1', sme_data_price_2='$sme_data_price_2', sme_data_price_3='$sme_data_price_3', sme_data_price_4='$sme_data_price_4' WHERE sme_data_qty='$mtn_sme_data_qty'";
			if (mysqli_query($conn_server_db, $mtn_price_update) == true) {
			}
		}
	}

	$airtel_sme_data_qty_array = $_POST["airtel-qty"];
	$airtel_sme_data_price_1_array = $_POST["airtel-price-1"];
	$airtel_sme_data_price_2_array = $_POST["airtel-price-2"];
	$airtel_sme_data_price_3_array = $_POST["airtel-price-3"];
	$airtel_sme_data_price_4_array = $_POST["airtel-price-4"];

	if (isset($airtel_sme_data_qty_array)) {
		foreach ($airtel_sme_data_qty_array as $key => $airtel_sme_data_qty) {
			$sme_data_price_1 = mysqli_real_escape_string($conn_server_db, strip_tags($airtel_sme_data_price_1_array[$key]));
			$sme_data_price_2 = mysqli_real_escape_string($conn_server_db, strip_tags($airtel_sme_data_price_2_array[$key]));
			$sme_data_price_3 = mysqli_real_escape_string($conn_server_db, strip_tags($airtel_sme_data_price_3_array[$key]));
			$sme_data_price_4 = mysqli_real_escape_string($conn_server_db, strip_tags($airtel_sme_data_price_4_array[$key]));

			$airtel_price_update = "UPDATE " . $airtel_sme_data_network_price_table_name . " SET sme_data_price_1='$sme_data_price_1', sme_data_price_2='$sme_data_price_2', sme_data_price_3='$sme_data_price_3', sme_data_price_4='$sme_data_price_4' WHERE sme_data_qty='$airtel_sme_data_qty'";
			if (mysqli_query($conn_server_db, $airtel_price_update) == true) {
			}
		}
	}

	$etisalat_sme_data_qty_array = $_POST["9mobile-qty"];
	$etisalat_sme_data_price_1_array = $_POST["9mobile-price-1"];
	$etisalat_sme_data_price_2_array = $_POST["9mobile-price-2"];
	$etisalat_sme_data_price_3_array = $_POST["9mobile-price-3"];
	$etisalat_sme_data_price_4_array = $_POST["9mobile-price-4"];

	if (isset($etisalat_sme_data_qty_array)) {
		foreach ($etisalat_sme_data_qty_array as $key => $etisalat_sme_data_qty) {
			$sme_data_price_1 = mysqli_real_escape_string($conn_server_db, strip_tags($etisalat_sme_data_price_1_array[$key]));
			$sme_data_price_2 = mysqli_real_escape_string($conn_server_db, strip_tags($etisalat_sme_data_price_2_array[$key]));
			$sme_data_price_3 = mysqli_real_escape_string($conn_server_db, strip_tags($etisalat_sme_data_price_3_array[$key]));
			$sme_data_price_4 = mysqli_real_escape_string($conn_server_db, strip_tags($etisalat_sme_data_price_4_array[$key]));

			$etisalat_price_update = "UPDATE " . $etisalat_sme_data_network_price_table_name . " SET sme_data_price_1='$sme_data_price_1', sme_data_price_2='$sme_data_price_2', sme_data_price_3='$sme_data_price_3', sme_data_price_4='$sme_data_price_4' WHERE sme_data_qty='$etisalat_sme_data_qty'";
			if (mysqli_query($conn_server_db, $etisalat_price_update) == true) {
			}
		}
	}
}

if (isset($_POST["add-sme_data-qty-price"])) {
	$network_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["network-name"]));
	$price_1 = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["price-1"]));
	$price_2 = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["price-2"]));
	$price_3 = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["price-3"]));
	$price_4 = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["price-4"]));

	if ($network_name == "mtn") {
		$sme_data_qty_price_table_name = $mtn_sme_data_network_price_table_name;
		$qty = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["mtn-qty"]));
	}

	if ($network_name == "airtel") {
		$sme_data_qty_price_table_name = $airtel_sme_data_network_price_table_name;
		$qty = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["airtel-qty"]));
	}

	if ($network_name == "9mobile") {
		$sme_data_qty_price_table_name = $etisalat_sme_data_network_price_table_name;
		$qty = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["9mobile-qty"]));
	}

	$check_if_sme_data_qty_exists = mysqli_query($conn_server_db, "SELECT sme_data_qty FROM " . $sme_data_qty_price_table_name . " WHERE sme_data_qty='$qty'");
	if (mysqli_num_rows($check_if_sme_data_qty_exists) == 0) {
		$insert_sme_data_qty_price = "INSERT INTO " . $sme_data_qty_price_table_name . " (sme_data_qty, sme_data_price_1, sme_data_price_2, sme_data_price_3, sme_data_price_4) VALUES ('$qty', '$price_1', '$price_2', '$price_3', '$price_4')";
		if (mysqli_query($conn_server_db, $insert_sme_data_qty_price) == true) {
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
</head>

<body>
	<?php include("../include/admin-header-html.php"); ?>

	<center>
		<div class="container-box bg-6 mobile-width-95 system-width-95 mobile-margin-top-2 system-margin-top-2">
			<fieldset>
				<legend>
					<span class="font-size-2 font-family-1">INSTALL SME DATA API</span>
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
						<option disabled hidden selected>Install SME Data API</option>
						<option value="datagifting.com.ng">datagifting.com.ng</option>
						<option value="smartrecharge.ng">smartrecharge.ng</option>
						<option value="benzoni.ng">benzoni.ng</option>
						<option value="grecians.ng">grecians.ng</option>
						<option value="smartrechargeapi.com">smartrechargeapi.com</option>
						<option value="rpidatang.com">rpidatang.com</option>
						<option value="subvtu.com">subvtu.com</option>
						<option value="legitdataway.com">legitdataway.com</option>
						<option value="mobileone.ng">mobileone.ng</option>
						<option value="alrahuzdata.com.ng">alrahuzdata.com.ng</option>
					</select>
					<input name="install-api" type="submit"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25"
						value="Install API" />
				</form>
			</fieldset><br>

			<?php
			$check_count_sme_data_table_name = mysqli_query($conn_server_db, "SELECT website FROM " . $sme_data_table_name);
			if ($check_count_sme_data_table_name == true) {
				if (mysqli_num_rows($check_count_sme_data_table_name) > 0) {

					?>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPDATE SME DATA API KEY</span>
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
								$sme_data_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sme_data_table_name);
								if (mysqli_num_rows($sme_data_select_api) > 0) {
									while ($sme_data_api_list = mysqli_fetch_assoc($sme_data_select_api)) {
										echo '<option sme_data-apikey="' . $sme_data_api_list["apikey"] . '" value="' . $sme_data_api_list["website"] . '">' . $sme_data_api_list["website"] . "</option>";
									}
								} else {
									echo '<option selected hidden value="">No SME Data API was Installed!</option>';
								}

								?>
							</select>
							<input name="apikey" id="apikey" type="text"
								class="input-box mobile-width-93 system-width-60 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
								placeholder="Apikey" />
							<input name="update-apikey" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Update API key" />
						</form>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">ENABLE/DISABLE SME DATA NETWORK</span>
						</legend>
						<?php
						$sme_data_network_select_api = mysqli_query($conn_server_db, "SELECT network_name, network_status FROM " . $sme_data_network_table_name);
						if (mysqli_num_rows($sme_data_network_select_api) > 0) {
							while ($sme_data_network_status_list = mysqli_fetch_assoc($sme_data_network_select_api)) {
								;
								$all_network_status .= $sme_data_network_status_list["network_status"] . ",";
							}
						}

						$exp_all_network_status = array_filter(explode(",", trim($all_network_status)));
						class allNetwork
						{
						}
						$mtn = "mtn";
						$airtel = "airtel";
						$etisalat = "9mobile";

						$allNetworkStatus = new allNetwork;
						$allNetworkStatus->$mtn = $exp_all_network_status[0];
						$allNetworkStatus->$airtel = $exp_all_network_status[1];
						$allNetworkStatus->$etisalat = $exp_all_network_status[2];

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
								value="active" name="mtn" type="checkbox" class="check-box" />

							<span class="font-size-2 font-family-1"><b>Airtel</b></span>
							<input <?php if ($decode_all_network_status_json['airtel'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="airtel" type="checkbox" class="check-box" />

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
							<span class="font-size-2 font-family-1">CHOOSE API TO RUN</span>
						</legend>
						<form method="post">
							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">MTN SME DATA ROUTE</span><br>
								</legend>
								<?php
								$sme_data_mtn_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sme_data_table_name);
								if (mysqli_num_rows($sme_data_mtn_select_api) > 0) {
									while ($sme_data_api_list = mysqli_fetch_assoc($sme_data_mtn_select_api)) {
										$get_sme_data_mtn_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM " . $sme_data_network_running_table_name . " WHERE network_name='mtn'"));
										if ($sme_data_api_list["website"] !== $get_sme_data_mtn_api_website["website"]) {
											echo $sme_data_api_list["website"] . ' <input value="' . $sme_data_api_list["website"] . '" name="mtn" type="radio" class="check-box"/><br>';
										} else {
											echo $sme_data_api_list["website"] . ' <input checked value="' . $sme_data_api_list["website"] . '" name="mtn" type="radio" class="check-box"/><br>';
										}
									}
								} else {
									echo 'No SME Data API was Installed!';
								}

								?>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">Airtel SME DATA ROUTE</span><br>
								</legend>
								<?php
								$sme_data_airtel_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sme_data_table_name);
								if (mysqli_num_rows($sme_data_airtel_select_api) > 0) {
									while ($sme_data_api_list = mysqli_fetch_assoc($sme_data_airtel_select_api)) {
										$get_sme_data_airtel_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM " . $sme_data_network_running_table_name . " WHERE network_name='airtel'"));
										if ($sme_data_api_list["website"] !== $get_sme_data_airtel_api_website["website"]) {
											echo $sme_data_api_list["website"] . ' <input value="' . $sme_data_api_list["website"] . '" name="airtel" type="radio" class="check-box"/><br>';
										} else {
											echo $sme_data_api_list["website"] . ' <input checked value="' . $sme_data_api_list["website"] . '" name="airtel" type="radio" class="check-box"/><br>';
										}
									}
								} else {
									echo 'No SME Data API was Installed!';
								}

								?>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">9mobile SME DATA ROUTE</span><br>
								</legend>
								<?php
								$sme_data_9mobile_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sme_data_table_name);
								if (mysqli_num_rows($sme_data_9mobile_select_api) > 0) {
									while ($sme_data_api_list = mysqli_fetch_assoc($sme_data_9mobile_select_api)) {
										$get_sme_data_9mobile_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM " . $sme_data_network_running_table_name . " WHERE network_name='9mobile'"));
										if ($sme_data_api_list["website"] !== $get_sme_data_9mobile_api_website["website"]) {
											echo $sme_data_api_list["website"] . ' <input value="' . $sme_data_api_list["website"] . '" name="9mobile" type="radio" class="check-box"/><br>';
										} else {
											echo $sme_data_api_list["website"] . ' <input checked value="' . $sme_data_api_list["website"] . '" name="9mobile" type="radio" class="check-box"/><br>';
										}
									}
								} else {
									echo 'No SME Data API was Installed!';
								}

								?>
							</fieldset>

							<input name="run-api" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-85 system-width-65"
								value="Run API" />
						</form>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">SME DATA QUALITY & PRICE</span>
						</legend>
						<form method="post">
							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1"><b>MTN PRICE</b></span><br>
								</legend>
								<table class="table-style-2 table-font-size-1">
									<thead>
										<tr>
											<th>Qty</th>
											<th>Smart Earner</th>
											<th>VIP Earner</th>
											<th>VIP Vendor</th>
											<th>API Earner</th>
										</tr>
									</thead>
									<tbody>

										<?php
										$mtn_sme_data_qty_price_select = mysqli_query($conn_server_db, "SELECT sme_data_qty, sme_data_price_1, sme_data_price_2, sme_data_price_3, sme_data_price_4 FROM " . $mtn_sme_data_network_price_table_name);
										if (mysqli_num_rows($mtn_sme_data_qty_price_select) > 0) {
											while ($sme_data_qty_price_list = mysqli_fetch_assoc($mtn_sme_data_qty_price_select)) {

												echo '<tr>
					<td><b>' . $sme_data_qty_price_list["sme_data_qty"] . '<input hidden value="' . $sme_data_qty_price_list["sme_data_qty"] . '" name="mtn-qty[]" type="text" placeholder="Qty"/></b></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_1"] . '" name="mtn-price-1[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_2"] . '" name="mtn-price-2[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_3"] . '" name="mtn-price-3[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_4"] . '" name="mtn-price-4[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				</tr>';

											}
										} else {
											echo 'No SME Data Quantity was Created!';
										}

										?>
									</tbody>
								</table>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1"><b>Airtel PRICE</b></span><br>
								</legend>
								<table class="table-style-2 table-font-size-1">
									<thead>
										<tr>
											<th>Qty</th>
											<th>Smart Earner</th>
											<th>VIP Earner</th>
											<th>VIP Vendor</th>
											<th>API Earner</th>
										</tr>
									</thead>
									<tbody>

										<?php
										$airtel_sme_data_qty_price_select = mysqli_query($conn_server_db, "SELECT sme_data_qty, sme_data_price_1, sme_data_price_2, sme_data_price_3, sme_data_price_4 FROM " . $airtel_sme_data_network_price_table_name);
										if (mysqli_num_rows($airtel_sme_data_qty_price_select) > 0) {
											while ($sme_data_qty_price_list = mysqli_fetch_assoc($airtel_sme_data_qty_price_select)) {

												echo '<tr>
					<td><b>' . $sme_data_qty_price_list["sme_data_qty"] . '<input hidden value="' . $sme_data_qty_price_list["sme_data_qty"] . '" name="airtel-qty[]" type="text" placeholder="Qty"/></b></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_1"] . '" name="airtel-price-1[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_2"] . '" name="airtel-price-2[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_3"] . '" name="airtel-price-3[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_4"] . '" name="airtel-price-4[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				</tr>';

											}
										} else {
											echo 'No SME Data Quantity was Created!';
										}

										?>
									</tbody>
								</table>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1"><b>9mobile PRICE</b></span><br>
								</legend>
								<table class="table-style-2 table-font-size-1">
									<thead>
										<tr>
											<th>Qty</th>
											<th>Smart Earner</th>
											<th>VIP Earner</th>
											<th>VIP Vendor</th>
											<th>API Earner</th>
										</tr>
									</thead>
									<tbody>

										<?php
										$etisalat_sme_data_qty_price_select = mysqli_query($conn_server_db, "SELECT sme_data_qty, sme_data_price_1, sme_data_price_2, sme_data_price_3, sme_data_price_4 FROM " . $etisalat_sme_data_network_price_table_name);
										if (mysqli_num_rows($etisalat_sme_data_qty_price_select) > 0) {
											while ($sme_data_qty_price_list = mysqli_fetch_assoc($etisalat_sme_data_qty_price_select)) {

												echo '<tr>
					<td><b>' . $sme_data_qty_price_list["sme_data_qty"] . '<input hidden value="' . $sme_data_qty_price_list["sme_data_qty"] . '" name="9mobile-qty[]" type="text" placeholder="Qty"/></b></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_1"] . '" name="9mobile-price-1[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_2"] . '" name="9mobile-price-2[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_3"] . '" name="9mobile-price-3[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $sme_data_qty_price_list["sme_data_price_4"] . '" name="9mobile-price-4[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				</tr>';

											}
										} else {
											echo 'No SME Data Quantity was Created!';
										}

										?>
									</tbody>
								</table>
							</fieldset>


							<input name="update-sme_data-price" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-65"
								value="Update sme Data Settings" />
						</form>
						<fieldset>
							<legend>
								<span class="font-size-2 font-family-1">ADD OR UPDATE SME DATA QUALITY & PRICE [<b>For Network
										Checked Below</b>]</span>
							</legend>
							<form method="post">
								<select name="network-name" id="add-data-select"
									class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-50">
									<option value="mtn">MTN</option>
									<option value="airtel">Airtel</option>
									<option value="9mobile">9mobile</option>
								</select>
								<select style="display:none;" name="mtn-qty" id="mtn-data-select"
									class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-35">
									<option value="500mb">500mb</option>
									<option value="1gb">1gb</option>
									<option value="2gb">2gb</option>
									<option value="3gb">3gb</option>
									<option value="5gb">5gb</option>
									<option value="10gb">10gb</option>
								</select>

								<select style="display:none;" name="airtel-qty" id="airtel-data-select"
									class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-35">
									<option value="100mb">100mb</option>
									<option value="500mb">500mb</option>
									<option value="1gb">1gb</option>
									<option value="2gb">2gb</option>
									<option value="5gb">5gb</option>
									<option value="10gb">10gb</option>
									<option value="15gb">15gb</option>
									<option value="20gb">20gb</option>
								</select>

								<select style="display:none;" name="9mobile-qty" id="etisalat-data-select"
									class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-35">
									<option value="1gb">1gb</option>
									<option value="1.5gb">1.5gb</option>
									<option value="2gb">2gb</option>
									<option value="3gb">3gb</option>
									<option value="4gb">4gb</option>
									<option value="4.5gb">4.5gb</option>
									<option value="5gb">5gb</option>
									<option value="10gb">10gb</option>
									<option value="40gb">40gb</option>
								</select><br>

								<script>

									setInterval(function () {
										const add_data_select = document.getElementById("add-data-select").value;

										if (add_data_select == "mtn") {
											document.getElementById("mtn-data-select").style.display = "inline-block";
										} else {
											document.getElementById("mtn-data-select").style.display = "none";
										}

										if (add_data_select == "airtel") {
											document.getElementById("airtel-data-select").style.display = "inline-block";
										} else {
											document.getElementById("airtel-data-select").style.display = "none";
										}

										if (add_data_select == "9mobile") {
											document.getElementById("etisalat-data-select").style.display = "inline-block";
										} else {
											document.getElementById("etisalat-data-select").style.display = "none";
										}
									});

								</script>


								<span class="font-size-1 font-family-1">Smart Earner</span> -
								<input name="price-1" type="number" class="input-box half-length" placeholder="Price" /><br>
								<span class="font-size-1 font-family-1">VIP Earner</span> -
								<input name="price-2" type="number" class="input-box half-length" placeholder="Price" /><br>
								<span class="font-size-1 font-family-1">VIP Vendor</span> -
								<input name="price-3" type="number" class="input-box half-length" placeholder="Price" /><br>
								<span class="font-size-1 font-family-1">API Earner</span> -
								<input name="price-4" type="number" class="input-box half-length" placeholder="Price" /><br>

								<input name="add-sme_data-qty-price" type="submit"
									class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-85"
									value="Add sme Data Qty, Price" />

						</fieldset>
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
			document.getElementById("apikey").value = apikey.options[apikey.selectedIndex].getAttribute("sme_data-apikey");
		}
	</script>

	<?php include("../include/admin-footer-html.php"); ?>
</body>

</html>