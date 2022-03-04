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

function lister_produitsassocies($type,$id) {

	$produitsassocies = new Produitsassocies();
	$produita = new Produit();
	$produitdesca = new Produitdesc();
  
  $produits=$produitsassocies->charger_produits($type,$id);
  $query = "select * from $produitsassocies->table where type=\"".$type."\" AND id_objet=\"".$id."\" order by classement";
	$resul = $produitsassocies->query($query);
  while($resul && $row = $produitsassocies->fetch_object($resul)){
    $produita->charger_id($row->id_produit);
  	$produitdesca->charger($produita->id);
  
  	$rubadesc = new Rubriquedesc();
  	$rubadesc->charger($produita->rubrique);
  
  	$fond = $i++%2 ? "fonce" : "claire";
    ?>
  		      <li class="<?php echo $fond; ?>">
  						<div class="cellule" style="width:260px;"><?php echo $rubadesc->titre; ?></div>
  						<div class="cellule" style="width:260px;"><?php echo $produitdesca->titre; ?></div>
  						<div class="cellule_supp"><a href="javascript:produitsassocies_supprimer(<?php echo $row->id; ?>)"><img src="gfx/supprimer.gif" /></a></div>
  					</li>
  	<?php
  }
  
}
?>