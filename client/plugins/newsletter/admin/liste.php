<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../../fonctions/authplugins.php");

	autorisation("newsletter");

	include_once(realpath(dirname(__FILE__)) . "/../Newsletter.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../config.php");
	include_once(realpath(dirname(__FILE__)) . "/../classes/Newsletter_campagne.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../classes/Newsletter_mail.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../classes/Newsletter_liste.class.php");
?>

<p><a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=newsletter" class="lien04">Newsletter</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">Gestion des listes</a></p>

<div id="bloc_description">
<?php
	if(isset($_REQUEST['id'])){
		$listecourante = new Newsletter_liste();
		$listecourante->charger($_REQUEST['id']);
?>
<div class="flottant">
<div class="entete_liste_config">
		<div class="titre">LISTE > <?php echo $listecourante->nom; ?></div>
		<div class="fonction_valider"><a href="../client/plugins/newsletter/export_newsletter.php?id=<?php echo $_REQUEST['id']; ?>">Exporter la liste des mails</a></div>		
</div>
<table width="100%" cellpadding="5" cellspacing="0">
    
<form action="module.php?nom=newsletter#listemail" method="post">
	<input type="hidden" name="action_newsletter" value="ajouter_email" />
	<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
				<tr class="claire">
    				<th class="designation" style="width:250px;">Ajouter un email manuellement</th>
					<th><input type="text" name="email" size="25"/></th>
					<th><input type="submit" value="Ajouter" /></th>
				 </tr>
</form>
	
<form action="module.php?nom=newsletter#listemail" method="post">
	<input type="hidden" name="action_newsletter" value="importer_base" />
	<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
			<tr class="fonce">
				<td class="designation">Importer votre base de donnée newsletter</td>
				<td></td>
				<td><input type="submit" value="Importer"/></td>
			</tr>
</form>

<form action="module.php?nom=newsletter#listemail" method="post">
	<input type="hidden" name="action_newsletter" value="importer_clients" />
	<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
			<tr class="claire">
				<td class="designation">Importer votre base de donnée client</td>
				<td></td>
				<td><input type="submit" value="Importer"/></td>
			</tr>
</form>

<form action="module.php?nom=newsletter#listemail" method="post" enctype="multipart/form-data" >
			<input type="hidden" name="action_newsletter" value="importer_csv" />
			<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
				<tr class="fonce">
					<td  class="designation">Importer vos e-mail à partir d'un fichier CSV avec prenom,nom,e-mail</td>
					<td><input type="file" name="fichiercsv" /></td>
					<td><input type="submit" /></td>
				</tr>
		</ul>
	
	</form>
</table>
	

<?php

$messagesParPage=10; 
$retour_total=mysql_query("select count(*) as total  FROM newsletter_mail_liste where liste=" . $_REQUEST['id'] . " order by id DESC");
 

$donnees_total=mysql_fetch_assoc($retour_total); 
$total=$donnees_total['total']; 

$nombreDePages=ceil($total/$messagesParPage);

	if(isset($_GET['page'])) {
    	$pageActuelle=intval($_GET['page']);
     
     	if($pageActuelle>$nombreDePages) 
     	  	$pageActuelle=$nombreDePages;
	}
	else 
		$pageActuelle=1;

	$premiereEntree=($pageActuelle-1)*$messagesParPage; 


$retour_messages=mysql_query("select * FROM newsletter_mail_liste where liste=" . $_REQUEST['id'] . "  ORDER BY id DESC LIMIT ".$premiereEntree.", ".$messagesParPage); 
$nb_row = mysql_num_rows($retour_messages);
	if(mysql_num_rows($retour_messages))
	{
		while($row = mysql_fetch_object($retour_messages)) 
		{
			$mail = new Newsletter_mail();
			$mail->charger_id($row->email);
?>	

<?php
		}
	}
?>	

</div>

  &nbsp;
	<div class="entete_liste_config">
		<div class="titre">Liste des e-mail de "<?php echo $listecourante->nom; ?>"</div>
	</div>
	<ul class="Nav_bloc_description">
		<li style="height:25px; width:150px; border-left:1px solid #96A8B5;">Prénom</li>
		<li style="height:25px; width:150px; border-left:1px solid #96A8B5;">Nom</li>
		<li style="height:25px; width:150px; border-left:1px solid #96A8B5;">E-mail</li>
	</ul>
	
	<div class="bordure_bottom">
	

<?php
	
$messagesParPage=10; 
$retour_total=mysql_query("select count(*) as total  FROM newsletter_mail_liste where liste=" . $_REQUEST['id'] . " order by id DESC");
 

$donnees_total=mysql_fetch_assoc($retour_total); 
$total=$donnees_total['total']; 

$nombreDePages=ceil($total/$messagesParPage);

	if(isset($_GET['page'])) {
    	$pageActuelle=intval($_GET['page']);
     
     	if($pageActuelle>$nombreDePages) 
     	  	$pageActuelle=$nombreDePages;
	}
	else 
		$pageActuelle=1;

	$premiereEntree=($pageActuelle-1)*$messagesParPage; 


$retour_messages=mysql_query("select * FROM newsletter_mail_liste where liste=" . $_REQUEST['id'] . "  ORDER BY id DESC LIMIT ".$premiereEntree.", ".$messagesParPage); 
$nb_row = mysql_num_rows($retour_messages);
	if(mysql_num_rows($retour_messages))
	{
		while($row = mysql_fetch_object($retour_messages)) 
		{
			$mail = new Newsletter_mail();
			$mail->charger_id($row->email);
?>		
				<ul class="ligne_claire_BlocDescription">

						<li style="border-left:1px solid #C4CACE; width:152px; height:50px;">&nbsp;</li>
						<li style="border-left:1px solid #C4CACE; width:152px; height:50px;">&nbsp;</li>
						<li style="border-left:1px solid #C4CACE; width:202px; height:50px;"><?php echo $mail->email; ?></li>
						<li style="border-left:1px solid #C4CACE; width:50px; height:50px;">
								<a href="?nom=newsletter&action_newsletter=supprimer_email&id=<?php echo $_REQUEST['id']; ?>&email=<?php echo $row->email; ?>#listemail" onclick="return(confirm(\'Etes-vous sur de vouloir supprimer cette email ?\'));">
								<img src="gfx/b_drop.png" alt="supprimer" title="supprimer"/></a>
						</li>
				</ul>
	<?php	

		}
	} else {
	?>
		<ul class="ligne_claire_BlocDescription">
		    				<li>Aucun e-mail enregistr&eacute;</li>
					</ul>
	<?php
	}	

	?>
	
<p align="center">Page : 
<?php
for($i=1; $i<=$nombreDePages; $i++){
     if($i==$pageActuelle){
?>
     	<?php echo "[" . $i . " ] "; ?>
     	
<?php
    } else {
?>
	  <a href="module.php?nom=newsletter&action_newsletter=liste&id=<?php echo $_REQUEST['id']; ?>&page=<?php echo $i; ?>"><?php echo $i;  ?></a>
<?php
	}
}
?>

	</p>
		
	</div>
<?php
	}
?>
</div>

<div id="bloc_colonne_droite" style="float:right">
	<div class="entete_config">
		<div class="titre">Mes listes</div>
		<form action="module.php?nom=newsletter" method="post" enctype="multipart/form-data" style="margin-top:-6px;">
	  			<input type="hidden" name="action_newsletter" value="creer_liste" />
				<input type="text" name="nom_liste" value="Ajouter une liste" onblur="if(this.value == '') { this.value='Ajouter une liste'}" onfocus="if (this.value == 'Ajouter une liste') {this.value=''}" /><input type="submit" value="OK" />
		</form>
	</div>
	<ul class="ligne1">
		<li style="width:200px; padding-left:5px">Nom de la liste</li>
		<li style="border-left:1px solid #96A8B5; width:70px; padding-left:5px">Editer</li>
		<li style="border-left:1px solid #96A8B5; padding-left:5px">Supprimer</li>
	</ul>
	
	<?php
		
		$query = mysql_query("select * FROM newsletter_liste where actif=1");
		if(mysql_num_rows($query) > 0)
		{
			while($row = mysql_fetch_object($query)) 
			{
	?>	
	<ul class="claire">
				<li style="width:220px;"><?php echo $row->nom; ?></li>
				<li  style="width:50px;"><a href="?nom=newsletter&action_newsletter=editer_liste&id=<?php echo $row->id; ?>#listemail">
								<img src="gfx/b_edit.png" alt="editer" title="editer"/></a></li>
				<li style="text-align:right; width:50px;">
	  			  	<a href="?nom=newsletter&action_newsletter=supprimer_liste&id=<?php echo $row->id; ?>" onclick="return(confirm(\'Etes-vous sur de vouloir supprimer cette liste ?\'));">
								<img src="gfx/b_drop.png" alt="supprimer" title="supprimer"/></a>
				</li>
    		
	</ul>
	<?php

			}
		}
		else
		{
?>
				<ul class="ligne_claire_BlocDescription">
		    		<li style="width:150px">Aucune Liste enregistr&eacute;e</li>
				</ul>
<?php
		}	
		
	?>

</div>	
	

