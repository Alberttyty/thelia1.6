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
    
    require_once __DIR__ . "/../../../fonctions/autoload.php";
    
    class TlogDestinationMail extends AbstractTlogDestination {

		// Nom des variables de configuration
		// ----------------------------------
		const VAR_ADRESSES = "tlog_mail_adresses";

		public function __construct() {
			parent::__construct();
		}


		public function get_titre() {
			return "E-Mail";
		}

		public function get_description() {
			return "Envoie le log par e-mail aux adresses indiquées";
		}

		public function get_configs() {
			return array(
				new TlogDestinationConfig(
						self::VAR_ADRESSES,
						"Destinataires",
						"Indiquez une ou plusieurs adresses e-mail séparées par point-virgule",
						Variable::lire('emailfrom'),
						TlogDestinationConfig::TYPE_TEXTFIELD
				)
			);
		}

        public function ecrire(&$res) {

        	$texte = implode("\n", $this->_logs);

        	if (! empty($texte)) {
	        	$adresses = explode(";", $this->get_config(self::VAR_ADRESSES));

	        	// Un CC serait plus efficace...
	        	foreach($adresses as $adresse) {
	        		Mail::envoyer (
	        			"", $adresse,
	        			"Log " . Variable::lire('nomsite'), Variable::lire('emailfrom'),
	        			strftime("%d/%m/%Y %H:%M:%S", time()) . " - Logs " . Variable::lire('nomsite'),
	        			"", $texte);
	        	}
        	}
        }
    }
?>