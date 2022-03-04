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

function lister_contenus($parent, $critere, $order, $alpha) {
		$contenu = new Contenu();
		$contenudesc = new Contenudesc();

		if($alpha == "alpha"){
			$query = "select c.id, c.ligne, c.classement from $contenu->table c LEFT JOIN $contenudesc->table cd ON cd.contenu = c.id and lang=".ActionsLang::instance()->get_id_langue_courante()." where dossier=\"$parent\" order by cd.$critere $order";
		}else{
			$query = "select id, ligne, classement from $contenu->table where dossier=\"$parent\" order by $critere $order";
		}

		$resul = $contenu->query($query);
		$i=0;
		while($resul && $row = $contenu->fetch_object($resul)){
			$contenudesc = new Contenudesc();
			$contenudesc->charger($row->id);

			if (! $contenudesc->affichage_back_office_permis()) continue;

			$fond="ligne_".($i++%2 ? "claire":"fonce")."_rub";
	?>

	<ul class="<?php echo($fond); ?>">
		<li style="width:627px;">
		<span id="titrecont_<?php echo $row->id; ?>" <?php if ($contenudesc->est_langue_courante()) echo 'class="texte_edit"'; ?>><?php echo substr($contenudesc->titre,0,90); if(strlen($contenudesc->titre) > 90) echo " ..."; ?></span></li>
		<li style="width:54px;">
			<input type="checkbox" id="cont_ligne_<?php echo $row->id; ?>" name="ligne[]" class="sytle_checkbox" onchange="checkvalues('lignecont','<?php echo $row->id; ?>')" <?php if($row->ligne) { ?> checked="checked" <?php } ?> />
		</li>
		<li style="width:54px;"></li>
		<li style="width:34px;"><a href="contenu_modifier.php?id=<?php echo($row->id); ?>&dossier=<?php echo $parent; ?>" class="txt_vert_11"><?php echo trad('editer', 'admin'); ?></a></li>
		<li style="width:71px;">
		 <div class="bloc_classement">
		    <div class="classement"><a href="contenu_modifier.php?id=<?php echo($row->id); ?>&action=modclassement&parent=<?php echo($parent); ?>&type=M"><img src="gfx/up.gif" border="0" /></a></div>
		    <div class="classement"><span id="classementcontenu_<?php echo $row->id; ?>" class="contenudos_edit"><?php echo $row->classement; ?></span></div>
		    <div class="classement"><a href="contenu_modifier.php?id=<?php echo($row->id); ?>&action=modclassement&parent=<?php echo($parent); ?>&type=D"><img src="gfx/dn.gif" border="0" /></a></div>
		 </div>
		</li>
		<li style="width:37px; text-align:center;"><a href="javascript:supprimer_contenu('<?php echo($row->id); ?>', '<?php echo($parent); ?>');"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
	</ul>

	<?php
	}
}
?>