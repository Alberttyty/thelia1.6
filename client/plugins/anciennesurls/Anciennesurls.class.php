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
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Reecriture.class.php"); 
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php"); 
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/url.php");
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/lire.php");
	
	
	class Anciennesurls extends PluginsClassiques{
  
    var $table = "anciennesurls";

		var $bddvars=array("id", "url");

		function Anciennesurls(){
	
			$this->PluginsClassiques("anciennesurls");	
	
		}
		
		function init(){
		
      $this->ajout_desc("Anciennes URLs", "Redirection des anciennes URL vers la recherche", "", 1);
      
      $query = "CREATE TABLE IF NOT EXISTS `anciennesurls` (
								`id` BIGINT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY ,
								`url` TINYTEXT NOT NULL
							);";
			$resul = mysql_query($query, $this->link);		

		}

		function destroy(){
		
		}
    
    function predemarrage(){
    
      $url = lireParam('url','string');
       
      if($url!=""||$url!="/"){
                 
        $reecriture = new Reecriture();
        if (!$reecriture->charger($url)) {
            
          if($this->trouverAncienne($url)){
          
            $url=str_replace(Variable::lire('urlsite'),'',$url);
            $url = preg_replace('/^[0-9]+-/','',$url);          
            $suppr = array("-.html",".html","--","-");
            $remplace = array("","","+","+");
            $motclef = str_replace($suppr,$remplace,$url);
            
            //supprimer les mots en double
            preg_match_all('#([a-z0-9]+)\+#i',$motclef,$motclef_ok);
            $motclef_ok[1]=array_unique($motclef_ok[1]);
            $motclef=implode(' ',$motclef_ok[1]);
            
            if ($motclef) {
            
              $produit = new Produit();
              $produitdesc = new Produitdesc();
              
              $motclef = $produit->escape_string(strip_tags(trim($motclef)));
      
              $query = "
      				SELECT pd.produit FROM
      					$produitdesc->table pd
      				LEFT JOIN
      					$produit->table p ON p.id=pd.produit
      				WHERE
                pd.lang = " . ActionsLang::instance()->get_id_langue_courante() . "
              AND
      					p.ref='$motclef'
      				OR (
      					match(pd.titre, pd.chapo, pd.description, pd.postscriptum) AGAINST ('$motclef' IN BOOLEAN MODE)
      				OR
      					pd.titre REGEXP '[[:<:]]${motclef}[[:>:]]'
      				OR
      				  pd.chapo REGEXP '[[:<:]]${motclef}[[:>:]]'
      				OR
      				 	pd.description REGEXP '[[:<:]]${motclef}[[:>:]]'
      				OR
      				 	pd.postscriptum REGEXP '[[:<:]]${motclef}[[:>:]]'
          			)
              LIMIT 0,1
      			  ";
      
              $resul = CacheBase::getCache()->query($query);
              
              $url_redirect="";
      
              $produitdesc->charger($resul[0]->produit);
              $url_redirect=$produitdesc->getUrl();
              
              if ($url_redirect!=""){
                header("HTTP/1.1 301 Moved Permanently");
      					redirige(trim($url_redirect));
              }
        
            }
          
          }    
        
        }
        
      }
    
    }
    
    function trouverAncienne($url){
    
      $url=str_replace(Variable::lire('urlsite'),'',$url);
      $url=trim($url,'/');
    
      $query = "select id from $this->table where url=\"$url\"";
      $resul = mysql_query($query, $this->link);
  
      $nbres = mysql_numrows($resul);
      if(!$nbres) return false;
      
      return true;    
    
    }		
		
	}


?>
