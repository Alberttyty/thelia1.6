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

class Declibredesc extends Baseobj
{
		public $id;
		public $declibre;
		public $declinaison;
		public $lang;

		public $table = "declibredesc";

		public $bddvars=array("id", "declibre", "declinaison", "lang");

		function Optlibredesc()
		{
				$this->Baseobj();
		}

		function init()
		{
				$query_declibredesc = "CREATE TABLE `declibredesc` (
						`id` BIGINT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`declibre` BIGINT unsigned NOT NULL ,
						`declinaison` TEXT NOT NULL ,
						`lang` INT NOT NULL
				);"
				;
				$resul_declibredesc = mysql_query($query_declibredesc, $this->link);
		}

    function charger($declibre = null, $lang=null)
		{
				if ($declibre != null) return $this->getVars("SELECT * FROM $this->table WHERE declibre=\"$declibre\" AND lang=\"$lang\"");
		}

		function charger_id($id, $lang=1)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\" AND lang=\"$lang\"");
		}
}
?>
