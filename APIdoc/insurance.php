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
	<span class="mobile-font-size-14 system-font-size-16">Third-party Insurance Integration</span><br>
	<span class="mobile-font-size-12 system-font-size-14">
		<b>Method: HTTP GET => Endpoint:</b><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;"><?php echo $w_host; ?>/api/insurance.php</div><br>
		
		<b>Parameters:</b><br>
		<b>token:</b> Your Developer APIKEY<br>
		<b>type:</b> motor<br>
		<b>variation:</b> <cite>1</cite> for Private car, <cite>2</cite> for Commercial car, <cite>3</cite> for Tricycle<br>
		<b>fullname:</b> Owner Name<br>
		<b>engine_number:</b> Motor Vehicle Engine Number<br>
		<b>chasis_number:</b> Motor Vehicle Chasis Number<br>
		<b>plate_number:</b> Vehicle Plate Number<br>
		<b>vehicle_make:</b> Vehicle Make<br>
		<b>vehicle_color:</b> Vehicle Color<br>
		<b>vehicle_model:</b> Vehicle  Model<br>
		<b>year:</b> Year Of Make<br>
		<b>address:</b> Home Address<br>
		<b>phone_number:</b> Owner Phone Number<br>
		
		<cite>Example: </cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<cite><?php echo $w_host; ?>/api/insurance.php?token=4us6j3ha8h0w&type=motor&variation=1&fullname=Abdul&engine_number=42hs25gXXXXXXXXX&chasis_number=5ydsa72fsgXXXXXX&plate_number=1234Kwara567&vehicle_make=mazda&vehicle_color=ash&vehicle_model=XXXX&year=XXXX&address=Ilorin&phone_number=0812423XXXX</cite>
		</div><br>
		<cite>EXPECTED SUCCESSFUL RESPONSE</cite><br>
		<div class="scrollable-div" style="display:inline-block; padding:5px 2px 5px 2px; border-radius:5px; border:1px solid var(--color-8); font-weight:bold;">
			<?php echo json_encode(array("code"=>200,"ref"=>"576","details"=>"Insurance Details and Certificate Download Link","desc"=>"Transaction Successful"),true); ?>
		</div>
	</span><br>
	<?php
		//GET EACH insurance API WEBSITE
		$get_motor_insurance_insurance_running_api = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT website, discount_1, discount_2, discount_3, discount_4 FROM insurance_subscription_running_api WHERE subscription_name='motor_insurance'"));
		
		$motor_type = array("private","commercial","tricycles");
		$motor_insurance_price = array(1 => 3000,2 => 5000,3 => 1500);
	?>
	<div class="scrollable-div">
		<table class="table-style-1 table-font-size-1">
			<tr>
				<th>Insurance Type</th><th>Car Type</th><th>Variation</th><th>Smart Earner (N)</th><th>VIP Earner (N)</th><th>VIP Vendor (N)</th><th>Agent/API User (N)</th>
			</tr>
			
			<?php
				foreach($motor_insurance_price as $key => $motor_insurance){
					echo '<tr>
						<td>motor</td><td>'.ucwords($motor_type[$key-1]).'</td><td>'.$key.'</td><td>'.($motor_insurance-($motor_insurance*$get_motor_insurance_insurance_running_api["discount_1"]/100)).'</td><td>'.($motor_insurance-($motor_insurance*$get_motor_insurance_insurance_running_api["discount_2"]/100)).'</td><td>'.($motor_insurance-($motor_insurance*$get_motor_insurance_insurance_running_api["discount_3"]/100)).'</td><td>'.($motor_insurance-($motor_insurance*$get_motor_insurance_insurance_running_api["discount_4"]/100)).'</td>
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