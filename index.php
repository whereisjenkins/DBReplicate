<?php
error_reporting(E_All);

require("class.atsreplicate.php");

// Create replication Object.
$r = new atsreplicate();

// Define Databases and credentials
// MySQL Database
include("_db-mysql-connect.php");
$myserv = $servername; $myuser = $username; $mypass = $password; $mydata = $dbname;
// SQL Server Database
include("_db-mssql-connect.php");
$msserv = $servername; $msuser = $username; $mspass = $password; $msdata = $dbname;
echo $servername;
// Test database connection
$r->start($mydata, $msdata);

?>
