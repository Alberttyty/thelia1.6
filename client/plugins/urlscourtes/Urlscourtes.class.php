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
	
	
	class Urlscourtes extends PluginsClassiques{
		
		function Urlscourtes(){
    
			$this->PluginsClassiques("Urlscourtes");
      	
		}
    
    function predemarrage(){
    
      $url=$_SERVER["REQUEST_URI"];
      $url_parse=parse_url($url);
      
      //on supprime le "/"
      $url_parse['path']=preg_replace("/^\//","",$url_parse['path']);
      
      if (preg_match("/^[p|c|d|r]{1}[0-9]+$/",$url_parse['path'])){ 

        switch (substr($url_parse['path'],0,1)){
        
          // pour les produits
          case 'p':
          
            $id=substr($url_parse['path'],1);
            
            if(is_numeric($id)){
            
              $produit=new Produit();
              $produit->charger_id($id);
              $url_rewrite=rewrite_prod($produit->ref);
              
              if($url_rewrite!=""){
                header('Location: http://'.$_SERVER['SERVER_NAME'].'/'.$url_rewrite,true,301);
                exit();
              }
              
            }
          
          break;
        
        }
      
      }
      
  	} 
		
	}


?>
