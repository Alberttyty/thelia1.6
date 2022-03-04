<?php
	require_once(__DIR__ . "/../../classes/Cnx.class.php");

	/* ------------------------------------------------------------------ */


	$listefichiersplugins = array(
	"dupliprod/dupliprod_admin.php",
	"expeditor/expeditor_admin.php",
	"expeditor/export.php",
	"multifact/multifact_admin.php",
	"prodprixmult/prodprixmult_admin.php",
	"tinymce/tinymce_admin_title.php",
	"validcli/validcli_admin.php",
	"alert/alertstock_admin.php",
	"avoir/avoir_admin.php",
	"changeref/changeref_admin.php",
	"cmdparmois/cmdparmois_admin.php",
	"commentairecmd/commentairecmd_admin_commandedetails.php",
	"declibre/declibre_admin_produitmodifier.php",
	"declibre/gestdeclibre.php",
	"declibre/prixprod.php",
	"declibre/stockprod.php",
	"declistockvide/declistockvide_admin.php",
	"exportcmdebp/exportcmdebp_admin.php",
	"importartebp/importartebp_admin.php",
	"fianet/fianet_admin.php",
	"importartebp/importartebp_admin.php",
	"newsletter/newsletter_admin.php",
	"newsletter/export_newsletter.php",
	"newsletter/export_mailcli.php",
	"osc2thelia/osc2thelia_admin.php",
	"osc2thelia/import.php",
	"produitdispohorsligne/produitdispohorsligne_admin.php",
	"produithorsligne/produithorsligne_admin.php",
	"ventedpt/ventedpt_admin.php",
	"caracdispinfo/caracdispinfo_admin_caracteristiquemodifier.php",
	"caracdispinfo/caracdispinfo_gestion.php",
	"commentaires/commentaires_admin.php",
	"degressif/degressif_admin_produitmodifier.php",
	"degressif/gestdegressif.php",
	"degressif/prixprod.php",
	"lot/lot_admin_produitmodifier.php",
	"lot/lot_gestion.php",
	"lot/lot_produit.php",
	"lot/stockprod.php",
	"messagecmd/messagecmd_admin_commandedetails.php",
	"nuage/nuage_admin.php",
	"parrainage/compteparrain_admin.php",
	"parrainage/gestparrainage_admin.php",
	"parrainage/parrainage_admin_clientvisualiser.php",
	"parrainage/parrainage_admin.php",
	"parrainage/gestranche.php",
	"prodvirtuel/prodvirtuel_admin_produitmodifier.php",
	"prodvirtuel/telecharger.php",
	"tntrelais/tntrelais_admin.php",
	"tntrelais/tntrelais_admin_commandedetails.php",
	"prodabonnement/prodabonnement_admin.php",
	"prodabonnement/prodabonnement_admin_produitmodifier.php",
	"prodprepaiement/prodprepaiement_admin_clientvisualiser.php",
	"prodprepaiement/prodprepaiement_admin_produitmodifier.php",
	"prodprepaiement/prodprepaiement_admin.php"
	);


foreach($listefichiersplugins as $fichier){
	
	if(file_exists("../client/plugins/" . $fichier)){
		$rec = file_get_contents("../client/plugins/" . $fichier);
		if(! strstr($rec, "authplugins.php")){
			if(! is_writable("../client/plugins/$fichier")){
				echo "Impossible de modifier $fichier. Merci de donner les droits d'&eacute;criture &agrave; Apache et de relancez ce script.<br />";
			exit;
			}
			else {
				echo "Patch $fichier <br />";
				preg_match("/([^\/]*)\//", $fichier, $nomplugin);
				$rec =  preg_replace("/<\?php/", "<?php\nrequire_once(__DIR__ . \"/../../../fonctions/authplugins.php\");\n\nautorisation(\"" . $nomplugin[1] . "\");\n\n", $rec, 1);
				$fp = fopen("../client/plugins/" . $fichier, "w");
				fputs($fp, $rec);
				fclose($fp);
			}
		}
	}

}

	echo "<br />";
	
	$cnx = new Cnx();
	
	$query_cnx = "ALTER TABLE  `administrateur` CHANGE  `niveau`  `profil` INT( 11 ) NOT NULL";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "update administrateur set profil=1";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "CREATE TABLE `autorisation` ( 
	  `id` int(11) NOT NULL auto_increment,
	  `nom` text NOT NULL,
  	  `type` int(11) NOT NULL,
	  PRIMARY KEY  (`id`)
	) AUTO_INCREMENT=1 ;";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "CREATE TABLE `autorisationdesc` (
	  `id` int(11) NOT NULL auto_increment,
	  `autorisation` int(11) NOT NULL,
	  `titre` text NOT NULL,
	  `chapo` text NOT NULL,
	  `description` text NOT NULL,
	  `postscriptum` text NOT NULL,
	  `lang` int(11) NOT NULL,
	  PRIMARY KEY  (`id`)
	) AUTO_INCREMENT=1 ;";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "CREATE TABLE `autorisation_administrateur` (
	  `id` int(11) NOT NULL auto_increment,
	  `administrateur` int(11) NOT NULL,
	  `autorisation` int(11) NOT NULL,
	  `lecture` smallint(6) NOT NULL,
	  `ecriture` smallint(6) NOT NULL,
	  PRIMARY KEY  (`id`)
	) AUTO_INCREMENT=1 ;";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "CREATE TABLE `autorisation_profil` (
	  `id` int(11) NOT NULL auto_increment,
	  `profil` int(11) NOT NULL,
	  `autorisation` int(11) NOT NULL,
	  `lecture` int(11) NOT NULL,
	  `ecriture` int(11) NOT NULL,
	  PRIMARY KEY  (`id`)
	) AUTO_INCREMENT=1 ;";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "CREATE TABLE `profil` (
	  `id` int(11) NOT NULL auto_increment,
	  `nom` text NOT NULL,
	  PRIMARY KEY  (`id`)
	) AUTO_INCREMENT=1 ;";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "CREATE TABLE `profildesc` (
	  `id` int(11) NOT NULL auto_increment,
	  `profil` int(11) NOT NULL,
	  `titre` text NOT NULL,
	  `chapo` text NOT NULL,
	  `description` text NOT NULL,
	  `postscriptum` text NOT NULL,
	  `lang` int(11) NOT NULL,
	  PRIMARY KEY  (`id`)
	) AUTO_INCREMENT=1 ;";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);


	$listeinsert = array(		
	"INSERT INTO `profildesc` VALUES(1, 1, 'Super administrateur', '', '', '', 1);",
	"INSERT INTO `profildesc` VALUES(2, 2, 'Gestionnaire des commandes', '', '', '', 1);",
	"INSERT INTO `profildesc` VALUES(3, 3, 'Gestionnaire du catalogue', '', '', '', 1);",
	"INSERT INTO `profil` VALUES(1, 'superadministrateur');",
	"INSERT INTO `profil` VALUES(2, 'gestionnairecommande');",
	"INSERT INTO `profil` VALUES(3, 'gestionnairecatalogue');",
	"INSERT INTO `autorisation_profil` VALUES(1, 2, 1, 1, 1);",
	"INSERT INTO `autorisation_profil` VALUES(2, 2, 2, 1, 1);",
	"INSERT INTO `autorisation_profil` VALUES(3, 2, 7, 1, 1);",
	"INSERT INTO `autorisation_profil` VALUES(4, 2, 8, 1, 1);",
	"INSERT INTO `autorisation_profil` VALUES(5, 3, 3, 1, 1);",
	"INSERT INTO `autorisation_profil` VALUES(6, 3, 4, 1, 1);",
	"INSERT INTO `autorisation_profil` VALUES(7, 3, 5, 1, 1);",
	"INSERT INTO `autorisation_profil` VALUES(8, 3, 7, 1, 1);",
	"INSERT INTO `autorisation_profil` VALUES(9, 3, 8, 1, 1);",
	"INSERT INTO `autorisationdesc` VALUES(1, 1, 'Acc&egrave;s aux clients', '', '', '', 1);",
	"INSERT INTO `autorisationdesc` VALUES(2, 2, 'Acc&egrave;s aux commandes', '', '', '', 1);",
	"INSERT INTO `autorisationdesc` VALUES(3, 3, 'Acc&egrave;s au catalogue', '', '', '', 1);",
	"INSERT INTO `autorisationdesc` VALUES(4, 4, 'Acc&egrave;s aux contenus', '', '', '', 1);",
	"INSERT INTO `autorisationdesc` VALUES(5, 5, 'Acc&egrave;s aux codes promos', '', '', '', 1);",
	"INSERT INTO `autorisationdesc` VALUES(6, 6, 'Acc&egrave;s &agrave; la configuration', '', '', '', 1);",
	"INSERT INTO `autorisationdesc` VALUES(7, 7, 'Acc&egrave;s aux modules', '', '', '', 1);",
	"INSERT INTO `autorisationdesc` VALUES(8, 8, 'Acc&egrave;s aux recherches', '', '', '', 1);",
	"INSERT INTO `autorisation` VALUES(1, 'acces_clients', 1);",
	"INSERT INTO `autorisation` VALUES(2, 'acces_commandes', 1);",
	"INSERT INTO `autorisation` VALUES(3, 'acces_catalogue', 1);",
	"INSERT INTO `autorisation` VALUES(4, 'acces_contenu', 1);",
	"INSERT INTO `autorisation` VALUES(5, 'acces_codespromos', 1);",
	"INSERT INTO `autorisation` VALUES(6, 'acces_configuration', 1);",
	"INSERT INTO `autorisation` VALUES(7, 'acces_modules', 1);",
	"INSERT INTO `autorisation` VALUES(8, 'acces_rechercher', 1);");

	foreach($listeinsert as $insert)
		$resul_cnx = mysql_query($insert,$cnx->link);

	$query_cnx = "CREATE TABLE  `autorisation_modules` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`administrateur` INT NOT NULL ,
	`module` INT NOT NULL ,
	`autorise` INT NOT NULL
	)";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);	

	$query_cnx = "update variable set valeur='142' where nom='version'";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

		
?>