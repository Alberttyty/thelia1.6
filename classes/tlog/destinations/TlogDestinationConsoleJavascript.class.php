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

    class TlogDestinationConsoleJavascript extends AbstractTlogDestination {

		public function __construct() {
			parent::__construct();
		}

		public function get_titre() {
			return "Console javascript";
		}

		public function get_description() {
			return "Permet d'afficher les logs dans la console Javascript de votre navigateur.";
		}

        public function ecrire(&$res) {

			$content = '<script type="text/javascript">try {'."\n";

			foreach($this->_logs as $line) {
				$content .= "console.log('".str_replace("'", "\\'", str_replace(array("\r\n", "\r", "\n"), '\\n', $line))."');\n";
			}

			$content .= '} catch(ex) { alert("Les logs Thelia ne peuvent être affichés dans la console javascript:" + ex); }</script>'."\n";

			if (preg_match("|</html>|i", $content))
				$res = preg_replace("|</html>|i", "$content</html>", $res);
			else
				$res .= $content;
       }
    }
?>