<?php
/**
 * Utilisation des modules depuis le front-office
 *
 * Ce singleton permet de gérer la manipulation des modules dans le front-office.
 *
 * @author Franck Allimant <franck@cqfdev.fr>
 * @version $Id$
 */
require_once __DIR__ . "/../../fonctions/autoload.php";
require_once(__DIR__ . '/../../fonctions/traduction.php');

class ActionsModules extends ActionsBase {

	private static $instance = false;

	private $utiliser_cache_plugins = false;

	protected $plugins_base_dir;

	protected function __construct() {
		// Définir le répertoire de base des plugins
		$this->plugins_base_dir = __DIR__ . "/../../client/plugins";
		$this->utiliser_cache_plugins = intval(Variable::lire("utilisercacheplugin")) != 0;
	}

	public static function instance() {
		if (self::$instance === false) self::$instance = new ActionsModules();
		return self::$instance;
	}

	/**
	 * @return string le chemin d'accès au dossier plugins sur le disque
	 */
	public function lire_chemin_base() {
		return $this->plugins_base_dir;
	}

	/**
	 * @return string l'URL de base d'accès au dossier plugins
	 */
	public function lire_url_base() {
		return rtrim(urlsite(), '/') . "/client/plugins";
	}

	/**
	 * @method string Retourne le chemin d'accès au répertoire d'un module
	 * @return Répertoire dans lequel se trouve le module
	 * @throws TheliaException::MODULE_REPERTOIRE_NON_TROUVE si le repertoire module n'existe pas
	 */
	public function lire_chemin_module($nom_module, $controle_existence = true) {

		$path = "$this->plugins_base_dir/$nom_module";
		if ($controle_existence === false || (file_exists($path) && is_dir($path)) ) return $path;

		throw new TheliaException(trad("Répertoire du module %s non trouvé", 'admin', $nom_module), TheliaException::MODULE_REPERTOIRE_NON_TROUVE);
	}

	/**
	 * @method mixed Crée une instance d'un module à partir de son nom
	 * @return l'instance créée
	 * @throws TheliaException::MODULE_CLASSE_NON_TROUVEE si le module n'a pas été trouvé.
	 */
	private static $cache = array();

	public function instancier($nom_module) {

		$clazz = ucfirst($nom_module);

		if (! $this->utiliser_cache_plugins || ! isset(self::$cache[$nom_module])) {

			$classpath = $this->lire_chemin_module($nom_module) . "/$clazz". ".class.php";

			// Ne pas utiliser @include, qui masque les éventuelles erreurs dans le fichier classe,
			if (file_exists($classpath) && include_once($classpath))
			{
				$instance = new $clazz();

				// S'assurer que le module a fourni son nom
				if (empty($instance->nom_plugin)) $instance->nom_plugin = $nom_module;

				self::$cache[$nom_module] = $instance;
			}
			else {
				throw new TheliaException(trad("Aucune classe trouvé pour %s", 'admin', $nom_module), TheliaException::MODULE_CLASSE_NON_TROUVEE);
			}
		}

		return self::$cache[$nom_module];
	}

	/**
	 * Récupérer le contenu du descripteur XML d'un module
	 *
	 * @param Module $module le module concerné
	 * @throws TheliaException si le descripteur XML n'existe pas ou est invalide
	 */
	protected function lire_descripteur_xml($module) {

		if (! isset($module->xml)) {

			$fic_xml = $this->lire_chemin_module($module->nom) . '/plugin.xml';

			if (file_exists($fic_xml)) {

				// Valider le fichier plugin.xml
				$pdv = new PluginDescriptorValidator(__DIR__."/thelia_plugin.xsd");

				$pdv->validate($fic_xml);

				if ($xml = @simplexml_load_file($fic_xml)) {
					$module->xml = $xml;

					return;
				}
			}

			unset($module->xml);

			throw new TheliaException(trad("Fichier descripteur XML inexistant ou invalide pour %s", 'admin', $module->nom), TheliaException::MODULE_DESCRIPTEUR_XML_NON_TROUVE);
		}
	}

	/**
	 * @method bool Déterminer si un module est activable
	 * @param Modules modules un object Modules chargé
	 * @return bool true si le module est activable, ou n'a pas d'indication de version
	 */
	public function est_activable($modules) {

		try {
			$this->lire_descripteur_xml($modules);

			if (isset($modules->xml->thelia)) {

				$version_courante = rtrim(preg_replace("/(.)/", "$1.", Variable::lire('version')), ".");

				return version_compare($version_courante, $modules->xml->thelia) != -1;
			}

		} catch (Exception $e) {}

		return true;
	}

	/**
	 * @method Modules[] Lister les modules d'un type donné. Utilisation d'un cache.
	 * @param int $type: le type de module (Classique, Paiement, Transport, Filtre)
	 * @param bool $actifs_seulement: seulement les modules actifs ?
	 * @param string $nom_module: retourner uniquement le module indiqué
	 */
	private static $list_cache = array();

	public function lister($type = false, $actifs_seulement = false, $nom_module = '') {

		$modules = new Modules();

		$where = '';

		if ($type !== false) $where .= "and type=$type ";
		if ($actifs_seulement !== false) $where .= "and actif=1 ";
		if ($nom_module !== '') $where .= "and nom='$nom_module' ";

		$hash = md5($where);

		if (! isset(self::$list_cache[$hash])) {
			self::$list_cache[$hash] = array();

			$resul = CacheBase::getCache()->query("select * from $modules->table where 1 $where order by classement", 'Modules');

			if($resul != "") {
				foreach($resul as $modules) {
					try {

						if (! $actifs_seulement)
						{
							if ($this->est_activable($modules)) {
								$modules->activable = 1;
							} else {
								$modules->activable = 0;
							}
						}

						self::$list_cache[$hash][] = $modules;

					} catch (TheliaException $e) {
						// Ignorer ce module
					}
				}
			}
		}

		return self::$list_cache[$hash];
	}

	/**
	 * @method void Appel d'une méthode des modules qui l'implémentent
	 * @param string $methode le nom de la méthode
	 * @param mixed $arg2 l'argument 1 à passer à la méthode
	 * @param mixed $arg2 l'argument 2 à passer à la méthode
	 * @param string $nom_module si spécifié, seul ce module sera appelé
	 */
	public function appel_module($methode, &$arg1 = '', &$arg2 = '', $nom_module = '') {

		$liste = $this->lister(false, true, $nom_module);

		foreach($liste as $module) {
			try {
				$instance = $this->instancier($module->nom);

				if (method_exists($instance, $methode)) $instance->$methode($arg1, $arg2);
			} catch (Exception $e) {}
		}
	}

	/**
	 *
	 * @method void Mise à jour de la description d'un module
	 * @param string $nom nom du module associé
	 * @param int $lang ID de la langue de la description
	 * @param string $titre titre du module
	 * @param string $chapo chapo du module
	 * @param string $description description du module
	 * @param int $devise ID de la devise associée
	 */
	public function mise_a_jour_description($nom_module, $lang, $titre, $chapo, $description, $devise) {

        $md = new Modulesdesc();

        $existe = $md->verif($nom_module, $lang);

        $md->titre = $titre;
        $md->chapo = $chapo;
        $md->description = $description;
        $md->devise = $devise;

        if ($existe) {
            $md->maj();
        } else {
            $md->id = '';
            $md->lang = $lang;
            $md->plugin = $nom_module;

            $md->add();
        }
	}
}
?>
