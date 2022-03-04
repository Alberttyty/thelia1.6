<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/
require_once("pre.php");
require_once("auth.php");
?>
<?php if(! est_autorise("acces_commandes")) exit; ?>
<?php

$produit = new Produit();
if($produit->charger($ref)){
	$produitdesc = new Produitdesc();
	$produitdesc->charger($produit->id);
	$_SESSION["commande"]->nbart++;
	$nbart = $_SESSION["commande"]->nbart;
	$_SESSION["commande"]->venteprod[$nbart-1] = new Venteprod();
	$_SESSION["commande"]->venteprod[$nbart-1]->ref = $produit->ref;
	$_SESSION["commande"]->venteprod[$nbart-1]->titre = $produitdesc->titre;
	$_SESSION["commande"]->venteprod[$nbart-1]->quantite = $_REQUEST["qtite"];
	$_SESSION["commande"]->venteprod[$nbart-1]->tva = $tva;
	$_SESSION["commande"]->venteprod[$nbart-1]->prixu = $prixu;
	
}
else{
	$_SESSION["commande"]->nbart++;
	$nbart = $_SESSION["commande"]->nbart;
	$_SESSION["commande"]->venteprod[$nbart-1] = new Venteprod();
	$_SESSION["commande"]->venteprod[$nbart-1]->ref = $ref;
	$_SESSION["commande"]->venteprod[$nbart-1]->titre = $titre;
	$_SESSION["commande"]->venteprod[$nbart-1]->quantite = $qtite;
	$_SESSION["commande"]->venteprod[$nbart-1]->tva = $tva;
	$_SESSION["commande"]->venteprod[$nbart-1]->prixu = $prixu;
}
?>

<div class="entete_client">
		<div class="titre">MON PANIER</div>
		<div class="valider"><a href="#" onclick="valid()">PASSER LA COMMANDE</a></div>
</div>
<ul class="Nav_bloc_description">
		<li style="width:50px;">Réf.</li>
		<li style="width:195px; border-left:1px solid #96A8B5;">Titre</li>
		<li style="width:45px; border-left:1px solid #96A8B5;">PU</li>
		<li style="width:25px; border-left:1px solid #96A8B5;">Qté</li>
		<li style="border-left:1px solid #96A8B5;">TVA</li>
</ul>

<?php
$j=0;
for($i=0;$i<$_SESSION["commande"]->nbart;$i++){
	if($i%2 == 0) $fond="ligne_claire_BlocDescription";
	else $fond="ligne_fonce_BlocDescription";
	$j++;
	?>
	<ul class="ligne_claire_BlocDescription">
		<li style="width:49px;"><?php echo $_SESSION["commande"]->venteprod[$i]->ref; ?></li>
		<li style="width:195px;"><?php echo $_SESSION["commande"]->venteprod[$i]->titre; ?></li>
		<li style="width:45px;"><?php echo $_SESSION["commande"]->venteprod[$i]->prixu; ?></li>
		<li style="width:25px;"><?php echo $_SESSION["commande"]->venteprod[$i]->quantite; ?></li>
		<li><?php echo $_SESSION["commande"]->venteprod[$i]->tva; ?></li>
	</ul>
	<?php
}
?>
