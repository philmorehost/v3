<?php
	if(isset($_SESSION["admin"])){
?>
<div class="footer-div-space"></div>
<div class="footer-menu-div bg-4">
	<a href="/admin/dashboard.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/home-icon.svg"/><br/>Admin Dashboard</button>
	</a>
  	<a href="/admin/site-setting.php?page=paymentorder">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/cart-icon.png"/><br/>Approve Payment</button>
	</a>
	<a href="/admin/api-balance.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/wallet-icon.png"/><br/>View API Balance</button>
	</a>
  	<a href="/admin/site-setting.php?page=user">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/account-setting-icon.png"/><br/>Manage Users</button>
	</a>
	<a href="/admin/airtime.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/airtime-icon.svg"/><br/>Airtime API</button>
	</a>
		
	<a href="/admin/sme-data.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/data-icon.svg"/><br/>SME Data API</button>
	</a>
	
	<a href="/admin/data-gifting.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/data-icon.svg"/><br/>Corporate Data API</button>
	</a>
	
	<a href="/admin/direct-data.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/data-icon.svg"/><br/>Direct Data API</button>
	</a>

	<a href="/admin/electricity.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/electricity-icon.svg"/><br/>Electric API</button>
	</a>
	
	<a href="/admin/cable.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/cable-icon.svg"/><br/>Cable TV API</button>
	</a>
	
	<a href="/admin/exam.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/exam-icon.png"/><br/>Exam PIN API</button>
	</a>
	
	<a href="/admin/sms.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/sms-icon.svg"/><br/>SMS API</button>
	</a>
					
	<a href="/admin/data-card.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/print-icon.svg"/><br/>Data Card API</button>
	</a>
	
	<a href="/admin/recharge-card-port.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/print-icon.svg"/><br/>Recharge Card API</button>
	</a>
	
	<a href="/admin/site-setting.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/account-setting-icon.png"/><br/>Site Settings</button>
	</a>

	<a href="/admin/site-setting.php?page=transaction">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/trans-icon.png"/><br/>All Transaction</button>
	</a>
	
	<a href="/admin/site-setting.php?page=fund">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/add-fund.svg"/><br/>Fund User</button>
	</a>
		
	<a href="/admin/db-integration.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/account-setting-icon.png"/><br/>Database Connection</button>
	</a>

	<a onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/admin/logout.php'}">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/logout-icon.png"/><br/>Logout</button>
	</a>
</div>
<?php
	}
?>
<!-- System Mode Div End -->
</div>