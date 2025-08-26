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
	<span class="mobile-font-size-14 system-font-size-16">Wallet Balance Integration</span><br>
	<span class="mobile-font-size-12 system-font-size-14">
		<b>Method: HTTP GET => Endpoint:</b><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;"><?php echo $w_host; ?>/api/balance.php</div><br>
		
		<b>Parameters:</b><br>
		<b>token:</b> Your Developer APIKEY<br>
		
		<cite>Example: </cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<cite><?php echo $w_host; ?>/api/balance.php?token=gw18ys5tfw</cite>
		</div><br>
		<cite>EXPECTED SUCCESSFUL RESPONSE</cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
		<?php echo json_encode(array("code"=>200,"balance"=>"20.53"),true); ?>
		</div>
	</span>
</div>
</center>

<?php include("./../include/footer-html.php"); ?>
</body>
</html>