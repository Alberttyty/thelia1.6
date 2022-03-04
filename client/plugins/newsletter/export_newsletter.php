<?php

	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");

	autorisation("newsletter");

 	include_once(realpath(dirname(__FILE__)) . "/Newsletter.class.php");
	header("Content-Type: application/csv-tab-delimited-table");
	header("Content-disposition: filename=export" . ".csv");
	
?>
<?php
        $liste = new Newsletter_mail_liste();
        $query = "select * from $liste->table where liste=" . $_REQUEST['id'];
        $resul = mysql_query($query, $liste->link);

	 	while($row = mysql_fetch_object($resul)){
      		$mail = new Newsletter_mail();
      		$mail->charger_id($row->email);
      		echo $mail->email . "\n";
    	}
      	

?>
