<?php
$basedir = __DIR__ . '/../..';
if(! defined('IN_UPDATE_THELIA_152')){
    //La mise à jour est supérieur à une 1.5.2
    define('IN_UPDATE_THELIA_1534', true);
    

    $cnxfilepath     = "$basedir/classes/Cnx.class.php";
    $cnxfileorigpath = "$basedir/classes/Cnx.class.php.orig";
    
    


    $filesWritable = array(
        $basedir . 'client/',
        $cnxfilepath,
        $cnxfileorigpath,
    );
    @clearstatcache();
    $err = null;
    foreach($filesWritable as $file){
        if(!is_writable($file)){
            $err .= '<span class="erreur">Le fichier '.str_replace($basedir, '', $file). ' n\'est pas accessible en &eacute;criture ou bien n\'existe pas.</span><br />';
        }
    }

    if (true === is_null($err)) {
        throw new Exception($err, 1);
    }



    if(is_file($cnxfileorigpath) ===  true){
        // Mise en place du Cnx.class.php définitif
        unlink($cnxfilepath);
        rename($cnxfileorigpath, $cnxfilepath);
    }
    else{
        throw new Exception('<span class="erreur">Le fichier '.str_replace($basedir, '', $cnxfileorigpath). ' n\'est pas accessible en &eacute;criture ou bien n\'existe pas.</span><br />', 1);
    }
    
    
}
@clearstatcache();
$facturepath = "$basedir/client/pdf/facture.php";
$factureorigpath = "$basedir/client.orig/pdf/facture.php";

$filesWritable = array(
    $basedir . 'client/',
    $facturepath,
    $factureorigpath,
);

$err = null;
foreach($filesWritable as $file){
    if(!is_writable($file)){
        $err .= '<span class="erreur">Le fichier '.str_replace($basedir, '', $file). ' n\'est pas accessible en &eacute;criture ou bien n\'existe pas.</span><br />';
    }
}

if (true === is_null($err)) {
    throw new Exception($err, 1);
}

unlink($facturepath);
copy($factureorigpath, $facturepath);

//CHANGEMENT DU NUMÉRO DE VERSION
$sanitize = query_patch("SELECT id FROM variable where nom='sanitize_admin'");

if(mysql_num_rows($sanitize) ){
    $sanitize_id = mysql_result($sanitize, 0, "id");
    query_patch("update variable set cache=1 where id=".$sanitize_id);
}

query_patch("update variable set valeur='1534' where nom='version'");

query_patch("INSERT INTO `variable` (`nom`, `valeur`, `protege`, `cache`) VALUES ('htmlpurifier_whiteList', 'www.youtube.com/embed/\nplayer.vimeo.com/video/\nmaps.google.*/', 1, 1);");


?>
