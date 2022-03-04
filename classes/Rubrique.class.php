<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*		email : info@thelia.net                                                      */
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
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/
require_once __DIR__ . "/../fonctions/autoload.php";

class Rubrique extends Baseobjclassable
{
		public $id;
		public $parent;
		public $lien;
		public $ligne;
		public $classement;

		const TABLE="rubrique";
		public $table=self::TABLE;

		public $bddvars = ["id", "parent", "lien", "ligne", "classement"];

		public function __construct($id = 0)
		{
				parent::__construct("parent", "modrub");
				if ($id > 0) $this->charger($id);
		}

		public function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=".intval($id));
		}

		public function delete()
		{
				if (! empty($this->id)) {
						$this->delete_cascade('Produit', 'rubrique', $this->id);
						$this->delete_cascade('Image', 'rubrique', $this->id);
						$this->delete_cascade('Document', 'rubrique', $this->id);
						$this->delete_cascade('Rubriquedesc', 'rubrique', $this->id);
						$this->delete_cascade('Rubdeclinaison', 'rubrique', $this->id);
						$this->delete_cascade('Rubcaracteristique', 'rubrique', $this->id);

						// Supprimer les sous-rubriques
						$this->delete_cascade('Rubrique', 'parent', $this->id);

						parent::delete();
				}
		}

		public function nbprod()
		{
				$resul = $this->query("SELECT COUNT(*) AS nb FROM ".Produit::TABLE." WHERE rubrique=".intval($this->id));
				return $resul ? $this->get_result($resul, 0, "nb") : 0;
		}

		public function aenfant()
		{
				$resul = $this->query("SELECT COUNT(*) AS nb FROM $this->table WHERE parent=".intval($this->id));
				return ($resul && $this->get_result($resul, 0, "nb") > 0) ? 1 : 0;
		}
}
?>
