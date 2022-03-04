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
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
	
	
	class Nombreproduits extends PluginsClassiques{

		function Nombreproduits(){
	
			$this->PluginsClassiques("nombreproduits");	
	
		}
		
		function init(){
		
      $this->ajout_desc("Nombre de produits", "Nombre de produits", "", 1);		

		}

		function destroy(){
		
		}		

		 function boucle($texte, $args){
     
  		$res="";
  		$stockmini = lireTag($args, "stockmini");
      $ligne = lireTag($args, "ligne");
      
      $search="";
    	$order="";
      $limit="";
  
      if($stockmini != "") $search .= " and stock>=\"$stockmini\"";
      if($ligne != "") $search .= " and ligne=\"$ligne\"";
      
      $produit=new Produit();
             
      $query = "select count(id) as nb from $produit->table where 1 $search $order $limit";
      $resul = mysql_query($query, $this->link);
  
      $nbres = mysql_numrows($resul);
      if(!$nbres) return "";
      
      while($row = mysql_fetch_object($resul)){
      
        $nb="";
        for($i=0;$i<strlen($row->nb);$i++){
          $nb.="<span>".substr($row->nb,$i,1)."</span>";
        }
        $temp = str_replace("#NB_TOTAL", $nb, $texte);
    	  $res .= $temp;
        
      }
      
      return $res;
      
    }
		
	}


?>
