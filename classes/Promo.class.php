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

class Promo extends Baseobj
{
    public $id;
    public $code;
    public $type;
    public $valeur;
    public $mini;
    public $utilise;
    public $limite;
    public $datefin;
    public $actif;

    const TYPE_SOMME = 1;
    const TYPE_POURCENTAGE = 2;

    const TABLE = "promo";
    public $table = self::TABLE;

    public $bddvars = array("id", "code", "type", "valeur", "mini", "utilise", "limite", "datefin", "actif");

    function __construct($code = false)
    {
        parent::__construct();
        if ($code !== false) $this->charger($code);
    }

    function charger($code = null, $var2 = null)
    {
        if ($code != null) return $this->getVars("SELECT * FROM $this->table WHERE code=\"$code\" AND (datefin>=CURDATE() OR datefin='0000-00-00') AND actif='1' AND (limite=0 OR limite>utilise)");
    }
}
?>
