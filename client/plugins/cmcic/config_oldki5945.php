<?php
/***************************************************************************************
* Warning !! CMCIC_Config contains the key, you have to protect this file with all     *
* the mechanism available in your development environment.                             *
* You may for instance put this file in another directory and/or change its name       *
***************************************************************************************/
//code client
//define ("CMCIC_CLE", "votre cle fournit par la banque");
define("CMCIC_CLE", "B7B8812CBD8DCBE3DA8A2504F05A5BB584BAA89F");

//TPE
define("CMCIC_TPE", "0373170");


//code société
define("CMCIC_CODESOCIETE", "echangeons");


//ne pas toucher
define("CMCIC_VERSION", "3.0");

//serveur de paiement
//serveur de test, supprimer une fois vos tests effectués
//define ("CMCIC_SERVEUR", "https://paiement.creditmutuel.fr/test/");
//serveur de production, décommenter lorsque votre statut est en production en supprimant les deux // au début de la ligne suivante
define("CMCIC_SERVEUR", "https://paiement.creditmutuel.fr/");


//url de retour ok
define("CMCIC_URLOK", "http://ecm-voyages.fr/16-paiement-en-ligne-paiement-en-ligne.html?retour_paiement=ok");


//url de retour ko
define("CMCIC_URLKO", "http://ecm-voyages.fr/16-paiement-en-ligne-paiement-en-ligne.html?retour_paiement=ko");
?>
