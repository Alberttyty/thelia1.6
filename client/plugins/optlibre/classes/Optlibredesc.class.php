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

class Optlibredesc extends Baseobj
{
  	public $id;
  	public $optlibre;
  	public $titre;
  	public $option;
  	public $lang;
  	public $table = "optlibredesc";
  	public $bddvars=array("id", "optlibre", "titre", "option", "lang");

  	function Optlibredesc()
    {
  		  $this->Baseobj();
  	}

  	function init()
    {
  		  $query_optlibredesc = "CREATE TABLE `optlibredesc` (
  					`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  					`optlibre` INT NOT NULL ,
  					`titre` TEXT NOT NULL ,
  					`option` TEXT NOT NULL ,
  					`lang` INT NOT NULL
  			);"
        ;
  		  $resul_optlibredesc = mysql_query($query_optlibredesc, $this->link);
  	}

    function charger($optlibre = null, $lang=null)
    {
        if ($optlibre != null) return $this->getVars("SELECT * FROM $this->table WHERE optlibre=\"$optlibre\" AND lang=\"$lang\"");
    }

  	function charger_id($id, $lang=1)
    {
  		  return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\" AND lang=\"$lang\"");
  	}
}
?>
