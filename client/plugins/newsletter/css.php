<?php      
header("Content-type: text/css");

include_once(realpath(dirname(__FILE__)) . "/Newsletter.class.php");
  
$campagne = new Newsletter_campagne();

$campagne->charger_id($_REQUEST['id']);

echo $campagne->css;

exit();

?>
