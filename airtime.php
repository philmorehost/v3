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
	
	//GET EACH AIRTIME API WEBSITE
	$get_mtn_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='mtn'"));
	$get_airtel_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='airtel'"));
	$get_glo_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='glo'"));
	$get_9mobile_airtime_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM airtime_network_running_api WHERE network_name='9mobile'"));
	
	//GET EACH AIRTIME APIKEY
	$mtn_api_website = $get_mtn_airtime_running_api['website'];
	$airtel_api_website = $get_airtel_airtime_running_api['website'];
	$glo_api_website = $get_glo_airtime_running_api['website'];
	$etisalat_api_website = $get_9mobile_airtime_running_api['website'];
	
	$get_mtn_airtime_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM airtime_api WHERE website='$mtn_api_website'"));
	$get_airtel_airtime_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM airtime_api WHERE website='$airtel_api_website'"));
	$get_glo_airtime_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM airtime_api WHERE website='$glo_api_website'"));
	$get_9mobile_airtime_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM airtime_api WHERE website='$etisalat_api_website'"));
	
	//GET EACH AIRTIME NETWORK STATUS
	$get_mtn_airtime_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM airtime_network_status WHERE network_name='mtn'"));
	$get_airtel_airtime_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM airtime_network_status WHERE network_name='airtel'"));
	$get_glo_airtime_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM airtime_network_status WHERE network_name='glo'"));
	$get_9mobile_airtime_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM airtime_network_status WHERE network_name='9mobile'"));
	
	if(isset($_POST["buy"])){
		$carrier = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["carrier"]));
		$phone_number = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["phone-number"]));
		$amount = str_replace(["-","+","/","*"],"",trim(mysqli_real_escape_string($conn_server_db,strip_tags($_POST["amount"]))));
		
		if($all_user_details["account_type"] == "smart_earner"){
		if($carrier == "mtn"){
		$airtime_discount = $get_mtn_airtime_running_api["discount_1"];
		}
		
		if($carrier == "airtel"){
		$airtime_discount = $get_airtel_airtime_running_api["discount_1"];
		}
		
		if($carrier == "glo"){
		$airtime_discount = $get_glo_airtime_running_api["discount_1"];
		}
		
		if($carrier == "9mobile"){
		$airtime_discount = $get_9mobile_airtime_running_api["discount_1"];
		}
		}
		
		if($all_user_details["account_type"] == "vip_earner"){
		if($carrier == "mtn"){
		$airtime_discount = $get_mtn_airtime_running_api["discount_2"];
		}
		
		if($carrier == "airtel"){
		$airtime_discount = $get_airtel_airtime_running_api["discount_2"];
		}
		
		if($carrier == "glo"){
		$airtime_discount = $get_glo_airtime_running_api["discount_2"];
		}
		
		if($carrier == "9mobile"){
		$airtime_discount = $get_9mobile_airtime_running_api["discount_2"];
		}
		}
		
		if($all_user_details["account_type"] == "vip_vendor"){
		if($carrier == "mtn"){
		$airtime_discount = $get_mtn_airtime_running_api["discount_3"];
		}
		
		if($carrier == "airtel"){
		$airtime_discount = $get_airtel_airtime_running_api["discount_3"];
		}
		
		if($carrier == "glo"){
		$airtime_discount = $get_glo_airtime_running_api["discount_3"];
		}
		
		if($carrier == "9mobile"){
		$airtime_discount = $get_9mobile_airtime_running_api["discount_3"];
		}
		}
		
		if($all_user_details["account_type"] == "api_earner"){
		if($carrier == "mtn"){
		$airtime_discount = $get_mtn_airtime_running_api["discount_4"];
		}
		
		if($carrier == "airtel"){
		$airtime_discount = $get_airtel_airtime_running_api["discount_4"];
		}
		
		if($carrier == "glo"){
		$airtime_discount = $get_glo_airtime_running_api["discount_4"];
		}
		
		if($carrier == "9mobile"){
		$airtime_discount = $get_9mobile_airtime_running_api["discount_4"];
		}
		}
		
		if($carrier == "mtn"){
			$site_name = $mtn_api_website;
			$apikey = $get_mtn_airtime_apikey["apikey"];
		}
		
		if($carrier == "airtel"){
			$site_name = $airtel_api_website;
			$apikey = $get_airtel_airtime_apikey["apikey"];
		}
		
		if($carrier == "glo"){
			$site_name = $glo_api_website;
			$apikey = $get_glo_airtime_apikey["apikey"];
		}
		
		if($carrier == "9mobile"){
			$site_name = $etisalat_api_website;
			$apikey = $get_9mobile_airtime_apikey["apikey"];
		}
		
		if($all_user_details["wallet_balance"] >= $amount){
			if($site_name == "smartrecharge.ng"){
				include("./include/airtime-smartrecharge.php");
			}
		
			if($site_name == "benzoni.ng"){
				include("./include/airtime-benzoni.php");
			}
			if($site_name == "grecians.ng"){
				include("./include/airtime-grecians.php");
			}
			if($site_name == "smartrechargeapi.com"){
				include("./include/airtime-smartrechargeapi.php");
			}
			if($site_name == "mobileone.ng"){
				include("./include/airtime-mobileone.php");
			}

			if($site_name == "datagifting.com.ng"){
				include("./include/airtime-datagifting.php");
			}
		}else{
			$log_airtime_message = "Insufficient Fund, Fund Wallet And Try Again! ";
		}
		
		$_SESSION["transaction_text"] = $log_airtime_message;
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
			
			<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">BUY AIRTIME</span><br>
			<img onclick="carrierServiceName('mtnServNetImg','mtn');" id="mtnServNetImg" src="/images/mtn.png" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<img onclick="carrierServiceName('airtelServNetImg','airtel');" id="airtelServNetImg" src="/images/airtel.png" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<img onclick="carrierServiceName('gloServNetImg','glo');" id="gloServNetImg" src="/images/glo.png" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<img onclick="carrierServiceName('9mobileServNetImg','9mobile');" id="9mobileServNetImg" src="/images/9mobile.png" style="cursor: pointer;" class="mobile-width-15 system-width-15" /><br>
			<select onkeydown="javascript: return nenterkey_function(event)" name="carrier" onchange="updateCarrierAPIkey()" id="carrier-name" hidden>
				<?php if($get_mtn_airtime_network_status["network_status"] == "active"){ ?>
				<option value="mtn">MTN</option>
				<?php } ?>
				<?php if($get_airtel_airtime_network_status["network_status"] == "active"){ ?>
				<option value="airtel">Airtel</option>
				<?php } ?>
				<?php if($get_glo_airtime_network_status["network_status"] == "active"){ ?>
				<option value="glo">GLO</option>
				<?php } ?>
				<?php if($get_9mobile_airtime_network_status["network_status"] == "active"){ ?>
				<option value="9mobile">9Mobile</option>
				<?php } ?>
			</select>
			<input onkeydown="javascript: return nenterkey_function(event)" name="phone-number" id="phone-number" type="tel" class="input-box mobile-width-40 system-width-30 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Phone Number"/>
			<input onkeydown="javascript: return nenterkey_function(event)" name="amount" id="amount" type="number" class="input-box mobile-width-30 system-width-29 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Amount"/><br>
			<center>
				<span style="font-weight:bold;" id="phone-error" class="color-8 mobile-font-size-10 system-font-size-12"></span>
				<span style="font-weight:bold;" id="product-error" class="color-8 blinker mobile-font-size-12 system-font-size-14"></span>
			</center>
			<input onkeydown="javascript: return nenterkey_function(event)" id="bypass" type="checkbox" class="check-box"/> <span class="color-8 mobile-font-size-10 system-font-size-12"><b>Bypass Phone Number Validator</b></span><br>
			<input style="pointer-events:none;" onclick="openAuth(<?php echo $all_user_details['transaction_pin']; ?>);" type="button" id="proceed" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-75 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Proceed"/>
			<input name="buy" onclick="(this.style='pointer-events:none;background:lightgray;')()" style="display:none;" type="submit" id="buyPRODUCT" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-75 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Processing..."/>
			<script>
				function carrierServiceName(serviceName,netName){
					setTimeout(function(){
						updateCarrierAPIkey();
					},100);
					
					let listbox = document.getElementById("carrier-name");
					for (var i = 0; i < listbox.options.length; ++i) {
						if (listbox.options[i].value === netName){
							listbox.options[i].selected = true;
						}
					}
					let servNetArray = ['mtnServNetImg','airtelServNetImg','gloServNetImg','9mobileServNetImg'];
					for(let x=0; x<servNetArray.length; x++){
						if(servNetArray[x] !== serviceName){
							document.getElementById(servNetArray[x]).style = "filter: grayscale(100%);";
							if(servNetArray[x] == 'mtnServNetImg'){
								document.getElementById(servNetArray[x]).src = "/images/mtn.png";
							}
							if(servNetArray[x] == 'airtelServNetImg'){
								document.getElementById(servNetArray[x]).src = "/images/airtel.png";
							}
							if(servNetArray[x] == 'gloServNetImg'){
								document.getElementById(servNetArray[x]).src = "/images/glo.png";
							}
							if(servNetArray[x] == '9mobileServNetImg'){
								document.getElementById(servNetArray[x]).src = "/images/9mobile.png";
							}
						}else{
							document.getElementById(servNetArray[x]).style = "filter: grayscale(0%);";
							if(servNetArray[x] == 'mtnServNetImg'){
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "mtn"){
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}
									
									if(listbox.value !== "mtn"){
										document.getElementById("product-error").innerHTML = "<br>MTN Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/mtn-marked.png";
							}
							if(servNetArray[x] == 'airtelServNetImg'){
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "airtel"){
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}
									
									if(listbox.value !== "airtel"){
										document.getElementById("product-error").innerHTML = "<br>Airtel Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/airtel-marked.png";
							}
							if(servNetArray[x] == 'gloServNetImg'){
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "glo"){
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}
									
									if(listbox.value !== "glo"){
										document.getElementById("product-error").innerHTML = "<br>Glo Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/glo-marked.png";
							}
							if(servNetArray[x] == '9mobileServNetImg'){
								for (var i = 0; i < listbox.options.length; ++i) {
									if (listbox.options[i].value === "9mobile"){
										listbox.options[i].selected = true;
										document.getElementById("product-error").innerHTML = "";
									}
									
									if(listbox.value !== "9mobile"){
										document.getElementById("product-error").innerHTML = "<br>9mobile Service not available! Try again later";
									}
								}
								document.getElementById(servNetArray[x]).src = "/images/9mobile-marked.png";
							}
						}
					}
					
				}
				function updateCarrierAPIkey(){
					const carrier_name = document.getElementById("carrier-name");
				}

				setInterval(function(){
					if(document.getElementById("bypass").checked == false){
						if(document.getElementById("phone-number").value.length == 11){
							if(carrierMTN.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
								carrierServiceName('mtnServNetImg','mtn');
								document.getElementById("phone-error").innerHTML = "Verified MTN Number";
							}

							if(carrierAirtel.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
								carrierServiceName('airtelServNetImg','airtel');
								document.getElementById("phone-error").innerHTML = "Verified Airtel Number";
							}

							if(carrierGlo.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
								carrierServiceName('gloServNetImg','glo');
								document.getElementById("phone-error").innerHTML = "Verified Glo Number";
							}

							if(carrier9mobile.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
								carrierServiceName('9mobileServNetImg','9mobile');
								document.getElementById("phone-error").innerHTML = "Verified 9mobile Number";
							}
						}
					}else{
						let fromSP = "";
							if(carrierMTN.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
								fromSP = "MTN";
							}

							if(carrierAirtel.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
								fromSP = "Airtel";
							}

							if(carrierGlo.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
								fromSP = "Glo";
							}

							if(carrier9mobile.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
								fromSP = "9mobile";
							}
							let toSP = document.getElementById("carrier-name").options[document.getElementById("carrier-name").selectedIndex].text;
							document.getElementById("phone-error").innerHTML = "Ported Number changed from "+fromSP+" to "+toSP;
					}

				}, 500);
				
				setInterval(function(){
					if((document.getElementById("carrier-name").value !== "") && (document.getElementById("phone-number").value.length == 11) && (Number(document.getElementById("amount").value) >= 100)){
						if(document.getElementById("bypass").checked == false){
							if(document.getElementById("carrier-name").value == "mtn"){
								if(carrierMTN.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
									document.getElementById("proceed").style.pointerEvents = "auto";
								}else{
									document.getElementById("proceed").style.pointerEvents = "none";
								}
							}
							
							if(document.getElementById("carrier-name").value == "airtel"){
								if(carrierAirtel.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
									document.getElementById("proceed").style.pointerEvents = "auto";
								}else{
									document.getElementById("proceed").style.pointerEvents = "none";
								}
							}
							
							if(document.getElementById("carrier-name").value == "glo"){
								if(carrierGlo.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
									document.getElementById("proceed").style.pointerEvents = "auto";
								}else{
									document.getElementById("proceed").style.pointerEvents = "none";
								}
							}
							
							if(document.getElementById("carrier-name").value == "9mobile"){
								if(carrier9mobile.indexOf(document.getElementById("phone-number").value.substring(1,4)) !== -1){
									document.getElementById("proceed").style.pointerEvents = "auto";
								}else{
									document.getElementById("proceed").style.pointerEvents = "none";
								}
							}
							
						}else{
							document.getElementById("proceed").style.pointerEvents = "auto";
						}
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