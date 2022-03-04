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
	if(!isset($action)) $action="";

	if(! isset($lang)) $lang="1";

	if(! est_autorise("acces_configuration")) exit;


	if($action == "modifier"){

		$message = new Message();
		$messagedesc = new Messagedesc();

 		$message->charger($nom);
		$messagedesc->charger_admin($message->id, $lang);

		$messagedesc->message = $message->id;
		$messagedesc->intitule = $_POST['intitule'];
		$messagedesc->titre = $_POST['titre'];
		$messagedesc->chapo = $_POST['chapo'];
		$messagedesc->description = $_POST['description'];
		// correction € pour le mail
		$messagedesc->description = str_replace("€", "&euro;", $messagedesc->description);
		$messagedesc->descriptiontext = $_POST['descriptiontext'];
		$messagedesc->lang = $lang;

		if($messagedesc->id)
 			$messagedesc->maj();

		else
			$messagedesc->add();

		redirige("message_modifier.php?nom=$nom&lang=$lang");


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

<?php

	$message = new Message();
	$message->charger($nom);

	$messagedesc = new Messagedesc();
	$messagedesc->charger($message->id, $lang);

?>

<div id="contenu_int">
   <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="configuration.php" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a> &nbsp;<img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="message.php" class="lien04"><?php echo trad('Gestion_messages', 'admin'); ?></a> &nbsp;<img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="#" class="lien04"><?php echo trad('Modifier', 'admin'); ?></a></p>

<!-- bloc déclinaisons / colonne gauche -->
<div id="bloc_description">
 <form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="formulaire" method="post">
    <input type="hidden" name="action" value="modifier" />
   <input type="hidden" name="lang" value="<?php echo $lang ?>" />
   <input type="hidden" name="nom" value="<?php echo($message->nom); ?>" />

<div class="entete_liste_config">
	<div class="titre"><?php echo trad('MODIFICATION_MESSAGE', 'admin'); ?></div>
	<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit();"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
</div>
 <!-- bloc descriptif de la déclinaison -->

<table width="100%" cellpadding="5" cellspacing="0">
    <tr class="claire">
        <th class="designation"><?php echo trad('Changer_langue', 'admin'); ?></th>
        <th>				<?php
								$langl = new Lang();
								$query = "select * from $langl->table";
								$resul = mysql_query($query);
								while($row = mysql_fetch_object($resul)){
									$langl->charger($row->id);
						    ?>

					  		 <div class="flag<?php if($lang ==  $langl->id) { ?>Selected<?php } ?>"><a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=<?php echo($nom); ?>&lang=<?php echo($langl->id); ?>"><img src="gfx/lang<?php echo($langl->id); ?>.gif" alt="-" /></a></div>

						  <?php } ?>

		</th>
   	</tr>
   	<tr class="fonce">
        <td class="designation"><?php echo trad('Nom_message', 'admin'); ?></td>
        <td><?php echo $message->nom; ?></td>
   	</tr>
   	<tr class="claire">
        <td class="designation"><?php echo trad('Intitule_message', 'admin'); ?></td>
        <td><input type="text" class="form_long" name="intitule" value="<?php echo  htmlspecialchars($messagedesc->intitule); ?>" /></td>
   	</tr>
   	<tr class="fonce">
        <td class="designation"><?php echo trad('Titre_message', 'admin'); ?></td>
        <td><input type="text" class="form_long" name="titre" value="<?php echo  htmlspecialchars($messagedesc->titre); ?>" /></td>
   	</tr>
   	<tr class="claire">
        <td class="designation"><?php echo trad('Chapo', 'admin'); ?><br /><span class="note"><?php echo trad('courte_descript_format_texte', 'admin'); ?></span></td>
        <td><textarea name="chapo" class="form_long" cols="40" rows="2"><?php echo $messagedesc->chapo; ?></textarea></td>
   	</tr>
   	<tr class="fonce">
        <td class="designation"><?php echo trad('Description', 'admin'); ?><br /><span class="note"><?php echo trad('format_html', 'admin'); ?></span></td>
        <td><textarea name="description" class="form" cols="53" rows="15"><?php echo $messagedesc->description; ?></textarea></td>
   	</tr>
   	<tr class="claire">
        <td class="designation">Description<br /><span class="note"><?php echo trad('format_text', 'admin'); ?></span></td>
        <td><textarea name="descriptiontext" class="form" cols="53" rows="15"><?php echo $messagedesc->descriptiontext; ?></textarea></td>
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
