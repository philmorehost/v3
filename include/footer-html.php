<?php
	if(isset($_SESSION["user"])){
?>
<div class="footer-div-space"></div>
<div class="footer-menu-div bg-4">
	<a href="/dashboard.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/home-icon.svg"/><br/>Dashboard</button>
	</a>
	
	<a href="/airtime.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/airtime-icon.svg"/><br/>Airtime</button>
	</a>
		
	<a href="/sme-data.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/data-icon.svg"/><br/>SME Data</button>
	</a>
	
	<a href="/data-gifting.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/data-icon.svg"/><br/>Gifting Data</button>
	</a>
	
	<a href="/direct-data.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/data-icon.svg"/><br/>Direct Data</button>
	</a>

	<a href="/electricity.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/electricity-icon.svg"/><br/>Electric</button>
	</a>
	
	<a href="/cable.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/cable-icon.svg"/><br/>Cable TV</button>
	</a>
	
	<a href="/exam.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/exam-icon.png"/><br/>Exam PIN</button>
	</a>
	
	
	<a href="/sms.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/sms-icon.svg"/><br/>SMS</button>
	</a>
		
	<a href="/APIdoc/prices.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/wallet-icon.png"/><br/>Pricing</button>
	</a>
		
	<a href="/fund-wallet.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/add-fund.svg"/><br/>Fund Wallet</button>
	</a>
  	
	<a href="/payment-order.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/trans-icon.png"/><br/>My Payments</button>
	</a>
	
	<a href="/place-payment-order.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/cart-icon.png"/><br/>Submit Payment</button>
	</a>
  		
	<a href="/transaction.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/trans-icon.png"/><br/>Transactions</button>
	</a>
		
	<a href="/account-setting.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/account-setting-icon.png"/><br/>Settings</button>
	</a>
		
	<a href="/change-password.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/password-lock-icon.png"/><br/>PIN/Password</button>
	</a>
		
	<a href="/documentation.php">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/developer-icon.png"/><br/>Dev API</button>
	</a>
		
	<a onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/logout.php'}">
		<button style="height: 3.2rem;" class="color-8 bg-4 mobile-width-30 mobile-margin-left-1 mobile-margin-right-1" type="button"><img src="/images/logout-icon.png"/><br/>Logout</button>
	</a>
</div>
<?php
	}
?>
<!-- System Mode Div End -->
</div>