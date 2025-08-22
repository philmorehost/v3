<?php session_start();
	if(!isset($_SESSION["user"])){
		header("Location: /login.php");
	}else{
		include(__DIR__."/include/config.php");
		include(__DIR__."/include/user-details.php");
	}
	
	if($conn_server_db == true){
		if(mysqli_query($conn_server_db,"CREATE TABLE IF NOT EXISTS sms_sender_id (email VARCHAR(225) NOT NULL, sender_id VARCHAR(60) NOT NULL, status VARCHAR(60) NOT NULL)") == true){}
	}
	
	//GET USER DETAILS
	$user_session = $_SESSION["user"];
	$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
	
	//GET EACH sms API WEBSITE
	$get_smsserver_sms_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, price_1, price_2, price_3, price_4 FROM sms_network_running_api WHERE network_name='smsserver'"));
	
	//GET EACH sms APIKEY
	$smsserver_api_website = $get_smsserver_sms_running_api['website'];
	
	$get_smsserver_sms_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM sms_api WHERE website='$smsserver_api_website'"));
	
	//GET EACH sms NETWORK STATUS
	$get_smsserver_sms_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM sms_network_status WHERE network_name='smsserver'"));
	
	if($all_user_details["account_type"] == "smart_earner"){
		$sms_price_per_qty = $get_smsserver_sms_running_api["price_1"];
	}
	
	if($all_user_details["account_type"] == "vip_earner"){
		$sms_price_per_qty = $get_smsserver_sms_running_api["price_2"];
	}
	
	if($all_user_details["account_type"] == "vip_vendor"){
		$sms_price_per_qty = $get_smsserver_sms_running_api["price_3"];
	}
	
	if($all_user_details["account_type"] == "api_earner"){
		$sms_price_per_qty = $get_smsserver_sms_running_api["price_4"];
	}
	
	if(isset($_POST["buy"])){
		$carrier = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["carrier"]));
		$phone_number = str_replace([" ","+"],"",mysqli_real_escape_string($conn_server_db,strip_tags($_POST["phone-number"])));
		$smsText = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["smsText"]));
		$amount = ($sms_price_per_qty*(count(array_filter(explode(",",trim($phone_number))))));
		$senderID = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["senderID"]));
		$schedule_date = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["date"]." ".$_POST["hour"].":".$_POST["min"].":".$_POST["sec"]));
		
		$site_name = $smsserver_api_website;
		$apikey = $get_smsserver_sms_apikey["apikey"];

		if($all_user_details["wallet_balance"] >= $amount){
			if($site_name == "philmoresms.com"){
				include("./include/sms-philmoresms.php");
			}
		}else{
			$log_sms_message = "Insufficient Fund, Fund Wallet And Try Again! ";
		}
		
		$_SESSION["transaction_text"] = $log_sms_message;
		header("Location: ".$_SERVER["REQUEST_URI"]);
	}
	
	
	if(isset($_POST["req-senderID"])){
		$senderID = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["u-senderID"]));
		if(mysqli_num_rows(mysqli_query($conn_server_db,"SELECT * FROM sms_sender_id WHERE email='$user_session' AND sender_id='$senderID'")) == 0){
			echo mysqli_error($conn_server_db);
			if(mysqli_query($conn_server_db,"INSERT INTO sms_sender_id (email, sender_id, status) VALUES ('$user_session','$senderID','pending')") == true){
				echo mysqli_error($conn_server_db);
			}else{
			echo mysqli_error($conn_server_db);
			}
		}else{
		echo mysqli_error($conn_server_db);
		}
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
<script src="/scripts/carrier.js"></script>
<script src="/scripts/auth.js"></script>
<script src="/scripts/trans-pass.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>
<body>
<?php include(__DIR__."/include/header-html.php"); ?>

<center>
<div class="container-box bg-4 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<form method="post">
			<?php if($_SESSION["transaction_text"] == true){ ?>
			<div name="message" id="font-color-1" class="message-box font-size-2"><?php echo $_SESSION["transaction_text"]; ?></div>
			<?php } ?>
			
			<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">SEND BULK SMS</span><br>
			<select name="senderID" id="senderID" class="select-box mobile-width-85 system-width-85 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
				<option disabled selected value="">Sender ID</option>
				<?php
					$get_sms_senderID = mysqli_query($conn_server_db,"SELECT * FROM sms_sender_id WHERE email='$user_session' AND status='approved'");
					if(mysqli_num_rows($get_sms_senderID)){
						while($senderID = mysqli_fetch_assoc($get_sms_senderID)){
							echo '<option value="'.$senderID["sender_id"].'">'.$senderID["sender_id"].'</option>';
						}
					}
				?>
			</select><br>
			<textarea onkeydown="javascript: return nenterkey_function(event)" name="phone-number" id="phone-number" class="textarea-box mobile-width-40 system-width-40 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Phone Number e.g 08121212121,0704724333"/></textarea>
			<textarea onkeydown="javascript: return nenterkey_function(event)" name="smsText" id="smsText" class="textarea-box mobile-width-40 system-width-42 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Message"/></textarea><br>
			<input onkeydown="javascript: return nenterkey_function(event)" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" max="" name="date" id="" type="date" class="input-box mobile-width-83 system-width-83 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1"/><br>
			<span class="color-8 mobile-font-size-15 system-font-size-20">Schedule Delivery Date</span><br>
			<select name="hour" class="select-box mobile-width-45 system-width-45 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
				<?php
					
					foreach(range(0,23) as $num){
						if(in_array($num,range(0,9))){
							$numb = "0".$num;
						}else{
							$numb = $num;
						}
						if($numb == date("H")){
							$selected = "selected";
						}else{
							$selected = "";
						}
						echo '<option '.$selected.' value="'.$numb.'">'.$numb.'hr</option>';
					}
				?>
			</select>
			<span class="color-8 mobile-font-size-15 system-font-size-20">:</span>
			<select name="min" class="select-box mobile-width-20 system-width-20 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
				<?php
					foreach(range(0,59) as $num){
						if(in_array($num,range(0,9))){
							$numb = "0".$num;
						}else{
							$numb = $num;
						}
						if($numb == date("i")){
							$selected = "selected";
						}else{
							$selected = "";
						}
						echo '<option '.$selected.' value="'.$numb.'">'.$numb.'min</option>';
					}
				?>
			</select>
			<span class="color-8 mobile-font-size-15 system-font-size-20">:</span>
			<select name="sec" class="select-box mobile-width-15 system-width-15 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
				<?php
					foreach(range(0,59) as $num){
						if(in_array($num,range(0,9))){
							$numb = "0".$num;
						}else{
							$numb = $num;
						}
						if($numb == date("s")){
							$selected = "selected";
						}else{
							$selected = "";
						}
						echo '<option '.$selected.' value="'.$numb.'">'.$numb.'sec</option>';
					}
				?>
			</select><br>
			<center>
				<span style="font-weight:bold;" id="phone-error" class="color-8 mobile-font-size-15 system-font-size-20"></span>
			</center>
			<input style="pointer-events:none;" onclick="openAuth(<?php echo $all_user_details['transaction_pin']; ?>);" type="button" id="proceed" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-85 system-width-85 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Proceed"/>
			<input name="buy" onclick="(this.style='pointer-events:none;background:lightgray;')()" style="display:none;" type="submit" id="buyPRODUCT" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-85 system-width-85 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Processing..."/>
			
			<script>
				setInterval(function(){
					if((document.getElementById("senderID").value !== "") && (document.getElementById("phone-number").value !== "") && (document.getElementById("smsText").value !== "")){
						var total_phone_number = ((document.getElementById("phone-number").value).split(",")).length;
						document.getElementById("phone-error").innerHTML = "Total Phone Number: "+total_phone_number+" Phone Number, @ N"+(total_phone_number*"<?php echo $sms_price_per_qty; ?>");
						if('<?php echo $all_user_details["wallet_balance"]; ?>' >= (total_phone_number*"<?php echo $sms_price_per_qty; ?>")){
							document.getElementById("proceed").style.pointerEvents = "auto";
						}else{
							document.getElementById("proceed").style.pointerEvents = "none";
						}
					}
				});
			</script>
		</form><br>
		<form method="post">
			<span class="color-8 mobile-font-size-15 system-font-size-20">REQUEST SENDER ID</span><br>
			<input name="u-senderID" pattern="[a-zA-Z]{6,7,8,9,10,11}" title="Sender ID must not be less than 6 and not more than 11 character" type="text" class="input-box mobile-width-55 system-width-62 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Sender ID" required/>
			<input name="req-senderID" type="submit" id="" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-27 system-width-20 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Request Approval"/>
		</form>
	</div><br>
	
	<span style="font-weight: bolder;" class="color-9 mobile-font-size-16 system-font-size-18">SMS SENDER IDs</span><br>
	<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
		<table class="table-style-1">
			<tr>
				<th>Sender ID</th><th>Status</th>
			</tr>
				<?php
					$getSMSSenderInfo = mysqli_query($conn_server_db,"SELECT * FROM sms_sender_id WHERE email='$user_session'");
					if($getSMSSenderInfo == true){
						if(mysqli_num_rows($getSMSSenderInfo) > 0){
							while($senderIdInfo = mysqli_fetch_assoc($getSMSSenderInfo)){
								echo "<tr>
									<td>".$senderIdInfo["sender_id"]."</td><td>".$senderIdInfo["status"]."</td>
								</tr>";
							}
						}
					}
				?>
			<tr>
				<th>Sender ID</th><th>Status</th>
			</tr>
		</table>
	</div><br>

<?php
	include("./include/top-5-transaction.php");
?>
</center>


<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>