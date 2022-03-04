<?php

include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");

autorisation("prixht");

$monproduit = new Produit();
$monproduit->charger($_REQUEST['ref']);

if($monproduit->prix!=0)
$prixht=round($monproduit->prix/(1+($monproduit->tva/100)),2);
else
$prixht=0;

if($monproduit->prix2!=0)
$prix2ht=round($monproduit->prix2/(1+($monproduit->tva/100)),2);
else
$prix2ht=0;

echo '
<div class="module_prixht">
<div class="entete">
			<div class="titre">PRIX HT</div>
			<div class="fonction_valider"><a href="#" onclick="envoyer()">VALIDER LES MODIFICATIONS</a></div>
</div>

<table cellpadding="5" cellspacing="0" width="100%" class="prixht">

   	<tbody>
    <tr class="claire">
        <td class="designation">Prix HT</td>
        <td><input name="prixht" id="prixht" class="form_court" value="'.$prixht.'" type="text"></td>
   	</tr>
    <tr class="fonce">
        <td class="designation">Prix en promo HT</td>
        <td><input name="prix2ht" id="prix2ht" class="form_court" value="'.$prix2ht.'" type="text"></td>
   	</tr>
    </tbody>         
    
</table>
</div>
';

?>