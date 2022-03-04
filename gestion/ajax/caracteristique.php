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
?>
<?php if(! est_autorise("acces_configuration")) exit; ?>
<?php

require_once(__DIR__ . "/../liste/caracteristique.php");

header('Content-Type: text/html; charset=utf-8');

if ($_REQUEST['action'] == "" && $_REQUEST['id'] != ""){
	$caracteristiquedesc = new Caracteristiquedesc($_REQUEST['id']);
	$caracteristiquedesc->titre = $_REQUEST['value'];
	$caracteristiquedesc->maj();

	echo $caracteristiquedesc->titre;
	exit();
}

switch($_REQUEST["action"]){

	case "ajouter" :
		caracteristique_ajouter($_REQUEST["caracteristique"],$_REQUEST["rubrique"]);
		break;
	case "liste" :
		caracteristique_liste_select($_REQUEST["id"]);
		break;
	case "supprimer" :
		caracteristique_supprimer($_REQUEST["caracteristique"],$_REQUEST["rubrique"]);
		break;

}

function caracteristique_ajouter($caracteristique,$rubrique){

	if (! empty($caracteristique)) {

		$rubcaracteristique = new Rubcaracteristique();
		$rubcaracteristique->rubrique = $rubrique;
		$rubcaracteristique->caracteristique = $caracteristique;
		$rubcaracteristique->add();
	}
	lister_caracteristiques_rubrique($rubrique);
}

function caracteristique_supprimer($caracteristique,$rubrique){
	$rubcaracteristique = new Rubcaracteristique($rubrique, $caracteristique);
	$rubcaracteristique->delete();

	lister_caracteristiques_rubrique($rubrique);
}
?>