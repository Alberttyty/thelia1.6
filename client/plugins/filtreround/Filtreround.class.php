<?php
require_once(dirname(realpath(__FILE__)) . '/../../../classes/filtres/FiltreBase.class.php');

class Filtreround extends FiltreBase {

	public function __construct() {
		parent::__construct("`\#FILTRE_round\(([0-9.]*)\)`");
	}
	
  	public function calcule($match)	{
		if (is_numeric($match[1])) {
			$tab2 = ceil($match[1]); 
		} else {
			$tab2 = $match[1];
		}
		return $tab2;
	}

}

?>