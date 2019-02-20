<?php

require('mssql.php');

$hostname = "MYSTPC\SQLEXPRESS";
$port = "";
$dbname = "klajmax";
$username = "MystPC\Kenny";
$pwd = "";

$conn = new PdoDblibMssql($hostname, $port, $dbname, $username, $pwd);

var_dump($conn);

//$tsql = "SELECT id, FirstName, LastName, Email FROM tblContact";  

        const API_URL = 'http://app.klajmaxgo.pl/api';
