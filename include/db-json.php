<?php
	$db_json_dtls = array("server" => "localhost", "user" => "root", "pass" => "", "dbname" => "datagift_v3up");
	$db_json_encode = json_encode($db_json_dtls,true);
	$db_json_decode = json_decode($db_json_encode,true);
?> 