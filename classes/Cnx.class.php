<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) OpenStudio		                                             */
/*		email : thelia@openstudio.fr		        	                          	 */
/*      web : http://www.openstudio.fr						   						 */
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
/*	    along with this program.  If not, see <http://www.gnu.org/licenses/>.		 */
/*                                                                                   */
/*************************************************************************************/
require_once(SITE_DIR."/client/config_thelia.php");
require_once(__DIR__ . "/../fonctions/mysql.php");

class StaticConnection
{
    public static $db_handle = null;

    public static function getHandle()
    {
        if (self::$db_handle === null) {
            self::$db_handle = @mysql_connect(
                            THELIA_BD_HOST,
                            THELIA_BD_LOGIN,
                            THELIA_BD_PASSWORD) or die('Le serveur de base de données n\'est pas accessible.');

            mysql_query("SET NAMES UTF8", self::$db_handle);

            if (! self::$db_handle && $_REQUEST['erreur'] != 1) {
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                header('Status: 503 Service Temporarily Unavailable');

                die("Connexion à la base de données impossible");
            }

            mysql_select_db(THELIA_BD_NOM, self::$db_handle) or die('Echec de selection de la base de données.');
            mysql_query("SET SESSION sql_mode = ''", self::$db_handle); // On désactive le mode strict pour cette session PHP (compatibilité MySQL 5.7)
        }

        return self::$db_handle;
    }
}

class Cnx
{
    public $table = "";
    public $link;

    function __construct()
    {
        $this->link = StaticConnection::getHandle();
    }

    public function query($query, $exception = false)
    {
        $resul = mysql_query($query, $this->link);

        if ($resul === false) {
            // Tlog::error("Erreur SQL: ", $this->get_error()," - Requête: ", $query);
            if ($exception === true) throw new Exception(mysql_error($this->link));
        }

        return $resul;
    }

    public function query_liste($query, $classname = false)
    {
        $liste = [];
        $resul = $this->query($query);

        while ($resul && $row = $this->fetch_object($resul, $classname)) {
            $liste[] = $row;
        }

        return $liste;
    }

    public function fetch_object($dbhandle, $classname = false)
    {
        if ($classname !== false) return mysql_fetch_object($dbhandle, $classname);
        else return mysql_fetch_object($dbhandle);
    }

    public function num_rows($dbhandle)
    {
        return mysql_num_rows($dbhandle);
    }

    public function get_result($dbhandle, $row = 0, $field = 0)
    {
        return mysql_result($dbhandle, $row, $field);
    }

    public function escape_string($value)
    {
        //if (get_magic_quotes_gpc()) $value = stripslashes($value);
        return mysql_real_escape_string($value, $this->link);
    }

    public function insert_id()
    {
        return mysql_insert_id($this->link);
    }

    public function get_error()
    {
        return mysql_error($this->link);
    }
}
?>
