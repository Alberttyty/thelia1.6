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
require_once(__DIR__ . "/pre.php");

session_start();
header("Content-type: text/html; charset=utf-8");

if (!isset($_SESSION["util"])) $_SESSION["util"]=new Administrateur();

if (isset($_POST['identifiant']) && isset($_POST['motdepasse'])){
		$identifiant = str_replace(" ", "", $_POST['identifiant']);
		$motdepasse = str_replace(" ", "", $_POST['motdepasse']);
}

if ($_POST['action'] == "identifier") {
   	$admin = new Administrateur();

		if (! $admin->charger($identifiant, $motdepasse)) {
				redirige("index.php");
				exit();
		}
    else {
        $_SESSION["util"] = new Administrateur();
        $_SESSION["util"] = $admin;
   }
}

if ( ! isset($_SESSION["util"]->id) ) {
		redirige("index.php");
		exit();
}

require_once(__DIR__ . "/../fonctions/traduction.php");

// chargement du fichier de langue
if (! isset($_SESSION["util"]->lang) || ! $_SESSION["util"]->lang) $_SESSION["util"]->lang = 1;

require_once(__DIR__ . "/lang/" . $_SESSION["util"]->lang . ".php");

ActionsAdminModules::instance()->inclure_lang_admin($_SESSION["util"]->lang);
ActionsAdminModules::instance()->inclure_module_admin("pre");

?>
