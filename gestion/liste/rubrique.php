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
 * Affichage de la liste des rubriques
 */
function liste_rubriques($parent, $critere, $order, $alpha) {

	$rubrique = new Rubrique();
	$rubriquedesc = new Rubriquedesc();

	if($alpha == "alpha"){
		$query = "select r.id, r.ligne, r.classement from $rubrique->table r LEFT JOIN $rubriquedesc->table rd ON rd.rubrique=r.id and lang=" . ActionsLang::instance()->get_id_langue_courante() . " where r.parent=\"$parent\" order by rd.$critere $order";
	}else{
		$query = "select id, ligne, classement from $rubrique->table where parent=\"$parent\" order by $critere $order";
	}

	$resul = $rubrique->query($query);

	$i=0;

	while($resul && $row = $rubrique->fetch_object($resul)){
		$rubriquedesc = new Rubriquedesc();
		$rubriquedesc->charger($row->id);

		if (! $rubriquedesc->affichage_back_office_permis()) continue;

		$fond="ligne_".($i++%2 ? "claire":"fonce")."_rub";
		?>

<ul class="<?php echo($fond); ?>">
	<li style="width:623px;"><span id="titrerub_<?php echo $row->id; ?>" <?php if ($rubriquedesc->est_langue_courante()) echo 'class="texte_edit"'; ?>><?php echo substr($rubriquedesc->titre,0,80); if(strlen($rubriquedesc->titre) > 80) echo " ..."; ?></span></li>
	<li style="width:53px;"><input type="checkbox" id="rub_ligne_<?php echo $row->ref; ?>" name="ligne[]" class="sytle_checkbox" onchange="checkvalues('lignerub','<?php echo $row->id; ?>')" <?php if($row->ligne) { ?> checked="checked" <?php } ?>/></li>
	<li style="width:54px;"><?php if($rubriquedesc->rubrique) { ?><a href="parcourir.php?parent=<?php echo($row->id); ?>" ><?php echo trad('parcourir', 'admin'); ?></a><?php } ?></li>
	<li style="width:34px;"><a href="rubrique_modifier.php?id=<?php echo($row->id); ?>" class="txt_vert_11"><?php echo trad('editer', 'admin'); ?></a></li>

	<li style="width:71px;">
	 <div class="bloc_classement">
	    <div class="classement"><a href="rubrique_modifier.php?id=<?php echo($row->id); ?>&action=modclassement&parent=<?php echo($parent); ?>&type=M"><img src="gfx/up.gif" border="0" /></a></div>
	    <div class="classement"><span id="classementrub_<?php echo $row->id ?>" class="classement_edit"><?php echo $row->classement; ?></span></div>
	    <div class="classement"><a href="rubrique_modifier.php?id=<?php echo($row->id); ?>&action=modclassement&parent=<?php echo($parent); ?>&type=D"><img src="gfx/dn.gif" border="0" /></a></div>
	 </div>
	</li>
	<li style="width:37px; text-align:center;"><a href="javascript:supprimer_rubrique('<?php echo $row->id ?>','<?php echo($parent); ?>')"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>

		<?php
	}
}
?>
