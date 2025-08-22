<?php session_start();
	if(!isset($_SESSION["user"])){
		header("Location: /login.php");
	}else{
		include(__DIR__."/include/config.php");
		include(__DIR__."/include/user-details.php");
	}
	
	//GET USER DETAILS
	$user_session = $_SESSION["user"];
	$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
	
	$showNum = trim(mysqli_real_escape_string($conn_server_db, strip_tags($_GET["num"])));
	
	if($showNum == true){
		if($showNum > 0 ){
			$pageNum = $showNum;
		}else{
			$pageNum = 1;
		}
	}else{
		$pageNum = 1;
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

<!-- BEGIN TRANSACTION CODE -->
<center>
	<div class="container-box bg-8 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
<?php
	$select_total_payment_order_history = mysqli_query($conn_server_db,"SELECT email, id, amount, transaction_date FROM payment_order_history WHERE email='$user_session'");
	if(empty(trim(strip_tags($_GET["search"])))){
		$select_payment_order_history = mysqli_query($conn_server_db,"SELECT email, id, amount, status, transaction_date FROM payment_order_history WHERE email='$user_session' ORDER BY transaction_date DESC LIMIT 20 OFFSET ".round(0+(($pageNum-1)*20)));
	}else{
		$select_payment_order_history = mysqli_query($conn_server_db,"SELECT email, id, amount, status, transaction_date FROM payment_order_history WHERE ((email='$user_session') AND (id='".trim(strip_tags($_GET["search"]))."')) ORDER BY transaction_date DESC LIMIT 20 OFFSET ".round(0+(($pageNum-1)*20)));	
	}
?>
	<center>
		<form method="get">
			<input name="search" value="<?php echo trim(strip_tags($_GET["search"])); ?>" type="text" class="input-box mobile-width-40 system-width-30" placeholder="Search by: Reference">
			<button id="" class="button-box color-8 bg-6 mobile-font-size-15 system-font-size-16 mobile-width-15 system-width-10">Search</button><br>
		</form>
	</center>
	<span class="font-size-2 font-family-1">PAYMENT ORDER HISTORY [Table List Count: <b><?php echo mysqli_num_rows($select_transaction_history); ?></b>], Total Transaction: <b><?php echo mysqli_num_rows($select_total_transaction_history); ?></b></span><br>
<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
<table class="table-style-1">
<tr>
	<th>Reference</th><th>Amount (Naira)</th><th>Initial Deposit</th><th>Status</th><th>Date</th>
</tr>
	<?php
		if(mysqli_num_rows($select_payment_order_history) > 0){
			while($payment_order_details = mysqli_fetch_assoc($select_payment_order_history)){
				echo "<tr>
					<td>".$payment_order_details["id"]."</td><td>".$payment_order_details["amount"]."</td><td>".($payment_order_details["amount"]+50)."</td><td>".$payment_order_details["status"]."</td><td>".$payment_order_details["transaction_date"]."</td>
				</tr>";
			}
		}
	?>
<tr>
	<th>Reference</th><th>Amount (Naira)</th><th>Initial Deposit</th><th>Status</th><th>Date</th>
</tr>
</table>
</div><br>

	<a href="/payment-order.php?num=<?php if($pageNum > 1){ echo round($pageNum-1); }else{ echo 1; } ?>">
		<button name="num" type="button" class="button-box color-8 bg-6 mobile-font-size-15 system-font-size-16 mobile-width-15 system-width-10" >Prev</button>
	</a>
	
	<a href="/payment-order.php?num=<?php if($pageNum >= 1){ echo round($pageNum+1); }else{ echo 1; } ?>">
		<button name="num" type="button" class="button-box color-8 bg-6 mobile-font-size-15 system-font-size-16 mobile-width-15 system-width-10" >Next</button>
	</a><br>
	</div>
</center>
<!-- END TRANSACTION CODE -->

<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>