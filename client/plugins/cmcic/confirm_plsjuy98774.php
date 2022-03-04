<?php
header("Pragma: no-cache");
header("Content-type: text/plain");
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/divers.php");
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
// TPE Settings
// Warning !! CMCIC_Config contains the key, you have to protect this file with all the mechanism available in your development environment.
// You may for instance put this file in another directory and/or change its name. If so, don't forget to adapt the include path below.
require_once(SITE_DIR."/client/plugins/cmcic/config_kjhgs51452.php");
// --- PHP implementation of RFC2104 hmac sha1 ---
require_once("CMCIC_Tpe.inc.php");
// Begin Main : Retrieve Variables posted by CMCIC Payment Server
$vars = $_POST;
// TPE init variables
$oHmac = new CMCIC_Hmac(new CMCIC_Tpe());
$request_mac = strtolower($vars['MAC']);
unset($vars['MAC']);
$computed_mac = $oHmac->computeHmac(CMCIC_Hmac::getHashable($vars));

if ($computed_mac == strtolower($computed_mac)) {
    $commande = new Commande();
    $commande->charger((int)$vars['reference']);

  	switch($vars['code-retour']) {
	  		case "Annulation" :
	  			// Payment has been refused
	  			// put your code here (email sending / Database update)
	  			// Attention : an autorization may still be delivered for this payment
	  			$commande->statut = 5;
	  			$commande->maj();
	  			ActionsModules::instance()->appel_module("statut", $commande);
	  			break;

	  		case "payetest":
	  			// Payment has been accepeted on the test server
	  			// put your code here (email sending / Database update)
	  			$commande->statut=2;
	  			$commande->genfact();
	  			ActionsModules::instance()->appel_module("confirmation", $commande);
	  			$commande->maj();
	  			ActionsModules::instance()->appel_module("statut", $commande);
	  			break;

	  		case "paiement":
	  			// Payment has been accepted on the productive server
	  			// put your code here (email sending / Database update)
	  			$commande->statut=2;
	  			$commande->genfact();
	  			ActionsModules::instance()->appel_module("confirmation", $commande);
	  			$commande->maj();
	  			ActionsModules::instance()->appel_module("statut", $commande);
	  			break;
  	}

  	$receipt = CMCIC_CGI2_MACOK;
}
else {
  	 // your code if the HMAC doesn't match
  	 $receipt = CMCIC_CGI2_MACNOTOK.CMCIC_Hmac::getHashable($inputs);
}

//-----------------------------------------------------------------------------
// Send receipt to CMCIC server
//-----------------------------------------------------------------------------
printf(CMCIC_CGI2_RECEIPT, $receipt);
// Copyright (c) 2009 Euro-Information ( mailto:centrecom@e-i.com )
// All rights reserved. ---
?>
