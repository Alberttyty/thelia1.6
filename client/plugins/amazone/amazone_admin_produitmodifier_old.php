<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("amazone");

require_once(realpath(dirname(__FILE__)) . "/Amazone.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Amazoneproduits.class.php");

$amazone = new Amazoneproduits();

$amazone->charger_reference($_GET['ref']);

?>

<div class="entete">
    <div class="titre">AMAZONE</div>
    <div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()">VALIDER LES
            MODIFICATIONS</a></div>                  
</div>
<table width="100%" cellpadding="5" cellspacing="0">
    <tr class="fonce">
        <td class="designation">Catégorie de produits</td>
        <td><input name="amazone_recommended_browse_nodes" id="amazone_recommended_browse_nodes" type="text" class="form_court" value="<?php echo($amazone->recommended_browse_nodes); ?>"/></td>
    </tr>
    <tr class="clair">
        <td class="designation">Couleur standardisée</td>
        <td><input name="amazone_color_map" id="amazone_color_map" type="text" class="form_court" value="<?php echo($amazone->color_map); ?>"/></td>
    </tr>
</table>