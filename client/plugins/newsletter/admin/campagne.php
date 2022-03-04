<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../../fonctions/authplugins.php");

	autorisation("newsletter");

	include_once(realpath(dirname(__FILE__)) . "/../Newsletter.class.php");
  
  $email_test = new Variable('emailcontact');
  
?>

<p><a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=newsletter" class="lien04">Newsletter</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">Gestion des campagnes</a></p>

<?php
  
if($_REQUEST['action_newsletter'] == "envoyer")
{
  $newsletter = new Newsletter_campagne();
	$newsletter->charger_id($_REQUEST['id']);
  $envoi= new Newsletter_envoi();
  $nb_envoye=count($envoi->charger_emails($newsletter->id,1));
  $nb_total=count($envoi->charger_emails($newsletter->id));
  if($newsletter->statut!=2)
  {
  ?>
  <div style="text-align:center;background:#868F99;color:#FFFFFF;font-weight:bold;margin:30px auto 30px auto;width:300px;padding:50px 50px 50px 50px;">
  Envoi en cours...<br/>
  Ne pas fermer le navigateur.<br/>
  Mail(s) envoyé(s) : <?php echo $nb_envoye+$mailpenv; ?> sur <?php echo $nb_total; ?>
  </div>
  <?php
  }
}  
  
?>

<div class="entete_liste_config">
	<div class="titre">MES CAMPAGNES</div>
	<div class="fonction_ajout">
	<a href="?nom=newsletter&amp;action_newsletter=campagne_editer">CRÉER UNE NOUVELLE CAMPAGNE</a>
	</div>
</div>
<ul class="Nav_bloc_description">
		<li style="height:25px; width:390px; border-left:1px solid #96A8B5;">Nom de la campagne</li>
		<li style="height:25px; width:64px; border-left:1px solid #96A8B5;">&nbsp;</li>
		<li style="height:25px; width:87px; border-left:1px solid #96A8B5;">Statut</li>
		<li style="height:25px;  border-left:1px solid #96A8B5;">Action</li>
	</ul>
	
	<div class="bordure_bottom">
<?php

	$urlsite = new Variable();
	$urlsite->charger("urlsite");

?>
<?php
	$campagne = new Newsletter_campagne();

	$query_campagne = "select * from $campagne->table order by date desc";
	$resul_campagne = mysql_query($query_campagne, $campagne->link);

	$i = 0;


	while($row_campagne = mysql_fetch_object($resul_campagne)){

		$i++;
		
		if($i%2)
			$fond = "ligne_claire_rub";
		else
			$fond = "ligne_fonce_rub";
?>
<ul class="<?php echo $fond; ?>">
	<li style="border-left:1px solid #C4CACE; width:383px">Campagne N° <?php echo $row_campagne->id; ?> / <?php echo substr($row_campagne->titre, 0, 50); ?></li>
<?php
	if($row_campagne->statut==0){
?>
	<li style="border-left:1px solid #C4CACE; width:57px">
		<a href="module.php?nom=newsletter&action_newsletter=campagne_editer&id=<?php echo $row_campagne->id; ?>">éditer</a>
	</li>
<?php
	} else {
?>
	<li style="border-left:1px solid #C4CACE; width:57px">
		<a href="module.php?nom=newsletter&action_newsletter=dupliquer&id=<?php echo $row_campagne->id; ?>" title="Envoyer à une autre liste">dupliquer</a>
  </li>
<?php
	}
?>
    <li style="border-left:1px solid #C4CACE; width:80px">
		<?php 
  		if($row_campagne->statut == 0){
  	?>
 		En attente
  	<?php
  	} if($row_campagne->statut == 1) {
  	?>
    En cours
    <?php
  	} if($row_campagne->statut == 2) {
  	?>
    Envoyée
    <?php
  	}
  	?>
    </li>
    
    <li style="width:165px;border-left:1px solid #C4CACE;">
    <form action="module.php" method="GET" id="formtester<?php echo $row_campagne->id; ?>">	
        				<input type="hidden" name="nom" value="newsletter"/>
        				<input type="hidden" name="action_newsletter" value="tester"/>
        				<input type="hidden" name="id" value="<?php echo $row_campagne->id; ?>"/>
        				<input type="text" size="14" name="email" value="<?php echo $email_test->valeur; ?>">
        				<a href="#" onclick="document.getElementById('formtester<?php echo $row_campagne->id; ?>').submit()">Tester</a>
    		    
    </form> 
    </li>
	
	<?php

   		if($row_campagne->statut==0){ 
  ?>
  <li style="width:190px;border-left:1px solid #C4CACE;">
    <form action="module.php" method="GET" id="formliste<?php echo $row_campagne->id; ?>">	
			<input type="hidden" name="nom" value="newsletter"/>
			<input type="hidden" name="action_newsletter" value="envoyer"/>
      <input type="hidden" name="debut_envoi" value="oui"/>
			<input type="hidden" name="id" value="<?php echo $row_campagne->id; ?>"/>
			<select name="num_liste" style="width:120px">
  		<?php	
  			$query_liste = mysql_query("select * FROM newsletter_liste where actif=1");
			while($result_liste = mysql_fetch_object($query_liste)){
		?>
				<option value="<?php echo $result_liste->id; ?>"><?php echo substr($result_liste->nom, 0, 45); ?></option> 
		<?php
			}
  		?>
  		</select>
			<a href="#" onclick="if(confirm('Etes-vous sur de vouloir envoyer cette campagne ?'))document.getElementById('formliste<?php echo $row_campagne->id; ?>').submit();return false;">Envoyer</a>  
    </form> 
  </li>
    <?php
    	} if($row_campagne->statut == 1){
    ?>	
    <li style="width:190px;border-left:1px solid #C4CACE;">
        <form action="module.php" method="GET" id="formreprendre<?php echo $row_campagne->id; ?>">	
    				<input type="hidden" name="nom" value="newsletter"/>
    				<input type="hidden" name="action_newsletter" value="envoyer"/>
            <input type="hidden" name="debut_envoi" value="oui"/>
    				<input type="hidden" name="id" value="<?php echo $row_campagne->id; ?>"/>
            <input type="hidden" name="num_liste" value="<?php echo $row_campagne->liste; ?>"/>
    				<a href="#" onclick="if(confirm('Etes-vous sur de vouloir reprendre l\'envoi de cette campagne ?'))document.getElementById('formreprendre<?php echo $row_campagne->id; ?>').submit();return false;">Reprendre</a>         		    
        </form> 
    </li>	
    <?php
    	} if($row_campagne->statut == 2){
    ?>	
    <li style="width:190px;border-left:1px solid #C4CACE;">Envoy&eacute;e le <?php echo date('d-m-y',strtotime($row_campagne->date)); ?> &nbsp; <a href="module.php?nom=newsletter&action_newsletter=statistique">(stats)</a></li>
    	
    <?php
    	}	
    ?>
    
    </li>
<li style="border-left:1px solid #C4CACE;"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=newsletter&action_newsletter=supprimer_campagne&id=<?php echo $row_campagne->id; ?>" onclick="return(confirm('Etes-vous sur de vouloir supprimer cette campagne ?'));"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>

<?php
	}
?>
</div>
</div>

