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

class Caracdispdesc extends Baseobjdesc
{
		public $id;
		public $caracdisp;
		public $classement;

		const TABLE="caracdispdesc";
		public $table=self::TABLE;

		public $bddvars = ["id", "caracdisp", "lang", "titre", "classement"];

		public function __construct($id = 0, $lang = false)
		{
				parent::__construct('caracdisp', $id, $lang);
		}

		public function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"".$id."\"");
		}

		public function charger_caracdisp($caracdisp, $lang = false)
		{
				return parent::charger_desc($caracdisp, $lang);
		}

		public function add()
    {
        if (empty($this->classement)) {
            $sql = 'SELECT MAX(classement) FROM '.$this->table.' WHERE caracdisp='.$this->caracdisp;
            $query = $this->query($sql);
            $this->classement = $this->get_result($query)+1;
        }

        return parent::add();
    }
}
?>
