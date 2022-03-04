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

function moddecli($produit, $declidisp, $type){

        $exdecprod = new Exdecprod();

        $exdecprod->query("delete from $exdecprod->table where produit=$produit and declidisp=$declidisp");

        if ($type != 0)
        {
                $exdecprod->produit = $produit;
                $exdecprod->declidisp = $declidisp;
                $exdecprod->add();
        }
}

moddecli(intval($_POST['produit']), intval($_POST['declidisp']), intval($_POST['type']));
?>