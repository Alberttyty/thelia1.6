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
/*include_once(realpath(dirname(__FILE__)) . "/classes/Amazoneproduits.class.php");*/

class Amazone extends PluginsClassiques{

  var $id;
  var $rubrique = 0;
  var $categorie = 0;
  
  const TABLE = "amazone";
  
  var $table = self::TABLE;

  var $bddvars = array("id", "rubrique", "categorie");
	
	function Amazone(){
		$this->PluginsClassiques("amazone");
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
    
    /*
    $amazoneproduits=new Ebayproduits();  
    $query = "CREATE TABLE IF NOT EXISTS `" . $amazoneproduits->table . "` (
	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `reference` VARCHAR(255) NOT NULL,
	  `recommended_browse_nodes` VARCHAR(255) NOT NULL,
    `department_name` VARCHAR(255) NOT NULL,
    `color_map` VARCHAR(255) NOT NULL,
	  PRIMARY KEY  (`id`)
  	) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    $this->query($query, $cnx->link);
    */
    
  	$var = new Variable();
		if(!$var->charger("amazone-product-id-type")){
			$var->nom = "amazone-product-id-type";
			$var->valeur = 0;
			$var->add();
		}
    
    $var = new Variable();
		if(!$var->charger("amazone-item-condition")){
			$var->nom = "amazone-item-condition";
			$var->valeur = 0;
			$var->add();
		}
    
    $var = new Variable();
		if(!$var->charger("amazone-will-ship-internationally")){
			$var->nom = "amazone-will-ship-internationally";
			$var->valeur = 0;
			$var->add();
		}
    
    $var = new Variable();
		if(!$var->charger("amazone-expedited-shipping")){
			$var->nom = "amazone-expedited-shipping";
			$var->valeur = 0;
			$var->add();
		}
    
    $var = new Variable();
		if(!$var->charger("amazone-brand-name")){
			$var->nom = "amazone-brand-name";
			$var->valeur = 0;
			$var->add();
		}
    
    $var = new Variable();
		if(!$var->charger("amazone-recommended-browse-nodes")){
			$var->nom = "amazone-recommended-browse-nodes";
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
    
    $amazone = new Amazone(); 
    $amazone->rubrique=$rubrique->id;
    
    if($amazone->charger_rubrique()) $maj=true;
    else $maj=false;
    
    $amazone->categorie=$_REQUEST['amazone_categorie'];
    
    if($maj) $amazone->maj();
    else $amazone->add(); 
    
  }  
  
  /*function modprod($produit){
    
    $amazone = new Amazoneproduits(); 
    $amazone->reference=$produit->ref;
    
    if($amazone->charger_reference($amazone->reference)) $maj=true;
    else $maj=false;
    
    $amazone->recommended_browse_nodes=$_REQUEST['amazone_recommended_browse_nodes'];
    $amazone->department_name=$_REQUEST['amazone_department_name'];
    $amazone->color_map=$_REQUEST['amazone_color_map'];
    
    if($maj) $amazone->maj();
    else $amazone->add(); 
    
  }*/
  
  function cleanInput($texte){
  
    $texte=str_replace('"',"'",$texte);
    $texte=str_replace('
'," ",$texte);
    $texte=str_replace(',',";",$texte);
    $texte=str_replace('\n',"",$texte);
    $texte=str_replace('\r',"",$texte);
    $texte=str_replace('	',"",$texte);
    $texte=preg_replace("/\r\n+|\r+|\n+|\t+/i","",$texte);
    $texte=strip_tags($texte);
    $texte=html_entity_decode($texte,ENT_QUOTES,'ISO-8859-1');
    
    return $texte;
  
  }  
  
  function getCarac($nom){
  
    $nom=str_replace("Amazon ","",$nom);
  
    $resul = $this->query("select caracval.caracdisp,caracval.valeur from caracteristiquedesc,caracval where caracval.caracteristique=caracteristiquedesc.caracteristique and caracteristiquedesc.titre like \"%".$nom."\" and caracteristiquedesc.lang=1 limit 0,1");
    $row = $this->fetch_object($resul);
    
    if($row->caracdisp!=0){
      $resul = $this->query("select titre from caracdispdesc where caracdisp=".$row->caracdisp." and lang=1 limit 0,1");
      $row = $this->fetch_object($resul);
      return $row->titre;
    }
    else {
      return $row->valeur;
    }
    
    return "";
  
  }      
  
}

?>