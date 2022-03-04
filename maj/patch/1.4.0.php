<?php
	require_once(__DIR__ . "/../../classes/Cnx.class.php");

	/************ RAJOUTER LE MESSAGE DANS L'INSTALL DE THELIA (thelia.sql) ***************/
	$cnx = new Cnx();
	
	$query_cnx = "INSERT INTO message(nom) VALUES('creation_client')";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	$id = mysql_insert_id();
	
	$query_cnx = "INSERT INTO messagedesc(message,lang,intitule,titre,description) VALUES($id,1,'Création compte client','Création compte client','Bonjour,<br /> Vous recevez ce mail pour vous avertir que votre compte vient d\'être crée sur __NOM_SITE__.<br /> <br /> Vos identifiants sont les suivants :<br /> <br /> e-mail : __EMAIL__<br /> mot de passe : __MOT_DE_PASSE__<br /> <br /> Vous pouvez modifier ces informations sur le <a href=\"__URL_SITE__\">site</a>')";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	
	$cnx = new Cnx();
	$query_cnx = "ALTER TABLE `messagedesc` ADD `descriptiontext` TEXT NOT NULL";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "update `messagedesc` set description = CONCAT(description,\"__MOTDEPASSE__\") where message in(select id from message where nom=\"changepass\")";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "ALTER TABLE `commande` ADD `devise` INT NOT NULL AFTER `remise` , ADD `taux` FLOAT NOT NULL AFTER `devise` ;";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	
	$query_cnx = "INSERT INTO devise(nom,code,symbole,taux) VALUES('euro','EUR',CHAR(128),'1')";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "UPDATE devise set code='USD',symbole='$' where id=1";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "UPDATE devise set code='GBP',symbole='£' where id=2";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	// changement des messages
	$query_cnx = "update messagedesc set descriptiontext=description";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	

	$query_cnx = "update variable set valeur='140' where nom='version'";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
		
?>