<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("amazone");

require_once(realpath(dirname(__FILE__)) . "/Amazone.class.php");

$amazone = new Amazone();

$amazone->rubrique=$_GET['id'];
$amazone->charger_rubrique();

?>

<div class="entete">
    <div class="titre">AMAZONE</div>
    <div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()">VALIDER LES
            MODIFICATIONS</a></div>
</div>
<table width="100%" cellpadding="5" cellspacing="0">
    <tr class="fonce">
        <td class="designation">Identifiant de la categorie</td>
        <td><input name="amazone_categorie" id="amazone_categorie" type="text" class="form_court" value="<?php echo($amazone->categorie); ?>"/></td>
    </tr>
</table>