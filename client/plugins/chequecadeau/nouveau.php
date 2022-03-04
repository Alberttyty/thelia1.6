<?php
require_once("Chequecadeau.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("chequecadeau");

include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");

$chequecadeau = new Chequecadeau();
$commande=new Commande();

$commande->ref=-1;
$commande->id=-1;

$type=$_POST['type'];

$produit=new Produit();
$produit->charger($_POST['montant']);

$chequecadeau->montant=$produit->prix;
$chequecadeau->ref=$produit->ref;
$chequecadeau->commande=$commande->id;
$chequecadeau->genCode();

if ($chequecadeau->code!="") {
    $code=$chequecadeau->code;
    $chequecadeau->code=md5($chequecadeau->code);
    $chequecadeau->date=date('Y-m-d H:i:s');
    $id=$chequecadeau->add();

    $chequecadeau->genPdf($commande,$code,$chequecadeau->montant,$id,'affichage','',$type);
}

?>
