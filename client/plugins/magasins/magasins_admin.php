<?php   
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");

	autorisation("magasins");

	include_once(realpath(dirname(__FILE__)) . "/Magasins.class.php");
	$magasins = new Magasins();
?>     
<div id="contenu_int"> 
<p align="left"><a href="accueil.php" class="lien04">Accueil </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">Gestion des magasins</a>              
	    </p>
	  <!-- bloc gestion des commentaires / colonne gauche -->  

<?php 
  
  if(isset($_REQUEST['id']))$id=$_REQUEST['id'];
  else $id=0;

	if(isset($action) && $action == "magasins_supprimer"){
		$magasins->charger($id);
		$magasins->delete();
		
		$cache = new Cache();
		$cache->vider("MAGASINS", "%");
	}
	
	if(isset($action) && $action == "magasins_modifier"){
		$magasins->charger($id);
		
		$magasins->nom=$_REQUEST['magasins_nom'];
		$magasins->description=$_REQUEST['magasins_description'];
		$magasins->adresse=$_REQUEST['magasins_adresse'];
		$magasins->code_postal=$_REQUEST['magasins_code_postal'];
    $magasins->ville=$_REQUEST['magasins_ville'];
    $magasins->pays=$_REQUEST['magasins_pays'];
    $magasins->telephone=$_REQUEST['magasins_telephone'];
    $magasins->email=$_REQUEST['magasins_email'];
    $magasins->url=$_REQUEST['magasins_url'];
    $magasins->lat=$_REQUEST['magasins_lat'];
    $magasins->lng=$_REQUEST['magasins_lng'];
		
		if($id!=0) $magasins->maj();
    if($id==0) $magasins->add();
		
	}
	
	if(isset($action) && $action == "magasins_editer"){
		$magasins->charger($id);
?>
<div id="bloc_description">
<div class="entete_liste_config">
	<div class="titre">DETAIL DU MAGASIN</div>
	<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit();">VALIDER LES MODIFICATIONS</a></div>
</div>
<form name="magasins" id="formulaire" action="module.php?nom=magasins" method="post">
<ul class="ligne_claire_BlocDescription">
  <li style="width:110px"><input type="hidden" name="action" value="magasins_modifier"/><input type="hidden" name="id" value="<?php echo($id); ?>"/>Titre</li>
  <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="magasins_nom" value="<?php echo($magasins->nom); ?>"/></li>
</ul>
<ul class="ligne_claire_BlocDescription">
  <li style="width:110px">Adresse</li>
  <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="magasins_adresse" id="magasins_adresse" value="<?php echo($magasins->adresse); ?>"/></li>
</ul>
<ul class="ligne_claire_BlocDescription">
  <li style="width:110px">Code Postal</li>
  <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form" name="magasins_code_postal" id="magasins_code_postal" value="<?php echo($magasins->code_postal); ?>"/></li>
</ul>
<ul class="ligne_claire_BlocDescription">
  <li style="width:110px">Ville</li>
  <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="magasins_ville" id="magasins_ville" value="<?php echo($magasins->ville); ?>"/></li>
</ul>
<ul class="ligne_claire_BlocDescription">
  <li style="width:110px">Pays</li>
  <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="magasins_pays" id="magasins_pays" value="<?php echo($magasins->pays); ?>"/></li>
</ul>
<ul class="ligne_claire_BlocDescription">
  <li style="width:110px">Coordonnées</li>
  <li style="border-left:1px solid #C4CACE;">Lat <input type="texte" class="form" name="magasins_lat" id="magasins_lat" value="<?php echo($magasins->lat); ?>"/> &nbsp;&nbsp; Lng <input type="texte" class="form" name="magasins_lng" id="magasins_lng" value="<?php echo($magasins->lng); ?>"/> &nbsp;&nbsp; <a href="#" onclick="getGeoLoc();return false;">charger</a></li>
</ul>
<ul class="ligne_claire_BlocDescription">
  <li style="width:110px">Téléphone</li>
  <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="magasins_telephone" value="<?php echo($magasins->telephone); ?>"/></li>
</ul>
<ul class="ligne_claire_BlocDescription">
  <li style="width:110px">Email</li>
  <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="magasins_email" value="<?php echo($magasins->email); ?>"/></li>
</ul>
<ul class="ligne_claire_BlocDescription">
  <li style="width:110px">Url (avec http://)</li>
  <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="magasins_url" value="<?php echo($magasins->url); ?>"/></li>
</ul>
<ul class="ligne_fonce_BlocDescription">
  <li style="width:110px">Description</li>
  <li style="border-left:1px solid #C4CACE;padding-bottom:10px;"><textarea name="magasins_description" id="description" class="form" cols="53" rows="15"><?php echo($magasins->description); ?></textarea></li>
</ul>
</form>
</div>

<?php
	} elseif( (!isset($action)) || $action == "" || $action == "magasins_supprimer" || $action == "magasins_modifier"){	
?>

<div class="entete_liste_config">
	<div class="titre">LISTES DES MAGASINS</div>
  <div class="fonction_ajout">
	<a href="?nom=magasins&amp;action=magasins_editer">AJOUTER UN MAGASIN</a>
	</div>
</div>
<div class="bordure_bottom">
<ul class="Nav_bloc_description">
		<li style="height:25px; width:300px; border-left:1px solid #96A8B5;">Nom</li>
		<li style="height:25px; width:250px; border-left:1px solid #96A8B5;">Ville</li>
    <li style="height:25px; width:250px; border-left:1px solid #96A8B5;">Pays</li>
		<li style="height:25px; width:70px; border-left:1px solid #96A8B5;">Voir</li>
</ul>
<?php
	$magasins = new Magasins();
	$query_magasins = "select * from $magasins->table order by pays,nom";
	$resul_magasins = mysql_query($query_magasins, $magasins->link);
	$i = 0;
	
	while($row = mysql_fetch_object($resul_magasins)){	
			
			
			if(!($i%2)) $fond="ligne_claire_rub";
  			else $fond="ligne_fonce_rub";
  			$i++;

?>
<ul class="<?php echo($fond); ?>">
	<li style="width:293px;"><?php echo $row->nom; ?></li>
	<li style="width:243px;"><?php echo $row->ville; ?></li>
  <li style="width:243px;"><?php echo $row->pays; ?></li>
	<li style="width:63px;"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=magasins&action=magasins_editer&id=<?php echo $row->id; ?>">&eacute;diter</a></li>
	<li style="width:43px; text-align:center"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=magasins&action=magasins_supprimer&id=<?php echo $row->id; ?>" onclick="return(confirm('Etes-vous sur de vouloir supprimer ce magasin ?'));"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>

<?php
		}
?>
</div>
<?php
}	
?>


</div>