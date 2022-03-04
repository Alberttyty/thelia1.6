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

class Ventedeclibre extends Baseobj
{
		public $id;
		public $venteprod;
		public $declibre;

		public $table="ventedeclibre";
		public $bddvars = array("id", "venteprod", "declibre");

		function Ventedeclibre()
		{
				$this->Baseobj();
		}

		function init()
		{
				$query_declibre = "CREATE TABLE `ventedeclibre` (
					  `id` int(11) NOT NULL auto_increment,
					  `venteprod` int(11) NOT NULL,
					  `declibre` int(11) NOT NULL,
					  PRIMARY KEY  (`id`)
				);"
				;
				$resul_declibre = mysql_query($query_declibre, $this->link);
		}

		function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

    function charger_prod($venteprod)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE venteprod=\"$venteprod\"");
		}

		function charger_vdec($venteprod, $declibre)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE venteprod=\"$venteprod\" AND declibre=\"$declibre\"");
		}
}
?>
