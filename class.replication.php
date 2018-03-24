<?php

class replication{
	public $conn;
	
	// MySQL Database SQL Codes
	private function myDb($id, $v){
		switch($id){
			case 1: // Obtain tables from database
				$msql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$v'";
				break;
			case 2: // Obtain table data
				$msql = "SELECT * FROM ";
		}
		
		return $msql;
	}
	private function dbtype($a){
		// Determine the database type. This will determine what SQL codes and DB connectors to use.
		switch($a){
			case 1: // MySQL
				$db = "MySQL";
				break;
			case 2: // SQL Server
				$db = "SQLServer";
				break;
			case 3: // Oracle Server
				$db = "Oracle";
				break;
		}
		
		return $db;
	}
	private function connect($t, $servername, $username, $password, $dbname){
		// Determine the appropriate database connectors to use
		switch($t){
			case "MySQL":
				$conn = new mysqli($servername, $username, $password, $dbname);
				if($conn->connect_error){
					return false;
				} else{
					echo "Successful MySQL Connection";
					return $this->conn;
				}
				break;
			case "SQLServer":
				$info = array("Database" => $dbname);
				$conn = sqlsrv_connect($servername, $info);
				if($conn){
					echo "Successful SQL Server Connection";
					return $this->conn;
				} else{
					return false;
				}
		}
	}
	public function getTables($type, $dbname){
		global $conn;
		
		// Determine DB Syntax to use.
		switch($this->dbtype($type)){
			case "MySQL":
				// Get SQL code
				$sql = $this->myDb(1, $dbname);
				$result = $conn->query($sql);
				// If table search is successful, assign table names to variables.
				$myTables = array();
				if($result->num_rows > 0){
					echo "Successfull data reading.";
				} else {
					echo "Can't read tables.";
				}
				break;
		}
	}
	
	//public function getSource
	
	public function start($stype, $sourceserver, $sourcedb, $sourceuser, $sourcepass, $dtype, $destserver, $destdb, $destuser, $destpass){
		// Define the source database and destination database types
		$db = $this->dbtype($stype);
		
		// Connect to the source database
		$sconn = $this->connect($db, $sourceserver, $sourceuser, $sourcepass, $sourcedb);
		
		// Select all tables from the source database.
		
		// Connect to the destination database
		$ddb = $this->dbtype($dtype);
		$dconn = $this->connect($ddb, $destserver, $destuser, $destpass, $destdb);
		
		// Select all tables from the source database.
		$stables = $this->getTables($db, $sourcedb);
		
	}
}


?>
