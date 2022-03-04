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
include_once(realpath(dirname(__FILE__)) . "/../../../../classes/Venteprod.class.php");

class Venteoptlibre extends Baseobj
{
		public $id;
		public $venteprod;
		public $optlibre;
		public $table="venteoptlibre";
		public $bddvars = array("id", "venteprod", "optlibre");

		function Venteoptlibre()
		{
				$this->Baseobj();
		}

		function init()
		{
				$query_optlibre = "CREATE TABLE `venteoptlibre` (
					  `id` int(11) NOT NULL auto_increment,
					  `venteprod` int(11) NOT NULL,
					  `optlibre` int(11) NOT NULL,
					  PRIMARY KEY  (`id`)
				);"
				;
				$resul_optlibre = mysql_query($query_optlibre, $this->link);
		}

		function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		function charger_vdec($venteprod, $optlibre)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE venteprod=\"$venteprod\" AND optlibre=\"$optlibre\"");
		}

    /* Compter les options vendues pour une ref */
    function compterVendus($ref,$optlibre)
		{
      	$venteprod=new Venteprod();
      	$query = "SELECT COUNT($this->table.id) as nombre
            	  FROM $this->table,$venteprod->table
            	  WHERE $this->table.venteprod=$venteprod->table.id AND $venteprod->table.ref=\"$ref\" AND $this->table.optlibre=$optlibre";
      	$nombre = $this->query($query);
      	return $this->get_result($nombre,0,"nombre");
		}
}
?>
