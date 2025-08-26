<?php session_start();
    if(!isset($_SESSION["user"])){
		header("Location: ../login.php");
	}else{
		include("config.php");
        $userSes = $_SESSION["user"];
        $get_userDet = mysqli_fetch_assoc(mysqli_query($conn_server_db, "SELECT * FROM users WHERE email='$userSes'"));
	}

    echo json_encode(array("balance" => $get_userDet["wallet_balance"]),true);
?>