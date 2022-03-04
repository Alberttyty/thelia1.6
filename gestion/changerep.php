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
	header("Content-type: text/html; charset=utf-8");

    require_once(__DIR__ . "/../classes/Tlog.class.php");

	require_once(__DIR__ . "/../fonctions/traduction.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>THELIA / BACK OFFICE</title>
	<link rel="SHORTCUT ICON" href="favicon.ico" />
	<link href="styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="wrapper">
<div id="subwrapper">
<div id="entete">
	<div class="logo">
		<a href="accueil.php"><img src="gfx/thelia_logo.jpg" alt="THELIA solution e-commerce" /></a>
	</div>
<div id="menuGeneral">
	&nbsp;
</div>

<div id="contenu_int">

	<div class="Bloc_news_index">
		<div class="texte">Veuillez renommer votre répertoire admin avant de vous connecter ...</div>
	</div>



</div>

    <?php require_once("pied.php");?>
</div>
</div>
</body>
</html>
