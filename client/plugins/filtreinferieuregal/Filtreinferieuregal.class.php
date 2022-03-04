<?php

require_once(dirname(realpath(__FILE__)) . '/../../../classes/filtres/FiltreBase.class.php');

class Filtreinferieuregal extends FiltreBase{

	public function __construct()
	{
		parent::__construct("`\#FILTRE_inferieuregal\(([^\|]*)\|\|([^\)]*)\|\|([^\)]+)\)`");
	}
	
  public function calcule($match)
	{
    $tab2="";
    if (is_numeric($match[1]) && is_numeric($match[2])) {
		if ($match[1]<=$match[2]) $tab2=$match[3];
    }
    return $tab2;
	}

}

?>