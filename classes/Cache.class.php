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

class Cache extends Baseobj
{
    public $id;
    public $session;
    public $texte;
    public $args;
    public $variables;
    public $type_boucle;
    public $res;
    public $date;

    const TABLE="cache";
    public $table=self::TABLE;

    public $bddvars=["id", "session", "texte", "args", "variables", "type_boucle", "res", "date"];

    function __construct()
    {
        parent::__construct();
    }

    function charger($texte = null, $args = null, $variables, $type_boucle)
    {
        if ($texte != null && $args != null) {
            return $this->getVars("SELECT * FROM $this->table WHERE texte=\"$texte\" AND args=\"$args\" AND variables=\"$variables\" AND type_boucle=\"$type_boucle\"");
        }
    }

    function charger_session($session, $texte, $args, $variables, $type_boucle)
    {
        return $this->getVars("SELECT * FROM $this->table WHERE session=\"$session\" AND texte=\"$texte\" AND args=\"$args\" AND variables=\"$variables\" AND type_boucle=\"$type_boucle\"");
    }

    function vider($type_boucle, $variables)
    {
        $query = "DELETE FROM $this->table WHERE type_boucle=\"$type_boucle\" AND variables LIKE \"$variables\"";
        $resul = $this->query($query);
    }

    function vider_session($session, $type_boucle, $variables)
    {
        $query = "DELETE FROM $this->table WHERE session=\"$session\" AND type_boucle=\"$type_boucle\" AND variables LIKE \"$variables\"";
        $resul = $this->query($query);
    }
}
?>
