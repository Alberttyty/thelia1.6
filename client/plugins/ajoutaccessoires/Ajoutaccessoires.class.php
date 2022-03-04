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
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
require_once(realpath(dirname(__FILE__)) . "/../declibre/Declibre.class.php");
	
class Ajoutaccessoires extends PluginsClassiques{

	function Ajoutaccessoires() {
		$this->PluginsClassiques("Ajoutaccessoires");	
	}
    
    function init() {
	  	$this->ajout_desc("Ajoutaccessoires", "Ajoutaccessoires", "", 1);
	}
    
    function action() {
    
      	foreach ($_REQUEST as $key => $valeur) {
			
        	if (preg_match('/accessoire_id_([0-9]+)/',$key,$retour)) {
				
          		$testprod = new Produit();
				
          		if($testprod->charger($valeur)) {
					
            		$quantite=$_REQUEST['accessoire_quantite_'.$retour[1]];
            		if (intval($quantite<=0)) $quantite = 1;
            		$_REQUEST['id_declibre_old']=$_REQUEST['id_declibre'];
            		unset($_REQUEST['id_declibre']);
            		$nouveau=0;
            
            		//si declinaison libre
            		if(isset($_REQUEST['accessoire_declibre_'.$retour[1]])){
  						if($_REQUEST['accessoire_declibre_'.$retour[1]] != ""){
                			$declibre=new Declibre();
                			if($declibre->testNouveauDansPanier($valeur,$_REQUEST['accessoire_declibre_'.$retour[1]])) $nouveau=1; 
                			$_REQUEST['id_declibre']=$_REQUEST['accessoire_declibre_'.$retour[1]];
  						}
            		}
            
            		$_SESSION['navig']->panier->ajouter($valeur,$quantite,array(),0,$nouveau,-1);
            		$_REQUEST['id_declibre']=$_REQUEST['id_declibre_old'];            
          		}
				
        	}
			
      	}
    
    }
	
}
?>