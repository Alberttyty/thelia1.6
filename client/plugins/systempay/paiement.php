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

require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
require_once(SITE_DIR."client/plugins/systempay/config.php");
require_once(realpath(dirname(__FILE__)) . "/systempay_api.php");

require_once("../../../classes/Navigation.class.php");
require_once("../../../classes/Venteadr.class.php");
require_once("../../../classes/Lang.class.php");
require_once("../../../classes/Devise.class.php");

session_start();

// order amount ...
$total = $_SESSION['navig']->panier->total() + $_SESSION['navig']->commande->port;
$total -= $_SESSION['navig']->commande->remise;

// addresses
$adrfact = new Venteadr();
$adrfact->charger($_SESSION['navig']->commande->adrfact);

$adrliv =  new Venteadr();
$adrliv->charger($_SESSION['navig']->commande->adrlivr);


$systempay_api = new SystempayApi();

// get currency
$currency = new Devise();
$currency->charger($_SESSION['navig']->devise);

$systempay_currency = $systempay_api->findCurrencyByAlphaCode($currency->code);
if($systempay_currency == null) {
	// store currency is not supported, use Systempay default currency
	$systempay_currency = $systempay_api->findCurrencyByAlphaCode('###ALPHA_CURRENCY###');
}

// Systempay Args
$misc_params = array(
		'amount' => $systempay_currency->convertAmountToInteger($total),
		'contrib' => 'Thelia1.5.1_1.0',
		'currency' => $systempay_currency->num,
		'order_id' => $_SESSION['navig']->commande->ref,

		// billing address info
		'cust_id' => $_SESSION['navig']->client->id,
		'cust_email' => $_SESSION['navig']->client->email,
		
		'cust_first_name' => $adrfact->prenom,
		'cust_last_name' => $adrfact->nom,
		'cust_address' => $adrfact->adresse1 . ($adrfact->adresse2 != '' ? ' ' .  $adrfact->adresse2 : ''),
		'cust_zip' => $adrfact->cpostal,
		'cust_country' => '', // Thelia n'utilise pas les codes ISO 3166
		'cust_phone' => $adrfact->tel,
		'cust_city' => $adrfact->ville,

		// shipping address info
		'ship_to_first_name' => $adrliv->prenom,
		'ship_to_last_name' => $adrliv->nom,
		'ship_to_street' => $adrliv->adresse1,
		'ship_to_street2' => $adrliv->adresse2,
		'ship_to_city' => $adrliv->ville,
		'ship_to_country' => '', // Thelia n'utilise pas les codes ISO 3166
		'ship_to_zip' => $adrliv->cpostal,
		'ship_to_phone_num' => $adrliv->tel,
);
$systempay_api->setFromArray($misc_params);

// detect language
$lang = new Lang();
$lang->charger($_SESSION['navig']->lang);

if($lang && $lang->code && in_array(strtolower($lang->code), $systempay_api->getSupportedLanguages())) {
	$systempay_api->set('language', strtolower($lang->code));
} else {
	$systempay_api->set('language', MODULE_PAYMENT_SYSTEMPAY_LANGUAGE);
}

// other configuration params
$config_keys = array(
		'site_id', 'key_test', 'key_prod', 'ctx_mode', 'platform_url', 'available_languages', 'capture_delay', 
		'validation_mode', 'payment_cards', 'redirect_enabled', 'redirect_success_timeout', 'redirect_success_message', 
		'redirect_error_timeout', 'redirect_error_message', 'return_mode', 'url_return'
);

foreach($config_keys as $key) {
	$cfg_key = 'MODULE_PAYMENT_SYSTEMPAY_' . strtoupper($key);
	$systempay_api->set($key, defined($cfg_key) ? constant($cfg_key) : '');
}

// Affichage du formulaire avec envoi javascript immédiat
?>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="-1">
	<title>Redirection vers la plateforme de paiement par CB</title>
</head>
<body onLoad="document.forms[0].submit()">
	<?php echo $systempay_api->getRequestHtmlForm('name="systempay_form"');?>
	<script type="text/javascript">document.forms[0].style.display='none';</script>
  
  
  <p style="text-align:center;"><br/><br/><img src="logo.jpg" alt=""/><br/><br/>Connexion au serveur de paiement<br/><br/>Merci de patientez.<br/><br/></p>

  
</body>
</html>