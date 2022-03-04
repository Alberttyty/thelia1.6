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

class Declibreintituledesc extends Baseobj
{
		public $id;
		public $declibreintitule;
		public $titre1;
    public $description1;
    public $titre2;
    public $description2;
		public $lang;

		public $table = "declibreintituledesc";

		public $bddvars=array("id", "declibreintitule", "titre1", "description1", "titre2", "description2", "lang");

		function Declibreintituledesc()
		{
				$this->Baseobj();
		}

		function init()
		{
				$query_declibreintituledesc = "CREATE TABLE `declibreintituledesc` (
						`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`declibreintitule` INT NOT NULL ,
						`titre1` TEXT NOT NULL ,
						`description1` TEXT NOT NULL ,
						`titre2` TEXT NOT NULL ,
						`description2` TEXT NOT NULL,
						`lang` INT NOT NULL
				);"
				;
				$resul_declibreintituledesc = mysql_query($query_declibreintituledesc, $this->link);
		}

    function charger($declibreintitule=null, $lang=null)
		{
				if ($declibreintitule != null) return $this->getVars("SELECT * FROM $this->table WHERE declibreintitule=\"$declibreintitule\" AND lang=\"$lang\"");
		}

		function charger_id($id, $lang=1)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\" AND lang=\"$lang\"");
		}
	}

?>
