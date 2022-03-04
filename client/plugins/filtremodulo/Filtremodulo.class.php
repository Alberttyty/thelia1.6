<?php

include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");

class Filtremodulo extends PluginsClassiques{

	function Filtremodulo(){
		$this->PluginsClassiques();
	}

	function init(){
	}

	function destroy(){
	}

	function post(){
		global $res;
				
		preg_match_all("`\#FILTRE_modulo\(([^\|]*)\|\|([^\)]*)\|\|([^\)]*)\)`", $res, $cut);

		$tab1 = "";
		$tab2 = "";

		for($i=0; $i<count($cut[2]); $i++){
				if(trim($cut[1][$i])%trim($cut[2][$i]) == 0){
						$tab1[$i] = "#FILTRE_modulo(" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . ")";
						$tab2[$i] = $cut[3][$i];
				}
		
				else{
					$tab1[$i] = "#FILTRE_modulo(" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . ")";
					$tab2[$i] = "";
				}

		}

		$res= str_replace($tab1, $tab2, $res);
		
	}

}
?>