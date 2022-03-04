<?php

// Permet d'utiliser un Cnx.class.php pre-1.5.1 dans Baseobj.class.php lors d'une update vers 1.5.1 ou supérieur
// Voir dans Baseobj.class.php.
define('IN_UPDATE_THELIA_150', true);

require_once(__DIR__ . "/../../classes/Cnx.class.php");
require_once(__DIR__ . "/../../classes/Produit.class.php");
require_once(__DIR__ . "/../../classes/Rubrique.class.php");
require_once(__DIR__ . "/../../classes/Dossier.class.php");
require_once(__DIR__ . "/../../classes/Contenu.class.php");
require_once(__DIR__ . "/../../classes/Modules.class.php");

require_once(__DIR__ . "/../../fonctions/hierarchie.php");

error_reporting(E_ALL ^ E_NOTICE);

/* ------------------------------------------------------------------------------------------------------------------*/
/* Anciennes fonctions de rewriting */

function ancien_rewrite_prod($ref, $lang=1){

	$prod = new Produit();
	$prod->charger($ref);

    $proddesc = new Produitdesc();
    if(! $proddesc->charger($prod->id, $lang))
		return "";

	$rubfinal = $prod->rubrique;

	if (function_exists('chemin_rub'))
		$chem = chemin_rub($rubfinal);
	else
		$chem = chemin($rubfinal); // 1.4.2.1 et avant

	if (! empty($chem))
	{
		$rubriquedesc = new Rubriquedesc();

		$listrub = "";

		$rubriquedesc->charger($chem[count($chem)-1]->rubrique, $lang);
		$listrub .= $rubriquedesc->titre . "_";

		$rubriquedesc->charger($chem[0]->rubrique, $lang);
		$listrub .= $rubriquedesc->rubrique . "_";



		for($i=count($chem)-2; $i>=0; $i--){
			$rubriquedesc->charger($chem[$i]->rubrique, $lang);
			$listrub .= $rubriquedesc->titre . "_";
		}


	    $listrub .= $proddesc->titre . "__" . $prod->ref . ".html";

		return eregurl($listrub);
	}
	else
	{
		return "";
	}
}

function ancien_rewrite_rub($id, $lang=1){

	$rub = new Rubrique();
	$rub->charger($id);

	$chem = chemin_rub($id);

	if (! empty($chem))
	{
		$rubriquedesc = new Rubriquedesc();

		$listrub = "";

		if(! $rubriquedesc->charger($chem[count($chem)-1]->rubrique, $lang))
			return "";

		$listrub .= $rubriquedesc->titre . "_";

		$rubriquedesc->charger($chem[0]->rubrique, $lang);
		$listrub .= $rubriquedesc->rubrique . "_";

		for($i=count($chem)-2; $i>=0; $i--){
			$rubriquedesc->charger($chem[$i]->rubrique, $lang);
			$listrub .= $rubriquedesc->titre . "_";
		}


		$listrub .= ".html";

		return eregurl($listrub);
	}
	else
	{
		return "";
	}
}

function ancien_rewrite_cont($id, $lang=1){

	$cont = new Contenu();
	$cont->charger($id);

	$dosfinal = $cont->dossier;
	$chem = chemin_dos($dosfinal);

	if (! empty($chem))
	{
		$dossierdesc = new Dossierdesc();

		$listdos = "";

		$dossierdesc->charger($chem[count($chem)-1]->dossier, $lang);
		$listdos .= $dossierdesc->titre . "__";

		$dossierdesc->charger($chem[0]->dossier, $lang);
		$listdos .= $dossierdesc->dossier . "_";



		for($i=count($chem)-2; $i>=0; $i--){
			$dossierdesc->charger($chem[$i]->dossier, $lang);
			$listdos .= $dossierdesc->titre . "_";
		}

		$contenudesc = new Contenudesc();
		if(! $contenudesc->charger($cont->id, $lang))
			return "";

		$listdos .= $contenudesc->titre . "_" . $cont->id . ".html";

		return eregurl($listdos);
	}
	else
	{
		return "";
	}
}
function ancien_rewrite_dos($id, $lang=1){

	$chem = chemin_dos($id);

	if (! empty($chem))
	{
		$dossierdesc = new Dossierdesc();

		$listdos = "";

		if( ! $dossierdesc->charger($chem[count($chem)-1]->dossier, $lang))
			return "";

		$listdos .= $dossierdesc->titre . "__";

		$dossierdesc->charger($chem[0]->dossier, $lang);
		$listdos .= $dossierdesc->dossier . "_";



		for($i=count($chem)-2; $i>=0; $i--){
			$dossierdesc->charger($chem[$i]->dossier, $lang);
			$listdos .= $dossierdesc->titre . "_";
		}


		$listdos .= ".html";



		return eregurl($listdos);
	}
	else
	{
		return "";
	}
}

/*------------------------------------------------------------------------------------------------------------------*/

/* Nouveau fichier Cnx.class.php */

$cnx = new Cnx();
mysql_query("SET NAMES UTF8");

if(! isset($_GET['rewrite_150'])){
	$newfile = file_get_contents(__DIR__ . "/1.5.0/NewCnx.class.php");
	$cnxfile = file_get_contents(__DIR__ . "/../../classes/Cnx.class.php");

	preg_match('/public static \$host[^\"]*\"([^\"]*)\"/', $cnxfile, $res);
	$newfile = str_replace("votre_serveur",$res[1],$newfile);

	preg_match('/public static \$login_mysql[^\"]*\"([^\"]*)\"/', $cnxfile, $res);
	$newfile = str_replace("votre_login_mysql",$res[1],$newfile);

	preg_match('/public static \$password_mysql[^\"]*\"([^\"]*)\"/', $cnxfile, $res);
	$newfile = str_replace("votre_motdepasse_mysql",$res[1],$newfile);

	preg_match('/public static \$db[^\"_]*\"([^\"]*)\"/', $cnxfile, $res);
	$newfile = str_replace("bdd_sql",$res[1],$newfile);

	file_put_contents(__DIR__ . "/../../classes/Cnx.class.php",$newfile);

	$query_cnx = "ALTER TABLE `produit` DROP `appro`";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "ALTER TABLE `produit` DROP `reappro`";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "ALTER TABLE `rubrique` DROP `boutique` ";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	/* conversion UTF-8 */

	$query_cnx = "ALTER DATABASE " . $res[1] . " CHARACTER SET utf8;";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "show tables";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	while($row_cnx = mysql_fetch_array($resul_cnx)){

		$query2_cnx = "ALTER TABLE " . $row_cnx[0] . " CHARACTER SET UTF8";
		$resul2_cnx = mysql_query($query2_cnx, $cnx->link);

		$query3_cnx = "SHOW fields FROM " . $row_cnx[0];
		$resul3_cnx = mysql_query($query3_cnx, $cnx->link);
		while($row3_cnx = mysql_fetch_array($resul3_cnx)){
			if(strstr($row3_cnx[1], "text") || strstr($row3_cnx[1], "varchar")){
				$query4_cnx = "ALTER TABLE `" . $row_cnx[0] . "` CHANGE `" . $row3_cnx[0] . "` `" . $row3_cnx[0] . "` ".$row3_cnx[1]." CHARACTER SET utf8 COLLATE utf8_general_ci";
				$resul4_cnx = mysql_query($query4_cnx, $cnx->link);
			}
		}

	}

	/* Nouveau parseur */
	$query_cnx = "insert into  variable (nom, valeur, protege, cache) VALUES (\"rewrite\",  \"0\", 0, 0);";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "insert into  variable (nom, valeur, protege, cache) VALUES (\"prx_show_time\",  \"1\", 1, 1);";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "insert into  variable (nom, valeur, protege, cache) VALUES (\"prx_use_cache\",  \"1\", 1, 1);";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "insert into  variable (nom, valeur, protege, cache) VALUES (\"prx_allow_debug\",  \"1\", 0, 1);";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "insert into  variable (nom, valeur, protege, cache) VALUES (\"prx_cache_file_lifetime\",  \"24\", 1, 1);";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "insert into  variable (nom, valeur, protege, cache) VALUES (\"prx_cache_check_period\",  \"2\", 1, 1);";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "insert into  variable (nom, valeur, protege, cache) VALUES (\"prx_cache_check_time\",  \"0\", 1, 1);";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	@mkdir("../client/cache/parseur");
	@chmod("../client/cache/parseur", 0775);


	$query_cnx = "CREATE TABLE `raison` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `defaut` smallint(6) NOT NULL,
	  `classement` int(11) NOT NULL,
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "INSERT INTO `raison` (`id`, `defaut`, `classement`) VALUES
	(1, 1, 1),
	(2, 0, 2),
	(3, 0, 3);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "CREATE TABLE `raisondesc` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `raison` int(11) NOT NULL,
	  `lang` int(11) NOT NULL,
	  `court` text NOT NULL,
	  `long` text NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `lang` (`lang`),
	  KEY `raison` (`raison`)
	) DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "INSERT INTO `raisondesc` (`id`, `raison`, `lang`, `court`, `long`) VALUES
	(1, 1, 1, 'Mme', 'Madame'),
	(2, 2, 1, 'Mlle', 'Mademoiselle'),
	(3, 3, 1, 'M', 'Monsieur'),
	(4, 1, 2, 'Mrs', 'Madam'),
	(5, 2, 2, 'Miss', 'Miss'),
	(6, 3, 2, 'Mr', 'Sir'),
	(7, 1, 2, 'Sra.', 'Señora'),
	(8, 2, 2, 'Srta.', 'Señorita'),
	(9, 3, 2, 'Sr.', 'Señor');";

	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	@mkdir("../client/lang");
	@mkdir("../client/lang/admin");
	@chmod("../client/lang", 0775);
	@chmod("../client/lang/admin", 0775);

	@copy(__DIR__ . "/1.5.0/lang/admin/1.php", "../client/lang/admin/1.php");
	@copy(__DIR__ . "/1.5.0/lang/admin/2.php", "../client/lang/admin/2.php");
	@copy(__DIR__ . "/1.5.0/lang/admin/3.php", "../client/lang/admin/3.php");
	@chmod("../client/lang/admin/1.php", 0775);
	@chmod("../client/lang/admin/2.php", 0775);
	@chmod("../client/lang/admin/3.php", 0775);

	/* Nouveaux indexes */
	$query_cnx="ALTER TABLE `accessoire` DROP INDEX `thelia_accessoire_produit_idx` , ADD INDEX `produit` ( `produit` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `caracdispdesc` DROP INDEX `thelia_caracdispdesc_caracdisp_idx` , ADD INDEX `caracdisp` ( `caracdisp` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `caracteristiquedesc` DROP INDEX `thelia_caracteristiquedesc_caracteristique_idx` , ADD INDEX `caracteristique` ( `caracteristique` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `client` DROP INDEX `thelia_client_ref_idx` , ADD INDEX `ref` ( `ref` ( 30 ) );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `commande` DROP INDEX `thelia_commande_client_idx` , ADD INDEX `client` ( `client` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `commande` DROP INDEX `thelia_commande_ref_idx` , ADD INDEX `ref` ( `ref` ( 30 ) );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `contenudesc` DROP INDEX `thelia_contenudesc_contenu_idx` , ADD INDEX `contenu` ( `contenu` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `declidispdesc` DROP INDEX `thelia_declidispdesc_declidisp_idx` , ADD INDEX `declidisp` ( `declidisp` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `declinaisondesc` DROP INDEX `thelia_declinaisondesc_declinaison_idx` , ADD INDEX `declinaison` ( `declinaison` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `documentdesc` DROP INDEX `thelia_documentdesc_document_idx` , ADD INDEX `document` ( `document` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `dossierdesc` DROP INDEX `thelia_dossierdesc_dossier_idx` , ADD INDEX `dossier` ( `dossier` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `imagedesc` DROP INDEX `thelia_imagedesc_image_idx` , ADD INDEX `image` ( `image` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `messagedesc` DROP INDEX `thelia_messagedesc_message_idx` , ADD INDEX `message` ( `message` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `modulesdesc` DROP INDEX `thelia_moduledesc_plugin_idx` , ADD INDEX `plugin` ( `plugin` ( 30 ) );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `paysdesc` DROP INDEX `thelia_paysdesc_pays_idx` , ADD INDEX `pays` ( `pays` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `produit` DROP INDEX `thelia_produit_ref_idx` , ADD INDEX `ref` ( `ref` ( 30 ) );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `produitdesc` DROP INDEX `thelia_produitdesc_produit_idx` , ADD INDEX `produit` ( `produit` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `rubriquedesc` DROP INDEX `thelia_rubriquedesc_rubrique_idx` , ADD INDEX `rubrique` ( `rubrique` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `statutdesc` DROP INDEX `thelia_statutdesc_statut_idx` , ADD INDEX `statut` ( `statut` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `ventedeclidisp` DROP INDEX `thelia_ventedeclidisp_venteprod_idx` , ADD INDEX `venteprod` ( `venteprod` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `venteprod` DROP INDEX `thelia_venteprod_commande_idx` , ADD INDEX `commande` ( `commande` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);


	$query_cnx="ALTER TABLE `autorisationdesc` ADD INDEX ( `autorisation` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `autorisationdesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `autorisation_administrateur` ADD INDEX ( `administrateur` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `autorisation_administrateur` ADD INDEX ( `autorisation` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `autorisation_modules` ADD INDEX ( `administrateur` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `autorisation_modules` ADD INDEX ( `module` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `autorisation_profil` ADD INDEX ( `profil` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `autorisation_profil` ADD INDEX ( `autorisation` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `caracdisp` ADD INDEX ( `caracteristique` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `caracdispdesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `caracval` ADD INDEX ( `produit` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `caracval` ADD INDEX ( `caracteristique` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `contenu` ADD INDEX ( `dossier` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `contenu` ADD INDEX ( `ligne` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `contenuassoc` ADD INDEX ( `objet` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `contenuassoc` ADD INDEX ( `type` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `contenudesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `declidisp` ADD INDEX ( `declinaison` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `declidispdesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `declinaisondesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `document` ADD INDEX ( `produit` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `document` ADD INDEX ( `rubrique` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `document` ADD INDEX ( `contenu` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `document` ADD INDEX ( `dossier` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `documentdesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `dossier` ADD INDEX ( `parent` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `dossier` ADD INDEX ( `ligne` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `dossierdesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `exdecprod` ADD INDEX ( `produit` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `exdecprod` ADD INDEX ( `declidisp` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `image` ADD INDEX ( `produit` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `image` ADD INDEX ( `rubrique` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `image` ADD INDEX ( `contenu` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `image` ADD INDEX ( `dossier` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `imagedesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `messagedesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `modules` ADD INDEX ( `type` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `modules` ADD INDEX ( `actif` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `modulesdesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `pays` ADD INDEX ( `zone` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `pays` ADD INDEX ( `defaut` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `paysdesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `produit` ADD INDEX ( `ligne` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `produitdesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `profildesc` ADD INDEX ( `profil` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `profildesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `promo` ADD INDEX ( `utilise` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `promo` ADD INDEX ( `illimite` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `promoutil` ADD INDEX ( `promo` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `promoutil` ADD INDEX ( `commande` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `rubcaracteristique` ADD INDEX ( `rubrique` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `rubcaracteristique` ADD INDEX ( `caracteristique` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `rubdeclinaison` ADD INDEX ( `rubrique` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `rubdeclinaison` ADD INDEX ( `declinaison` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `rubrique` ADD INDEX ( `parent` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `rubrique` ADD INDEX ( `ligne` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `rubriquedesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `statutdesc` ADD INDEX ( `lang` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `stock` ADD INDEX ( `declidisp` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `stock` ADD INDEX ( `produit` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `transzone` ADD INDEX ( `transport` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `transzone` ADD INDEX ( `zone` );";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx="ALTER TABLE `ventedeclidisp` ADD INDEX ( `declidisp` );";

	$query_cnx = "update modules set actif=0 where nom='filtremodulo'";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);


	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (1, 'Customer access', '', '', '', 2);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (2, 'Orders access', '', '', '', 2);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (3, 'Catalog access', '', '', '', 2);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (4, 'Content access', '', '', '', 2);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (5, 'Coupon codes access', '', '', '', 2);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (6, 'Configuration access', '', '', '', 2);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (7, 'Plugins access', '', '', '', 2);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (8, 'Search access', '', '', '', 2);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (9, 'Stats access', '', '', '', 2);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (1, 'Customer access', '', '', '', 3);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (2, 'Orders access', '', '', '', 3);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (3, 'Catalog access', '', '', '', 3);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (4, 'Content access', '', '', '', 3);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (5, 'Coupon codes access', '', '', '', 3);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (6, 'Configuration access', '', '', '', 2);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (7, 'Plugins access', '', '', '', 3);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (8, 'Search access', '', '', '', 3);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "INSERT INTO `autorisationdesc` (`autorisation`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (9, 'Stats access', '', '', '', 3);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);



	$query_cnx = "INSERT INTO `profildesc` (`profil`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (1, 'Super administrator', '', '', '', 2);";
	$query_cnx = "INSERT INTO `profildesc` (`profil`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (2, 'Order manager', '', '', '', 2);";
	$query_cnx = "INSERT INTO `profildesc` (`profil`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (3, 'Catalog manager', '', '', '', 2);";
	$query_cnx = "INSERT INTO `profildesc` (`profil`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (1, 'Super administrador', '', '', '', 3);";
	$query_cnx = "INSERT INTO `profildesc` (`profil`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (2, 'Gestión de los pedidos', '', '', '', 3);";
	$query_cnx = "INSERT INTO `profildesc` (`profil`, `titre`, `chapo`, `description`, `postscriptum`, `lang`) VALUES (3, 'Gestión del catalogo', '', '', '', 3);";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	// Désactiver le module parserex
	mysql_query("update modules set actif=0 where nom='parserex'");

	/* Rewriting */

	$query_cnx = "CREATE TABLE IF NOT EXISTS `reecriture` (
	 `id` int(11) NOT NULL AUTO_INCREMENT,
	 `url` varchar(255) NOT NULL,
	 `fond` varchar(255) NOT NULL,
	 `param` varchar(255) NOT NULL,
	 `actif` smallint(6) NOT NULL,
	 `lang` int(11) NOT NULL,
	 PRIMARY KEY (`id`),
	 KEY `url` (`url`),
	 KEY `lang` (`lang`)
	) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
?>
	<script type="text/javascript">
		location="maj.php?rewrite_150=1";
	</script>
<?php
exit;
} else if($_GET['rewrite_150'] == "1"){

	$query_lang = "select * from lang";
	$resul_lang = mysql_query($query_lang, $cnx->link);

	while($row_lang = mysql_fetch_object($resul_lang)){

		$query_cnx = "select * from rubrique";
		$resul_cnx = mysql_query($query_cnx, $cnx->link);
		while($row_cnx = mysql_fetch_object($resul_cnx)){
			$url = ancien_rewrite_rub($row_cnx->id, $row_lang->id);
			if($url == "")
				continue;
			$query_rewrite = "insert into reecriture(url, fond, param, lang, actif) values(\"$url\", \"rubrique\", \"&id_rubrique=" . $row_cnx->id . "\", \"" . $row_lang->id . "\", \"1\")";
			$resul_rewrite = mysql_query($query_rewrite, $cnx->link);

		}
	}

	$query_lang = "select * from lang";
	$resul_lang = mysql_query($query_lang, $cnx->link);

	while($row_lang = mysql_fetch_object($resul_lang)){

		$query_cnx = "select * from contenu";
		$resul_cnx = mysql_query($query_cnx, $cnx->link);
		while($row_cnx = mysql_fetch_object($resul_cnx)){
			$url = ancien_rewrite_cont($row_cnx->id, $row_lang->id);
			if($url == "")
				continue;
			$query_rewrite = "insert into reecriture(url, fond, param, lang, actif) values(\"$url\", \"contenu\", \"&id_contenu=" . $row_cnx->id . "&id_dossier=" . $row_cnx->dossier . "\", \"" . $row_lang->id ."\", \"1\")";
			$resul_rewrite = mysql_query($query_rewrite, $cnx->link);

		}
	}

	$query_lang = "select * from lang";
	$resul_lang = mysql_query($query_lang, $cnx->link);

	while($row_lang = mysql_fetch_object($resul_lang)){

		$query_cnx = "select * from dossier";
		$resul_cnx = mysql_query($query_cnx, $cnx->link);
		while($row_cnx = mysql_fetch_object($resul_cnx)){
			$url = ancien_rewrite_dos($row_cnx->id, $row_lang->id);
			if($url == "")
				continue;
			$query_rewrite = "insert into reecriture(url, fond, param, lang, actif) values(\"$url\", \"dossier\", \"&id_dossier=" . $row_cnx->id . "\", \"" . $row_lang->id ."\", \"1\")";
			$resul_rewrite = mysql_query($query_rewrite, $cnx->link);

		}
	}

?>
<script type="text/javascript">
	location="maj.php?rewrite_150=2";
</script>
<?php
exit;
} else if($_GET['rewrite_150'] == "2"){

	if(! isset($_GET['rewrite_150_2_debut']))
		$debut = 0;
	else
		$debut = $_GET['rewrite_150_2_debut'];

	$query_cnx = "select count(*) as nb from produit";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$nb = mysql_result($resul_cnx, 0, $nb);

	$query_lang = "select * from lang";
	$resul_lang = mysql_query($query_lang, $cnx->link);

	while($row_lang = mysql_fetch_object($resul_lang)){

		$query_cnx = "select * from produit order by id limit $debut,200";
		$resul_cnx = mysql_query($query_cnx, $cnx->link);
		while($row_cnx = mysql_fetch_object($resul_cnx)){
			$url = ancien_rewrite_prod($row_cnx->ref, $row_lang->id);
			if($url == "")
				continue;
			$query_rewrite = "insert into reecriture(url, fond, param, lang, actif) values(\"$url\", \"produit\", \"&id_produit=" . $row_cnx->id . "&id_rubrique=" . $row_cnx->rubrique . "\", \"" . $row_lang->id ."\", \"1\")";
			$resul_rewrite = mysql_query($query_rewrite, $cnx->link);

		}
	}

	$debut += 200;

	if($debut < $nb){
?>
<script type="text/javascript">
	location="maj.php?rewrite_150=2&rewrite_150_2_debut=<?php echo $debut; ?>";
</script>
<?php
exit;
	} else {
?>
<script type="text/javascript">
	location="maj.php?rewrite_150=3";
</script>
<?php
exit;
	}
?>
<?php
} else if($_GET['rewrite_150'] == "3"){

    $message = "";

	if(file_exists("../.htaccess")){

    $message = "
		Un fichier htaccess a été détecté à la racine.<br />
		Si vous souhaitez utiliser la réécriture d'url de Thelia, vous devez remplacer votre fichier htaccess.<br />
		Le nouveau fichier est disponible dans le répertoire template.orig<br />
		Activez ensuite la nouvelle réécriture depuis l'interface d'administration. (Configuration, gestion des variables, rewrite à 1)<br />
		Si vous avez déjà customisé le votre, soyez prudent afin de ne perdre aucune configuration spécifique. <br /><br />
        ";
	}

    $message .= "
        Votre template doit être mis à jour pour fonctionner convenablement avec Thelia 1.5.0. Si vous souhaitez garder votre ancien template,
        vous devez télécharger et installer le plugin compat14 sur le site de contributions.<br />
        Cette méthode est à déconseiller.<br /><br />
        L'ensemble de vos plugins doivent être mis à jour.<br /><br />
    ";

	$query_cnx = "update variable set valeur='150' where nom='version'";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
}
?>
