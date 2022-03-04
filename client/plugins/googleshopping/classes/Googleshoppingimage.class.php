<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) Octolys Development		                                     */
/*		email : thelia@octolys.fr		        	                             	 */
/*      web : http://www.octolys.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/
include_once(realpath(dirname(__FILE__)) . "/../../../../classes/Baseobj.class.php");

class Googleshoppingimage extends Baseobj
{
		public $id;
		public $image;
    public $couleurs;

		public $table="googleshopping_image";
		public $bddvars = array("id", "image", "couleurs");

		function Googleshoppingimage()
		{
				$this->Baseobj();
		}

		function init()
		{
				$query = "CREATE TABLE IF NOT EXISTS `googleshopping_image` (
						`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `image` INT NOT NULL ,
						`couleurs` TEXT NOT NULL
				);"
				;
				$resul = mysql_query($query, $this->link);
		}

		function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		function charger_image($image)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE image=\"$image\"");
		}

    function charger_couleurs($couleurs)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE couleurs LIKE \"%$couleurs%\"");
		}
}
?>
