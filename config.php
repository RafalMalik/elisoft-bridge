<?php

require_once('mssql.php');

$hostname = "PPHU-KOMPUTER\ELISOFT";
$port = 1443;
$dbname = "bdFaktury";
$username = "klajmaxapp";
$pwd = "12345";

$conn = new PdoDblibMssql($hostname, $port, $dbname, $username, $pwd);

const API_URL = 'http://app.klajmaxgo.pl';

const API_TOKEN = 'ki4Xjr8#jq@';
