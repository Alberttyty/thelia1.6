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

class Accessoire extends Baseobjclassable
{
		public $id;
		public $produit;
		public $accessoire;
		public $classement;

		const TABLE = "accessoire";
		public $table=self::TABLE;

		public $bddvars = ["id", "produit", "accessoire", "classement"];

		public function __construct($id = 0)
    {
        parent::__construct("produit");
        if ($id > 0) $this->charger($id);
 		}

		public function charger($id = null, $var2 = null)
    {
		    if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		public function charger_uni($produit, $accessoire)
    {
			  return $this->getVars("SELECT * FROM $this->table WHERE produit=\"$produit\" AND accessoire=\"$accessoire\"");
		}
	}
?>
