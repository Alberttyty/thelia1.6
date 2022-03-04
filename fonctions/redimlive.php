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
require_once(__DIR__ . "/error_reporting.php");

// redimensionnement des images + effets
require_once(__DIR__ . "/mutualisation.php");
require_once(__DIR__ . "/divers.php");

// Déclaration des variables
$vars = array('type', 'nomorig', 'height', 'width', 'opacite', 'nb', 'miroir', 'exact', 'couleurfond');

foreach($vars as $var) {
		if (isset($_REQUEST[$var])) $$var = $_REQUEST[$var];
		else $$var = '';
}

$nomcache = redim($type, $nomorig, $width, $height, $opacite, $nb, $miroir, 1, $exact, $couleurfond);

if($nomcache != '' && preg_match("/([^\/]*).((jpg|gif|png|jpeg))/i", $nomorig, $nsimple)) {
	switch(strtolower($nsimple[2])) {
		case "gif" :
	        header("Content-type: image/gif");
	        break;

	    case "jpg":
	    case "jpeg":
	        header("Content-type: image/jpeg");
	        break;

	    case "png":
	        header("Content-type: image/png");
	        break;

	    default:
	    	exit();
	}
	if ($stat = stat('..'.$nomcache)) {
			header('Last-Modified: '.date('r', $stat['mtime']));
      header('Content-Length: '.$stat['size']);
	}
  readfile('..'.$nomcache);
}
?>
