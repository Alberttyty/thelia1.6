<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
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
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/
require_once __DIR__ . "/../fonctions/autoload.php";
// Classe Request

// table --> table à requêter
class Requete extends Cnx
{
    public $table = "";

    function __construct()
    {
        parent::__construct();
    }

    function charger($var1 = null, $var2 = null)
    {
        $varid = $this->bddvars[0];
        return $this->getVars("SELECT * FROM `$this->table` WHERE `$varid`=\"" . $this->$varid . "\"");
    }

    function charger_id($id)
    {
        $varid = $this->bddvars[0];
        return $this->getVars("SELECT * FROM `$this->table` WHERE `$varid`=".intval($id));
    }

    function add()
    {
        $query = "INSERT INTO `$this->table` (" . $this->getListVarsSql() . ") VALUES(" . $this->getListValsSql() . ")";
        $resul = $this->query($query);
        CacheBase::getCache()->reset_cache();
        return $this->insert_id();
    }

    function delete()
    {
        $varid = $this->bddvars[0];
        $query = "DELETE FROM `$this->table` WHERE `$varid`=\"" . $this->$varid . "\"";
        if ($this->$varid != "") {
            $resul = $this->query($query);
            CacheBase::getCache()->reset_cache();
        }
    }

    /**
     * @see delete()
     * @deprecated since version 1.5.3
     *
     */
    public function supprimer()
    {
        if (true === method_exists($this, "delete")) $this->delete();
    }

    /**
     * Suppression en cascade d'elements en relation avec l'element courant.
     * La methode delete de chacun des elements est appellée.
     *
     * @param string $classname non de la classe concernée
     * @param string $colonne nom de la colonne dans la table
     * @param mixed $idrelation identifiant à rechercher
     */
    function delete_cascade($classname, $colonne, $idrelation)
    {
        $liste = $this->query_liste("SELECT * FROM ".$classname::TABLE." WHERE $colonne=$idrelation", $classname);
        foreach($liste as $item) $item->delete();
    }

    function maj()
    {
        $listv = "";
        $varid = $this->bddvars[0];

        foreach($this->bddvars as $var) {
            $val = $this->$var;
            //if (get_magic_quotes_gpc()) $val = stripslashes($val);
            $val = $this->escape_string($val, $this->link);
            $listv.= '`'.$var . "`=\"" . $val . "\",";
        }

        $query = "UPDATE `$this->table` SET " . rtrim($listv, ',') . " WHERE `$varid`=\"" . $this->$varid . "\"";

        if ($this->$varid != "") {
            $resul = $this->query($query);
            CacheBase::getCache()->reset_cache();
        }

        return $query;
    }

    function purge()
    {
        $query = "TRUNCATE TABLE `$this->table` ";
        $resul = $this->query($query);
    }
}
?>
