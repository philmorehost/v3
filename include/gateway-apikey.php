<?php
	$api_web_apikey = array();
	$get_airtime_web_key = mysqli_query($conn_server_db,"SELECT website, apikey FROM airtime_api");
	while($details = mysqli_fetch_assoc($get_airtime_web_key)){
	if(($api_web_apikey[$details["website"]] == false) OR (empty($api_web_apikey[$details["website"]]))){
	$api_web_apikey[$details["website"]] = $details["apikey"];
	}
	}
	$get_cable_web_key = mysqli_query($conn_server_db,"SELECT website, apikey FROM cable_api");
	while($details = mysqli_fetch_assoc($get_cable_web_key)){
	if(($api_web_apikey[$details["website"]] == false) OR (empty($api_web_apikey[$details["website"]]))){
	$api_web_apikey[$details["website"]] = $details["apikey"];
	}
	}
	$get_direct_data_web_key = mysqli_query($conn_server_db,"SELECT website, apikey FROM direct_data_api");
	while($details = mysqli_fetch_assoc($get_direct_data_web_key)){
	if(($api_web_apikey[$details["website"]] == false) OR (empty($api_web_apikey[$details["website"]]))){
	$api_web_apikey[$details["website"]] = $details["apikey"];
	}
	}
	
	$get_gifting_data_web_key = mysqli_query($conn_server_db,"SELECT website, apikey FROM gifting_data_api");
	while($details = mysqli_fetch_assoc($get_gifting_data_web_key)){
	if(($api_web_apikey[$details["website"]] == false) OR (empty($api_web_apikey[$details["website"]]))){
	$api_web_apikey[$details["website"]] = $details["apikey"];
	}
	}
	
	$get_sme_data_web_key = mysqli_query($conn_server_db,"SELECT website, apikey FROM sme_data_api");
	while($details = mysqli_fetch_assoc($get_sme_data_web_key)){
	if(($api_web_apikey[$details["website"]] == false) OR (empty($api_web_apikey[$details["website"]]))){
	$api_web_apikey[$details["website"]] = $details["apikey"];
	}
	}
	
	$get_electricity_web_key = mysqli_query($conn_server_db,"SELECT website, apikey FROM electricity_api");
	while($details = mysqli_fetch_assoc($get_electricity_web_key)){
	if(($api_web_apikey[$details["website"]] == false) OR (empty($api_web_apikey[$details["website"]]))){
	$api_web_apikey[$details["website"]] = $details["apikey"];
	}
	}
	
	$get_exam_web_key = mysqli_query($conn_server_db,"SELECT website, apikey FROM exam_api");
	while($details = mysqli_fetch_assoc($get_exam_web_key)){
	if(($api_web_apikey[$details["website"]] == false) OR (empty($api_web_apikey[$details["website"]]))){
	$api_web_apikey[$details["website"]] = $details["apikey"];
	}
	}
	
	$get_insurance_web_key = mysqli_query($conn_server_db,"SELECT website, apikey FROM insurance_api");
	while($details = mysqli_fetch_assoc($get_insurance_web_key)){
	if(($api_web_apikey[$details["website"]] == false) OR (empty($api_web_apikey[$details["website"]]))){
	$api_web_apikey[$details["website"]] = $details["apikey"];
	}
	}
	
	$get_sms_web_key = mysqli_query($conn_server_db,"SELECT website, apikey FROM sms_api");
	while($details = mysqli_fetch_assoc($get_sms_web_key)){
	if(($api_web_apikey[$details["website"]] == false) OR (empty($api_web_apikey[$details["website"]]))){
	$api_web_apikey[$details["website"]] = $details["apikey"];
	}
	}
	
?>