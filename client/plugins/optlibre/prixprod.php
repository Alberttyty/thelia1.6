<?php
	include_once(realpath(dirname(__FILE__)) . "/Optlibre.class.php");	

if(isset($_GET['ref'])){
	
	if(isset($_GET['prix']) && $_GET['prix'] == "1")
		$prix = "prix";
	else
		if(isset($_GET['prix']) && $_GET['prix'] == "2")
			$prix = "prix2";
	
		$dec = new Optlibre();
		
		$query = "select min($prix) as prix from $dec->table where ref=\"" . $_GET['ref'] . "\" and $prix !=\"0\"";
		$resul = mysql_query($query, $dec->link);
		$row = mysql_fetch_object($resul);
		
		echo $row->prix;
}

?>