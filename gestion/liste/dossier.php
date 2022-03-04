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

function lister_dossiers($parent, $critere, $order, $alpha) {

	$dossier = new Dossier();
	$dossierdesc = new Dossierdesc();

	if($alpha == "alpha"){
		$query = "select d.id, d.ligne, d.classement from $dossier->table d LEFT JOIN $dossierdesc->table dd ON dd.dossier=d.id and lang=".ActionsLang::instance()->get_id_langue_courante()." where parent=\"$parent\" order by dd.$critere $order";
	}else{
		$query = "select id, ligne, classement from $dossier->table where parent=\"$parent\" order by $critere $order";
	}


	$resul = $dossier->query($query);

	$i=0;
	while($resul && $row = $dossier->fetch_object($resul)){
		$dossierdesc = new Dossierdesc();
		$dossierdesc->charger($row->id);

		if (! $dossierdesc->affichage_back_office_permis()) continue;

		$fond="ligne_".($i++%2 ? "claire":"fonce")."_rub";
?>

<ul class="<?php echo($fond); ?>">
	<li style="width:629px;"><span id="titredos_<?php echo $row->id; ?>" <?php if ($dossierdesc->est_langue_courante()) echo 'class="texte_edit"'; ?>><?php echo substr($dossierdesc->titre,0,90); if(strlen($dossierdesc->titre) > 90) echo " ..."; ?></span></li>
	<li style="width:54px;">
		<input type="checkbox" id="dos_ligne_<?php echo $row->id; ?>" name="ligne[]" class="sytle_checkbox" onchange="checkvalues('lignedos','<?php echo $row->id; ?>')" <?php if($row->ligne) { ?> checked="checked" <?php } ?> />
	</li>
	<li style="width:54px;"><a href="listdos.php?parent=<?php echo($row->id); ?>" class="txt_vert_11"><?php echo trad('parcourir', 'admin'); ?></a></li>
	<li style="width:34px;"><a href="dossier_modifier.php?id=<?php echo($row->id); ?>" class="txt_vert_11"><?php echo trad('editer', 'admin'); ?></a></li>

	<li style="width:71px;">
	 <div class="bloc_classement">
	    <div class="classement"><a href="dossier_modifier.php?id=<?php echo($row->id); ?>&action=modclassement&parent=<?php echo($parent); ?>&type=M"><img src="gfx/up.gif" border="0" /></a></div>
	    <div class="classement"><span id="classementdossier_<?php echo $row->id; ?>" class="classement_edit"><?php echo $row->classement; ?></span></div>
	    <div class="classement"><a href="dossier_modifier.php?id=<?php echo($row->id); ?>&action=modclassement&parent=<?php echo($parent); ?>&type=D"><img src="gfx/dn.gif" border="0" /></a></div>
	 </div>
	</li>
	<li style="width:37px; text-align:center;"><a href="javascript:supprimer_dossier('<?php echo($row->id); ?>', '<?php echo($parent); ?>')"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>

<?php
	}
}
?>