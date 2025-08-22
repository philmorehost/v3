<?php session_start(); date_default_timezone_set("Africa/Lagos");
	if(!isset($_SESSION["user"])){
		header("Location: /login.php");
	}else{
		include(__DIR__."/include/config.php");
		include(__DIR__."/include/user-details.php");
	}
	
	//GET USER DETAILS
	$user_session = $_SESSION["user"];
	$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
	
	//GET EACH insurance API WEBSITE
	$get_motor_insurance_insurance_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM insurance_subscription_running_api WHERE subscription_name='motor_insurance'"));
	
	//GET EACH insurance APIKEY
	$motor_insurance_api_website = $get_motor_insurance_insurance_running_api['website'];
	
	$get_motor_insurance_insurance_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM insurance_api WHERE website='$motor_insurance_api_website'"));
	
	//GET EACH insurance subscription STATUS
	$get_motor_insurance_insurance_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM insurance_subscription_status WHERE subscription_name='motor_insurance'"));
	
	if($all_user_details["account_type"] == "smart_earner"){
		$insurance_discount = $get_motor_insurance_insurance_running_api["discount_1"];
	}
	
	if($all_user_details["account_type"] == "vip_earner"){
		$insurance_discount = $get_motor_insurance_insurance_running_api["discount_2"];
	}
	
	if($all_user_details["account_type"] == "vip_vendor"){
		$insurance_discount = $get_motor_insurance_insurance_running_api["discount_3"];
	}
	
	if($all_user_details["account_type"] == "api_earner"){
		$insurance_discount = $get_motor_insurance_insurance_running_api["discount_4"];
	}
	
	$motor_insurance_price = array(1 => 3000,2 => 5000,3 => 1500);
	
	if(isset($_POST["buy"])){
	
		$insuranceType = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["insuranceType"]));
		$package = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["package"]));
		
		if($package == "private"){
			$variation = 1;
		}
		
		if($package == "commercial"){
			$variation = 2;
		}
		
		if($package == "tricycles"){
			$variation = 3;
		}
		
		$fullname = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["fullname"]));
		$engine_number = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["engine_number"]));
		$chasis_number = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["chasis_number"]));
		$plate_number = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["plate_number"]));
		$vehicle_make = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["vehicle_make"]));
		$vehicle_color = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["vehicle_color"]));
		$vehicle_model = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["vehicle_model"]));
		$year = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["year"]));
		$address = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["address"]));
		
		$phone_number = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["phone"]));
		$amount = ($motor_insurance_price[$variation]-($motor_insurance_price[$variation]*$insurance_discount/100));
		
		if($insuranceType == "motor"){
			$site_name = $motor_insurance_api_website;
			$apikey = $get_motor_insurance_insurance_apikey["apikey"];
		}

		if($all_user_details["wallet_balance"] >= $amount){
			if($site_name == "vtpass.com"){
				include("./include/insurance-vtpass.php");
			}
		}else{
			$log_insurance_message = "Insufficient Fund, Fund Wallet And Try Again! ";
		}
		
		$_SESSION["transaction_text"] = $log_insurance_message;
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
			
			<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">INSURANCE SUBSCRIPTION</span><br>
			<select name="insuranceType" id="insuranceType" onchange="updateCarrierAPIkey();" class="select-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
				<option disabled selected value="">Choose Insurance Type</option>
				<?php if($get_motor_insurance_insurance_subscription_status["subscription_status"] == "active"){ ?>
					<option value="motor">MOTOR INSURANCE</option>
				<?php } ?>
			</select><br>
			
			<select name="package" id="package" class="select-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
				<option value="private">Private Car - N3000 @ N<?php echo (3000-(3000*$insurance_discount/100)); ?></option>
				<option value="commercial">Commercial Bus - N5000 @ N<?php echo (5000-(5000*$insurance_discount/100)); ?></option>
				<option value="tricycles">Tricycles - N1500 @ N<?php echo (1500-(1500*$insurance_discount/100)); ?></option>
			</select>
			<input onkeydown="javascript: return nenterkey_function(event)" value="" name="fullname" id="fullname" type="text" class="input-box mobile-width-63 system-width-63 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Name"/>
			<input onkeydown="javascript: return nenterkey_function(event)" value="" name="engine_number" id="engine_number" type="text" class="input-box mobile-width-63 system-width-63 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Engine Number"/>
			<input onkeydown="javascript: return nenterkey_function(event)" value="" name="chasis_number" id="chasis_number" type="text" class="input-box mobile-width-63 system-width-63 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Chasis Number"/>
			<input onkeydown="javascript: return nenterkey_function(event)" value="" name="plate_number" id="plate_number" type="text" class="input-box mobile-width-63 system-width-63 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Plate Number"/>
			<input onkeydown="javascript: return nenterkey_function(event)" value="" name="vehicle_make" id="vehicle_make" type="text" class="input-box mobile-width-63 system-width-63 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Vehicle Maker"/>
			<input onkeydown="javascript: return nenterkey_function(event)" value="" name="vehicle_color" id="vehicle_color" type="text" class="input-box mobile-width-63 system-width-63 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Vehicle Color"/>
			<input onkeydown="javascript: return nenterkey_function(event)" value="" name="vehicle_model" id="vehicle_model" type="text" class="input-box mobile-width-63 system-width-63 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Vehicle Model"/>
			<input onkeydown="javascript: return nenterkey_function(event)" value="" name="year" id="year" type="number" class="input-box mobile-width-63 system-width-63 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Year Made"/>
			<input onkeydown="javascript: return nenterkey_function(event)" value="" name="phone" id="phone" type="text" pattern="[0-9]{11}" title="Input must be an 11 digit" class="input-box mobile-width-63 system-width-63 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Phone Number"/>
			<input onkeydown="javascript: return nenterkey_function(event)" value="" name="address" id="address" type="text" class="input-box mobile-width-63 system-width-63 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Contact Address"/>
			
			<center>
				<span style="font-weight:bold;" id="phone-error" class="color-8 mobile-font-size-10 system-font-size-12"></span>
			</center>
			<input style="pointer-events:none;" onclick="openAuth(<?php echo $all_user_details['transaction_pin']; ?>);" type="button" id="proceed" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-64 system-width-64 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Proceed"/>
			<input name="buy" onclick="(this.style='pointer-events:none;background:lightgray;')()" style="display:none;" type="submit" id="buyPRODUCT" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-64 system-width-64 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Processing..."/>
			
			<script>
				
				function updateCarrierAPIkey(){
					const carrier_name = document.getElementById("insuranceType");
				}
				
				setInterval(function(){
					if((document.getElementById("insuranceType").value !== "") && (document.getElementById("package").value !== "") && (document.getElementById("fullname").value !== "") && (document.getElementById("engine_number").value !== "") && (document.getElementById("chasis_number").value !== "") && (document.getElementById("plate_number").value !== "") && (document.getElementById("vehicle_make").value !== "") && (document.getElementById("vehicle_color").value !== "") && (document.getElementById("vehicle_model").value !== "") && (document.getElementById("year").value !== "") && (document.getElementById("phone").value !== "") && (document.getElementById("address").value !== "")){
						document.getElementById("proceed").style.pointerEvents = "auto";
					}else{
						document.getElementById("proceed").style.pointerEvents = "none";
					}
				});
			</script>
		</form>
	</div><br>

<?php
	include("./include/top-5-transaction.php");
?>
</center>

<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>