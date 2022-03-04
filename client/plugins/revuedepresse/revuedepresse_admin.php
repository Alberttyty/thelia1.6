<?php   
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");    

	autorisation("revuedepresse");
  
  require_once(realpath(dirname(__FILE__)) ."/../../../fonctions/divers.php");

	include_once(realpath(dirname(__FILE__)) . "/Revuedepresse.class.php");
	$revuedepresse = new Revuedepresse();
?>     
<div id="contenu_int"> 
<p align="left"><a href="accueil.php" class="lien04">Accueil </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=revuedepresse" class="lien04">Revue de presse</a>              
	    </p>
                          
<?php 
  
  if(!isset($lang)) $lang=$_SESSION["util"]->lang;
	if(isset($action) && $action == "supprimer"){
		$revuedepresse->charger($id);
    $revuedepressedesc=new Revuedepressedesc($revuedepresse->id);
    $revuedepressedesc->delete_cascade('Revuedepressedesc','revuedepresse', $revuedepresse->id);
    /**************Effacer le fichier*********************/
    if ($documentdir = opendir(realpath(SITE_DIR)."/client/document"))
    {
      while ($file = readdir($documentdir))
  		{
  			if ($file == '.' || $file == '..') continue;
        if (preg_match("#^(.*)revue_de_presse_".$revuedepresse->id."\.(.*)$#i",$file)==1) unlink(realpath(SITE_DIR)."/client/document/".$file);
  		}
    }
    /*****************************************************/
		$revuedepresse->delete();
		$cache = new Cache();
		$cache->vider("REVUEDEPRESSE", "%");
	}
  
  if (isset($action) && $action == "supprimer_fichier"){
  
    $revuedepresse->charger($id);
  
    if(file_exists(realpath(SITE_DIR)."/client/document/".$revuedepresse->fichier)) unlink(realpath(SITE_DIR)."/client/document/".$revuedepresse->fichier);
    $revuedepresse->fichier="";
    $revuedepresse->maj();
    
    exit('ok_suppression_fichier_revue_de_presse');
  
  }
	
	if(isset($action) && $action == "modifier"){
	
		$revuedepresse->charger($id);
		$revuedepressedesc=new Revuedepressedesc($revuedepresse->id,$lang);
    $revuedepressedesc->lang=$lang;
		$revuedepressedesc->titre=$_REQUEST['titre'];
		$revuedepresse->source=$_REQUEST['source'];
		$revuedepressedesc->description=$_REQUEST['description'];
		$revuedepresse->lien=$_REQUEST['lien'];
    $revuedepresse->date=$_REQUEST['date'];
    list($jour, $mois, $annee) = explode("/", $revuedepresse->date);
    $revuedepresse->date=$annee."-".$mois."-".$jour;
		
		if($id==""){
      $query = "select max(classement) as maxClassement from $revuedepresse->table where 1";
  		$resul = mysql_query($query, $revuedepresse->link);
  		$max = mysql_result($resul, 0, "maxClassement");
  		$revuedepresse->classement = $max+1;
      $id=$revuedepresse->add();
      $revuedepresse->id=$id;
      $revuedepressedesc->revuedepresse=$id;
      $revuedepressedesc->add();
    }
		else{
      $revuedepresse->maj();
      $revuedepressedesc->maj();
    }
    
    $fichier = $_FILES['fichier']['tmp_name'];
		$nom = $_FILES['fichier']['name']; 
    if ($fichier != "") {
      $dot = strrpos($nom, '.');
      if ($dot !== false) {
        $extension = substr($nom, $dot+1);
        $extension = mb_strtolower($extension, 'UTF-8');
        //exit("..".FICHIER_URL."client/document/revue_de_presse_".$revuedepresse->id.".".$extension);
        copy($fichier,realpath(SITE_DIR)."/client/document/revue_de_presse_".$revuedepresse->id.".".$extension);
        $revuedepresse->fichier="revue_de_presse_".$revuedepresse->id.".".$extension;
        $revuedepresse->maj();
      }
    }
    
	} 
	
	if(isset($action) && $action == "modclassement"){
	   
     $revuedepresse->charger($_REQUEST['id']);
     $revuedepresse->changer_classement($_REQUEST['id'],$_REQUEST['type']);
     
	}
	
	if(isset($action) && $action == "visualiser"){
		$revuedepresse->charger($id);
    $revuedepressedesc=new Revuedepressedesc($revuedepresse->id,$lang);
		if($revuedepresse->date!="0000-00-00"&&$revuedepresse->date!=""){
  		list($annee, $mois, $jour) = explode("-", $revuedepresse->date);
  		$revuedepresse->date=$jour."/".$mois."/".$annee;
		}
?>
<div id="bloc_description">
<div class="entete_liste_config">
	<div class="titre">DETAIL DE LA REVUE DE PRESSE</div>
	<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit();">VALIDER LES MODIFICATIONS</a></div>
</div>
<form name="revuedepresse" class="revuedepresse" id="formulaire" action="module.php?nom=revuedepresse" method="post" enctype="multipart/form-data">
<?php if($revuedepresse->id){ ?>
   <ul class="ligne_claire_BlocDescription">
      <li style="width:110px"><?php echo trad('Changer_langue', 'admin'); ?></li>
      <li style="border-left:1px solid #C4CACE;">
<?php
			$langl = new Lang();
			$query = "select * from $langl->table";
			$resul = mysql_query($query);

			while($row = mysql_fetch_object($resul)){
				$langl->charger($row->id);
				$ttrad = new Revuedepressedesc();
				if ( (! $ttrad->charger($revuedepresse->id,$row->id)) && ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_INDIQUEE)
					continue;
	    ?>
	  		 <div class="flag<?php if($lang ==  $langl->id) { ?>Selected<?php } ?>"><a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=revuedepresse&amp;action=visualiser&amp;id=<?php echo($revuedepresse->id); ?>&amp;lang=<?php echo($langl->id); ?>"><img src="gfx/lang<?php echo($langl->id); ?>.gif" alt="-" /></a></div>
	    <?php } ?>
      </li>
    </ul>
<?php } ?>

 <ul class="ligne_fonce_BlocDescription">
    <li style="width:110px"><input type="hidden" name="lang" value="1"/><input type="hidden" name="action" value="modifier"/><input type="hidden" name="id" value="<?php echo($revuedepresse->id); ?>"/>Id</li>
    <li style="border-left:1px solid #C4CACE;"><?php echo($revuedepresse->id); ?></li>
 </ul>
 <ul class="ligne_claire_BlocDescription">
    <li style="width:110px">Titre</li>
    <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="titre" value="<?php echo($revuedepressedesc->titre); ?>"/></li>
 </ul>
 <ul class="ligne_fonce_BlocDescription">
    <li style="width:110px">Source</li>
    <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="source" value="<?php echo($revuedepresse->source); ?>"/></li>
 </ul>
 <ul class="ligne_claire_BlocDescription">
    <li style="width:110px">Date</li>
    <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form date" name="date" value="<?php echo($revuedepresse->date); ?>"/> (jj/mm/aaaa)</li>
 </ul>
 <ul class="ligne_fonce_BlocDescription">
    <li style="width:110px">Lien (avec http://)</li>
    <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="lien" value="<?php echo($revuedepresse->lien); ?>"/></li>
 </ul>
 <ul class="ligne_claire_BlocDescription">
    <li style="width:110px">Fichier</li>
    <li style="border-left:1px solid #C4CACE;">
    <?php if(trim($revuedepresse->fichier)!="") {echo "<span id=\"lien_fichier\"><a href=\"".FICHIER_URL."client/document/".$revuedepresse->fichier."\" class=\"fichier\">".$revuedepresse->fichier."</a> <a href=\"#\" onclick=\"if(confirm('Voulez vous vraiment supprimer ce fichier ?'))supprimerFichier('".$revuedepresse->id."');return false;\"><img src=\"gfx/supprimer.gif\"/></a><br/>Changer pour :<br/></span>";} ?>
      <input type="file" name="fichier" id="fichier"/>
    </li>
 </ul>
 <ul class="ligne_fonce_BlocDescription">
    <li style="width:110px">Description</li>
    <li style="border-left:1px solid #C4CACE;"><textarea id="description" name="description" class="form" cols="53" rows="15"><?php echo($revuedepressedesc->description); ?></textarea></li>
 </ul>

</form>
</div>

<?php
	} elseif( (!isset($action)) || $action == "" || $action == "supprimer" || $action == "modifier" || $action == "modclassement"){	
?>

<div class="entete_liste_config">
	<div class="titre">REVUE DE PRESSE</div>
	<div class="fonction_ajout"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=revuedepresse&action=visualiser&id=">AJOUTER UN ARTICLE DE PRESSE</a></div>
</div>
<div class="bordure_bottom">
<ul class="Nav_bloc_description">
		<li style="height:25px; width:50px; border-left:1px solid #96A8B5;">Id</li>
		<li style="height:25px; width:310px; border-left:1px solid #96A8B5;">Titre</li>
		<li style="height:25px; width:160px; border-left:1px solid #96A8B5;">Source</li>
    <li style="height:25px; width:120px; border-left:1px solid #96A8B5;">Date</li>	
		<li style="height:25px; width:70px; border-left:1px solid #96A8B5;">Voir</li>
		<li style="height:25px; width:80px; border-left:1px solid #96A8B5;">Classement</li>
		<li style="height:25px; width:40px; border-left:1px solid #96A8B5;">Suppr.</li>
</ul>
<?php

	$query = "select * from $revuedepresse->table order by classement";
	$resul = mysql_query($query, $revuedepresse->link);
	$i = 0;
	                         
	while($row = mysql_fetch_object($resul)){	
			
      $revuedepressedesc=new Revuedepressedesc($row->id);
			
			if(!($i%2)) $fond="ligne_claire_rub";
  			else $fond="ligne_fonce_rub";
  			$i++;
  			
		list($annee, $mois, $jour) = explode("-", $row->datedebut);
		$row->datedebut=$jour."/".$mois."/".$annee;
		
		list($annee, $mois, $jour) = explode("-", $row->datefin);
		$row->datefin=$jour."/".$mois."/".$annee;

?>
<ul class="<?php echo($fond); ?>">
	<li style="width:43px;"><?php echo $row->id; ?></li>    
	<li style="width:303px;"><?php echo $revuedepressedesc->titre; ?></li>
	<li style="width:153px;"><?php echo $row->source; ?></li>
	<li style="width:113px;"><?php echo $row->date; ?></li>
	<li style="width:63px;"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=revuedepresse&action=visualiser&id=<?php echo $row->id; ?>">&eacute;diter</a></li>
	<li style="width:73px;">
	 <div class="bloc_classement">  
	    <div class="classement"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=revuedepresse&action=modclassement&id=<?php echo($row->id); ?>&type=M"><img src="gfx/up.gif" border="0" /></a></div>
	    <div class="classement"><span id="classementdossier_<?php echo $row->id; ?>" class="classement_edit"><?php echo $row->classement; ?></span></div>
	    <div class="classement"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=revuedepresse&action=modclassement&id=<?php echo($row->id); ?>&type=D"><img src="gfx/dn.gif" border="0" /></a></div>
	 </div>
	</li>
  <li style="width:43px; text-align:center"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=revuedepresse&action=supprimer&id=<?php echo $row->id; ?>" onclick="return(confirm('Etes-vous sur de vouloir supprimer cette ligne ?'));"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>
       
<?php
		}
?>
</div>
<?php
}	
?>


</div>