<?php
	require_once(__DIR__ . "/../../classes/Cnx.class.php");

	$cnx = new Cnx();
	
	$query_cnx = "UPDATE variable set protege=1, cache=1,valeur='http://blog.thelia.fr/rss.php' where nom='rssadmin'";
	$resul_cnx = mysql_query($query_cnx,$cnx->link);
	
	
	$var = "<Files *>
 			  <limit GET POST>
 			  order deny,allow
 			  deny from all
 			  </Limit>
			</Files>";
			
	file_put_contents("../client/plugins/atos/conf/.htaccess", $var);
			
	$query_cnx = "update variable set valeur='139' where nom='version'";
	$resul_cnx = mysql_query($query_cnx, $cnx->link);

	
?>