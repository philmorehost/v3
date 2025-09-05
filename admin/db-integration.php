<?php session_start();
	include("../include/mailer.php");
	if(!isset($_SESSION["admin"])){
		header("Location: /admin/login.php");
	}else{
		include("../include/admin-config.php");
		include("../include/admin-details.php");
	}
	
	function getBrowser()
	{
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$browser = "N/A";
	
	$browsers = [
	'/msie/i' => 'Internet explorer',
	'/firefox/i' => 'Firefox',
	'/safari/i' => 'Safari',
	'/chrome/i' => 'Chrome',
	'/edge/i' => 'Edge',
	'/opera/i' => 'Opera',
	'/mobile/i' => 'Mobile browser',
	];
	
	foreach ($browsers as $regex => $value) {
	if (preg_match($regex, $user_agent)) {
	$browser = $value;
	}
	}
	
	return $browser;
	}
	
	
	if(isset($_POST["setup"])){
		$server = strip_tags(strtolower($_POST["server"]));
		$user = strip_tags(strtolower($_POST["user"]));
		$pass = strip_tags(strtolower($_POST["pass"]));
		$dbname = strip_tags(strtolower($_POST["dbname"]));
		
        $connectdb = mysqli_connect($server,$user,$pass,$dbname);
        if($connectdb == true){
            if(file_exists("../include/db-json.php")){
                file_put_contents("../include/db-json.php",'<?php'."\n".'	$db_json_dtls = array("server" => "'.$server.'", "user" => "'.$user.'", "pass" => "'.$pass.'", "dbname" => "'.$dbname.'");'."\n".'	$db_json_encode = json_encode($db_json_dtls,true);'."\n".'	$db_json_decode = json_decode($db_json_encode,true);'."\n".'?>');
                unset($_SESSION["allowreset"]);
                header("Location: ".$_SERVER["REQUEST_URI"]);
            }else{
                fopen("../include/db-json.php","a++");
                file_put_contents("../include/db-json.php",'<?php'."\n".'	$db_json_dtls = array("server" => "'.$server.'", "user" => "'.$user.'", "pass" => "'.$pass.'", "dbname" => "'.$dbname.'");'."\n".'	$db_json_encode = json_encode($db_json_dtls,true);'."\n".'	$db_json_decode = json_decode($db_json_encode,true);'."\n".'?>');
                unset($_SESSION["allowreset"]);
                header("Location: ".$_SERVER["REQUEST_URI"]);
            }    
        }else{
            $log_message = "Error: Can't connect to server with DB information provided!";
        }
		
        /*if(!empty($db_json_decode["server"]) && !empty($db_json_decode["dbname"]) && ($conn_server_db == true)){
            unset($_SESSION["allowreset"]);
        }*/
	}
	
	if(isset($_POST["canceldb"])){
		unset($_SESSION["resetdb"]);
        unset($_SESSION["allowreset"]);
		header("Location: ".$_SERVER["REQUEST_URI"]);
	}
	
	if(isset($_POST["confirm"])){
		$email = mysqli_real_escape_string($conn_server_db, strip_tags(strtolower($_POST["email"])));
		$password = md5(strip_tags($_POST["password"]));
		
		if(!empty(trim($email)) && filter_var($email,FILTER_VALIDATE_EMAIL) && !empty(trim($password))){
				$check_admin_info = mysqli_query($conn_server_db,"SELECT email, password FROM ".$admin_table_name." WHERE email='$email'");
				if(mysqli_num_rows($check_admin_info) > 0){
					while($admin_details = mysqli_fetch_assoc($check_admin_info)){
						if(trim($password) == $admin_details["password"]){
							unset($_SESSION["resetdb"]);
                            $_SESSION["allowreset"] = true;
							$to = mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT email FROM admin WHERE 1"))["email"];
							$subject = "Alert!!! Admin Database was Altered! ";
							$html_message = '<!DOCTYPE html>
							<html>
							<head>
							<title></title>
							<meta name="theme-color" content="skyblue" />
							<meta name="viewport" content="width=device-width, initial-scale=1"/>
							<style type="text/css">
							body{
							font-size:14px;
							font-family:tahooma;
							}
							
							#header{
							width:100%;
							height:4rem;
							margin:-8px 0 8px -8px;
							padding:0 16px 0 0;
							background: skyblue;
							top:0;
							position:sticky;
							}
							
							#header img{
							width:auto;
							height:3.2rem;
							margin:5px 3px;
							}
							
							#content{
							color:black;
							font-size:14px;
							font-family:tahooma;
							}
							
							#web_link{
							font-size:14px;
							font-family:tahooma;
							text-align:left;
							}
							
							#footer{
							width:100%;
							height:10rem;
							margin:8px 0 -8px -8px;
							padding:0 16px 0 0;
							background: skyblue;
							}
							
							#footer img{
							width:auto;
							height:3.2rem;
							margin:5px 3px;
							}
							</style>
							
							</head>
							<body>
							<div id="header">
							<img style="width: 30%; height: auto;" src="http://'.$_SERVER["HTTP_HOST"].'/images/logo.png">
							</div>
							<span id="content">Dear <b>'.mysqli_fetch_assoc(mysqli_query($conn_server_db,"SELECT * FROM admin WHERE 1"))["fullname"].'</b><br>
							<center><b>Your Site Database was changed from your admin panel from the follow mobile/system details:</b></center><br>
							<blockquote>IP Address: '.getenv("REMOTE_ADDR").'<br>
							Device Details: '.$_SERVER['HTTP_USER_AGENT'].'<br>
							Is this you? if yes ignore this message. if no rush to your admin panel and change your details.<br>
							Thank You.</blockquote>
							</span><br>
							<center>
							<a href="'.$_SERVER["HTTP_HOST"].'" id="web_link">Visit Website</a><br>
							<a href="mailto:" id="web_link">support@'.$_SERVER["HTTP_HOST"].'</a>
							</center>
							<div id="footer">
							<center>
							<img style="width: 15%; height: auto;" src="http://'.$_SERVER["HTTP_HOST"].'/images/logo.png"><br>
							<b>Visit Our Website:</b> <a href="'.$_SERVER["HTTP_HOST"].'" id="web_link">'.$_SERVER["HTTP_HOST"].'</a><br>
							<b>Contact Us:</b> <a href="mailto:support@'.$_SERVER["HTTP_HOST"].'" id="web_link">support@'.$_SERVER["HTTP_HOST"].'</a>
							</center>
							</div>
							
							</body>
							</html>';
							// Always set content-type when sending HTML email
							$headers = "MIME-Version: 1.0" . "\r\n";
							$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
							
							// More headers
							$headers .= 'From: <support@'.$_SERVER["HTTP_HOST"].'>' . "\r\n";
							$headers .= 'Cc: support@'.$_SERVER["HTTP_HOST"] . "\r\n";
							
							smtpEMAIL('support@'.$_SERVER["HTTP_HOST"],$to,$subject,$html_message,$headers);
							
							header("Location: ".$_SERVER["REQUEST_URI"]);
						}else{
							$log_message = "Password Doesn't Match! ";
						}
					}
				}else{
					$log_message = "User Doesn't Exists! ";
				}
		}else{
			$reg_message = "Required Information must not be BLANK";
		}
		
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; " />
<meta name="theme-color" content="skyblue" />
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="/css/site.css">
<script src="/scripts/auth.js"></script>
</head>
<body>
<?php include("../include/admin-header-html.php"); ?>

<center>
	<div style="margin-left:-8px;" class="container-box bg-10 mobile-width-93 system-width-50 mobile-margin-top-37 system-margin-top-9 mobile-padding-top-3 system-padding-top-3 mobile-padding-left-6 system-padding-left-3 mobile-padding-right-6 system-padding-right-3 mobile-padding-bottom-3 system-padding-bottom-3">
		<?php
			if($log_message == true){
		?>
				<div class="message-box color-5 mobile-font-size-12 system-font-size-14"><?php echo $log_message; ?></div>
		<?php } ?>
		    <?php if(!isset($_SESSION["allowreset"])){ ?>
				<span style="font-weight: bolder;" class="color-5 mobile-font-size-20 system-font-size-25">Server Database Set-up</span><br>
				<span style="font-weight: bolder;" class="color-3 mobile-font-size-16 system-font-size-18">Confirm Details to Proceed</span><br><br>
			<form action="" method="post">
				<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Email address</span><br>
				<input name="email" type="email" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="abc@gmail.com" required/><br/>
				<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Password</span><br>
				<input name="password" type="password" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="••••••••" required/><br/>
				<input name="confirm" type="submit" class="button-box color-8 bg-5 mobile-font-size-13 system-font-size-16 mobile-width-99 system-width-99 mobile-margin-top-2 system-margin-top-2 mobile-margin-bottom-15 system-margin-bottom-20" value="Proceed"/>
			</form>
            <?php } ?>
        <?php if(isset($_SESSION["allowreset"])){ ?>
			<span style="font-weight: bolder;" class="color-5 mobile-font-size-20 system-font-size-25">Server Database Set-up</span><br><br>
			<form action="" method="post">
				<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Server URL</span><br>
				<input name="server" type="text" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="e.g localhost" value="<?php if(file_exists('../include/db-json.php')){ echo $db_json_decode['server']; } ?>" required/><br/>
				<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Server Username</span><br>
				<input name="user" type="text" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="e.g user" value="<?php if(file_exists('../include/db-json.php')){ echo $db_json_decode['user']; } ?>" /><br/>
				<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Server Password</span><br>
				<input name="pass" type="password" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="••••••••" value="<?php if(file_exists('../include/db-json.php')){ echo $db_json_decode['pass']; } ?>" /><br/>
				<span style="float: left; font-weight: lighter;" class="color-9 mobile-font-size-12 system-font-size-14 font-family-1">Database Name</span><br>
				<input name="dbname" type="text" class="input-box mobile-width-95 system-width-95 mobile-margin-top-3 system-margin-top-3 mobile-margin-bottom-5 system-margin-bottom-5" placeholder="e.g mydb (new or existing db)" value="<?php if(file_exists('../include/db-json.php')){ echo $db_json_decode['dbname']; } ?>" required/><br/>
				<input name="setup" type="submit" class="button-box color-8 bg-5 mobile-font-size-13 system-font-size-16 mobile-width-99 system-width-99 mobile-margin-top-2 system-margin-top-2 mobile-margin-bottom-2 system-margin-bottom-2" value="Set-up"/>
			</form>
		<?php } ?>
            <form action="" method="post">
                <?php if(isset($_SESSION["allowreset"])){ ?>
					<input name="canceldb" type="submit" class="button-box color-8 bg-5 mobile-font-size-13 system-font-size-16 mobile-width-99 system-width-99 mobile-margin-top-2 system-margin-top-2 mobile-margin-bottom-2 system-margin-bottom-2" value="Cancel DB Reset"/>
                <?php } ?>
			</form>
	</div>
</center>

<?php include("../include/footer-header-html.php"); ?>
</body>
</html>