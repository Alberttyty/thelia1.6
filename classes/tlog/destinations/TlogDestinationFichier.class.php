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
    
    require_once __DIR__ . "/../../../fonctions/autoload.php";
    
    class TlogDestinationFichier extends AbstractTlogDestination {

		// Nom des variables de configuration
		// ----------------------------------
		const VAR_PATH_FICHIER = "tlog_destinationfichier_path";
		const VALEUR_NOM_DEFAUT = "log-thelia.txt";

		const VAR_MODE = "tlog_destinationfichier_mode";
		const VALEUR_MODE_DEFAUT = "E";

		protected $path_defaut = false;
		protected $fh = false;

		public function __construct() {

			$this->path_defaut = __DIR__. "/../../../client/tlog/".self::VALEUR_NOM_DEFAUT;

			parent::__construct();
		}

		public function configurer($config = false) {
			$file_path = $this->get_config(self::VAR_PATH_FICHIER);
			$mode = strtolower($this->get_config(self::VAR_MODE)) == 'a' ? 'a' : 'w';

			if (! empty($file_path)) {
				if (! is_file($file_path)) {
					$dir = dirname($file_path);
					if(! is_dir($dir)) {
						mkdir($dir, 0777, true);
					}
				}

				if ($this->fh) @fclose($this->fh);

				$this->fh = fopen($file_path, $mode);
			}
		}

		public function get_titre() {
			return "Fichier texte";
		}

		public function get_description() {
			return "Permet de stocker les logs dans un fichier de votre choix";
		}

		public function get_configs() {
			return array(
				new TlogDestinationConfig(
						self::VAR_PATH_FICHIER,
						"Chemin du fichier",
						"Attention, vous devez indiquer un chemin absolu.<br />Le répertoire de base de votre Thelia est ".dirname(getcwd()),
						$this->path_defaut,
						TlogDestinationConfig::TYPE_TEXTFIELD
				),
				new TlogDestinationConfig(
						self::VAR_MODE,
						"Mode d'ouverture (A ou E)",
						"Indiquez E pour ré-initialiser le fichier à chaque requête, A pour ne jamais réinitialiser le fichier. Pensez à le vider de temps en temps !",
						self::VALEUR_MODE_DEFAUT,
						TlogDestinationConfig::TYPE_TEXTFIELD
				)
			);
		}

		public function ajouter($texte) {
			if ($this->fh) {
				fwrite($this->fh, $texte."\n");
			}
		}

        public function ecrire(&$res) {
			if ($this->fh) @fclose($this->fh);

			$this->fh = false;
        }
    }
?>