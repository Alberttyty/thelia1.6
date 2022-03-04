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
	require_once(__DIR__ . "/../pre.php");
	require_once(__DIR__ . "/../auth.php");

	require_once(__DIR__ . "/../../fonctions/divers.php");

	require_once(__DIR__ . "/../liste/accessoire.php");

?>
<?php if(! est_autorise("acces_catalogue")) exit; ?>
<?php

	header('Content-Type: text/html; charset=utf-8');

  if(isset($_GET['action'])) $action = $_GET['action'];
  else $action = '';

	switch($_GET['action']){
		case 'produit' : accessoire_produit(); break;
		case 'ajouter' : accessoire_ajouter(); break;
		case 'supprimer' : accessoire_supprimer(); break;
	}
?>
<?php
	function accessoire_produit(){
		$produit = new Produit();
		$produit->charger($_GET['ref']);

		$query = "select * from $produit->table where rubrique=\""  . $_GET['id_rubrique'] . "\"";
		$resul = $produit->query($query);

		while($resul && $row = $produit->fetch_object($resul)){

			$test = new Accessoire();
			if($test->charger_uni($produit->id, $row->id))
				continue;

			$produitdesc = new Produitdesc();
			$produitdesc->charger($row->id);
?>
			<option value="<?php echo $row->id; ?>"><?php echo $produitdesc->titre; ?></option>
<?php
		}
	}
?>
<?php
	function accessoire_ajouter(){
		$produit = new Produit();
		$produit->charger($_GET['ref']);

		$accessoire = new Accessoire();

		$accessoire = new Accessoire();
		$accessoire->produit = $produit->id;
		$accessoire->accessoire = $_GET['id'];
		$accessoire->add();

		lister_accessoires($_GET['ref']);
	}
?>
<?php
	function accessoire_supprimer(){
		$accessoire = new Accessoire();
		$accessoire->charger($_GET['id']);
		$accessoire->delete();

		lister_accessoires($_GET['ref']);
	}
?>