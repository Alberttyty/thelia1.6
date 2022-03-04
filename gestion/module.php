<?php
require_once("pre.php");
require_once("auth.php");
if(! est_autorise("acces_modules")) exit;
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once("title.php");?>
</head>

<body>
<div id="wrapper">
<div id="subwrapper">
<?php
    $menu="plugins";
    require_once("entete.php");


    try {
        require_once(ActionsAdminModules::instance()->trouver_fichier_admin($nom));
    } catch (Exception $e) {
        die($e->getMessage());
    }
?>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>
