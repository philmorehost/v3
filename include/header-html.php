<?php
	if($conn_server_db == true){
		$get_notificationMessage = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM user_message"));
	}
?>
<div class="header-div-container">
	<img class="header-logo" src="/images/logo.png" />
		<button class="header-fullname">
			<span style="font-weight: bold;" class="color-9 system-font-size-14 "><?php echo strtoupper($user_details["firstname"]." ".$user_details["lastname"]); ?></span>
		</button>
		
		<button class="header-notify" style="cursor: pointer;" onclick="notifyPush();" title='Dear Customer,<?php echo "\n•".str_replace("<br/>","\n",$get_notificationMessage["user_alert"])."\n•".str_replace("<br/>","\n",$get_notificationMessage["user_static"]); ?>'>
			<img style="pointer-events:none;" class="mobile-width-100 system-width-100" src="/images/notification-icon.gif" />
		</button>
		<script>
			function notifyPush() {
				let notificationTextMsg = `Dear Customer,<?php echo "<br/>•".str_replace("\n","<br/>",$get_notificationMessage["user_alert"])."<br/>•".str_replace("\n","<br/>",$get_notificationMessage["user_static"]); ?>`;
				alertPopUp(notificationTextMsg);
			}
		</script>
		<!--<button class="header-menu">
			<img src="/images/menu_icon.png" />
		</button>-->
</div>
<!--<div class="menu-div" style="display:none;">-->
<?php
//	if(isset($_SESSION["user"])){
?>
	<!--<a href="/dashboard.php">
		<button type="button"><img src="/images/home.png"/>Dashboard</button>
	</a>
	<a href="/APIdoc/prices.php">
		<button type="button"><img src="/images/wallet.png"/>View Prices</button>
	</a>
	<a href="/airtime.php">
		<button type="button"><img src="/images/airtime.png"/>Airtime</button>
	</a>
	
	<a href="/direct-data.php">
		<button type="button"><img src="/images/data.png"/>Direct Data</button>
	</a>
	
	<a href="/sme-data.php">
		<button type="button"><img src="/images/data.png"/>SME Data</button>
	</a>
	
	<a href="/data-gifting.php">
		<button type="button"><img src="/images/data.png"/>Data Gifting</button>
	</a>
	
	<a href="/electricity.php">
		<button type="button"><img src="/images/electricity.png"/>Electricity</button>
	</a>
	
	<a href="/cable.php">
		<button type="button"><img src="/images/cable.png"/>Cable Tv</button>
	</a>
	
	<a href="/exam.php">
		<button type="button"><img src="/images/exam.png"/>Exam Pin</button>
	</a>
	
	<a href="/sms.php">
		<button type="button"><img src="/images/sms.png"/>Bulk SMS</button>
	</a>
  	
  	<a href="/send-money.php?page=user">
		<button type="button"><img src="/images/money.png"/>Share Fund</button>
	</a>
	
	<a href="/insurance.php">
		<button type="button"><img src="/images/insurance.png"/>Insurance</button>
	</a>
	
	<a href="/recharge-card-printing.php">
		<button type="button"><img src="/images/airtime.png"/>Recharge Card Printing</button>
	</a>
	
	<a href="/data-card.php">
		<button type="button"><img src="/images/data.png"/>Data Card</button>
	</a>
	
	<a href="/fund-wallet.php">
		<button type="button"><img src="/images/wallet.png"/>Fund Wallet</button>
	</a>
	
	<a href="/place-payment-order.php">
		<button type="button"><img src="/images/wallet.png"/>Place Payment Order</button>
	</a>

	<a href="/payment-order.php">
		<button type="button"><img src="/images/wallet.png"/>Payment Orders</button>
	</a>

	<a href="/transaction.php">
		<button type="button"><img src="/images/transaction.png"/>Transactions</button>
	</a>
	

	<a href="/account-setting.php">
		<button type="button"><img src="/images/setting.png"/>Account Setting</button>
	</a>
	
	<a href="/documentation.php">
		<button type="button"><img src="/images/developer.png"/>Developer API</button>
	</a>
	
	<a onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/logout.php'}">
		<button type="button"><img src="/images/logout.png"/>Logout</button>
	</a>-->
<?php
//	}else{
?>
<!--<center>
	<a href="/register.php">
		<button type="button"><img src="/images/reg.png"/>Register</button>
	</a>
	
	<a href="/login.php">
		<button type="button"><img src="/images/log.png"/>Login</button>
	</a>
</center>-->
<?php
//	}
?>
	
</div>


<!--<div class="system-menu-div">-->
<?php
//	if(isset($_SESSION["user"])){
?>
<!--<center>
	<a href="/dashboard.php">
		<button type="button"><img src="/images/home.png"/>Dashboard</button> <br/>
	</a>
	<a href="/APIdoc/prices.php">
		<button type="button"><img src="/images/wallet.png"/>View Prices</button> <br/>
	</a>
	<a href="/airtime.php">
		<button type="button"><img src="/images/airtime.png"/>Airtime</button> <br/>
	</a>
	
	<a href="/direct-data.php">
		<button type="button"><img src="/images/data.png"/>Direct Data</button> <br/>
	</a>
	
	<a href="/sme-data.php">
		<button type="button"><img src="/images/data.png"/>SME Data</button> <br/>
	</a>
	
	<a href="/data-gifting.php">
		<button type="button"><img src="/images/data.png"/>Data Gifting</button> <br/>
	</a>
	
	<a href="/electricity.php">
		<button type="button"><img src="/images/electricity.png"/>Electricity</button> <br/>
	</a>
	
	<a href="/cable.php">
		<button type="button"><img src="/images/cable.png"/>Cable Tv</button> <br/>
	</a>
	
	<a href="/exam.php">
		<button type="button"><img src="/images/exam.png"/>Exam Pin</button> <br/>
	</a>
	
	<a href="/sms.php">
		<button type="button"><img src="/images/sms.png"/>Bulk SMS</button> <br/>
	</a>
	
	<a href="/insurance.php">
		<button type="button"><img src="/images/insurance.png"/>Insurance</button> <br/>
	</a>
	
	<a href="/send-money.php?page=user">
		<button type="button"><img src="/images/money.png"/>Share Fund</button> <br/>
	</a>
	
	<a href="/fund-wallet.php">
		<button type="button"><img src="/images/wallet.png"/>Fund Wallet</button> <br/>
	</a>

	<a href="/recharge-card-printing.php">
		<button type="button"><img src="/images/airtime.png"/>Recharge Card Printing</button>
	</a>
	
	<a href="/data-card.php">
		<button type="button"><img src="/images/data.png"/>Data Card</button>
	</a>
	
	<a href="/place-payment-order.php">
		<button type="button"><img src="/images/wallet.png"/>Place Payment Order</button>
	</a>
	
	<a href="/payment-order.php">
		<button type="button"><img src="/images/wallet.png"/>Payment Orders</button>
	</a>

	<a href="/transaction.php">
		<button type="button"><img src="/images/transaction.png"/>Transactions</button> <br/>
	</a>
	
	<a href="/change-password.php">
		<button type="button"><img src="/images/setting.png"/>Pin & Password</button> <br/>
	</a>
	
	<a href="/account-setting.php">
		<button type="button"><img src="/images/setting.png"/>Account Setting</button> <br/>
	</a>
  <a href="/change-password.php">
		<button type="button"><img src="/images/setting.png"/>Change PIN & Password</button>
	</a>
	
	<a href="/documentation.php">
		<button type="button"><img src="/images/developer.png"/>Developer API</button> <br/>
	</a>
	
	<a onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/logout.php'}">
		<button type="button"><img src="/images/logout.png"/>Logout</button>
	</a>
</center>-->
<?php
//	}else{
?>
<!--<center>
	<a href="/register.php">
		<button type="button"><img src="/images/reg.png"/>Register</button> <br/>
	</a>
	
	<a href="/login.php">
		<button type="button"><img src="/images/log.png"/>Login</button> <br/>
	</a>
</center>-->
<?php
//	}
?>

<div class="system-left-menu color-5 bg-6 system-width-20 system-height-85 system-padding-top-3 system-padding-bottom-3">
<?php
	if(isset($_SESSION["user"])){
?>
	<center>
		<a href="/dashboard.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/home-icon.svg"/>Dashboard</button> <br/>
		</a>
		
		<a onclick="openProfileLists();">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/share-icon.svg"/>Funds<img style="width: 8%; height: auto; margin-top: 1.5%; float: right;" src="/images/dropdown-w.png"/></button> <br/>
		</a>
				
		<a href="/send-money.php?page=user" id="profilelist-1" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-4 system-width-90 system-padding-left-10" type="button"><img src="/images/share-icon.svg"/>Share Fund</button> <br/>
		</a>
		
		<a href="/fund-wallet.php" id="profilelist-2" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-4 system-width-90 system-padding-left-10" type="button"><img src="/images/add-fund.svg"/>Fund Wallet</button> <br/>
		</a>

		<script>
			function openProfileLists(){
				if(document.getElementById('profilelist-1').style.display == "none"){
					document.getElementById('profilelist-1').style.display = "inline-block";
					document.getElementById('profilelist-2').style.display = "inline-block";
					document.getElementById('profilelist-3').style.display = "inline-block";
				}else{
					document.getElementById('profilelist-1').style.display = "none";
					document.getElementById('profilelist-2').style.display = "none";
					document.getElementById('profilelist-3').style.display = "none";
				}
			}				
		</script>

		<a href="/APIdoc/prices.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/wallet-icon.png"/>Pricing</button> <br/>
		</a>
		<a href="/airtime.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/airtime-icon.svg"/>Airtime</button> <br/>
		</a>
		
		<a onclick="openDataLists();">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/internet-icon.png"/>Internet<img style="width: 8%; height: auto; margin-top: 1.5%; float: right;" src="/images/dropdown-w.png"/></button> <br/>
		</a>
		
		<a href="/sme-data.php" id="datalist-1" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-4 system-width-90 system-padding-left-10" type="button"><img src="/images/data-icon.svg"/>SME Data</button> <br/>
		</a>
		
		<a href="/direct-data.php" id="datalist-2" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-4 system-width-90 system-padding-left-10" type="button"><img src="/images/data-icon.svg"/>Direct Data</button> <br/>
		</a>
				
		<a href="/data-gifting.php" id="datalist-3" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-4 system-width-90 system-padding-left-10" type="button"><img src="/images/data-icon.svg"/>Data Gifting</button> <br/>
		</a>
		
		<script>
			function openDataLists(){
				if(document.getElementById('datalist-1').style.display == "none"){
					document.getElementById('datalist-1').style.display = "inline-block";
					document.getElementById('datalist-2').style.display = "inline-block";
					document.getElementById('datalist-3').style.display = "inline-block";
				}else{
					document.getElementById('datalist-1').style.display = "none";
					document.getElementById('datalist-2').style.display = "none";
					document.getElementById('datalist-3').style.display = "none";
				}
			}
		</script>
		<a href="/transaction.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/trans-icon.png"/>Transactions</button> <br/>
		</a>
		<a href="/electricity.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/electricity-icon.svg"/>Electric</button> <br/>
		</a>
		
		<a href="/cable.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/cable-icon.svg"/>Cable Tv</button> <br/>
		</a>
		
		<a href="/exam.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/exam-icon.png"/>Exam Pin</button> <br/>
		</a>
		
		<a href="/sms.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/sms-icon.svg"/>Bulk SMS</button> <br/>
		</a>
		
		<a href="/insurance.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/insurance-icon.png"/>Insurance</button> <br/>
		</a>
		
		<a onclick="openCardsLists();">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/print-icon.svg"/>Card Printing<img style="width: 8%; height: auto; margin-top: 1.5%; float: right;" src="/images/dropdown-w.png"/></button> <br/>
		</a>
				
		<a href="/data-card.php" id="cardslist-1" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-4 system-width-90 system-padding-left-10" type="button"><img src="/images/data-icon.svg"/>Data Card</button>
		</a>
				
		<a href="/recharge-card-printing.php" id="cardslist-2" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-4 system-width-90 system-padding-left-10" type="button"><img src="/images/airtime-icon.svg"/>Recharge Card</button>
		</a>
		
		<script>
			function openCardsLists(){
				if(document.getElementById('cardslist-1').style.display == "none"){
					document.getElementById('cardslist-1').style.display = "inline-block";
					document.getElementById('cardslist-2').style.display = "inline-block";
				}else{
					document.getElementById('cardslist-1').style.display = "none";
					document.getElementById('cardslist-2').style.display = "none";
				}
			}				
		</script>
		
		<a onclick="openPaymentLists();">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/cart-icon.png"/>Payment Order<img style="width: 8%; height: auto; margin-top: 1.5%; float: right;" src="/images/dropdown-w.png"/></button> <br/>
		</a>
		
		<a href="/payment-order.php" id="paymentlist-1" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-4 system-width-90 system-padding-left-10" type="button"><img src="/images/trans-icon.png"/>My Payments</button>
		</a>
		
		<a href="/place-payment-order.php" id="paymentlist-2" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-4 system-width-90 system-padding-left-10" type="button"><img src="/images/cart-icon.png"/>Submit Payment</button>
		</a>
				
		<script>
			function openPaymentLists(){
				if(document.getElementById('paymentlist-1').style.display == "none"){
					document.getElementById('paymentlist-1').style.display = "inline-block";
					document.getElementById('paymentlist-2').style.display = "inline-block";
				}else{
					document.getElementById('paymentlist-1').style.display = "none";
					document.getElementById('paymentlist-2').style.display = "none";
				}
			}				
		</script>
		
		<a onclick="openSettingsLists();">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/setting-icon.png"/>Settings<img style="width: 8%; height: auto; margin-top: 1.5%; float: right;" src="/images/dropdown-w.png"/></button> <br/>
		</a>
		
		<a href="/account-setting.php" id="settingslist-1" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-4 system-width-90 system-padding-left-10" type="button"><img src="/images/account-setting-icon.png"/>Account</button> <br/>
		</a>
		
		<a href="/change-password.php" id="settingslist-2" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-4 system-width-90 system-padding-left-10" type="button"><img src="/images/password-lock-icon.png"/>Pin & Password</button> <br/>
		</a>
		
		<script>
			function openSettingsLists(){
				if(document.getElementById('settingslist-1').style.display == "none"){
					document.getElementById('settingslist-1').style.display = "inline-block";
					document.getElementById('settingslist-2').style.display = "inline-block";
				}else{
					document.getElementById('settingslist-1').style.display = "none";
					document.getElementById('settingslist-2').style.display = "none";
				}
			}				
		</script>
		
		<a href="/documentation.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/developer-icon.png"/>API Documentation</button> <br/>
		</a>
		
		<a onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/logout.php'}">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/logout-icon.png"/>Logout</button>
		</a>
				
	</center>
<?php
	}else{
?>
	<center>
		<a href="/register.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/profile-icon.svg"/>Sign Up</button> <br/>
		</a>
		
		<a href="/login.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/profile-icon.svg"/>Sign In</button> <br/>
		</a>
		
		<a href="/APIdoc/prices.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/wallet-icon.png"/>Pricing</button> <br/>
		</a>
		
		<a href="/documentation.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/developer-icon.png"/>API Documentation</button> <br/>
		</a>
		
	</center>
<?php
	}
?>

</div>

<script>

/*document.getElementsByClassName("header-menu")[0].onclick = function(){
	if(document.getElementsByClassName("menu-div")[0].style.display == "none"){
		document.getElementsByClassName("menu-div")[0].style.display = "inline-block";
		document.getElementsByClassName("header-menu")[0].style.backgroundColor = "orange";
	}else{
		document.getElementsByClassName("menu-div")[0].style.display = "none";
		document.getElementsByClassName("header-menu")[0].style.backgroundColor = "";
	}
}*/

//Destroy Transaction Session
setTimeout(function(){
	var httpDestroyTransactionText = new XMLHttpRequest();
	httpDestroyTransactionText.open("GET","./include/destroy-transaction-session.php",true);
	httpDestroyTransactionText.setRequestHeader("Content-Type","application/json");
	httpDestroyTransactionText.send();
	
},3000);
</script>
<div class="header-div-space"></div>
<!-- System Mode Div Begin -->
<div class="system-right-menu mobile-width-100 system-width-74 system-height-100 system-margin-left-24">