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

function lister_contenuassoc($type, $objet) {

	if ($type == 1) {
		$obj = new Produit();
		$obj->charger($objet);
	} else {
		$obj = new Rubrique();
		$obj->charger($objet);
	}

	$contenuassoc = new Contenuassoc();
	$contenua = new Contenu();
	$contenuadesc = new Contenudesc();

	$query = "select * from $contenuassoc->table where type='$type' and objet='$obj->id' order by classement";
	$resul = $contenuassoc->query($query);

	$i = 0;

	while ($resul && $row = $contenuassoc->fetch_object($resul)) {

		$fond = $i++ % 2 ? "fonce" : "claire";

		$contenua->charger($row->contenu);
		$contenuadesc->charger($contenua->id);

		$dossierdesc = new Dossierdesc();
		$dossierdesc->charger($contenua->dossier);

?>
		<li class="<?php echo $fond; ?>">
				<div class="cellule" style="width:260px;"><?php echo $dossierdesc->titre; ?></div>
				<div class="cellule" style="width:260px;"><?php echo $contenuadesc->titre; ?></div>
				<div class="cellule_supp"><a href="javascript:contenuassoc_supprimer(<?php echo $row->id;?>, <?php echo $type ?>,'<?php echo $objet; ?>')"><img src="gfx/supprimer.gif" /></a></div>
		</li>
<?php
	}
}
?>