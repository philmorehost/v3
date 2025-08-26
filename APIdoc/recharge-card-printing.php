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
	<span class="mobile-font-size-14 system-font-size-16">Recharge Card Integration</span><br>
	<span class="mobile-font-size-12 system-font-size-14">
		<b>Method: HTTP GET => Endpoint:</b><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;"><?php echo $w_host; ?>/api/rechargecard.php</div><br>
		
		<b>Parameters:</b><br>
		<b>token:</b> Your Developer APIKEY<br>
		<b>network:</b> service provider e.g <cite>mtn,airtel,glo,9mobile</cite><br>
		<b>amount:</b> Amount of Recharge Card to buy<br>
		<b>qty:</b> Quantity of Recharge Card e.g 3<br>
		
		<cite>Example: </cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<cite><?php echo $w_host; ?>/api/rechargecard.php?token=gw18ys5tfw&network=mtn&amount=100&qty=4</cite>
		</div><br>
		<cite>EXPECTED SUCCESSFUL RESPONSE</cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
		<?php echo json_encode(array("code"=>200,"ref"=>144266267,"desc"=>"Transaction Successful", "card"=>array(array("pin"=>83978477565656456,"serialno"=>9785765768478647),array("pin"=>8397887565656456,"serialno"=>9785765768478647))),true); ?>
		</div>
	</span><br>
	<?php
		//GET EACH rechargecard API DISCOUNT
		$get_mtn_rechargecard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM rechargecard_network_running_api WHERE network_name='mtn'"));
		$get_airtel_rechargecard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM rechargecard_network_running_api WHERE network_name='airtel'"));
		$get_glo_rechargecard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM rechargecard_network_running_api WHERE network_name='glo'"));
		$get_9mobile_rechargecard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM rechargecard_network_running_api WHERE network_name='9mobile'"));
	?>
	<div class="scrollable-div">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Network</th><th>Smart Earner (%)</th><th>VIP Earner (%)</th><th>VIP Vendor (%)</th><th>Agent/API User (%)</th>
			</tr>
			<tr>
				<td>MTN</td><td><?php echo $get_mtn_rechargecard_running_api["discount_1"]; ?></td><td><?php echo $get_mtn_rechargecard_running_api["discount_2"]; ?></td><td><?php echo $get_mtn_rechargecard_running_api["discount_3"]; ?></td><td><?php echo $get_mtn_rechargecard_running_api["discount_4"]; ?></td>
			</tr>
			<tr>
				<td>Airtel</td><td><?php echo $get_airtel_rechargecard_running_api["discount_1"]; ?></td><td><?php echo $get_airtel_rechargecard_running_api["discount_2"]; ?></td><td><?php echo $get_airtel_rechargecard_running_api["discount_3"]; ?></td><td><?php echo $get_airtel_rechargecard_running_api["discount_4"]; ?></td>
			</tr>
			<tr>
				<td>GLO</td><td><?php echo $get_glo_rechargecard_running_api["discount_1"]; ?></td><td><?php echo $get_glo_rechargecard_running_api["discount_2"]; ?></td><td><?php echo $get_glo_rechargecard_running_api["discount_3"]; ?></td><td><?php echo $get_glo_rechargecard_running_api["discount_4"]; ?></td>
			</tr>
			<tr>
				<td>9mobile</td><td><?php echo $get_9mobile_rechargecard_running_api["discount_1"]; ?></td><td><?php echo $get_9mobile_rechargecard_running_api["discount_2"]; ?></td><td><?php echo $get_9mobile_rechargecard_running_api["discount_3"]; ?></td><td><?php echo $get_9mobile_rechargecard_running_api["discount_4"]; ?></td>
			</tr>
			
		</table>
	</div>
</div>
</center>

<?php include("./../include/footer-html.php"); ?>
</body>
</html>