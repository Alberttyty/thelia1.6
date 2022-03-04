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
require_once __DIR__ . "/../fonctions/autoload.php";

class Stock extends Baseobj
{
    public $id;
    public $declidisp;
    public $produit;
    public $valeur;
    public $surplus;

    const TABLE="stock";
    public $table=self::TABLE;

    public $bddvars=["id", "declidisp", "produit", "valeur", "surplus"];

    function __construct($declidisp = 0, $produit = 0)
    {
        parent::__construct();
        if ($declidisp > 0 && $produit > 0) $this->charger($declidisp, $produit);
    }

    function charger($declidisp = null, $produit = null)
    {
        if ($declidisp != null && $produit != null) {
            return $this->getVars("SELECT * FROM $this->table WHERE declidisp=\"$declidisp\" AND produit=\"$produit\"");
        }
    }
}
?>
