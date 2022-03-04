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
require_once __DIR__ . "/../fonctions/autoload.php";

class PluginsTransports extends PluginsClassiques
{
		public $poids;
		public $nbart;
		public $total;
		public $zone;
		public $pays;
		public $unitetr;
		public $cpostal;

		public function __construct($nom="")
		{
				parent::__construct($nom);
		}

		/* Compatibilité avec les anciens plugins */
		public function PluginsTransports($nom="")
		{
				parent::__construct($nom);
		}

		public function calcule()
		{
				// A implementer
		}

		/*
		* Permet de déterminer si ce module est le module de transport pour
		* une commande donnée
		*/
		public function est_module_de_transport_pour($commande)
		{
				$module = new Modules();
				return $module->charger_id($commande->transport) && $module->nom == $this->getNom();
		}
}
?>
