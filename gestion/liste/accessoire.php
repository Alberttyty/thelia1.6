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

function lister_accessoires($refproduit) {

	$produit = new Produit();
	$produit->charger($_GET['ref']);
	$accessoire = new Accessoire();
	$produita = new Produit();
	$produitdesca = new Produitdesc();

	$query = "select * from $accessoire->table where produit='$produit->id' order by classement";
	$resul = $accessoire->query($query);

	$i = 0;

	while($resul && $row = $accessoire->fetch_object($resul)){
		$produita->charger_id($row->accessoire);
		$produitdesca->charger($produita->id);

		$rubadesc = new Rubriquedesc();
		$rubadesc->charger($produita->rubrique);

		$fond = $i++%2 ? "fonce" : "claire";
		?>

			        	 <li class="<?php echo $fond; ?>">
							<div class="cellule" style="width:260px;"><?php echo $rubadesc->titre; ?></div>
							<div class="cellule" style="width:260px;"><?php echo $produitdesca->titre; ?></div>
							<div class="cellule_supp"><a href="javascript:accessoire_supprimer(<?php echo $row->id; ?>)"><img src="gfx/supprimer.gif" /></a></div>
						</li>

		<?php
	}
}
?>