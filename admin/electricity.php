<?php session_start();
if (!isset($_SESSION["admin"])) {
	header("Location: /admin/login.php");
} else {
	include("../include/admin-config.php");
	include("../include/admin-details.php");
}

if ($conn_server_db == true) {
	$electricity_table_name = "electricity_api";
	$electricity_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $electricity_table_name . "(website VARCHAR(225) NOT NULL, apikey VARCHAR(225))";
	if (mysqli_query($conn_server_db, $electricity_apikey_db_table) == true) {
	}


	$electricity_select_running_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name . " LIMIT 1");
	if (mysqli_num_rows($electricity_select_running_api) > 0) {
		while ($electricity_api_running_list = mysqli_fetch_assoc($electricity_select_running_api)) {
			$first_electricity_api_website_row = $electricity_api_running_list["website"];
			$electricity_subscription_running_table_name = "electricity_subscription_running_api";
			$electricity_subscription_running_db_table = "CREATE TABLE IF NOT EXISTS " . $electricity_subscription_running_table_name . "(subscription_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))";
			if (mysqli_query($conn_server_db, $electricity_subscription_running_db_table) == true) {
				if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $electricity_subscription_running_table_name)) == 0) {
					$insert_electricity_subscription_running_api = "INSERT INTO " . $electricity_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('ekedc', '$first_electricity_api_website_row','1','1','1','1');";
					$insert_electricity_subscription_running_api .= "INSERT INTO " . $electricity_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('eedc', '$first_electricity_api_website_row','1','1','1','1');";
					$insert_electricity_subscription_running_api .= "INSERT INTO " . $electricity_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('ikedc', '$first_electricity_api_website_row','1','1','1','1');";
					$insert_electricity_subscription_running_api .= "INSERT INTO " . $electricity_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('jedc', '$first_electricity_api_website_row','1','1','1','1');";
					$insert_electricity_subscription_running_api .= "INSERT INTO " . $electricity_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('kano', '$first_electricity_api_website_row','1','1','1','1');";
					$insert_electricity_subscription_running_api .= "INSERT INTO " . $electricity_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('ibedc', '$first_electricity_api_website_row','1','1','1','1');";
					$insert_electricity_subscription_running_api .= "INSERT INTO " . $electricity_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('phed', '$first_electricity_api_website_row','1','1','1','1');";
					$insert_electricity_subscription_running_api .= "INSERT INTO " . $electricity_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('aedc', '$first_electricity_api_website_row','1','1','1','1');";
					if (mysqli_multi_query($conn_server_db, $insert_electricity_subscription_running_api) == true) {

					}
				}
			}
		}
	}


	$electricity_subscription_table_name = "electricity_subscription_status";
	$electricity_subscription_db_table = "CREATE TABLE IF NOT EXISTS " . $electricity_subscription_table_name . "(subscription_name VARCHAR(30) NOT NULL, subscription_status VARCHAR(30) NOT NULL)";
	if (mysqli_query($conn_server_db, $electricity_subscription_db_table) == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $electricity_subscription_table_name)) == 0) {
			$insert_subscription_status = "INSERT INTO " . $electricity_subscription_table_name . " (subscription_name, subscription_status) VALUES ('ekedc', 'active');";
			$insert_subscription_status .= "INSERT INTO " . $electricity_subscription_table_name . " (subscription_name, subscription_status) VALUES ('eedc', 'active');";
			$insert_subscription_status .= "INSERT INTO " . $electricity_subscription_table_name . " (subscription_name, subscription_status) VALUES ('ikedc', 'active');";
			$insert_subscription_status .= "INSERT INTO " . $electricity_subscription_table_name . " (subscription_name, subscription_status) VALUES ('jedc', 'active');";
			$insert_subscription_status .= "INSERT INTO " . $electricity_subscription_table_name . " (subscription_name, subscription_status) VALUES ('kano', 'active');";
			$insert_subscription_status .= "INSERT INTO " . $electricity_subscription_table_name . " (subscription_name, subscription_status) VALUES ('ibedc', 'active');";
			$insert_subscription_status .= "INSERT INTO " . $electricity_subscription_table_name . " (subscription_name, subscription_status) VALUES ('phed', 'active');";
			$insert_subscription_status .= "INSERT INTO " . $electricity_subscription_table_name . " (subscription_name, subscription_status) VALUES ('aedc', 'active');";

			if (mysqli_multi_query($conn_server_db, $insert_subscription_status) == true) {

			}
		}
	}
}


if (isset($_POST["install-api"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$electricity_api_website = "INSERT INTO " . $electricity_table_name . " (website) VALUES ('$api_name')";

	$check_electricity_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name . " WHERE website='$api_name'");

	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_electricity_select_api) == 0) {
			if (mysqli_query($conn_server_db, $electricity_api_website) == true) {
				$add_api_message = ucwords($api_name) . " electricity API installed successfully! ";
			}
		} else {
			$add_api_message = ucwords($api_name) . " electricity API has already been Installed! ";
		}
	}

}

if (isset($_POST["update-apikey"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$apikey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["apikey"]));

	$electricity_api_website = "UPDATE " . $electricity_table_name . " SET apikey='$apikey' WHERE website='$api_name'";
	$check_electricity_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name . " WHERE website='$api_name'");
	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_electricity_select_api) > 0) {
			if (mysqli_query($conn_server_db, $electricity_api_website) == true) {
				$update_api_message = ucwords($api_name) . " API key Updated successfully! ";
			}
		}
	} else {
		$update_api_message = "Error: Cannot Update Empty API Website";
	}

}

if (isset($_POST["update-subscription"])) {
	$ekedc = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["ekedc"]));
	$eedc = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["eedc"]));
	$ikedc = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["ikedc"]));
	$jedc = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["jedc"]));
	$kano = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["kano"]));
	$ibedc = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["ibedc"]));
	$phed = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["phed"]));
	$aedc = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["aedc"]));

	if ($ekedc == "active") {
		$ekedc_status = "active";
	} else {
		$ekedc_status = "down";
	}

	if ($eedc == "active") {
		$eedc_status = "active";
	} else {
		$eedc_status = "down";
	}

	if ($ikedc == "active") {
		$ikedc_status = "active";
	} else {
		$ikedc_status = "down";
	}

	if ($jedc == "active") {
		$jedc_status = "active";
	} else {
		$jedc_status = "down";
	}

	if ($kano == "active") {
		$kano_status = "active";
	} else {
		$kano_status = "down";
	}

	if ($ibedc == "active") {
		$ibedc_status = "active";
	} else {
		$ibedc_status = "down";
	}

	if ($phed == "active") {
		$phed_status = "active";
	} else {
		$phed_status = "down";
	}

	if ($aedc == "active") {
		$aedc_status = "active";
	} else {
		$aedc_status = "down";
	}


	$update_ekedc_subscription_status = "UPDATE " . $electricity_subscription_table_name . " SET subscription_status='$ekedc_status' WHERE subscription_name='ekedc'";
	$update_eedc_subscription_status = "UPDATE " . $electricity_subscription_table_name . " SET subscription_status='$eedc_status' WHERE subscription_name='eedc'";
	$update_ikedc_subscription_status = "UPDATE " . $electricity_subscription_table_name . " SET subscription_status='$ikedc_status' WHERE subscription_name='ikedc'";
	$update_jedc_subscription_status = "UPDATE " . $electricity_subscription_table_name . " SET subscription_status='$jedc_status' WHERE subscription_name='jedc'";
	$update_kano_subscription_status = "UPDATE " . $electricity_subscription_table_name . " SET subscription_status='$kano_status' WHERE subscription_name='kano'";
	$update_ibedc_subscription_status = "UPDATE " . $electricity_subscription_table_name . " SET subscription_status='$ibedc_status' WHERE subscription_name='ibedc'";
	$update_phed_subscription_status = "UPDATE " . $electricity_subscription_table_name . " SET subscription_status='$phed_status' WHERE subscription_name='phed'";
	$update_aedc_subscription_status = "UPDATE " . $electricity_subscription_table_name . " SET subscription_status='$aedc_status' WHERE subscription_name='aedc'";

	if (mysqli_query($conn_server_db, $update_ekedc_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_eedc_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_ikedc_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_jedc_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_kano_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_ibedc_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_phed_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_aedc_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

}

if (isset($_POST["run-api"])) {
	$ekedc_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["ekedc"]));
	$ekedc_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ekedc-discount-1"])));
	$ekedc_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ekedc-discount-2"])));
	$ekedc_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ekedc-discount-3"])));
	$ekedc_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ekedc-discount-4"])));

	$ekedc_subscription_table_injection = "UPDATE " . $electricity_subscription_running_table_name . " SET website='$ekedc_website', discount_1='$ekedc_discount_1', discount_2='$ekedc_discount_2', discount_3='$ekedc_discount_3', discount_4='$ekedc_discount_4' WHERE subscription_name='ekedc'";
	if (mysqli_query($conn_server_db, $ekedc_subscription_table_injection) == true) {
	}

	$eedc_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["eedc"]));
	$eedc_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["eedc-discount-1"])));
	$eedc_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["eedc-discount-2"])));
	$eedc_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["eedc-discount-3"])));
	$eedc_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["eedc-discount-4"])));

	$eedc_subscription_table_injection = "UPDATE " . $electricity_subscription_running_table_name . " SET website='$eedc_website', discount_1='$eedc_discount_1', discount_2='$eedc_discount_2', discount_3='$eedc_discount_3', discount_4='$eedc_discount_4' WHERE subscription_name='eedc'";
	if (mysqli_query($conn_server_db, $eedc_subscription_table_injection) == true) {
	}

	$ikedc_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["ikedc"]));
	$ikedc_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ikedc-discount-1"])));
	$ikedc_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ikedc-discount-2"])));
	$ikedc_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ikedc-discount-3"])));
	$ikedc_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ikedc-discount-4"])));

	$ikedc_subscription_table_injection = "UPDATE " . $electricity_subscription_running_table_name . " SET website='$ikedc_website', discount_1='$ikedc_discount_1', discount_2='$ikedc_discount_2', discount_3='$ikedc_discount_3', discount_4='$ikedc_discount_4' WHERE subscription_name='ikedc'";
	if (mysqli_query($conn_server_db, $ikedc_subscription_table_injection) == true) {
	}

	$jedc_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["jedc"]));
	$jedc_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["jedc-discount-1"])));
	$jedc_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["jedc-discount-2"])));
	$jedc_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["jedc-discount-3"])));
	$jedc_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["jedc-discount-4"])));

	$jedc_subscription_table_injection = "UPDATE " . $electricity_subscription_running_table_name . " SET website='$jedc_website', discount_1='$jedc_discount_1', discount_2='$jedc_discount_2', discount_3='$jedc_discount_3', discount_4='$jedc_discount_4' WHERE subscription_name='jedc'";
	if (mysqli_query($conn_server_db, $jedc_subscription_table_injection) == true) {
	}

	$kano_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["kano"]));
	$kano_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["kano-discount-1"])));
	$kano_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["kano-discount-2"])));
	$kano_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["kano-discount-3"])));
	$kano_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["kano-discount-4"])));

	$kano_subscription_table_injection = "UPDATE " . $electricity_subscription_running_table_name . " SET website='$kano_website', discount_1='$kano_discount_1', discount_2='$kano_discount_2', discount_3='$kano_discount_3', discount_4='$kano_discount_4' WHERE subscription_name='kano'";
	if (mysqli_query($conn_server_db, $kano_subscription_table_injection) == true) {
	}

	$ibedc_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["ibedc"]));
	$ibedc_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ibedc-discount-1"])));
	$ibedc_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ibedc-discount-2"])));
	$ibedc_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ibedc-discount-3"])));
	$ibedc_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["ibedc-discount-4"])));

	$ibedc_subscription_table_injection = "UPDATE " . $electricity_subscription_running_table_name . " SET website='$ibedc_website', discount_1='$ibedc_discount_1', discount_2='$ibedc_discount_2', discount_3='$ibedc_discount_3', discount_4='$ibedc_discount_4' WHERE subscription_name='ibedc'";
	if (mysqli_query($conn_server_db, $ibedc_subscription_table_injection) == true) {
	}

	$phed_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["phed"]));
	$phed_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["phed-discount-1"])));
	$phed_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["phed-discount-2"])));
	$phed_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["phed-discount-3"])));
	$phed_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["phed-discount-4"])));

	$phed_subscription_table_injection = "UPDATE " . $electricity_subscription_running_table_name . " SET website='$phed_website', discount_1='$phed_discount_1', discount_2='$phed_discount_2', discount_3='$phed_discount_3', discount_4='$phed_discount_4' WHERE subscription_name='phed'";
	if (mysqli_query($conn_server_db, $phed_subscription_table_injection) == true) {
	}

	$aedc_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["aedc"]));
	$aedc_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["aedc-discount-1"])));
	$aedc_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["aedc-discount-2"])));
	$aedc_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["aedc-discount-3"])));
	$aedc_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["aedc-discount-4"])));

	$aedc_subscription_table_injection = "UPDATE " . $electricity_subscription_running_table_name . " SET website='$aedc_website', discount_1='$aedc_discount_1', discount_2='$aedc_discount_2', discount_3='$aedc_discount_3', discount_4='$aedc_discount_4' WHERE subscription_name='aedc'";
	if (mysqli_query($conn_server_db, $aedc_subscription_table_injection) == true) {
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
					<span class="font-size-2 font-family-1">INSTALL Electricity API</span>
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
						<option disabled hidden selected>Install Electricity API</option>
						<option value="datagifting.com.ng">datagifting.com.ng</option>
						<option value="smartrecharge.ng">smartrecharge.ng</option>
						<option value="smartrechargeapi.com">smartrechargeapi.com</option>
						<option value="mobileone.ng">mobileone.ng</option>
						<!-- <option value="alrahuzdata.com.ng">alrahuzdata.com.ng</option> -->
					</select>
					<input name="install-api" type="submit"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25"
						value="Install API" />
				</form>
			</fieldset><br>

			<?php
			$check_count_electricity_table_name = mysqli_query($conn_server_db, "SELECT website FROM " . $electricity_table_name);
			if ($check_count_electricity_table_name == true) {
				if (mysqli_num_rows($check_count_electricity_table_name) > 0) {

					?>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPDATE ELECTRICITY API KEY</span>
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
								$electricity_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name);
								if (mysqli_num_rows($electricity_select_api) > 0) {
									while ($electricity_api_list = mysqli_fetch_assoc($electricity_select_api)) {
										echo '<option data-apikey="' . $electricity_api_list["apikey"] . '" value="' . $electricity_api_list["website"] . '">' . $electricity_api_list["website"] . "</option>";
									}
								} else {
									echo '<option selected hidden value="">No Electricity API was Installed!</option>';
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
							<span class="font-size-2 font-family-1">ENABLE/DISABLE ELECTRICITY SUBSCRIPTION</span>
						</legend>
						<?php
						$electricity_subscription_select_api = mysqli_query($conn_server_db, "SELECT subscription_name, subscription_status FROM " . $electricity_subscription_table_name);
						if (mysqli_num_rows($electricity_subscription_select_api) > 0) {
							while ($electricity_subscription_status_list = mysqli_fetch_assoc($electricity_subscription_select_api)) {
								;
								$all_subscription_status .= $electricity_subscription_status_list["subscription_status"] . ",";
							}
						}

						$exp_all_subscription_status = array_filter(explode(",", trim($all_subscription_status)));
						class allSubscription
						{
						}
						$ekedc = "ekedc";
						$eedc = "eedc";
						$ikedc = "ikedc";
						$jedc = "jedc";
						$kano = "kano";
						$ibedc = "ibedc";
						$phed = "phed";
						$aedc = "aedc";

						$allSubscriptionStatus = new allSubscription;
						$allSubscriptionStatus->$ekedc = $exp_all_subscription_status[0];
						$allSubscriptionStatus->$eedc = $exp_all_subscription_status[1];
						$allSubscriptionStatus->$ikedc = $exp_all_subscription_status[2];
						$allSubscriptionStatus->$jedc = $exp_all_subscription_status[3];
						$allSubscriptionStatus->$kano = $exp_all_subscription_status[4];
						$allSubscriptionStatus->$ibedc = $exp_all_subscription_status[5];
						$allSubscriptionStatus->$phed = $exp_all_subscription_status[6];
						$allSubscriptionStatus->$aedc = $exp_all_subscription_status[7];

						$all_subscription_status_json = json_encode($allSubscriptionStatus, true);
						$decode_all_subscription_status_json = json_decode($all_subscription_status_json, true);

						?>
						<form method="post">
							<?php
							if ($subscription_update_message == true) {
								?>
								<div id="font-color-1" class="message-box font-size-2"><?php echo $subscription_update_message; ?>
								</div>
								<?php
							}
							?>
							<span class="font-size-2 font-family-1"><b>EKEDC Electricity</b></span>
							<input <?php if ($decode_all_subscription_status_json['ekedc'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="ekedc" type="checkbox" class="check-box" /><br>
							<span class="font-size-2 font-family-1"><b>EEDC Electricity</b></span>
							<input <?php if ($decode_all_subscription_status_json['eedc'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="eedc" type="checkbox" class="check-box" /><br>
							<span class="font-size-2 font-family-1"><b>IKEDC Electricity</b></span>
							<input <?php if ($decode_all_subscription_status_json['ikedc'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="ikedc" type="checkbox" class="check-box" /><br>
							<span class="font-size-2 font-family-1"><b>JEDC Electricity</b></span>
							<input <?php if ($decode_all_subscription_status_json['jedc'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="jedc" type="checkbox" class="check-box" /><br>
							<span class="font-size-2 font-family-1"><b>KEDCO Electricity</b></span>
							<input <?php if ($decode_all_subscription_status_json['kano'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="kano" type="checkbox" class="check-box" /><br>
							<span class="font-size-2 font-family-1"><b>IBEDC Electricity</b></span>
							<input <?php if ($decode_all_subscription_status_json['ibedc'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="ibedc" type="checkbox" class="check-box" /><br>
							<span class="font-size-2 font-family-1"><b>PHED Electricity</b></span>
							<input <?php if ($decode_all_subscription_status_json['phed'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="phed" type="checkbox" class="check-box" /><br>
							<span class="font-size-2 font-family-1"><b>AEDC Electricity</b></span>
							<input <?php if ($decode_all_subscription_status_json['aedc'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="aedc" type="checkbox" class="check-box" /><br>
							<input name="update-subscription" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Update Subscription Settings" />

						</form>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">CHOOSE API TO RUN</span>
						</legend>
						<form method="post">

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">EKEDC ELECTRICITY ROUTE</span><br>
								</legend>
								<?php
								$electricity_ekedc_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name);
								if (mysqli_num_rows($electricity_ekedc_select_api) > 0) {
									while ($electricity_api_list = mysqli_fetch_assoc($electricity_ekedc_select_api)) {
										$get_electricity_ekedc_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $electricity_subscription_running_table_name . " WHERE subscription_name='ekedc'"));
										if ($electricity_api_list["website"] !== $get_electricity_ekedc_api_website["website"]) {
											echo $electricity_api_list["website"] . ' <input value="' . $electricity_api_list["website"] . '" name="ekedc" type="radio" class="check-box"/><br>';
										} else {
											echo $electricity_api_list["website"] . ' <input checked value="' . $electricity_api_list["website"] . '" name="ekedc" type="radio" class="check-box"/><br>';
										}
										$ekedc_electricity_api_discount .= $get_electricity_ekedc_api_website["discount_1"] . "," . $get_electricity_ekedc_api_website["discount_2"] . "," . $get_electricity_ekedc_api_website["discount_3"] . "," . $get_electricity_ekedc_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Electricity API was Installed!';
								}

								$ekedc_exp_electricity_api_discount = array_filter(explode(",", trim($ekedc_electricity_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $ekedc_exp_electricity_api_discount[0]; ?>" name="ekedc-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $ekedc_exp_electricity_api_discount[1]; ?>" name="ekedc-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $ekedc_exp_electricity_api_discount[2]; ?>" name="ekedc-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $ekedc_exp_electricity_api_discount[3]; ?>" name="ekedc-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">EEDC ELECTRICITY ROUTE</span><br>
								</legend>
								<?php
								$electricity_eedc_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name);
								if (mysqli_num_rows($electricity_eedc_select_api) > 0) {
									while ($electricity_api_list = mysqli_fetch_assoc($electricity_eedc_select_api)) {
										$get_electricity_eedc_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $electricity_subscription_running_table_name . " WHERE subscription_name='eedc'"));
										if ($electricity_api_list["website"] !== $get_electricity_eedc_api_website["website"]) {
											echo $electricity_api_list["website"] . ' <input value="' . $electricity_api_list["website"] . '" name="eedc" type="radio" class="check-box"/><br>';
										} else {
											echo $electricity_api_list["website"] . ' <input checked value="' . $electricity_api_list["website"] . '" name="eedc" type="radio" class="check-box"/><br>';
										}
										$eedc_electricity_api_discount .= $get_electricity_eedc_api_website["discount_1"] . "," . $get_electricity_eedc_api_website["discount_2"] . "," . $get_electricity_eedc_api_website["discount_3"] . "," . $get_electricity_eedc_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Electricity API was Installed!';
								}

								$eedc_exp_electricity_api_discount = array_filter(explode(",", trim($eedc_electricity_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $eedc_exp_electricity_api_discount[0]; ?>" name="eedc-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $eedc_exp_electricity_api_discount[1]; ?>" name="eedc-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $eedc_exp_electricity_api_discount[2]; ?>" name="eedc-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $eedc_exp_electricity_api_discount[3]; ?>" name="eedc-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>


							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">IKEDC ELECTRICITY ROUTE</span><br>
								</legend>
								<?php
								$electricity_ikedc_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name);
								if (mysqli_num_rows($electricity_ikedc_select_api) > 0) {
									while ($electricity_api_list = mysqli_fetch_assoc($electricity_ikedc_select_api)) {
										$get_electricity_ikedc_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $electricity_subscription_running_table_name . " WHERE subscription_name='ikedc'"));
										if ($electricity_api_list["website"] !== $get_electricity_ikedc_api_website["website"]) {
											echo $electricity_api_list["website"] . ' <input value="' . $electricity_api_list["website"] . '" name="ikedc" type="radio" class="check-box"/><br>';
										} else {
											echo $electricity_api_list["website"] . ' <input checked value="' . $electricity_api_list["website"] . '" name="ikedc" type="radio" class="check-box"/><br>';
										}
										$ikedc_electricity_api_discount .= $get_electricity_ikedc_api_website["discount_1"] . "," . $get_electricity_ikedc_api_website["discount_2"] . "," . $get_electricity_ikedc_api_website["discount_3"] . "," . $get_electricity_ikedc_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Electricity API was Installed!';
								}

								$ikedc_exp_electricity_api_discount = array_filter(explode(",", trim($ikedc_electricity_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $ikedc_exp_electricity_api_discount[0]; ?>" name="ikedc-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $ikedc_exp_electricity_api_discount[1]; ?>" name="ikedc-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $ikedc_exp_electricity_api_discount[2]; ?>" name="ikedc-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $ikedc_exp_electricity_api_discount[3]; ?>" name="ikedc-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>


							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">JEDC ELECTRICITY ROUTE</span><br>
								</legend>
								<?php
								$electricity_jedc_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name);
								if (mysqli_num_rows($electricity_jedc_select_api) > 0) {
									while ($electricity_api_list = mysqli_fetch_assoc($electricity_jedc_select_api)) {
										$get_electricity_jedc_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $electricity_subscription_running_table_name . " WHERE subscription_name='jedc'"));
										if ($electricity_api_list["website"] !== $get_electricity_jedc_api_website["website"]) {
											echo $electricity_api_list["website"] . ' <input value="' . $electricity_api_list["website"] . '" name="jedc" type="radio" class="check-box"/><br>';
										} else {
											echo $electricity_api_list["website"] . ' <input checked value="' . $electricity_api_list["website"] . '" name="jedc" type="radio" class="check-box"/><br>';
										}
										$jedc_electricity_api_discount .= $get_electricity_jedc_api_website["discount_1"] . "," . $get_electricity_jedc_api_website["discount_2"] . "," . $get_electricity_jedc_api_website["discount_3"] . "," . $get_electricity_jedc_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Electricity API was Installed!';
								}

								$jedc_exp_electricity_api_discount = array_filter(explode(",", trim($jedc_electricity_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $jedc_exp_electricity_api_discount[0]; ?>" name="jedc-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $jedc_exp_electricity_api_discount[1]; ?>" name="jedc-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $jedc_exp_electricity_api_discount[2]; ?>" name="jedc-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $jedc_exp_electricity_api_discount[3]; ?>" name="jedc-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>


							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">KEDCO ELECTRICITY ROUTE</span><br>
								</legend>
								<?php
								$electricity_kano_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name);
								if (mysqli_num_rows($electricity_kano_select_api) > 0) {
									while ($electricity_api_list = mysqli_fetch_assoc($electricity_kano_select_api)) {
										$get_electricity_kano_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $electricity_subscription_running_table_name . " WHERE subscription_name='kano'"));
										if ($electricity_api_list["website"] !== $get_electricity_kano_api_website["website"]) {
											echo $electricity_api_list["website"] . ' <input value="' . $electricity_api_list["website"] . '" name="kano" type="radio" class="check-box"/><br>';
										} else {
											echo $electricity_api_list["website"] . ' <input checked value="' . $electricity_api_list["website"] . '" name="kano" type="radio" class="check-box"/><br>';
										}
										$kano_electricity_api_discount .= $get_electricity_kano_api_website["discount_1"] . "," . $get_electricity_kano_api_website["discount_2"] . "," . $get_electricity_kano_api_website["discount_3"] . "," . $get_electricity_kano_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Electricity API was Installed!';
								}

								$kano_exp_electricity_api_discount = array_filter(explode(",", trim($kano_electricity_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $kano_exp_electricity_api_discount[0]; ?>" name="kano-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $kano_exp_electricity_api_discount[1]; ?>" name="kano-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $kano_exp_electricity_api_discount[2]; ?>" name="kano-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $kano_exp_electricity_api_discount[3]; ?>" name="kano-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>


							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">IBEDC ELECTRICITY ROUTE</span><br>
								</legend>
								<?php
								$electricity_ibedc_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name);
								if (mysqli_num_rows($electricity_ibedc_select_api) > 0) {
									while ($electricity_api_list = mysqli_fetch_assoc($electricity_ibedc_select_api)) {
										$get_electricity_ibedc_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $electricity_subscription_running_table_name . " WHERE subscription_name='ibedc'"));
										if ($electricity_api_list["website"] !== $get_electricity_ibedc_api_website["website"]) {
											echo $electricity_api_list["website"] . ' <input value="' . $electricity_api_list["website"] . '" name="ibedc" type="radio" class="check-box"/><br>';
										} else {
											echo $electricity_api_list["website"] . ' <input checked value="' . $electricity_api_list["website"] . '" name="ibedc" type="radio" class="check-box"/><br>';
										}
										$ibedc_electricity_api_discount .= $get_electricity_ibedc_api_website["discount_1"] . "," . $get_electricity_ibedc_api_website["discount_2"] . "," . $get_electricity_ibedc_api_website["discount_3"] . "," . $get_electricity_ibedc_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Electricity API was Installed!';
								}

								$ibedc_exp_electricity_api_discount = array_filter(explode(",", trim($ibedc_electricity_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $ibedc_exp_electricity_api_discount[0]; ?>" name="ibedc-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $ibedc_exp_electricity_api_discount[1]; ?>" name="ibedc-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $ibedc_exp_electricity_api_discount[2]; ?>" name="ibedc-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $ibedc_exp_electricity_api_discount[3]; ?>" name="ibedc-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>


							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">PHED ELECTRICITY ROUTE</span><br>
								</legend>
								<?php
								$electricity_phed_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name);
								if (mysqli_num_rows($electricity_phed_select_api) > 0) {
									while ($electricity_api_list = mysqli_fetch_assoc($electricity_phed_select_api)) {
										$get_electricity_phed_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $electricity_subscription_running_table_name . " WHERE subscription_name='phed'"));
										if ($electricity_api_list["website"] !== $get_electricity_phed_api_website["website"]) {
											echo $electricity_api_list["website"] . ' <input value="' . $electricity_api_list["website"] . '" name="phed" type="radio" class="check-box"/><br>';
										} else {
											echo $electricity_api_list["website"] . ' <input checked value="' . $electricity_api_list["website"] . '" name="phed" type="radio" class="check-box"/><br>';
										}
										$phed_electricity_api_discount .= $get_electricity_phed_api_website["discount_1"] . "," . $get_electricity_phed_api_website["discount_2"] . "," . $get_electricity_phed_api_website["discount_3"] . "," . $get_electricity_phed_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Electricity API was Installed!';
								}

								$phed_exp_electricity_api_discount = array_filter(explode(",", trim($phed_electricity_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $phed_exp_electricity_api_discount[0]; ?>" name="phed-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $phed_exp_electricity_api_discount[1]; ?>" name="phed-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $phed_exp_electricity_api_discount[2]; ?>" name="phed-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $phed_exp_electricity_api_discount[3]; ?>" name="phed-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>


							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">AEDC ELECTRICITY ROUTE</span><br>
								</legend>
								<?php
								$electricity_aedc_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $electricity_table_name);
								if (mysqli_num_rows($electricity_aedc_select_api) > 0) {
									while ($electricity_api_list = mysqli_fetch_assoc($electricity_aedc_select_api)) {
										$get_electricity_aedc_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $electricity_subscription_running_table_name . " WHERE subscription_name='aedc'"));
										if ($electricity_api_list["website"] !== $get_electricity_aedc_api_website["website"]) {
											echo $electricity_api_list["website"] . ' <input value="' . $electricity_api_list["website"] . '" name="aedc" type="radio" class="check-box"/><br>';
										} else {
											echo $electricity_api_list["website"] . ' <input checked value="' . $electricity_api_list["website"] . '" name="aedc" type="radio" class="check-box"/><br>';
										}
										$aedc_electricity_api_discount .= $get_electricity_aedc_api_website["discount_1"] . "," . $get_electricity_aedc_api_website["discount_2"] . "," . $get_electricity_aedc_api_website["discount_3"] . "," . $get_electricity_aedc_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Electricity API was Installed!';
								}

								$aedc_exp_electricity_api_discount = array_filter(explode(",", trim($aedc_electricity_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $aedc_exp_electricity_api_discount[0]; ?>" name="aedc-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $aedc_exp_electricity_api_discount[1]; ?>" name="aedc-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $aedc_exp_electricity_api_discount[2]; ?>" name="aedc-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $aedc_exp_electricity_api_discount[3]; ?>" name="aedc-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
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