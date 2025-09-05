<?php session_start();
	unset($_SESSION["admin"]);
	unset($_SESSION["admin_password"]);
	header("Location: /admin/login.php");
?>