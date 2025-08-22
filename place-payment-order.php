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

	$raw_number = "123456789012345678901234567890";
	$reference = substr(str_shuffle($raw_number),0,15);
	
	if(isset($_POST["place-order"])){
		$email = mysqli_real_escape_string($conn_server_db,trim(strip_tags($_POST["email"])));
		$ref = mysqli_real_escape_string($conn_server_db,trim(strip_tags($_POST["ref"])));
		$amount = mysqli_real_escape_string($conn_server_db,trim(strip_tags($_POST["amount"])));
		$discounted_amount = $amount-50;
		if(mysqli_query($conn_server_db,"INSERT INTO payment_order_history (email, id, amount, status) VALUES ('$email','$ref','$discounted_amount','pending')") == true){
			$_SESSION["transaction_text"] = "Order Placed Successfully";
		}else{
			$_SESSION["transaction_text"] = "Error: Cant Place Order! Contact Admin";
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


<center>
	<div style="text-align: left;" class="container-box bg-8 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<form method="post">
			<?php echo $_SESSION["transaction_text"]; ?><br/>
			
			<center>
				<span style="text-align: left; font-weight: bolder;" class="color-9 mobile-font-size-20 system-font-size-25">PLACE PAYMENT ORDER</span><br><br>
			</center>
			<?php
				$get_admin_details = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM admin WHERE 1"));
				$get_admin_bank_details = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM admin_bank_details WHERE 1"));
			?>
			<span style="line-height: 35px;" class="color-9 mobile-font-size-16 system-font-size-18">WE ACCEPT: Bank Transfer, ATM Payment to our Bank.<br/>Send Money to the below Account:<br> <b>Account No: <?php echo $get_admin_bank_details["acct_number"]; ?><br/> Bank Name: <?php echo $get_admin_bank_details["bank_name"]; ?><br/> Account Name: <?php echo $get_admin_bank_details["acct_name"]; ?></b><br/>,<b>Make sure to send payment information like(Bank Account Name[The one use to make Payment], Amount, Order ID[Reference Number]) to <?php echo $get_admin_details["phone_number"]; ?><br/></b> then your wallet will be fund within 15minutes after Payment Confirmation<br/>Note: <b>N50 flat rate apply</b></a></span><br>
			<input name="email" value="<?php echo $all_user_details['email']; ?>" id="email" type="email" hidden/>
			<input name="ref" value="<?php echo $reference; ?>" id="ref" type="number" hidden/>
			<input name="amount" id="amount" type="text" pattern="[0-9]{3,}" title="Input must be a number and must be 3 digit or more" class="input-box mobile-width-58 system-width-70 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Amount" required/>
			<button name="place-order" type="submit" id="order-btn" class="button-box color-8 bg-6 mobile-font-size-15 system-font-size-16 mobile-width-35 system-width-25 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">Order</button>
		</form>
		</div>

		<span class="font-size-2 font-family-1">PAYMENT ORDER HISTORY</span><br>
		<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
		<table class="table-style-1">
		<tr>
			<th>Reference</th><th>Amount (Naira)</th><th>Initial Deposit</th><th>Status</th><th>Date</th>
		</tr>
			<?php
				$select_payment_order_history = mysqli_query($conn_server_db,"SELECT id, amount, status, transaction_date FROM payment_order_history WHERE email='$user_session' ORDER BY transaction_date DESC LIMIT 5");
				if(mysqli_num_rows($select_payment_order_history) > 0){
					while($payment_order_details = mysqli_fetch_assoc($select_payment_order_history)){
						echo "<tr>
							<td>".$payment_order_details["id"]."</td><td>".$payment_order_details["amount"]."</td><td>".($payment_order_details["amount"]+50)."</td><td>".ucwords($payment_order_details["status"])."</td><td>".$payment_order_details["transaction_date"]."</td>
						</tr>";
					}
				}
			?>
		<tr>
			<th>Reference</th><th>Amount (Naira)</th><th>Initial Deposit</th><th>Status</th><th>Date</th>
		</tr>
		</table>
	</div>
</center>


<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>