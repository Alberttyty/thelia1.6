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
require_once("pre.php");
require_once("auth.php");

if(!isset($action)) $action="";

if(! est_autorise("acces_configuration")) exit;

if ($action == "modifier") {

	if (is_array($_REQUEST['valeur'])) {
		foreach($_REQUEST['valeur'] as $id => $valeur) {

	            $variable = new Variable();

	            if ($variable->charger_id($id)) {

	                    if ($valeur != $variable->valeur) {
	                            $variable->valeur = $valeur;

	                            $variable->maj();

	                            ActionsModules::instance()->appel_module("modvariable", $variable);
	                    }
	            }
		}
	}

	// Ajouter ?
	$nom = lireParam('ajout_nom', 'string');

	if ($nom != '') {
            $variable = new Variable();
            $variable->nom = $nom;
            $variable->valeur = lireParam('ajout_valeur', 'string');
            $variable->protege = 0;
            $variable->cache = 0;

            $variable->add();

            ActionsModules::instance()->appel_module("addvariable", $variable);
	}
}
else if ($action == "supprimer") {

	$variable = new Variable();

	if ($variable->charger_id(intval(lireParam('id', 'int')))) {
		$variable->delete();
	}

	ActionsModules::instance()->appel_module("delvariable", $variable);
}

redirige("variable.php");

?>