<?php

require_once('config.php');

function call($endpoint, $method = 'GET') {
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => API_URL . $endpoint,
		CURLOPT_CUSTOMREQUEST => $method,
		CURLOPT_USERAGENT => 'Codular Sample cURL Request'
	));

	$response = curl_exec($curl);
	
	curl_close($curl);

	return $response;
}