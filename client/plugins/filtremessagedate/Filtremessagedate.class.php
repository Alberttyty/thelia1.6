<?php

include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");

class Filtremessagedate extends PluginsClassiques{

	function Filtremessagedate(){
		$this->PluginsClassiques();
	}

	function init(){
	}

	function destroy(){
	}

	function post(){
		global $res;
		//#FILTRE_messagedate(2011-12-25 00:00:01||2011-12-31 23:59:59||Message si dans les dates||Message si hors des dates)
		preg_match_all("`\#FILTRE_messagedate\(([^\|]*)\|\|([^\)]*)\|\|([^\)]*)\|\|([^\)]+)\)`", $res, $cut);
		$tab1 = "";
		$tab2 = "";
		for($i=0; $i<count($cut[2]); $i++){
			$tab1[$i] = "#FILTRE_messagedate(" . $cut[1][$i] . "||" . $cut[2][$i] . "||" . $cut[3][$i] . "||" . $cut[4][$i] . ")";
			$maintenant=time();
			$debut=strtotime($cut[1][$i]);
			$fin=strtotime($cut[2][$i]);
			if ($maintenant>=$debut && $maintenant<=$fin) {
				$tab2[$i] = $cut[3][$i]; 
			} else {
				$tab2[$i] = $cut[4][$i];
			}
		}
		$res= str_replace($tab1, $tab2, $res);
	}

}

?>