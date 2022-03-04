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

class Port extends Baseobj
{
    public $id;
    public $zone;
    public $poids;
    public $port;

    const TABLE="port";
    public $table=self::TABLE;

    public $bddvars=["id", "zone", "poids", "port"];

    function __construct(){
        parent::__construct();
    }

    function charger($id = null, $var2 = null)
    {
        if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
    }

    function calcule($zone, $poids)
    {
        $this->getVars("SELECT * FROM $this->table WHERE zone='" . $zone . "' AND poids>='" . $poids . "' ORDER BY port LIMIT 0,1");
        return $this->port;
    }
}
?>
