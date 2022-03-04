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

if(! est_autorise("acces_catalogue")) exit;

header('Content-Type: text/html; charset=utf-8');

list($modif, $id) = explode( "_", lireParam('id', 'string'));

if($modif == "titrerub"){
        $obj = new Rubrique();
        $obj->charger($id);
        $objdesc = new Rubriquedesc();
        $objdesc->charger($obj->id);
        echo $objdesc->titre;
}
else if($modif == "titreprod") {
        $obj = new Produit();
        $obj->charger_id($id);
        $objdesc = new Produitdesc();
        $objdesc->charger($obj->id);
        echo $objdesc->titre;
}
else if($modif == "stock"){
        $obj = new Produit();
        $obj->charger_id($id);
        echo $obj->stock;
}
else if($modif == "prix"){
        $obj = new Produit();
        $obj->charger_id($id);
        echo $obj->prix;
}
else if($modif == "prix2"){
        $obj = new Produit();
        $obj->charger_id($id);
        echo $obj->prix2;
}
?>