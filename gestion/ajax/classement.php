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

require_once (__DIR__ . "/../pre.php");
require_once (__DIR__ . "/../auth.php");

if (!est_autorise("acces_configuration"))
	exit;

require_once ("../../fonctions/modules.php");

header('Content-Type: text/html; charset=utf-8');

list($modif, $id) = explode("_", $_POST['id']);

$classement = $_POST["value"];

if ($modif == "classementrub" && est_autorise("acces_catalogue")) {

	$obj = new Rubrique();
	$obj->modifier_classement($id, $classement);

	echo "rubrique|";
}
else if ($modif == "classementprod" && est_autorise("acces_catalogue")) {

	$obj = new Produit();
	$obj->modifier_classement($id, $classement);

	echo "produit|";

} else	if ($modif == "classementdossier" && est_autorise("acces_catalogue")) {

	$obj = new Dossier();
	$obj->modifier_classement($id, $classement);

	echo "...";

} else if ($modif == "classementcontenu" && est_autorise("acces_catalogue")) {

	$obj = new Contenu();
	$obj->modifier_classement($id, $classement);

	echo "...";

} else if ($modif == "classementcarac" && est_autorise("acces_configuration")) {

	$obj = new Caracteristique();
	$obj->modifier_classement($id, $classement);

	echo "...";
}
else if ($modif == "classementdecli" && est_autorise("acces_configuration")) {

	$obj = new Declinaison();
	$obj->modifier_classement($id, $classement);

	echo "...";
}
else if ($modif == "classementplugin" && est_autorise("acces_configuration")) {

	$obj = new Modules();
	$obj->modifier_classement($id, $classement);

	echo "...";
}
?>