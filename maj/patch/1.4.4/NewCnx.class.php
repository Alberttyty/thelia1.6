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
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/
?>
<?php
	//Modif par Roadster31 - singleton de connexion à la BDD


    class StaticConnection
    {
        public static $db_handle = -1;

        public static function getHandle()
        {
            if (self::$db_handle == -1)
            {
                self::$db_handle = mysql_connect(Cnx::$host, Cnx::$login_mysql, Cnx::$password_mysql);

                if(! self::$db_handle && $_REQUEST['erreur'] != 1)
                {
                    header("Location: maintenance.php?erreur=1");
                }

                mysql_select_db(Cnx::$db, self::$db_handle);
            }

            return self::$db_handle;
        }
    }

    // Classe Cnx
	// host --> votre serveur mysql
    // login_mysql --> login de connexion
    // password_mysql --> mot de passe de connexion
    // db --> nom de la base de donnée
    class Cnx{

		public static $host= "votre_serveur";
        public static $login_mysql= "votre_login_mysql";
        public static $password_mysql= "votre_motdepasse_mysql";
        public static $db = "bdd_sql";

        var $table = "";
        var $link="";

        function Cnx() {

            $this->link = StaticConnection::getHandle();

			self::$host = '';
			self::$login_mysql = '';
			self::$password_mysql = '';
			self::$db = '';
        }

		public function query($query) {
			$resul = mysql_query($query, $this->link);

			// A décommenter pour debug
			/*
			if ($resul === false) {
				die("Erreur: ".mysql_error().": requête: $query");
			}
			*/

			return $resul;
		}
	}
?>