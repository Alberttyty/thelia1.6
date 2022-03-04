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

class Autorisation_profil extends Baseobj
{
    public $id;
    public $profil;
    public $autorisation;
    public $lecture;
    public $ecriture;

    const TABLE="autorisation_profil";
    public $table=self::TABLE;

    public $bddvars = ["id", "profil", "autorisation" ,"lecture" ,"ecriture"];

    function __construct()
    {
        parent::__construct();
    }

	  function charger($autorisation = null, $profil = null)
    {
        if ($autorisation != null && $profil != null) {
            return $this->getVars("SELECT * FROM $this->table WHERE autorisation=\"$autorisation\" AND profil=\"$profil\"");
        }
    }
}
?>
