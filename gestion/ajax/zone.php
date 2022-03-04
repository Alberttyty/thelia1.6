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

require_once("../liste/zone.php");

        header('Content-Type: text/html; charset=utf-8');

if($_GET['action'] == "forfait" && $_GET['valeur'] != ""){
        $zone = new Zone();
        $zone->charger($_GET['id']);
        $zone->unite = $_GET['valeur'];
        $zone->maj();
}

else if($_GET['action'] == "ajouter" && $_GET['pays'] != ""){
        $pays = new Pays();
        $query = "update $pays->table set zone='" . $_GET['id'] . "' where id=\"" . $_GET['pays'] . "\"";
        $resul = $pays->query($query);
}

else if($_GET['action'] == "supprimer" && $_GET['pays'] != ""){
        $pays = new Pays();
        $query = "update $pays->table set zone='-1' where id=\"" . $_GET['pays'] . "\"";
        $resul = $pays->query($query);
}

modifier_pays_zone($_GET['id']);
?>