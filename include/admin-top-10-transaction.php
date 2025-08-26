<?php
	include("gateway-apikey.php");
	include("requery-transaction.php");
?>
<form method="get" action="/admin/site-setting.php">
	<input hidden name="page" value="transaction">
	<input name="search" value="<?php echo trim(strip_tags($_GET["search"])); ?>" type="text" class="input-box mobile-width-35 system-width-30" placeholder="Search by: Reference, Meter No"> <button id="" class="button-box color-8 bg-6 mobile-font-size-13 system-font-size-16 mobile-width-20 system-width-15">Search</button>
	<a href="/admin/site-setting.php?page=transaction">
		<button style="float: right;" class="button-box color-8 bg-6 mobile-width-20 system-width-10 mobile-margin-right-7 system-margin-right-3 mobile-padding-left-1 system-padding-left-1" type="button">View All</button> <br/>
	</a>
</form>

<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
<table class="table-style-1">
<tr>
	<th>Email</th><th>Reference</th><th>Amount (Naira)</th><th>Status</th><th>Transaction Details</th><th>Type</th><th>Date</th>
</tr>
	<?php
		$select_transaction_history = mysqli_query($conn_server_db,"SELECT email, id, amount, status, description, transaction_type, website, transaction_date FROM transaction_history ORDER BY transaction_date DESC LIMIT 10");
		if(mysqli_num_rows($select_transaction_history) > 0){
			while($transaction_details = mysqli_fetch_assoc($select_transaction_history)){
				
				echo "<tr>
					<td>".$transaction_details["email"]."</td><td>".$transaction_details["id"]."</td><td>".$transaction_details["amount"]."</td><td>".ucwords($transaction_details["status"])."</td><td>".$transaction_details["description"]."</td><td>".ucwords(str_replace(["-","_"]," ",$transaction_details["transaction_type"]))."</td><td>".$transaction_details["transaction_date"]."</td>
				</tr>";
			}
		}
	?>
<tr>
	<th>Email</th><th>Reference</th><th>Amount (Naira)</th><th>Status</th><th>Transaction Details</th><th>Type</th><th>Date</th>
</tr>
</table>
</div>