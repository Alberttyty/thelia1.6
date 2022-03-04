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

class Message extends Baseobj
{
		public $id;
		public $nom;
		public $protege;

		const TABLE="message";
		public $table=self::TABLE;

		public $bddvars = ["id", "nom", "protege"];

		public function __construct($nom = "")
		{
				parent::__construct();
				if ($nom != "") $this->charger($nom);
		}

		public function charger($nom = null, $var2 = null)
		{
				if ($nom != null) return $this->getVars("SELECT * FROM $this->table WHERE nom=\"$nom\"");
		}

		public function  delete()
		{
				if (! empty($this->id)) {
						$this->delete_cascade('Messagedesc', 'message', $this->id);
						parent::delete();
				}
		}
}
?>
