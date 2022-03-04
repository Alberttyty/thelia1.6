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
/*	 � �along with this program. �If not, see <http://www.gnu.org/licenses/>.		 */
/*                                                                                   */
/*************************************************************************************/

require_once __DIR__ . "/../../fonctions/autoload.php";

class TheliaException extends Exception {

	/**
	 * Codes des exception relatives aux Modules
	 */
	const MODULE_REPERTOIRE_NON_TROUVE = 100;
	const MODULE_INACTIF = 101;
	const MODULE_ERR_SUPPRESSION_REPERTOIRE = 102;
	const MODULE_INCOMPATIBLE = 103;
	const MODULE_ECHEC_CHARGEMENT = 104;
	const MODULE_ECHEC_INSTALL = 105;
	const MODULE_CLASSE_NON_TROUVEE = 106;
	const MODULE_INVALIDE = 107;
	const MODULE_FICHIER_ADMIN_NON_TROUVE = 108;
	const MODULE_DESCRIPTEUR_XML_NON_TROUVE = 109;
	const MODULE_ECHEC_UPLOAD = 110;
	const MODULE_PREREQUIS_NON_VERIFIES = 111;

	const MODULE_ECHEC_MIGRATION_DESCRIPTEUR = 112;
	const MODULE_ECHEC_VALIDATION_DESCRIPTEUR = 113;

}


?>