<?php
header('HTTP/1.1 200 OK');
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/divers.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
require_once(SITE_DIR."/client/plugins/paypal/config_okdhsu74plk5.php");

$reponse = '';
$donnees = '';

$chaine="cmd=_notify-validate";

foreach ($_POST as $champs=>$valeur) {
   $donnes["$champs"] = $valeur;
   $chaine .= '&'.$champs.'='.urlencode(stripslashes($valeur));
}

// Open the connection to paypal
$ch = curl_init($serveur);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $chaine);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

if ( !($reponse = curl_exec($ch)) ) {
    error_log("PAYPAL Got " . curl_error($ch) . " when processing IPN data");
    curl_close($ch);
    exit;
}
curl_close($ch);

// inspect IPN validation result and act accordingly
if (strcmp($reponse, "VERIFIED") == 0) {
    // The IPN is verified, process it
    $ref_commande = $_POST['invoice'];

  	$commande = new Commande();
  	$commande->charger_ref($ref_commande);
    $commande->statut = 2;
    $commande->genfact();
  	$commande->maj();

    //mail('thierry@pixel-plurimedia.fr', 'Test Paypal VERIFIED', $reponse.'   -   ICI : '.print_r($commande));
    error_log("Paypal : email_de_confirmation_thelia : ".print_r($email_de_confirmation_thelia,true));

    if($email_de_confirmation_thelia) {
        error_log("Paypal : commande : ".print_r($commande,true));
        ActionsModules::instance()->appel_module("confirmation", $commande);
        ActionsModules::instance()->appel_module("statut", $commande);
      	//modules_fonction("confirmation", $commande);
        //modules_fonction("statut", $commande);
    }

} else if (strcmp($reponse, "INVALID") == 0) {
    // IPN invalid, log for manual investigation
    mail('thierry@pixel-plurimedia.fr', 'Test Paypal INVALID', $reponse.'   -   ICI : '.print_r($commande));
}

?>
