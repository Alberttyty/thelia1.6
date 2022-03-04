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

/**
 * @method static mixed exist_*(mixed $value, int $lang) - @see __callStatic, retourne le nombre d'enregistrement trouvé ou bien false si un problème survient (bien vérifier donc que @return !== false et non pas simplement !@return
 */
class Baseobjdesc extends Baseobj
{
		const TRAITER_TRAD_VIDE = 1;
		const NE_PAS_TRAITER_TRAD_VIDE = 2;

		protected $colonne;

		// Les colonnes 'standard'
		public $titre;
		public $chapo;
		public $description;
		public $postscriptum;
		public $lang;

		public function __construct($colonne, $fkey = "", $lang = false)
		{
				parent::__construct();
				// Identifier la colonne qui contient la foreign key
				$this->colonne = $colonne;
				// Charger la description si nécessaire.
				if (! empty($fkey)) $this->charger_desc($fkey, $lang);
		}

		/**
		* Cette méthode permet d'appeler des méthodes static non définies dans le code de type exist_chapo
		*
		* @static
		*
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
		        if (!count($arguments)) return false;

		        $value = $arguments[0];

		        if (isset($arguments[1])) $lang = $arguments[1];
		        else $lang = ActionsLang::instance()->get_id_langue_courante();

						return self::$method($key,$value,$calee['class']::TABLE,$lang);
		    }

		    trigger_error(sprintf("Call to undefined method %s::%s in %s on line %s",  $calee['class'],$method,$calee['file'],$calee['line']), E_USER_ERROR);
		}

		/**
	    * retourne le nombre d'occurence trouvé pour un couple colonne/valeur pour une table donnée. Il est obligatoire de spécifier la langue
			* ex : exist_key('chapo','foo','produitdesc',1)
	    *
	    * @param string $key colonne sur laquelle se porte la recherche
	    * @param string $value valeur recherché
	    * @param string $table nom de la table concerné
	    * @param int $lang id de la langue
	    * @return boolean/int retourne false si un problème survient. Le nombre de résultat sinon.
	    * (attention, Cette fonction peut retourner FALSE, mais elle peut aussi retourner une valeur équivalent à FALSE. Veuillez lire la section sur les booléens (http://fr2.php.net/manual/fr/language.types.boolean.php) pour plus d'informations. Utilisez l'opérateur === (http://fr2.php.net/manual/fr/language.operators.comparison.php) pour tester la valeur de retour exacte de cette fonction.)
	    */
		public static function exist_key($key, $value, $table, $lang = null)
	  {
	      return parent::exist_key($key, $value, $table, $lang);
	  }

		/**
		 * Permet de déterminer si une description est affichage dans le back office.
		 */
		public function affichage_back_office_permis()
		{
				return $this->est_langue_courante()	|| ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_PAR_DEFAUT;
		}

		/**
		 * Permet de déterminer si une description est dans la langue courante
		 */
		public function est_langue_courante()
		{
				return $this->lang == ActionsLang::instance()->get_id_langue_courante();
		}

		/**
		 * Charge une description, dans la langue demandée, dans la langue courante, ou dans la langue par défaut.
		 * Dans le cobntexte du back office, on charge toujours la langue demandé, même si celle-ci n'existe pas.
		 *
		 * @param int $fkey l'identifiant de l'objet associé (foreign key)
		 * @param int $lang l'ID de la languie demandée. Si false, la langue courante sera utilisée
		 * @param string $colonne la colonne de la table 'desc' à utiliser pour la recherche. Si false, c'est celle passée au constructeur qui est utilisée.
		 * @param int $traiter_trad_vide indique comment traiter le cas d'une traduction absente, cf les constantes définies
		 * @return 0 si la trdunction n' pas pu être chargée, 1 sinon.
		 */
		public function charger_desc($fkey, $lang = false, $colonne = false, $traiter_trad_vide = self::TRAITER_TRAD_VIDE)
		{
				// Aucune colonne indiquée ? utiliser celle de base
				if ($colonne === false) $colonne = $this->colonne;

				// Dans le back office, on force le chargement de la langue demandée.
				if (ActionsLang::instance()->est_mode_backoffice() && $lang !== false) $traiter_trad_vide = self::NE_PAS_TRAITER_TRAD_VIDE;

				// La langue n'est pas définie ?
				if ($lang === false || $lang === null) {
						// Utiliser la langue courante
						$lang = ActionsLang::instance()->get_id_langue_courante();
				}

				$res = $this->getVars($this->get_charger_sql($fkey, $lang, $colonne));

				// La traduction n'est pas dispo ?
				if ($traiter_trad_vide == self::TRAITER_TRAD_VIDE && $res == 0) {
						switch (ActionsLang::instance()->get_action_si_trad_absente()) {
								case ActionsLang::UTILISER_LANGUE_PAR_DEFAUT :
										// Charger la description dans la langue par defaut
										$res = $this->getVars($this->get_charger_sql($fkey, ActionsLang::instance()->get_id_langue_defaut(), $colonne));
										break;

								case ActionsLang::UTILISER_LANGUE_INDIQUEE :
										// ne rien à faire
										break;
						}
				}

				return $res;
		}

		/**
		 * Définit la requête a utiliser pour charger la description. Les classes filles
		 * peuvent surcharger cette méthode pour construire des requêtes non standard.
		 *
		 * @param int $fkey valeur de la foreign key
		 * @param int $lang langue désirée
		 * @param string $colonne nom de la colonne de la foreign key
		 */
		protected function get_charger_sql($fkey, $lang, $colonne)
		{
				return "SELECT * FROM $this->table WHERE $colonne=".intval($fkey)." AND lang=".intval($lang);
		}
}
?>
