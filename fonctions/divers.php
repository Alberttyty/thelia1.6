<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
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
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/
require_once(__DIR__ . "/hierarchie.php");
require_once(__DIR__ . "/nettoyage.php");
require_once(__DIR__ . "/lire.php");
require_once(__DIR__ . "/rewrite.php");
require_once(__DIR__ . "/image.php");
require_once(__DIR__ . "/modules.php");
require_once(__DIR__ . "/url.php");
require_once(__DIR__ . "/port.php");

function randgen($letter, $size) {
	return substr(str_shuffle($letter), 0, $size);
 }

// génération mot de passe
function genpass($size){

	 $letter = "abcdefghijklmnopqrstuvwxyz";
	 $letter .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	 $letter .= "0123456789";

	 return randgen($letter, $size);
}

// génération de code
function gencode($size){

	 $letter = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	 $letter .= "0123456789";

	 return randgen($letter, $size);
}

function genid($id, $taille) {
	return str_pad($id, $taille, '0', STR_PAD_LEFT);
}

// Formatter un nombre réel pour affichage en front office
// Le retour de cette fonction ne peut être utilisé pour des calculs ultérieurs.
function formatter_somme($nombre) {
	return number_format($nombre, 2, ".", "");
}
?>
