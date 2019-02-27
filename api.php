<?php

require_once('config.php');

function call($endpoint, $method = 'GET', $params = null) {
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => API_URL . $endpoint,
		CURLOPT_CUSTOMREQUEST => $method,
		CURLOPT_USERAGENT => 'Codular Sample cURL Request',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => array(
		 'Content-Type: application/json'
		)
	));
	
	if ($method == 'POST') {
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
	}
	
	if ($method == 'PUT') {
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
	}

	$response = curl_exec($curl);

//var_dump($response);
	
	curl_close($curl);

	return $response;
}
