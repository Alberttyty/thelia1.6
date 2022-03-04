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
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracval.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdispdesc.class.php");
	
	class Caracpanier extends PluginsClassiques{
  
    var $caracteristiques;

		function Caracpanier(){
	
			$this->PluginsClassiques();	
      $caracteristiques = new Variable();
      $caracteristiques->charger("caracpanier");
      $this->caracteristiques = $caracteristiques->valeur;
	
		}
    
    function init(){
    
    	$caracteristiques = new Variable();
			if(!$caracteristiques->charger("caracpanier")){
				$caracteristiques->nom = "caracpanier";
				$caracteristiques->valeur = 0;
				$caracteristiques->add();
			}
    
		}
		
		function ajouterPanier($indiceAjoute){
    
      $titre=$_SESSION['navig']->panier->tabarticle[$indiceAjoute]->produitdesc->titre;
      $produit=$_SESSION['navig']->panier->tabarticle[$indiceAjoute]->produit->id;
      
      $caracteritiques=explode(";",$this->caracteristiques);
      
      foreach($caracteritiques as $k => $v){
        $caracval=new Caracval();
        $caracval->charger($produit,$v);
        $caracdispdesc=new Caracdispdesc();
        $caracdispdesc->charger_caracdisp($caracval->caracdisp);
        if($caracdispdesc->titre!=""){
          $titre=str_replace(" (".$caracdispdesc->titre,"",$titre);
          $titre.=" (".$caracdispdesc->titre.")";
        }
      }
    
  		$_SESSION['navig']->panier->tabarticle[$indiceAjoute]->produitdesc->titre=$titre; 
      
    }

		
	}


?>
