<?php
#####################################################################################################
#
#					Module pour la plateforme de paiement Systempay
#						Version : 1.0 (révision 37181)
#									########################
#					Développé pour Thelia
#						Version : 1.5.1
#						Compatibilité plateforme : V2
#									########################
#					Développé par Lyra Network
#						http://www.lyra-network.com/
#						10/07/2012
#						Contact : supportvad@lyra-network.com
#
#####################################################################################################

/*
 * Page appelée par la plateforme de paiement pour confirmation
 */
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php"); 
require_once(SITE_DIR."client/plugins/systempay/config.php");
require_once(realpath(dirname(__FILE__)) . "/systempay_api.php");

require_once("../../../classes/Navigation.class.php");
require_once("../../../classes/Commande.class.php");
require_once("../../../classes/Variable.class.php");
require_once("../../../fonctions/divers.php");

/*
 * Initialisation des variables
 */
$systempay_resp = new SystempayResponse(
	$_REQUEST,
	MODULE_PAYMENT_SYSTEMPAY_CTX_MODE,
	MODULE_PAYMENT_SYSTEMPAY_KEY_TEST,
	MODULE_PAYMENT_SYSTEMPAY_KEY_PROD
);

$from_server = $systempay_resp->get('hash');

session_start();

// Urls
$url_success = urlfond('merci');
$url_error = urlfond('regret');
$url_retry = urlfond('commande');

/*
 * Vérification de l'authenticité de la requête
 */
if(! $systempay_resp->isAuthentified()) {
	if($from_server) {
		die($systempay_resp->getOutputForGateway('auth_fail'));
	} else {
		header("Location:" . $url_error);
		die();
	}
}

// Requête authentique

/*
 * Récupération de la commande
 */
$commande = new Commande();
$reference = mysql_real_escape_string($systempay_resp->get('order_id')); // On n'est jamais trop prudent
$commande->charger_ref($reference);

/*
 * Action selon les différents cas
 */
if($commande->statut == 1) {
	// Commande non payée
	if($systempay_resp->isAcceptedPayment()) {
		// Paiement accepté
		$commande->statut = 2;	// statut "payé"
		$commande->genfact();	// génération facture
		$commande->maj();
		
		modules_fonction("confirmation", $commande);	// traitements supplémentaires
		
		// Message de confirmation
		if($from_server) {
			// Une réponse courte pour la plateforme de paiement
			die($systempay_resp->getOutputForGateway('payment_ok'));
		} else {
			// Une belle page pour le client
			if($systempay_resp->get('ctx_mode') == 'TEST') {
				echo '<html>';
				echo '<head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head>';
				echo '<body>';
				echo "Avertissement mode TEST : la commande a bien été enregistrée, mais la validation automatique n'a pas fonctionné.";
				echo "<br/>Vérifiez que vous avez correctement configuré l'url serveur (".MODULE_PAYMENT_SYSTEMPAY_URL_CHECK.") ";
				echo "dans l'outil de gestion de caisse Systempay et qu'elle est accessible depuis internet";
				echo '<br/>En mode production, vous serez redirigé automatiquement vers <a href="'.$url_success.'">la page de succès</a>';
				echo '</body></html>';
				exit();
			} else {
				header("Location:" . $url_success);
				exit();
			}
		}
	}
	else {
		// Paiement échoué => on met la commande en annulé 
    $commande->statut = 5;
    $commande->maj();
    
		// Message de confirmation
		if($from_server) {
			// Une réponse courte pour la plateforme de paiement
			die($systempay_resp->getOutputForGateway('payment_ko'));
		} else {
			// Retour à la liste des moyens de paiement pour le client
			header("Location:" . $url_retry);
			exit();
		}
	}

} else {
	// Commande déjà payée
	if($systempay_resp->isAcceptedPayment()) {
		// Paiement accepté, déjà enregistré
		if($from_server) {
			// Une réponse courte pour la plateforme de paiement
			die($systempay_resp->getOutputForGateway('payment_ok_already_done'));
		} else {
			// Une belle page pour le client
			header("Location:" . $url_success);
			exit();
		}
		
	}
	else {
		// Paiement échoué pour une commande déjà enregistrée (cas anormal)
		if($from_server) {
			// Une réponse courte pour la plateforme de paiement
			die($systempay_resp->getOutputForGateway('payment_ko_on_order_ok'));
		} else {
			// Une belle page pour le client
			header("Location:" . $url_error);
			exit();
		}
	}
}

?>