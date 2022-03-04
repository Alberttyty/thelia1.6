<?php
	//Modif par Roadster31 - singleton de connexion à la BDD


    class StaticConnection
    {
        public static $db_handle = -1;

        public static function getHandle()
        {
            if (self::$db_handle == -1)
            {
                self::$db_handle = @mysql_connect(Cnx::$host, Cnx::$login_mysql, Cnx::$password_mysql) or die('Le serveur MySQL n\'est pas accessible.');
				mysql_query("SET NAMES UTF8", self::$db_handle);

                if(! self::$db_handle && $_REQUEST['erreur'] != 1)
                {
					header('HTTP/1.1 503 Service Temporarily Unavailable');
					header('Status: 503 Service Temporarily Unavailable');
					echo "Connexion à la base de données impossible"; exit;
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

		public function fetch_object($dbhandle, $classname = false) {
			if ($classname !== false)
				return mysql_fetch_object($dbhandle, $classname);
			else
				return mysql_fetch_object($dbhandle);
		}

		public function num_rows($dbhandle) {
			return mysql_num_rows($dbhandle);
		}

		public function get_result($dbhandle, $row = 0, $field = 0) {
			return mysql_result($dbhandle, $row, $field);
		}

		public function escape_string($value) {
	        if(get_magic_quotes_gpc()) $value = stripslashes($value);

	        return mysql_real_escape_string($value);
		}
    }
?>