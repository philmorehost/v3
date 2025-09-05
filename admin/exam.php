<?php session_start();
if (!isset($_SESSION["admin"])) {
	header("Location: /admin/login.php");
} else {
	include("../include/admin-config.php");
	include("../include/admin-details.php");
}

if ($conn_server_db == true) {
	$exam_table_name = "exam_api";
	$exam_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $exam_table_name . "(website VARCHAR(225) NOT NULL, apikey VARCHAR(225))";
	if (mysqli_query($conn_server_db, $exam_apikey_db_table) == true) {
	}


	$exam_select_running_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $exam_table_name . " LIMIT 1");
	if (mysqli_num_rows($exam_select_running_api) > 0) {
		while ($exam_api_running_list = mysqli_fetch_assoc($exam_select_running_api)) {
			$first_exam_api_website_row = $exam_api_running_list["website"];
			$exam_pin_running_table_name = "exam_pin_running_api";
			$exam_pin_running_db_table = "CREATE TABLE IF NOT EXISTS " . $exam_pin_running_table_name . "(pin_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL, discount_1 VARCHAR(30), discount_2 VARCHAR(30), discount_3 VARCHAR(30), discount_4 VARCHAR(30))";
			if (mysqli_query($conn_server_db, $exam_pin_running_db_table) == true) {
				if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $exam_pin_running_table_name)) == 0) {
					$insert_exam_pin_running_api = "INSERT INTO " . $exam_pin_running_table_name . " (pin_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('waec', '$first_exam_api_website_row','1','1','1','1');";
					$insert_exam_pin_running_api .= "INSERT INTO " . $exam_pin_running_table_name . " (pin_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('neco', '$first_exam_api_website_row','1','1','1','1');";
					$insert_exam_pin_running_api .= "INSERT INTO " . $exam_pin_running_table_name . " (pin_name, website, discount_1, discount_2, discount_3, discount_4) VALUES ('nabteb', '$first_exam_api_website_row','1','1','1','1');";
					if (mysqli_multi_query($conn_server_db, $insert_exam_pin_running_api) == true) {

					}
				}
			}
		}
	}


	$exam_pin_table_name = "exam_pin_status";
	$exam_pin_db_table = "CREATE TABLE IF NOT EXISTS " . $exam_pin_table_name . "(pin_name VARCHAR(30) NOT NULL, pin_status VARCHAR(30) NOT NULL)";
	if (mysqli_query($conn_server_db, $exam_pin_db_table) == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $exam_pin_table_name)) == 0) {
			$insert_pin_status = "INSERT INTO " . $exam_pin_table_name . " (pin_name, pin_status) VALUES ('waec', 'active');";
			$insert_pin_status .= "INSERT INTO " . $exam_pin_table_name . " (pin_name, pin_status) VALUES ('neco', 'active');";
			$insert_pin_status .= "INSERT INTO " . $exam_pin_table_name . " (pin_name, pin_status) VALUES ('nabteb', 'active');";

			if (mysqli_multi_query($conn_server_db, $insert_pin_status) == true) {

			}
		}
	}
}


if (isset($_POST["install-api"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$exam_api_website = "INSERT INTO " . $exam_table_name . " (website) VALUES ('$api_name')";

	$check_exam_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $exam_table_name . " WHERE website='$api_name'");

	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_exam_select_api) == 0) {
			if (mysqli_query($conn_server_db, $exam_api_website) == true) {
				$add_api_message = ucwords($api_name) . " exam API installed successfully! ";
			}
		} else {
			$add_api_message = ucwords($api_name) . " exam API has already been Installed! ";
		}
	}

}

if (isset($_POST["update-apikey"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$apikey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["apikey"]));

	$exam_api_website = "UPDATE " . $exam_table_name . " SET apikey='$apikey' WHERE website='$api_name'";
	$check_exam_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $exam_table_name . " WHERE website='$api_name'");
	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_exam_select_api) > 0) {
			if (mysqli_query($conn_server_db, $exam_api_website) == true) {
				$update_api_message = ucwords($api_name) . " API key Updated successfully! ";
			}
		}
	} else {
		$update_api_message = "Error: Cannot Update Empty API Website";
	}

}

if (isset($_POST["update-pin"])) {
	$waec = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["waec"]));
	$neco = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["neco"]));
	$nabteb = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["nabteb"]));

	if ($waec == "active") {
		$waec_status = "active";
	} else {
		$waec_status = "down";
	}

	if ($neco == "active") {
		$neco_status = "active";
	} else {
		$neco_status = "down";
	}

	if ($nabteb == "active") {
		$nabteb_status = "active";
	} else {
		$nabteb_status = "down";
	}


	$update_waec_pin_status = "UPDATE " . $exam_pin_table_name . " SET pin_status='$waec_status' WHERE pin_name='waec'";
	$update_neco_pin_status = "UPDATE " . $exam_pin_table_name . " SET pin_status='$neco_status' WHERE pin_name='neco'";
	$update_nabteb_pin_status = "UPDATE " . $exam_pin_table_name . " SET pin_status='$nabteb_status' WHERE pin_name='nabteb'";

	if (mysqli_query($conn_server_db, $update_waec_pin_status) == true) {
		$pin_update_message = "All pin Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_neco_pin_status) == true) {
		$pin_update_message = "All pin Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}

	if (mysqli_query($conn_server_db, $update_nabteb_pin_status) == true) {
		$pin_update_message = "All pin Settings Updated Successfully! ";
	} else {
		echo mysqli_error($conn_server_db);
	}
}

if (isset($_POST["run-api"])) {
	$waec_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["waec"]));
	$waec_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["waec-discount-1"])));
	$waec_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["waec-discount-2"])));
	$waec_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["waec-discount-3"])));
	$waec_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["waec-discount-4"])));

	$waec_pin_table_injection = "UPDATE " . $exam_pin_running_table_name . " SET website='$waec_website', discount_1='$waec_discount_1', discount_2='$waec_discount_2', discount_3='$waec_discount_3', discount_4='$waec_discount_4' WHERE pin_name='waec'";
	if (mysqli_query($conn_server_db, $waec_pin_table_injection) == true) {
	}

	$neco_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["neco"]));
	$neco_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["neco-discount-1"])));
	$neco_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["neco-discount-2"])));
	$neco_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["neco-discount-3"])));
	$neco_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["neco-discount-4"])));

	$neco_pin_table_injection = "UPDATE " . $exam_pin_running_table_name . " SET website='$neco_website', discount_1='$neco_discount_1', discount_2='$neco_discount_2', discount_3='$neco_discount_3', discount_4='$neco_discount_4' WHERE pin_name='neco'";
	if (mysqli_query($conn_server_db, $neco_pin_table_injection) == true) {
	}

	$nabteb_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["nabteb"]));
	$nabteb_discount_1 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["nabteb-discount-1"])));
	$nabteb_discount_2 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["nabteb-discount-2"])));
	$nabteb_discount_3 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["nabteb-discount-3"])));
	$nabteb_discount_4 = mysqli_real_escape_string($conn_server_db, strip_tags(str_replace(",", "", $_POST["nabteb-discount-4"])));

	$nabteb_pin_table_injection = "UPDATE " . $exam_pin_running_table_name . " SET website='$nabteb_website', discount_1='$nabteb_discount_1', discount_2='$nabteb_discount_2', discount_3='$nabteb_discount_3', discount_4='$nabteb_discount_4' WHERE pin_name='nabteb'";
	if (mysqli_query($conn_server_db, $nabteb_pin_table_injection) == true) {
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
					<span class="font-size-2 font-family-1">INSTALL EXAM API</span>
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
						<option disabled hidden selected>Install Exam API</option>
						<option value="datagifting.com.ng">datagifting.com.ng</option>
						<option value="abumpay.com">abumpay.com</option>
						<option value="clubkonnect.com">clubkonnect.com</option>
					</select>
					<input name="install-api" type="submit"
						class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25"
						value="Install API" />
				</form>
			</fieldset><br>

			<?php
			$check_count_exam_table_name = mysqli_query($conn_server_db, "SELECT website FROM " . $exam_table_name);
			if ($check_count_exam_table_name == true) {
				if (mysqli_num_rows($check_count_exam_table_name) > 0) {

					?>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPDATE EXAM API KEY</span>
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
								$exam_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $exam_table_name);
								if (mysqli_num_rows($exam_select_api) > 0) {
									while ($exam_api_list = mysqli_fetch_assoc($exam_select_api)) {
										echo '<option data-apikey="' . $exam_api_list["apikey"] . '" value="' . $exam_api_list["website"] . '">' . $exam_api_list["website"] . "</option>";
									}
								} else {
									echo '<option selected hidden value="">No Exam API was Installed!</option>';
								}

								?>
							</select>
							<input name="apikey" id="apikey" type="text"
								class="input-box mobile-width-93 system-width-60 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"
								placeholder="User ID:Apikey or Apikey" />
							<input name="update-apikey" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Update API key" />
						</form>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">ENABLE/DISABLE EXAM PIN</span>
						</legend>
						<?php
						$exam_pin_select_api = mysqli_query($conn_server_db, "SELECT pin_name, pin_status FROM " . $exam_pin_table_name);
						if (mysqli_num_rows($exam_pin_select_api) > 0) {
							while ($exam_pin_status_list = mysqli_fetch_assoc($exam_pin_select_api)) {
								;
								$all_pin_status .= $exam_pin_status_list["pin_status"] . ",";
							}
						}

						$exp_all_pin_status = array_filter(explode(",", trim($all_pin_status)));
						class allpin
						{
						}
						$waec = "waec";
						$neco = "neco";
						$nabteb = "nabteb";
						$allpinStatus = new allpin;
						$allpinStatus->$waec = $exp_all_pin_status[0];
						$allpinStatus->$neco = $exp_all_pin_status[1];
						$allpinStatus->$nabteb = $exp_all_pin_status[2];

						$all_pin_status_json = json_encode($allpinStatus, true);
						$decode_all_pin_status_json = json_decode($all_pin_status_json, true);

						?>
						<form method="post">
							<?php
							if ($pin_update_message == true) {
								?>
								<div id="font-color-1" class="message-box font-size-2"><?php echo $pin_update_message; ?></div>
								<?php
							}
							?>
							<span class="font-size-2 font-family-1"><b>WAEC</b></span>
							<input <?php if ($decode_all_pin_status_json['waec'] == 'active') {
								echo 'checked';
							} ?> value="active"
								name="waec" type="checkbox" class="check-box" /> -
							<span class="font-size-2 font-family-1"><b>NECO</b></span>
							<input <?php if ($decode_all_pin_status_json['neco'] == 'active') {
								echo 'checked';
							} ?> value="active"
								name="neco" type="checkbox" class="check-box" /> -
							<span class="font-size-2 font-family-1"><b>NABTEB</b></span>
							<input <?php if ($decode_all_pin_status_json['nabteb'] == 'active') {
								echo 'checked';
							} ?>
								value="active" name="nabteb" type="checkbox" class="check-box" /> -
							<input name="update-pin" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-87"
								value="Update Pin Settings" />
						</form>
					</fieldset><br>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">CHOOSE API TO RUN</span>
						</legend>
						<form method="post">
							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">WAEC EXAM ROUTE</span><br>
								</legend>
								<?php
								$exam_waec_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $exam_table_name);
								if (mysqli_num_rows($exam_waec_select_api) > 0) {
									while ($exam_api_list = mysqli_fetch_assoc($exam_waec_select_api)) {
										$get_exam_waec_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $exam_pin_running_table_name . " WHERE pin_name='waec'"));
										if ($exam_api_list["website"] !== $get_exam_waec_api_website["website"]) {
											echo $exam_api_list["website"] . ' <input value="' . $exam_api_list["website"] . '" name="waec" type="radio" class="check-box"/><br>';
										} else {
											echo $exam_api_list["website"] . ' <input checked value="' . $exam_api_list["website"] . '" name="waec" type="radio" class="check-box"/><br>';
										}
										$waec_exam_api_discount .= $get_exam_waec_api_website["discount_1"] . "," . $get_exam_waec_api_website["discount_2"] . "," . $get_exam_waec_api_website["discount_3"] . "," . $get_exam_waec_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Exam API was Installed!';
								}

								$waec_exp_exam_api_discount = array_filter(explode(",", trim($waec_exam_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $waec_exp_exam_api_discount[0]; ?>" name="waec-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $waec_exp_exam_api_discount[1]; ?>" name="waec-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $waec_exp_exam_api_discount[2]; ?>" name="waec-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $waec_exp_exam_api_discount[3]; ?>" name="waec-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">NECO EXAM ROUTE</span><br>
								</legend>
								<?php
								$exam_neco_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $exam_table_name);
								if (mysqli_num_rows($exam_neco_select_api) > 0) {
									while ($exam_api_list = mysqli_fetch_assoc($exam_neco_select_api)) {
										$get_exam_neco_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $exam_pin_running_table_name . " WHERE pin_name='neco'"));
										if ($exam_api_list["website"] !== $get_exam_neco_api_website["website"]) {
											echo $exam_api_list["website"] . ' <input value="' . $exam_api_list["website"] . '" name="neco" type="radio" class="check-box"/><br>';
										} else {
											echo $exam_api_list["website"] . ' <input checked value="' . $exam_api_list["website"] . '" name="neco" type="radio" class="check-box"/><br>';
										}
										$neco_exam_api_discount .= $get_exam_neco_api_website["discount_1"] . "," . $get_exam_neco_api_website["discount_2"] . "," . $get_exam_neco_api_website["discount_3"] . "," . $get_exam_neco_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Exam API was Installed!';
								}

								$neco_exp_exam_api_discount = array_filter(explode(",", trim($neco_exam_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $neco_exp_exam_api_discount[0]; ?>" name="neco-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $neco_exp_exam_api_discount[1]; ?>" name="neco-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $neco_exp_exam_api_discount[2]; ?>" name="neco-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $neco_exp_exam_api_discount[3]; ?>" name="neco-discount-4"
										type="text" class="input-box half-half-length" placeholder="API Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
								</fieldset>
							</fieldset>


							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">NABTEB EXAM ROUTE</span><br>
								</legend>
								<?php
								$exam_nabteb_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $exam_table_name);
								if (mysqli_num_rows($exam_nabteb_select_api) > 0) {
									while ($exam_api_list = mysqli_fetch_assoc($exam_nabteb_select_api)) {
										$get_exam_nabteb_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM " . $exam_pin_running_table_name . " WHERE pin_name='nabteb'"));
										if ($exam_api_list["website"] !== $get_exam_nabteb_api_website["website"]) {
											echo $exam_api_list["website"] . ' <input value="' . $exam_api_list["website"] . '" name="nabteb" type="radio" class="check-box"/><br>';
										} else {
											echo $exam_api_list["website"] . ' <input checked value="' . $exam_api_list["website"] . '" name="nabteb" type="radio" class="check-box"/><br>';
										}
										$nabteb_exam_api_discount .= $get_exam_nabteb_api_website["discount_1"] . "," . $get_exam_nabteb_api_website["discount_2"] . "," . $get_exam_nabteb_api_website["discount_3"] . "," . $get_exam_nabteb_api_website["discount_4"] . ",";
									}
								} else {
									echo 'No Exam API was Installed!';
								}

								$nabteb_exp_exam_api_discount = array_filter(explode(",", trim($nabteb_exam_api_discount)));
								?>

								<fieldset>
									<legend>
										<span class="font-size-2 font-family-1">USERS DISCOUNT</span><br>
									</legend>
									<span class="font-size-1 font-family-1">Smart Earner</span> -
									<input value="<?php echo $nabteb_exp_exam_api_discount[0]; ?>" name="nabteb-discount-1"
										type="text" class="input-box half-half-length" placeholder="Smart Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Earner</span> -
									<input value="<?php echo $nabteb_exp_exam_api_discount[1]; ?>" name="nabteb-discount-2"
										type="text" class="input-box half-half-length" placeholder="VIP Earner" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">VIP Vendor</span> -
									<input value="<?php echo $nabteb_exp_exam_api_discount[2]; ?>" name="nabteb-discount-3"
										type="text" class="input-box half-half-length" placeholder="VIP Vendor" /><span
										class="font-size-2 font-family-1">%</span><br>
									<span class="font-size-1 font-family-1">API Earner</span> -
									<input value="<?php echo $nabteb_exp_exam_api_discount[3]; ?>" name="nabteb-discount-4"
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