<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("ebay");

require_once(realpath(dirname(__FILE__)) . "/Ebay.class.php");

$ebay = new Ebay();

$ebay->rubrique=$_GET['id'];
$ebay->charger_rubrique();

?>

<div class="entete">
    <div class="titre">EBAY</div>
    <div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()">VALIDER LES
            MODIFICATIONS</a></div>
</div>
<table width="100%" cellpadding="5" cellspacing="0">
    <tr class="fonce">
        <td class="designation">Identifiant de la categorie</td>
        <td><input name="ebay_categorie" id="ebay_categorie" type="text" class="form_court" value="<?php echo($ebay->categorie); ?>"/></td>
    </tr>
</table>