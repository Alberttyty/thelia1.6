<?php
/**
 * Ce singleton permet de gérer la manipulation des langues dans le front-office.
 *
 * @author Franck Allimant <franck@cqfdev.fr>
 * @version $Id$
 */

require_once __DIR__ . "/../../fonctions/autoload.php";

class ActionsLang extends ActionsBase {

	// Les constantes définissant l'action a effectuer si une traduction demandée est vide ou absente
	const UTILISER_LANGUE_PAR_DEFAUT = 1;
	const UTILISER_LANGUE_INDIQUEE = 2;

	const VAR_UN_DOMAINE_PAR_LANGUE  = 'un_domaine_par_langue';
	const VAR_ACTION_SI_TRAD_ABSENTE = 'action_si_trad_absente';

	private static $instance = false;

	private $id_langue_defaut = false;
	private $action_si_trad_absente = false;
	private $un_domaine_par_langue = false;
	private $mode_backoffice = false;

	protected function __construct() {
	}

	/**
	 * Cette classe est un singleton
	 * @return ActionsLang une instance de ActionsLang
	 */
	public static function instance() {
		if (self::$instance === false) self::$instance = new ActionsLang();

		return self::$instance;
	}

	/**
	 * Méthode substitutions
	 */
	public function substitutions($lang, $texte) {

		if (strstr($texte, "LANG")) {
			$texte = str_replace("#LANG", $lang->id, $texte);
			$texte = str_replace("#CODELANG", $lang->code, $texte);
		}

		return $texte;
	}

	public function est_mode_backoffice() {
		return $this->mode_backoffice;
	}

	public function set_mode_backoffice($bool) {
		$this->mode_backoffice = $bool;
	}

	/**
	 * Détermine si on fonctionne avec un domaine par langue (1), ou un domaine pour toutes les langues (0)
	 */
	public function get_un_domaine_par_langue() {

		// Intialiser l'action à affectuer si la traduction n'est pas definie
		if ($this->un_domaine_par_langue === false) {
			$this->un_domaine_par_langue = Variable::lire(self::VAR_UN_DOMAINE_PAR_LANGUE);
		}

		return $this->un_domaine_par_langue;
	}

	/**
	 * Retourne l'ID de la langue par défaut. Pour améliorer les perfs, on cache cet ID
	 * dans une variable statique.
	 */
	public function get_id_langue_defaut() {

		if ($this->id_langue_defaut === false) {

			$lang = new Lang();

			if ($lang->charger_defaut()) {
				$this->id_langue_defaut = $lang->id;
			}
			else {
				$this->id_langue_defaut = 1;
			}
		}

		return $this->id_langue_defaut;
	}

	/**
	 * Retourner l'action à effectuer si une traduction est absente
	 * @return boolean
	 */
	public function get_action_si_trad_absente() {

		// Intialiser l'action à affectuer si la traduction n'est pas definie
		if ($this->action_si_trad_absente === false) {
			$this->action_si_trad_absente = Variable::lire(self::VAR_ACTION_SI_TRAD_ABSENTE);
		}

		return $this->action_si_trad_absente;
	}

	/**
	 * Retourne la langue courante.
	 */
	public function get_langue_courante() {
		return new Lang($this->get_id_langue_courante());
	}

	/**
	 * Retourne l'ID de la langue courante.
	 */
	public function get_id_langue_courante() {
		return $this->id_langue_courante_defini() ? $this->get_id_lang_session() : $this->get_id_langue_defaut();
	}

	/**
	 * Met à jour l'ID de la langue courante.
	 */
	public function set_id_langue_courante($id_langue) {

		if ($this->mode_backoffice)
			$_SESSION["util"]->lang = $id_langue;
		else
			$_SESSION['navig']->lang = $id_langue;
	}

	/**
	 * Détermine si une langue est définie en session.
	 */
	public function id_langue_courante_defini() {
		return $this->mode_backoffice ? isset($_SESSION["util"]->lang) : isset($_SESSION['navig']->lang);
	}

	/**
	 * Retourne la langue actuellement définie en session
	 */
	public function get_id_lang_session() {
		return $this->mode_backoffice ? $_SESSION["util"]->lang : $_SESSION['navig']->lang;
	}
}
?>