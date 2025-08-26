<div class="header-div-container">
	<img class="header-logo" src="/images/logo.png" />
		<?php
			if(isset($_SESSION["admin"])){
		?>
		<button class="header-fullname">
			<span style="font-weight: bold;" class="color-9 system-font-size-14 ">Welcome, <?php echo strtoupper($admin_details["fullname"]); ?></span>
		</button>
		<!--<button class="header-menu">
			<img src="/images/menu_icon.png" />
		</button>-->
		<?php
			}
		?>
</div>
<!--<div class="menu-div" style="display:none;">-->
<?php
	//if(isset($_SESSION["admin"])){
?>
		<!--<a href="/admin/dashboard.php">
		<button type="button"><img src="/images/home.png"/>Dashboard</button>
	</a>
	<a href="/admin/site-setting.php?page=paymentorder">
		<button type="button"><img src="/images/wallet.png"/>Approve Payment</button>
	</a>
	<a href="/admin/site-setting.php?page=transaction">
		<button type="button"><img src="/images/sms.png"/>All Transactions</button>
	</a>
  <a href="/admin/site-setting.php?page=user">
		<button type="button"><img src="/images/data.png"/>View All Users</button>
	</a>
  <a href="/admin/site-setting.php?page=fund">
		<button type="button"><img src="/images/setting.png"/>Credit Users</button>
	</a>
	<a href="/admin/airtime.php">
		<button type="button"><img src="/images/airtime.png"/>Airtime API</button>
	</a>
	
	<a href="/admin/direct-data.php">
		<button type="button"><img src="/images/data.png"/>Direct Data API</button>
	</a>
	
	<a href="/admin/sme-data.php">
		<button type="button"><img src="/images/data.png"/>SME Data API</button>
	</a>
	
	<a href="/admin/data-gifting.php">
		<button type="button"><img src="/images/data.png"/>Data Gifting API</button>
	</a>
	
	<a href="/admin/electricity.php">
		<button type="button"><img src="/images/electricity.png"/>Electricity API</button>
	</a>
	
	<a href="/admin/cable.php">
		<button type="button"><img src="/images/cable.png"/>Cable API</button>
	</a>
	
	<a href="/admin/exam.php">
		<button type="button"><img src="/images/exam.png"/>Exam API</button>
	</a>
	
	<a href="/admin/sms.php">
		<button type="button"><img src="/images/sms.png"/>SMS API</button>
	</a>
	
	<a href="/admin/insurance.php">
		<button type="button"><img src="/images/insurance.png"/>Insurance API</button>
	</a>
	
	<a href="/admin/recharge-card-port.php">
		<button type="button"><img src="/images/airtime.png"/>Recharge Card Printing API</button>
	</a>
	
	<a href="/admin/data-card.php">
		<button type="button"><img src="/images/data.png"/>Data Card API</button>
	</a>
	
	<a href="/admin/site-setting.php">
		<button type="button"><img src="/images/setting.png"/>Site Setting</button>
	</a>
	
	<a onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/admin/logout.php'}">
		<button type="button"><img src="/images/logout.png"/>Logout</button>
	</a>-->
<?php
	//}else{
?>
<!--<center>
	<a href="/admin/login.php">
		<button type="button"><img src="/images/log.png"/>Login</button> <br/>
	</a>
</center>-->
<?php
	//}
?>
<!--</div>

<div class="system-menu-div">-->
<?php
	//if(isset($_SESSION["admin"])){
?>
<!--<center>
		<a href="/admin/dashboard.php">
		<button type="button"><img src="/images/home.png"/>Dashboard</button>
	</a>
	<a href="/admin/site-setting.php?page=paymentorder">
		<button type="button"><img src="/images/wallet.png"/>Approve Payment</button>
	</a>
	<a href="/admin/site-setting.php?page=transaction">
		<button type="button"><img src="/images/sms.png"/>All Transactions</button>
	</a>
  <a href="/admin/site-setting.php?page=fund">
		<button type="button"><img src="/images/sms.png"/>Credit Users</button>
	</a>
  <a href="/admin/site-setting.php?page=user">
		<button type="button"><img src="/images/data.png"/>View All Users</button>
	</a>
	<a href="/admin/airtime.php">
		<button type="button"><img src="/images/airtime.png"/>Airtime API</button>
	</a>
	
	<a href="/admin/direct-data.php">
		<button type="button"><img src="/images/data.png"/>Direct Data API</button>
	</a>
	
	<a href="/admin/sme-data.php">
		<button type="button"><img src="/images/data.png"/>SME Data API</button>
	</a>
	
	<a href="/admin/data-gifting.php">
		<button type="button"><img src="/images/data.png"/>Data Gifting API</button>
	</a>
	
	<a href="/admin/electricity.php">
		<button type="button"><img src="/images/electricity.png"/>Electricity API</button>
	</a>
	
	<a href="/admin/cable.php">
		<button type="button"><img src="/images/cable.png"/>Cable API</button>
	</a>
	
	<a href="/admin/exam.php">
		<button type="button"><img src="/images/exam.png"/>Exam API</button>
	</a>
	
	<a href="/admin/sms.php">
		<button type="button"><img src="/images/sms.png"/>SMS API</button>
	</a>
	
	<a href="/admin/insurance.php">
		<button type="button"><img src="/images/insurance.png"/>Insurance API</button>
	</a>
	
	<a href="/admin/recharge-card-port.php">
		<button type="button"><img src="/images/airtime.png"/>Recharge Card Printing API</button>
	</a>
	
	<a href="/admin/data-card.php">
		<button type="button"><img src="/images/data.png"/>Data Card API</button>
	</a>
	
	<a href="/admin/site-setting.php">
		<button type="button"><img src="/images/setting.png"/>Site Setting</button>
	</a>
	
	<a onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/admin/logout.php'}">
		<button type="button"><img src="/images/logout.png"/>Logout</button>
	</a>
</center>-->
<?php
	//}else{
?>
<!--<center>
	<a href="/admin/login.php">
		<button type="button"><img src="/images/log.png"/>Login</button> <br/>
	</a>
</center>-->
<?php
	//}
?>

<div class="system-left-menu color-5 bg-6 system-width-24 system-height-85 system-padding-top-3 system-padding-bottom-3">
<?php
	if(isset($_SESSION["admin"])){
?>
	<center>
		<a href="/admin/dashboard.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/home-icon.svg"/>Dashboard</button> <br/>
		</a>
		
		<a href="/admin/api-balance.php">
			<button style="text-align: left;" class="color-8 bg-3 system-width-100 system-padding-left-10" type="button"><img src="/images/wallet-icon.png"/>View API Balance</button> <br/>
		</a>
		
		<a onclick="openManageAPILists();">
			<button style="text-align: left;" class="color-8 bg-3 system-width-100 system-padding-left-10" type="button"><img src="/images/cart-icon.png"/>Manage API<img style="width: 8%; height: auto; margin-top: 1.5%; float: right;" src="/images/dropdown-w.png"/></button> <br/>
		</a>
				
		<a href="/admin/airtime.php" id="manageapilist-1" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-3 system-width-90 system-padding-left-10" type="button"><img src="/images/airtime-icon.svg"/>Airtime</button> <br/>
		</a>
		
		<a href="/admin/cable.php" id="manageapilist-2" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-3 system-width-90 system-padding-left-10" type="button"><img src="/images/cable-icon.svg"/>Cable TV</button> <br/>
		</a>

		<a href="/admin/data-card.php" id="manageapilist-3" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-3 system-width-90 system-padding-left-10" type="button"><img src="/images/print-icon.svg"/>Data Card Printing</button> <br/>
		</a>
		
		<a href="/admin/recharge-card-port.php" id="manageapilist-4" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-3 system-width-90 system-padding-left-10" type="button"><img src="/images/print-icon.svg"/>Recharge Card Printing</button> <br/>
		</a>

		<a href="/admin/sme-data.php" id="manageapilist-5" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-3 system-width-90 system-padding-left-10" type="button"><img src="/images/data-icon.svg"/>SME Data</button> <br/>
		</a>
		
		<a href="/admin/data-gifting.php" id="manageapilist-6" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-3 system-width-90 system-padding-left-10" type="button"><img src="/images/data-icon.svg"/>Corporate Gifting</button> <br/>
		</a>

		<a href="/admin/direct-data.php" id="manageapilist-7" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-3 system-width-90 system-padding-left-10" type="button"><img src="/images/data-icon.svg"/>Direct Data</button> <br/>
		</a>
		
		<a href="/admin/electricity.php" id="manageapilist-8" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-3 system-width-90 system-padding-left-10" type="button"><img src="/images/electricity-icon.svg"/>Electric</button> <br/>
		</a>

		<a href="/admin/exam.php" id="manageapilist-9" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-3 system-width-90 system-padding-left-10" type="button"><img src="/images/exam-icon.png"/>Exam PIN</button> <br/>
		</a>

		<a href="/admin/sms.php" id="manageapilist-10" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-3 system-width-90 system-padding-left-10" type="button"><img src="/images/sms-icon.svg"/>Bulk SMS</button> <br/>
		</a>

		<a href="/admin/insurance.php" id="manageapilist-11" style="display: none;">
			<button style="text-align: left;" class="color-8 bg-3 system-width-90 system-padding-left-10" type="button"><img src="/images/insurance-icon.png"/>Insurance</button> <br/>
		</a>

		<script>
			function openManageAPILists(){
				if(document.getElementById('manageapilist-1').style.display == "none"){
					for(x=1; x<1+12; x++){
						document.getElementById('manageapilist-'+x).style.display = "inline-block";
					}
				}else{
					for(x=1; x<1+12; x++){
						document.getElementById('manageapilist-'+x).style.display = "none";
					}
				}
			}				
		</script>

		<a href="/admin/site-setting.php?page=user">
			<button style="text-align: left;" class="color-8 bg-3 system-width-100 system-padding-left-10" type="button"><img src="/images/account-setting-icon.png"/>Manage Users</button> <br/>
		</a>
		
		<a href="/admin/site-setting.php">
			<button style="text-align: left;" class="color-8 bg-3 system-width-100 system-padding-left-10" type="button"><img src="/images/setting-icon.png"/>Site Settings</button> <br/>
		</a>
		
		<a href="/admin/site-setting.php?page=transaction">
			<button style="text-align: left;" class="color-8 bg-3 system-width-100 system-padding-left-10" type="button"><img src="/images/trans-icon.png"/>All Transactions</button> <br/>
		</a>
		
		<a href="/admin/site-setting.php?page=fund">
			<button style="text-align: left;" class="color-8 bg-3 system-width-100 system-padding-left-10" type="button"><img src="/images/add-fund.svg"/>Fund User</button> <br/>
		</a>
		
		<a href="/admin/site-setting.php?page=payment">
			<button style="text-align: left;" class="color-8 bg-3 system-width-100 system-padding-left-10" type="button"><img src="/images/exam-icon.png"/>Payment Gateway</button> <br/>
		</a>
		
		<a href="/admin/site-setting.php?page=paymentorder">
			<button style="text-align: left;" class="color-8 bg-3 system-width-100 system-padding-left-10" type="button"><img src="/images/cart-icon.png"/>Approve Payment</button> <br/>
		</a>
		
		<a href="/admin/db-integration.php">
			<button style="text-align: left;" class="color-8 bg-3 system-width-100 system-padding-left-10" type="button"><img src="/images/setting-icon.png"/>Database Connection</button> <br/>
		</a>
		
		<a onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/admin/logout.php'}">
			<button style="text-align: left;" class="color-8 bg-3 system-width-100 system-padding-left-10" type="button"><img src="/images/logout-icon.png"/>Logout</button>
		</a>

		<a>
			<button class="color-9 bg-2 system-width-100" type="button">Main V3.0</button> <br/>
		</a>
				
	</center>
<?php
	}else{
?>
	<center>
		<a href="/admin/login.php">
			<button style="text-align: left;" class="color-8 bg-4 system-width-100 system-padding-left-10" type="button"><img src="/images/profile-icon.svg"/>Sign In</button> <br/>
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
	httpDestroyTransactionText.open("GET","./../include/destroy-transaction-session.php",true);
	httpDestroyTransactionText.setRequestHeader("Content-Type","application/json");
	httpDestroyTransactionText.send();
	
},3000);
</script>
<div class="header-div-space"></div>
<!-- System Mode Div Begin -->
<div class="system-right-menu mobile-width-100 system-width-74 system-height-100 system-margin-left-24">