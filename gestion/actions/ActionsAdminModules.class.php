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
/**
 * Administration des modulesGestion des modules depuis l'administration
 *
 * Ce singleton permet de gérer la manipulation des modules depuis l'admin Thelia.
 *
 * @author Franck Allimant <franck@cqfdev.fr>
 * @version $Id$
 */
require_once(__DIR__ . '/../../lib/pclzip.lib.php');

class ActionsAdminModules extends ActionsModules
{
		private static $instance = false;

		private function __construct()
		{
				parent::__construct();
		}

		/**
		 * Cette classe est un singleton
		 * @return ActionsAdminModules une instance de ActionsAdminModules
		 */
		public static function instance()
		{
				if (self::$instance === false) self::$instance = new ActionsAdminModules();
				return self::$instance;
		}

		/**
		 * @method string Retourne le titre du module, en fonction du contenu du fichier XML
		 * @param Modules le module concerné
		 * @return string le titre du module
		 */
		public function lire_titre_module($modules)
		{
				try {
						$this->lire_descripteur_xml($modules);
						if ($modules->xml->descriptif->titre != "") return $modules->xml->descriptif->titre;
				}
				catch (Exception $e) {}

				return $modules->nom;
		}

		/**
		 * @method bool determiner si le fichier xxx_adminyyy existe sur un module
		 * @param string le nom complet du fichier
		 * @throws TheliaException::MODULE_FICHIER_ADMIN_NON_TROUVE si le fichier n'existe pas
		 */
		public function trouver_fichier_admin($nom_module, $type_fichier_admin = false)
		{
				$suffixe = '_admin';

				if ($type_fichier_admin !== false) $suffixe .= "_$type_fichier_admin";

				$path = $this->lire_chemin_module($nom_module) . "/${nom_module}${suffixe}.php";

				if (file_exists($path)) return $path;
				else throw new TheliaException(trad("Fichier admin %s non trouvé", 'admin', $path), TheliaException::MODULE_FICHIER_ADMIN_NON_TROUVE);
		}

		/**
		 * @method bool determiner si un module comporte un include dans le B.O.
		 * @param string le nom du module
		 * @return true si le module est present dans l'admin, false sinon.
		 * @throws TheliaException::MODULE_FICHIER_ADMIN_NON_TROUVE si le fichier n'existe pas
		 */
		public function est_administrable($nom_module)
		{
				$suffixe = '_admin';
				$dir = $this->lire_chemin_module($nom_module);
				$est_admin = false;

				if ($dh = @opendir($dir)) {
						while ($entry = readdir($dh)) {
								if (strstr($entry, '_admin') !== FALSE) {
										$est_admin = true;
										break;
								}
						}

						@closedir($dh);
				}

				return $est_admin;
		}

		/**
		 * @method void inclure dans une page admin un fichier spécifique des modules
		 * @param string le nom complet du fichier
		 */
		public function inclure_module_admin($type_fichier_admin = false)
		{
				$liste = $this->lister(false, true);

				foreach($liste as $module) {
						if (method_exists($module, 'est_autorise') && $module->est_autorise()) {
								try {
											$path = $this->trouver_fichier_admin($module->nom, $type_fichier_admin);
											include_once($path);
								}
								catch (Exception $e) {}
						}
				}
		}

		/**
	     *
	     * Cherches les fichiers de langues définit dans les plugins activés. Le fichier de langue doit être dans le répertoire lang_admin du plugin
	     *
	     * @param int $lang
	     */
	    public function inclure_lang_admin($lang)
	    {
	        $liste = $this->lister(false, true);

	        foreach($liste as $module) {
		        	try {
			            $path = $this->lire_chemin_module($module->nom) . "/lang/".$lang.'.php';
			            if (file_exists($path)) include_once($path);
		        	}
		        	catch (Exception $ex) {}
	        }
	    }

		/**
		 * @method Modules Charger un module depuis la BD
		 * @param string $nom_module: le nom du module
		 * @return un objet Modules chargé
		 * @throws MODULE_ECHEC_CHARGEMENT si le module ne peut pas être chargé.
		 */
		public function charger($nom_module)
		{
				$modules = new Modules();
				if ($modules->charger($nom_module)) return $modules;
				throw new TheliaException(trad("Ne peut charger le module %s depuis la BD: ", 'admin', $nom_module), TheliaException::MODULE_ECHEC_CHARGEMENT);
		}

		/**
		 * @method Modules Activer un module
		 * @param string $nom_module: le nom du module
		 * @return un objet Modules chargé
		 * @throws MODULE_INCOMPATIBLE si le module est incompatibel avec la version courante de Thelia
		 * @throws MODULE_PREREQUIS_NON_VERIFIES si les prérequis du modules ne sont pas vérifiés.
		 */
		public function activer($nom_module)
		{
				$modules = $this->charger($nom_module);
				$instance = $this->instancier($nom_module);

				if ($this->est_activable($modules))	{
						if ($instance->prerequis()) {
								$instance->init();
								$modules->actif = 1;
								$modules->maj();

								return $modules;
						}
						else {
							throw new TheliaException(trad("Les prérequis du module %s ne sont pas vérifiés", 'admin', $nom_module), TheliaException::MODULE_PREREQUIS_NON_VERIFIES);
						}
				}
				else {
						throw new TheliaException(trad("Module %s incompatible avec votre version de Thelia", 'admin', $nom_module), TheliaException::MODULE_INCOMPATIBLE);
				}
		}

		/**
		 * @method Modules Désactiver un module
		 * @param string $nom_module: le nom du module
		 * @return un objet Modules chargé
		 */
		public function desactiver($nom_module)
		{
				$modules = $this->charger($nom_module);

				// On essaye d'invoquer destroy()
				try {
						$instance = $this->instancier($nom_module);
						$instance->destroy();
				}
				catch (Exception $e) { }

				$modules->actif = 0;
				$modules->maj();

				return $modules;
		}

		/**
		 * @method void Supprime un module, après avoir invoqué la méthiode destroy
		 * @param string $nom_module: le nom du module
		 */
		public function supprimer($nom_module)
		{
				// Tenter de désactiver le module, sans tenir compte des erreurs
				try {
						$modules = $this->desactiver($nom_module);

						// Supprimer le module de la BD
						$modules->delete();
						// Supprimer aussi la description
						$modules->query("delete from " . Modulesdesc::TABLE . " where plugin='$nom_module'");
				}
				catch (Exception $e) {
						// Ignorer l'erreur
				}

				// Par mesure de précaution
				$nom_module = basename($nom_module);

				if ($nom_module != '') {
						// Supprimer le répertoire des modules
						$this->delTree($this->lire_chemin_module($nom_module));

						// En cas d'échec de la suppression
						try {
								$this->lire_chemin_module($nom_module);
								$existe_toujours = true;
						}
						catch (Exception $e) {
								// Le repertoire n'existe plus !
								$existe_toujours = false;
						}

						if ($existe_toujours) throw new TheliaException(trad("Echec de la suppression du répertoire du module %s", 'admin', $nom_module) , TheliaException::MODULE_ERR_SUPPRESSION_REPERTOIRE);
				}
		}

		/**
		 * @method void Upload et décompresse un plugin au format zip
		 * @throws MODULE_ECHEC_UPLOAD si le module n'a pas pu être chargé décompressé
		 * @throws MODULE_INVALIDE si le fichier à installer ne contient pas un module Thelia
		 * @throws MODULE_ECHEC_INSTALL si l'installation échoue (problème de zip, de copie, etc.)
		 */
		public function installer($uploadedfile, $fichier_zip)
		{
				require_once(__DIR__ . '/../../lib/pclzip.lib.php');

				if ($uploadedfile != '') {
						$path_zip = "$this->plugins_base_dir/$fichier_zip";

						if (@copy($uploadedfile, $path_zip)) {
								$archive = new PclZip($path_zip);
								$resul = $archive->extract(PCLZIP_OPT_PATH, $this->plugins_base_dir);

								@unlink($path_zip);

								if ($resul == 0) {
										throw new TheliaException(trad("Echec à l'installation du module %s. Erreur ZIP: %s", 'admin', $nom_module, $archive->errorInfo(true)), TheliaException::MODULE_ECHEC_INSTALL);
								}

								// Vérifier qu'on peut instancier le plugin. Retrouver tout d'abord le repertoire du plugin
								$tmp = preg_split ("/[\/\\\:]/", $resul[0]['stored_filename']);
								$module_name = $tmp[0];

								try {
									$this->instancier($module_name);
									$this->conversion_utf8($this->lire_chemin_module($module_name));
								}
								catch (Exception $ex) {
										// Supprimer le répertoire
										if ($module_name != '') $this->delTree($this->plugins_base_dir . "/$module_name");
										throw new TheliaException(trad("Le fichier %s ne semble pas contenir un module Thelia", 'admin', $fichier_zip), TheliaException::MODULE_INVALIDE);
								}
						}
						else {
								throw new TheliaException(trad("Echec à la copie du fichier %s vers %s", 'admin', $uploadedfile, $path_zip), TheliaException::MODULE_ECHEC_INSTALL);
						}
				}
		}

		/**
		 * @method void Mise a jour des modules en BD en fonction du contenu du repertoire des modules
		 */
		public function mettre_a_jour()
		{
				if ($dh = opendir($this->plugins_base_dir)) {
						while ($file = readdir($dh)) {
								if ($file == '.' || $file == '..') continue;

								try {
										// Tenter d'instancier le plugin
										$instance = $this->instancier($file);
										$modules = new Modules();

										if ($instance instanceof PluginsPaiements) $modules->type = Modules::PAIEMENT;
										else if ($instance instanceof PluginsTransports) $modules->type = Modules::TRANSPORT;
										else if ($instance instanceof FiltreBase) $modules->type = Modules::FILTRE;
										else if ($instance instanceof PluginsClassiques) $modules->type = Modules::CLASSIQUE;
										else continue; // On ignore

										// Vérifier si le module existe en BD, et l'ajouter s'il n'y est pas
										if (! $modules->charger($file)) {
												$modules->nom = $file;
												$modules->actif = 0;

												$modules->add();

												// On en profite pour le convertir en UTF-8 au passage si nécessaire.
												$this->conversion_utf8($this->lire_chemin_module($modules->nom));

												// Lire le descripteur, et mettre à jour la description du module en base
												$this->lire_descripteur_xml($modules);

												foreach($modules->xml->descriptif as $desc) {
														$codelang = $desc->attributes()->lang;
														$lang = new Lang();

														if ($lang->charger_code($codelang)) {
																$this->mise_a_jour_description(
																			$modules->nom,
																			$lang->id,
																			$desc->titre,
																			$desc->chapo,
																			$desc->description,
																			0 // Devise ?
																);
														}
												}
										}
								}
								catch (Exception $ex) {
										// On ne peut pas instancier -> ignorer
								}
						}

						@closedir($dh);
				}

				// Vérifier que les plugins en base existent toujours sur disque
				$modules = new Modules();
				$result = $modules->query('select nom from '.Modules::TABLE);

				while ($result && $row = $modules->fetch_object($result)) {
						try {
								$this->lire_chemin_module($row->nom);
						}
						catch (Exception $ex) {
								// Le plugin n'existe plus sur disque -> le retirer de la BD
								if ($modules->charger($row->nom)) {
										$modules->delete();
										// Supprimer aussi la description
										$modules->query("delete from " . Modulesdesc::TABLE . " where plugin='$modules->nom'");
								}
						}
				}

				CacheBase::getCache()->reset_cache();
		}

		/**
		 * @method void Suppression récursive du contenu d'un répertoire
		 * @todo FIXME: A déplacer vers une classe outils ?
		 */
		private function delTree($dir)
		{
				if (is_dir($dir)) {
						if ($dh = opendir($dir)) {
								while ($file = readdir($dh)) {
										if ($file == '.' || $file == '..') continue;
										$this->delTree("$dir/$file");
								}

								@closedir($dh);
								@rmdir($dir);
						}
				}
				else @unlink($dir);
		}

		/**
		 * @method void convertir récursivement en UTF-8 les fichiers d'un plugin uploadé
		 * @param string $path le chemin d'accès au fichier
		 */
		private function conversion_utf8($path)
		{
				if (is_dir($path)) {
						if ($dh = opendir($path)) {
								while ($file = readdir($dh)) {
										if ($file == '.' || $file == '..') continue;
										$this->conversion_utf8("$path/$file");
								}

								@closedir($dh);
						}
				}
				else {
						// Uniquement les .php, .xml et .txt
						$ext = strtolower(substr($path, -4));

						if ( ($ext == '.php' || $ext == '.txt' || $ext == '.xml') && $str = file_get_contents($path)) {
								$is_utf8 = mb_detect_encoding($str, 'UTF-8', true);

								if (! $is_utf8) {
										if ($fh = @fopen($path, "w")) {
												$str = str_ireplace("iso-8859-1", "utf-8", $str);
												fwrite($fh, utf8_encode($str));
												@fclose($fh);
										}
								}
						}
				}
		}
}
?>
