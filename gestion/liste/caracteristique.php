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

function lister_caracteristiques($critere, $order) {

	$caracteristique = new Caracteristique();

	$query = "select id, classement from $caracteristique->table order by $critere $order";
	$resul = $caracteristique->query($query);

	while($resul && $row = $caracteristique->fetch_object($resul)){
		$caracteristiquedesc = new Caracteristiquedesc();
		$caracteristiquedesc->charger($row->id);

		$fond="ligne_".($i++%2 ? "claire":"fonce")."_rub";

		?>
		<ul class="<?php echo($fond); ?>">
			<li style="width:770px;"><span id="<?php echo $row->id; ?>" class="texte_edit"><?php echo($caracteristiquedesc->titre); ?></span></li>
			<li style="width:37px;"><a href="<?php echo "caracteristique_modifier.php?id=$row->id"; ?>"><?php echo trad('editer', 'admin'); ?></a></li>
			<li style="width:71px;">
			 <div class="bloc_classement">
			    <div class="classement"><a href="caracteristique_modifier.php?id=<?php echo($row->id); ?>&action=modclassement&type=M"><img src="gfx/up.gif" border="0" /></a></div>
			    <div class="classement"><span id="classementcarac_<?php echo $row->id ?>" class="classement_edit"><?php echo $row->classement; ?></span></div>
			    <div class="classement"><a href="caracteristique_modifier.php?id=<?php echo($row->id); ?>&action=modclassement&type=D"><img src="gfx/dn.gif" border="0" /></a></div>
			 </div>
			</li>
			<li style="width:37px; text-align:center;"><a onclick="return suppr_carac(<?php echo $row->id ?>);" href="<?php echo "caracteristique_modifier.php?id=$row->id&action=supprimer"; ?>" ><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
		</ul>
		<?php
	}
}

function lister_caracteristiques_rubrique($idrubrique) {

	$rubcaracteristique = new Rubcaracteristique();
	$query = "select * from $rubcaracteristique->table where rubrique=$idrubrique";
	$resul = $rubcaracteristique->query($query);
	$i=0;
	while($resul && $row = $rubcaracteristique->fetch_object($resul)){
		$fond= $i++%2 ? "fonce" : "claire";

		$caracteristiquedesc = new Caracteristiquedesc($row->caracteristique);
		?>
       	<li class="<?php echo $fond; ?>">
			<div class="cellule" style="width:260px;"><?php echo $caracteristiquedesc->titre; ?></div>
			<div class="cellule" style="width:260px;">&nbsp;</div>
			<div class="cellule_supp"><a href="javascript:caracteristique_supprimer(<?php echo $row->caracteristique; ?>)"><img src="gfx/supprimer.gif" /></a></div>
		</li>
  		<?php
	}
}

function caracteristique_liste_select($idrubrique){
	$rubcaracteristique = new Rubcaracteristique();
	$query = "select * from $rubcaracteristique->table where rubrique=$idrubrique";
	$resul = $rubcaracteristique->query($query);
	$listeid = "";
	while($resul && $row = $rubcaracteristique->fetch_object($resul)){
		$listeid .= $row->caracteristique.",";
	}
	if(strlen($listeid) > 0){
		$listeid = substr($listeid,0,strlen($listeid)-1);

		$caracteristique = new Caracteristique();
		$query = "select * from $caracteristique->table where id NOT IN($listeid)";
		$resul = $caracteristique->query($query);
	}
	else{
		$caracteristique = new Caracteristique();
		$query = "select * from $caracteristique->table";
		$resul = $caracteristique->query($query);
	}
	?>
	<select class="form_select" id="prod_caracteristique">
 	<option value="">&nbsp;</option>
	<?php
		while($resul && $row = $caracteristique->fetch_object($resul)){
			$caracteristiquedesc = new Caracteristiquedesc($row->id);
			?>
			<option value="<?php echo $row->id; ?>"><?php echo $caracteristiquedesc->titre; ?></option>
		<?php
		}
	?>
	</select>
	<?php
}
?>