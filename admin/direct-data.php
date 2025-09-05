<?php session_start();
if (!isset($_SESSION["admin"])) {
	header("Location: /admin/login.php");
} else {
	include("../include/admin-config.php");
	include("../include/admin-details.php");
}

if ($conn_server_db == true) {
	$direct_data_table_name = "direct_data_api";
	$direct_data_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $direct_data_table_name . "(website VARCHAR(225) NOT NULL, apikey VARCHAR(225))";
	if (mysqli_query($conn_server_db, $direct_data_apikey_db_table) == true) {
	}


	$direct_data_select_running_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $direct_data_table_name . " LIMIT 1");
	if (mysqli_num_rows($direct_data_select_running_api) > 0) {
		while ($direct_data_api_running_list = mysqli_fetch_assoc($direct_data_select_running_api)) {
			$first_direct_data_api_website_row = $direct_data_api_running_list["website"];
			$direct_data_network_running_table_name = "direct_data_network_running_api";
			$direct_data_network_running_db_table = "CREATE TABLE IF NOT EXISTS " . $direct_data_network_running_table_name . "(network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))";
			if (mysqli_query($conn_server_db, $direct_data_network_running_db_table) == true) {
				if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $direct_data_network_running_table_name)) == 0) {
					$insert_direct_data_network_running_api = "INSERT INTO " . $direct_data_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('mtn', '$first_direct_data_api_website_row','1','1','1','1');";
					$insert_direct_data_network_running_api .= "INSERT INTO " . $direct_data_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('airtel', '$first_direct_data_api_website_row','1','1','1','1');";
					$insert_direct_data_network_running_api .= "INSERT INTO " . $direct_data_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('glo', '$first_direct_data_api_website_row','1','1','1','1');";
					$insert_direct_data_network_running_api .= "INSERT INTO " . $direct_data_network_running_table_name . " (network_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('9mobile', '$first_direct_data_api_website_row','1','1','1','1')";
					if (mysqli_multi_query($conn_server_db, $insert_direct_data_network_running_api) == true) {

					}
				}
			}
		}
	}


	$direct_data_network_table_name = "direct_data_network_status";
	$direct_data_network_db_table = "CREATE TABLE IF NOT EXISTS " . $direct_data_network_table_name . "(network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)";
	if (mysqli_query($conn_server_db, $direct_data_network_db_table) == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $direct_data_network_table_name)) == 0) {
			$insert_network_status = "INSERT INTO " . $direct_data_network_table_name . " (network_name, network_status) VALUES ('mtn', 'active');";
			$insert_network_status .= "INSERT INTO " . $direct_data_network_table_name . " (network_name, network_status) VALUES ('airtel', 'active');";
			$insert_network_status .= "INSERT INTO " . $direct_data_network_table_name . " (network_name, network_status) VALUES ('glo', 'active');";
			$insert_network_status .= "INSERT INTO " . $direct_data_network_table_name . " (network_name, network_status) VALUES ('9mobile', 'active')";

			if (mysqli_multi_query($conn_server_db, $insert_network_status) == true) {

			}
		}
	}
}


if (isset($_POST["install-api"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$direct_data_api_website = "INSERT INTO " . $direct_data_table_name . " (website) VALUES ('$api_name')";

	$check_direct_data_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $direct_data_table_name . " WHERE website='$api_name'");

	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_direct_data_select_api) == 0) {
			if (mysqli_query($conn_server_db, $direct_data_api_website) == true) {
				$add_api_message = ucwords($api_name) . " direct_data API installed successfully! ";
			}
		} else {
			$add_api_message = ucwords($api_name) . " direct_data API has already been Installed! ";
		}
	}

}

if (isset($_POST["update-apikey"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$apikey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["apikey"]));

	$direct_data_api_website = "UPDATE " . $direct_data_table_name . " SET apikey='$apikey' WHERE website='$api_name'";
	$check_direct_data_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $direct_data_table_name . " WHERE website='$api_name'");
	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_direct_data_select_api) > 0) {
			if (mysqli_query($conn_server_db, $direct_data_api_website) == true) {
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



	$update_mtn_network_status = "UPDATE " . $direct_data_network_table_name . " SET network_status='$mtn_status' WHERE network_name='mtn'";
	$update_airtel_network_status = "UPDATE " . $direct_data_network_table_name . " SET network_status='$airtel_status' WHERE network_name='airtel'";
	$update_glo_network_status = "UPDATE " . $direct_data_network_table_name . " SET network_status='$glo_status' WHERE network_name='glo'";
	$update_etisalat_network_status = "UPDATE " . $direct_data_network_table_name . " SET network_status='$etisalat_status' WHERE network_name='9mobile'";

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

if (isset($_POST["run-api"])) {
	$mtn_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["mtn"]));
	$mtn_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["mtn-discount-1"])));
	$mtn_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["mtn-discount-2"])));
	$mtn_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["mtn-discount-3"])));
	$mtn_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["mtn-discount-4"])));

	$mtn_network_table_injection = "UPDATE " . $direct_data_network_running_table_name . " SET website='$mtn_website', discount_1='$mtn_discount_1', discount_2='$mtn_discount_2', discount_3='$mtn_discount_3', discount_4='$mtn_discount_4' WHERE network_name='mtn'";
	if (mysqli_query($conn_server_db, $mtn_network_table_injection) == true) {
	}

	$airtel_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["airtel"]));
	$airtel_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-1"])));
	$airtel_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-2"])));
	$airtel_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-3"])));
	$airtel_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["airtel-discount-4"])));

	$airtel_network_table_injection = "UPDATE " . $direct_data_network_running_table_name . " SET website='$airtel_website', discount_1='$airtel_discount_1', discount_2='$airtel_discount_2', discount_3='$airtel_discount_3', discount_4='$airtel_discount_4' WHERE network_name='airtel'";
	if (mysqli_query($conn_server_db, $airtel_network_table_injection) == true) {
	}

	$glo_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["glo"]));
	$glo_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-1"])));
	$glo_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-2"])));
	$glo_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-3"])));
	$glo_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["glo-discount-4"])));

	$glo_network_table_injection = "UPDATE " . $direct_data_network_running_table_name . " SET website='$glo_website', discount_1='$glo_discount_1', discount_2='$glo_discount_2', discount_3='$glo_discount_3', discount_4='$glo_discount_4' WHERE network_name='glo'";
	if (mysqli_query($conn_server_db, $glo_network_table_injection) == true) {
	}

	$etisalat_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["9mobile"]));
	$etisalat_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-1"])));
	$etisalat_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-2"])));
	$etisalat_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-3"])));
	$etisalat_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["9mobile-discount-4"])));

	$etisalat_network_table_injection = "UPDATE " . $direct_data_network_running_table_name . " SET website='$etisalat_website', discount_1='$etisalat_discount_1', discount_2='$etisalat_discount_2', discount_3='$etisalat_discount_3', discount_4='$etisalat_discount_4' WHERE network_name='9mobile'";
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
					<span class="font-size-2 font-family-1">INSTALL Direct Data API</span>
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
						<option disabled hidden selected>Install Direct Data API</option>
						<option value="datagifting.com.ng">datagifting.com.ng</option>
						<option value="smartrecharge.ng">smartrecharge.ng</option>
						<option value="benzoni.ng">benzoni.ng</option>
						<option value="grecians.ng">grecians.ng</option>
						<option value="smartrechargeapi.com">smartrechargeapi.com</option>
						<option value="mobileone.ng">mobileone.ng</option>
					</select>
					<input name="install-api" type="submit"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25"
						value="Install API" />
				</form>
			</fieldset><br>

			<?php
			$check_count_direct_data_table_name = mysqli_query($conn_server_db, "SELECT website FROM " . $direct_data_table_name);
			if ($check_count_direct_data_table_name == true) {
				if (mysqli_num_rows($check_count_direct_data_table_name) > 0) {

					?>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPDATE direct_data API KEY</span>
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
								$direct_data_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $direct_data_table_name);
								if (mysqli_num_rows($direct_data_select_api) > 0) {
									while ($direct_data_api_list = mysqli_fetch_assoc($direct_data_select_api)) {
										echo '<option data-apikey="' . $direct_data_api_list["apikey"] . '" value="' . $direct_data_api_list["website"] . '">' . $direct_data_api_list["website"] . "</option>";
									}
								} else {
									echo '<option selected hidden value="">No direct_data API was Installed!</option>';
								}

								?>
							</select>
							<input name="apikey" id="apikey" type="text"
								class="input-box mobile-width-93 system-width-60 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
								placeholder="Apikey" />
							<input name="update-apikey" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Update API key" />
						</form>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">ENABLE/DISABLE Direct Data NETWORK</span>
						</legend>
						<?php
						$direct_data_network_select_api = mysqli_query($conn_server_db, "SELECT network_name, network_status FROM " . $direct_data_network_table_name);
						if (mysqli_num_rows($direct_data_network_select_api) > 0) {
							while ($direct_data_network_status_list = mysqli_fetch_assoc($direct_data_network_select_api)) {
								;
								$all_network_status .= $direct_data_network_status_list["network_status"] . ",";
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
							<span class="font-size-2 font-family-1">CHOOSE API TO RUN</span>
						</legend>
						<form method="post">
							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">MTN direct_data ROUTE</span><br>
								</legend>
								<?php
								$direct_data_mtn_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $direct_data_table_name);
								if (mysqli_num_rows($direct_data_mtn_select_api) > 0) {
									while ($direct_data_api_list = mysqli_fetch_assoc($direct_data_mtn_select_api)) {
										$get_direct_data_mtn_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $direct_data_network_running_table_name . " WHERE network_name='mtn'"));
										if ($direct_data_api_list["website"] !== $get_direct_data_mtn_api_website["website"]) {
											echo $direct_data_api_list["website"] . ' <input value="' . $direct_data_api_list["website"] . '" name="mtn" type="radio" class="check-box"/><br>';
										} else {
											echo $direct_data_api_list["website"] . ' <input checked value="' . $direct_data_api_list["website"] . '" name="mtn" type="radio" class="check-box"/><br>';
										}
										$mtn_direct_data_api_discount .= $get_direct_data_mtn_api_website["discount_1"] . "," . $get_direct_data_mtn_api_website["discount_2"] . "," . $get_direct_data_mtn_api_website["discount_3"] . "," . $get_direct_data_mtn_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No direct_data API was Installed!';
								}

								$mtn_exp_direct_data_api_discount = array_filter(explode(",", trim($mtn_direct_data_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $mtn_exp_direct_data_api_discount[0]; ?>" name="mtn-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $mtn_exp_direct_data_api_discount[1]; ?>" name="mtn-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $mtn_exp_direct_data_api_discount[2]; ?>" name="mtn-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $mtn_exp_direct_data_api_discount[3]; ?>" name="mtn-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">AIRTEL direct_data ROUTE</span><br>
								</legend>
								<?php
								$direct_data_airtel_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $direct_data_table_name);
								if (mysqli_num_rows($direct_data_airtel_select_api) > 0) {
									while ($direct_data_api_list = mysqli_fetch_assoc($direct_data_airtel_select_api)) {
										$get_direct_data_airtel_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $direct_data_network_running_table_name . " WHERE network_name='airtel'"));
										if ($direct_data_api_list["website"] !== $get_direct_data_airtel_api_website["website"]) {
											echo $direct_data_api_list["website"] . ' <input value="' . $direct_data_api_list["website"] . '" name="airtel" type="radio" class="check-box"/><br>';
										} else {
											echo $direct_data_api_list["website"] . ' <input checked value="' . $direct_data_api_list["website"] . '" name="airtel" type="radio" class="check-box"/><br>';
										}
										$airtel_direct_data_api_discount .= $get_direct_data_airtel_api_website["discount_1"] . "," . $get_direct_data_airtel_api_website["discount_2"] . "," . $get_direct_data_airtel_api_website["discount_3"] . "," . $get_direct_data_airtel_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No direct_data API was Installed!';
								}

								$airtel_exp_direct_data_api_discount = array_filter(explode(",", trim($airtel_direct_data_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $airtel_exp_direct_data_api_discount[0]; ?>"
										name="airtel-discount-1" type="text" class="input-box half-half-length"
										placeholder="Smart Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $airtel_exp_direct_data_api_discount[1]; ?>"
										name="airtel-discount-2" type="text" class="input-box half-half-length"
										placeholder="VIP Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $airtel_exp_direct_data_api_discount[2]; ?>"
										name="airtel-discount-3" type="text" class="input-box half-half-length"
										placeholder="VIP Vendor" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $airtel_exp_direct_data_api_discount[3]; ?>"
										name="airtel-discount-4" type="text" class="input-box half-half-length"
										placeholder="API Earner" /><span class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>


							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">GLO direct_data ROUTE</span><br>
								</legend>
								<?php
								$direct_data_glo_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $direct_data_table_name);
								if (mysqli_num_rows($direct_data_glo_select_api) > 0) {
									while ($direct_data_api_list = mysqli_fetch_assoc($direct_data_glo_select_api)) {
										$get_direct_data_glo_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $direct_data_network_running_table_name . " WHERE network_name='glo'"));
										if ($direct_data_api_list["website"] !== $get_direct_data_glo_api_website["website"]) {
											echo $direct_data_api_list["website"] . ' <input value="' . $direct_data_api_list["website"] . '" name="glo" type="radio" class="check-box"/><br>';
										} else {
											echo $direct_data_api_list["website"] . ' <input checked value="' . $direct_data_api_list["website"] . '" name="glo" type="radio" class="check-box"/><br>';
										}
										$glo_direct_data_api_discount .= $get_direct_data_glo_api_website["discount_1"] . "," . $get_direct_data_glo_api_website["discount_2"] . "," . $get_direct_data_glo_api_website["discount_3"] . "," . $get_direct_data_glo_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No direct_data API was Installed!';
								}

								$glo_exp_direct_data_api_discount = array_filter(explode(",", trim($glo_direct_data_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $glo_exp_direct_data_api_discount[0]; ?>" name="glo-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $glo_exp_direct_data_api_discount[1]; ?>" name="glo-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $glo_exp_direct_data_api_discount[2]; ?>" name="glo-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $glo_exp_direct_data_api_discount[3]; ?>" name="glo-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">9MOBILE direct_data ROUTE</span><br>
								</legend>
								<?php
								$direct_data_9mobile_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $direct_data_table_name);
								if (mysqli_num_rows($direct_data_9mobile_select_api) > 0) {
									while ($direct_data_api_list = mysqli_fetch_assoc($direct_data_9mobile_select_api)) {
										$get_direct_data_9mobile_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $direct_data_network_running_table_name . " WHERE network_name='9mobile'"));
										if ($direct_data_api_list["website"] !== $get_direct_data_9mobile_api_website["website"]) {
											echo $direct_data_api_list["website"] . ' <input value="' . $direct_data_api_list["website"] . '" name="9mobile" type="radio" class="check-box"/><br>';
										} else {
											echo $direct_data_api_list["website"] . ' <input checked value="' . $direct_data_api_list["website"] . '" name="9mobile" type="radio" class="check-box"/><br>';
										}
										$etisalat_direct_data_api_discount .= $get_direct_data_9mobile_api_website["discount_1"] . "," . $get_direct_data_9mobile_api_website["discount_2"] . "," . $get_direct_data_9mobile_api_website["discount_3"] . "," . $get_direct_data_9mobile_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No direct_data API was Installed!';
								}

								$etisalat_exp_direct_data_api_discount = array_filter(explode(",", trim($etisalat_direct_data_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $etisalat_exp_direct_data_api_discount[0]; ?>"
										name="9mobile-discount-1" type="text" class="input-box half-half-length"
										placeholder="Smart Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $etisalat_exp_direct_data_api_discount[1]; ?>"
										name="9mobile-discount-2" type="text" class="input-box half-half-length"
										placeholder="VIP Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $etisalat_exp_direct_data_api_discount[2]; ?>"
										name="9mobile-discount-3" type="text" class="input-box half-half-length"
										placeholder="VIP Vendor" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $etisalat_exp_direct_data_api_discount[3]; ?>"
										name="9mobile-discount-4" type="text" class="input-box half-half-length"
										placeholder="API Earner" /><span class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>

							<input name="run-api" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-65"
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