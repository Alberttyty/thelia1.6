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
// Classe Baseobj
require_once __DIR__ . "/../fonctions/autoload.php";

/**
 * @method static mixed exist_*(mixed $value) - @see __callStatic, retourne le nombre d'enregistrement trouvé ou bien false si un problème survient (bien vérifier donc que @return !== false et non pas simplement !@return
 */
class Baseobj extends Requete
{
		public $bddvars = [];

		function __construct()
    {
			   parent::__construct();
		}

		/**
     * Cette méthode permet d'appeler des méthodes static non définies dans le code de type exist_email
     *
     * @static
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        $method = strtolower($method);
        $calee = next(debug_backtrace());

        if (substr($method, 0, 6) == 'exist_') {
            $key = substr($method, 6, strlen($method));
            $method = 'exist_key';
        }

        if (isset($key)) {
            $tempInstance = new $calee['class'];

            if (array_search($key, $tempInstance->bddvars) === false) return false;
            if (! count($arguments)) return false;

            $value = $arguments[0];

            return self::$method($key,$value,$calee['class']::TABLE);
        }

        trigger_error(sprintf("Call to undefined method %s::%s in %s on line %s",  $calee['class'],$method,$calee['file'],$calee['line']), E_USER_ERROR);
    }

		/* Compatibilité 1.4.x */
		function Baseobj()
    {
			   self::__construct();
		}


		/**
      * retourne le nombre d'occurence trouvé pour un couple colonne/valeur pour une table donnée.
      * ex : exist_key('email','foo@bar.com','client')
      *
      * @param string $key colonne sur laquelle se porte la recherche
      * @param string $value valeur recherché
      * @param string $table nom de la table concerné
      * @param int $lang id de la langue. N'est pas utile ici.
      * @return boolean/int retourne false si un problème survient. Le nombre de résultat sinon.
      * (attention, Cette fonction peut retourner FALSE, mais elle peut aussi retourner une valeur équivalent à FALSE. Veuillez lire la section sur les booléens (http://fr2.php.net/manual/fr/language.types.boolean.php) pour plus d'informations. Utilisez l'opérateur === (http://fr2.php.net/manual/fr/language.operators.comparison.php) pour tester la valeur de retour exacte de cette fonction.)
      */
		public static function exist_key($key, $value, $table, $lang = null)
    {
        $request = new Requete();
        $sql = 'SELECT COUNT(id) FROM '.$table.' WHERE '.$key.' ="'.$value.'"';

        if ($lang !== null) $sql .= ' AND lang='.intval($lang);

        $query = $request->query($sql);

        return $request->get_result($query,0,0);
    }

		function getListVarsSql()
    {
  			$listvars="";
  			foreach ($this->bddvars as $var) {
            $listvars .= '`'.$var. "`,";
  			}

  			return rtrim($listvars, ',');
		}


		function getListValsSql()
    {
  			$listvals="";

  			foreach($this->bddvars as $var) {
            $tempvar = $this->$var;

  				  //if (get_magic_quotes_gpc()) $tempvar = stripslashes($tempvar);

    				$tempvar = $this->escape_string($tempvar);
    				$listvals .= "\"" . $tempvar . "\",";
  			}

  			return rtrim($listvals, ',');
		}

    function getVars($query)
    {
        $row = CacheBase::getCache()->get($query);

        if ($row == FALSE) {
          	if (! $resul = $this->query($query)) {
                CacheBase::getCache()->set($query,"-");
                return 0;
            }

            // Nécessaire pour une mise à jour pour les versions <= 1.5.0
            // Cnx::fetch_object() n'existe pas encore dans Cnx.class.php.
            // Le fichier install/maj.php definit la constante IN_UPDATE_THELIA.
            if (defined('IN_UPDATE_THELIA')) $row = mysql_fetch_object($resul/*, get_class($this)*/);
            else $row = $this->fetch_object($resul/*, get_class($this)*/);

            if ($row === false) $row = "-";

            CacheBase::getCache()->set($query, $row);
        }

        if ($row && $row != "-") {
            foreach($this->bddvars as $var) {
                $this->$var = $row->$var;
            }

            return 1;
        }
        else return 0;

        // return $this->num_rows($resul);
    }

		function serialise_js()
    {
			$this->link="";
 			$json = new Services_JSON();

			return $json->encode($this);
		}
}
?>
