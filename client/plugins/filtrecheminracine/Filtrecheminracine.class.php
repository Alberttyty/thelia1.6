<?php

require_once(dirname(realpath(__FILE__)) . '/../../../classes/filtres/FiltreBase.class.php');

class Filtrecheminracine extends FiltreBase{

	public function __construct()
	{
    parent::__construct("`\#FILTRE_cheminracine\(([^\)]+)\)`");
	}
	
  public function calcule($match)
	{
    $tab2="";
    
    $tab2 = $_SERVER["DOCUMENT_ROOT"]."/".$match[1];
    
    return $tab2;
	}

}

?>