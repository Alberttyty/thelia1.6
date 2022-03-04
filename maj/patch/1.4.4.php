<?php
require_once(__DIR__ . "/../../classes/Cnx.class.php");


$cnx = new Cnx();

$query = "CREATE TABLE `smtpconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serveur` varchar(255) NOT NULL,
  `port` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `secure` varchar(30) NOT NULL,
  `active` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1;";
$resul = mysql_query($query,$cnx->link);

$query = "CREATE TABLE `promoutil` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`promo` int( 11 ) NOT NULL ,
`commande` int( 11 ) NOT NULL ,
PRIMARY KEY ( `id` )
) AUTO_INCREMENT =1;";
$resul = mysql_query($query,$cnx->link);

$query = "delete from pays where id=195";
$resul = mysql_query($query,$cnx->link);

$query = "ALTER TABLE `pays` CHANGE `default` `defaut` INT( 11 ) NOT NULL";
$resul = mysql_query($query,$cnx->link);

$query = "ALTER TABLE `caracdispdesc` ADD `classement` INT NOT NULL";
$resul = mysql_query($query,$cnx->link);

$query = "ALTER TABLE `declidispdesc` ADD `classement` INT NOT NULL";
$resul = mysql_query($query,$cnx->link);

// Initialiser le classement des caracdispdesc et des declidispdesc
$query = "select * from declidispdesc cd left join declidisp c on c.id = cd.declidisp order by cd.lang, c.declinaison";
$resul = mysql_query($query, $cnx->link);

$currlang = '';
$currobj = '';

while ($resul && $row = mysql_fetch_object($resul))
{
	if ($row->lang != $currlang || $row->declinaison != $currobj)
	{
		$classement = 1;
		$currlang = $row->lang;
		$currobj = $row->declinaison;
	}

	mysql_query("update declidispdesc set classement = $classement where id=$row->id", $cnx->link);

	$classement++;
}

$query = "select * from caracdispdesc cd left join caracdisp c on c.id = cd.caracdisp order by cd.lang, c.caracteristique";
$resul = mysql_query($query, $cnx->link);

$currlang = '';
$currobj = '';

while ($resul && $row = mysql_fetch_object($resul))
{
	if ($row->lang != $currlang || $row->caracteristique != $currobj)
	{
		$classement = 1;
		$currlang = $row->lang;
		$currobj = $row->caracteristique;
	}

	mysql_query("update caracdispdesc set classement = $classement where id=$row->id", $cnx->link);

	$classement++;
}

$newfile = file_get_contents(__DIR__ . "/1.4.4/NewCnx.class.php");
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

$query_cnx = "update variable set valeur='144' where nom='version'";
$resul_cnx = mysql_query($query_cnx, $cnx->link);

?>