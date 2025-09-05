<?php session_start();
	if(!isset($_SESSION["admin"])){
		header("Location: /admin/login.php");
	}else{
		include("../include/admin-config.php");
		include("../include/admin-details.php");
	}
	include("../include/gateway-apikey.php");
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
</head>
<body>
<?php include("../include/admin-header-html.php"); ?>

<?php
	$betoday = date("Ym").sprintf("%02d",(date("d")));
	if(date("m") !== "12"){
		if(date("d") !== cal_days_in_month(CAL_GREGORIAN,date("m"),date("Y"))){
			$aftoday = date("Ym").sprintf("%02d",(date("d")+1));
		}else{
			$aftoday = date("Y").sprintf("%02d",(date("m")+1))."01";
		}
	}else{
		if(date("d") !== cal_days_in_month(CAL_GREGORIAN,date("m"),date("Y"))){
			$aftoday = sprintf("%02d",(date("Y")+1))."01".sprintf("%02d",(date("d")+1));
		}else{
			$aftoday = sprintf("%02d",(date("Y")+1))."0101";
		}
	}
	
	$todaysales = mysqli_query($conn_server_db, "SELECT * FROM transaction_history WHERE transaction_date BETWEEN '$betoday' AND '$aftoday'");
	if(mysqli_num_rows($todaysales) > 0){
		while($eachsale = mysqli_fetch_assoc($todaysales)){
			if(($eachsale["transaction_type"] !== "wallet-funding") && ($eachsale["transaction_type"] !== "credit") && ($eachsale["transaction_type"] !== "refunded") && ($eachsale["transaction_type"] !== "commission") && (strtolower($eachsale["status"]) !== "failed") && (strtolower($eachsale["status"]) !== "pending")){
				if($eachsale["d_amount"] !== null){
					$todaySalesMoneyDAmount += $eachsale["d_amount"];
				}
				
				if($eachsale["d_amount"] == null){
					$todaySalesMoneyAmount += $eachsale["amount"];
				}
			}
		}
		$todaySalesMoney = $todaySalesMoneyDAmount + $todaySalesMoneyAmount;
	}else{
		$todaySalesMoney = "0.00";
	}
	
	//YEAR(transaction_date) = YEAR(CURRENT_DATE()) AND MONTH(transaction_date) = MONTH(CURRENT_DATE())
	
	$bethisMonth = date("Ym")."01";
	$enthisMonth = date("Ym").cal_days_in_month(CAL_GREGORIAN,date("m"),date("Y"));
	
	$thisMonthsales = mysqli_query($conn_server_db, "SELECT * FROM transaction_history WHERE transaction_date BETWEEN '$bethisMonth' AND '$enthisMonth'");
	if(mysqli_num_rows($thisMonthsales) > 0){
		while($eachsale = mysqli_fetch_assoc($thisMonthsales)){
			if(($eachsale["transaction_type"] !== "wallet-funding") && ($eachsale["transaction_type"] !== "credit") && ($eachsale["transaction_type"] !== "refunded") && ($eachsale["transaction_type"] !== "commission") && (strtolower($eachsale["status"]) !== "failed") && (strtolower($eachsale["status"]) !== "pending")){
				if($eachsale["d_amount"] !== null){
					$thisMonthSalesMoneyDAmount += $eachsale["d_amount"];
				}
				
				if($eachsale["d_amount"] == null){
					$thisMonthSalesMoneyAmount += $eachsale["amount"];
				}
			}
		}
		$thisMonthSalesMoney = $thisMonthSalesMoneyDAmount + $thisMonthSalesMoneyAmount;
	}else{
		$thisMonthSalesMoney = "0.00";
	}

	
	$overallMonthsales = mysqli_query($conn_server_db, "SELECT * FROM transaction_history WHERE transaction_date");
	if(mysqli_num_rows($overallMonthsales) > 0){
		while($eachsale = mysqli_fetch_assoc($overallMonthsales)){
			if(($eachsale["transaction_type"] !== "wallet-funding") && ($eachsale["transaction_type"] !== "credit") && ($eachsale["transaction_type"] !== "refunded") && ($eachsale["transaction_type"] !== "commission") && (strtolower($eachsale["status"]) !== "failed") && (strtolower($eachsale["status"]) !== "pending")){
				if($eachsale["d_amount"] !== null){
					$overallMonthSalesMoneyDAmount += $eachsale["d_amount"];
				}
				
				if($eachsale["d_amount"] == null){
					$overallMonthSalesMoneyAmount += $eachsale["amount"];
				}
			}
		}
		$overallMonthSalesMoney = $overallMonthSalesMoneyDAmount + $overallMonthSalesMoneyAmount;
	}else{
		$overallMonthSalesMoney = "0.00";
	}

	$wallet_total_users_balance = mysqli_query($conn_server_db,"SELECT * FROM users WHERE account_status='active'");
	if(mysqli_num_rows($wallet_total_users_balance) > 0){
		while($total_users_balance = mysqli_fetch_assoc($wallet_total_users_balance)){
			$users_total_balance += $total_users_balance["wallet_balance"];
		}
	}else{
		$users_total_balance = "0.00";
	}

	$monnifyfunding = mysqli_query($conn_server_db, "SELECT * FROM transaction_history WHERE website='monnify.com'");
	if(mysqli_num_rows($monnifyfunding) > 0){
		while($eachmonnifyfund = mysqli_fetch_assoc($monnifyfunding)){
			$monnifyfundingMoney += $eachmonnifyfund["amount"];
		}
	}else{
		$monnifyfundingMoney = "0.00";
	}
	
	$flutterwavefunding = mysqli_query($conn_server_db, "SELECT * FROM transaction_history WHERE website='flutterwave.com'");
	if(mysqli_num_rows($flutterwavefunding) > 0){
		while($eachflutterwavefund = mysqli_fetch_assoc($flutterwavefunding)){
			$flutterwavefundingMoney += $eachflutterwavefund["amount"];
		}
	}else{
		$flutterwavefundingMoney = "0.00";
	}
	
	
	$paystackfunding = mysqli_query($conn_server_db, "SELECT * FROM transaction_history WHERE website='paystack.com'");
	if(mysqli_num_rows($paystackfunding) > 0){
		while($eachpaystackfund = mysqli_fetch_assoc($paystackfunding)){
			$paystackfundingMoney += $eachpaystackfund["amount"];
		}
	}else{
		$paystackfundingMoney = "0.00";
	}
	
	$bankfunding = mysqli_query($conn_server_db, "SELECT * FROM transaction_history WHERE transaction_type='wallet-funding' OR transaction_type='credit'");
	if(mysqli_num_rows($bankfunding) > 0){
		while($eachbankfund = mysqli_fetch_assoc($bankfunding)){
			$bankfundingMoney += $eachbankfund["amount"];
		}
	}else{
		$bankfundingMoney = "0.00";
	}
?>
<center>
<div class="container-box bg-4 mobile-width-95 system-width-95 mobile-margin-top-1 system-margin-left-1 mobile-padding-top-1 system-padding-top-1 mobile-padding-left-1 system-padding-left-1 mobile-padding-right-1 system-padding-right-1 mobile-padding-bottom-1 system-padding-bottom-1">
	<div style="display: inline-block;" class="container-box bg-2 mobile-width-40 system-width-15 mobile-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<span class="mobile-font-size-14 system-font-size-18">
			Total Users <br/>
			<strong>
				<?php
					$get_users = mysqli_query($conn_server_db,"SELECT firstname, lastname, email, phone_number, account_status, account_type FROM ".$user_table_name);
					echo mysqli_num_rows($get_users);
				?>
			</strong>
		</span>
	</div>

	<div style="display: inline-block;" class="container-box bg-2 mobile-width-40 system-width-18 mobile-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<span class="mobile-font-size-14 system-font-size-18">
			Sales Today <br/>
			<strong>N<?php echo $todaySalesMoney; ?></strong>
		</span>
	</div>

	<div style="display: inline-block;" class="container-box bg-2 mobile-width-40 system-width-19 mobile-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<span class="mobile-font-size-14 system-font-size-18">
			Sales This Month <br/>
			<strong>N<?php echo $thisMonthSalesMoney; ?></strong>
		</span>
	</div>

	<div style="display: inline-block;" class="container-box bg-2 mobile-width-40 system-width-20 mobile-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<span class="mobile-font-size-14 system-font-size-18">
			Sales All Time <br/>
			<strong>N<?php echo $overallMonthSalesMoney; ?></strong>
		</span>
	</div><br>

	<!--<span class="mobile-font-size-14 system-font-size-20">Total Users: 
		<b><?php
			$get_users = mysqli_query($conn_server_db,"SELECT firstname, lastname, email, phone_number, account_status, account_type FROM ".$user_table_name);
			echo mysqli_num_rows($get_users);
		?></b>
	</span>, 

	<span class="mobile-font-size-14 system-font-size-20">Active Users: 
		<b><?php
			$get_users = mysqli_query($conn_server_db,"SELECT firstname, lastname, email, phone_number, account_status, account_type FROM ".$user_table_name." WHERE account_status='active'");
			echo mysqli_num_rows($get_users);
		?></b>
	</span>, 

	<span class="mobile-font-size-14 system-font-size-20">Blocked Users: 
		<b><?php
			$get_users = mysqli_query($conn_server_db,"SELECT firstname, lastname, email, phone_number, account_status, account_type FROM ".$user_table_name." WHERE account_status='blocked'");
			echo mysqli_num_rows($get_users);
		?></b>
	</span><br>

	<span class="mobile-font-size-14 system-font-size-20">Users Accumulated Balance Plus (Blocked Users): 
		<b><?php 
		$wallet_total_balance = mysqli_query($conn_server_db,"SELECT * FROM users");
		if(mysqli_num_rows($wallet_total_balance) > 0){
			while($total_balance = mysqli_fetch_assoc($wallet_total_balance)){
				$user_total_balance += $total_balance["wallet_balance"];
			}
			echo $user_total_balance;
		}else{
			echo "0.00";
		}
		
		?></b>
	</span>, 

	<span class="mobile-font-size-14 system-font-size-20">Users Accumulated Balance Minus (Blocked Users): 
		<b></b>
	</span><br>-->
</div>

<div class="container-box color-9 bg-1 mobile-font-size-14 system-font-size-18 mobile-width-95 system-width-93 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-1 system-padding-top-1 mobile-padding-left-1 system-padding-left-1 mobile-padding-right-1 system-padding-right-1 mobile-padding-bottom-1 system-padding-bottom-1">
	<div style="display: inline-block;" class="container-box bg-4 mobile-width-40 system-width-13 mobile-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<span class="mobile-font-size-14 system-font-size-18">
			Users Total Funds <br/><br/><br/>
			<strong>N<?php echo $users_total_balance; ?></strong>
		</span>
	</div>

	<div style="display: inline-block;" class="container-box bg-7 mobile-width-40 system-width-13 mobile-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<span class="mobile-font-size-14 system-font-size-18">
			Monnify’s Total Customers Deposit <br/><br/>
			<strong>N<?php echo $monnifyfundingMoney; ?></strong>
		</span>
	</div>

	<div style="display: inline-block;" class="container-box bg-2 mobile-width-40 system-width-13 mobile-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<span class="mobile-font-size-14 system-font-size-18">
			Flutterwave’s Total Customers Deposit <br/><br/>
			<strong>N<?php echo $flutterwavefundingMoney; ?></strong>
		</span>
	</div>

	<div style="display: inline-block;" class="container-box bg-3 mobile-width-40 system-width-13 mobile-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<span class="mobile-font-size-14 system-font-size-18">
			PayStack’s Total Customers Deposit <br/><br/>
			<strong>N<?php echo $paystackfundingMoney; ?></strong>
		</span>
	</div>

	<div style="display: inline-block;" class="container-box bg-5 mobile-width-87 system-width-13 mobile-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<span class="mobile-font-size-14 system-font-size-18">
			Manual Bank’s Total Customers Deposit <br/><br/>
			<strong>N<?php echo ($bankfundingMoney-($monnifyfundingMoney + $flutterwavefundingMoney + $paystackfundingMoney)); ?></strong>
		</span>
	</div>
</div><br/>

	<?php include("../include/admin-top-10-transaction.php"); ?>
</center>
	
<?php include("../include/admin-footer-html.php"); ?>
</body>
</html>