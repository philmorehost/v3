<?php session_start();
	if(!isset($_SESSION["user"])){
		header("Location: /login.php");
	}else{
		include("./../include/config.php");
		include("./../include/user-details.php");
	}
	
	//GET USER DETAILS
	$user_session = $_SESSION["user"];
	$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
	
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
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
<div style="text-align: left; overflow: auto;" class="container-box color-8 bg-4 mobile-width-90 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-2 system-padding-top-2 mobile-padding-right-2 system-padding-right-2 mobile-padding-left-2 system-padding-left-2 mobile-padding-bottom-2 system-padding-bottom-2">
	<a style="text-decoration: none;" href="/documentation.php"><img style="float: left;" class="" src="/images/back-arrow.svg" /></a><br><br>
	<span class="mobile-font-size-14 system-font-size-16">Cable Subscription Integration</span><br>
	<span class="mobile-font-size-12 system-font-size-14">
		<b>Method: HTTP GET => Endpoint:</b><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;"><?php echo $w_host; ?>/api/cable.php</div><br>
		
		<b>Parameters:</b><br>
		<b>token:</b> Your Developer APIKEY<br>
		<b>tv:</b> Cable Type/Provider e.g <cite>startimes,dstv,gotv</cite><br>
		<b>package:</b>Package Name e.g <cite>nova,smallie,classic</cite><br>
		<b>smartcard_number:</b> Smart card IUC No e.g <cite>0214579XXXX</cite><br>
		
		<cite>Example: </cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<cite><?php echo $w_host; ?>/api/cable.php?token=gw18ys5tfw&tv=startimes&package=basic&smartcard_number=0214579XXXX</cite>
		</div><br>
		<cite>EXPECTED SUCCESSFUL RESPONSE</cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<?php echo json_encode(array("code"=>200,"ref"=>"4742972327833389934","desc"=>"Transaction Successful"),true); ?>
		</div><br>
		
		<cite>Example: </cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<cite><?php echo $w_host; ?>/api/cable.php?token=gw18ys5tfw&tv=startimes&package=basic&smartcard_number=0214579XXXX&task=verify</cite>
		</div><br>
		<cite>EXPECTED SUCCESSFUL RESPONSE</cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<?php echo json_encode(array("code"=>200,"name"=>"Abdulrahaman Habeebullahi","desc"=>"Verification Successful"),true); ?>
		</div><br>
	</span><br>
	<?php
		//GET ALL CABLE API PACKAGE, PRICE
		$startimes_package_price = array("nova"=>"1500","basic"=>"2600","smart"=>"3500","classic"=>"3800","super"=>"6500");
		$dstv_package_price = array("padi"=>"2500","yanga"=>"3500","confam"=>"6200","compact"=>"10500","compact_plus"=>"16600","premium"=>"24500","padi__extraview"=>"6000","yanga__extraview"=>"6900","confam__extraview"=>"9600","compact__extra_view"=>"13900","compact_plus__extra_view"=>"20000","premium__extra_view"=>"27900");
		$gotv_package_price = array("smallie"=>"1100","jinja"=>"2250","jolli"=>"3300","max"=>"4850","super"=>"6400");
		
		
		//GET EACH cable API WEBSITE
		$get_startimes_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='startimes'"));
		$get_dstv_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='dstv'"));
		$get_gotv_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='gotv'"));
	?>
	<div class="scrollable-div">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Cable</th><th>Package</th><th>Plan Code(qty)</th><th>Smart Earner (N)</th><th>VIP Earner (N)</th><th>VIP Vendor (N)</th><th>Agent Vendor (N)</th>
			</tr>
			
			<?php
				foreach($startimes_package_price as $key => $startimes_price){
					echo '<tr>
						<td>Startimes</td><td>'.ucwords(str_replace(["_"]," ",$key)).' Bouquet</td><td>'.$key.'</td><td>'.($startimes_price-($startimes_price*($get_startimes_cable_running_api["discount_1"]/100))).'</td><td>'.($startimes_price-($startimes_price*($get_startimes_cable_running_api["discount_2"]/100))).'</td><td>'.($startimes_price-($startimes_price*($get_startimes_cable_running_api["discount_3"]/100))).'</td><td>'.($startimes_price-($startimes_price*($get_startimes_cable_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			?>
			
			<?php
				foreach($dstv_package_price as $key => $dstv_price){
					echo '<tr>
						<td>DStv</td><td>'.ucwords(str_replace(["_"]," ",$key)).' Package</td><td>'.$key.'</td><td>'.($dstv_price-($dstv_price*($get_dstv_cable_running_api["discount_1"]/100))).'</td><td>'.($dstv_price-($dstv_price*($get_dstv_cable_running_api["discount_2"]/100))).'</td><td>'.($dstv_price-($dstv_price*($get_dstv_cable_running_api["discount_3"]/100))).'</td><td>'.($dstv_price-($dstv_price*($get_dstv_cable_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			?>
			
			<?php
				foreach($gotv_package_price as $key => $gotv_price){
					echo '<tr>
						<td>GOtv</td><td>'.ucwords(str_replace(["_"]," ",$key)).' Package</td><td>'.$key.'</td><td>'.($gotv_price-($gotv_price*($get_gotv_cable_running_api["discount_1"]/100))).'</td><td>'.($gotv_price-($gotv_price*($get_gotv_cable_running_api["discount_2"]/100))).'</td><td>'.($gotv_price-($gotv_price*($get_gotv_cable_running_api["discount_3"]/100))).'</td><td>'.($gotv_price-($gotv_price*($get_gotv_cable_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			?>
			
		</table>
	</div>
</div>
</center>

<?php include("./../include/footer-html.php"); ?>
</body>
</html>