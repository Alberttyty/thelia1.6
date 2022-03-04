<?php
	require_once(__DIR__ . "/../../classes/Cnx.class.php");
	
	$cnx = new Cnx();
	
	$query_cnx = "update variable set protege='0'";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "update message set protege='0'";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);	
	
	$query_cnx = "ALTER TABLE `variable` ADD `cache` SMALLINT NOT NULL AFTER `protege`";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "insert into variable(nom,valeur,protege,cache) values('version', '136', '1', '1')";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	
	$query_cnx = "CREATE TABLE `ventedeclidisp` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`venteprod` INT NOT NULL ,
	`declidisp` INT NOT NULL
	) ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);	

?>