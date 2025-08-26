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
	<span class="mobile-font-size-14 system-font-size-16">Bulk SMS Integration</span><br>
	<span class="mobile-font-size-12 system-font-size-14">
		<b>Method: HTTP GET => Endpoint:</b><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;"><?php echo $w_host; ?>/api/sms.php</div><br>
		
		<b>Parameters:</b><br>
		<b>token:</b> Your Developer APIKEY<br>
		<b>from:</b> Your Unique SMS Sender ID from your User Bulk SMS page e.g <cite>BeeTech</cite><br>
		<b>to:</b> Phones Numbers to send SMS to e.g <cite>0812423XXXX,0906824XXXX</cite><br>
		<b>message:</b> Message to Send e.g <cite>Hello, This is BeeTech Nig. LTD, We Build Websites from Scratch at Low Price call +2348124232128 for more Info</cite><br>
		<b>date:</b> Datetime to disburse SMS e.g <cite>2022-07-24 11:01:31</cite> (Optional)<br>
		
		<cite>Example: </cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<cite><?php echo $w_host; ?>/api/sms.php?token=gw18ys5tfw&from=BeeTech&to=0812423XXXX,0906824XXXX&message=Hello,+This+Is+BeeTech&date=2022-07-24 11:01:31</cite>
		</div><br>
		<cite>EXPECTED SUCCESSFUL RESPONSE</cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<?php echo json_encode(array("code"=>200,"ref"=>"576","desc"=>"Transaction Successful"),true); ?>
		</div>
	</span><br>
	<?php
		//GET EACH sms API WEBSITE
		$get_smsserver_sms_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, price_1, price_2, price_3, price_4 FROM sms_network_running_api WHERE network_name='smsserver'"));
	?>
	<div class="scrollable-div">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Smart Earner (N)</th><th>VIP Earner (N)</th><th>VIP Vendor (N)</th><th>Agent/API User (N)</th>
			</tr>
			
			<?php
				echo '<tr>
					<td>'.$get_smsserver_sms_running_api["price_1"].'</td><td>'.$get_smsserver_sms_running_api["price_2"].'</td><td>'.$get_smsserver_sms_running_api["price_3"].'</td><td>'.$get_smsserver_sms_running_api["price_4"].'</td>
				</tr>';
			?>
		</table>
	</div>
</div>
</center>

<?php include("./../include/footer-html.php"); ?>
</body>
</html>