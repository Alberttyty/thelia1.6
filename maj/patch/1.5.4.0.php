<?php
query_patch("update variable set valeur='1540' where nom='version'");

// Ordonner les plugins
$r = mysql_query("SELECT id FROM modules order by id asc",$cnx->link);

$idx = 1;

while($r && $a = mysql_fetch_object($r))
{
	mysql_query("UPDATE modules set classement=$idx where id=$a->id",$cnx->link);

	$idx++;
}

// Ajouter les variables de qualité de vignettes
query_patch("insert into variable(nom, valeur, protege, cache) values('qualite_vignettes_png', '7', 1, 0)");
query_patch("insert into variable(nom, valeur, protege, cache) values('qualite_vignettes_jpeg', '75', 1, 0)");

//rajout de la colonne boutique dans la table pays permettant d'identifier le pays de la boutique afin de gérer correctement l'intracom
query_patch("ALTER TABLE `pays` ADD `boutique` TINYINT NOT NULL DEFAULT '0'");

//lors de la MAJ on assigne comme pays à la boutique, le pays par défaut.
$hdl = query_patch("select id from pays where defaut=1");
$pays_id = mysql_result($hdl, 0, "id");
if($pays_id === false) {
    $pays_id = 64;
}

query_patch("update pays set boutique=1 where id=".$pays_id);

$basedir = __DIR__ . "/../..";

if (file_exists($basedir . "/classes/Cnx.class.php.orig")) {
    unlink($basedir . "/classes/Cnx.class.php");
    rename($basedir . "/classes/Cnx.class.php.orig", $basedir . "/classes/Cnx.class.php");
}


?>
