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

class Ebayproduits extends Baseobj{

  var $id;
  var $reference = 0;
  var $itemid = 0;
  
  const TABLE = "ebay_produits";
  
  var $table = self::TABLE;

  var $bddvars = array("id", "reference", "itemid");
	
	function Ebayproduits(){
		$this->Baseobj();
  }  
  
  function charger_reference($reference)
  {
    $requete = "select * from " . self::TABLE . " where reference=\"" . $reference . "\" limit 0,1";
    return $this->getVars($requete);
  } 
  
  function charger_itemid($itemid)
  {
    $requete = "select * from " . self::TABLE . " where itemid=\"" . $itemid . "\" limit 0,1";
    return $this->getVars($requete);
  } 
  
  
}

?>