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

	class Personnalisation extends PluginsClassiques{

		
		function Personnalisation(){
			$this->PluginsClassiques("personnalisation");
		}     
        
    function demarrage(){
    
  		global $nouveau;
  		//si personnalisation, ajout en tant que nouveau produit dans le panier
  		if(is_array($_REQUEST['personnalisation'])){
			foreach ($_REQUEST['personnalisation'] as $key => $value){
			if($value!="")$nouveau=1;
			}
			}

    }

	
		function boucle($texte, $args){
			
			$article =  lireTag($args, "article");
			if($article == "" || $_SESSION['session_personnalisation'][$article] == "")
			return "";
				
			$res="";
			
      $temp = str_replace("#PERSONNALISATION", $_SESSION['session_personnalisation'][$article], $texte);
			$temp = str_replace("#ARTICLE", $article, $temp);
			$res .= $temp;
			
     return $res;

		}
		
		function predemarrage(){
      if(!is_array($_SESSION['session_personnalisation']))
      $_SESSION['session_personnalisation']=array();
    }
		
		function action(){
			
			global $nouveau;

			if($_REQUEST['action'] == "ajouter" && $nouveau==1){

				$nb = $_SESSION['navig']->panier->nbart;
	
				if($_REQUEST['personnalisation'] != ""){
							
						$nb = $_SESSION['navig']->panier->nbart;

						if(is_array($_REQUEST['personnalisation'])){
						    
						    $personnalisation="";
						    $i=0;
						    foreach ($_REQUEST['personnalisation'] as $key => $value){
  						    if($value!=""){
    						    if($i!=0)$separation=" - ";
    						    else $separation=" ";
    						    $i++;
    						    if(!is_numeric($key)){
                    $nom=$key." : ";
                    }
                    else {
                    $nom="";
                    }
    						    $personnalisation=$personnalisation."".$separation."".$nom.$value;
  						    }
						    }

  							$_SESSION['session_personnalisation'][$nb-1] = $personnalisation;
  						
						}	
				
				}	
		
			} else if($_REQUEST['action'] == "supprimer" && $_REQUEST['article'] != ""){

				for($i=$_REQUEST['article']; $i <= $_SESSION['navig']->panier->nbart; $i++){
					  if(isset($_SESSION['session_personnalisation'][$i+1])){
						$_SESSION['session_personnalisation'][$i] = $_SESSION['session_personnalisation'][$i+1];
						unset($_SESSION['session_personnalisation'][$i+1]);
						}
						else {
			      unset($_SESSION['session_personnalisation'][$i]);
            }
					}
					
					$_SESSION['session_personnalisation']=array_values($_SESSION['session_personnalisation']);
					
				}
    }
		
		function apres(){
		if($reset){
            $_SESSION['session_personnalisation'] = array();
		}
		}
	
		
		function aprescommande($commande){
	
			for($i=0; $i< $_SESSION['navig']->panier->nbart; $i++)
			{
				if(isset($_SESSION['session_personnalisation'][$i])){
				
				    $personnalisation=$_SESSION['session_personnalisation'][$i];
  											
  					$venteprod = new Venteprod();
  					$query = "select * from $venteprod->table where commande=\"" .$commande->id . "\" order by id limit $i,1";
  					$resul = mysql_query($query, $venteprod->link);		
  					$row = mysql_fetch_object($resul);
  					
  					$tmp = new Venteprod();
  					$tmp->charger($row->id);
  					if($personnalisation!=""){
            $tmp->titre .= " - Personnalisation : " . $personnalisation;
            $tmp->maj();
            }
  					
  					//$_SESSION['session_personnalisation'][$i]="";
					
				}
				
      }	
		
		}
		

	}

?>
