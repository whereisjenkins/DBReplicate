<?php
error_reporting(E_ALL);

set_time_limit(0);
ini_set('mysqli.connect_timeout', 0);
ini_set('default_socket_timeout', 0);

$servername = "127.0.0.1";
$username = "root";
$password = "root";
$dbname = "CFCHEALTH_HRM";
$dbport = "3306";
$dbsock = "/var/lib/mysql/mysql.sock";
/*
$servername = "107.180.40.18";
$username = "cfcadmin";
$password = "qkkGx4!982";
$dbname = "CFCHEALTH_HRM";
$dbport = "3306";
$dbsock = "/var/lib/mysql/mysql.sock";
*/
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $dbport, $dbsock);

//$conn = new mysqli($servername);

if($conn->connect_error){
	die("Connection failed: " . $conn->connect_errno . ' ' . $conn->connect_error);
} else{
	echo "Connected successfully " . $conn->host_info;

}

?>
