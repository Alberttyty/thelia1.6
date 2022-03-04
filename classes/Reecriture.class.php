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

class Reecriture extends Baseobj
{
    public $id;
    public $fond;
    public $url;
    public $param;
    public $lang;
    public $actif;

    const TABLE="reecriture";
    public $table=self::TABLE;

    public $bddvars = ["id", "fond", "url", "param", "lang", "actif"];

    function __construct($url="")
    {
        parent::__construct();
        if ($url != "") $this->charger($url);
    }

    function charger($url = null, $var2 = null)
    {
        if ($url != null) return $this->getVars("SELECT * FROM $this->table WHERE url=\"$url\" ORDER BY actif DESC");
    }

    function charger_param($fond, $param, $lang=1, $actif=1)
    {
       return $this->getVars("SELECT * FROM $this->table WHERE fond=\"$fond\" AND param=\"$param\" AND lang=\"$lang\" AND actif=\"$actif\"");
    }

    function charger_url_classique($param, $lang, $actif)
    {
       preg_match('/fond=([^&]*)(.*)$/', $param, $rec);
       return $this->getVars("SELECT * FROM $this->table WHERE fond=\"" . $rec[1] . "\" AND param=\"" . $rec[2] ."\" AND lang=\"$lang\" AND actif=\"$actif\"");
    }
}
?>
