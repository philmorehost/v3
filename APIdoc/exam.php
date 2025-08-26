<?php session_start();
	if(!isset($_SESSION["user"])){
		header("Location: /login.php");
	}else{
		include("./../include/config.php");
		include("./../include/user-details.php");
	}
	
	//GET USER DETAILS
	$user_session = $_SESSION["user"];
	$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));
	
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; " />
<meta name="theme-color" content="skyblue" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="/css/site.css">
<script src="/scripts/auth.js"></script>
</head>
<body>
<?php include("./../include/header-html.php"); ?>

<center>
<div style="text-align: left; overflow: auto;" class="container-box color-8 bg-4 mobile-width-90 system-width-90 mobile-margin-top-2 system-margin-top-2 mobile-padding-top-2 system-padding-top-2 mobile-padding-right-2 system-padding-right-2 mobile-padding-left-2 system-padding-left-2 mobile-padding-bottom-2 system-padding-bottom-2">
	<a style="text-decoration: none;" href="/documentation.php"><img style="float: left;" class="" src="/images/back-arrow.svg" /></a><br><br>
	<span class="mobile-font-size-14 system-font-size-16">Examination Pin Integration</span><br>
	<span class="mobile-font-size-12 system-font-size-14">
		<b>Method: HTTP GET => Endpoint:</b><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;"><?php echo $w_host; ?>/api/exam.php</div><br>
		
		<b>Parameters:</b><br>
		<b>token:</b> Your Developer APIKEY<br>
		<b>package:</b> Exam Type e.g <cite>waec,neco,nabteb</cite><br>
		
		<cite>Example: </cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<cite><?php echo $w_host; ?>/api/exam.php?token=gw18ys5tfw&package=waec</cite>
		</div><br>
		<cite>EXPECTED SUCCESSFUL RESPONSE</cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<?php echo json_encode(array("code"=>200,"ref"=>"4742972327833389934","desc"=>"Transaction Successful, Waec Pin: 362362235212786332"),true); ?>
		</div>
	</span><br>
	<?php
		//GET ALL EXAM API PACKAGE, PRICE
		$waec_package_price = array("waec"=>"3100");
		$neco_package_price = array("neco"=>"1100");
		$nabteb_package_price = array("nabteb"=>"1100");
		
		//GET EACH exam API WEBSITE
		$get_waec_exam_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM exam_pin_running_api WHERE pin_name='waec'"));
		$get_neco_exam_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM exam_pin_running_api WHERE pin_name='neco'"));
		$get_nabteb_exam_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM exam_pin_running_api WHERE pin_name='nabteb'"));
		
	?>
	<div class="scrollable-div">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Exam</th><th>Package</th><th>Plan Code(qty)</th><th>Smart Earner (N)</th><th>VIP Earner (N)</th><th>VIP Vendor (N)</th><th>Agent/API User (N)</th>
			</tr>
			
			<?php
				foreach($waec_package_price as $key => $waec_price){
					echo '<tr>
						<td>waec</td><td>'.ucwords(str_replace(["_"]," ",$key)).' Pin</td><td>'.$key.'</td><td>'.($waec_price-($waec_price*($get_waec_exam_running_api["discount_1"]/100))).'</td><td>'.($waec_price-($waec_price*($get_waec_exam_running_api["discount_2"]/100))).'</td><td>'.($waec_price-($waec_price*($get_waec_exam_running_api["discount_3"]/100))).'</td><td>'.($waec_price-($waec_price*($get_waec_exam_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			?>
			
			<?php
				foreach($neco_package_price as $key => $neco_price){
					echo '<tr>
						<td>neco</td><td>'.ucwords(str_replace(["_"]," ",$key)).' Pin</td><td>'.$key.'</td><td>'.($neco_price-($neco_price*($get_neco_exam_running_api["discount_1"]/100))).'</td><td>'.($neco_price-($neco_price*($get_neco_exam_running_api["discount_2"]/100))).'</td><td>'.($neco_price-($neco_price*($get_neco_exam_running_api["discount_3"]/100))).'</td><td>'.($neco_price-($neco_price*($get_neco_exam_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			?>
			
			<?php
				foreach($nabteb_package_price as $key => $nabteb_price){
					echo '<tr>
						<td>nabteb</td><td>'.ucwords(str_replace(["_"]," ",$key)).' Pin</td><td>'.$key.'</td><td>'.($nabteb_price-($nabteb_price*($get_nabteb_exam_running_api["discount_1"]/100))).'</td><td>'.($nabteb_price-($nabteb_price*($get_nabteb_exam_running_api["discount_2"]/100))).'</td><td>'.($nabteb_price-($nabteb_price*($get_nabteb_exam_running_api["discount_3"]/100))).'</td><td>'.($nabteb_price-($nabteb_price*($get_nabteb_exam_running_api["discount_4"]/100))).'</td>
					</tr>';
				}
			?>
			
		</table>
	</div>
</div>
</center>

<?php include("./../include/footer-html.php"); ?>
</body>
</html>