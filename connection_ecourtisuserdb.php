<?php
	$db_host = "10.173.34.126";
	$db_user = "postgres";
	$db_port="5432";
	$db_password = "";
	$db_dbname = "ecourtisuserdb"; 
	try 
	{  
		$conn = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_dbname", $db_user, $db_password, array(PDO::ATTR_PERSISTENT => true));	
	}
	catch(PDOException $e) 
	{  
		 $e='Connection Failed';
		 $conn=$e;
	}  
?>