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

require_once(__DIR__ . "/nettoyage.php");
require_once(__DIR__ . "/hierarchie.php");
// rewriting produit
function rewrite_prod($ref, $lang = false){

	$prod = new Produit($ref, $lang);
	$desc = new Produitdesc($prod->id, $lang);

	return $desc->charger_reecriture()->url;
}

// rewriting rubrique
function rewrite_rub($id, $lang = false){

	$desc = new Rubriquedesc($id, $lang);
	return $desc->charger_reecriture()->url;
}

// rewriting contenu
function rewrite_cont($id, $lang = false){

	$desc = new Contenudesc($id, $lang);
	return $desc->charger_reecriture()->url;
}

// rewriting dossier
function rewrite_dos($id, $lang = false){

	$desc = new Dossierdesc($id, $lang);
	return $desc->charger_reecriture()->url;
}

?>