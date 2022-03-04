<?php
require_once(dirname(realpath(__FILE__)) . '/../../../classes/filtres/FiltreBase.class.php');

class Filtreformatnombre extends FiltreBase {

	public function __construct() {
		parent::__construct("`\#FILTRE_formatnombre\(([^\|]*)\|\|([^\)]*)\|\|([^\)]+)\|\|([^\)]+)\)`");
	}
	
  	public function calcule($match)	{
		$tab2="";
		if (is_numeric($match[1]) && is_numeric($match[2])) {
			$tab2 = number_format($match[1], $match[2], $match[3], $match[4]);
			//$tab2 = str_replace($match[3]."00", "", $tab2);
			$tab2 = "<span class=\"nombre\">".str_replace($match[3], "</span><span class=\"virgule\">".$match[3], $tab2)."</span>";  
		} else {
			$tab2 = $match[1];
		}
		return $tab2;
	}

}

?>