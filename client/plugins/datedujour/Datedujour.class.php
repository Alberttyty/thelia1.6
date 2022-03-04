<?php

include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");

class Datedujour extends PluginsClassiques {
    function action() {
	      global $res;
	      $res = str_replace("#DATE_DU_JOUR", date("Y-m-d"), $res);
				$res = str_replace("#MOIS_DU_JOUR", date("m"), $res);
				$res = str_replace("#ANNEE_DU_JOUR", date("Y"), $res);
    }
}

?>
