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

    class TlogDestinationPopup extends AbstractTlogDestination {

		// Nom des variables de configuration
		// ----------------------------------
		const VAR_POPUP_WIDTH = "tlog_destinationpopup_width";
		const VALEUR_POPUP_WIDTH_DEFAUT = "600";

		const VAR_POPUP_HEIGHT = "tlog_destinationpopup_height";
		const VALEUR_POPUP_HEIGHT_DEFAUT = "600";

		const VAR_POPUP_TPL = "tlog_destinationpopup_template";
		// Ce fichier doit se trouver dans le même répertoire que TlogDestinationPopup.class.php
		const VALEUR_POPUP_TPL_DEFAUT = "TlogDestinationPopup.tpl";

		public function __construct() {
			parent::__construct();
		}

		public function get_titre() {
			return "Fenêtre javascript";
		}

		public function get_description() {
			return "Permet d'afficher les logs dans une fenêtre séparée de la fenêtre principale.";
		}

		public function get_configs() {
			return array(
				new TlogDestinationConfig(
						self::VAR_POPUP_TPL,
						"Template de la fenêtre popup",
						"Insérez #DEBUGTEXT à l'endroit où vous voulez afficher les logs.",
						file_get_contents(__DIR__."/" . self::VALEUR_POPUP_TPL_DEFAUT),
						TlogDestinationConfig::TYPE_TEXTAREA
				),
				new TlogDestinationConfig(
						self::VAR_POPUP_HEIGHT,
						"Hauteur de la fenêtre popup",
						"En pixels",
						self::VALEUR_POPUP_HEIGHT_DEFAUT,
						TlogDestinationConfig::TYPE_TEXTFIELD
				),
				new TlogDestinationConfig(
						self::VAR_POPUP_WIDTH,
						"Largeur de la fenêtre popup",
						"En pixels",
						self::VALEUR_POPUP_WIDTH_DEFAUT,
						TlogDestinationConfig::TYPE_TEXTFIELD
				)
			);
		}

        public function ecrire(&$res) {

			$content = ""; $count = 1;

			foreach($this->_logs as $line) {
				$content .= "<div class=\"".($count++ % 2 ? "paire" : "impaire")."\">".htmlspecialchars($line)."</div>";
			}

	    	$tpl = $this->get_config(self::VAR_POPUP_TPL);

	    	$tpl = str_replace('#DEBUGTEXT', $content, $tpl);
	    	$tpl = str_replace(array("\r\n", "\r", "\n"), '\\n', $tpl);

	    	$wop = sprintf('
					<script type="text/javascript">
					    _thelia_console = window.open("","console_thelia","width=%s,height=%s,resizable,scrollbars=yes");
					    _thelia_console.document.write("%s");
					    _thelia_console.document.close();
					</script>',
					$this->get_config(self::VAR_POPUP_WIDTH),
					$this->get_config(self::VAR_POPUP_HEIGHT),
					str_replace('"', '\\"', $tpl)
			);

			if (preg_match("|</html>|i", $res))
				$res = preg_replace("|</html>|i", "$wop\n</html>", $res);
			else
				$res .= $wop;
       }
    }
?>