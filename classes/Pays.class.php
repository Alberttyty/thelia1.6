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

class Pays extends Baseobj
{
		public $id;
		public $lang;
		public $zone;
		public $defaut;
		public $tva;
		public $isocode;
		public $isoalpha2;
		public $isoalpha3;
    public $boutique;

		const TABLE="pays";

		public $table=self::TABLE;

		public $bddvars=["id", "lang", "zone", "defaut", "tva", "isocode", "isoalpha2", "isoalpha3", "boutique"];

		public function __construct($id = 0)
		{
				parent::__construct();
				if ($id > 0) $this->charger($id);
		}

		public function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		public function charger_defaut()
		{
				return $this->getVars("SELECT * FROM $this->table WHERE defaut<>0");
		}
}
?>
