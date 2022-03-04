<?php
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Navigation.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Pays.class.php");
// TPE Settings
// Warning !! CMCIC_Config contains the key, you have to protect this file with all the mechanism available in your development environment.
// You may for instance put this file in another directory and/or change its name. If so, don't forget to adapt the include path below.
require_once(SITE_DIR."client/plugins/cmcic/config_kjhgs51452.php");
// PHP implementation of RFC2104 hmac sha1 ---
require_once("CMCIC_Tpe.inc.php");
session_start();

// ----------------------------------------------------------------------------
//  CheckOut Stub setting fictious Merchant and Order datas.
//  That's your job to set actual order fields. Here is a stub.
// -----------------------------------------------------------------------------

// Amount : format  "xxxxx.yy" (no spaces)
$total = 0;
$total = $_SESSION['navig']->panier->total(1,$_SESSION['navig']->commande->remise) + $_SESSION['navig']->commande->port;
$total = str_replace(",", ".", $total);
if ($total<$_SESSION['navig']->commande->port) $total = $_SESSION['navig']->commande->port;
// Currency : ISO 4217 compliant
$sDevise  = "EUR";

// between 2 and 4
//$sNbrEch = "4";
$sNbrEch = "";

// date echeance 1 - format dd/mm/yyyy
//$sDateEcheance1 = date("d/m/Y");
$sDateEcheance1 = "";

// montant échéance 1 - format  "xxxxx.yy" (no spaces)
//$sMontantEcheance1 = "0.26" . $sDevise;
$sMontantEcheance1 = "";

$sDateEcheance2 = "";
$sMontantEcheance2 = "";

$sDateEcheance3 = "";
$sMontantEcheance3 = "";

$sDateEcheance4 = "";
$sMontantEcheance4 = "";

// ----------------------------------------------------------------------------

$oTpe = new CMCIC_Tpe('FR');
$oHmac = new CMCIC_Hmac($oTpe);

$clientNom = $_SESSION["navig"]->client->nom;
$clientPrenom = $_SESSION["navig"]->client->prenom;
$clientEntreprise = $_SESSION["navig"]->client->entreprise;
$clientAdresse1 = $_SESSION["navig"]->client->adresse1;
$clientAdresse2 = $_SESSION["navig"]->client->adresse2;
$clientAdresse3 = $_SESSION["navig"]->client->adresse3;
$clientVille = $_SESSION["navig"]->client->ville;
$clientCpostal = $_SESSION["navig"]->client->cpostal;
$pays = new Pays($_SESSION["navig"]->client->pays);

$sContexte = [
		"billing" => [
				"name" => substr($clientPrenom." ".$clientNom." ".$clientEntreprise, 0, 45),
				"firstName" => substr($clientPrenom, 0, 45),
				"lastName" => substr($clientNom, 0, 45),
				"addressLine1" => substr($clientAdresse1, 0, 50),
				"city" => substr($clientVille, 0, 50),
				"postalCode" => $clientCpostal,
				"country" => $pays->isoalpha2,
		],
		"shipping" => [
				"name" => substr($clientPrenom." ".$clientNom." ".$clientEntreprise, 0, 45),
				"firstName" => substr($clientPrenom, 0, 45),
				"lastName" => substr($clientNom, 0, 45),
				"addressLine1" => substr($clientAdresse1, 0, 50),
				"city" => substr($clientVille, 0, 50),
				"postalCode" => $clientCpostal,
				"country" => $pays->isoalpha2,
		],
];

if (!empty($clientAdresse2)) {
		$sContexte['billing']['addressLine2'] = substr($clientAdresse2, 0, 50);
		$sContexte['shipping']['addressLine2'] = substr($clientAdresse2, 0, 50);
}
if (!empty($clientAdresse3)) {
		$sContexte['billing']['addressLine3'] = substr($clientAdresse3, 0, 50);
		$sContexte['shipping']['addressLine3'] = substr($clientAdresse3, 0, 50);
}

$sContexteJson = json_encode($sContexte);
$sContexteUtf8 = utf8_encode($sContexteJson);
$sContexte = base64_encode($sContexteUtf8);

// Data to certify
$inputs = [
		'version' => $oTpe->sVersion,
		'TPE' => $oTpe->sNumero,
		'date' => date("d/m/Y:H:i:s"),
		'montant' => (string)round($total, 2).$sDevise,
		'reference' => $oHmac->harmonise($_SESSION['navig']->commande->id, 'numeric', 12),
		'url_retour_ok' => $oTpe->sUrlOK,
		'url_retour_err' => $oTpe->sUrlKO,
		'lgue' => strtoupper($oTpe->sLangue),
		'contexte_commande' => $sContexte,
		'societe' => $oTpe->sCodeSociete,
		"texte-libre" => $_SESSION['navig']->commande->ref,
		'mail' => $_SESSION["navig"]->client->email,
		'3dsdebrayable' => "0",
		'ThreeDSecureChallenge' => "challenge_preferred",
		// Uniquement pour le Paiement fractionné
		//'nbrech' => $sNbrEch,
		//'dateech1' => $sDateEcheance1,
		//'montantech1' => $sMontantEcheance1,
		//'dateech2' => $sDateEcheance2,
		//'montantech2' => $sMontantEcheance2,
		//'dateech3' => $sDateEcheance3,
		//'montantech3' => $sMontantEcheance3,
		//'dateech4' => $sDateEcheance4,
		//'montantech4' => $sMontantEcheance4,
];
// MAC computation
$inputs["MAC"] = $oHmac->computeHmac(CMCIC_Hmac::getHashable($inputs));
// --------------------------------------------------- End Stub ---------------
// Your Page displaying payment button to be customized
?>
<!DOCTYPE html>
<html xml:lang="fr" lang="fr">
<head>
		<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
		<meta http-equiv="cache-control" content="no-store, no-cache, must-revalidate, post-check=0, pre-check=0" />
		<meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT" />
		<meta http-equiv="pragma" content="no-cache" />
		<title>Connexion au serveur de paiement</title>
		<link type="text/css" rel="stylesheet" href="CMCIC.css" />
</head>
<body>
		<img alt="" src="logo_cm-paiement-grd.jpg" />
		<h1>Connexion au serveur de paiement / <span class="anglais">Connection to the payment server</span></h1>
		<div id="frm">
				<p>Cliquez sur le bouton ci-dessous pour vous connecter au serveur de paiement.<br /><span class="anglais">Click on the following button to be redirected to the payment server.</span></p>
				<form action="<?php echo($oTpe->sUrlPaiement);?>" id="PaymentRequest" method="post">
						<p> <?php
										foreach ($inputs as $name => $value) {
												echo('<input name="'.$name.'" type="hidden" value="'.$value.'" />');
										}
								?>
								<input id="bouton" name="bouton" type="submit" value="Connexion / Connection" />
						</p>
				</form>
				<script>
						document.forms['PaymentRequest'].submit();
				</script>
		</div>
</body>
</html>
