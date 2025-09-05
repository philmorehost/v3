<?php session_start();
if (!isset($_SESSION["admin"])) {
	header("Location: /admin/login.php");
} else {
	include("../include/admin-config.php");
	include("../include/admin-details.php");
}

if ($conn_server_db == true) {
	$gifting_data_table_name = "gifting_data_api";
	$gifting_data_apikey_db_table = "CREATE TABLE IF NOT EXISTS " . $gifting_data_table_name . "(website VARCHAR(225) NOT NULL, apikey VARCHAR(225))";
	if (mysqli_query($conn_server_db, $gifting_data_apikey_db_table) == true) {
	}


	$gifting_data_select_running_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $gifting_data_table_name . " LIMIT 1");
	if (mysqli_num_rows($gifting_data_select_running_api) > 0) {
		while ($gifting_data_api_running_list = mysqli_fetch_assoc($gifting_data_select_running_api)) {
			$first_gifting_data_api_website_row = $gifting_data_api_running_list["website"];
			$gifting_data_network_running_table_name = "gifting_data_network_running_api";
			$gifting_data_network_running_db_table = "CREATE TABLE IF NOT EXISTS " . $gifting_data_network_running_table_name . "(network_name VARCHAR(30) NOT NULL, website VARCHAR(225) NOT NULL)";
			if (mysqli_query($conn_server_db, $gifting_data_network_running_db_table) == true) {
				if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $gifting_data_network_running_table_name)) == 0) {
					$insert_gifting_data_network_running_api = "INSERT INTO " . $gifting_data_network_running_table_name . " (network_name, website) VALUES ('mtn', '$first_gifting_data_api_website_row');";
					$insert_gifting_data_network_running_api .= "INSERT INTO " . $gifting_data_network_running_table_name . " (network_name, website) VALUES ('airtel', '$first_gifting_data_api_website_row');";
					$insert_gifting_data_network_running_api .= "INSERT INTO " . $gifting_data_network_running_table_name . " (network_name, website) VALUES ('glo', '$first_gifting_data_api_website_row');";
					$insert_gifting_data_network_running_api .= "INSERT INTO " . $gifting_data_network_running_table_name . " (network_name, website) VALUES ('9mobile', '$first_gifting_data_api_website_row')";
					if (mysqli_multi_query($conn_server_db, $insert_gifting_data_network_running_api) == true) {

					}
				}
			}
		}
	}


	$gifting_data_network_table_name = "gifting_data_network_status";
	$gifting_data_network_db_table = "CREATE TABLE IF NOT EXISTS " . $gifting_data_network_table_name . "(network_name VARCHAR(30) NOT NULL, network_status VARCHAR(30) NOT NULL)";
	if (mysqli_query($conn_server_db, $gifting_data_network_db_table) == true) {
		if (mysqli_num_rows(mysqli_query($conn_server_db, "SELECT * FROM " . $gifting_data_network_table_name)) == 0) {
			$insert_network_status = "INSERT INTO " . $gifting_data_network_table_name . " (network_name, network_status) VALUES ('mtn', 'active');";
			$insert_network_status .= "INSERT INTO " . $gifting_data_network_table_name . " (network_name, network_status) VALUES ('airtel', 'active');";
			$insert_network_status .= "INSERT INTO " . $gifting_data_network_table_name . " (network_name, network_status) VALUES ('glo', 'active');";
			$insert_network_status .= "INSERT INTO " . $gifting_data_network_table_name . " (network_name, network_status) VALUES ('9mobile', 'active')";

			if (mysqli_multi_query($conn_server_db, $insert_network_status) == true) {

			}
		}
	}

	$mtn_gifting_data_network_price_table_name = "mtn_gifting_data_network_qty_price";
	$mtn_gifting_data_network_price_db_table = "CREATE TABLE IF NOT EXISTS " . $mtn_gifting_data_network_price_table_name . "(gifting_data_qty VARCHAR(30) NOT NULL, gifting_data_price_1 INT NOT NULL, gifting_data_price_2 INT NOT NULL, gifting_data_price_3 INT NOT NULL, gifting_data_price_4 INT NOT NULL)";
	if (mysqli_query($conn_server_db, $mtn_gifting_data_network_price_db_table) == true) {
	}

	$airtel_gifting_data_network_price_table_name = "airtel_gifting_data_network_qty_price";
	$airtel_gifting_data_network_price_db_table = "CREATE TABLE IF NOT EXISTS " . $airtel_gifting_data_network_price_table_name . "(gifting_data_qty VARCHAR(30) NOT NULL, gifting_data_price_1 INT NOT NULL, gifting_data_price_2 INT NOT NULL, gifting_data_price_3 INT NOT NULL, gifting_data_price_4 INT NOT NULL)";
	if (mysqli_query($conn_server_db, $airtel_gifting_data_network_price_db_table) == true) {
	}

	$glo_gifting_data_network_price_table_name = "glo_gifting_data_network_qty_price";
	$glo_gifting_data_network_price_db_table = "CREATE TABLE IF NOT EXISTS " . $glo_gifting_data_network_price_table_name . "(gifting_data_qty VARCHAR(30) NOT NULL, gifting_data_price_1 INT NOT NULL, gifting_data_price_2 INT NOT NULL, gifting_data_price_3 INT NOT NULL, gifting_data_price_4 INT NOT NULL)";
	if (mysqli_query($conn_server_db, $glo_gifting_data_network_price_db_table) == true) {
	}

	$etisalat_gifting_data_network_price_table_name = "etisalat_gifting_data_network_qty_price";
	$etisalat_gifting_data_network_price_db_table = "CREATE TABLE IF NOT EXISTS " . $etisalat_gifting_data_network_price_table_name . "(gifting_data_qty VARCHAR(30) NOT NULL, gifting_data_price_1 INT NOT NULL, gifting_data_price_2 INT NOT NULL, gifting_data_price_3 INT NOT NULL, gifting_data_price_4 INT NOT NULL)";
	if (mysqli_query($conn_server_db, $etisalat_gifting_data_network_price_db_table) == true) {
	}

}

if (isset($_POST["install-api"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$gifting_data_api_website = "INSERT INTO " . $gifting_data_table_name . " (website) VALUES ('$api_name')";

	$check_gifting_data_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $gifting_data_table_name . " WHERE website='$api_name'");

	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_gifting_data_select_api) == 0) {
			if (mysqli_query($conn_server_db, $gifting_data_api_website) == true) {
				$add_api_message = ucwords($api_name) . " Gifting Data API installed successfully! ";
			}
		} else {
			$add_api_message = ucwords($api_name) . " Gifting Data API has already been Installed! ";
		}
	}

}

if (isset($_POST["update-apikey"])) {
	$api_name = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["api-name"]));
	$apikey = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["apikey"]));

	$gifting_data_api_website = "UPDATE " . $gifting_data_table_name . " SET apikey='$apikey' WHERE website='$api_name'";
	$check_gifting_data_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $gifting_data_table_name . " WHERE website='$api_name'");
	if (!empty(trim($api_name))) {
		if (mysqli_num_rows($check_gifting_data_select_api) > 0) {
			if (mysqli_query($conn_server_db, $gifting_data_api_website) == true) {
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



	$update_mtn_network_status = "UPDATE " . $gifting_data_network_table_name . " SET network_status='$mtn_status' WHERE network_name='mtn'";
	$update_airtel_network_status = "UPDATE " . $gifting_data_network_table_name . " SET network_status='$airtel_status' WHERE network_name='airtel'";
	$update_glo_network_status = "UPDATE " . $gifting_data_network_table_name . " SET network_status='$glo_status' WHERE network_name='glo'";
	$update_etisalat_network_status = "UPDATE " . $gifting_data_network_table_name . " SET network_status='$etisalat_status' WHERE network_name='9mobile'";

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

	$mtn_network_table_injection = "UPDATE " . $gifting_data_network_running_table_name . " SET website='$mtn_website' WHERE network_name='mtn'";
	if (mysqli_query($conn_server_db, $mtn_network_table_injection) == true) {
	}

	$airtel_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["airtel"]));

	$airtel_network_table_injection = "UPDATE " . $gifting_data_network_running_table_name . " SET website='$airtel_website' WHERE network_name='airtel'";
	if (mysqli_query($conn_server_db, $airtel_network_table_injection) == true) {
	}

	$glo_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["glo"]));

	$glo_network_table_injection = "UPDATE " . $gifting_data_network_running_table_name . " SET website='$glo_website' WHERE network_name='glo'";
	if (mysqli_query($conn_server_db, $glo_network_table_injection) == true) {
	}

	$etisalat_website = mysqli_real_escape_string($conn_server_db, strip_tags($_POST["9mobile"]));

	$etisalat_network_table_injection = "UPDATE " . $gifting_data_network_running_table_name . " SET website='$etisalat_website' WHERE network_name='9mobile'";
	if (mysqli_query($conn_server_db, $etisalat_network_table_injection) == true) {
	}


}

if (isset($_POST["update-gifting_data-price"])) {
	$mtn_gifting_data_qty_array = $_POST["mtn-qty"];
	$mtn_gifting_data_price_1_array = $_POST["mtn-price-1"];
	$mtn_gifting_data_price_2_array = $_POST["mtn-price-2"];
	$mtn_gifting_data_price_3_array = $_POST["mtn-price-3"];
	$mtn_gifting_data_price_4_array = $_POST["mtn-price-4"];

	if (isset($mtn_gifting_data_qty_array)) {
		foreach ($mtn_gifting_data_qty_array as $key => $mtn_gifting_data_qty) {
			$gifting_data_price_1 = mysqli_real_escape_string($conn_server_db, strip_tags($mtn_gifting_data_price_1_array[$key]));
			$gifting_data_price_2 = mysqli_real_escape_string($conn_server_db, strip_tags($mtn_gifting_data_price_2_array[$key]));
			$gifting_data_price_3 = mysqli_real_escape_string($conn_server_db, strip_tags($mtn_gifting_data_price_3_array[$key]));
			$gifting_data_price_4 = mysqli_real_escape_string($conn_server_db, strip_tags($mtn_gifting_data_price_4_array[$key]));

			$mtn_price_update = "UPDATE " . $mtn_gifting_data_network_price_table_name . " SET gifting_data_price_1='$gifting_data_price_1', gifting_data_price_2='$gifting_data_price_2', gifting_data_price_3='$gifting_data_price_3', gifting_data_price_4='$gifting_data_price_4' WHERE gifting_data_qty='$mtn_gifting_data_qty'";
			if (mysqli_query($conn_server_db, $mtn_price_update) == true) {
			}
		}
	}

	$airtel_gifting_data_qty_array = $_POST["airtel-qty"];
	$airtel_gifting_data_price_1_array = $_POST["airtel-price-1"];
	$airtel_gifting_data_price_2_array = $_POST["airtel-price-2"];
	$airtel_gifting_data_price_3_array = $_POST["airtel-price-3"];
	$airtel_gifting_data_price_4_array = $_POST["airtel-price-4"];

	if (isset($airtel_gifting_data_qty_array)) {
		foreach ($airtel_gifting_data_qty_array as $key => $airtel_gifting_data_qty) {
			$gifting_data_price_1 = mysqli_real_escape_string($conn_server_db, strip_tags($airtel_gifting_data_price_1_array[$key]));
			$gifting_data_price_2 = mysqli_real_escape_string($conn_server_db, strip_tags($airtel_gifting_data_price_2_array[$key]));
			$gifting_data_price_3 = mysqli_real_escape_string($conn_server_db, strip_tags($airtel_gifting_data_price_3_array[$key]));
			$gifting_data_price_4 = mysqli_real_escape_string($conn_server_db, strip_tags($airtel_gifting_data_price_4_array[$key]));

			$airtel_price_update = "UPDATE " . $airtel_gifting_data_network_price_table_name . " SET gifting_data_price_1='$gifting_data_price_1', gifting_data_price_2='$gifting_data_price_2', gifting_data_price_3='$gifting_data_price_3', gifting_data_price_4='$gifting_data_price_4' WHERE gifting_data_qty='$airtel_gifting_data_qty'";
			if (mysqli_query($conn_server_db, $airtel_price_update) == true) {
			}
		}
	}

	$glo_gifting_data_qty_array = $_POST["glo-qty"];
	$glo_gifting_data_price_1_array = $_POST["glo-price-1"];
	$glo_gifting_data_price_2_array = $_POST["glo-price-2"];
	$glo_gifting_data_price_3_array = $_POST["glo-price-3"];
	$glo_gifting_data_price_4_array = $_POST["glo-price-4"];

	if (isset($glo_gifting_data_qty_array)) {
		foreach ($glo_gifting_data_qty_array as $key => $glo_gifting_data_qty) {
			$gifting_data_price_1 = mysqli_real_escape_string($conn_server_db, strip_tags($glo_gifting_data_price_1_array[$key]));
			$gifting_data_price_2 = mysqli_real_escape_string($conn_server_db, strip_tags($glo_gifting_data_price_2_array[$key]));
			$gifting_data_price_3 = mysqli_real_escape_string($conn_server_db, strip_tags($glo_gifting_data_price_3_array[$key]));
			$gifting_data_price_4 = mysqli_real_escape_string($conn_server_db, strip_tags($glo_gifting_data_price_4_array[$key]));

			$glo_price_update = "UPDATE " . $glo_gifting_data_network_price_table_name . " SET gifting_data_price_1='$gifting_data_price_1', gifting_data_price_2='$gifting_data_price_2', gifting_data_price_3='$gifting_data_price_3', gifting_data_price_4='$gifting_data_price_4' WHERE gifting_data_qty='$glo_gifting_data_qty'";
			if (mysqli_query($conn_server_db, $glo_price_update) == true) {
			}
		}
	}

	$etisalat_gifting_data_qty_array = $_POST["9mobile-qty"];
	$etisalat_gifting_data_price_1_array = $_POST["9mobile-price-1"];
	$etisalat_gifting_data_price_2_array = $_POST["9mobile-price-2"];
	$etisalat_gifting_data_price_3_array = $_POST["9mobile-price-3"];
	$etisalat_gifting_data_price_4_array = $_POST["9mobile-price-4"];

	if (isset($etisalat_gifting_data_qty_array)) {
		foreach ($etisalat_gifting_data_qty_array as $key => $etisalat_gifting_data_qty) {
			$gifting_data_price_1 = mysqli_real_escape_string($conn_server_db, strip_tags($etisalat_gifting_data_price_1_array[$key]));
			$gifting_data_price_2 = mysqli_real_escape_string($conn_server_db, strip_tags($etisalat_gifting_data_price_2_array[$key]));
			$gifting_data_price_3 = mysqli_real_escape_string($conn_server_db, strip_tags($etisalat_gifting_data_price_3_array[$key]));
			$gifting_data_price_4 = mysqli_real_escape_string($conn_server_db, strip_tags($etisalat_gifting_data_price_4_array[$key]));

			$etisalat_price_update = "UPDATE " . $etisalat_gifting_data_network_price_table_name . " SET gifting_data_price_1='$gifting_data_price_1', gifting_data_price_2='$gifting_data_price_2', gifting_data_price_3='$gifting_data_price_3', gifting_data_price_4='$gifting_data_price_4' WHERE gifting_data_qty='$etisalat_gifting_data_qty'";
			if (mysqli_query($conn_server_db, $etisalat_price_update) == true) {
			}
		}
	}
}


$mtn_gifting_array = array("50mb" => "", "150mb" => "", "250mb" => "", "500mb" => "208", "1gb" => "209", "2gb" => "210", "3gb" => "211", "5gb" => "212", "10gb" => "220", "15gb" => "221", "20gb" => "222");
$airtel_gifting_array = array("300mb" => "290", "500mb" => "246", "1gb" => "213", "2gb" => "214", "5gb" => "215", "10gb" => "291");
$glo_gifting_array = array("200mb" => "", "500mb" => "", "1gb" => "", "2gb" => "", "3gb" => "", "5gb" => "", "10gb" => "");
$etisalat_gifting_array = array("500mb" => "308", "1gb" => "309", "2gb" => "311", "3gb" => "312", "5gb" => "314", "10gb" => "315", "15gb" => "317", "20gb" => "318", "40gb" => "321", "50gb" => "322", "75gb" => "323", "100gb" => "324");

foreach ($mtn_gifting_array as $qty => $id) {
	$check_if_gifting_data_qty_exists = mysqli_query($conn_server_db, "SELECT gifting_data_qty FROM " . $mtn_gifting_data_network_price_table_name . " WHERE gifting_data_qty='$qty'");
	if (mysqli_num_rows($check_if_gifting_data_qty_exists) == 0) {
		$insert_gifting_data_qty_price = "INSERT INTO " . $mtn_gifting_data_network_price_table_name . " (gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4) VALUES ('$qty', '1', '1', '1', '1')";
		if (mysqli_query($conn_server_db, $insert_gifting_data_qty_price) == true) {
		}
	}
}

foreach ($airtel_gifting_array as $qty => $id) {
	$check_if_gifting_data_qty_exists = mysqli_query($conn_server_db, "SELECT gifting_data_qty FROM " . $airtel_gifting_data_network_price_table_name . " WHERE gifting_data_qty='$qty'");
	if (mysqli_num_rows($check_if_gifting_data_qty_exists) == 0) {
		$insert_gifting_data_qty_price = "INSERT INTO " . $airtel_gifting_data_network_price_table_name . " (gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4) VALUES ('$qty', '1', '1', '1', '1')";
		if (mysqli_query($conn_server_db, $insert_gifting_data_qty_price) == true) {
		}
	}
}


foreach ($glo_gifting_array as $qty => $id) {
	$check_if_gifting_data_qty_exists = mysqli_query($conn_server_db, "SELECT gifting_data_qty FROM " . $glo_gifting_data_network_price_table_name . " WHERE gifting_data_qty='$qty'");
	if (mysqli_num_rows($check_if_gifting_data_qty_exists) == 0) {
		$insert_gifting_data_qty_price = "INSERT INTO " . $glo_gifting_data_network_price_table_name . " (gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4) VALUES ('$qty', '1', '1', '1', '1')";
		if (mysqli_query($conn_server_db, $insert_gifting_data_qty_price) == true) {
		}
	}
}


foreach ($etisalat_gifting_array as $qty => $id) {
	$check_if_gifting_data_qty_exists = mysqli_query($conn_server_db, "SELECT gifting_data_qty FROM " . $etisalat_gifting_data_network_price_table_name . " WHERE gifting_data_qty='$qty'");
	if (mysqli_num_rows($check_if_gifting_data_qty_exists) == 0) {
		$insert_gifting_data_qty_price = "INSERT INTO " . $etisalat_gifting_data_network_price_table_name . " (gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4) VALUES ('$qty', '1', '1', '1', '1')";
		if (mysqli_query($conn_server_db, $insert_gifting_data_qty_price) == true) {
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
					<span class="font-size-2 font-family-1">INSTALL GIFTING DATA API</span>
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
						<option disabled hidden selected>Install Gifting Data API</option>
						<option value="datagifting.com.ng">datagifting.com.ng</option>
						<option value="rpidatang.com">rpidatang.com</option>
						<option value="subvtu.com">subvtu.com</option>
						<option value="smartrecharge.ng">smartrecharge.ng</option>
						<option value="smartrechargeapi.com">smartrechargeapi.com</option>
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
			$check_count_gifting_data_table_name = mysqli_query($conn_server_db, "SELECT website FROM " . $gifting_data_table_name);
			if ($check_count_gifting_data_table_name == true) {
				if (mysqli_num_rows($check_count_gifting_data_table_name) > 0) {

					?>

					<fieldset>
						<legend>
							<span class="font-size-2 font-family-1">UPDATE GIFTING DATA API KEY</span>
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
								$gifting_data_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $gifting_data_table_name);
								if (mysqli_num_rows($gifting_data_select_api) > 0) {
									while ($gifting_data_api_list = mysqli_fetch_assoc($gifting_data_select_api)) {
										echo '<option gifting_data-apikey="' . $gifting_data_api_list["apikey"] . '" value="' . $gifting_data_api_list["website"] . '">' . $gifting_data_api_list["website"] . "</option>";
									}
								} else {
									echo '<option selected hidden value="">No Gifting Data API was Installed!</option>';
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
							<span class="font-size-2 font-family-1">ENABLE/DISABLE GIFTING DATA NETWORK</span>
						</legend>
						<?php
						$gifting_data_network_select_api = mysqli_query($conn_server_db, "SELECT network_name, network_status FROM " . $gifting_data_network_table_name);
						if (mysqli_num_rows($gifting_data_network_select_api) > 0) {
							while ($gifting_data_network_status_list = mysqli_fetch_assoc($gifting_data_network_select_api)) {
								;
								$all_network_status .= $gifting_data_network_status_list["network_status"] . ",";
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
									<span class="font-size-2 font-family-1">MTN GIFTING DATA ROUTE</span><br>
								</legend>
								<?php
								$gifting_data_mtn_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $gifting_data_table_name);
								if (mysqli_num_rows($gifting_data_mtn_select_api) > 0) {
									while ($gifting_data_api_list = mysqli_fetch_assoc($gifting_data_mtn_select_api)) {
										$get_gifting_data_mtn_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM " . $gifting_data_network_running_table_name . " WHERE network_name='mtn'"));
										if ($gifting_data_api_list["website"] !== $get_gifting_data_mtn_api_website["website"]) {
											echo $gifting_data_api_list["website"] . ' <input value="' . $gifting_data_api_list["website"] . '" name="mtn" type="radio" class="check-box"/><br>';
										} else {
											echo $gifting_data_api_list["website"] . ' <input checked value="' . $gifting_data_api_list["website"] . '" name="mtn" type="radio" class="check-box"/><br>';
										}
									}
								} else {
									echo 'No Gifting Data API was Installed!';
								}

								?>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">AIRTEL GIFTING DATA ROUTE</span><br>
								</legend>
								<?php
								$gifting_data_airtel_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $gifting_data_table_name);
								if (mysqli_num_rows($gifting_data_airtel_select_api) > 0) {
									while ($gifting_data_api_list = mysqli_fetch_assoc($gifting_data_airtel_select_api)) {
										$get_gifting_data_airtel_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM " . $gifting_data_network_running_table_name . " WHERE network_name='airtel'"));
										if ($gifting_data_api_list["website"] !== $get_gifting_data_airtel_api_website["website"]) {
											echo $gifting_data_api_list["website"] . ' <input value="' . $gifting_data_api_list["website"] . '" name="airtel" type="radio" class="check-box"/><br>';
										} else {
											echo $gifting_data_api_list["website"] . ' <input checked value="' . $gifting_data_api_list["website"] . '" name="airtel" type="radio" class="check-box"/><br>';
										}
									}
								} else {
									echo 'No Gifting Data API was Installed!';
								}

								?>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">GLO GIFTING DATA ROUTE</span><br>
								</legend>
								<?php
								$gifting_data_glo_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $gifting_data_table_name);
								if (mysqli_num_rows($gifting_data_glo_select_api) > 0) {
									while ($gifting_data_api_list = mysqli_fetch_assoc($gifting_data_glo_select_api)) {
										$get_gifting_data_glo_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM " . $gifting_data_network_running_table_name . " WHERE network_name='glo'"));
										if ($gifting_data_api_list["website"] !== $get_gifting_data_glo_api_website["website"]) {
											echo $gifting_data_api_list["website"] . ' <input value="' . $gifting_data_api_list["website"] . '" name="glo" type="radio" class="check-box"/><br>';
										} else {
											echo $gifting_data_api_list["website"] . ' <input checked value="' . $gifting_data_api_list["website"] . '" name="glo" type="radio" class="check-box"/><br>';
										}
									}
								} else {
									echo 'No Gifting Data API was Installed!';
								}

								?>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1">9MOBILE GIFTING DATA ROUTE</span><br>
								</legend>
								<?php
								$gifting_data_9mobile_select_api = mysqli_query($conn_server_db, "SELECT website, apikey FROM " . $gifting_data_table_name);
								if (mysqli_num_rows($gifting_data_9mobile_select_api) > 0) {
									while ($gifting_data_api_list = mysqli_fetch_assoc($gifting_data_9mobile_select_api)) {
										$get_gifting_data_9mobile_api_website = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website FROM " . $gifting_data_network_running_table_name . " WHERE network_name='9mobile'"));
										if ($gifting_data_api_list["website"] !== $get_gifting_data_9mobile_api_website["website"]) {
											echo $gifting_data_api_list["website"] . ' <input value="' . $gifting_data_api_list["website"] . '" name="9mobile" type="radio" class="check-box"/><br>';
										} else {
											echo $gifting_data_api_list["website"] . ' <input checked value="' . $gifting_data_api_list["website"] . '" name="9mobile" type="radio" class="check-box"/><br>';
										}
									}
								} else {
									echo 'No Gifting Data API was Installed!';
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
							<span class="font-size-2 font-family-1">GIFTING DATA QUALITY & PRICE</span>
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
										$mtn_gifting_data_qty_price_select = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM " . $mtn_gifting_data_network_price_table_name);
										if (mysqli_num_rows($mtn_gifting_data_qty_price_select) > 0) {
											while ($gifting_data_qty_price_list = mysqli_fetch_assoc($mtn_gifting_data_qty_price_select)) {

												echo '<tr>
					<td><b>' . $gifting_data_qty_price_list["gifting_data_qty"] . '<input hidden value="' . $gifting_data_qty_price_list["gifting_data_qty"] . '" name="mtn-qty[]" type="text" placeholder="Qty"/></b></td>
					<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_1"] . '" name="mtn-price-1[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_2"] . '" name="mtn-price-2[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_3"] . '" name="mtn-price-3[]" type="number" class="input-box half-length" placeholder="Price"/></td>
					<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_4"] . '" name="mtn-price-4[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				</tr>';

											}
										} else {
											echo 'No Gifting Data Quantity was Created!';
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
										$airtel_gifting_data_qty_price_select = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM " . $airtel_gifting_data_network_price_table_name);
										if (mysqli_num_rows($airtel_gifting_data_qty_price_select) > 0) {
											while ($gifting_data_qty_price_list = mysqli_fetch_assoc($airtel_gifting_data_qty_price_select)) {

												echo '<tr>
				<td><b>' . $gifting_data_qty_price_list["gifting_data_qty"] . '<input hidden value="' . $gifting_data_qty_price_list["gifting_data_qty"] . '" name="airtel-qty[]" type="text" placeholder="Qty"/></b></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_1"] . '" name="airtel-price-1[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_2"] . '" name="airtel-price-2[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_3"] . '" name="airtel-price-3[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_4"] . '" name="airtel-price-4[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				</tr>';

											}
										} else {
											echo 'No Gifting Data Quantity was Created!';
										}

										?>
									</tbody>
								</table>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1"><b>GLO PRICE</b></span><br>
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
										$glo_gifting_data_qty_price_select = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM " . $glo_gifting_data_network_price_table_name);
										if (mysqli_num_rows($glo_gifting_data_qty_price_select) > 0) {
											while ($gifting_data_qty_price_list = mysqli_fetch_assoc($glo_gifting_data_qty_price_select)) {

												echo '<tr>
				<td><b>' . $gifting_data_qty_price_list["gifting_data_qty"] . '<input hidden value="' . $gifting_data_qty_price_list["gifting_data_qty"] . '" name="glo-qty[]" type="text" placeholder="Qty"/></b></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_1"] . '" name="glo-price-1[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_2"] . '" name="glo-price-2[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_3"] . '" name="glo-price-3[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_4"] . '" name="glo-price-4[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				</tr>';

											}
										} else {
											echo 'No Gifting Data Quantity was Created!';
										}

										?>
									</tbody>
								</table>
							</fieldset>

							<fieldset>
								<legend>
									<span class="font-size-2 font-family-1"><b>9mobile (Etisalat) PRICE</b></span><br>
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
										$etisalat_gifting_data_qty_price_select = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM " . $etisalat_gifting_data_network_price_table_name);
										if (mysqli_num_rows($etisalat_gifting_data_qty_price_select) > 0) {
											while ($gifting_data_qty_price_list = mysqli_fetch_assoc($etisalat_gifting_data_qty_price_select)) {

												echo '<tr>
				<td><b>' . $gifting_data_qty_price_list["gifting_data_qty"] . '<input hidden value="' . $gifting_data_qty_price_list["gifting_data_qty"] . '" name="9mobile-qty[]" type="text" placeholder="Qty"/></b></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_1"] . '" name="9mobile-price-1[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_2"] . '" name="9mobile-price-2[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_3"] . '" name="9mobile-price-3[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				<td><input value="' . $gifting_data_qty_price_list["gifting_data_price_4"] . '" name="9mobile-price-4[]" type="number" class="input-box half-length" placeholder="Price"/></td>
				</tr>';

											}
										} else {
											echo 'No Gifting Data Quantity was Created!';
										}

										?>
									</tbody>
								</table>
							</fieldset>

							<input name="update-gifting_data-price" type="submit"
								class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-65"
								value="Update gifting Data Settings" />

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
			document.getElementById("apikey").value = apikey.options[apikey.selectedIndex].getAttribute("gifting_data-apikey");
		}
	</script>

	<?php include("../include/admin-footer-html.php"); ?>
</body>

</html>