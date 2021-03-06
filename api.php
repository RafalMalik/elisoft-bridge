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
            'Content-Type: application/json',
		'X-AUTH-TOKEN:' . API_TOKEN
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

//curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-AUTH-TOKEN:' . API_TOKEN));

    $response = curl_exec($curl);

	//var_dump($response);

    curl_close($curl);

    return json_decode($response);
}
