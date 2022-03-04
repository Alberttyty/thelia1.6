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
     <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="configuration.php" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="#" class="lien04"><?php echo trad('Gestion_messages', 'admin'); ?></a>           
    </p>
<!-- bloc gestion des messages / colonne gauche -->  
<div id="bloc_description">

<div class="entete_liste_config">
	<div class="titre"><?php echo trad('LISTE_MESSAGES', 'admin'); ?></div>
</div>
<div class="bordure_bottom">
<?php
	$i=0;
	
	$message = new Message();
	$query = "select * from $message->table";
	$resul = mysql_query($query, $message->link);
	
	while($row = mysql_fetch_object($resul)){
		
		 $i++;
		
		if(!($i%2)) $fond="ligne_fonce_BlocDescription";
  		else $fond="ligne_claire_BlocDescription";

		$messagedesc = new Messagedesc();
		$messagedesc->charger($row->id, $_SESSION['util']->lang);
?>


  <ul class="<?php echo $fond; ?>">
    <li style="width:530px"><?php if($messagedesc->intitule != "") echo $messagedesc->intitule; else echo $row->nom; ?></li>
    <li style="border-left:1px solid #C4CACE;"><a href="message_modifier.php?nom=<?php echo $row->nom ?>"><?php echo trad('editer', 'admin'); ?></a></li>
  </ul>


<?php 

	}
?>
</div>
</div>
<!-- bloc du bloc de gestion des messages / colonne gauche -->  

</div>

<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>
