<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/parseur/Analyse.class.php");
include_once(realpath(dirname(__FILE__)) . "/lib/simple_html_dom.php");

class Templateexterne extends PluginsClassiques {
  
	var $cache_dir;
	var $cache_vie;
		
	function Templateexterne() {
		$this->PluginsClassiques();
		$cache_dir = SITE_DIR.'client/cache/externe/';
		$cache_vie = 3600;
	}     
    
    function init(){
		  $this->ajout_desc("Template externe", "Intégrer un template d'une autre URL", "", 1);
      if (!is_dir($this->cache_dir))
      {
      	if (mkdir($this->cache_dir,0755,true) === false)
      	{
      		die('Impossible de créer le répertoire '.$this->cache_dir.'. Vérifiez les droits d\'accès');
      	}
      }
    }
    
    function analyse(){
      global $res;
      
      /* On recherche #TEMPLATE_EXTERNE_HEADER{http://....} */
      if (preg_match_all('/#TEMPLATE_EXTERNE_([^{]+){([^}]+)}/', $res, $matches, PREG_SET_ORDER))
  		{
        
        /* Si on passe recalculer dans l'URL */
        $forcer_recalculer=false;
        if(isset($_REQUEST['var_mode'])){if($_REQUEST['var_mode']=="recalcul") $forcer_recalculer=true;}

  			foreach($matches as $match)
  			{
          $balise=strtolower($match[1]);
          $url=$match[2];
          
          $externe=$this->chargerExterneAvecCache($url,$balise,$forcer_recalculer);
          
          $res=str_replace($match[0],$externe,$res);
          
  		  }
      }
      
    }
    
    function chargerExterneAvecCache($url,$balise,$forcer_recalculer=false){
    
      $cache_externe = $this->cache_dir.$balise.'-'.md5($url).'.cache';
          
      $recalculer=true;
      /* Si le fichier de cache existe */
      if(file_exists($cache_externe)) {
        $recalculer=false;
        $filemtime = filemtime($cache_externe);
        /* Age du fichier en seconde supérieur à la durée de vie*/
        if((time()-filemtime($cache_externe))>$this->cache_vie) $recalculer=true;
      }
      if($forcer_recalculer) $recalculer=true;
      
      /* Si on recalcule */
      if($recalculer){
        if(file_exists($cache_externe)) unlink($cache_externe);
        $html = file_get_html($url);
        $externe="";
        foreach($html->find($balise) as $retour){
          $externe.=$this->urlAbsolue($retour->outertext,$url);  
        }
        file_put_contents($cache_externe,$externe);
      }
      /* Si on lit le cache */
      else {
        $externe=file_get_contents($cache_externe);
      } 
      
      return $externe;         
    
    }    
    
    function urlAbsolue($html,$url){
    
      if(substr($url,-1)!="/")$url=$url."/";
      /* On recherche tout les attributs href qui ne commence pas par http */
      $html=preg_replace('/(href|src)=([\'|"])(?!http(s)?:)([^\'|"]+)([\'|"])/i','$1=$2'.$url.'$4$5',$html);
    
      return $html;  
    
    }    
    
	}

?>
