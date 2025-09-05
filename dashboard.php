<?php session_start();
	if(!isset($_SESSION["user"])){
		header("Location: /login.php");
	}else{
		include(__DIR__."/include/config.php");
		include(__DIR__."/include/user-details.php");
	}

	if($conn_server_db == true){
		if(mysqli_query($conn_server_db,"CREATE TABLE IF NOT EXISTS user_message (user_alert LONGTEXT NOT NULL, user_static LONGTEXT NOT NULL)") == true){
			$get_userMessage_details = mysqli_query($conn_server_db,"SELECT * FROM user_message");
			if(mysqli_num_rows($get_userMessage_details) == 0){
				if(mysqli_query($conn_server_db,"INSERT INTO user_message (user_alert, user_static) VALUES ('Welcome Back! ','Welcome Back! ')") == true){

				}
			}
		}

		$get_userMessage_details = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM user_message"));
	}

	//GET USER DETAILS
	$user_session = $_SESSION["user"];
	$all_user_details = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT firstname, lastname, email, password, phone_number, referral, home_address, wallet_balance, account_type, commission, apikey, account_status, transaction_pin FROM users WHERE email='$user_session'"));



	if(isset($_POST["upgrade-account"])){
		$all_details = mysqli_real_escape_string($conn_server_db,strip_tags($_POST["upgrade-package"]));
		$upgrade_to = array_filter(explode(":",trim($all_details)))[0];
		$amount = str_replace(["-","+","/","*"],"",array_filter(explode(":",trim($all_details)))[1]);
		$site_name = $_SERVER["HTTP_HOST"];

		if(!empty($upgrade_to) && !empty($amount)){
		if($all_user_details["wallet_balance"] > $amount){
			$raw_number = "123456789012345678901234567890";
			$reference = substr(str_shuffle($raw_number),0,15);
			$remain_balance = ($all_user_details["wallet_balance"]-$amount);
			if(mysqli_query($conn_server_db,"UPDATE users SET account_type='$upgrade_to', wallet_balance='$remain_balance' WHERE email='$user_session'") == true){
				if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('$user_session','$reference','$amount', '".$all_user_details["wallet_balance"]."', '$remain_balance', 'successful', 'Account Upgrading to ".ucwords(str_replace("_"," ",$upgrade_to))."','account-upgrade', '$site_name')")){
					$get_referee_account = mysqli_query($conn_server_db,"SELECT * FROM users WHERE email='".$all_user_details["referral"]."'");
					if(mysqli_num_rows($get_referee_account) == 1){
						$ref_amount = ($amount*20/100);
						$ref_account_details = mysqli_fetch_assoc($get_referee_account);
						$ref_remain_balance = ($ref_account_details["wallet_balance"]+$ref_amount);
						$ref_reference = substr(str_shuffle($raw_number),0,15);
						if(mysqli_query($conn_server_db,"UPDATE users SET wallet_balance='$ref_remain_balance' WHERE email='".$all_user_details["referral"]."'") == true){
							if(mysqli_query($conn_server_db,"INSERT INTO transaction_history (email, id, amount, w_bef, w_aft, status, description, transaction_type, website) VALUES ('".$all_user_details["referral"]."','$ref_reference','$ref_amount', '".$ref_account_details["wallet_balance"]."', '$ref_remain_balance', 'successful', 'Referral Upgrade Commission of $user_session','commission', '$site_name')")){

							}
						}
					}
					$_SESSION["transaction_upgrade"] = "Account Upgraded Successfully!";
				}
			}
		}else{
			$_SESSION["transaction_upgrade"] = "Insufficient Balance, Upgrade can't continue, Fund wallet and try again! ";
		}
		}else{
			$_SESSION["transaction_upgrade"] = "Upgrade Form Is Empty! Try to select package to Upgrade To";
		}

		header("Location: ".$_SERVER["REQUEST_URI"]);
	}

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM site_info WHERE 1"))["sitetitle"]; ?></title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; " />
<meta name="theme-color" content="skyblue" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="/css/dashboard.css">
<script src="/scripts/auth.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/iconoir-icons/iconoir@main/css/iconoir.css" />
<link rel="apple-touch-icon" sizes="180x180" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h3>Fintech App</h3>
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Transactions</a></li>
                <li><a href="#">Fund Wallet</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </div>
        <div class="main-content">
            <?php include(__DIR__."/include/header-html.php"); ?>

            <script type="text/javascript">
                    setTimeout(function(){
                        alertPopUp(`<?php echo str_replace("\n","<br/>",$get_userMessage_details["user_alert"]); ?>`);
                    }, 1000);
            </script>

            <?php if(isset($_SESSION["transaction_upgrade"])){ ?>
            <script type="text/javascript">
                alertPopUp(`<?php echo $_SESSION["transaction_upgrade"]; ?>`);
            </script>
            <?php } ?>
            <script type="text/javascript">
                let balCodes = "<strong>BALANCE CODES</strong>";
                balCodes += "<br><img src='/images/mtn.png' style='border-radius: 10px; float: left; clear: both;' class='mobile-width-10 system-width-10'/>";
                balCodes += "<br><img src='/images/airtel.png' style='border-radius: 10px; float: left; clear: both;' class='mobile-width-10 system-width-10'/>";
                balCodes += "<br><img src='/images/glo.png' style='border-radius: 10px; float: left; clear: both;' class='mobile-width-10 system-width-10'/>";
                balCodes += "<br><img src='/images/9mobile.png' style='border-radius: 10px; float: left; clear: both;' class='mobile-width-10 system-width-10'/>";

            </script>
            <div class="wallet-balance-container">
                <div class="wallet-balance-card">
                    <div class="wallet-balance-header">
                        <h2>Wallet Balance</h2>
                        <button class="reload-btn" onclick="reloadWalletBalance()">
                            <img src="/images/reload-icon.svg" alt="Reload" />
                        </button>
                    </div>
                    <div class="wallet-balance-amount">
                        <h1 id="walletBal"></h1>
                    </div>
                    <div class="wallet-balance-actions">
                        <a href="/fund-wallet.php" class="btn btn-primary">Fund Wallet</a>
                        <a href="/send-money.php?page=user" class="btn btn-secondary">Transfer Fund</a>
                    </div>
                </div>
            </div>
            <script>
                //Reload Wallet Balance
                reloadWalletBalance();
                function reloadWalletBalance(){
                    var reloadImg = document.querySelector(".reload-btn img");

                        reloadImg.classList.remove("reload-img");
                        reloadImg.classList.add("reload-img");
                        setTimeout(function(){
                            reloadImg.classList.remove("reload-img");
                        },3000);
                    setTimeout(function(){
                        var httpReloadWalletBalanceText = new XMLHttpRequest();
                        httpReloadWalletBalanceText.open("POST","./include/walbal.php");
                        httpReloadWalletBalanceText.setRequestHeader("Content-Type","application/json");
                        const body = JSON.stringify({
                            title: 1
                        });
                        httpReloadWalletBalanceText.onload = function(){
                            if(httpReloadWalletBalanceText.readyState == 4 && httpReloadWalletBalanceText.status == 200){
                                document.getElementById("walletBal").innerHTML = "N"+JSON.parse(httpReloadWalletBalanceText.responseText)["balance"];
                            }else{
                                document.getElementById("walletBal").innerHTML = httpReloadWalletBalanceText.status;
                            }
                        }
                        httpReloadWalletBalanceText.send(body);
                    },1000);
                }
            </script>
                <div class="services-container">
                    <div class="service-category">
                        <h3>Payments</h3>
                        <a style="text-decoration:none;" href="/airtime.php">
                            <button type="button" class="btn-service">
                                <i class="iconoir-cell-tower"></i><br>
                                Buy Airtime
                            </button>
                        </a>
                        <a style="text-decoration:none;" onclick="openDashboardBtnDataLists();">
                            <button type="button" class="btn-service">
                                <i class="iconoir-database"></i><br>
                                Buy Data
                            </button>
                        </a>
                        <a style="text-decoration:none; display: none" href="/sme-data.php" id="dashboadbtndatalist-1">
                            <button type="button" class="btn-service">
                                <i class="iconoir-database"></i><br>
                                SME Data
                            </button>
                        </a>
                        <a style="text-decoration:none; display: none" href="/direct-data.php" id="dashboadbtndatalist-2">
                            <button type="button" class="btn-service">
                                <i class="iconoir-database"></i><br>
                                Direct Data
                            </button>
                        </a>
                        <a style="text-decoration:none; display: none" href="/data-gifting.php" id="dashboadbtndatalist-3">
                            <button type="button" class="btn-service">
                                <i class="iconoir-database"></i><br>
                                Corporate Data
                            </button>
                        </a>
                        <a style="text-decoration:none;" href="/cable.php">
                            <button type="button" class="btn-service">
                                <i class="iconoir-tv"></i><br>
                                Cable TV
                            </button>
                        </a>
                        <a style="text-decoration:none;" href="/electricity.php">
                            <button type="button" class="btn-service">
                                <i class="iconoir-flash"></i><br>
                                Electricity Bill
                            </button>
                        </a>
                    </div>
                    <div class="service-category">
                        <h3>Printing</h3>
                        <a style="text-decoration:none;" href="/recharge-card-printing.php">
                            <button type="button" class="btn-service">
                                <i class="iconoir-bank"></i><br>
                                Recharge Card
                            </button>
                        </a>
                        <a style="text-decoration:none;" href="/data-card.php">
                            <button type="button" class="btn-service">
                                <i class="iconoir-printer"></i><br>
                                Print Data Card
                            </button>
                        </a>
                    </div>
                    <div class="service-category">
                        <h3>Exams</h3>
                        <a style="text-decoration:none;" href="/exam.php">
                            <button type="button" class="btn-service">
                                <i class="iconoir-graduation-cap"></i><br>
                                Buy Exam Pin
                            </button>
                        </a>
                    </div>
                    <div class="service-category">
                        <h3>Messaging</h3>
                        <a style="text-decoration:none;" href="/sms.php">
                            <button type="button" class="btn-service">
                                <i class="iconoir-chat-bubble"></i><br>
                                Bulk SMS
                            </button>
                        </a>
                    </div>
                    <div class="service-category">
                        <h3>Account</h3>
                        <a style="text-decoration:none;" href="/place-payment-order.php">
                            <button type="button" class="btn-service">
                                <i class="iconoir-submit-document"></i><br>
                                Submit Payment
                            </button>
                        </a>
                        <a style="text-decoration:none;" href="/change-password.php">
                            <button type="button" class="btn-service">
                                <i class="iconoir-key-reset"></i><br>
                                Reset Pin
                            </button>
                        </a>
                        <a style="text-decoration:none;" href="/transaction.php">
                            <button type="button" class="btn-service">
                                <i class="iconoir-historic-shield"></i><br>
                                Transactions
                            </button>
                        </a>
                        <a style="text-decoration:none;" onclick="alertPopUp(balCodes)">
                            <button type="button" class="btn-service">
                                <i class="iconoir-dialpad"></i><br>
                                Balance Code
                            </button>
                        </a>
                    </div>
                </div>
                <?php
                    $get_admin_details = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM admin WHERE 1"));
                    $get_admin_bank_details = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM admin_bank_details WHERE 1"));
                ?>
                <div style="text-align:left; display: inline-block;" class="container-box bg-2 mobile-font-size-12 system-font-size-14 mobile-width-85 system-width-39 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
                    Do you need a vtu business website like this?<br><a style="font-weight: bold; color: inherit;" target="_blank" href='https://wa.me/<?php echo $get_admin_details["phone_number"]; ?>?text=I%20need%20help%20regarding%20vtu%20website%20setup'>Click Here to Get Started</a>
                </div>
                <div style="text-align:left; display: inline-block;" class="container-box bg-7 mobile-font-size-12 system-font-size-14 mobile-width-40 system-width-19 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
                    30% Refer: <span style="cursor: pointer; text-decoration: underline; font-weight: bold;" onclick="copyReferLink();">Copy Link</span><br>
                    <?php
                        $get_referrals = mysqli_query($conn_server_db,"SELECT firstname, lastname, email, phone_number, account_status, account_type FROM ".$user_table_name." WHERE referral='$user_session'");
                        echo mysqli_num_rows($get_referrals);
                    ?> People Referred
                </div>
                <script>
                let ReferLink = '<?php echo $w_host."/register.php?ref=".$user_details["email"]; ?>';
                const copyReferLink = async () => {
                    try {
                    await navigator.clipboard.writeText(ReferLink);
                    alert('Content copied to clipboard');
                    } catch (err) {
                    alert('Failed to copy: ', err);
                    }
                }
                </script>
                <div style="text-align:left; display: inline-block;" class="container-box bg-7 mobile-font-size-12 system-font-size-14 mobile-width-38 system-width-18 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
                    Commission: N
                    <?php
                    $wallet_total_commission = mysqli_query($conn_server_db,"SELECT * FROM transaction_history WHERE email='$user_session' AND transaction_type='commission'");
                        if(mysqli_num_rows($wallet_total_commission) > 0){
                            while($total_commission = mysqli_fetch_assoc($wallet_total_commission)){
                                $user_total_commission += $total_commission["amount"];
                            }
                        }
                        echo $user_total_commission;
                    ?><br>
                    Type:
                    <strong>
                        <?php
                            if($user_details["account_type"] == "smart_earner"){
                                $user_account_level = "Smart Earner";
                            }

                            if($user_details["account_type"] == "vip_earner"){
                                $user_account_level = "VIP Earner";
                            }

                            if($user_details["account_type"] == "vip_vendor"){
                                $user_account_level = "VIP Vendor";
                            }

                            if($user_details["account_type"] == "api_earner"){
                                $user_account_level = "Agent Vendor";
                            }

                            echo ucwords($user_account_level);
                        ?>
                    </strong>
                </div><br>

                <!--<div style="text-align:left; display: inline-block;" class="container-box bg-4 mobile-font-size-12 system-font-size-14 mobile-width-85 system-width-48 mobile-margin-top-1 system-margin-top-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
                    <strong><?php echo str_replace("\n","<br/>",$get_userMessage_details["user_static"]); ?></strong>
                </div>-->
                <?php
                    $wallet_total_funding = mysqli_query($conn_server_db,"SELECT * FROM transaction_history WHERE email='$user_session' AND (transaction_type='wallet-funding' OR transaction_type='credit' OR transaction_type='refunded' OR transaction_type='commission') ");
                    if(mysqli_num_rows($wallet_total_funding) > 0){
                        while($total_funding = mysqli_fetch_assoc($wallet_total_funding)){
                            $user_total_funding += $total_funding["amount"];
                        }
                    }

                    $wallet_total_spent = mysqli_query($conn_server_db,"SELECT * FROM transaction_history WHERE email='$user_session'");
                    if(mysqli_num_rows($wallet_total_spent) > 0){
                        while($total_spent = mysqli_fetch_assoc($wallet_total_spent)){
                            if(($total_spent["transaction_type"] !== "wallet-funding") && ($total_spent["transaction_type"] !== "credit") && ($total_spent["transaction_type"] !== "refunded") && ($total_spent["transaction_type"] !== "commission")){
                                $user_total_spent += $total_spent["amount"];
                            }
                        }
                    }
                ?>
                <div style="text-align:left; display:inline-block;" class="button-box bg-2 mobile-font-size-12 system-font-size-14 mobile-width-85 system-width-45 mobile-margin-top-1 system-margin-top-1 mobile-margin-right-1 system-margin-right-1">
                    <strong>Total Funded: N<?php echo $user_total_funding; ?></strong><br>
                    <strong>Total Spent: N<?php echo $user_total_spent; ?></strong>
                </div>

                <div class="transaction-history">
                    <h2>Recent Transactions</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $get_transactions = mysqli_query($conn_server_db,"SELECT * FROM transaction_history WHERE email='$user_session' ORDER BY id DESC LIMIT 10");
                                if(mysqli_num_rows($get_transactions) > 0){
                                    while($transaction = mysqli_fetch_assoc($get_transactions)){
                                        echo "<tr>";
                                        echo "<td>".date("d/m/Y", strtotime($transaction["date"]))."</td>";
                                        echo "<td>".$transaction["description"]."</td>";
                                        echo "<td>N".$transaction["amount"]."</td>";
                                        echo "<td>".$transaction["status"]."</td>";
                                        echo "</tr>";
                                    }
                                }else{
                                    echo "<tr><td colspan='4'>No transactions yet.</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>



                <div style="text-align:left; display: inline-block; height: auto;" class="container-box bg-2 mobile-font-size-12 system-font-size-14 mobile-width-85 system-width-40 mobile-margin-top-1 system-margin-top-1 mobile-margin-bottom-1 system-margin-bottom-1 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-3 system-padding-left-3 mobile-padding-right-3 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
                    <span class="font-size-2 font-family-1"><b>Upgrade your Account</b></span><br>
                    <form action="" method="post">
                    <?php
                        $get_vip_earner_upgrade_price = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM upgrade_price WHERE level='vip_earner'"));
                        $get_vip_vendor_upgrade_price = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM upgrade_price WHERE level='vip_vendor'"));
                        $get_api_earner_upgrade_price = mysqli_fetch_array(mysqli_query($conn_server_db,"SELECT * FROM upgrade_price WHERE level='api_earner'"));
                    ?>
                        <select name="upgrade-package" id="package" class="select-box color-8 bg-10 mobile-font-size-12 system-font-size-14 mobile-width-95 system-width-60" required>
                            <option disabled hidden selected>Choose Package</option>
                            <option	<?php if(($user_details["account_type"] == "vip_earner") OR ($user_details["account_type"] == "vip_vendor") OR ($user_details["account_type"] == "api_earner")){ echo "hidden"; } ?> value="vip_earner:<?php echo $get_vip_earner_upgrade_price['amount']; ?>">VIP Earner @ N<?php echo $get_vip_earner_upgrade_price["amount"]; ?></option>
                            <option <?php if(($user_details["account_type"] == "vip_vendor") OR ($user_details["account_type"] == "api_earner")){ echo "hidden"; } ?> value="vip_vendor:<?php echo $get_vip_vendor_upgrade_price['amount']; ?>">VIP Vendor @ N<?php echo $get_vip_vendor_upgrade_price["amount"]; ?></option>
                            <option <?php if($user_details["account_type"] == "api_earner"){ echo "hidden"; } ?> value="api_earner:<?php echo $get_api_earner_upgrade_price['amount']; ?>">Agent Vendor @ N<?php echo $get_api_earner_upgrade_price["amount"]; ?></option>
                        </select>
                        <input name="upgrade-account" type="submit" style="font-weight: bolder;" class="button-box color-8 bg-3 mobile-font-size-13 system-font-size-16 mobile-width-95 system-width-25" value="Upgrade"/>
                    </form>
                </div><br>

            </center>

            <?php include(__DIR__."/include/footer-html.php"); ?>
        </div>
    </div>
</body>
</html>
