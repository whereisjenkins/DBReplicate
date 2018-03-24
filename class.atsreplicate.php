<?php
class atsreplicate{
	
	// MySQL Codes
	private function mySql($id, $v){
		switch($id){
			case 1: // Select all tables from mysql database
				$msql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$v'";
				break;
			case 2: // Select column names from table
				$msql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$v' AND TABLE_SCHEMA = 'CFCHEALTH_HRM'";
				break;
		}
		return $msql;
	}
	// Obtain MySQL DB Tables.
	private function getTables($dbname){
		global $conn;
	
		// Obtain SQL code.
		$sql = $this->mySql(1, $dbname);
		
		$result = $conn->query($sql);
		// If table search is successful, assign table names to variable array
		$myTables = array();
		if($result->num_rows > 0){
			$i = 0;
			while($row = $result->fetch_assoc()){
				$myTables[$i] = $row['TABLE_NAME'];
				$i++;
			}
		} else{
			echo "Can't read tables";
		}
		
		return $myTables;
	}
	
	private function pullData($sql){
		global $conn;
		
		$result = $conn->query($sql);
		
	}
	private function chkDup($table, $pkey, $fkey){
		global $msconn;
		
		// Switch primary key column name based on table name
		if($table == 'CANDIDATE'){
			$pkcol = 'CANDIDATE_ID';
		} else{
			$pkcol = 'ID';
		}
		$sql = "SELECT * FROM [$table] WHERE SOURCE = '$fkey' OR $pkcol = '$pkey'";
		$result = sqlsrv_query($msconn, $sql, array(), array( "Scrollable" => 'static'));
		
		// Obtain row count
		$row_count = sqlsrv_num_rows($result);
	
		if($row_count > 0){
			return FALSE;
		} else{
			return TRUE;
		}
	}
	private function tblMigrate($table){
		global $conn;
		global $msconn;
		
		// Obtain table column names
		$sql = $this->mySql(2, $table);
		$result = $conn->query($sql);
		// If column search is successfull, assign column names to variable array
		$myColumns = array();
		if($result->num_rows > 0){
			// Once all column names selected. Construct SQL String for selecting table data and insert string for SQL Server
			$i = 0; $j = $result->num_rows;
			$msql = "SELECT "; 
			$ssql = "INSERT INTO [$table] (";
			// Using table & column array values for construction
			while($row = $result->fetch_assoc()){
				$myColumns[$i] = $row['COLUMN_NAME'];
				$msql .= "$myColumns[$i]"; 
				$ssql .= "[$myColumns[$i]]"; //$table . "." . This prepended the variable for ssql
				
				$i++;
				
				if($i < $j) { $msql .= ", "; $ssql .= ", "; }
			}
			$msql .= " FROM $table LIMIT 3"; 
			$ssql .= ") VALUES ";
			echo $msql;
			// Convert to upper case for SQL Server Syntax.
			$ssql = strtoupper($ssql);
			
			// Execute MySQL Query to pull data
			$mresult = $conn->query($msql);
			// If data pull is successfull continue constructing insert query for SQL Server
			// Set identity insert to on
			$table = strtoupper($table);
			$s = "SET IDENTITY_INSERT $table ON";
			sqlsrv_query($msconn, $s, array());
			
			if($mresult->num_rows > 0){
				$time = 0;
				while($mrow = $mresult->fetch_assoc()){
					$k = 0; 
					$rdata = array(); $vstring = "";
					echo $mrow[$myColumns[1]] . "<hr>";
					// Check for duplicate data in SQL Server
					$chk = $this->chkDup($table, $mrow[$myColumns[0]], $mrow[$myColumns[1]]);
					if($chk){
						// If no duplicates exist, proceed with constructing string.
						while($k < $j){
						
						
							$rdata[$k] = $mrow[$myColumns[$k]];
						
							$vstring .= "'$rdata[$k]'";
							$k++;
						
							if($k < $j) { $vstring .= ","; }
						}
						$ssql .= "(" . $vstring . "),";
					} else{
						echo "Record already exists in SQL Server table: " . $table . "<br/>";
					}
					
				}
				$ssql = rtrim($ssql, ",");
				// SQL Query Constructed. Execute insertion into SQL Server Database.
				// Check for completed sql string before execution
				if(substr($ssql, -7) == 'VALUES '){
					// Don't execute query
					echo "Query was not inserted due to incomplete query string <br>";
				} else{
					// Execute query
					$params = array();
					$submit = sqlsrv_query($msconn, $ssql, $params);
					if($submit){
						echo "Successful insertion into SQL Server database";
					} else{
						echo $ssql;
						echo "Error with SQL Server insertion";
						die( print_r( sqlsrv_errors(), true));
					}
				}

			}
			// Set identity insert to off
			$table = strtoupper($table);
			$s = "SET IDENTITY_INSERT $table OFF";
			sqlsrv_query($msconn, $s, array());
				
			
		} else{
			echo "Can't read columns";
		}
	}
	
	private function lmTable($table){
		$table = strtoupper($table);
		switch($table){
			case 'CANDIDATE':
				return true;
				break;
			case 'CANDIDATE_AVAILABILITY':
				return true;
				break;
			case 'CANDIDATE_CREDENTIALS':
				return true;
				break;
			case 'CANDIDATE_EDUCATION':
				return true;
				break;
			case 'CANDIDATE_EMPLOYMENTHISTORY':
				return true;
				break;
			case 'CANDIDATE_REFERENCES':
				return true;
				break;
			case 'CANDIDATE_RESUME':
				return true;
				break;
			case 'CANDIDATE_SKILLS':
				return true;
				break;
			case 'USER':
				return true;
				break;
			default:
				return false;
		}
	}
	public function start($source, $dest){
		// Select all tables from the source database.
		$stables = $this->getTables($source);
		
		// Select all data from souce tables and insert into destination tables.
		// Count number of tables in array for use in looping
		$j = count($stables); $q = 0;
		
		while($q < $j){
			$table = $stables[$q];
			
			// Only migrate selected tables.
			$mg = $this->lmTable($table);
			if($mg){
				echo "Starting migration of $table <br/>";
				$this->tblMigrate($table);
			}
			echo $q . "$table <br>";
			$q++;
		}
	}
	
}

?>