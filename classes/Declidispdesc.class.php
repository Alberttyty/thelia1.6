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

class Declidispdesc extends Baseobjdesc
{
		public $id;
		public $declidisp;
		public $classement;

		const TABLE = "declidispdesc";

		public $table = self::TABLE;

		public $bddvars = ["id", "declidisp", "lang", "titre", "classement"];

		public function __construct($declidisp = 0, $lang = false)
		{
				parent::__construct('declidisp', $declidisp, $lang);
				if ($declidisp > 0) $this->charger_declidisp($declidisp, $lang);
		}

		public function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"".$id."\"");
		}

		public function charger_declidisp($declidisp, $lang = false)
		{
				return parent::charger_desc($declidisp, $lang);
		}

		public function charger_valeur($titre)
		{
				return $this->getVars("select * from $this->table where titre='".$this->escape_string($titre)."'");
		}

		public function add()
	  {
	      if(empty($this->classement))
	      {
	          $sql = 'select max(classement) from '.$this->table.' where declidisp='.$this->declidisp;
	          $query = $this->query($sql);
	          $this->classement = $this->get_result($query)+1;
	      }

	      return parent::add();
	  }
}
?>
