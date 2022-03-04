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

class Caracteristique extends Baseobjclassable
{
		public $id;
		public $affiche;
		public $classement;

		const TABLE = "caracteristique";
		public $table = self::TABLE;

		public $bddvars = [
				"id",
				"affiche",
				"classement"
		];

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
				if (! empty($this->id)) {
						$this->delete_cascade('Caracteristiquedesc', 'caracteristique', $this->id);
						$this->delete_cascade('Caracdisp', 'caracteristique', $this->id);
						$this->delete_cascade('Rubcaracteristique', 'caracteristique', $this->id);

						parent::delete();
				}
		}
}
?>
