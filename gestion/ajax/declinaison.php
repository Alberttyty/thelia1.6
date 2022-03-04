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

if(! est_autorise("acces_configuration")) exit;

require_once(__DIR__ . "/../liste/declinaison.php");

header('Content-Type: text/html; charset=utf-8');


switch($_GET["action"]){

	case "ajouter" :
		declinaison_ajouter($_GET["declinaison"],$_GET["rubrique"]);
		break;
	case "liste" :
		declinaison_liste_select($_GET["id"]);
		break;
	case "supprimer" :
		declinaison_supprimer($_GET["declinaison"],$_GET["rubrique"]);
		break;

}

function declinaison_ajouter($declinaison,$rubrique){

	$rubdeclinaison = new Rubdeclinaison();
	$rubdeclinaison->rubrique = $rubrique;
	$rubdeclinaison->declinaison = $declinaison;
	$rubdeclinaison->add();

	lister_declinaisons_rubrique($rubrique);
}

function declinaison_supprimer($declinaison,$rubrique){
	$rubdeclinaison = new Rubdeclinaison($rubrique,$declinaison);
	$rubdeclinaison->delete();

	lister_declinaisons_rubrique($rubrique);
}
?>