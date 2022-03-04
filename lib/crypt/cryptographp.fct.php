<?php

// -----------------------------------------------
// Cryptographp v1.4
// (c) 2006-2007 Sylvain BRISON
//
// www.cryptographp.com
// cryptographp@alphpa.com
//
// Licence CeCILL modifi�e
// => Voir fichier Licence_CeCILL_V2-fr.txt)
// -----------------------------------------------

if (session_id() == "") session_start();

$_SESSION['cryptdir'] = dirname($cryptinstall);

// Affiche le cryptogramme
function dsp_crypt($cfg=0,$reload=1,$echo=1)
{
    //echo('<pre>');print_r($_SESSION);exit();
    $html= '<span><img src="'.$_SESSION["cryptdir"].'/cryptographp.php?cfg='.$cfg./*'&amp;'.htmlspecialchars(SID).*/'" alt=\'\' class=\'captcha\'/>';

    if ($reload) $html.= '<a class="cryptogram_reload" href="#" onclick="$(this).closest(\'span\').find(\'img\').attr(\'src\',\''.$_SESSION["cryptdir"].'/cryptographp.php?cfg='.$cfg.'&amp;date=\'+Math.round(Math.random(0)*1000)+1);return false;"><i class="icon icon-recaptcha"></i></a>';

    $html.= '</span>';

    if ($echo==0) return $html;
    else echo $html;
}


 function chk_crypt($code) {
 // V�rifie si le code est correct
 include ($_SESSION['configfile']);
 $code = addslashes ($code);
 $code = str_replace(' ','',$code);  // supprime les espaces saisis par erreur.
 $code = ($difuplow?$code:strtoupper($code));
 switch (strtoupper($cryptsecure)) {
        case "MD5"  : $code = md5($code); break;
        case "SHA1" : $code = sha1($code); break;
        }
 if ($_SESSION['cryptcode'] and ($_SESSION['cryptcode'] == $code))
    {
    unset($_SESSION['cryptreload']);
    if ($cryptoneuse) unset($_SESSION['cryptcode']);
    return true;
    }
    else {
         $_SESSION['cryptreload']= true;
         return false;
         }
 }

?>
