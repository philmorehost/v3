<?php session_start();
	include("./../include/config.php");
	if(isset($_SESSION["user"])){
		include(__DIR__."/include/user-details.php");
	}
	
	//GET USER DETAILS
	if(isset($_SESSION["user"])){
		$user_session = $_SESSION["user"];
		$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<title>All Poducts/Services Pricing Page</title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; " />
<meta name="theme-color" content="skyblue" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="/css/site.css">
<script src="/scripts/auth.js"></script>
</head>
<body>
<?php include("./../include/header-html.php"); ?>

<center>
<div class="container-box bg-4 mobile-width-85 system-width-95 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
  <span class="font-size-2 font-family-1"><b>ALL PRICES AND DISCOUNTS ARE DIFFER FROM ONE VENDOR LEVEL TO THE OTHER.</b></span></div><br>

	<div class="container-box bg-4 mobile-width-85 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
	<span class="font-size-2 font-family-1"><b>Airtime Prices Discount in Percentages.</b></span><br>
		<?php
			//GET EACH AIRTIME API DISCOUNT
			$get_mtn_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='mtn'"));
			$get_airtel_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='airtel'"));
			$get_glo_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='glo'"));
			$get_9mobile_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='9mobile'"));
		?>
		<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
			<table class="table-style-1 table-font-size-1">
				<tr>
					<th>Network</th><th>Smart Earner (%)</th><th>VIP Earner (%)</th><th>VIP Vendor (%)</th><th>Agent/API User (%)</th>
				</tr>
				<tr>
					<td>MTN</td><td><?php echo $get_mtn_airtime_running_api["discount_1"]; ?>%</td><td><?php echo $get_mtn_airtime_running_api["discount_2"]; ?>%</td><td><?php echo $get_mtn_airtime_running_api["discount_3"]; ?>%</td><td><?php echo $get_mtn_airtime_running_api["discount_4"]; ?>%</td>
				</tr>
				<tr>
					<td>Airtel</td><td><?php echo $get_airtel_airtime_running_api["discount_1"]; ?>%</td><td><?php echo $get_airtel_airtime_running_api["discount_2"]; ?>%</td><td><?php echo $get_airtel_airtime_running_api["discount_3"]; ?>%</td><td><?php echo $get_airtel_airtime_running_api["discount_4"]; ?>%</td>
				</tr>
				<tr>
					<td>GLO</td><td><?php echo $get_glo_airtime_running_api["discount_1"]; ?>%</td><td><?php echo $get_glo_airtime_running_api["discount_2"]; ?>%</td><td><?php echo $get_glo_airtime_running_api["discount_3"]; ?>%</td><td><?php echo $get_glo_airtime_running_api["discount_4"]; ?>%</td>
				</tr>
				<tr>
					<td>9mobile</td><td><?php echo $get_9mobile_airtime_running_api["discount_1"]; ?>%</td><td><?php echo $get_9mobile_airtime_running_api["discount_2"]; ?>%</td><td><?php echo $get_9mobile_airtime_running_api["discount_3"]; ?>%</td><td><?php echo $get_9mobile_airtime_running_api["discount_4"]; ?>%</td>
				</tr>
				
			</table>
		</div>
	</div><br>
  <div class="container-box bg-4 mobile-width-85 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
    <span class="font-size-2 font-family-1"><b>SME Data Prices</b></span><br>

	<?php
		//GET ALL SME DATA API QTY, PRICE
		$get_all_mtn_sme_data_network_qty_price = mysqli_query($conn_server_db, "SELECT sme_data_qty, sme_data_price_1, sme_data_price_2, sme_data_price_3, sme_data_price_4 FROM mtn_sme_data_network_qty_price");
		$get_all_airtel_sme_data_network_qty_price = mysqli_query($conn_server_db, "SELECT sme_data_qty, sme_data_price_1, sme_data_price_2, sme_data_price_3, sme_data_price_4 FROM airtel_sme_data_network_qty_price");
	?>
	<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Network</th><th>Data Qty</th><th>Smart Earner (N)</th><th>VIP Earner (N)</th><th>VIP Vendor (N)</th><th>Agent/API User (N)</th>
			</tr>
			
			<?php
			
			if(mysqli_num_rows($get_all_mtn_sme_data_network_qty_price) > 0){
				while($mtn_sme_data_qty = mysqli_fetch_assoc($get_all_mtn_sme_data_network_qty_price)){
					echo '<tr>
						<td>MTN SME</td><td>'.$mtn_sme_data_qty["sme_data_qty"].'</td><td>N'.$mtn_sme_data_qty["sme_data_price_1"].'</td><td>N'.$mtn_sme_data_qty["sme_data_price_2"].'</td><td>N'.$mtn_sme_data_qty["sme_data_price_3"].'</td><td>N'.$mtn_sme_data_qty["sme_data_price_4"].'</td>
					</tr>';
				}
			}
			?>
			
			<?php
			
			if(mysqli_num_rows($get_all_airtel_sme_data_network_qty_price) > 0){
				while($airtel_sme_data_qty = mysqli_fetch_assoc($get_all_airtel_sme_data_network_qty_price)){
					echo '<tr>
						<td>Airtel SME</td><td>'.$airtel_sme_data_qty["sme_data_qty"].'</td><td>N'.$airtel_sme_data_qty["sme_data_price_1"].'</td><td>N'.$airtel_sme_data_qty["sme_data_price_2"].'</td><td>N'.$airtel_sme_data_qty["sme_data_price_3"].'</td><td>N'.$airtel_sme_data_qty["sme_data_price_4"].'</td>
					</tr>';
				}
			}
			?>
			
		</table>
	</div>
</div>
  <br>
  
  <div class="container-box bg-4 mobile-width-85 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
    <span class="font-size-2 font-family-1"><b>Corporate Gifting Data Prices</b></span><br>
	<?php
		//GET ALL GIFTING DATA API QTY, PRICE
		$get_all_mtn_gifting_data_network_qty_price = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM mtn_gifting_data_network_qty_price");
		$get_all_airtel_gifting_data_network_qty_price = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM airtel_gifting_data_network_qty_price");
		$get_all_glo_gifting_data_network_qty_price = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM glo_gifting_data_network_qty_price");
		$get_all_9mobile_gifting_data_network_qty_price = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM etisalat_gifting_data_network_qty_price");
		
	?>
	<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Network</th><th>Data Qty</th><th>Smart Earner (N)</th><th>VIP Earner (N)</th><th>VIP Vendor (N)</th><th>Agent/API User (N)</th>
			</tr>
			
			<?php
			
			if(mysqli_num_rows($get_all_mtn_gifting_data_network_qty_price) > 0){
				while($mtn_gifting_data_qty = mysqli_fetch_assoc($get_all_mtn_gifting_data_network_qty_price)){
					echo '<tr>
						<td>MTN</td><td>'.$mtn_gifting_data_qty["gifting_data_qty"].'</td><td>N'.$mtn_gifting_data_qty["gifting_data_price_1"].'</td><td>N'.$mtn_gifting_data_qty["gifting_data_price_2"].'</td><td>N'.$mtn_gifting_data_qty["gifting_data_price_3"].'</td><td>N'.$mtn_gifting_data_qty["gifting_data_price_4"].'</td>
					</tr>';
				}
			}
			?>
			
			<?php
			
			if(mysqli_num_rows($get_all_airtel_gifting_data_network_qty_price) > 0){
				while($airtel_gifting_data_qty = mysqli_fetch_assoc($get_all_airtel_gifting_data_network_qty_price)){
					echo '<tr>
						<td>Airtel</td><td>'.$airtel_gifting_data_qty["gifting_data_qty"].'</td><td>N'.$airtel_gifting_data_qty["gifting_data_price_1"].'</td><td>N'.$airtel_gifting_data_qty["gifting_data_price_2"].'</td><td>N'.$airtel_gifting_data_qty["gifting_data_price_3"].'</td><td>N'.$airtel_gifting_data_qty["gifting_data_price_4"].'</td>
					</tr>';
				}
			}
			?>
			
			<?php
			
			if(mysqli_num_rows($get_all_glo_gifting_data_network_qty_price) > 0){
				while($glo_gifting_data_qty = mysqli_fetch_assoc($get_all_glo_gifting_data_network_qty_price)){
					echo '<tr>
						<td>GLO</td><td>'.$glo_gifting_data_qty["gifting_data_qty"].'</td><td>N'.$glo_gifting_data_qty["gifting_data_price_1"].'</td><td>N'.$glo_gifting_data_qty["gifting_data_price_2"].'</td><td>N'.$glo_gifting_data_qty["gifting_data_price_3"].'</td><td>N'.$glo_gifting_data_qty["gifting_data_price_4"].'</td>
					</tr>';
				}
			}
			?>
			
			<?php
			
			if(mysqli_num_rows($get_all_9mobile_gifting_data_network_qty_price) > 0){
				while($etisalat_gifting_data_qty = mysqli_fetch_assoc($get_all_9mobile_gifting_data_network_qty_price)){
					echo '<tr>
						<td>Etisalat</td><td>'.$etisalat_gifting_data_qty["gifting_data_qty"].'</td><td>N'.$etisalat_gifting_data_qty["gifting_data_price_1"].'</td><td>N'.$etisalat_gifting_data_qty["gifting_data_price_2"].'</td><td>N'.$etisalat_gifting_data_qty["gifting_data_price_3"].'</td><td>N'.$etisalat_gifting_data_qty["gifting_data_price_4"].'</td>
					</tr>';
				}
			}
			?>
			
		</table>
	</div>
</div>
  <br>
  
  <div class="container-box bg-4 mobile-width-85 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
    <span class="font-size-2 font-family-1"><b>Direct Data Prices</b></span><br>
	<?php
		//GET EACH direct_data API WEBSITE
		$get_mtn_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='mtn'"));
		$get_airtel_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='airtel'"));
		$get_glo_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='glo'"));
		$get_9mobile_direct_data_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM direct_data_network_running_api WHERE network_name='9mobile'"));
		
		$mtn_smartrecharge_1 = array("mtn_20gb_30_days"=>"6000","mtn_110gb_30days"=>"20000","mtn_2gb_30_days"=>"1200","mtn_40gb"=>"10000","mtn_75gb_30days"=>"15000","mtn_15gb_30_days"=>"5000","mtn_25mb_24hrs"=>"50","mtn_3gb_30days"=>"1500","mtn_120gb_60days"=>"30000","mtn_150gb_90_days"=>"50000","mtn_75mb_24hrs"=>"100","mtn_1gb_24hrs"=>"300","mtn_200mb_2days"=>"200","mtn_2gb_2days"=>"500","mtn_350mb_7days"=>"300","mtn_1gb_7days"=>"500","mtn_6gb_30days"=>"2500","mtn_15gb_30days"=>"1000","mtn_75gb_60days"=>"20000","mtn_250gb_90days"=>"75000","mtn_400gb_365days"=>"120000","mtn_1000gb_365days"=>"250000","mtn_2000gb_365days"=>"450000","mtn_45gb_30days"=>"2000","mtn_6gb_7_days"=>"1500","mtn_10gb_30days"=>"3500","mtn_750mb_14days"=>"500","mtn_2_5gb_2days"=>"500","mtn_8gb_30days"=>"3000");
		$mtn_benzoni_2 = array("mtn_3gb30days"=>"1500","mtn_6gb30days"=>"2500","mtn_2_5gb2days"=>"500","mtn_1_5gb_30days"=>"1000","mtn_2gb_30days"=>"1200","mtn_4_5gb_30days"=>"2000","mtn_10gb_30days"=>"3500","mtn_15gb30days"=>"5000","mtn_75gb30days"=>"15000","mtn_75gb60days"=>"20000","mtn_750mb_14days"=>"500","mtn_40gb30days"=>"10000","mtn_120gb_60days"=>"30000","mtn_8gb30days"=>"3000","mtn_20gb30days"=>"6000","mtn_110gb30days"=>"20000","mtn_30gb60days"=>"8000");
		$mtn_intersect = array_intersect($mtn_smartrecharge_1,$mtn_benzoni_2);
		
		$airtel_smartrecharge_1 = array("airtel_1_5gb"=>"1000","airtel_3gb_30days"=>"1500","airtel_6gb_7days"=>"1500","airtel_4_5gb_30days"=>"2000","airtel_110gb_30days"=>"20000","airtel_750mb"=>"500","airtel_75mb10_extra_24hrs"=>"100","airtel_200mb_3days"=>"200","airtel_350mb__10_extra_7days"=>"300","airtel_40gb_30days"=>"10000","airtel_8gb_30days"=>"3000","airtel_11gb_30days"=>"4000","airtel_75gb_30days"=>"15000","airtel_1gb__1day"=>"300","airtel_2gb__2days"=>"500","airtel_2gb__30days"=>"1200","airtel_6gb__30days"=>"2500","airtel_15gb"=>"5000");
		$airtel_benzoni_2 = array("airtel_1_5gb30days"=>"1000","airtel_15gb30days"=>"5000","airtel_40gb30days"=>"10000","airtel_6gb30days"=>"2500","airtel_8gb30days"=>"3000","airtel_11gb30days"=>"4000","airtel_4_5gb30days"=>"2000","airtel_750mb14days"=>"500","airtel_2gb30days"=>"1200","airtel_75gb30days"=>"15000","airtel_110gb30days"=>"20000");
		$airtel_intersect = array_intersect($airtel_smartrecharge_1,$airtel_benzoni_2);
		
		$glo_smartrecharge_1 = array("glo_2gb_2days"=>"500","glo_100mb_1_day"=>"100","glo_350mb_2_days"=>"200","glo_1_35gb_14days"=>"500","glo_2_5gb"=>"1000","glo_5_8_gb"=>"2000","glo_7_7_gb"=>"2500","glo_10gb"=>"3000","glo_13_5_gb"=>"4000","glo_1825gb"=>"5000","glo_295gb"=>"8000","glo_50gb"=>"10000","glo_93gb"=>"15000","glo_119gb"=>"18000","glo_50mb_1_day"=>"50","glo_138gb"=>"20000","glo_3_75gb"=>"1500","glo_special_1_gb_special1day"=>"200","glo__7_gb_special7days"=>"1500","glo__3_58_gb_oneoff30days"=>"1500","glo_225gb30days"=>"30000","glo_300gb30days"=>"36000","glo_425gb90days"=>"50000","glo_525gb90days"=>"60000","glo_675gb120days"=>"75000","glo_1024gb365days"=>"100000");
		$glo_benzoni_2 = array("glo_2_5gb30days"=>"1000","glo_5_8gb30days"=>"2000","glo_7_7gb30days"=>"2500","glo_10gbdays"=>"3000","glo_13_25gb30days"=>"4000","glo_18_25gb30days"=>"5000","glo_50gb30days"=>"10000","glo_93gb30days"=>"15000","glo_119gb30days"=>"18000","glo_138gb30days"=>"20000","glo_29_5gb30days"=>"8000","glo_4_1gb30days"=>"1500","glo_1_05gb14days"=>"500");
		$glo_intersect = array_intersect($glo_smartrecharge_1,$glo_benzoni_2);
		
		$etisalat_smartrecharge_1 = array("9mobile_15gb_30days"=>"5000","9mobile_40_gb_30_days"=>"10000","9mobile_75_gb_30_days"=>"15000","9mobile_7gb_7_days"=>"1500","9mobile_120gb_365_days"=>"110000","9mobile_100mb_24hrs"=>"100","9mobile_1_5gb_30_days"=>"1000","9mobile_3gb_30_days"=>"1500","9mobile_2gb_30days"=>"1200","9mobile_100gb_100_days"=>"84992","9mobile_60gb_180_days"=>"55000","9mobile_500mb_30days"=>"500","9mobile_4_5gb_30_days"=>"2000","9mobile_30gb_90_days"=>"27500","9mobile_650mb_24hrs"=>"200","9mobile_25mb_24_hrs"=>"50","9mobile_11gb_30days"=>"4000");
		$etisalat_benzoni_2 = array("9mobile_2gb30days"=>"1200","9mobile_4_5gb30days"=>"2000","9mobile_11gb30days"=>"4000","9mobile_75gb30days"=>"15000","9mobile_500mb30days"=>"500","9mobile_1_5gb30days"=>"1000","9mobile_40gb30days"=>"10000","9mobile_3gb30days"=>"1500",);
		$etisalat_intersect = array_intersect($etisalat_smartrecharge_1,$etisalat_benzoni_2);
		
	?>
	<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Network</th><th>Data Qty</th><th>Smart Earner (N)</th><th>VIP Earner (N)</th><th>VIP Vendor (N)</th><th>Agent/API User (N)</th>
			</tr>
			
			<?php
			
			if(count($mtn_intersect) > 0){
				foreach($mtn_intersect as $key => $mtn_default_price){
					echo '<tr>
						<td>MTN</td><td>'.ucwords(str_replace("_"," ",$key)).'</td><td>N'.($mtn_default_price-($mtn_default_price*($get_mtn_direct_data_running_api["discount_1"]/100))).'</td><td>N'.($mtn_default_price-($mtn_default_price*($get_mtn_direct_data_running_api["discount_2"]/100))).'</td><td>N'.($mtn_default_price-($mtn_default_price*($get_mtn_direct_data_running_api["discount_3"]/100))).'</td><td>N'.($mtn_default_price-($mtn_default_price*($get_mtn_direct_data_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			}
			?>
			
			<?php
			
			if(count($airtel_intersect) > 0){
				foreach($airtel_intersect as $key => $airtel_default_price){
					echo '<tr>
						<td>Airtel</td><td>'.ucwords(str_replace("_"," ",$key)).'</td><td>N'.($airtel_default_price-($airtel_default_price*($get_airtel_direct_data_running_api["discount_1"]/100))).'</td><td>N'.($airtel_default_price-($airtel_default_price*($get_airtel_direct_data_running_api["discount_2"]/100))).'</td><td>N'.($airtel_default_price-($airtel_default_price*($get_airtel_direct_data_running_api["discount_3"]/100))).'</td><td>N'.($airtel_default_price-($airtel_default_price*($get_airtel_direct_data_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			}
			?>
			
			<?php
			
			if(count($glo_intersect) > 0){
				foreach($glo_intersect as $key => $glo_default_price){
					echo '<tr>
						<td>GLO</td><td>'.ucwords(str_replace("_"," ",$key)).'</td><td>N'.($glo_default_price-($glo_default_price*($get_glo_direct_data_running_api["discount_1"]/100))).'</td><td>N'.($glo_default_price-($glo_default_price*($get_glo_direct_data_running_api["discount_2"]/100))).'</td><td>N'.($glo_default_price-($glo_default_price*($get_glo_direct_data_running_api["discount_3"]/100))).'</td><td>N'.($glo_default_price-($glo_default_price*($get_glo_direct_data_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			}
			?>
			
			<?php
			
			if(count($etisalat_intersect) > 0){
				foreach($etisalat_intersect as $key => $etisalat_default_price){
					echo '<tr>
						<td>9mobile</td><td>'.ucwords(str_replace("_"," ",$key)).'</td><td>N'.($etisalat_default_price-($etisalat_default_price*($get_9mobile_direct_data_running_api["discount_1"]/100))).'</td><td>N'.($etisalat_default_price-($etisalat_default_price*($get_9mobile_direct_data_running_api["discount_2"]/100))).'</td><td>N'.($etisalat_default_price-($etisalat_default_price*($get_9mobile_direct_data_running_api["discount_3"]/100))).'</td><td>N'.($etisalat_default_price-($etisalat_default_price*($get_9mobile_direct_data_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			}
			?>
			
		</table>
	</div>
</div>
  
  <br>
  <div class="container-box bg-4 mobile-width-85 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
    <span class="font-size-2 font-family-1"><b>Bulk SMS Prices</b></span><br>


	<?php
		//GET EACH sms API WEBSITE
		$get_smsserver_sms_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, price_1, price_2, price_3, price_4 FROM sms_network_running_api WHERE network_name='smsserver'"));
	?>
	<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Smart Earner (N)</th><th>VIP Earner (N)</th><th>VIP Vendor (N)</th><th>Agent/API User (N)</th>
			</tr>
			
			<?php
				echo '<tr>
					<td>N'.$get_smsserver_sms_running_api["price_1"].'</td><td>N'.$get_smsserver_sms_running_api["price_2"].'</td><td>N'.$get_smsserver_sms_running_api["price_3"].'</td><td>N'.$get_smsserver_sms_running_api["price_4"].'</td>
				</tr>';
			?>
		</table>
	</div>
  </div>

  
  <br>
  
  
  <div class="container-box bg-4 mobile-width-85 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
    <span class="font-size-2 font-family-1"><b>Cable TV Subscription Prices</b></span><br>
	
	<?php
		//GET ALL CABLE API PACKAGE, PRICE
					$startimes_package_price = array("nova"=>"900","basic"=>"1850","smart"=>"2600","classic"=>"2750","unique"=>"3900","super"=>"4900");
					$dstv_package_price = array("padi"=>"2150","yanga"=>"2950","confam"=>"5300","compact"=>"9000","premium"=>"21000","asia"=>"7100","padi__extraview"=>"5050","yanga__extraview"=>"5850","confam__extraview"=>"8200","compact__extra_view"=>"11900","compact_plus"=>"14250","compact__asia__extraview"=>"19000","compact_plus__extra_view"=>"17150","compact_plus__frenchplus__extra_view"=>"26450","compact_plus__asia__extraview"=>"24250","premium__extra_view"=>"23900","premium__asia__extra_view"=>"31000","premium__french__extra_view"=>"28000");
					$gotv_package_price = array("smallie"=>"900","jinja"=>"1900","jolli"=>"2800","max"=>"4150","super"=>"5600");
		
		
		//GET EACH cable API WEBSITE
		$get_startimes_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='startimes'"));
		$get_dstv_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='dstv'"));
		$get_gotv_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='gotv'"));
	?>
	<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Cable</th><th>Package</th><th>Smart Earner (N)</th><th>VIP Earner (N)</th><th>VIP Vendor (N)</th><th>Agent Vendor (N)</th>
			</tr>
			
			<?php
				foreach($startimes_package_price as $key => $startimes_price){
					echo '<tr>
						<td>Startimes</td><td>'.ucwords(str_replace(["_"]," ",$key)).' Bouquet</td><td>N'.($startimes_price-($startimes_price*($get_startimes_cable_running_api["discount_1"]/100))).'</td><td>N'.($startimes_price-($startimes_price*($get_startimes_cable_running_api["discount_2"]/100))).'</td><td>N'.($startimes_price-($startimes_price*($get_startimes_cable_running_api["discount_3"]/100))).'</td><td>N'.($startimes_price-($startimes_price*($get_startimes_cable_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			?>
			
			<?php
				foreach($dstv_package_price as $key => $dstv_price){
					echo '<tr>
						<td>DStv</td><td>'.ucwords(str_replace(["_"]," ",$key)).' Package</td><td>N'.($dstv_price-($dstv_price*($get_dstv_cable_running_api["discount_1"]/100))).'</td><td>N'.($dstv_price-($dstv_price*($get_dstv_cable_running_api["discount_2"]/100))).'</td><td>N'.($dstv_price-($dstv_price*($get_dstv_cable_running_api["discount_3"]/100))).'</td><td>N'.($dstv_price-($dstv_price*($get_dstv_cable_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			?>
			
			<?php
				foreach($gotv_package_price as $key => $gotv_price){
					echo '<tr>
						<td>GOtv</td><td>'.ucwords(str_replace(["_"]," ",$key)).' Package</td><td>N'.($gotv_price-($gotv_price*($get_gotv_cable_running_api["discount_1"]/100))).'</td><td>N'.($gotv_price-($gotv_price*($get_gotv_cable_running_api["discount_2"]/100))).'</td><td>N'.($gotv_price-($gotv_price*($get_gotv_cable_running_api["discount_3"]/100))).'</td><td>N'.($gotv_price-($gotv_price*($get_gotv_cable_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			?>
			
		</table>
	</div>
</div>

  <br>
  
  <div class="container-box bg-4 mobile-width-85 system-width-95 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
    <span class="font-size-2 font-family-1"><b>PHCN Electricity Discount in Percentages</b></span><br>
	
	<?php
		//GET ALL ELECTRICITY API DISCOUNT
		$get_all_electricity_subscription_running_api = mysqli_query($conn_server_db, "SELECT subscription_name, discount_1, discount_2, discount_3, discount_4 FROM electricity_subscription_running_api");
	?>
	<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Disco Company</th><th>Smart Earner (%)</th><th>VIP Earner (%)</th><th>VIP Vendor (%)</th><th>Agent/API User (%)</th>
			</tr>
			
			<?php
			
			if(mysqli_num_rows($get_all_electricity_subscription_running_api) > 0){
				while($electricity = mysqli_fetch_assoc($get_all_electricity_subscription_running_api)){
					echo '<tr>
						<td>'.$electricity["subscription_name"].'</td><td>'.$electricity["discount_1"].'%</td><td>'.$electricity["discount_2"].'%</td><td>'.$electricity["discount_3"].'%</td><td>'.$electricity["discount_4"].'%</td>
					</tr>';
				}
			}
			?>
			
		</table>
	</div>
</div>
</center>

<?php include("./../include/footer-html.php"); ?>
</body>
</html>