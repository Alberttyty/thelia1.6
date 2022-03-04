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

class Venteprod extends Baseobj
{
		public $id;
		public $ref;
		public $titre;
		public $chapo;
		public $description;
		public $quantite;
		public $prixu;
		public $tva;
		public $commande;
		public $parent;

		const TABLE="venteprod";
		public $table=self::TABLE;

		public $bddvars = ["id", "ref", "titre", "chapo", "description", "quantite", "prixu", "tva", "commande", "parent"];

		public function __construct($id = 0)
		{
				parent::__construct();
				if ($id > 0) $this->charger($id);
		}

		public function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		public function delete()
		{
				// Supprimer les ventedeclidisp associés
				$this->delete_cascade('Ventedeclidisp', 'venteprod', $this->id);
				parent::delete();
		}
}
?>
