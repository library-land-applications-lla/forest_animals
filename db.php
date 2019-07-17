<?php
    $config = include('config/config.php');
    $connection = mysqli_connect($config['host'],
    	                         $config['db_username'],
    	                         $config['db_password'],
    	                         $config['db_name']);
    // Test Connection
	if (mysqli_connect_errno()) {
		echo "DB Connection Error: " . mysqli_connect_errno();
	}

	$tables_exist_check = "SELECT COUNT(DISTINCT(table_name)) AS num_tables "
	                      ."FROM information_schema.columns "
	                      ."WHERE table_schema = '". $config['db_name']."'";
	$set_identifier = mysqli_query($connection, $tables_exist_check);
	$result = mysqli_fetch_all($set_identifier, MYSQLI_BOTH);
	
	if (!$result[0]["num_tables"]) {
		$create_tables = file_get_contents('sql/create_db.sql');
		mysqli_query($connection, $create_tables);
		$populate_tables = file_get_contents('sql/populate_db.sql');
		if(!mysqli_multi_query($connection, $populate_tables)) {
			echo mysqli_error($connection);
		}
	}
?>