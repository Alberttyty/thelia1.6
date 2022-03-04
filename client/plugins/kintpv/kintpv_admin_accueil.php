<?php
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
require_once(realpath(dirname(__FILE__)) . "/Kintpv.class.php");
autorisation("kintpv");

echo('<div class="kintpv_accueil">');
require_once(realpath(dirname(__FILE__)) . "/kintpv_formulaire.php");
echo('</div>');
?>
