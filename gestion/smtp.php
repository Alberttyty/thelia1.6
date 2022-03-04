<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
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
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/
    require_once("pre.php");
    require_once("auth.php");
    
    if(! est_autorise("acces_configuration")) exit;

    if(isset($action)){
        switch($action){
            case "modifier":
                modification($serveur,$port,$username,$password,$secure,$active);
                break;
        }
    }

    function modification($serveur,$port,$username,$password,$secure,$active){
        $smtp  = new Smtpconfig();
        $smtp->charger(1);

        $smtp->serveur = $serveur;
        $smtp->port = $port;
        $smtp->username = $username;
        $smtp->password = $password;
        $smtp->secure = $secure;
        if($active == "on") $smtp->active = 1;
        else $smtp->active = 0;

        if($smtp->id != "") $smtp->maj();
        else $smtp->add();
        redirige("smtp.php");
    }

    $smtp = new Smtpconfig();
    $smtp->charger(1);


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
   <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="configuration.php" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="smtp.php" class="lien04"><?php echo trad('Configuration_mail', 'admin'); ?></a></p>

<!-- bloc déclinaisons / colonne gauche -->
<div id="bloc_description">
<div class="entete_liste_config">
	<div class="titre"><?php echo trad('CONFIGURATION_MAIL', 'admin'); ?></div>
	<div class="fonction_valider"><a href="javascript:document.getElementById('validation').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
</div>
<div class="bordure_bottom">
	<form method="post" action="smtp.php" id="validation">
		<input type="hidden" name="action" value="modifier" />
		<ul class="claire">
			<li style="width:200px;"><?php echo trad('Serveur', 'admin'); ?></li>
			<li style="width:360px; border-left:1px solid #96A8B5;"><input name="serveur" type="text" class="form" value="<?php echo  htmlspecialchars($smtp->serveur); ?>" size="50" /></li>
		</ul>
		<ul class="fonce">
			<li style="width:200px;"><?php echo trad('Port', 'admin'); ?></li>
			<li style="width:360px; border-left:1px solid #96A8B5;"><input name="port" type="text" class="form" value="<?php if($smtp->port == "") echo "25"; else echo $smtp->port ?>" size="50" /></li>
		</ul>
		<ul class="claire">
			<li style="width:200px;"><?php echo trad('Nom_utilisateur', 'admin'); ?></li>
			<li style="width:360px; border-left:1px solid #96A8B5;"><input name="username" type="text" class="form" value="<?php echo  htmlspecialchars($smtp->username); ?>" size="50" /></li>
		</ul>
		<ul class="fonce">
			<li style="width:200px;"><?php echo trad('Mdp', 'admin'); ?></li>
			<li style="width:360px; border-left:1px solid #96A8B5;"><input name="password" type="password" class="form" value="<?php echo  htmlspecialchars($smtp->password); ?>" size="50" /></li>
		</ul>
		<ul class="claire">
			<li style="width:200px;"><?php echo trad('Protocole_securise', 'admin'); ?></li>
			<li style="width:360px; border-left:1px solid #96A8B5;"><input name="secure" type="text" class="form" value="<?php echo $smtp->secure; ?>" size="50" /></li>
		</ul>
		<ul class="fonce">
			<li style="width:200px;"><?php echo trad('Actif', 'admin'); ?></li>
			<li style="width:360px; border-left:1px solid #96A8B5;"><input type="checkbox" class="form" name="active" <?php if($smtp->active) echo "checked";?> /></li>
		</ul>

   </form>

</div>
</div>
<!-- fin du bloc de description / colonne de gauche -->
</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>
