<?php
#####################################################################################################
#
#					Module pour la plateforme de paiement Systempay
#						Version : V1.0a
#									########################
#					Développé pour Thelia
#						Version : 1.4.2.1
#									########################
#					Auteur Lyra Network
#						03/2010
#						Contact : supportvad@lyra-network.com
#
#####################################################################################################
/*
 * Page appelée par la plateforme de paiement pour confirmation
 */
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");  
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Navigation.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php"); 
require_once(SITE_DIR."/client/plugins/vads/config_qhjsd452.php");
require_once(realpath(dirname(__FILE__)) . "/lang_french.php");
require_once(realpath(dirname(__FILE__)) . "/vad_api.php");

require_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Stock.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Ventedeclidisp.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Venteprod.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/divers.php");

if (!isset($_POST["order_id"] ) || empty($_POST["order_id"] ) ) {
	// pas d'order_id, c'est une erreur
	header("Location:".MODULE_PAYMENT_VADS_URL_ERROR);
	
} else {
	// Récupération paramètres pour analyse
	$vad_api = new VADS_API();
	$vad_api->setResponseFromPost(
		$_POST,
		MODULE_PAYMENT_VADS_KEY_TEST,
		MODULE_PAYMENT_VADS_KEY_PROD,
		MODULE_PAYMENT_VADS_CTX_MODE
	);
	
	if($vad_api->isAuthentifiedResponse() && $vad_api->isAcceptedPayment()) {
		//TODO tester s'il n'y a pas des traitements gênants traités 2 fois (serveur/serveur et retour client)
		// Paiement réalisé avec succès
		$commande = new Commande();
		$reference = mysql_real_escape_string($_POST['order_id']); // On n'est jamais trop prudent
		$commande->charger_ref($reference); // Récupération de la commande
		
		$commande->statut = 2;	// statut "payé"
		$commande->genfact();	// génération facture
		$commande->maj();
		
		modules_fonction("confirmation",$commande);	// traitements supplémentaires
		
		// direction le message de succès
		$variable_loader = new Variable();
		$variable_loader->charger("urlsite");
		$urlsite = $variable_loader->valeur;
		$urlsite = (substr($urlsite,0,-1)=="/") ? $urlsite : $urlsite."/";
		header("Location:".$urlsite."merci.php");
		exit();
		
	} else {
		$commande = new Commande();
		$reference = mysql_real_escape_string($_POST['order_id']); // On n'est jamais trop prudent
		$commande->charger_ref($reference); // Récupération de la commande
		
		$commande->statut = "5";
		$commande->maj();
   		$venteprod = new Venteprod();
   		$query = 'SELECT * FROM '.$venteprod->table.' WHERE commande = '.$commande->id;
   		$resul = mysql_query($query, $venteprod->link);
		while($row = mysql_fetch_object($resul)){
			// incrémentation du stock général
			$produit = new Produit();   
			$produit->charger($row->ref);
			$produit->stock = $produit->stock + $row->quantite;
			$produit->maj();
			$vdec = new Ventedeclidisp();
			$query2 = 'SELECT * FROM '.$vdec->table.' WHERE venteprod = '.$row->id;
			$resul2 = mysql_query($query2, $vdec->link);
			while($row2 = mysql_fetch_object($resul2)){
				$stock = new Stock();
				if($stock->charger($row2->declidisp, $produit->id)){
					$stock->valeur = $stock->valeur + $row->quantite;
					$stock->maj();					
				}
		  	}
		// Erreur survenue lors du paiement
		header("Location:".MODULE_PAYMENT_VADS_URL_ERROR);
	 	}
	}
}

?>