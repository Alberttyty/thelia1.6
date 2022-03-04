<?php
	//Modif par Roadster31 - singleton de connexion à la BDD


    class StaticConnection
    {
        public static $db_handle = -1;

        public static function getHandle()
        {
            if (self::$db_handle == -1)
            {
                self::$db_handle = mysql_connect(Cnx::$host, Cnx::$login_mysql, Cnx::$password_mysql) or die('Le serveur MySQL n\'est pas accessible.');
				mysql_query("SET NAMES UTF8", self::$db_handle);
				
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

        function __construct() {

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