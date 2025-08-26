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
	
?>