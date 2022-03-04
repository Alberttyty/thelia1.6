<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*		email : info@thelia.net                                                      */
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
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

/*
 * Affichage de la liste des produits d'une rubrique
 */
function liste_produits($rubrique, $critere, $order, $alpha, $limit=500) {
//RAJOUT D'UN ARGUMENT LIMIT DANS LA REQUETE
	$produit = new Produit();
	$produitdesc = new Produitdesc();
	
	//RAJOUT D'UNE PAGINATION
	if(!empty($_GET['page'])) {
		$page = $_GET['page']-1;
	} else {
		$page = 0;
	}
	
	if($alpha == "alpha"){
		$query = "select p.* from $produit->table p LEFT JOIN $produitdesc->table pd ON pd.produit = p.id and lang="  . ActionsLang::instance()->get_id_langue_courante() . " where p.rubrique=\"$rubrique\" order by pd.$critere $order";
	}else{
		$limit=$page*$limit.','.$limit;
		$query = "select * from $produit->table where rubrique=\"$rubrique\" order by $critere $order LIMIT $limit";
	}

	$resul = $produit->query($query);
	$i=0;
	while($resul && $row = $produit->fetch_object($resul)){

		$produit->charger($row->ref);

		$produitdesc = new Produitdesc();
		$produitdesc->charger($row->id);

		if (! $produitdesc->affichage_back_office_permis()) continue;

		$fond = "ligne_".($i++%2 ? "claire":"fonce");

		$image = new Image();
		$query_image = "select * from $image->table where produit=\"" . $row->id . "\" order by classement limit 0,1";
		$resul_image = $image->query($query_image);
		$row_image = $image->fetch_object($resul_image);
		?>

<ul class="<?php echo($fond); ?>">
	<li><div class="vignette"><?php if($row_image) { ?> <img src="../fonctions/redimlive.php?nomorig=<?php echo $row_image->fichier;?>&type=produit&width=51&height=51&exact=1" title="<?php echo($produit->ref); ?>" /><?php }  ?></div></li>
	<li style="width:61px;"><span class="texte_noedit" title="<?php echo $row->ref; ?>"><?php echo(substr($row->ref,0,9)); if(strlen($row->ref)>9) echo " ..."; ?></span></li>
	<li style="width:225px;"><span id="titreprod_<?php echo $row->id; ?>" <?php if ($produitdesc->est_langue_courante()) echo 'class="texte_edit"'; ?>><?php echo substr($produitdesc->titre,0,35); if(strlen($produitdesc->titre) > 35) echo " ..."; ?></span></li>
	<li style="width:39px;"><span id="stock_<?php echo $row->id; ?>" class="texte_edit"><?php echo($row->stock); ?></span></li>
	<li style="width:30px;"><span id="prix_<?php echo $row->id; ?>" class="texte_edit"><?php echo($row->prix); ?></span></li>
	<li style="width:68px;"><span id="prix2_<?php echo $row->id; ?>" class="texte_edit"><?php echo($row->prix2); ?></span></li>
	<li style="width:64px;"><input id="promo_<?php echo $row->id; ?>" type="checkbox" name="promo[]" class="sytle_checkbox" onchange="checkvalues('promo','<?php echo $row->id; ?>')" <?php if($row->promo) { ?> checked="checked" <?php } ?>/></li>
	<li style="width:64px;"><input type="checkbox" id="nouveaute_<?php echo $row->id; ?>" name="nouveaute[]" class="sytle_checkbox" onchange="checkvalues('nouveaute','<?php echo $row->id; ?>')" <?php if($row->nouveaute) { ?> checked="checked" <?php } ?>/></li>
	<li style="width:53px;"><input type="checkbox" id="prod_ligne_<?php echo $row->id; ?>" name="ligne[]" class="sytle_checkbox" onchange="checkvalues('ligneprod','<?php echo $row->id; ?>')" <?php if($row->ligne) { ?> checked="checked" <?php } ?>/></li>
	<li style="width:41px;"><a href="produit_modifier.php?ref=<?php echo($produit->ref); ?>&rubrique=<?php echo($produit->rubrique); ?>"  class="txt_vert_11"><?php echo trad('editer', 'admin'); ?></a></li>

	<li style="width:78px; text-align:center;">
	<div class="bloc_classement">
  <div class="classement">
		<a href="produit_modifier.php?ref=<?php echo($produit->id ); ?>&action=modclassement&parent=<?php echo($rubrique); ?>&type=M"><img src="gfx/up.gif" border="0" /></a>
	</div>
	 <div class="classement"><span id="classementprod_<?php echo $produit->id; ?>" class="classement_edit"><?php echo $row->classement; ?></span></div>
	 <div class="classement">
		<a href="produit_modifier.php?ref=<?php echo($produit->id ); ?>&action=modclassement&parent=<?php echo($rubrique); ?>&type=D"><img src="gfx/dn.gif" border="0" /></a>
	</div>
	</div>
	</li>
	<li style="width:37px; text-align:center;"><a href="javascript:supprimer_produit('<?php echo $produit->ref ?>','<?php echo($rubrique); ?>')"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>

<?php
	}
}
?>
