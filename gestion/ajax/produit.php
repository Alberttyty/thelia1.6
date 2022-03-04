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

	if(! est_autorise("acces_catalogue")) exit;

	header('Content-Type: text/html; charset=utf-8');

	list($modif, $id) = explode("_", lireParam("id", "string"));

	if (strstr($modif, "rub") !== false) {
		$obj = new Rubrique();
		$obj->charger($id);
		$objdesc = new Rubriquedesc();
		$objdesc->charger($obj->id);
		$point_entree = "modrub";
		$champ_parent = "parent";
	}
	else {
		$obj = new Produit();
		$obj->charger_id($id);
		$obj->datemodif = date('Y-m-d H:i:s');
		$objdesc = new Produitdesc();
		$objdesc->charger($obj->id);
		$point_entree = "modprod";
		$champ_parent = "rubrique";
	}

	switch($modif) {
		case 'prix' :
			$obj->prix = lireParam('value', 'string');
			echo $obj->prix;
		break;

		case 'prix2' :
			$obj->prix2 = lireParam('value', 'string');
			echo $obj->prix2;
		break;

		case 'stock' :
			$obj->stock = lireParam('value', 'string');
			echo $obj->stock;
		break;

		case 'titreprod' :
		case 'titrerub' :
			$objdesc->titre = stripslashes(lireParam('value', 'string'));
			echo $objdesc->titre;
		break;

		case 'promo' :
			$obj->promo = $obj->promo ? 0 : 1;
		break;

		case 'nouveaute' :
			$obj->nouveaute = $obj->nouveaute ? 0 : 1;
			break;

		case 'ligneprod' :
		case 'lignerub' :
			$obj->ligne = $obj->ligne ? 0 : 1;
		break;

		case 'lignetousrub' :
		case 'lignetousprod' :
			$modif = lireParam("modif", "int");
			$parent = intval(lireParam("parent", "int"));

			$obj->query("update $obj->table set ligne=$modif where $champ_parent=$parent");
		break;

		case 'nouveautetous' :
			$modif = lireParam("modif", "int");
			$parent = intval(lireParam("parent", "int"));

			$obj->query("update $obj->table set nouveaute=$modif where $champ_parent=$parent");
		break;

		case 'promotous' :
			$modif = lireParam("modif", "int");
			$parent = intval(lireParam("parent", "int"));

			$obj->query("update $obj->table set promo=$modif where $champ_parent=$parent");
		break;

		default:
			exit;
	}
	
	$obj->maj();
	$objdesc->maj();

	ActionsModules::instance()->appel_module($point_entree, $obj);
?>