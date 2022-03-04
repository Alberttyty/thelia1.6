<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*		email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/
require_once("pre.php");
require_once("auth.php");

if(! est_autorise("acces_configuration")) exit; 

if(!isset($lang)) $lang=$_SESSION["util"]->lang;


class HtmlPurifierAdmin extends Variable {
        
    public function updateConfig(){
        
        if('' != $value = lireParam('white_list')){
            $this->updateParam('htmlpurifier_whiteList', $value);
            
            unset($_REQUEST['white_list']);
        }
        foreach($_REQUEST as $key => $value){
            if (! preg_match('/^sanitize/', $key)) continue;
            
            self::ecrire($key, $value);
        }
        
        redirige($_SERVER['PHP_SELF']);
    }
    
    protected function updateParam($key, $value){
        self::ecrire($key, $value, true, 1, 1);
    }
    
    
    public function make_yes_no_radio($var_name)
    {
        $val = Variable::lire($var_name);

        echo '<input type="radio" name="'.$var_name.'" value="1"'.($val == 1 ? ' checked="checked"':'').'>' . trad('Oui', 'admin') . '
              <input type="radio" name="'.$var_name.'" value="0"'.($val == 0 ? ' checked="checked"':'').'>' . trad('Non', 'admin');
    }
        
}

$htmlPurifierAdmin = new HtmlPurifierAdmin();

if(isset($htmlpurifier_maj)){
    $htmlPurifierAdmin->updateConfig();
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");?>
</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php
	$menu="configuration";
	require_once("entete.php");
?>

<div id="contenu_int">
   <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a>  <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="configuration.php" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="htmlpurifier.php" class="lien04"><?php echo trad('Gestion_htmlpurifier', 'admin'); ?></a></p>

   <div id="bloc_description">
    <div class="entete">
        <div class="titre"><?php echo strtoupper(trad('Gestion_htmlpurifier', 'admin')); ?></div>
     <div class="fonction_valider"><a href="#" onclick="document.getElementById('htmlpurifier_form').submit(); return false;"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
    </div>
       <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" id="htmlpurifier_form">
           <input type="hidden" name="htmlpurifier_maj" value="oui">
           <table width="100%" cellpadding="5" cellspacing="0">
               <tr class="fonce">
                <td width="70%" class="designation"><?php echo trad('sanitize_active', 'admin'); ?><div style="font-size: 0.8em"><?php echo trad('sanitize_active_desc', 'admin'); ?></div></td>
                <td><?php $htmlPurifierAdmin->make_yes_no_radio('sanitize_admin'); ?></td>
               </tr>
               <tr class="claire">
                <td width="70%" class="designation"><?php echo trad('liste_url', 'admin'); ?><div style="font-size: 0.8em"><?php echo trad('liste_url_desc', 'admin'); ?></div></td>
                <td><textarea name="white_list" style="width: 300px; height: 100px;"><?php echo Variable::lire('htmlpurifier_whiteList'); ?></textarea></td>
               </tr>
           </table>
       </form>
   </div>
</div>




<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>