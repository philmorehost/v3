<?php session_start();
if (!isset($_SESSION["admin"])) {
	header("Location: /admin/login.php");
} else {
	include("../include/admin-config.php");
	include("../include/admin-details.php");
}

if ($conn_server_db == true) {
	$cable_table_name = "cable_api";
	$cable_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $cable_table_name . "(website VARCHAR(225) NOT NULL, apikey VARCHAR(225))";
	if (mysqli_query($conn_server_db, $cable_apikey_db_table) == true) {
	}


	$cable_select_running_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $cable_table_name . " LIMIT 1");
	if (mysqli_num_rows($cable_select_running_api) > 0) {
		while ($cable_api_running_list = mysqli_fetch_assoc($cable_select_running_api)) {
			$first_cable_api_website_row = $cable_api_running_list["website"];
			$cable_subscription_running_table_name = "cable_subscription_running_api";
			$cable_subscription_running_db_table = "CREATE TABLE IF NOT EXISTS " . $cable_subscription_running_table_name . "(subscription_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))";
			if (mysqli_query($conn_server_db, $cable_subscription_running_db_table) == true) {
				if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $cable_subscription_running_table_name)) == 0) {
					$insert_cable_subscription_running_api = "INSERT INTO " . $cable_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('startimes', '$first_cable_api_website_row','1','1','1','1');";
					$insert_cable_subscription_running_api .= "INSERT INTO " . $cable_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('dstv', '$first_cable_api_website_row','1','1','1','1');";
					$insert_cable_subscription_running_api .= "INSERT INTO " . $cable_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('gotv', '$first_cable_api_website_row','1','1','1','1');";
					if (mysqli_multi_query($conn_server_db, $insert_cable_subscription_running_api) == true) {

					}
				}
			}
		}
	}


	$cable_subscription_table_name = "cable_subscription_status";
	$cable_subscription_db_table = "CREATE TABLE IF NOT EXISTS " . $cable_subscription_table_name . "(subscription_name VARCHAR(30) NOT NULL, subscription_status VARCHAR(30) NOT NULL)";
	if (mysqli_query($conn_server_db, $cable_subscription_db_table) == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $cable_subscription_table_name)) == 0) {
			$insert_subscription_status = "INSERT INTO " . $cable_subscription_table_name . " (subscription_name, subscription_status) VALUES ('startimes', 'active');";
			$insert_subscription_status .= "INSERT INTO " . $cable_subscription_table_name . " (subscription_name, subscription_status) VALUES ('dstv', 'active');";
			$insert_subscription_status .= "INSERT INTO " . $cable_subscription_table_name . " (subscription_name, subscription_status) VALUES ('gotv', 'active');";

			if (mysqli_multi_query($conn_server_db, $insert_subscription_status) == true) {

			}
		}
	}
}


if (isset($_POST["install-api"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$cable_api_website = "INSERT INTO " . $cable_table_name . " (website) VALUES ('$api_name')";

	$check_cable_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $cable_table_name . " WHERE website='$api_name'");

	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_cable_select_api) == 0) {
			if (mysqli_query($conn_server_db, $cable_api_website) == true) {
				$add_api_message = ucwords($api_name) . " cable API installed successfully! ";
			}
		} else {
			$add_api_message = ucwords($api_name) . " cable API has already been Installed! ";
		}
	}

}

if (isset($_POST["update-apikey"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$apikey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["apikey"]));

	$cable_api_website = "UPDATE " . $cable_table_name . " SET apikey='$apikey' WHERE website='$api_name'";
	$check_cable_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $cable_table_name . " WHERE website='$api_name'");
	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_cable_select_api) > 0) {
			if (mysqli_query($conn_server_db, $cable_api_website) == true) {
				$update_api_message = ucwords($api_name) . " API key Updated successfully! ";
			}
		}
	} else {
		$update_api_message = "Error: Cannot Update Empty API Website";
	}

}

if (isset($_POST["update-subscription"])) {
	$startimes = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["startimes"]));
	$dstv = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["dstv"]));
	$gotv = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["gotv"]));

	if ($startimes == "active") {
		$startimes_status = "active";
	} else {
		$startimes_status = "down";
	}

	if ($dstv == "active") {
		$dstv_status = "active";
	} else {
		$dstv_status = "down";
	}

	if ($gotv == "active") {
		$gotv_status = "active";
	} else {
		$gotv_status = "down";
	}


	$update_startimes_subscription_status = "UPDATE " . $cable_subscription_table_name . " SET subscription_status='$startimes_status' WHERE subscription_name='startimes'";
	$update_dstv_subscription_status = "UPDATE " . $cable_subscription_table_name . " SET subscription_status='$dstv_status' WHERE subscription_name='dstv'";
	$update_gotv_subscription_status = "UPDATE " . $cable_subscription_table_name . " SET subscription_status='$gotv_status' WHERE subscription_name='gotv'";

	if (mysqli_query($conn_server_db, $update_startimes_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_dstv_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_gotv_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}
}

if (isset($_POST["run-api"])) {
	$startimes_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["startimes"]));
	$startimes_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["startimes-discount-1"])));
	$startimes_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["startimes-discount-2"])));
	$startimes_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["startimes-discount-3"])));
	$startimes_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["startimes-discount-4"])));

	$startimes_subscription_table_injection = "UPDATE " . $cable_subscription_running_table_name . " SET website='$startimes_website', discount_1='$startimes_discount_1', discount_2='$startimes_discount_2', discount_3='$startimes_discount_3', discount_4='$startimes_discount_4' WHERE subscription_name='startimes'";
	if (mysqli_query($conn_server_db, $startimes_subscription_table_injection) == true) {
	}

	$dstv_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["dstv"]));
	$dstv_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["dstv-discount-1"])));
	$dstv_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["dstv-discount-2"])));
	$dstv_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["dstv-discount-3"])));
	$dstv_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["dstv-discount-4"])));

	$dstv_subscription_table_injection = "UPDATE " . $cable_subscription_running_table_name . " SET website='$dstv_website', discount_1='$dstv_discount_1', discount_2='$dstv_discount_2', discount_3='$dstv_discount_3', discount_4='$dstv_discount_4' WHERE subscription_name='dstv'";
	if (mysqli_query($conn_server_db, $dstv_subscription_table_injection) == true) {
	}

	$gotv_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["gotv"]));
	$gotv_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["gotv-discount-1"])));
	$gotv_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["gotv-discount-2"])));
	$gotv_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["gotv-discount-3"])));
	$gotv_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["gotv-discount-4"])));

	$gotv_subscription_table_injection = "UPDATE " . $cable_subscription_running_table_name . " SET website='$gotv_website', discount_1='$gotv_discount_1', discount_2='$gotv_discount_2', discount_3='$gotv_discount_3', discount_4='$gotv_discount_4' WHERE subscription_name='gotv'";
	if (mysqli_query($conn_server_db, $gotv_subscription_table_injection) == true) {
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
					<span class="font-size-2 font-family-1">INSTALL CABLE API</span>
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
						<option disabled hidden selected>Install CABLE API</option>
						<option value="datagifting.com.ng">datagifting.com.ng</option>
						<option value="smartrecharge.ng">smartrecharge.ng</option>
						<option value="smartrechargeapi.com">smartrechargeapi.com</option>
						<option value="vtpass.com">vtpass.com</option>
						<option value="mobileone.ng">mobileone.ng</option>
					</select>
					<input name="install-api" type="submit"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25"
						value="Install API" />
				</form>
			</fieldset><br>

			<?php
			$check_count_cable_table_name = mysqli_query($conn_server_db, "SELECT website FROM " . $cable_table_name);
			if ($check_count_cable_table_name == true) {
				if (mysqli_num_rows($check_count_cable_table_name) > 0) {

					?>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPDATE CABLE API KEY</span>
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
								$cable_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $cable_table_name);
								if (mysqli_num_rows($cable_select_api) > 0) {
									while ($cable_api_list = mysqli_fetch_assoc($cable_select_api)) {
										echo '<option data-apikey="' . $cable_api_list["apikey"] . '" value="' . $cable_api_list["website"] . '">' . $cable_api_list["website"] . "</option>";
									}
								} else {
									echo '<option selected hidden value="">No Cable API was Installed!</option>';
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
							<span class="font-size-2 font-family-1">ENABLE/DISABLE CABLE SUBSCRIPTION</span>
						</legend>
						<?php
						$cable_subscription_select_api = mysqli_query($conn_server_db, "SELECT subscription_name, subscription_status FROM " . $cable_subscription_table_name);
						if (mysqli_num_rows($cable_subscription_select_api) > 0) {
							while ($cable_subscription_status_list = mysqli_fetch_assoc($cable_subscription_select_api)) {
								;
								$all_subscription_status .= $cable_subscription_status_list["subscription_status"] . ",";
							}
						}

						$exp_all_subscription_status = array_filter(explode(",", trim($all_subscription_status)));
						class allSubscription
						{
						}
						$startimes = "startimes";
						$dstv = "dstv";
						$gotv = "gotv";
						$allSubscriptionStatus = new allSubscription;
						$allSubscriptionStatus->$startimes = $exp_all_subscription_status[0];
						$allSubscriptionStatus->$dstv = $exp_all_subscription_status[1];
						$allSubscriptionStatus->$gotv = $exp_all_subscription_status[2];

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
							<span class="font-size-2 font-family-1"><b>STARTIMES</b></span>
							<input <?php if ($decode_all_subscription_status_json['startimes'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="startimes" type="checkbox" class="check-box" /> -
							<span class="font-size-2 font-family-1"><b>DSTV</b></span>
							<input <?php if ($decode_all_subscription_status_json['dstv'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="dstv" type="checkbox" class="check-box" /> -
							<span class="font-size-2 font-family-1"><b>GOTV</b></span>
							<input <?php if ($decode_all_subscription_status_json['gotv'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="gotv" type="checkbox" class="check-box" /> -
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
									<span class="font-size-2 font-family-1">STARTIMES CABLE ROUTE</span><br>
								</legend>
								<?php
								$cable_startimes_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $cable_table_name);
								if (mysqli_num_rows($cable_startimes_select_api) > 0) {
									while ($cable_api_list = mysqli_fetch_assoc($cable_startimes_select_api)) {
										$get_cable_startimes_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $cable_subscription_running_table_name . " WHERE subscription_name='startimes'"));
										if ($cable_api_list["website"] !== $get_cable_startimes_api_website["website"]) {
											echo $cable_api_list["website"] . ' <input value="' . $cable_api_list["website"] . '" name="startimes" type="radio" class="check-box"/><br>';
										} else {
											echo $cable_api_list["website"] . ' <input checked value="' . $cable_api_list["website"] . '" name="startimes" type="radio" class="check-box"/><br>';
										}
										$startimes_cable_api_discount .= $get_cable_startimes_api_website["discount_1"] . "," . $get_cable_startimes_api_website["discount_2"] . "," . $get_cable_startimes_api_website["discount_3"] . "," . $get_cable_startimes_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Cable API was Installed!';
								}

								$startimes_exp_cable_api_discount = array_filter(explode(",", trim($startimes_cable_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $startimes_exp_cable_api_discount[0]; ?>"
										name="startimes-discount-1" type="text" class="input-box half-half-length"
										placeholder="Smart Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $startimes_exp_cable_api_discount[1]; ?>"
										name="startimes-discount-2" type="text" class="input-box half-half-length"
										placeholder="VIP Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $startimes_exp_cable_api_discount[2]; ?>"
										name="startimes-discount-3" type="text" class="input-box half-half-length"
										placeholder="VIP Vendor" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $startimes_exp_cable_api_discount[3]; ?>"
										name="startimes-discount-4" type="text" class="input-box half-half-length"
										placeholder="API Earner" /><span class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">DSTV CABLE ROUTE</span><br>
								</legend>
								<?php
								$cable_dstv_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $cable_table_name);
								if (mysqli_num_rows($cable_dstv_select_api) > 0) {
									while ($cable_api_list = mysqli_fetch_assoc($cable_dstv_select_api)) {
										$get_cable_dstv_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $cable_subscription_running_table_name . " WHERE subscription_name='dstv'"));
										if ($cable_api_list["website"] !== $get_cable_dstv_api_website["website"]) {
											echo $cable_api_list["website"] . ' <input value="' . $cable_api_list["website"] . '" name="dstv" type="radio" class="check-box"/><br>';
										} else {
											echo $cable_api_list["website"] . ' <input checked value="' . $cable_api_list["website"] . '" name="dstv" type="radio" class="check-box"/><br>';
										}
										$dstv_cable_api_discount .= $get_cable_dstv_api_website["discount_1"] . "," . $get_cable_dstv_api_website["discount_2"] . "," . $get_cable_dstv_api_website["discount_3"] . "," . $get_cable_dstv_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Cable API was Installed!';
								}

								$dstv_exp_cable_api_discount = array_filter(explode(",", trim($dstv_cable_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $dstv_exp_cable_api_discount[0]; ?>" name="dstv-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $dstv_exp_cable_api_discount[1]; ?>" name="dstv-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $dstv_exp_cable_api_discount[2]; ?>" name="dstv-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $dstv_exp_cable_api_discount[3]; ?>" name="dstv-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>


							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">GOTV CABLE ROUTE</span><br>
								</legend>
								<?php
								$cable_gotv_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $cable_table_name);
								if (mysqli_num_rows($cable_gotv_select_api) > 0) {
									while ($cable_api_list = mysqli_fetch_assoc($cable_gotv_select_api)) {
										$get_cable_gotv_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $cable_subscription_running_table_name . " WHERE subscription_name='gotv'"));
										if ($cable_api_list["website"] !== $get_cable_gotv_api_website["website"]) {
											echo $cable_api_list["website"] . ' <input value="' . $cable_api_list["website"] . '" name="gotv" type="radio" class="check-box"/><br>';
										} else {
											echo $cable_api_list["website"] . ' <input checked value="' . $cable_api_list["website"] . '" name="gotv" type="radio" class="check-box"/><br>';
										}
										$gotv_cable_api_discount .= $get_cable_gotv_api_website["discount_1"] . "," . $get_cable_gotv_api_website["discount_2"] . "," . $get_cable_gotv_api_website["discount_3"] . "," . $get_cable_gotv_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Cable API was Installed!';
								}

								$gotv_exp_cable_api_discount = array_filter(explode(",", trim($gotv_cable_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $gotv_exp_cable_api_discount[0]; ?>" name="gotv-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $gotv_exp_cable_api_discount[1]; ?>" name="gotv-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $gotv_exp_cable_api_discount[2]; ?>" name="gotv-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $gotv_exp_cable_api_discount[3]; ?>" name="gotv-discount-4"
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