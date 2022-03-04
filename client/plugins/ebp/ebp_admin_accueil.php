<?php
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php"); 
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
  include_once(realpath(dirname(__FILE__)) . "/Ebp.class.php");
	autorisation("ebp");
?>
<div class="ebp_accueil">
<?php
  include_once(realpath(dirname(__FILE__)) . "/ebp_formulaire.php");
?>
</div>