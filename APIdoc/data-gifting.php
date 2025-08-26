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
	<span class="mobile-font-size-14 system-font-size-16">Data Gifting Integration</span><br>
	<span class="mobile-font-size-12 system-font-size-14">
		<b>Method: HTTP GET => Endpoint:</b><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;"><?php echo $w_host; ?>/api/data-gifting.php</div><br>
		
		<b>Parameters:</b><br>
		<b>token:</b> Your Developer APIKEY<br>
		<b>network:</b> Service Provider e.g <cite>mtn,airtel,glo,9mobile</cite><br>
		<b>qty:</b>Data quantity e.g <cite>500mb,1gb,3gb</cite><br>
		<b>phone_number:</b> Phone Number<cite></cite><br>
		
		<cite>Example: </cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<cite><?php echo $w_host; ?>/api/data-gifting.php?token=gw18ys5tfw&network=mtn&qty=1gb&phone_number=0906824XXXXXX</cite>
		</div><br>
		<cite>EXPECTED SUCCESSFUL RESPONSE</cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<?php echo json_encode(array("code"=>200,"ref"=>"4742972327833389934","desc"=>"Transaction Successful"),true); ?>
		</div>
	</span><br>
	<?php
		//GET ALL GIFTING DATA API QTY, PRICE
		$get_all_mtn_gifting_data_network_qty_price = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM mtn_gifting_data_network_qty_price");
		$get_all_airtel_gifting_data_network_qty_price = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM airtel_gifting_data_network_qty_price");
		$get_all_glo_gifting_data_network_qty_price = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM glo_gifting_data_network_qty_price");
		$get_all_9mobile_gifting_data_network_qty_price = mysqli_query($conn_server_db, "SELECT gifting_data_qty, gifting_data_price_1, gifting_data_price_2, gifting_data_price_3, gifting_data_price_4 FROM etisalat_gifting_data_network_qty_price");
		
	?>
	<div class="scrollable-div">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Network</th><th>Data Qty</th><th>Plan Code(qty)</th><th>Smart Earner (%)</th><th>VIP Earner (%)</th><th>VIP Vendor (%)</th><th>Agent/API User (%)</th>
			</tr>
			
			<?php
			
			if(mysqli_num_rows($get_all_mtn_gifting_data_network_qty_price) > 0){
				while($mtn_gifting_data_qty = mysqli_fetch_assoc($get_all_mtn_gifting_data_network_qty_price)){
					echo '<tr>
						<td>MTN</td><td>'.$mtn_gifting_data_qty["gifting_data_qty"].'</td><td>'.$mtn_gifting_data_qty["gifting_data_qty"].'</td><td>'.$mtn_gifting_data_qty["gifting_data_price_1"].'</td><td>'.$mtn_gifting_data_qty["gifting_data_price_2"].'</td><td>'.$mtn_gifting_data_qty["gifting_data_price_3"].'</td><td>'.$mtn_gifting_data_qty["gifting_data_price_4"].'</td>
					</tr>';
				}
			}
			?>
			
			<?php
			
			if(mysqli_num_rows($get_all_airtel_gifting_data_network_qty_price) > 0){
				while($airtel_gifting_data_qty = mysqli_fetch_assoc($get_all_airtel_gifting_data_network_qty_price)){
					echo '<tr>
						<td>Airtel</td><td>'.$airtel_gifting_data_qty["gifting_data_qty"].'</td><td>'.$airtel_gifting_data_qty["gifting_data_qty"].'</td><td>'.$airtel_gifting_data_qty["gifting_data_price_1"].'</td><td>'.$airtel_gifting_data_qty["gifting_data_price_2"].'</td><td>'.$airtel_gifting_data_qty["gifting_data_price_3"].'</td><td>'.$airtel_gifting_data_qty["gifting_data_price_4"].'</td>
					</tr>';
				}
			}
			?>
			
			<?php
			
			if(mysqli_num_rows($get_all_glo_gifting_data_network_qty_price) > 0){
				while($glo_gifting_data_qty = mysqli_fetch_assoc($get_all_glo_gifting_data_network_qty_price)){
					echo '<tr>
						<td>GLO</td><td>'.$glo_gifting_data_qty["gifting_data_qty"].'</td><td>'.$glo_gifting_data_qty["gifting_data_qty"].'</td><td>'.$glo_gifting_data_qty["gifting_data_price_1"].'</td><td>'.$glo_gifting_data_qty["gifting_data_price_2"].'</td><td>'.$glo_gifting_data_qty["gifting_data_price_3"].'</td><td>'.$glo_gifting_data_qty["gifting_data_price_4"].'</td>
					</tr>';
				}
			}
			?>
			
			<?php
			
			if(mysqli_num_rows($get_all_9mobile_gifting_data_network_qty_price) > 0){
				while($etisalat_gifting_data_qty = mysqli_fetch_assoc($get_all_9mobile_gifting_data_network_qty_price)){
					echo '<tr>
						<td>Etisalat</td><td>'.$etisalat_gifting_data_qty["gifting_data_qty"].'</td><td>'.$etisalat_gifting_data_qty["gifting_data_qty"].'</td><td>'.$etisalat_gifting_data_qty["gifting_data_price_1"].'</td><td>'.$etisalat_gifting_data_qty["gifting_data_price_2"].'</td><td>'.$etisalat_gifting_data_qty["gifting_data_price_3"].'</td><td>'.$etisalat_gifting_data_qty["gifting_data_price_4"].'</td>
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