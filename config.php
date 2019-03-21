<?php

require_once('mssql.php');

$hostname = "MYSTPC\SQLEXPRESS";
$port = "1433";
$dbname = "klajmax";
$username = "kenny";
$pwd = "klajmax";

$conn = new PdoDblibMssql($hostname, $port, $dbname, $username, $pwd);

const API_URL = 'http://app.klajmaxgo.pl';
