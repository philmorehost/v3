<?php date_default_timezone_set("Africa/Lagos"); error_reporting(0);
	include("db-dtl.php");	
	$conn_server = mysqli_connect($server_url,$server_username,$server_password);
	if($conn_server == true){
	}
	
	$database_name = "CREATE DATABASE IF NOT EXISTS ".$server_dbname;
	
	if(mysqli_query($conn_server,$database_name) == true){
		$conn_server_db = mysqli_connect($server_url,$server_username,$server_password,$server_dbname);
	}else{
		$conn_server_db = mysqli_connect($server_url,$server_username,$server_password,$server_dbname);
	}

	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){   
         $w_host = "https://";   
         $w_host.= $_SERVER['HTTP_HOST'];   
    }else{  
         $w_host = "http://";   
    // Append the host(domain name, ip) to the URL.   
    $w_host.= $_SERVER['HTTP_HOST'];   
    
  }
	
	if(isset($_SESSION["admin"]) && isset($_SESSION["admin_password"])){
		$session_cross_check = $_SESSION["admin"];
		$session_admin_pa = md5($_SESSION["admin_password"]);
		if(mysqli_query($conn_server_db,"SELECT * FROM admin WHERE email='$session_cross_check' && password='$session_admin_pa'") === FALSE){
			unset($_SESSION["admin"]);
			unset($_SESSION["admin_password"]);
		}
	}else{
		if(isset($_SESSION["admin"]) && !isset($_SESSION["admin_password"])){
			$session_cross_check = $_SESSION["admin"];
			unset($_SESSION["admin"]);
		}
	}

	if(isset($_SESSION["admin"])){
		if($_SESSION["admin"] !== adminArray("email")[0]){
			if($_SERVER["SCRIPT_NAME"] !== "/admin/site-setting.php"){
				header("Location: /admin/site-setting.php");
			}
		}
	}

	function adminArray($type){
		global $conn_server_db;
		$admin_arr = array();
		if($type == "email"){
			$element = "email";
		}
		if($type == "name"){
			$element = "fullname";
		}
		if($type == "phone"){
			$element = "phone_number";
		}
		if($type == "address"){
			$element = "home_address";
		}
		if($type == "date"){
			$element = "reg_date";
		}
			$get_admin_details = mysqli_query($conn_server_db,"SELECT * FROM admin");
			if(mysqli_num_rows($get_admin_details) > 0){
				while($rows = mysqli_fetch_assoc($get_admin_details)){
					$admin_arr[] = $rows[$element];
				}
			}
			return $admin_arr;
	}
?>