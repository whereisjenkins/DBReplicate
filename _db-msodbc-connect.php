<?php

set_time_limit(0);
ini_set('default_socket_timeout', 0);
ini_set('sqlsrv.connect_timeout', 0);

$servername = "hmrecruitment.tangrainc.com";
$username = "comforcare";
$password = "433J}}b`+aEtuhU.*";
$dbname = "CFCHEALTH_HRM";

// Create connection
/*$info = array("Database" => $dbname);
$info = array("Database"=>$dbname, "UID"=>$username, "PWD"=>$password);
$msconn = sqlsrv_connect($servername, $info);
*/
$msconn = odbc_connect("Driver={SQL Server Native Client 10.0};Server=$servername;Database=$dbname;", $username, $password);

if($msconn){
	echo "Successful SQL Server ODBC Connection";
} else {
	echo "Bad connection";
}


?>
