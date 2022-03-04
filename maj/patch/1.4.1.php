<?php
	require_once(__DIR__ . "/../../classes/Cnx.class.php");

	/* ------------------------------------------------------------------ */
	
	$cnx = new Cnx();

	$query_cnx = "ALTER TABLE  `commande` CHANGE  `facture`  `facture` INT NOT NULL";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "ALTER TABLE  `client` ADD  `lang` INT NOT NULL ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "update client set lang='1';";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
		
	$query_cnx = "update variable set valeur='141' where nom='version'";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

		
?>