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
/*	 Ê Êalong with this program. ÊIf not, see <http://www.gnu.org/licenses/>.		 */
/*                                                                                   */
/*************************************************************************************/

    require_once __DIR__ . "/../../../fonctions/autoload.php";

    class TlogDestinationTexte extends AbstractTlogDestination {

		public function __construct() {
			parent::__construct();
		}

		public function get_titre() {
			return "Affichage direct dans la page, en texte brut";
		}

		public function get_description() {
			return "Permet d'afficher les logs directement dans la page resultat, au format texte brut.";
		}

		public function ajouter($texte) {
			echo $texte."\n";
		}

        public function ecrire(&$res) {
			// Rien
        }
    }
?>