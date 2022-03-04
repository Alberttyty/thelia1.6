<?php

require_once(dirname(realpath(__FILE__)) . '/../../../classes/filtres/FiltreBase.class.php');

class Filtredatepassee extends FiltreBase{

	public function __construct()
	{
		parent::__construct("`\#FILTRE_datepassee\(([^\|]*)\|\|([^\)]*)\|\|([^\)]*)\|\|([^\)]+)\)`");
	}
	                                                              
  public function calcule($match)
	{
    $tab2=$match[4];
                  
    if(preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{2})/",$match[1],$date))
    {
    
      $horaire="";
      if(preg_match("/ ([0-9]{2})\:([0-9]{2}) /",$match[1],$heure)) $horaire=" ".$heure[1].":".$heure[2]; 
    
      if($match[2]==""||$match[2]=="0")
        $reference=strtotime($date[1]."-".$date[2]."-20".$date[3].$horaire);
      else
        $reference=strtotime($match[2],strtotime($date[1]."-".$date[2]."-20".$date[3].$horaire));
      
      if(time()>=$reference){
        $tab2=$match[3];
      }
       
    }
    return $tab2;
	}

}

?>