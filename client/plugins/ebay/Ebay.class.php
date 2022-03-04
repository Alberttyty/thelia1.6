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

include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
include_once(realpath(dirname(__FILE__)) . "/classes/Ebayproduits.class.php");

class Ebay extends PluginsClassiques{

  var $id;
  var $rubrique = 0;
  var $categorie = 0;
  
  const TABLE = "ebay";
  
  var $table = self::TABLE;

  var $bddvars = array("id", "rubrique", "categorie");
	
	function Ebay(){
		$this->PluginsClassiques("ebay");
  }
  
  function init(){
  
    $cnx = new Cnx();
    $query = "CREATE TABLE IF NOT EXISTS `" . self::TABLE . "` (
	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `rubrique` INT(11) NOT NULL,
	  `categorie` VARCHAR(255) NOT NULL,
	  PRIMARY KEY  (`id`)
  	) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    $this->query($query, $cnx->link);
    
    $ebayproduits=new Ebayproduits();  
    $query = "CREATE TABLE IF NOT EXISTS `" . $ebayproduits->table . "` (
	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `reference` VARCHAR(255) NOT NULL,
	  `itemid` VARCHAR(255) NOT NULL,
	  PRIMARY KEY  (`id`)
  	) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    $this->query($query, $cnx->link);
    
  	$var = new Variable();
		if(!$var->charger("ebay-conditionid")){
			$var->nom = "ebay-conditionid";
			$var->valeur = 1000;
			$var->add();
		}
    
    $var = new Variable();
		if(!$var->charger("ebay-location")){
			$var->nom = "ebay-location";
			$var->valeur = 0;
			$var->add();
		}
    
    $var = new Variable();
		if(!$var->charger("ebay-marque")){
			$var->nom = "ebay-marque";
			$var->valeur = 0;
			$var->add();
		}
    
    $var = new Variable();
		if(!$var->charger("ebay-shippingservice1")){
			$var->nom = "ebay-shippingservice1";
			$var->valeur = 0;
			$var->add();
		}
    
    $var = new Variable();
		if(!$var->charger("ebay-shippingservice1_cost")){
			$var->nom = "ebay-shippingservice1_cost";
			$var->valeur = 0;
			$var->add();
		}
    
    $var = new Variable();
		if(!$var->charger("ebay-intlshippingservice1")){
			$var->nom = "ebay-intlshippingservice1";
			$var->valeur = 0;
			$var->add();
		}
    
    $var = new Variable();
		if(!$var->charger("ebay-intlshippingservice1_cost")){
			$var->nom = "ebay-intlshippingservice1_cost";
			$var->valeur = 0;
			$var->add();
		}
    
	}     
  
  function charger_rubrique()
  {
    $requete = "select * from " . self::TABLE . " where rubrique=\"" . $this->rubrique . "\"";
    return $this->getVars($requete);
  } 
  
  function modrub($rubrique){
    
    $ebay = new Ebay(); 
    $ebay->rubrique=$rubrique->id;
    
    if($ebay->charger_rubrique()) $maj=true;
    else $maj=false;
    
    $ebay->categorie=$_REQUEST['ebay_categorie'];
    
    if($maj) $ebay->maj();
    else $ebay->add(); 
    
  }
  
  function ajouterItemID($reference,$itemid){
    $ebayproduits=new Ebayproduits();
    if($ebayproduits->charger_itemid($itemid))
    {
       $ebayproduits->reference=$reference;
       $ebayproduits->maj();
    }
    else{
       $ebayproduits->reference=$reference;
       $ebayproduits->itemid=$itemid;
       $ebayproduits->add();
    }  
  }    
  
  function cleanInput($texte){
  
    $texte=str_replace('"',"'",$texte);
    $texte=str_replace('
'," ",$texte);
    $texte=str_replace(',',";",$texte);
    $texte=str_replace('\n',"",$texte);
    $texte=str_replace('\r',"",$texte);
    $texte=str_replace('	',"",$texte);
    $texte=preg_replace("/\r\n+|\r+|\n+|\t+/i","",$texte);
    
    return $texte;
  
  } 
  
}

?>