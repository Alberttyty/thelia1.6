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

class Devise extends Baseobj
{
		public $id;
		public $nom;
		public $code;
		public $symbole;
		public $taux;
		public $defaut;

		const TABLE="devise";
		public $table=self::TABLE;

		public $bddvars = ["id", "nom", "code", "symbole", "taux", "defaut"];

		public function __construct($id =  0)
		{
				parent::__construct();
				if ($id  > 0) $this->charger($id);
		}

		public function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		public function charger_nom($nom)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE nom=\"$nom\"");
		}

		public function charger_symbole($symbole)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE symbole=\"$symbole\"");
		}

		/**
		 * Charger la devise par défaut.
		 *
		 * @return la devise par defaut.
		 */
		public function charger_defaut()
		{
				return $this->getVars("SELECT * FROM $this->table WHERE defaut<>0");
		}
}
?>
