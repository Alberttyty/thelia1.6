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

class Paysdesc extends Baseobjdesc
{
		public $id;
		public $pays;

		const TABLE = "paysdesc";

		public $table = self::TABLE;

		public $bddvars = ["id", "pays", "lang", "titre", "chapo", "description"];

		public function __construct($pays = 0, $lang = false)
		{
				parent::__construct('pays', $pays, $lang);
		}

		public function charger($pays = null, $lang = null)
		{
				if ($pays != null) return $this->charger_desc($pays, $lang);
		}
}
?>
