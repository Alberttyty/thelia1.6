<?php        
/****************Mutualisation de Thélia****************/

$serveur=$_SERVER['SERVER_NAME'];
$serveur=str_replace('www.','',$serveur);
/******multilingue*****/
if(preg_match('/^([^.]+)\.([^.]+)\./',$serveur,$langue)){
  $langue=$langue[1];
  $langues=array('fr','de','nl','en','da','it','es');
  if (in_array($langue, $langues)) {
    $serveur=str_replace($langue.".","",$serveur);
  }
}
/*********************/
if (is_dir($chemin = __DIR__ .'/../sites/'.$serveur.'/')) {
  define('SITE_DIR',$chemin); 
  define('FICHIER_URL','/sites/'.$serveur.'/');
  if (is_dir(SITE_DIR.'/template/')) $reptpl='sites/'.$serveur.'/template/';
}
/*DEBOGUAGE*
if($_SERVER['REMOTE_ADDR'] == '176.188.99.221') {
	//phpinfo(INFO_MODULES);
}
*/
?>