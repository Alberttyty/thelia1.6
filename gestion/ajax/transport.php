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

    require_once("../liste/transport.php");

	header('Content-Type: text/html; charset=utf-8');

	if($_GET['action'] == "supprimer" && $_GET['zone'] != ""){
		$transzone = new Transzone();
		$transzone->charger_id($_GET['zone']);
		$transzone->delete();

	} else if($_GET['action'] == "ajouter" && $_GET['id'] != "" && $_GET['zone'] != ""){
		$transzone = new Transzone();
		$transzone->zone = $_GET['zone'];
		$transzone->transport = $_GET['id'];
		$transzone->add();

	}

	modifier_transports($_GET['id']);
?>