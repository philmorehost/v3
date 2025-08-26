<?php include("config.php");
if (isset($_GET["requery"])) {
	$query_ref_id = mysqli_real_escape_string($conn_server_db, strip_tags($_GET["requery"]));
	if (!empty($query_ref_id)) {
		$select_transaction_history_requery = mysqli_query($conn_server_db, "SELECT id, amount, d_amount, status, description, transaction_type, website, transaction_date FROM transaction_history WHERE id='$query_ref_id'");
		if (mysqli_num_rows($select_transaction_history_requery) == 1) {
			$get_transaction_history_requery_details = mysqli_fetch_assoc($select_transaction_history_requery);
			//Smartrecharge.ng
			if ($get_transaction_history_requery_details["website"] == "smartrecharge.ng") {
				$requery_status_text = json_decode(getAPIRequery("GET", "https://smartrecharge.ng/api/v2/airtime/?api_key=" . $api_web_apikey["smartrecharge.ng"] . "&order_id=" . $query_ref_id . "&task=check_status", "", ""), true)["text_status"];
				if ($requery_status_text == true) {
					if (mysqli_query($conn_server_db, "UPDATE transaction_history SET status='$requery_status_text' WHERE id='$query_ref_id'")) {
					}
				}

				$requery_owner_info = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT email, id, amount, d_amount, status, description, transaction_type, website, transaction_date FROM transaction_history WHERE id='$query_ref_id'"));
				$requery_check_user_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='" . $requery_owner_info["email"] . "'");
				$requery_check_user = mysqli_fetch_assoc($requery_check_user_details);
				$requery_new_balance = ($requery_check_user["wallet_balance"] + $requery_owner_info["d_amount"]);

				if ($requery_status_text == "FAILED") {
					if (!empty($requery_owner_info["d_amount"])) {
						userDailyProductRemover($requery_owner_info["email"], $query_ref_id);
						if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$requery_new_balance' WHERE email='" . $requery_owner_info["email"] . "'")) {
							if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('" . $requery_owner_info["email"] . "','$query_ref_id','" . $requery_owner_info["d_amount"] . "', '" . $requery_check_user["wallet_balance"] . "', '" . $requery_new_balance . "', 'successful', 'Money Refunded','refunded', '" . $_SERVER["HTTP_HOST"] . "')")) {
							}
						}
					}
				}

			}

			//Benzoni.ng
			if ($get_transaction_history_requery_details["website"] == "benzoni.ng") {
				$requery_status_text = json_decode(getAPIRequery("GET", "https://benzoni.ng/api/v2/airtime/?api_key=" . $api_web_apikey["benzoni.ng"] . "&order_id=" . $query_ref_id . "&task=check_status", "", ""), true)["text_status"];
				if ($requery_status_text == true) {
					if (mysqli_query($conn_server_db, "UPDATE transaction_history SET status='$requery_status_text' WHERE id='$query_ref_id'")) {
					}
				}

				$requery_owner_info = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT email, id, amount, d_amount, status, description, transaction_type, website, transaction_date FROM transaction_history WHERE id='$query_ref_id'"));
				$requery_check_user_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='" . $requery_owner_info["amount"] . "'");
				$requery_check_user = mysqli_fetch_assoc($requery_check_user_details);
				$requery_new_balance = ($requery_check_user["wallet_balance"] + $requery_owner_info["d_amount"]);

				if ($requery_status_text == "FAILED") {
					if (!empty($requery_owner_info["d_amount"])) {
						if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$requery_new_balance' WHERE email='" . $requery_owner_info["email"] . "'")) {
							if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('" . $requery_owner_info["email"] . "','$query_ref_id','" . $requery_owner_info["d_amount"] . "', '" . $requery_check_user["wallet_balance"] . "', '" . $requery_new_balance . "', 'successful', 'Money Refunded','refunded', '" . $_SERVER["HTTP_HOST"] . "')")) {
							}
						}
					}
				}
			}

			//Vtpass.com
			if ($get_transaction_history_requery_details["website"] == "vtpass.com") {

				$requery_status_text = json_decode(getAPIRequery("POST", "https://vtpass.com/api/requery", ["Authorization: Basic " . base64_encode($api_web_apikey["vtpass.com"]), "Content-Type: application/json"], "{'request_id':'$query_ref_id'}"), true)["content"]["transactions"]["status"];
				$requery_status_code = json_decode(getAPIRequery("POST", "https://vtpass.com/api/requery", ["Authorization: Basic " . base64_encode($api_web_apikey["vtpass.com"]), "Content-Type: application/json"], "{'request_id':'$query_ref_id'}"), true)["code"];

				if ($requery_status_text == true) {
					if (mysqli_query($conn_server_db, "UPDATE transaction_history SET status='$requery_status_text' WHERE id='$query_ref_id'")) {
					}
				}

				$requery_owner_info = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT email, id, amount, d_amount, status, description, transaction_type, website, transaction_date FROM transaction_history WHERE id='$query_ref_id'"));
				$requery_check_user_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='" . $requery_owner_info["amount"] . "'");
				$requery_check_user = mysqli_fetch_assoc($requery_check_user_details);
				$requery_new_balance = ($requery_check_user["wallet_balance"] + $requery_owner_info["d_amount"]);

				if ($requery_status_code == "016") {
					if (!empty($requery_owner_info["d_amount"])) {
						if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$requery_new_balance' WHERE email='" . $requery_owner_info["email"] . "'")) {
							if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('" . $requery_owner_info["email"] . "','$query_ref_id','" . $requery_owner_info["d_amount"] . "', '" . $requery_check_user["wallet_balance"] . "', '" . $requery_new_balance . "', 'successful', 'Money Refunded','refunded', '" . $_SERVER["HTTP_HOST"] . "')")) {
							}
						}
					}
				}
			}

			//Smartrechargeapi.com
			if ($get_transaction_history_requery_details["website"] == "smartrechargeapi.com") {
				$requery_status_text = json_decode(getAPIRequery("GET", "https://smartrechargeapi.com/api/v2/airtime/?api_key=" . $api_web_apikey["smartrecharge.ng"] . "&order_id=" . $query_ref_id . "&task=check_status", "", ""), true)["text_status"];
				if ($requery_status_text == true) {
					if (mysqli_query($conn_server_db, "UPDATE transaction_history SET status='$requery_status_text' WHERE id='$query_ref_id'")) {
					}
				}

				$requery_owner_info = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT email, id, amount, d_amount, status, description, transaction_type, website, transaction_date FROM transaction_history WHERE id='$query_ref_id'"));
				$requery_check_user_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='" . $requery_owner_info["email"] . "'");
				$requery_check_user = mysqli_fetch_assoc($requery_check_user_details);
				$requery_new_balance = ($requery_check_user["wallet_balance"] + $requery_owner_info["d_amount"]);

				if ($requery_status_text == "FAILED") {
					if (!empty($requery_owner_info["d_amount"])) {
						if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$requery_new_balance' WHERE email='" . $requery_owner_info["email"] . "'")) {
							if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('" . $requery_owner_info["email"] . "','$query_ref_id','" . $requery_owner_info["d_amount"] . "', '" . $requery_check_user["wallet_balance"] . "', '" . $requery_new_balance . "', 'successful', 'Money Refunded','refunded', '" . $_SERVER["HTTP_HOST"] . "')")) {
							}
						}
					}
				}

			}

			//mobileone.ng
			if ($get_transaction_history_requery_details["website"] == "mobileone.ng") {
				$requery_status_text = json_decode(getAPIRequery("GET", "https://mobileone.ng/api/v2/airtime/?api_key=" . $api_web_apikey["mobileone.ng"] . "&order_id=" . $query_ref_id . "&task=check_status", "", ""), true)["text_status"];
				if ($requery_status_text == true) {
					if (mysqli_query($conn_server_db, "UPDATE transaction_history SET status='$requery_status_text' WHERE id='$query_ref_id'")) {
					}
				}

				$requery_owner_info = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT email, id, amount, d_amount, status, description, transaction_type, website, transaction_date FROM transaction_history WHERE id='$query_ref_id'"));
				$requery_check_user_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='" . $requery_owner_info["email"] . "'");
				$requery_check_user = mysqli_fetch_assoc($requery_check_user_details);
				$requery_new_balance = ($requery_check_user["wallet_balance"] + $requery_owner_info["d_amount"]);

				if ($requery_status_text == "FAILED") {
					if (!empty($requery_owner_info["d_amount"])) {
						if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$requery_new_balance' WHERE email='" . $requery_owner_info["email"] . "'")) {
							if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('" . $requery_owner_info["email"] . "','$query_ref_id','" . $requery_owner_info["d_amount"] . "', '" . $requery_check_user["wallet_balance"] . "', '" . $requery_new_balance . "', 'successful', 'Money Refunded','refunded', '" . $_SERVER["HTTP_HOST"] . "')")) {
							}
						}
					}
				}

			}

			//Datagifting.com.ng
			if ($get_transaction_history_requery_details["website"] == "datagifting.com.ng") {

				$requery_status_text = json_decode(getAPIRequery("POST", "https://v5.datagifting.com.ng/web/api/requery.php", ["Authorization: Basic " . base64_encode($api_web_apikey["vtpass.com"]), "Content-Type: application/json"], "{'api_key':'" . $api_web_apikey["datagifting.com.ng"] . "', 'reference':'$query_ref_id'}"), true)["status"];

				if ($requery_status_text == true) {
					if (mysqli_query($conn_server_db, "UPDATE transaction_history SET status='$requery_status_text' WHERE id='$query_ref_id'")) {
					}
				}

				$requery_owner_info = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT email, id, amount, d_amount, status, description, transaction_type, website, transaction_date FROM transaction_history WHERE id='$query_ref_id'"));
				$requery_check_user_details = mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='" . $requery_owner_info["amount"] . "'");
				$requery_check_user = mysqli_fetch_assoc($requery_check_user_details);
				$requery_new_balance = ($requery_check_user["wallet_balance"] + $requery_owner_info["d_amount"]);

				if ($requery_status_text == "failed") {
					if (!empty($requery_owner_info["d_amount"])) {
						if (mysqli_query($conn_server_db, "UPDATE users SET wallet_balance='$requery_new_balance' WHERE email='" . $requery_owner_info["email"] . "'")) {
							if (mysqli_query($conn_server_db, "INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('" . $requery_owner_info["email"] . "','$query_ref_id','" . $requery_owner_info["d_amount"] . "', '" . $requery_check_user["wallet_balance"] . "', '" . $requery_new_balance . "', 'successful', 'Money Refunded','refunded', '" . $_SERVER["HTTP_HOST"] . "')")) {
							}
						}
					}
				}
			}

		}
	}
}

function getAPIRequery($method, $url, $header, $json)
{
	$apiTransactionRequery = curl_init($url);
	$apiTransactionRequeryUrl = $url;
	curl_setopt($apiTransactionRequery, CURLOPT_URL, $apiTransactionRequeryUrl);
	curl_setopt($apiTransactionRequery, CURLOPT_RETURNTRANSFER, true);
	if ($method == "POST") {
		curl_setopt($apiTransactionRequery, CURLOPT_POST, true);
	}

	if ($method == "GET") {
		curl_setopt($apiTransactionRequery, CURLOPT_HTTPGET, true);
	}

	if ($header == true) {
		curl_setopt($apiTransactionRequery, CURLOPT_HTTPHEADER, $header);
	}
	if ($json == true) {
		curl_setopt($apiTransactionRequery, CURLOPT_POSTFIELDS, $json);
	}
	curl_setopt($apiTransactionRequery, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($apiTransactionRequery, CURLOPT_SSL_VERIFYPEER, false);

	$GetAPIRequeryJSON = curl_exec($apiTransactionRequery);
	return $GetAPIRequeryJSON;
}
?>