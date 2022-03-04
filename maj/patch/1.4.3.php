<?php
	require_once(__DIR__ . "/../../classes/Cnx.class.php");
	
	$cnx = new Cnx();

	$query_cnx = "ALTER TABLE `administrateur` ADD  `lang` INT NOT NULL ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "update administrateur set lang=1";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "ALTER TABLE  `statut` ADD  `nom` TEXT NOT NULL ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	$query_cnx = "update statut set nom=\"nonpaye\" where id=1";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "update statut set nom=\"paye\" where id=2";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "update statut set nom=\"traitement\" where id=3";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "update statut set nom=\"envoye\" where id=4";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "update statut set nom=\"annule\" where id=5";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);				


	$query_cnx = "ALTER TABLE  `adresse` ADD  `entreprise` TEXT NOT NULL AFTER  `raison` ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	$query_cnx = "ALTER TABLE  `venteadr` ADD  `entreprise` TEXT NOT NULL AFTER  `raison` ;";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	
	
	$query_cnx = "select * from commande";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	while($row_cnx = mysql_fetch_object($resul_cnx)){
	        $query2_cnx = "select * from client where id=" . $row_cnx->client;
	        $resul2_cnx = mysql_query($query2_cnx, $cnx->link);
	        if(mysql_num_rows($resul2_cnx)){
				$row2_cnx = mysql_fetch_object($resul2_cnx);
		        $query3_cnx = "update venteadr set entreprise=\""  . $row2_cnx->entreprise ."\" where id=" . $row_cnx->adrfact;
		        $resul3_cnx = mysql_query($query3_cnx, $cnx->link);
			}
	}

	@unlink(__DIR__ . "/../../client/pdf/visudoc.php");
	
	//création des index
	$query_cnx = "create index thelia_accessoire_produit_idx using btree on accessoire (produit)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_caracdispdesc_caracdisp_idx using btree on caracdispdesc (caracdisp)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_caracteristiquedesc_caracteristique_idx using btree on caracteristiquedesc (caracteristique)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_client_ref_idx using btree on client (ref(30))";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_commande_client_idx using btree on commande (client)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_commande_ref_idx using btree on commande (ref(30))";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_contenudesc_contenu_idx using btree on contenudesc (contenu)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_declidispdesc_declidisp_idx using btree on declidispdesc (declidisp)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_declinaisondesc_declinaison_idx using btree on declinaisondesc (declinaison)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_rubriquedesc_rubrique_idx using btree on rubriquedesc (rubrique)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_venteprod_commande_idx using btree on venteprod (commande)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_ventedeclidisp_venteprod_idx using btree on ventedeclidisp (venteprod)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_documentdesc_document_idx using btree on documentdesc (document)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_dossierdesc_dossier_idx using btree on dossierdesc (dossier)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_imagedesc_image_idx using btree on imagedesc (image)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_messagedesc_message_idx using btree on messagedesc (message)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_modulesdesc_plugin_idx using btree on modulesdesc (plugin(30))";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_paysdesc_pays_idx using btree on paysdesc (pays)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_produit_ref_idx using btree on produit (ref(30))";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_produitdesc_produit_idx using btree on produitdesc (produit)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	$query_cnx = "create index thelia_statutdesc_statut_idx using btree on statutdesc (statut)";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);

	$query_cnx = "select valeur from variable where nom=\"emailcontact\"";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	$emailcontact = mysql_result($resul_cnx, 0, "valeur");
		
	$query_cnx = "insert into  variable (nom, valeur, protege, cache) VALUES (\"emailfrom\",  \"$emailcontact\", 0, 0);";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
		
	$query_cnx = "insert into  variable (nom, valeur, protege, cache) VALUES (\"memcache\",  \"0\", 0, 0);";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
		
	$query_cnx = "update variable set valeur='143' where nom='version'";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);
	
	$newfile = file_get_contents(__DIR__ . "/1.4.3/NewCnx.class.php");
	$newfile = str_replace("votre_serveur",$cnx->host,$newfile);
	$newfile = str_replace("votre_login_mysql",$cnx->login_mysql,$newfile);
	$newfile = str_replace("votre_motdepasse_mysql",$cnx->password_mysql,$newfile);
	$newfile = str_replace("bdd_sql",$cnx->db,$newfile);
	
	file_put_contents(__DIR__ . "/../../classes/Cnx.class.php",$newfile);
?>