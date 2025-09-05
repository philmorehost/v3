<?php session_start();
if (!isset($_SESSION["admin"])) {
	header("Location: /admin/login.php");
} else {
	include("../include/admin-config.php");
	include("../include/admin-details.php");
}

if ($conn_server_db == true) {
	$sms_table_name = "sms_api";
	$sms_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $sms_table_name . "(website VARCHAR(225) NOT NULL, apikey VARCHAR(225))";
	if (mysqli_query($conn_server_db, $sms_apikey_db_table) == true) {
	}

	if (mysqli_query($conn_server_db, "CREATE TABLE IF NOT EXISTS sms_sender_id (email VARCHAR(225) NOT NULL, sender_id VARCHAR(60) NOT NULL, status VARCHAR(60) NOT NULL)") == true) {
	}

	$sms_select_running_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sms_table_name . " LIMIT 1");
	if (mysqli_num_rows($sms_select_running_api) > 0) {
		while ($sms_api_running_list = mysqli_fetch_assoc($sms_select_running_api)) {
			$first_sms_api_website_row = $sms_api_running_list["website"];
			$sms_network_running_table_name = "sms_network_running_api";
			$sms_network_running_db_table = "CREATE TABLE IF NOT EXISTS " . $sms_network_running_table_name . "(network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, price_1 VARCHAR(30), price_2 VARCHAR(30), price_3 VARCHAR(30), price_4 VARCHAR(30))";
			if (mysqli_query($conn_server_db, $sms_network_running_db_table) == true) {
				if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $sms_network_running_table_name)) == 0) {
					$insert_sms_network_running_api = "INSERT INTO " . $sms_network_running_table_name . " (network_name, website, price_1, price_2, price_3, price_4) VALUES ('smsserver', '$first_sms_api_website_row','1','1','1','1');";
					if (mysqli_multi_query($conn_server_db, $insert_sms_network_running_api) == true) {

					}
				}
			}
		}
	}


	$sms_network_table_name = "sms_network_status";
	$sms_network_db_table = "CREATE TABLE IF NOT EXISTS " . $sms_network_table_name . "(network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)";
	if (mysqli_query($conn_server_db, $sms_network_db_table) == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $sms_network_table_name)) == 0) {
			$insert_network_status = "INSERT INTO " . $sms_network_table_name . " (network_name, network_status) VALUES ('smsserver', 'active');";
			if (mysqli_multi_query($conn_server_db, $insert_network_status) == true) {

			}
		}
	}
}


if (isset($_POST["install-api"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$sms_api_website = "INSERT INTO " . $sms_table_name . " (website) VALUES ('$api_name')";

	$check_sms_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sms_table_name . " WHERE website='$api_name'");

	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_sms_select_api) == 0) {
			if (mysqli_query($conn_server_db, $sms_api_website) == true) {
				$add_api_message = ucwords($api_name) . " sms API installed successfully! ";
			}
		} else {
			$add_api_message = ucwords($api_name) . " sms API has already been Installed! ";
		}
	}

}

if (isset($_POST["update-apikey"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$apikey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["apikey"]));

	$sms_api_website = "UPDATE " . $sms_table_name . " SET apikey='$apikey' WHERE website='$api_name'";
	$check_sms_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sms_table_name . " WHERE website='$api_name'");
	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_sms_select_api) > 0) {
			if (mysqli_query($conn_server_db, $sms_api_website) == true) {
				$update_api_message = ucwords($api_name) . " API key Updated successfully! ";
			}
		}
	} else {
		$update_api_message = "Error: Cannot Update Empty API Website";
	}

}

if (isset($_POST["update-network"])) {
	$smsserver = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["smsserver"]));

	if ($smsserver == "active") {
		$smsserver_status = "active";
	} else {
		$smsserver_status = "down";
	}

	$update_smsserver_network_status = "UPDATE " . $sms_network_table_name . " SET network_status='$smsserver_status' WHERE network_name='smsserver'";

	if (mysqli_query($conn_server_db, $update_smsserver_network_status) == true) {
		$network_update_message = "All Network Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

}

if (isset($_POST["run-api"])) {
	$smsserver_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["smsserver"]));
	$smsserver_price_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["smsserver-price-1"])));
	$smsserver_price_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["smsserver-price-2"])));
	$smsserver_price_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["smsserver-price-3"])));
	$smsserver_price_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["smsserver-price-4"])));

	$smsserver_network_table_injection = "UPDATE " . $sms_network_running_table_name . " SET website='$smsserver_website', price_1='$smsserver_price_1', price_2='$smsserver_price_2', price_3='$smsserver_price_3', price_4='$smsserver_price_4' WHERE network_name='smsserver'";
	if (mysqli_query($conn_server_db, $smsserver_network_table_injection) == true) {
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
					<span class="font-size-2 font-family-1">INSTALL SMS API</span>
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
						<option disabled hidden selected>Install SMS API</option>
						<option value="datagifting.com.ng">datagifting.com.ng</option>
						<!-- <option value="philmoresms.com">philmoresms.com</option> -->
					</select>
					<input name="install-api" type="submit"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25"
						value="Install API" />
				</form>
			</fieldset><br>

			<?php
			$check_count_sms_table_name = mysqli_query($conn_server_db, "SELECT website FROM " . $sms_table_name);
			if ($check_count_sms_table_name == true) {
				if (mysqli_num_rows($check_count_sms_table_name) > 0) {

					?>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPDATE SMS API KEY</span>
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
								$sms_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sms_table_name);
								if (mysqli_num_rows($sms_select_api) > 0) {
									while ($sms_api_list = mysqli_fetch_assoc($sms_select_api)) {
										echo '<option data-apikey="' . $sms_api_list["apikey"] . '" value="' . $sms_api_list["website"] . '">' . $sms_api_list["website"] . "</option>";
									}
								} else {
									echo '<option selected hidden value="">No SMS API was Installed!</option>';
								}

								?>
							</select>
							<input name="apikey" id="apikey" type="text"
								class="input-box mobile-width-93 system-width-60 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
								placeholder="Apikey format APIKEY:APITOKEN" />
							<input name="update-apikey" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Update API key" />
						</form>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">ENABLE/DISABLE SMS NETWORK</span>
						</legend>
						<?php
						$sms_network_select_api = mysqli_query($conn_server_db, "SELECT network_name, network_status FROM " . $sms_network_table_name);
						if (mysqli_num_rows($sms_network_select_api) > 0) {
							while ($sms_network_status_list = mysqli_fetch_assoc($sms_network_select_api)) {
								;
								$all_network_status .= $sms_network_status_list["network_status"] . ",";
							}
						}

						$exp_all_network_status = array_filter(explode(",", trim($all_network_status)));
						class allNetwork
						{
						}
						$smsserver = "smsserver";
						$allNetworkStatus = new allNetwork;
						$allNetworkStatus->$smsserver = $exp_all_network_status[0];
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
							<span class="font-size-2 font-family-1"><b>SMS SERVER</b></span>
							<input <?php if ($decode_all_network_status_json['smsserver'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="smsserver" type="checkbox" class="check-box" />
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
									<span class="font-size-2 font-family-1">SMS SERVER SMS ROUTE</span><br>
								</legend>
								<?php
								$sms_smsserver_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $sms_table_name);
								if (mysqli_num_rows($sms_smsserver_select_api) > 0) {
									while ($sms_api_list = mysqli_fetch_assoc($sms_smsserver_select_api)) {
										$get_sms_smsserver_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, price_1, price_2, price_3, price_4 FROM " . $sms_network_running_table_name . " WHERE network_name='smsserver'"));
										if ($sms_api_list["website"] !== $get_sms_smsserver_api_website["website"]) {
											echo $sms_api_list["website"] . ' <input value="' . $sms_api_list["website"] . '" name="smsserver" type="radio" class="check-box"/><br>';
										} else {
											echo $sms_api_list["website"] . ' <input checked value="' . $sms_api_list["website"] . '" name="smsserver" type="radio" class="check-box"/><br>';
										}
										$smsserver_sms_api_price .= $get_sms_smsserver_api_website["price_1"] . "," . $get_sms_smsserver_api_website["price_2"] . "," . $get_sms_smsserver_api_website["price_3"] . "," . $get_sms_smsserver_api_website["price_4"] . ",";
									}
								} else {
									echo 'No SMS API was Installed!';
								}

								$smsserver_exp_sms_api_price = array_filter(explode(",", trim($smsserver_sms_api_price)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS PRICE</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $smsserver_exp_sms_api_price[0]; ?>" name="smsserver-price-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">₦</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $smsserver_exp_sms_api_price[1]; ?>" name="smsserver-price-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">₦</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $smsserver_exp_sms_api_price[2]; ?>" name="smsserver-price-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">₦</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $smsserver_exp_sms_api_price[3]; ?>" name="smsserver-price-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">₦</span><br>
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