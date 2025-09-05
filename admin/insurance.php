<?php session_start();
if (!isset($_SESSION["admin"])) {
	header("Location: /admin/login.php");
} else {
	include("../include/admin-config.php");
	include("../include/admin-details.php");
}

if ($conn_server_db == true) {
	$insurance_table_name = "insurance_api";
	$insurance_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $insurance_table_name . "(website VARCHAR(225) NOT NULL, apikey VARCHAR(225))";
	if (mysqli_query($conn_server_db, $insurance_apikey_db_table) == true) {
	}

	$insurance_select_running_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $insurance_table_name . " LIMIT 1");
	if (mysqli_num_rows($insurance_select_running_api) > 0) {
		while ($insurance_api_running_list = mysqli_fetch_assoc($insurance_select_running_api)) {
			$first_insurance_api_website_row = $insurance_api_running_list["website"];
			$insurance_subscription_running_table_name = "insurance_subscription_running_api";
			$insurance_subscription_running_db_table = "CREATE TABLE IF NOT EXISTS " . $insurance_subscription_running_table_name . "(subscription_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))";
			if (mysqli_query($conn_server_db, $insurance_subscription_running_db_table) == true) {
				if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $insurance_subscription_running_table_name)) == 0) {
					$insert_insurance_subscription_running_api = "INSERT INTO " . $insurance_subscription_running_table_name . " (subscription_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('motor_insurance', '$first_insurance_api_website_row','1','1','1','1');";
					if (mysqli_multi_query($conn_server_db, $insert_insurance_subscription_running_api) == true) {

					}
				}
			}
		}
	}


	$insurance_subscription_table_name = "insurance_subscription_status";
	$insurance_subscription_db_table = "CREATE TABLE IF NOT EXISTS " . $insurance_subscription_table_name . "(subscription_name VARCHAR(30) NOT NULL, subscription_status VARCHAR(30) NOT NULL)";
	if (mysqli_query($conn_server_db, $insurance_subscription_db_table) == true) {
		$insert_subscription_status = "INSERT INTO " . $insurance_subscription_table_name . " (subscription_name, subscription_status) VALUES ('motor_insurance', 'active');";
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $insurance_subscription_table_name)) == 0) {
			if (mysqli_multi_query($conn_server_db, $insert_subscription_status) == true) {

			}
		}
	}
}


if (isset($_POST["install-api"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$insurance_api_website = "INSERT INTO " . $insurance_table_name . " (website) VALUES ('$api_name')";

	$check_insurance_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $insurance_table_name . " WHERE website='$api_name'");

	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_insurance_select_api) == 0) {
			if (mysqli_query($conn_server_db, $insurance_api_website) == true) {
				$add_api_message = ucwords($api_name) . " insurance API installed successfully! ";
			}
		} else {
			$add_api_message = ucwords($api_name) . " insurance API has already been Installed! ";
		}
	}

}

if (isset($_POST["update-apikey"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$apikey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["apikey"]));

	$insurance_api_website = "UPDATE " . $insurance_table_name . " SET apikey='$apikey' WHERE website='$api_name'";
	$check_insurance_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $insurance_table_name . " WHERE website='$api_name'");
	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_insurance_select_api) > 0) {
			if (mysqli_query($conn_server_db, $insurance_api_website) == true) {
				$update_api_message = ucwords($api_name) . " API key Updated successfully! ";
			}
		}
	} else {
		$update_api_message = "Error: Cannot Update Empty API Website";
	}

}

if (isset($_POST["update-subscription"])) {
	$motor_insurance = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["motor_insurance"]));

	if ($motor_insurance == "active") {
		$motor_insurance_status = "active";
	} else {
		$motor_insurance_status = "down";
	}

	$update_motor_insurance_subscription_status = "UPDATE " . $insurance_subscription_table_name . " SET subscription_status='$motor_insurance_status' WHERE subscription_name='motor_insurance'";

	if (mysqli_query($conn_server_db, $update_motor_insurance_subscription_status) == true) {
		$subscription_update_message = "All subscription Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

}

if (isset($_POST["run-api"])) {
	$motor_insurance_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["motor_insurance"]));
	$motor_insurance_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["motor_insurance-discount-1"])));
	$motor_insurance_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["motor_insurance-discount-2"])));
	$motor_insurance_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["motor_insurance-discount-3"])));
	$motor_insurance_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["motor_insurance-discount-4"])));

	$motor_insurance_subscription_table_injection = "UPDATE " . $insurance_subscription_running_table_name . " SET website='$motor_insurance_website', discount_1='$motor_insurance_discount_1', discount_2='$motor_insurance_discount_2', discount_3='$motor_insurance_discount_3', discount_4='$motor_insurance_discount_4' WHERE subscription_name='motor_insurance'";
	if (mysqli_query($conn_server_db, $motor_insurance_subscription_table_injection) == true) {
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
					<span class="font-size-2 font-family-1">INSTALL INSURANCE API</span>
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
						<option disabled hidden selected>Install Insurance API</option>
						<option value="vtpass.com">vtpass.com</option>
					</select>
					<input name="install-api" type="submit"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25"
						value="Install API" />
				</form>
			</fieldset><br>

			<?php
			$check_count_insurance_table_name = mysqli_query($conn_server_db, "SELECT website FROM " . $insurance_table_name);
			if ($check_count_insurance_table_name == true) {
				if (mysqli_num_rows($check_count_insurance_table_name) > 0) {

					?>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPDATE INSURANCE API KEY</span>
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
								$insurance_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $insurance_table_name);
								if (mysqli_num_rows($insurance_select_api) > 0) {
									while ($insurance_api_list = mysqli_fetch_assoc($insurance_select_api)) {
										echo '<option data-apikey="' . $insurance_api_list["apikey"] . '" value="' . $insurance_api_list["website"] . '">' . $insurance_api_list["website"] . "</option>";
									}
								} else {
									echo '<option selected hidden value="">No Insurance API was Installed!</option>';
								}

								?>
							</select>
							<input name="apikey" id="apikey" type="text"
								class="input-box mobile-width-93 system-width-60 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
								placeholder="Apikey format USERNAME:PASSWORD" />
							<input name="update-apikey" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Update API key" />
						</form>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">ENABLE/DISABLE INSURANCE SUBSCRIPTION</span>
						</legend>
						<?php
						$insurance_subscription_select_api = mysqli_query($conn_server_db, "SELECT subscription_name, subscription_status FROM " . $insurance_subscription_table_name);
						if (mysqli_num_rows($insurance_subscription_select_api) > 0) {
							while ($insurance_subscription_status_list = mysqli_fetch_assoc($insurance_subscription_select_api)) {
								;
								$all_subscription_status .= $insurance_subscription_status_list["subscription_status"] . ",";
							}
						}

						$exp_all_subscription_status = array_filter(explode(",", trim($all_subscription_status)));
						class allsubscription
						{
						}
						$motor_insurance = "motor_insurance";
						$allsubscriptionStatus = new allsubscription;
						$allsubscriptionStatus->$motor_insurance = $exp_all_subscription_status[0];
						$all_subscription_status_json = json_encode($allsubscriptionStatus, true);
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
							<span class="font-size-2 font-family-1"><b>MOTOR INSURANCE</b></span>
							<input <?php if ($decode_all_subscription_status_json['motor_insurance'] == 'active') {
								echo 'checked';
							} ?> value="active" name="motor_insurance" type="checkbox" class="check-box" />
							<input name="update-subscription" type="submit"
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
									<span class="font-size-2 font-family-1">MOTOR INSURANCE ROUTE</span><br>
								</legend>
								<?php
								$insurance_motor_insurance_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $insurance_table_name);
								if (mysqli_num_rows($insurance_motor_insurance_select_api) > 0) {
									while ($insurance_api_list = mysqli_fetch_assoc($insurance_motor_insurance_select_api)) {
										$get_insurance_motor_insurance_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $insurance_subscription_running_table_name . " WHERE subscription_name='motor_insurance'"));
										if ($insurance_api_list["website"] !== $get_insurance_motor_insurance_api_website["website"]) {
											echo $insurance_api_list["website"] . ' <input value="' . $insurance_api_list["website"] . '" name="motor_insurance" type="radio" class="check-box"/><br>';
										} else {
											echo $insurance_api_list["website"] . ' <input checked value="' . $insurance_api_list["website"] . '" name="motor_insurance" type="radio" class="check-box"/><br>';
										}
										$motor_insurance_insurance_api_discount .= $get_insurance_motor_insurance_api_website["discount_1"] . "," . $get_insurance_motor_insurance_api_website["discount_2"] . "," . $get_insurance_motor_insurance_api_website["discount_3"] . "," . $get_insurance_motor_insurance_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No insurance API was Installed!';
								}

								$motor_insurance_exp_insurance_api_discount = array_filter(explode(",", trim($motor_insurance_insurance_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS discount</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $motor_insurance_exp_insurance_api_discount[0]; ?>"
										name="motor_insurance-discount-1" type="text" class="input-box half-half-length"
										placeholder="Smart Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $motor_insurance_exp_insurance_api_discount[1]; ?>"
										name="motor_insurance-discount-2" type="text" class="input-box half-half-length"
										placeholder="VIP Earner" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $motor_insurance_exp_insurance_api_discount[2]; ?>"
										name="motor_insurance-discount-3" type="text" class="input-box half-half-length"
										placeholder="VIP Vendor" /><span class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $motor_insurance_exp_insurance_api_discount[3]; ?>"
										name="motor_insurance-discount-4" type="text" class="input-box half-half-length"
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