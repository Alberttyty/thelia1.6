<?php
        
        query_patch("ALTER TABLE  `venteprod` ADD  `parent` INT(11) NOT NULL DEFAULT '0'");

	$newfile = file_get_contents(__DIR__ . "/1.5.1/NewCnx.class.php");
	$cnxfile = file_get_contents(__DIR__ . "/../../classes/Cnx.class.php");

	preg_match('/public static \$host[^\"]*\"([^\"]*)\"/', $cnxfile, $res);
	$newfile = str_replace("votre_serveur",$res[1],$newfile);

	preg_match('/public static \$login_mysql[^\"]*\"([^\"]*)\"/', $cnxfile, $res);
	$newfile = str_replace("votre_login_mysql",$res[1],$newfile);

	preg_match('/public static \$password_mysql[^\"]*\"([^\"]*)\"/', $cnxfile, $res);
	$newfile = str_replace("votre_motdepasse_mysql",$res[1],$newfile);

	preg_match('/public static \$db[^\"_]*\"([^\"]*)\"/', $cnxfile, $res);
	$newfile = str_replace("bdd_sql",$res[1],$newfile);

	file_put_contents(__DIR__ . "/../../classes/Cnx.class.php",$newfile);
        
        query_patch("INSERT INTO variable (nom, valeur, protege, cache) VALUES ('verifstock', '0', '', '');");

	// changement de la table lang
        query_patch("ALTER TABLE  `lang` ADD  `code` VARCHAR( 2 ) NOT NULL");
        query_patch("ALTER TABLE  `lang` ADD  `url` VARCHAR( 255 ) NOT NULL");
        query_patch("ALTER TABLE  `lang` ADD  `defaut` SMALLINT NOT NULL");
        query_patch("ALTER TABLE  `lang` ADD INDEX (  `defaut` )");
        query_patch("UPDATE  `lang` SET  `code` =  'FR',`defaut` =  '1' WHERE  `id` =1;");
        query_patch("UPDATE  `lang` SET  `code` =  'EN' WHERE  `id` =2;");
        query_patch("UPDATE  `lang` SET  `code` =  'ES' WHERE  `id` =3;");
        query_patch("UPDATE  `lang` SET  `code` =  'IT' WHERE  `id` =4;");

	@unlink("../client/lang/admin/1.php");
	@unlink("../client/lang/admin/2.php");
	@unlink("../client/lang/admin/3.php");
	@unlink("../client/lang/admin/4.php");
	@rmdir("../client/lang/admin");
	@rmdir("../client/lang");

	// changement de l'urlsite

	$resul_cnx = query_patch("select * from variable where nom=\"urlsite\"");
	$urlsite = mysql_result($resul_cnx, 0, "valeur");

        query_patch("UPDATE  lang SET  url=\"" . rtrim($urlsite, "/") . "\" where id=1");
        query_patch("UPDATE  variable SET cache =  '1' WHERE  nom = \"urlsite\";");
        query_patch("update variable set valeur='151' where nom='version'");

?>