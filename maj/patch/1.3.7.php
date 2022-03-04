<?php
	require_once(__DIR__ . "/../../classes/Cnx.class.php");
	
	$cnx = new Cnx();
	
	$query_cnx = "CREATE TABLE `racmodule` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`module` TEXT NOT NULL
	) ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);	

	$query_cnx = "ALTER TABLE `stock` ADD `surplus` FLOAT NOT NULL ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);	

	$query_cnx = "ALTER TABLE `contenudesc` ADD `postscriptum` TEXT NOT NULL AFTER `description` ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);	

	$query_cnx = "ALTER TABLE `dossierdesc` ADD `postscriptum` TEXT NOT NULL AFTER `description` ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	
	$query_cnx = "ALTER TABLE `rubriquedesc` ADD `postscriptum` TEXT NOT NULL AFTER `description` ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	
	$query_cnx = "ALTER TABLE `produitdesc` ADD `postscriptum` TEXT NOT NULL AFTER `description` ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "ALTER TABLE `client` ADD `intracom` TEXT NOT NULL AFTER `siret` ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "update pays set tva=0;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	
	$query_cnx = "update pays set tva=1 where id in (5,13,20,31,40,51,58,59,63,64,118,69,78,83,86,97,102,103,110,137,140,141,145,146,147,162,163,167);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "ALTER TABLE `commande` ADD `lang` INT NOT NULL ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "CREATE TABLE `venteadr` (
	  `id` int(11) NOT NULL auto_increment,
	  `raison` smallint(6) NOT NULL default '0',
	  `nom` text NOT NULL,
	  `prenom` text NOT NULL,
	  `adresse1` varchar(40) NOT NULL default '',
	  `adresse2` varchar(40) NOT NULL default '',
	  `adresse3` varchar(40) NOT NULL default '',
	  `cpostal` varchar(10) NOT NULL default '',
	  `ville` varchar(30) NOT NULL default '',
	  `tel` text NOT NULL,
	  `pays` int(11) NOT NULL default '0',
	  PRIMARY KEY  (`id`)
	)AUTO_INCREMENT=1 ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "ALTER TABLE `commande` CHANGE `adresse` `adrlivr` INT( 11 ) NOT NULL DEFAULT '0'";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	
	$query_cnx = "ALTER TABLE `commande` ADD `adrfact` INT NOT NULL AFTER `client` ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
		
	$query_cmd = "select * from commande";
	$resul_cmd = mysql_query($query_cmd, $cnx->link);
	while($row_cmd = mysql_fetch_object($resul_cmd)){
		
		
		$query_client = "select * from client where id=\"" . $row_cmd->client. "\"";
		$resul_client = mysql_query($query_client, $cnx->link);
		$row_client = mysql_fetch_object($resul_client);
		
		$query_venteadr = "insert into venteadr(raison, nom, prenom, adresse1, adresse2, adresse3, cpostal, ville, tel, pays) values(\"" . $row_client->raison . "\", \"" . $row_client->nom . "\", \"" . $row_client->prenom . "\", \"" . $row_client->adresse1 . "\", \"" . $row_client->adresse2 . "\", \"" . $row_client->adresse3 . "\", \"" . $row_client->cpostal . "\", \"" . $row_client->ville . "\", \"" . $row_client->telfixe . "/" . $row_client->telport . "\", \"" . $row_client->pays . "\")";
		$resul_venteadr = mysql_query($query_venteadr, $cnx->link);
		
		$adrcli = mysql_insert_id();
		
		$query_majcmd = "update commande set adrfact=\"" . $adrcli. "\" where id=\"" . $row_cmd->id . "\"";
		$resul_majcmd = mysql_query($query_majcmd, $cnx->link);
		
			
		if($row_cmd->adrlivr){

			$query_livr = "select * from adresse where id=\"" . $row_cmd->adrlivr. "\"";
			$resul_livr = mysql_query($query_livr, $cnx->link);
			$row_livr = mysql_fetch_object($resul_livr);
			
			
			$query_venteadr = "insert into venteadr(raison, nom, prenom, adresse1, adresse2, adresse3, cpostal, ville, tel, pays) values(\"" . $row_livr->raison . "\", \"" . $row_livr->nom . "\", \"" . $row_livr->prenom . "\", \"" . $row_livr->adresse1 . "\", \"" . $row_livr->adresse2 . "\", \"" . $row_livr->adresse3 . "\", \"" . $row_livr->cpostal . "\", \"" . $row_livr->ville . "\", \"" . $row_livr->tel . "\", \"" . $row_livr->pays . "\")";
			$resul_venteadr = mysql_query($query_venteadr, $cnx->link);
			$adrlivr = mysql_insert_id();
	
			
		}
		
		else{
			$query_venteadr = "insert into venteadr(raison, nom, prenom, adresse1, adresse2, adresse3, cpostal, ville, tel, pays) values(\"" . $row_client->raison . "\", \"" . $row_client->nom . "\", \"" . $row_client->prenom . "\", \"" . $row_client->adresse1 . "\", \"" . $row_client->adresse2 . "\", \"" . $row_client->adresse3 . "\", \"" . $row_client->cpostal . "\", \"" . $row_client->ville . "\", \"" . $row_client->telfixe . "/" . $row_client->telport . "\", \"" . $row_client->pays . "\")";
			$resul_venteadr = mysql_query($query_venteadr, $cnx->link);
			$adrlivr = mysql_insert_id();

		}
		
		$query_majcmd = "update commande set adrlivr=\"" . $adrlivr. "\" where id=\"" . $row_cmd->id . "\"";
		$resul_majcmd = mysql_query($query_majcmd, $cnx->link);
		
	}
			

	$query_cnx = "update variable set valeur='137' where nom='version'";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

?>