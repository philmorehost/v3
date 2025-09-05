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

<center>
    <div style="text-align:left;" class="container-box bg-8 mobile-width-95 system-width-95 mobile-margin-top-2 system-margin-top-2">
	    <span class="mobile-font-size-14 system-font-size-20">SMARTRECHARGEAPI WALLET: N<?php echo json_decode(getAPIBalance("GET","https://smartrechargeapi.com/api/v2/others/get_account_balance.php/?api_key=".$api_web_apikey["smartrechargeapi.com"],"",""),true)["wallet"]; ?></span><br>
	    <span class="mobile-font-size-14 system-font-size-20">BENZONI WALLET: N<?php echo json_decode(getAPIBalance("GET","https://benzoni.ng/api/v2/others/get_account_balance.php/?api_key=".$api_web_apikey["benzoni.ng"],"",""),true)["wallet"]; ?></span><br>
		<span class="mobile-font-size-14 system-font-size-20">GRECIANS WALLET: N<?php echo json_decode(getAPIBalance("GET","https://grecians.ng/api/v2/others/get_account_balance.php/?api_key=".$api_web_apikey["grecians.ng"],"",""),true)["wallet"]; ?></span><br>
      	<span class="mobile-font-size-14 system-font-size-20">RPIDATANG WALLET: N<?php echo json_decode(getAPIBalance("GET","https://www.rpidatang.com/api/user/",["Authorization: Token ".$api_web_apikey["rpidatang.com"]],""),true)["user"]["wallet_balance"]; ?></span><br>
		<span class="mobile-font-size-14 system-font-size-20">SUBVTU WALLET: N<?php echo json_decode(getAPIBalance("GET","https://www.subvtu.com/api/user/",["Authorization: Token ".$api_web_apikey["subvtu.com"]],""),true)["user"]["wallet_balance"]; ?></span><br>
      <span class="mobile-font-size-14 system-font-size-20">ABUMPAY WALLET: N<?php echo json_decode(getAPIBalance("POST","https://abumpay.com/api/details","",'{"token":"'.$api_web_apikey["abumpay.com"].'"}'),true)["balance"]; ?></span><br>
		
	</div>
</center>

<?php
function getAPIBalance($method,$url,$header,$json){
	$apiwalletBalance = curl_init($url);
	$apiwalletBalanceUrl = $url;
	curl_setopt($apiwalletBalance,CURLOPT_URL,$apiwalletBalanceUrl);
	curl_setopt($apiwalletBalance,CURLOPT_RETURNTRANSFER,true);
	if($method == "POST"){
		curl_setopt($apiwalletBalance,CURLOPT_POST,true);
	}
	
	if($method == "GET"){
	curl_setopt($apiwalletBalance,CURLOPT_HTTPGET,true);
	}
	
	if($header == true){
		curl_setopt($apiwalletBalance,CURLOPT_HTTPHEADER,$header);
	}
	if($json == true){
		curl_setopt($apiwalletBalance,CURLOPT_POSTFIELDS,$json);
	}
	curl_setopt($apiwalletBalance, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($apiwalletBalance, CURLOPT_SSL_VERIFYPEER, false);
	
	$GetAPIBalanceJSON = curl_exec($apiwalletBalance);
	return $GetAPIBalanceJSON;
	}

?>
<?php include("../include/admin-footer-html.php"); ?>
</body>
</html>