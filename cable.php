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
	
	//GET EACH cable API WEBSITE
	$get_startimes_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='startimes'"));
	$get_dstv_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='dstv'"));
	$get_gotv_cable_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM cable_subscription_running_api WHERE subscription_name='gotv'"));
	
	//GET EACH cable APIKEY
	$startimes_api_website = $get_startimes_cable_running_api['website'];
	$dstv_api_website = $get_dstv_cable_running_api['website'];
	$gotv_api_website = $get_gotv_cable_running_api['website'];
	
	$get_startimes_cable_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM cable_api WHERE website='$startimes_api_website'"));
	$get_dstv_cable_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM cable_api WHERE website='$dstv_api_website'"));
	$get_gotv_cable_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM cable_api WHERE website='$gotv_api_website'"));
	
	//GET EACH cable subscription STATUS
	$get_startimes_cable_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM cable_subscription_status WHERE subscription_name='startimes'"));
	$get_dstv_cable_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM cable_subscription_status WHERE subscription_name='dstv'"));
	$get_gotv_cable_subscription_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT subscription_status FROM cable_subscription_status WHERE subscription_name='gotv'"));
	
	
	$startimes_package_price = array("nova"=>"1900","basic"=>"3700","smart"=>"4700","classic"=>"5500","super"=>"9000");
	
		$dstv_package_price = array("padi"=>"4400","yanga"=>"6000","confam"=>"11000","compact"=>"19000","compact_plus"=>"30000","premium"=>"44500","padi__extraview"=>"10400","yanga__extraview"=>"12000","confam__extraview"=>"17000","compact__extra_view"=>"25000","compact_plus__extra_view"=>"36000","premium__extra_view"=>"50500");
		$gotv_package_price = array("smallie"=>"1900","jinja"=>"3900","jolli"=>"5800","max"=>"8500","super"=>"11400");
	
	if($all_user_details["account_type"] == "smart_earner"){
		$startimes_discount = $get_startimes_cable_running_api['discount_1'];
		$dstv_discount = $get_dstv_cable_running_api['discount_1'];
		$gotv_discount = $get_gotv_cable_running_api['discount_1'];
	}
	
	if($all_user_details["account_type"] == "vip_earner"){
		$startimes_discount = $get_startimes_cable_running_api['discount_2'];
		$dstv_discount = $get_dstv_cable_running_api['discount_2'];
		$gotv_discount = $get_gotv_cable_running_api['discount_2'];
	}
	
	if($all_user_details["account_type"] == "vip_vendor"){
		$startimes_discount = $get_startimes_cable_running_api['discount_3'];
		$dstv_discount = $get_dstv_cable_running_api['discount_3'];
		$gotv_discount = $get_gotv_cable_running_api['discount_3'];
	}
	
	if($all_user_details["account_type"] == "api_earner"){
		$startimes_discount = $get_startimes_cable_running_api['discount_4'];
		$dstv_discount = $get_dstv_cable_running_api['discount_4'];
		$gotv_discount = $get_gotv_cable_running_api['discount_4'];
	}
	
	if(isset($_POST["verify"])){
		$carrier = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["carrier"]));
		$cable_iuc_no = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["cable-iuc-no"]));
		
		if($carrier == "startimes"){
		$package_name = array_filter(explode(":",trim($_POST[$carrier])))[0];
		$amount = array_filter(explode(":",trim($_POST[$carrier])))[1];
		}
		
		if($carrier == "dstv"){
		$package_name = array_filter(explode(":",trim($_POST[$carrier])))[0];
		$amount = array_filter(explode(":",trim($_POST[$carrier])))[1];
		}
		
		if($carrier == "gotv"){
		$package_name = array_filter(explode(":",trim($_POST[$carrier])))[0];
		$amount = array_filter(explode(":",trim($_POST[$carrier])))[1];
		}
		
		if($carrier == "startimes"){
			$site_name = $startimes_api_website;
			$apikey = $get_startimes_cable_apikey["apikey"];
		}
		
		if($carrier == "dstv"){
			$site_name = $dstv_api_website;
			$apikey = $get_dstv_cable_apikey["apikey"];
		}
		
		if($carrier == "gotv"){
			$site_name = $gotv_api_website;
			$apikey = $get_gotv_cable_apikey["apikey"];
		}
		
		if(strtolower($site_name) === "smartrecharge.ng"){
		$verifyCablePurchase = curl_init();
		$verifyCableApiUrl = "https://smartrecharge.ng/api/v2/tv/?api_key=".$apikey."&product_code=".$carrier."_".$package_name."&smartcard_number=".$cable_iuc_no."&task=verify";
		curl_setopt($verifyCablePurchase,CURLOPT_URL,$verifyCableApiUrl);
		curl_setopt($verifyCablePurchase,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($verifyCablePurchase,CURLOPT_HTTPGET,1);
		curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYPEER, false);
		
		$GetverifyCableJSON = curl_exec($verifyCablePurchase);
		$verifyCableJSONObj = json_decode($GetverifyCableJSON,true);
		
		/*1987 Verification Successful
		1986 Transaction Successful
		1985 This user does not exists or is not activated for API ACCESS
		1984 User does not exist
		1983 Insufficient Credit, Please fund your account and try again
		1982 Transaction Failed
		1981 Transaction Pending
		1980 VERIFICATION FAILED, PLEASE TRY AGAIN OR CALL ADMIN
		1979 SOME PARAMETERS ARE MISSING
		1978 SERVICE NOT AVAILABLE AT THE MOMENT
		1977 ORDER IS FRAUDULENT*/

		if($GetverifyCableJSON == true){
		
			if(in_array($verifyCableJSONObj["error_code"],array(1987))){
				$_SESSION["cable_name"] = $verifyCableJSONObj["data"]["name"];
				$_SESSION["carrier"] = $carrier;
				$_SESSION["cable_iuc_no"] = $cable_iuc_no;
				$_SESSION["package_name"] = $package_name;
				$_SESSION["amount"] = $amount;
				$_SESSION["site_name"] = $site_name;
				$_SESSION["apikey"] = $apikey;
			}
			
			}
		}

		if(strtolower($site_name) === "vtpass.com"){
			$verifyCablePurchase = curl_init();
			$verifyCableApiUrl = "https://vtpass.com/api/merchant-verify";
			curl_setopt($verifyCablePurchase,CURLOPT_URL,$verifyCableApiUrl);
			curl_setopt($verifyCablePurchase,CURLOPT_RETURNTRANSFER,true);
			$vtpassHeader = array("Authorization: Basic ".base64_encode($apikey),"Content-Type: application/json");
			curl_setopt($verifyCablePurchase,CURLOPT_HTTPHEADER,$vtpassHeader);
			$vtpassPurchaseData = json_encode(array("serviceID"=>$carrier,"billersCode"=>$cable_iuc_no),true);
			curl_setopt($verifyCablePurchase,CURLOPT_POSTFIELDS,$vtpassPurchaseData);
			curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYPEER, false);
			
			$GetverifyCableJSON = curl_exec($verifyCablePurchase);
			$verifyCableJSONObj = json_decode($GetverifyCableJSON,true);
			
			if($GetverifyCableJSON == true){
		
				if(in_array($verifyCableJSONObj["code"],array("000"))){
					$_SESSION["cable_name"] = $verifyCableJSONObj["content"]["Customer_Name"];
					$_SESSION["carrier"] = $carrier;
					$_SESSION["cable_iuc_no"] = $cable_iuc_no;
					$_SESSION["package_name"] = $package_name;
					$_SESSION["amount"] = $amount;
					$_SESSION["site_name"] = $site_name;
					$_SESSION["apikey"] = $apikey;
				}
				
				}

		}

		if(strtolower($site_name) === "datagifting.com.ng"){
			$verifyCablePurchase = curl_init();
			$verifyCableApiUrl = "https://v5.datagifting.com.ng/web/api/verify-cable.php";
			curl_setopt($verifyCablePurchase,CURLOPT_URL,$verifyCableApiUrl);
			curl_setopt($verifyCablePurchase,CURLOPT_RETURNTRANSFER,true);
			$vtpassHeader = array("Content-Type: application/json");
			curl_setopt($verifyCablePurchase,CURLOPT_HTTPHEADER,$vtpassHeader);
			if ($carrier == "startimes") {
				$cable_package_array = array("nova"=>"nova","basic"=>"basic","smart"=>"smart","classic"=>"classic","super"=>"super");
			}
			if ($carrier == "dstv") {
				$cable_package_array = array("padi"=>"padi","yanga"=>"yanga","confam"=>"comfam","compact"=>"compact","compact_plus"=>"compact_plus","premium"=>"premium","padi__extraview"=>"padi_extraview","yanga__extraview"=>"yanga_extraview","confam__extraview"=>"comfam_extraview","compact__extra_view"=>"compact_extra_view","compact_plus__extra_view"=>"compact_plus_extra_view","premium__extra_view"=>"premium_extra_view");
			}
			if ($carrier == "gotv") {
				$cable_package_array = array("smallie"=>"smallie","jinja"=>"jinja","jolli"=>"jolli","max"=>"max","super"=>"super");
			}
			$vtpassPurchaseData = json_encode(array(
				"api_key" => $apikey,
				"type" => $carrier,
				"iuc_number" => $cable_iuc_no,
				"package" => $cable_package_array[$package_name]
			));
			curl_setopt($verifyCablePurchase,CURLOPT_POSTFIELDS,$vtpassPurchaseData);
			curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($verifyCablePurchase, CURLOPT_SSL_VERIFYPEER, false);
			
			$GetverifyCableJSON = curl_exec($verifyCablePurchase);
			$verifyCableJSONObj = json_decode($GetverifyCableJSON,true);
			
			if($GetverifyCableJSON == true){
		
				if(in_array($verifyCableJSONObj["status"],array("success"))){
					$_SESSION["cable_name"] = $verifyCableJSONObj["desc"];
					$_SESSION["carrier"] = $carrier;
					$_SESSION["cable_iuc_no"] = $cable_iuc_no;
					$_SESSION["package_name"] = $package_name;
					$_SESSION["amount"] = $amount;
					$_SESSION["site_name"] = $site_name;
					$_SESSION["apikey"] = $apikey;
				}
				
				}

		}
		
		header("Location: ".$_SERVER["REQUEST_URI"]);	
	}
	
	if(isset($_POST["buy"])){
		$carrier = $_SESSION["carrier"];
		$cable_iuc_no = $_SESSION["cable_iuc_no"];
		$site_name = $_SESSION["site_name"];
		$apikey = $_SESSION["apikey"];
		$package_name = $_SESSION["package_name"];
		$amount = $_SESSION["amount"];
		
		if($all_user_details["wallet_balance"] >= $amount){
			if($site_name == "smartrecharge.ng"){
				include("./include/cable-smartrecharge.php");
			}
			if($site_name == "smartrechargeapi.com"){
				include("./include/cable-smartrechargeapi.php");
			}
			if($site_name == "vtpass.com"){
				include("./include/cable-vtpass.php");
			}
			if($site_name == "mobileone.ng"){
				include("./include/cable-mobileone.php");
			}

			if($site_name == "datagifting.com.ng"){
				include("./include/cable-datagifting.php");
			}
		}else{
			$log_cable_message = "Error, Please contact us 08086697100 via WhatsApp ";
		}
		
		$_SESSION["transaction_text"] = $log_cable_message;
		unset($_SESSION["cable_name"]);
		unset($_SESSION["carrier"]);
		unset($_SESSION["cable_iuc_no"]);
		unset($_SESSION["package_name"]);
		unset($_SESSION["amount"]);
		unset($_SESSION["site_name"]);
		unset($_SESSION["apikey"]);
		header("Location: ".$_SERVER["REQUEST_URI"]);
	}
	
	if(isset($_POST["cancel"])){
		unset($_SESSION["cable_name"]);
		unset($_SESSION["carrier"]);
		unset($_SESSION["cable_iuc_no"]);
		unset($_SESSION["package_name"]);
		unset($_SESSION["amount"]);
		unset($_SESSION["site_name"]);
		unset($_SESSION["apikey"]);
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
<script type="text/javascript">
		setTimeout(function(){
			alertPopUp("Select Cable Type by clicking the image that represents the Cable Company");
		}, 1000);
</script>

<center>
	<div class="container-box bg-4 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<form method="post">
			<?php if($_SESSION["transaction_text"] == true){ ?>
			<div name="message" id="font-color-1" class="message-box font-size-2"><?php echo $_SESSION["transaction_text"]; ?></div>
			<?php } ?>
			
        	<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">SELECT CABLE BILLER</span><br>
			<?php if(!isset($_SESSION["cable_name"])){ ?>
			<img onclick="carrierServiceName('startimesServNetImg','startimes');" id="startimesServNetImg" src="/images/STARTIMES.jpg" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<img onclick="carrierServiceName('dstvServNetImg','dstv');" id="dstvServNetImg" src="/images/DSTV.jpg" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<img onclick="carrierServiceName('gotvServNetImg','gotv');" id="gotvServNetImg" src="/images/GOTV.jpg" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			
			<select name="carrier" onchange="updateCarrierAPIkey()" id="carrier-name" hidden>
				<option disabled hidden selected>Choose Carrier</option>
				<?php if($get_startimes_cable_subscription_status["subscription_status"] == "active"){ ?>
				<option value="startimes">STARTIMES</option>
				<?php } ?>
				<?php if($get_dstv_cable_subscription_status["subscription_status"] == "active"){ ?>
				<option value="dstv">DSTV</option>
				<?php } ?>
				<?php if($get_gotv_cable_subscription_status["subscription_status"] == "active"){ ?>
				<option value="gotv">GOTV</option>
				<?php } ?>
			</select>
			<select style="display:none;" name="startimes" id="startimes" class="select-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
			<?php
				foreach($startimes_package_price as $package_name => $initial_price){
				$package_price = round($initial_price-($initial_price*($startimes_discount/100)));
					echo "<option value='".$package_name.":".$package_price."' >".ucwords($package_name." N".$initial_price." @ N".$package_price)."</option>";
				}
			?>
			</select>
			
			<select style="display:none;" name="dstv" id="dstv" class="select-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
			<?php
				foreach($dstv_package_price as $package_name => $initial_price){
				$package_price = round($initial_price-($initial_price*($dstv_discount/100)));
					echo "<option value='".$package_name.":".$package_price."' >".ucwords($package_name." N".$initial_price." @ N".$package_price)."</option>";
				}
			?>
			</select>
			
			<select style="display:none;" name="gotv" id="gotv" class="select-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
			<?php
				foreach($gotv_package_price as $package_name => $initial_price){
				$package_price = round($initial_price-($initial_price*($gotv_discount/100)));
					echo "<option value='".$package_name.":".$package_price."' >".ucwords($package_name." N".$initial_price." @ N".$package_price)."</option>";
				}
			?>
			</select>
			<input onkeydown="javascript: return nenterkey_function(event)" name="cable-iuc-no" id="cable-iuc-no" type="tel" class="input-box mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Cable IUC No."/><br>
			<?php } ?>
			
			<center>
				<span style="font-weight:bold;" id="cable-error" class="color-8 mobile-font-size-10 system-font-size-12">
					<?php
						if(isset($_SESSION["cable_name"])){
							echo "Cable Type: <b>".strtoupper($_SESSION["carrier"])."</b><br>
							<img style='pointer-events:none;' src='./images/".strtoupper($_SESSION["carrier"]).".jpg' class='mobile-width-30 system-width-30'/><br>
							<b>Cable Name: ".$_SESSION["cable_name"]."<br>
							Cable IUC No: ".$_SESSION["cable_iuc_no"]."<br>
							Package: ".ucwords($_SESSION["package_name"])."<br>
							Amount To Pay: N".$_SESSION["amount"]."</b>";
						}
					?>
				</span>
				<span style="font-weight:bold;" id="product-error" class="color-8 blinker mobile-font-size-12 system-font-size-14"></span>
			</center>
			<?php if(!isset($_SESSION["cable_name"])){ ?>
			<input name="verify" type="submit" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Verify Cable"/>
			<?php }else{ ?>
			<input onclick="openAuth(<?php echo $all_user_details['transaction_pin']; ?>);" type="button" id="proceed" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Proceed"/>
			<?php } ?>
			<input name="buy" onclick="(this.style='pointer-events:none;background:lightgray;')()" style="display:none;" type="submit" id="buyPRODUCT" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Processing..."/>
			<?php if(isset($_SESSION["cable_name"])){ ?>
			<input name="cancel" type="submit" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-65 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Cancel"/>
			<?php } ?><br>
			<span class="color-8 mobile-font-size-12 system-font-size-14">Contact DSTV/GOtv's customers care unit on 01-2703232/08039003788 or the toll free lines: 08149860333, 07080630333, and 09090630333 for assistance, STARTIMES's customers care unit on (094618888, 014618888)</span><br>
		</form>
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
	let servNetArray = ['startimesServNetImg','dstvServNetImg','gotvServNetImg'];
	for(let x=0; x<servNetArray.length; x++){
if(servNetArray[x] !== serviceName){
document.getElementById(servNetArray[x]).style = "filter: grayscale(100%);";
if(servNetArray[x] == 'startimesServNetImg'){
	document.getElementById(servNetArray[x]).src = "/images/STARTIMES.jpg";
}
if(servNetArray[x] == 'dstvServNetImg'){
	document.getElementById(servNetArray[x]).src = "/images/DSTV.jpg";
}
if(servNetArray[x] == 'gotvServNetImg'){
	document.getElementById(servNetArray[x]).src = "/images/GOTV.jpg";
}
}else{
document.getElementById(servNetArray[x]).style = "filter: grayscale(0%);";
if(servNetArray[x] == 'startimesServNetImg'){
	for (var i = 0; i < listbox.options.length; ++i) {
		if (listbox.options[i].value === "startimes"){
			listbox.options[i].selected = true;
			document.getElementById("product-error").innerHTML = "";
		}
		
		if(listbox.value !== "startimes"){
			document.getElementById("product-error").innerHTML = "<br>Startimes Service not available! Try again later";
		}
	}
	document.getElementById(servNetArray[x]).src = "/images/startimes-marked.png";
}
if(servNetArray[x] == 'dstvServNetImg'){
	for (var i = 0; i < listbox.options.length; ++i) {
		if (listbox.options[i].value === "dstv"){
			listbox.options[i].selected = true;
			document.getElementById("product-error").innerHTML = "";
		}
		
		if(listbox.value !== "dstv"){
			document.getElementById("product-error").innerHTML = "<br>DSTV Service not available! Try again later";
		}
	}
	document.getElementById(servNetArray[x]).src = "/images/dstv-marked.jpg";
}
if(servNetArray[x] == 'gotvServNetImg'){
	for (var i = 0; i < listbox.options.length; ++i) {
		if (listbox.options[i].value === "gotv"){
			listbox.options[i].selected = true;
			document.getElementById("product-error").innerHTML = "";
		}
		
		if(listbox.value !== "gotv"){
			document.getElementById("product-error").innerHTML = "<br>GOTV Service not available! Try again later";
		}
	}
	document.getElementById(servNetArray[x]).src = "/images/gotv-marked.jpg";
}
}
	}
	
}
				function updateCarrierAPIkey(){
					const carrier_name = document.getElementById("carrier-name");
					
				if(document.getElementById("carrier-name").value == "startimes"){
					document.getElementById("startimes").style.display = "inline-block";
				}else{
					document.getElementById("startimes").style.display = "none";
				}
				
				if(document.getElementById("carrier-name").value == "dstv"){
					document.getElementById("dstv").style.display = "inline-block";
				}else{
					document.getElementById("dstv").style.display = "none";
				}
				
				if(document.getElementById("carrier-name").value == "gotv"){
					document.getElementById("gotv").style.display = "inline-block";
				}else{
					document.getElementById("gotv").style.display = "none";
				}
				
				
				}
				
				setInterval(function(){
					if((document.getElementById("carrier-name").value !== "") && (document.getElementById("cable-iuc-no").value.trim().length > 0)){
						document.getElementById("proceed").style.pointerEvents = "auto";
					}
					
				});
				
			</script>
	</div>

<?php
	include("./include/top-5-transaction.php");
?>
</center>

<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>