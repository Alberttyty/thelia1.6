<?php
/**
 * Ce singleton permet de gérer la manipulation des client dans le front-office.
 *
 * @author Franck Allimant <franck@cqfdev.fr>
 * @version $Id$
 */

require_once __DIR__ . "/../../fonctions/autoload.php";

class ActionsDevises extends ActionsBase {

	private static $instance = false;

	private $id_devise_defaut = false;

	protected function __construct() {
	}

	/**
	 * Cette classe est un singleton
	 * @return ActionsAdminModules une instance de ActionsAdminModules
	 */
	public static function instance() {
		if (self::$instance === false) self::$instance = new ActionsDevises();

		return self::$instance;
	}

	/**
	 * Substitutions mail spécifiques aux devises
	 *
	 * @param string $texte
	 * @return string le texte avec les substitutions réalisées
	 */
	public function subsititutions_mail($devise, $texte) {

		return $this->_substitutions($devise, $texte, '__', '__');
	}

	/**
	 * Substitutions simples spécifiques aux devises
	 */
	public function substitutions($devise, $texte) {

		return $this->_substitutions($devise, $texte, '#', '');
	}

	/**
	 * Méthode générique de substitutions
	 */
	protected function _substitutions($devise, $texte, $prefixe, $suffixe) {

		if (strstr($texte, "${prefixe}DEVISE")) {

			$texte = str_replace("${prefixe}DEVISE_HTMLSYMBOLE${suffixe}", htmlentities($devise->symbole, ENT_COMPAT, 'UTF-8', false), $texte);
			$texte = str_replace("${prefixe}DEVISE_SYMBOLE${suffixe}", $devise->symbole, $texte);
			$texte = str_replace("${prefixe}DEVISE_DEFAUT${suffixe}", $devise->defaut, $texte);
			$texte = str_replace("${prefixe}DEVISE_TAUX${suffixe}", $devise->taux, $texte);
			$texte = str_replace("${prefixe}DEVISE_CODE${suffixe}", $devise->code, $texte);
			$texte = str_replace("${prefixe}DEVISE_NOM${suffixe}", $devise->nom, $texte);
			$texte = str_replace("${prefixe}DEVISE_ID${suffixe}", $devise->id, $texte);
			$texte = str_replace("${prefixe}DEVISE${suffixe}", $devise->id, $texte);
		}

		return $texte;
	}

	/**
	 * Retourne l'ID de la devise par défaut. Pour améliorer les perfs, on cache cet ID
	 * dans une variable statique.
	 */
	public function get_id_devise_defaut() {

		if ($this->id_devise_defaut === false) {

			$devise = new Devise();

			if ($devise->charger_defaut()) {
				$this->id_devise_defaut = $devise->id;
			}
			else {
				$this->id_devise_defaut = 1;
			}
		}

		return $this->id_devise_defaut;
	}

	/**
	 * Retourne la devise courante.
	 */
	public function get_devise_courante() {
		return new Devise($this->get_id_devise_courante());
	}

	/**
	 * Retourne l'ID de la devise courante.
	 */
	public function get_id_devise_courante() {
		return $this->id_devise_courante_definie() ? $this->get_id_devise_session() : $this->get_id_devise_defaut();
	}

	/**
	 * Met à jour l'ID de la devise courante.
	 */
	public function set_id_devise_courante($id_devise) {

		$_SESSION['navig']->devise = $id_devise;
	}

	/**
	 * Détermine si une devise est définie en session.
	 */
	public function id_devise_courante_definie() {
		return (isset($_SESSION['navig']->devise) && $_SESSION['navig']->devise !== false);
	}

	/**
	 * Retourne la devise actuellement définie en session
	 */
	public function get_id_devise_session() {
		return $_SESSION['navig']->devise;
	}
}
?>