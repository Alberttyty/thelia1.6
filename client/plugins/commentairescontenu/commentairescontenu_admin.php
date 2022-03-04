<?php   
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");

	autorisation("commentairescontenu");

	include_once(realpath(dirname(__FILE__)) . "/Commentairescontenu.class.php");
	$commentaires = new Commentairescontenu();
?>     
<div id="contenu_int"> 
<p align="left"><a href="accueil.php" class="lien04">Accueil </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">Gestion des commentaires de contenu</a>              
	    </p>
	  <!-- bloc gestion des commentaires / colonne gauche -->  

<?php 
	if(isset($action) && $action == "supprimer"){
		$commentaires->charger($id);
		$commentaires->delete();
		
		$cache = new Cache();
		$cache->vider("COMMENTAIRES", "%");
	}
	
	if(isset($action) && $action == "modifier"){
		$commentaires->charger($id);
		
		$commentaires->nom=$_REQUEST['commentaires_nom'];
		$commentaires->titre=$_REQUEST['commentaires_titre'];
		$commentaires->message=$_REQUEST['commentaires_message'];
		$commentaires->valide=$_REQUEST['commentaires_valide'];
		
		$commentaires->maj();
		
	}
	
	if(isset($action) && $action == "visualiser"){
		$commentaires->charger($id);
?>
<div id="bloc_description">
<div class="entete_liste_config">
	<div class="titre">DETAIL DU COMMENTAIRE</div>
	<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit();">VALIDER LES MODIFICATIONS</a></div>
</div>
<form name="commentaires" id="formulaire" action="module.php?nom=commentairescontenu" method="post">
 <ul class="ligne_fonce_BlocDescription">
    <li style="width:110px"><input type="hidden" name="action" value="modifier"/><input type="hidden" name="id" value="<?php echo($commentaires->id); ?>"/>Date</li>
    <li style="border-left:1px solid #C4CACE;"><?php echo($commentaires->date); ?></li>
 </ul>
 <ul class="ligne_claire_BlocDescription">
    <li style="width:110px">Contenu</li>
    <li style="border-left:1px solid #C4CACE;"><a href="contenu_modifier.php?id=<?php echo $commentaires->id_contenu; ?>" class="lien04"><?php echo $commentaires->id_contenu; ?></a></li>
 </ul>
 <ul class="ligne_fonce_BlocDescription">
    <li style="width:110px">Nom</li>
    <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form" name="commentaires_nom" value="<?php echo($commentaires->nom); ?>"/></li>
 </ul>
 <ul class="ligne_claire_BlocDescription">
    <li style="width:110px">Titre</li>
    <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="commentaires_titre" value="<?php echo($commentaires->titre); ?>"/></li>
 </ul>
 <ul class="ligne_fonce_BlocDescription">
    <li style="width:110px">Message</li>
    <li style="border-left:1px solid #C4CACE;"><textarea name="commentaires_message" class="form" cols="53" rows="15"><?php echo($commentaires->message); ?></textarea></li>
 </ul>
  <ul class="ligne_fonce_BlocDescription">
    <li style="width:110px">Valid&eacute;</li>
    <li style="border-left:1px solid #C4CACE;"><input type="radio" name="commentaires_valide" value="1" <?php if($commentaires->valide==1)echo('checked="checked"'); ?>/> Oui &nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" name="commentaires_valide" value="0" <?php if($commentaires->valide==0)echo('checked="checked"'); ?>/> Non</li>
 </ul>
</form>
</div>

<?php
	} elseif( (!isset($action)) || $action == "" || $action == "supprimer" || $action == "modifier"){	
?>

<div class="entete_liste_config">
	<div class="titre">LISTES DES COMMENTAIRES</div>
</div>
<div class="bordure_bottom">
<ul class="Nav_bloc_description">
		<li style="height:25px; width:150px; border-left:1px solid #96A8B5;">Date</li>
		<li style="height:25px; width:150px; border-left:1px solid #96A8B5;">R&eacute;f&eacute;rence contenu</li>
		<li style="height:25px; width:250px; border-left:1px solid #96A8B5;">Titre</li>
		<li style="height:25px; width:150px; border-left:1px solid #96A8B5;">Auteur</li>
    <li style="height:25px; width:70px; border-left:1px solid #96A8B5;">Valid&eacute;</li>	
		<li style="height:25px; width:70px; border-left:1px solid #96A8B5;">Voir</li>
		<li style="height:25px; width:80px; border-left:1px solid #96A8B5;">Supprimer</li>
</ul>
<?php
	$commentaires = new Commentairescontenu();
	$query_commentaires = "select * from $commentaires->table order by date desc";
	$resul_commentaires = mysql_query($query_commentaires, $commentaires->link);
	$i = 0;
	
	while($row = mysql_fetch_object($resul_commentaires)){	
			
			
			if(!($i%2)) $fond="ligne_claire_rub";
  			else $fond="ligne_fonce_rub";
  			$i++;

?>
<ul class="<?php echo($fond); ?>">
	<li style="width:143px;"><?php echo $row->date; ?></li>
	<li style="width:143px;"><a href="contenu_modifier.php?id=<?php echo $row->id_contenu; ?>" class="lien04"><?php echo $row->id_contenu; ?></a></li>
	<li style="width:243px;"><?php echo $row->titre; ?></li>
	<li style="width:143px;"><?php echo $row->nom; ?></li>
	<li style="width:63px;"><strong><?php if($row->valide==1) echo "Oui"; if($row->valide==0) echo "Non"; ?></strong></li>
	<li style="width:63px;"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=commentairescontenu&action=visualiser&id=<?php echo $row->id; ?>">&eacute;diter</a></li>
	<li style="width:43px; text-align:center"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=commentairescontenu&action=supprimer&id=<?php echo $row->id; ?>" onclick="return(confirm('Etes-vous sur de vouloir supprimer ce commentaire ?'));"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>

<?php
		}
?>
</div>
<?php
}	
?>


</div>