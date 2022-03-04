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

class Modulesdesc extends Baseobjdesc
{
		public $id;
		public $plugin;
		public $devise;

		const TABLE = "modulesdesc";
		public $table = self::TABLE;

		public $bddvars = array("id", "plugin", "lang", "titre", "chapo", "description", "devise");

		public function __construct($plugin = "", $lang = false)
		{
				parent::__construct('plugin', $plugin, $lang);
		}

		public function charger($plugin = null, $lang = null)
		{
				if ($plugin != null) return $this->charger_desc($plugin, $lang);
		}

		public function verif($plugin, $lang = false)
		{
				return 	$this->charger_desc($plugin, $lang, 'plugin', self::NE_PAS_TRAITER_TRAD_VIDE);
		}

		// La foreign key est une chaine de caractère dans le cas des modules
		protected function get_charger_sql($fkey, $lang, $colonne)
		{
				return "SELECT * FROM $this->table WHERE $colonne='$fkey' AND lang=".intval($lang);
		}
}
?>
