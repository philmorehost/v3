<?php
	include("gateway-apikey.php");
	include("requery-transaction.php");
?>
<span style="font-weight: bolder;" class="color-9 mobile-font-size-16 system-font-size-18">TRANSACTION HISTORY</span><br>
<div class="scrollable-div color-9 bg-10 mobile-width-90 system-width-95 mobile-padding-top-1 system-padding-top-1 mobile-padding-bottom-2 system-padding-bottom-2">
<table class="table-style-1">
<tr>
<th>Action</th><th>Status</th><th>Reference</th><th>Amount (Naira)</th><th>Transaction Details</th><th>Type</th><th>Date</th>
</tr>
	<?php
		$select_transaction_history = mysqli_query($conn_server_db,"SELECT id, amount, status, description, transaction_type, website, transaction_date FROM transaction_history WHERE email='$user_session' ORDER BY transaction_date DESC LIMIT 5");
		if(mysqli_num_rows($select_transaction_history) > 0){
			while($transaction_details = mysqli_fetch_assoc($select_transaction_history)){
			if((strtolower($transaction_details["status"]) !== "successful") AND (strtolower($transaction_details["status"]) !== "completed") AND (strtolower($transaction_details["status"]) !== "done") AND (strtolower($transaction_details["status"]) !== "success")){
				$requery_html = '<a style="color:inherit;" href="'.$_SERVER["REQUEST_URI"].'?requery='.$transaction_details["id"].'">Requery</a>';
			}else{
				if(strtolower($transaction_details["transaction_type"]) === "electricity"){
					$requery_html = '<a style="color:inherit;" href="/receipt.php?ref='.$transaction_details["id"].'">View Receipt</a>';
				}else{
					if(strtolower($transaction_details["transaction_type"]) === "recharge-card"){
						$requery_html = '<a style="color:inherit;" href="/print_card.php?ref='.$transaction_details["id"].'">Print Card</a>';
					}else{
						if(strtolower($transaction_details["transaction_type"]) === "data-card"){
							$requery_html = '<a style="color:inherit;" href="/print_data_card.php?ref='.$transaction_details["id"].'">Print Data Card</a>';
						}else{
							$requery_html = "Done";
						}
					}
				}
			}
				echo "<tr>
				    <td>".$requery_html."</td>
				    <td>".ucwords($transaction_details["status"])."</td>
					<td>".$transaction_details["id"]."</td><td>".$transaction_details["amount"]."</td><td>".$transaction_details["description"]."</td><td>".ucwords(str_replace(["-","_"]," ",$transaction_details["transaction_type"]))."</td><td>".$transaction_details["transaction_date"]."</td>
				</tr>";
			}
		}
	?>
<tr>
	<th>Action</th><th>Status</th><th>Reference</th><th>Amount (Naira)</th><th>Transaction Details</th><th>Type</th><th>Date</th>
</tr>
</table>
</div>