<?php

$err_report = E_ALL & ~E_NOTICE;

if(defined('E_DEPRECATED')){
	$err_report = $err_report & ~E_DEPRECATED;
}

if(defined('E_STRICT')){
	$err_report = $err_report & ~E_STRICT;
}

error_reporting($err_report);

include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
include_once realpath(dirname(__FILE__)) . '/../../../classes/Navigation.class.php';
include_once realpath(dirname(__FILE__)) . '/../../../classes/Devise.class.php';
include_once realpath(dirname(__FILE__)) . '/../../../classes/Variable.class.php';
include_once realpath(dirname(__FILE__)) . '/Paybox.class.php';
include_once realpath(dirname(__FILE__)) . "/config.php";

session_start();

$total = round($total = $_SESSION['navig']->commande->total, 2)*100;

$devise = new Devise($_SESSION['navig']->commande->devise);
$transaction = urlencode($_SESSION['navig']->commande->transaction);


switch($devise->code){
    case 'USD':
        $deviseNum = 840;
        break;
    case 'GBP':
        $deviseNum = 826;
        break;
    case 'EUR':
    default : 
        $deviseNum = 978;
        break;
}

$paybox = new Paybox();

$paybox->loadValues()
        ->addValues(array(
            'PBX_TOTAL' => $total,
            'PBX_DEVISE' => $deviseNum,
            'PBX_CMD' => $transaction,
            'PBX_PORTEUR' => $_SESSION['navig']->client->email,
            'PBX_TIME' => date("c")
        ));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
<meta http-equiv="cache-control" content="no-store, no-cache, must-revalidate, post-check=0, pre-check=0" />
<meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<title>Connexion au serveur de paiement</title>
<link type="text/css" rel="stylesheet" href="paybox.css" />
</head>
<body onload="document.getElementById('formulaire_paybox').submit();">
<h1>Veuillez patienter...</h1>
<form method="post" id="formulaire_paybox" action="<?php echo $serveur; ?>">
<p>
<?php foreach($paybox->getValues() as $key => $value): ?>
<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
<?php endforeach; ?>
<input type="image" src="<?php echo Variable::lire('urlsite') . "/client/plugins/paybox/logo.jpg" ?>" />
</p>
</form>
</body>