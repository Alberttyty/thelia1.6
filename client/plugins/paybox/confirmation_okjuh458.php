<?php

$err_report = E_ALL & ~E_NOTICE;

if(defined('E_DEPRECATED')) $err_report = $err_report & ~E_DEPRECATED;
if(defined('E_STRICT')) $err_report = $err_report & ~E_STRICT;

error_reporting($err_report);

require_once(realpath(dirname(__FILE__)) . '/../../../fonctions/divers.php');
require_once(realpath(dirname(__FILE__)) . '/../../../fonctions/mutualisation.php');
require_once(realpath(dirname(__FILE__)) . '/Paybox.class.php');
require_once(realpath(dirname(__FILE__)) . '/../../../classes/Commande.class.php');
require_once(realpath(dirname(__FILE__)) . '/../../../classes/Mail.class.php');
require_once(realpath(dirname(__FILE__)) . '/../../../classes/Variable.class.php');
require_once(realpath(dirname(__FILE__)) . '/config.php');

$valeur = Paybox::lire('PBX_RETOUR');

//Récupère les variables qui doivent être retournées par paybox en supprimant le paramètre signature
$valeur = explode(';',$valeur);
unset($valeur[count($valeur)-1]);

//création du tableau contenant les valeurs renvoyés par paybox
$payboxValues = array();
foreach($valeur as $param) {
    $tempParam = explode(':',$param);
    $payboxValues[$tempParam[0]] = $_GET[$tempParam[0]];
}

$stringParam = '';
//vérification de la signature
foreach($payboxValues as $key => $value) {
    $stringParam .= "&".$key.'='.$value;
}

$stringParam = ltrim($stringParam,'&');

$signature = base64_decode($_GET['sign']);
//display_error($signature, true);
$keyfile = SITE_DIR . 'client/plugins/paybox/paybox.pem';

$fsize =  filesize( $keyfile );

$fp = fopen($keyfile, "r");
$filedata = fread($fp, $fsize);
fclose($fp);

$pubkey = openssl_pkey_get_public( $filedata );

if(! openssl_verify($stringParam, $signature, $pubkey) ){

    Mail::envoyer(
            Variable::lire('nomsite'),
            Variable::lire('emailcontact'),
            Variable::lire('nomsite'),
            Variable::lire('emailfrom'),
            "Problème lors du paiement paybox",
            "Un problème est survenue lors de la vérification du retour de paiement. Merci de contacter votre support technique",
            "Un problème est survenue lors de la vérification du retour de paiement. Merci de contacter votre support technique");

    exit;
}

$tabError = array(
  '00001' => 'La connexion au centre d’autorisation a échoué. Vous pouvez dans ce cas là effectuer les redirections des internautes vers le FQDN tpeweb1.paybox.com.',
  '001xx' => 'Paiement refusé par le centre d’autorisation',
  '00003' => 'Erreur Paybox',
  '00004' => 'Numéro de porteur ou cryptogramme visuel invalide.',
  '00006' => 'Accès refusé ou site/rang/identifiant incorrect.',
  '00008' => 'Date de fin de validité incorrecte',
  '00009' => 'Erreur de création d’un abonnement.',
  '00010' => 'Devise inconnue.',
  '00011' => 'Montant incorrect.',
  '00015' => 'Paiement déjà effectué.',
  '00016' => 'Abonné déjà existant (inscription nouvel abonné). Valeur ‘U’ de la variable PBX_RETOUR.',
  '00021' => 'Carte non autorisée.',
  '00029' => 'Carte non conforme. Code erreur renvoyé lors de la documentation de la variable « PBX_EMPREINTE ».',
  '00030' => 'Temps d’attente > 15 mn par l’internaute/acheteur au niveau de la page de paiements.',
  '00031' => 'Code réservé par paybox',
  '00032' => 'Code réservé par paybox',
  '00033' => 'Code pays de l’adresse IP du navigateur de l’acheteur non autorisé.',
  '00040' => 'Opération sans authentification 3DSecure, bloquée par le filtre.'
);

//La signature est vérifiée, on peut passer au traitement de la réponse.

$ref = $payboxValues['ref'];

$commande = new Commande();
if($commande->charger_trans($ref)){
    if($payboxValues['erreur'] == '00000'/* && $payboxValues['auto'] == 'XXXXXX'*/){

        $commande->statut = 2;
        $commande->genfact();

        $commande->maj();
        ActionsModules::instance()->appel_module("confirmation", $commande);
        ActionsModules::instance()->appel_module("statut", $commande);
    }
    else{

        $commande->statut = 5;
        $commande->maj();
        ActionsModules::instance()->appel_module("statut", $commande);

        $message = 'Une erreur a été reporté par paybox lors du retour de la commande ayant pour référence '.$commande->ref.'\r\n';
        $message .= $payboxValues['erreur'].' : '.$tabError[$payboxValues['erreur']].'\r\n';
        $message .= 'Auto : '.$payboxValues['auto'];

        //traitement des erreurs.
        Mail::envoyer(Variable::lire('nomsite'),
            Variable::lire('emailcontact'),
            Variable::lire('nomsite'),
            Variable::lire('emailfrom'),
            "erreur lors du retour paiment Paybox",
            nl2br($message),
            $message);

    }
}
else{
    Mail::envoyer(Variable::lire('nomsite'),
            Variable::lire('emailcontact'),
            Variable::lire('nomsite'),
            Variable::lire('emailfrom'),
            "erreur lors du retour paiment Paybox",
            "Lors du retour Paybox, la commande n'a pu être identifié. Paramètres de la commande : ".print_r($payboxValues, true)
            , "Lors du retour Paybox, la commande n'a pu être identifié. Paramètres de la commande : ".print_r($payboxValues, true)
    );
}


?>
