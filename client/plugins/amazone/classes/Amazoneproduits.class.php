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
?>
<?php

include_once(realpath(dirname(__FILE__)) . "/../../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../../classes/Variable.class.php");

class Amazoneproduits extends Baseobj{

  var $id;
  var $reference = 0;
  var $recommended_browse_nodes = 0;
  var $department_name = 0;
  var $color_map = 0;
  
  const TABLE = "amazone_produits";
  
  var $table = self::TABLE;

  var $bddvars = array("id", "reference", "recommended_browse_nodes", "department_name", "color_map");
	
	function Amazoneproduits(){
		$this->Baseobj();
  }  
  
  function charger_reference($reference)
  {
    $requete = "select * from " . self::TABLE . " where reference=\"" . $reference . "\" limit 0,1";
    return $this->getVars($requete);
  } 
  
  function charger_recommended_browse_nodes($itemid)
  {
    $requete = "select * from " . self::TABLE . " where recommended_browse_nodes=\"" . $itemid . "\" limit 0,1";
    return $this->getVars($requete);
  } 
  
  
}

?>