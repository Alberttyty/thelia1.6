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
?>
<?php if(! est_autorise("acces_configuration")) exit; ?>
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
     <p><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a>
    </p>
<div id="bloc_informations">
	<ul>
	<li class="entete_configuration"><?php echo trad('GESTION_CATALOGUE_PRODUIT', 'admin'); ?></li>
	<li class="lignetop" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion_caracteristiques', 'admin'); ?></li>
	<li class="lignetop" style="width:72px;"><a href="caracteristique.php"><?php echo trad('editer', 'admin'); ?></a></li>
	<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion_declinaison', 'admin'); ?></li>
	<li class="fonce" style="width:72px;"><a href="declinaison.php"><?php echo trad('editer', 'admin'); ?></a></li>
	<li class="claire" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion_messages', 'admin'); ?></li>
	<li class="claire" style="width:72px;"><a href="message.php"><?php echo trad('editer', 'admin'); ?></a></li>
	<li class="lignebottomfonce" style="width:222px; background-color:#9eb0be;"><?php echo trad('Gestion_devises', 'admin'); ?></li>
	<li class="lignebottomfonce" style="width:72px;"><a href="devise.php"><?php echo trad('editer', 'admin'); ?></a></li>

	</ul>
	<ul>
	<li class="entete_configuration"><?php echo trad('GESTION_TRANSPORTS_LIVRAISONS', 'admin'); ?></li>
	<li class="lignetop" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion des pays', 'admin'); ?></li>
	<li class="lignetop" style="width:72px;"><a href="pays.php"><?php echo trad('editer', 'admin'); ?></a></li>
	<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion_transport', 'admin'); ?></li>
	<li class="fonce" style="width:72px;"><a href="transport.php"><?php echo trad('editer', 'admin'); ?></a></li>
	<li class="lignebottomclaire" style="width:222px; background-color:#9eb0be;"><?php echo trad('Gestion_zones_livraison', 'admin'); ?></li>
	<li class="lignebottomclaire" style="width:72px;"><a href="zone.php"><?php echo trad('editer', 'admin'); ?></a></li>
	</ul>
	<ul>
	<li class="entete_configuration"><?php echo trad('PARAMETRES_SYSTEME', 'admin'); ?></li>
	<li class="lignetop" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Activation_plugins', 'admin'); ?></li>
	<li class="lignetop" style="width:72px;"><a href="plugins.php"><?php echo trad('editer', 'admin'); ?> </a></li>
	<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion_variables', 'admin'); ?></li>
	<li class="fonce" style="width:72px;"><a href="variable.php"><?php echo trad('editer', 'admin'); ?> </a></li>
	<li class="claire" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion_administrateurs', 'admin'); ?></li>
	<li class="claire" style="width:72px;"><a href="gestadm.php"><?php echo trad('editer', 'admin'); ?></a></li>
	<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion_cache', 'admin'); ?></li>
	<li class="fonce" style="width:72px;"><a href="cache.php"><?php echo trad('editer', 'admin'); ?></a></li>
	<li class="claire" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion_log', 'admin'); ?></li>
	<li class="claire" style="width:72px;"><a href="logs.php"><?php echo trad('editer', 'admin'); ?></a></li>
	<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion_droit', 'admin'); ?></li>
	<li class="fonce" style="width:72px;"><a href="droits.php"><?php echo trad('editer', 'admin'); ?></a></li>
	<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion_htmlpurifier', 'admin'); ?></li>
	<li class="fonce" style="width:72px;"><a href="htmlpurifier.php"><?php echo trad('editer', 'admin'); ?></a></li>
	<li class="claire" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Gestion_mail', 'admin'); ?></li>
	<li class="claire" style="width:72px;"><a href="smtp.php"><?php echo trad('editer', 'admin'); ?></a></li>
	<li class="lignebottomfonce" style="width:222px; background-color:#9eb0be;"><?php echo trad('Gestion_langue', 'admin'); ?></li>
	<li class="lignebottomfonce" style="width:72px;"><a href="langue.php"><?php echo trad('editer', 'admin'); ?></a></li>
	</ul>
</div>
</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>