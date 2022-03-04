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

class Caracval extends Baseobj
{
    public $id;
    public $produit;
    public $caracteristique;
    public $caracdisp;
    public $valeur;

    const TABLE="caracval";
    public $table=self::TABLE;

    public $bddvars = ["id", "produit", "caracteristique", "caracdisp", "valeur"];

    function __construct($produit = 0, $caracteristique = 0)
    {
        parent::__construct();
        if ($produit > 0 && $caracteristique > 0) $this->charger($produit, $caracteristique);
    }

    function charger($produit = null, $caracteristique = null)
    {
        if ($produit != null && $caracteristique != null) {
            return $this->getVars("SELECT * FROM $this->table WHERE produit=\"$produit\" AND caracteristique=\"$caracteristique\"");
        }
    }

    function charger_caracdisp($produit, $caracteristique, $caracdisp)
    {
        return $this->getVars("SELECT * FROM $this->table WHERE produit=\"$produit\" AND caracteristique=\"$caracteristique\" AND caracdisp=\"$caracdisp\"");
    }

    function charger_valeur($valeur)
    {
        return $this->getVars("SELECT * FROM $this->table WHERE valeur=\"$valeur\"");
    }
}
?>
