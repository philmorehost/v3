<?php session_start();
	if(!isset($_SESSION["user"])){
		header("Location: /login.php");
	}else{
		include(__DIR__."/include/config.php");
		include(__DIR__."/include/user-details.php");
	}
	
	if($conn_server_db == true){
		if(mysqli_query($conn_server_db,"CREATE TABLE IF NOT EXISTS user_message (user_alert LONGTEXT NOT NULL, user_static LONGTEXT NOT NULL)") == true){
			$get_userMessage_details = mysqli_query($conn_server_db,"SELECT * FROM user_message");
			if(mysqli_num_rows($get_userMessage_details) == 0){
				if(mysqli_query($conn_server_db,"INSERT INTO user_message (user_alert, user_static) VALUES ('Welcome Back! ','Welcome Back! ')") == true){
					
				}
			}
		}
		
		$get_userMessage_details = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM user_message"));
	}
	
	//GET USER DETAILS
	$user_session = $_SESSION["user"];
	$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
	
	
	
	if(isset($_POST["upgrade-account"])){
		$all_details = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["upgrade-package"]));
		$upgrade_to = array_filter(explode(":",trim($all_details)))[0];
		$amount = str_replace(["-","+","/","*"],"",array_filter(explode(":",trim($all_details)))[1]);
		$site_name = $_SERVER["HTTP_HOST"];
		
		if(!empty($upgrade_to) && !empty($amount)){
		if($all_user_details["wallet_balance"] > $amount){
			$raw_number = "123456789012345678901234567890";
			$reference = substr(str_shuffle($raw_number),0,15);
			$remain_balance = ($all_user_details["wallet_balance"]-$amount);
			if(mysqli_query($conn_server_db,"UPDATE users SET account_type='$upgrade_to', wallet_balance='$remain_balance' WHERE email='$user_session'") == true){
				if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$reference','$amount', '".$all_user_details["wallet_balance"]."', '$remain_balance', 'successful', 'Account Upgrading to ".ucwords(str_replace("_"," ",$upgrade_to))."','account-upgrade', '$site_name')")){
					$get_referee_account = mysqli_query($conn_server_db,"SELECT * FROM users WHERE email='".$all_user_details["referral"]."'");
					if(mysqli_num_rows($get_referee_account) == 1){
						$ref_amount = ($amount*20/100);
						$ref_account_details = mysqli_fetch_assoc($get_referee_account);
						$ref_remain_balance = ($ref_account_details["wallet_balance"]+$ref_amount);
						$ref_reference = substr(str_shuffle($raw_number),0,15);
						if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$ref_remain_balance' WHERE email='".$all_user_details["referral"]."'") == true){
							if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('".$all_user_details["referral"]."','$ref_reference','$ref_amount', '".$ref_account_details["wallet_balance"]."', '$ref_remain_balance', 'successful', 'Referral Upgrade Commission of $user_session','commission', '$site_name')")){
												
							}
						}
					}
					$_SESSION["transaction_upgrade"] = "Account Upgraded Successfully!";
				}
			}
		}else{
			$_SESSION["transaction_upgrade"] = "Insufficient Balance, Upgrade can't continue, Fund wallet and try again! ";
		}
		}else{
			$_SESSION["transaction_upgrade"] = "Upgrade Form Is Empty! Try to select package to Upgrade To";
		}
		
		header("Location: ".$_SERVER["REQUEST_URI"]);
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM site_info WHERE 1"))["sitetitle"]; ?></title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; " />
<meta name="theme-color" content="skyblue" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="/css/site.css">
<script src="/scripts/auth.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>
<body>
<?php include(__DIR__."/include/header-html.php"); ?>

<script type="text/javascript">
		setTimeout(function(){
			alertPopUp(`<?php echo str_replace("\n","<br/>",$get_userMessage_details["user_alert"]); ?>`);
		}, 1000);
</script>

<?php if(isset($_SESSION["transaction_upgrade"])){ ?>
<script type="text/javascript">
	alertPopUp(`<?php echo $_SESSION["transaction_upgrade"]; ?>`);
</script>
<?php } ?>
<script type="text/javascript">
	let balCodes = "<strong>BALANCE CODES</strong>";
	balCodes += "<br><img src='/images/mtn.png' style='border-radius: 10px; float: left; clear: both;' class='mobile-width-10 system-width-10'/>";
	balCodes += "<br><img src='/images/airtel.png' style='border-radius: 10px; float: left; clear: both;' class='mobile-width-10 system-width-10'/>";
	balCodes += "<br><img src='/images/glo.png' style='border-radius: 10px; float: left; clear: both;' class='mobile-width-10 system-width-10'/>";
	balCodes += "<br><img src='/images/9mobile.png' style='border-radius: 10px; float: left; clear: both;' class='mobile-width-10 system-width-10'/>";

</script>
<center>
	<div style="text-align:left;" class="container-box bg-6 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<span class="color-8 mobile-font-size-12 system-font-size-14"><b>Wallet Balance</b></span><br>
		<span style="font-weight: bold;" id="walletBal" class="color-8 mobile-font-size-28 system-font-size-30"></span>
		<img style="cursor: pointer;" onclick="reloadWalletBalance();" id="reload-img" class="mobile-width-6 system-width-3 mobile-margin-right-3 system-margin-right-3" src="/images/reload-icon.svg"/>
		<script>
			//Reload Wallet Balance
			reloadWalletBalance();
			function reloadWalletBalance(){
				var reloadImg = document.getElementById("reload-img");
				
					reloadImg.classList.remove("reload-img");
					reloadImg.classList.add("reload-img");
					setTimeout(function(){
						reloadImg.classList.remove("reload-img");
					},3000);
				setTimeout(function(){
					var httpReloadWalletBalanceText = new XMLHttpRequest();
					httpReloadWalletBalanceText.open("POST","./include/walbal.php");
					httpReloadWalletBalanceText.setRequestHeader("Content-Type","application/json");
					const body = JSON.stringify({
						title: 1
					});
					httpReloadWalletBalanceText.onload = function(){
						if(httpReloadWalletBalanceText.readyState == 4 && httpReloadWalletBalanceText.status == 200){
							document.getElementById("walletBal").innerHTML = "N"+JSON.parse(httpReloadWalletBalanceText.responseText)["balance"];
						}else{
							document.getElementById("walletBal").innerHTML = httpReloadWalletBalanceText.status;
						}
					}
					httpReloadWalletBalanceText.send(body);
				},1000);
			}
		</script>
		<div style="height: auto; display: inline-block; float: var(--system-float-item-right);" class="mobile-width-100 system-width-78">
			<center>
				<a style="text-decoration:none;" href="/fund-wallet.php">
					<button type="button" style="font-weight: bold;" class="button-box color-8 bg-1 mobile-font-size-18 system-font-size-24 mobile-width-35 system-width-35 mobile-height-35 system-height-35 mobile-margin-right-1 system-margin-right-1"><img class="mobile-width-14 system-width-10 mobile-margin-right-3 system-margin-right-3" src="/images/add-fund.svg"/> Fund</button>
				</a>
				<a style="text-decoration:none;" href="/send-money.php?page=user">
					<button type="button" style="font-weight: bold;" class="button-box color-8 bg-3 mobile-font-size-17 system-font-size-22 mobile-width-58 system-width-35 mobile-height-35 system-height-35 mobile-margin-left-1 system-margin-left-1"><img class="mobile-width-7 system-width-9 mobile-margin-right-3 system-margin-right-3" src="/images/share-icon.svg"/> Transfer Fund</button>
				</a>
			</center>
		</div>
	</div><br>
	<div class="container-box bg-10 mobile-width-100 system-width-100 mobile-margin-top-1 system-margin-top-1 system-margin-left-1">
		<center>
		  
			<a style="text-decoration:none;" href="/airtime.php">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_airtime.jpg"/><br>
					Buy Airtime
				</button>
			</a>
			<a style="text-decoration:none;" onclick="openDashboardBtnDataLists();">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_data.jpg"/><br>
					Buy Data
				</button>
			</a>
			<a style="text-decoration:none; display: none" href="/sme-data.php" id="dashboadbtndatalist-1">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_data.jpg"/><br>
					SME Data
				</button>
			</a>
			<a style="text-decoration:none; display: none" href="/direct-data.php" id="dashboadbtndatalist-2">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_data.jpg"/><br>
					Direct Data
				</button>
			</a>
			<a style="text-decoration:none; display: none" href="/data-gifting.php" id="dashboadbtndatalist-3">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_data.jpg"/><br>
					Corporate Data
				</button>
			</a>
			<script>
			function openDashboardBtnDataLists(){
				if(document.getElementById('dashboadbtndatalist-1').style.display == "none"){
					document.getElementById('dashboadbtndatalist-1').style.display = "inline";
					document.getElementById('dashboadbtndatalist-2').style.display = "inline";
					document.getElementById('dashboadbtndatalist-3').style.display = "inline";
				}else{
					document.getElementById('dashboadbtndatalist-1').style.display = "none";
					document.getElementById('dashboadbtndatalist-2').style.display = "none";
					document.getElementById('dashboadbtndatalist-3').style.display = "none";
				}
			}
			</script>

			<a style="text-decoration:none;" href="/cable.php">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_cable.jpg"/><br>
					Cable TV
				</button>
			</a>
			<a style="text-decoration:none;" href="/recharge-card-printing.php">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_print.jpg"/><br>
					Recharge Card
				</button>
			</a>
			<a style="text-decoration:none;" href="/data-card.php">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_print.jpg"/><br>
					Print Data Card
				</button>
			</a>
			<a style="text-decoration:none;" href="/electricity.php">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_electric.jpg"/><br>
					Electricity Bill
				</button>
			</a>
			<a style="text-decoration:none;" href="/sms.php">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_sms.jpg"/><br>
					Bulk SMS
				</button>
			</a>
			<a style="text-decoration:none;" href="/exam.php">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/resultchecker.png"/><br>
					Buy Exam Pin
				</button>
			</a>
          <a style="text-decoration:none;" href="/place-payment-order.php">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/directbanktransfer.png"/><br>
					Submit Payment
				</button>
			</a>
          	<a style="text-decoration:none;" href="/change-password.php">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/reset-pin.png"/><br>
					Reset Pin
				</button>
			</a>
			<a style="text-decoration:none;" href="/transaction.php">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_unknown.jpg"/><br>
					Transactions
				</button>
			</a>
			<a style="text-decoration:none;" onclick="alertPopUp(balCodes)">
				<button type="button" style="font-weight: 400;" class="button-box box-shadow color-9 bg-8 mobile-font-size-17 system-font-size-22 mobile-width-45 system-width-23 mobile-height-15 system-height-15 mobile-margin-right-1 system-margin-right-1 system-height-15 mobile-margin-top-2 system-margin-top-2">
					<img style="height:4rem; object-fit: contain;" class="mobile-width-60 system-width-65" src="/images/dash_unknown.jpg"/><br>
					Balance Code
				</button>
			</a>
		</center>
	</div><br>
	<?php
		$get_admin_details = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM admin WHERE 1"));
		$get_admin_bank_details = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM admin_bank_details WHERE 1"));
	?>
	<div style="text-align:left; display: inline-block;" class="container-box bg-2 mobile-font-size-12 system-font-size-14 mobile-width-85 system-width-39 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		Do you need a vtu business website like this?<br><a style="font-weight: bold; color: inherit;" target="_blank" href='https://wa.me/<?php echo $get_admin_details["phone_number"]; ?>?text=I%20need%20help%20regarding%20vtu%20website%20setup'>Click Here to Get Started</a>
	</div>
	<div style="text-align:left; display: inline-block;" class="container-box bg-7 mobile-font-size-12 system-font-size-14 mobile-width-40 system-width-19 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		30% Refer: <span style="cursor: pointer; text-decoration: underline; font-weight: bold;" onclick="copyReferLink();">Copy Link</span><br>
		<?php
			$get_referrals = mysqli_query($conn_server_db,"SELECT firstname, lastname, email, phone_number, account_status, account_type FROM ".$user_table_name." WHERE referral='$user_session'");
			echo mysqli_num_rows($get_referrals);
		?> People Referred
	</div>
	<script>
	let ReferLink = '<?php echo $w_host."/register.php?ref=".$user_details["email"]; ?>';
	const copyReferLink = async () => {
		try {
		await navigator.clipboard.writeText(ReferLink);
		alert('Content copied to clipboard');
		} catch (err) {
		alert('Failed to copy: ', err);
		}
	}
	</script>
	<div style="text-align:left; display: inline-block;" class="container-box bg-7 mobile-font-size-12 system-font-size-14 mobile-width-38 system-width-18 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		Commission: N
		<?php 
		$wallet_total_commission = mysqli_query($conn_server_db,"SELECT * FROM transaction_history WHERE email='$user_session' AND transaction_type='commission'");
			if(mysqli_num_rows($wallet_total_commission) > 0){
				while($total_commission = mysqli_fetch_assoc($wallet_total_commission)){
					$user_total_commission += $total_commission["amount"];
				}
			}
			echo $user_total_commission;
		?><br>
		Type: 
		<strong>
			<?php
				if($user_details["account_type"] == "smart_earner"){
					$user_account_level = "Smart Earner";
				}
				
				if($user_details["account_type"] == "vip_earner"){
					$user_account_level = "VIP Earner";
				}
				
				if($user_details["account_type"] == "vip_vendor"){
					$user_account_level = "VIP Vendor";
				}
				
				if($user_details["account_type"] == "api_earner"){
					$user_account_level = "Agent Vendor";
				}
				
				echo ucwords($user_account_level);
			?>
		</strong>
	</div><br>
	
	<!--<div style="text-align:left; display: inline-block;" class="container-box bg-4 mobile-font-size-12 system-font-size-14 mobile-width-85 system-width-48 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<strong><?php echo str_replace("\n","<br/>",$get_userMessage_details["user_static"]); ?></strong>
	</div>-->
	<?php
		$wallet_total_funding = mysqli_query($conn_server_db,"SELECT * FROM transaction_history WHERE email='$user_session' AND (transaction_type='wallet-funding' OR transaction_type='credit' OR transaction_type='refunded' OR transaction_type='commission') ");
		if(mysqli_num_rows($wallet_total_funding) > 0){
			while($total_funding = mysqli_fetch_assoc($wallet_total_funding)){
				$user_total_funding += $total_funding["amount"];
			}
		}
		
		$wallet_total_spent = mysqli_query($conn_server_db,"SELECT * FROM transaction_history WHERE email='$user_session'");
		if(mysqli_num_rows($wallet_total_spent) > 0){
			while($total_spent = mysqli_fetch_assoc($wallet_total_spent)){
				if(($total_spent["transaction_type"] !== "wallet-funding") && ($total_spent["transaction_type"] !== "credit") && ($total_spent["transaction_type"] !== "refunded") && ($total_spent["transaction_type"] !== "commission")){
					$user_total_spent += $total_spent["amount"];
				}
			}
		}
	?>
	<div style="text-align:left; display:inline-block;" class="button-box bg-2 mobile-font-size-12 system-font-size-14 mobile-width-85 system-width-45 mobile-margin-top-1 system-margin-top-1 mobile-margin-right-1 system-margin-right-1">
		<strong>Total Funded: N<?php echo $user_total_funding; ?></strong><br>
		<strong>Total Spent: N<?php echo $user_total_spent; ?></strong>
	</div>
	
	

	<div style="text-align:left; display: inline-block; height: auto;" class="container-box bg-2 mobile-font-size-12 system-font-size-14 mobile-width-85 system-width-40 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<span class="font-size-2 font-family-1"><b>Upgrade your Account</b></span><br>
		<form action="" method="post">
		<?php
			$get_vip_earner_upgrade_price = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM upgrade_price WHERE level='vip_earner'"));
			$get_vip_vendor_upgrade_price = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM upgrade_price WHERE level='vip_vendor'"));
			$get_api_earner_upgrade_price = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM upgrade_price WHERE level='api_earner'"));
		?>
			<select name="upgrade-package" id="package" class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-60" required>
				<option disabled hidden selected>Choose Package</option>
				<option	<?php if(($user_details["account_type"] == "vip_earner") OR ($user_details["account_type"] == "vip_vendor") OR ($user_details["account_type"] == "api_earner")){ echo "hidden"; } ?> value="vip_earner:<?php echo $get_vip_earner_upgrade_price['amount']; ?>">VIP Earner @ N<?php echo $get_vip_earner_upgrade_price["amount"]; ?></option>
				<option <?php if(($user_details["account_type"] == "vip_vendor") OR ($user_details["account_type"] == "api_earner")){ echo "hidden"; } ?> value="vip_vendor:<?php echo $get_vip_vendor_upgrade_price['amount']; ?>">VIP Vendor @ N<?php echo $get_vip_vendor_upgrade_price["amount"]; ?></option>
				<option <?php if($user_details["account_type"] == "api_earner"){ echo "hidden"; } ?> value="api_earner:<?php echo $get_api_earner_upgrade_price['amount']; ?>">Agent Vendor @ N<?php echo $get_api_earner_upgrade_price["amount"]; ?></option>
			</select>
			<input name="upgrade-account" type="submit" style="font-weight: bolder;" class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25" value="Upgrade"/>
		</form>
	</div><br>

</center>

<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>