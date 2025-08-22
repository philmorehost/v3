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
	$get_mtn_datacard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM datacard_network_running_api WHERE network_name='mtn'"));
	$get_airtel_datacard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM datacard_network_running_api WHERE network_name='airtel'"));
	$get_glo_datacard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM datacard_network_running_api WHERE network_name='glo'"));
	$get_9mobile_datacard_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM datacard_network_running_api WHERE network_name='9mobile'"));
	
	//GET EACH AIRTIME APIKEY
	$mtn_api_website = $get_mtn_datacard_running_api['website'];
	$airtel_api_website = $get_airtel_datacard_running_api['website'];
	$glo_api_website = $get_glo_datacard_running_api['website'];
	$etisalat_api_website = $get_9mobile_datacard_running_api['website'];
	
	$get_mtn_datacard_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM datacard_api WHERE website='$mtn_api_website'"));
	$get_airtel_datacard_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM datacard_api WHERE website='$airtel_api_website'"));
	$get_glo_datacard_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM datacard_api WHERE website='$glo_api_website'"));
	$get_9mobile_datacard_apikey = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT apikey FROM datacard_api WHERE website='$etisalat_api_website'"));
	
	//GET EACH AIRTIME NETWORK STATUS
	$get_mtn_datacard_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM datacard_network_status WHERE network_name='mtn'"));
	$get_airtel_datacard_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM datacard_network_status WHERE network_name='airtel'"));
	$get_glo_datacard_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM datacard_network_status WHERE network_name='glo'"));
	$get_9mobile_datacard_network_status = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT network_status FROM datacard_network_status WHERE network_name='9mobile'"));
	
	$mtn_data_card_qtyprice_array = array("1gb"=>"250","1.5gb"=>"320","2gb"=>"500");
	$airtel_data_card_qtyprice_array = array();
	$glo_data_card_qtyprice_array = array();
	$etisalat_data_card_qtyprice_array = array();
	if(isset($_POST["buy"])){
		$carrier = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["carrier"]));
		$qty = str_replace(["-","+","/","*"],"",mysqli_real_escape_string($conn_server_db,strip_tags($_POST["qty"])));
		$data_size = str_replace(["-","+","/","*"],"",trim(mysqli_real_escape_string($conn_server_db,strip_tags($_POST["size"]))));
		
		if($all_user_details["account_type"] == "smart_earner"){
		if($carrier == "mtn"){
		$datacard_discount = $get_mtn_datacard_running_api["discount_1"];
		}
		
		if($carrier == "airtel"){
		$datacard_discount = $get_airtel_datacard_running_api["discount_1"];
		}
		
		if($carrier == "glo"){
		$datacard_discount = $get_glo_datacard_running_api["discount_1"];
		}
		
		if($carrier == "9mobile"){
		$datacard_discount = $get_9mobile_datacard_running_api["discount_1"];
		}
		}
		
		if($all_user_details["account_type"] == "vip_earner"){
		if($carrier == "mtn"){
		$datacard_discount = $get_mtn_datacard_running_api["discount_2"];
		}
		
		if($carrier == "airtel"){
		$datacard_discount = $get_airtel_datacard_running_api["discount_2"];
		}
		
		if($carrier == "glo"){
		$datacard_discount = $get_glo_datacard_running_api["discount_2"];
		}
		
		if($carrier == "9mobile"){
		$datacard_discount = $get_9mobile_datacard_running_api["discount_2"];
		}
		}
		
		if($all_user_details["account_type"] == "vip_vendor"){
		if($carrier == "mtn"){
		$datacard_discount = $get_mtn_datacard_running_api["discount_3"];
		}
		
		if($carrier == "airtel"){
		$datacard_discount = $get_airtel_datacard_running_api["discount_3"];
		}
		
		if($carrier == "glo"){
		$datacard_discount = $get_glo_datacard_running_api["discount_3"];
		}
		
		if($carrier == "9mobile"){
		$datacard_discount = $get_9mobile_datacard_running_api["discount_3"];
		}
		}
		
		if($all_user_details["account_type"] == "api_earner"){
		if($carrier == "mtn"){
		$datacard_discount = $get_mtn_datacard_running_api["discount_4"];
		}
		
		if($carrier == "airtel"){
		$datacard_discount = $get_airtel_datacard_running_api["discount_4"];
		}
		
		if($carrier == "glo"){
		$datacard_discount = $get_glo_datacard_running_api["discount_4"];
		}
		
		if($carrier == "9mobile"){
		$datacard_discount = $get_9mobile_datacard_running_api["discount_4"];
		}
		}
		
		if($carrier == "mtn"){
			$site_name = $mtn_api_website;
			$apikey = $get_mtn_datacard_apikey["apikey"];
			$amount = $mtn_data_card_qtyprice_array[$data_size];
		}
		
		if($carrier == "airtel"){
			$site_name = $airtel_api_website;
			$apikey = $get_airtel_datacard_apikey["apikey"];
			$amount = $airtel_data_card_qtyprice_array[$data_size];
		}
		
		if($carrier == "glo"){
			$site_name = $glo_api_website;
			$apikey = $get_glo_datacard_apikey["apikey"];
			$amount = $glo_data_card_qtyprice_array[$data_size];
		}
		
		if($carrier == "9mobile"){
			$site_name = $etisalat_api_website;
			$apikey = $get_9mobile_datacard_apikey["apikey"];
			$amount = $etisalat_data_card_qtyprice_array[$data_size];
		}
		
		$discounted_price_amount = (($amount-($amount*(floatval($datacard_discount)/100)))*$qty);

		if($all_user_details["wallet_balance"] >= $discounted_price_amount){
			if($site_name == $_SERVER["HTTP_HOST"]){
				include("./include/datacard-manual.php");
			}

			if($site_name == "datagifting.com.ng"){
				include("./include/datacard-datagifting.php");
			}
		}else{
			$log_datacard_message = "Insufficient Fund, Fund Wallet And Try Again! ";
		}
		
		$_SESSION["transaction_text"] = $log_datacard_message;
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
			alertPopUp("Select Data Card Company by clicking the image that represents the Service Provider");
		}, 1000);
</script>

<center>
	<div class="container-box bg-4 mobile-width-85 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<form method="post">
			<?php if($_SESSION["transaction_text"] == true){ ?>
			<div name="message" id="font-color-1" class="message-box font-size-2"><?php echo $_SESSION["transaction_text"]; ?></div>
			<?php } ?>
			
			<span style="font-weight: bolder;" class="color-8 mobile-font-size-20 system-font-size-25">PRINT DATA CARD</span><br>
			<img onclick="carrierServiceName('mtnServNetImg','mtn');" id="mtnServNetImg" src="/images/mtn.png" style="cursor: pointer;" class="mobile-width-15 system-width-15" />
			<select onkeydown="javascript: return nenterkey_function(event)" name="carrier" onchange="updateCarrierAPIkey()" id="carrier-name" hidden>
				<option value="" disabled hidden selected>Choose Carrier</option>
				<?php if($get_mtn_datacard_network_status["network_status"] == "active"){ ?>
				<option value="mtn">MTN</option>
				<?php } ?>
				<!--<?php if($get_airtel_datacard_network_status["network_status"] == "active"){ ?>
				<option value="airtel">Airtel</option>
				<?php } ?>
				<?php if($get_glo_datacard_network_status["network_status"] == "active"){ ?>
				<option value="glo">GLO</option>
				<?php } ?>
				<?php if($get_9mobile_datacard_network_status["network_status"] == "active"){ ?>
				<option value="9mobile">9Mobile</option>
				<?php } ?>-->
			</select><br>
			
			<select onkeydown="javascript: return nenterkey_function(event)" name="size" id="size" class="select-box mobile-width-63 system-width-41 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1">
				<option value="" disabled hidden selected>Choose Data Package</option>
				<?php
				function mtnPrice($qty){
					global $all_user_details;
					global $mtn_data_card_qtyprice_array;
					global $get_mtn_datacard_running_api;
					if($all_user_details["account_type"] == "smart_earner"){
						$mtn_datacard_price = (($mtn_data_card_qtyprice_array[$qty])-($mtn_data_card_qtyprice_array[$qty]*($get_mtn_datacard_running_api["discount_1"]/100)));
					}
					
					if($all_user_details["account_type"] == "vip_earner"){
						$mtn_datacard_price = ($mtn_data_card_qtyprice_array[$qty]-($mtn_data_card_qtyprice_array[$qty]*($get_mtn_datacard_running_api["discount_2"]/100)));
					}
					
					if($all_user_details["account_type"] == "vip_vendor"){
						$mtn_datacard_price = ($mtn_data_card_qtyprice_array[$qty]-($mtn_data_card_qtyprice_array[$qty]*($get_mtn_datacard_running_api["discount_3"]/100)));
					}
					
					if($all_user_details["account_type"] == "api_earner"){
						$mtn_datacard_price = ($mtn_data_card_qtyprice_array[$qty]-($mtn_data_card_qtyprice_array[$qty]*($get_mtn_datacard_running_api["discount_4"]/100)));
					}
					
					return "N".$mtn_data_card_qtyprice_array[$qty]." @ N".$mtn_datacard_price;
				}
				
				?>
				<option value="1gb">1gb @ <?php echo mtnPrice('1gb'); ?></option>
				<option value="1.5gb">1.5gb @ <?php echo mtnPrice('1.5gb'); ?></option>
				<option value="2gb">2gb @ <?php echo mtnPrice('2gb'); ?></option>
			</select><br>
			<input onkeydown="javascript: return nenterkey_function(event)" name="qty" id="datacard-qty" type="number" pattern="[0-9]{1,}" title="Input must be a number and must be 1 digit or more" class="input-box mobile-width-60 system-width-40 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" placeholder="Quantity"/>
			<center>
				<span style="font-weight:bold;" id="phone-error" class="color-8 mobile-font-size-10 system-font-size-12"></span>
				<span style="font-weight:bold;" id="product-error" class="color-8 blinker mobile-font-size-12 system-font-size-14"></span>
			</center>
			<input style="pointer-events:none;" onclick="openAuth(<?php echo $all_user_details['transaction_pin']; ?>);" type="button" id="proceed" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-41 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Proceed"/>
			<input name="buy" onclick="(this.style='pointer-events:none;background:lightgray;')()" style="display:none;" type="submit" id="buyPRODUCT" class="button-box color-8 bg-5 mobile-font-size-15 system-font-size-16 mobile-width-65 system-width-41 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1" value="Processing..."/>
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
					let carArrName = ['mtn','airtel','glo','9mobile'];
					if((carArrName.indexOf(document.getElementById("carrier-name").value) !== -1) && (document.getElementById("size").value !== "") && (Number(document.getElementById("datacard-qty").value) >= 1)){
						document.getElementById("proceed").style.pointerEvents = "auto";
					}else{
						document.getElementById("proceed").style.pointerEvents = "none";
					}
				});
			</script>

		</form>
	</div>

<?php
	include("./include/top-5-transaction.php");
?>
</center>

<?php include(__DIR__."/include/footer-html.php"); ?>
</body>
</html>