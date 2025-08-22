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
	
	//GET EACH exam API WEBSITE
	$get_waec_exam_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM exam_pin_running_api WHERE pin_name='waec'"));
	$get_neco_exam_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM exam_pin_running_api WHERE pin_name='neco'"));
	$get_nabteb_exam_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM exam_pin_running_api WHERE pin_name='nabteb'"));
	
	//GET EACH exam APIKEY
	$waec_api_website = $get_waec_exam_running_api['website'];
	$neco_api_website = $get_neco_exam_running_api['website'];
	$nabteb_api_website = $get_nabteb_exam_running_api['website'];
	
	$get_waec_exam_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM exam_api WHERE website='$waec_api_website'"));
	$get_neco_exam_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM exam_api WHERE website='$neco_api_website'"));
	$get_nabteb_exam_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM exam_api WHERE website='$nabteb_api_website'"));
	
	//GET EACH exam pin STATUS
	$get_waec_exam_pin_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT pin_status FROM exam_pin_status WHERE pin_name='waec'"));
	$get_neco_exam_pin_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT pin_status FROM exam_pin_status WHERE pin_name='neco'"));
	$get_nabteb_exam_pin_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT pin_status FROM exam_pin_status WHERE pin_name='nabteb'"));
	
	$waec_package_price = array("waec"=>"4000");
	$neco_package_price = array("neco"=>"1100");
	$nabteb_package_price = array("nabteb"=>"1100");
	
	if($all_user_details["account_type"] == "smart_earner"){
		$waec_discount = $get_waec_exam_running_api['discount_1'];
		$neco_discount = $get_neco_exam_running_api['discount_1'];
		$nabteb_discount = $get_nabteb_exam_running_api['discount_1'];
	}
	
	if($all_user_details["account_type"] == "vip_earner"){
		$waec_discount = $get_waec_exam_running_api['discount_2'];
		$neco_discount = $get_neco_exam_running_api['discount_2'];
		$nabteb_discount = $get_nabteb_exam_running_api['discount_2'];
	}
	
	if($all_user_details["account_type"] == "vip_vendor"){
		$waec_discount = $get_waec_exam_running_api['discount_3'];
		$neco_discount = $get_neco_exam_running_api['discount_3'];
		$nabteb_discount = $get_nabteb_exam_running_api['discount_3'];
	}
	
	if($all_user_details["account_type"] == "api_earner"){
		$waec_discount = $get_waec_exam_running_api['discount_4'];
		$neco_discount = $get_neco_exam_running_api['discount_4'];
		$nabteb_discount = $get_nabteb_exam_running_api['discount_4'];
	}

	
	if(isset($_POST["buy"])){
		$carrier = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["carrier"]));
		if($carrier == "waec"){
		$package_name = array_filter(explode(":",trim($_POST[$carrier])))[0];
		$amount = array_filter(explode(":",trim($_POST[$carrier])))[1];
		}
		
		if($carrier == "neco"){
		$package_name = array_filter(explode(":",trim($_POST[$carrier])))[0];
		$amount = array_filter(explode(":",trim($_POST[$carrier])))[1];
		}
		
		if($carrier == "nabteb"){
		$package_name = array_filter(explode(":",trim($_POST[$carrier])))[0];
		$amount = array_filter(explode(":",trim($_POST[$carrier])))[1];
		}
		
		if($carrier == "waec"){
			$site_name = $waec_api_website;
			$apikey = $get_waec_exam_apikey["apikey"];
		}

		if($carrier == "neco"){
			$site_name = $neco_api_website;
			$apikey = $get_neco_exam_apikey["apikey"];
		}

		if($carrier == "nabteb"){
			$site_name = $nabteb_api_website;
			$apikey = $get_nabteb_exam_apikey["apikey"];
		}

		if($all_user_details["wallet_balance"] >= $amount){
			if($site_name == "abumpay.com"){
				include("./include/exam-abumpay.php");
			}
			
			if($site_name == "clubkonnect.com"){
				include("./include/exam-clubkonnect.php");
			}

			if($site_name == "datagifting.com.ng"){
				include("./include/exam-datagifting.php");
			}
		}else{
			$log_exam_message = "Error, Insufficient Fund ";
		}
		
		$_SESSION["transaction_text"] = $log_exam_message;
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
			alertPopUp("Select Exam Type by clicking the image that represents the Examination");
		}, 1000);
</script>

<center>
<div class="container-box bg-4 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<form method="post">
			<?php if($_SESSION["transaction_text"] == true){ ?>
			<div name="message" id="font-color-1" class="message-box font-size-2"><?php echo $_SESSION["transaction_text"]; ?></div>
			<?php } ?>
			
			<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">SELECT EXAM TYPE</span><br>
			<img onclick="carrierServiceName('waecServNetImg','waec');" id="waecServNetImg" src="/images/WAEC.jpg" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<img onclick="carrierServiceName('necoServNetImg','neco');" id="necoServNetImg" src="/images/NECO.jpg" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<img onclick="carrierServiceName('nabtebServNetImg','nabteb');" id="nabtebServNetImg" src="/images/NABTEB.jpg" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<select name="carrier" onchange="updateCarrierAPIkey()" id="carrier-name" hidden>
				<option disabled hidden selected>Choose Carrier</option>
				<?php if($get_waec_exam_pin_status["pin_status"] == "active"){ ?>
				<option value="waec">WAEC</option>
				<?php } ?>
				<?php if($get_neco_exam_pin_status["pin_status"] == "active"){ ?>
				<option value="neco">NECO</option>
				<?php } ?>
				<?php if($get_nabteb_exam_pin_status["pin_status"] == "active"){ ?>
				<option value="nabteb">NABTEB</option>
				<?php } ?>
			</select><br>
			<select style="display:none;" name="waec" id="waec" class="select-box mobile-width-47 system-width-47 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
			<?php
				foreach($waec_package_price as $package_name => $initial_price){
				$package_price = round($initial_price-($initial_price*($waec_discount/100)));
					echo "<option value='".$package_name.":".$package_price."' >".ucwords($package_name." N".$initial_price." @ N".$package_price)."</option>";
				}
			?>
			</select>
			
			<select style="display:none;" name="neco" id="neco" class="select-box mobile-width-47 system-width-47 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
			<?php
				foreach($neco_package_price as $package_name => $initial_price){
				$package_price = round($initial_price-($initial_price*($neco_discount/100)));
					echo "<option value='".$package_name.":".$package_price."' >".ucwords($package_name." N".$initial_price." @ N".$package_price)."</option>";
				}
			?>
			</select>
			
			<select style="display:none;" name="nabteb" id="nabteb" class="select-box mobile-width-47 system-width-47 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
			<?php
				foreach($nabteb_package_price as $package_name => $initial_price){
				$package_price = round($initial_price-($initial_price*($nabteb_discount/100)));
					echo "<option value='".$package_name.":".$package_price."' >".ucwords($package_name." N".$initial_price." @ N".$package_price)."</option>";
				}
			?>
			</select><br>
			<span style="font-weight:bold;" id="product-error" class="color-8 blinker mobile-font-size-12 system-font-size-14"></span>
			<input onclick="openAuth(<?php echo $all_user_details['transaction_pin']; ?>);" type="button" id="proceed" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-47 system-width-47 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Proceed"/>
			<input name="buy" onclick="(this.style='pointer-events:none;background:lightgray;')()" style="display:none;" type="submit" id="buyPRODUCT" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-47 system-width-47 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Processing..."/>
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
	let servNetArray = ['waecServNetImg','necoServNetImg','nabtebServNetImg'];
	for(let x=0; x<servNetArray.length; x++){
if(servNetArray[x] !== serviceName){
document.getElementById(servNetArray[x]).style = "filter: grayscale(100%);";
if(servNetArray[x] == 'waecServNetImg'){
	document.getElementById(servNetArray[x]).src = "/images/WAEC.jpg";
}
if(servNetArray[x] == 'necoServNetImg'){
	document.getElementById(servNetArray[x]).src = "/images/NECO.jpg";
}
if(servNetArray[x] == 'nabtebServNetImg'){
	document.getElementById(servNetArray[x]).src = "/images/NABTEB.jpg";
}
}else{
document.getElementById(servNetArray[x]).style = "filter: grayscale(0%);";
if(servNetArray[x] == 'waecServNetImg'){
	for (var i = 0; i < listbox.options.length; ++i) {
		if (listbox.options[i].value === "waec"){
			listbox.options[i].selected = true;
			document.getElementById("product-error").innerHTML = "";
		}
		
		if(listbox.value !== "waec"){
			document.getElementById("product-error").innerHTML = "<br>Waec Service not available! Try again later";
		}
	}
	document.getElementById(servNetArray[x]).src = "/images/waec-marked.jpg";
}
if(servNetArray[x] == 'necoServNetImg'){
	for (var i = 0; i < listbox.options.length; ++i) {
		if (listbox.options[i].value === "neco"){
			listbox.options[i].selected = true;
			document.getElementById("product-error").innerHTML = "";
		}
		
		if(listbox.value !== "neco"){
			document.getElementById("product-error").innerHTML = "<br>Neco Service not available! Try again later";
		}
	}
	document.getElementById(servNetArray[x]).src = "/images/neco-marked.jpg";
}
if(servNetArray[x] == 'nabtebServNetImg'){
	for (var i = 0; i < listbox.options.length; ++i) {
		if (listbox.options[i].value === "nabteb"){
			listbox.options[i].selected = true;
			document.getElementById("product-error").innerHTML = "";
		}
		
		if(listbox.value !== "nabteb"){
			document.getElementById("product-error").innerHTML = "<br>Nabteb Service not available! Try again later";
		}
	}
	document.getElementById(servNetArray[x]).src = "/images/nabteb-marked.jpg";
}
}
	}
	
}
				function updateCarrierAPIkey(){
					const carrier_name = document.getElementById("carrier-name");
					
				if(document.getElementById("carrier-name").value == "waec"){
					document.getElementById("waec").style.display = "inline-block";
				}else{
					document.getElementById("waec").style.display = "none";
				}
				
				if(document.getElementById("carrier-name").value == "neco"){
					document.getElementById("neco").style.display = "inline-block";
				}else{
					document.getElementById("neco").style.display = "none";
				}
				
				if(document.getElementById("carrier-name").value == "nabteb"){
					document.getElementById("nabteb").style.display = "inline-block";
				}else{
					document.getElementById("nabteb").style.display = "none";
				}
				
				
				}
				
				setInterval(function(){
					if(document.getElementById("carrier-name").value !== ""){
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