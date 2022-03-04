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

	if(! est_autorise("acces_contenu")) exit;

	header('Content-Type: text/html; charset=utf-8');

	list($modif, $id) = explode( "_", lireParam("id", "string"));

	if (strstr($modif, "dos") !== false) {
		$obj = new Dossier();
		$obj->charger($id);
		$objdesc = new Dossierdesc();
		$objdesc->charger($obj->id);
		$point_entree = "moddcont";
		$champ_parent = "parent";
	}
	else if (strstr($modif, "cont") !== false) {
		$obj = new Contenu();
		$obj->charger($id);
		$objdesc = new Contenudesc();
		$objdesc->charger($obj->id);
		$point_entree = "moddos";
		$champ_parent = "dossier";
	}
	else {
		exit();
	}

	switch($modif) {
		case 'titrecont' :
		case 'titredos' :
			$objdesc->titre = lireParam("value", "string");
			echo $objdesc->titre;
		break;

		case 'lignecont' :
		case 'lignedos' :
			$obj->ligne = $obj->ligne ? 0 : 1;
			break;

		case 'lignetouscont' :
		case 'lignetousdos' :

			$ligne = lireParam("ligne", "int");
			$parent = intval(lireParam("parent", "int"));

			$obj->query("update $obj->table set ligne=$ligne where $champ_parent=$parent");
		break;

		default:
			exit;
	}

	$obj->maj();
	$objdesc->maj();

	ActionsModules::instance()->appel_module($point_entree, $obj);
?>