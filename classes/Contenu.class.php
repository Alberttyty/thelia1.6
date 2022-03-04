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

class Contenu extends Baseobjclassable
{
		public $id;
		public $datemodif;
		public $ligne;
		public $dossier;
		public $classement;

		const TABLE = "contenu";
		public $table = self::TABLE;

		public $bddvars = [
			"id",
			"datemodif",
			"ligne",
			"dossier",
			"classement"
		];

		public function __construct($id = 0)
		{
				parent::__construct("dossier", "modcont");
				if ($id > 0) $this->charger($id);
		}


		public function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		public function delete()
		{
				if (! empty($this->id)) {
						$this->delete_cascade('Image', 'contenu', $this->id);
						$this->delete_cascade('Document', 'contenu', $this->id);
						$this->delete_cascade('Contenudesc', 'contenu', $this->id);
						$this->delete_cascade('Contenuassoc', 'contenu', $this->id);

						parent::delete();
				}
		}
}
?>
