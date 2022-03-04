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

class Modules extends Baseobjclassable
{
    // Les types de plugins
    const PAIEMENT = 1;
    const TRANSPORT = 2;
    const CLASSIQUE = 3;
    const FILTRE = 4;

    public $id;
    public $nom;
    public $type;
    public $actif;
    public $classement;
    public $xml;

    public $activable;

    const TABLE = "modules";
    public $table=self::TABLE;

    public $bddvars = ["id", "nom", "type", "actif", "classement"];

    public function __construct($id = 0)
    {
        parent::__construct("type");
        if ($id > 0) $this->charger_id($id);
    }

    public function charger($nom = null, $var2 = null)
    {
        return $this->getVars("SELECT * FROM $this->table WHERE nom=\"$nom\"");
    }

    public function est_autorise()
    {
        if ($_SESSION['util']->profil == "1") return 1;

        $verif = new Autorisation_modules();
        if ($verif->charger($this->id, $_SESSION['util']->id) && $verif->autorise) return 1;

        return 0;
    }
}
?>
