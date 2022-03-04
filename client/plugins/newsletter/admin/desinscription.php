<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../../fonctions/authplugins.php");

	autorisation("newsletter");

	include_once(realpath(dirname(__FILE__)) . "/../Newsletter.class.php");
?>

	    <p><a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=newsletter" class="lien04">Newsletter</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">Gestion des d&eacute;sinscription</a></p>

<div id="bloc_description">
<div class="flottant">
<div class="entete_liste_config">
<form action="module.php" method="get" id="formsuppr">
	<input type="hidden" name="action_newsletter" value="desinscription" />
	<input type="hidden" name="nom" value="newsletter" />
	<div class="titre">DÉSINSCRIPTION</div>
	<div class="fonction_valider" style="margin-top:-6px;">
	
		<input type="text" name="email"  />
		<input type="submit" value="Désinscrire"/>

	</div>
</form>
</div>

	<ul class="Nav_bloc_description">
		<li style="height:25px; width:160px; border-left:1px solid #96A8B5;">Liste des e-mails</li>
	</ul>
	<div class="bordure_bottom">
<?php
	$desinscription = new Newsletter_desinscription();
	$query_desinc = "select * from $desinscription->table order by date desc";
	$resul_desinc = mysql_query($query_desinc, $desinscription->link);
	
	$i = 0;
	
	while($row_desinc = mysql_fetch_object($resul_desinc)){
	
		$i++;
		
		if($i%2)
			$fond = "claire";
		else
			$fond = "sombre";
?>
<ul class="<?php echo $fond; ?>">
	<li style="border-left:1px solid #C4CACE; width:520px"><?php echo $row_desinc->email; ?></li>
  <li style="border-left:1px solid #C4CACE;"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=newsletter&action_newsletter=desinscription_annulation&id=<?php echo $row_desinc->id; ?>" onclick="return(confirm('Etes-vous sur de vouloir réinscrire cette personne ?'));"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>
<?php
	}
?>
</div>
</div>
</div>