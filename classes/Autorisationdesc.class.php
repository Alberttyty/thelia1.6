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

class Autorisationdesc extends Baseobjdesc
{
    public $id;
    public $autorisation;

    const TABLE="autorisationdesc";
    public $table=self::TABLE;

    public $bddvars = ["id", "autorisation", "titre", "chapo", "description", "postscriptum", "lang"];

    function __construct()
    {
        parent::__construct("autorisation");
    }

    public function charger($idobj = null, $lang = null)
    {
        if ($idobj != null) return parent::charger_desc($idobj, $lang);
    }
}
?>
