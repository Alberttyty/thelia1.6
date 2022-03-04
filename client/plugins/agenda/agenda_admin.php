<?php   
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");

	autorisation("agenda");

	include_once(realpath(dirname(__FILE__)) . "/Agenda.class.php");
	$agenda = new Agenda();
?>     
<div id="contenu_int"> 
<p align="left"><a href="accueil.php" class="lien04">Accueil </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=agenda" class="lien04">Gestion de l'agenda</a>              
	    </p>
	  <!-- bloc gestion des commentaires / colonne gauche -->  

<?php 

  if(!isset($lang)) $lang=$_SESSION["util"]->lang;

	if(isset($action) && $action == "supprimer"){
		$agenda->charger($id);
    $agendadesc=new Agendadesc($agenda->id);
    $agendadesc->delete_cascade('Agendadesc','agenda', $agenda->id);
		$agenda->delete();
		$cache = new Cache();
		$cache->vider("AGENDA", "%");
	}
	
	if(isset($action) && $action == "modifier"){
	
		$agenda->charger($id);
		$agendadesc=new Agendadesc($agenda->id,$lang);
    $agendadesc->lang=$lang;
		$agendadesc->titre=$_REQUEST['titre'];
		$agenda->lieu=$_REQUEST['lieu'];
		$agendadesc->description=$_REQUEST['description'];
		$agenda->lien=$_REQUEST['lien'];
    $agenda->datedebut=$_REQUEST['datedebut'];
    list($jour, $mois, $annee) = explode("/", $agenda->datedebut);
    $agenda->datedebut=$annee."-".$mois."-".$jour;
    $agenda->datefin=$_REQUEST['datefin'];
    list($jour, $mois, $annee) = explode("/", $agenda->datefin);
    $agenda->datefin=$annee."-".$mois."-".$jour;
		
		if($id==""){
      $query = "select max(classement) as maxClassement from $agenda->table where 1";
  		$resul = mysql_query($query, $agenda->link);
  		$max = mysql_result($resul, 0, "maxClassement");
  		$agenda->classement = $max+1;
      $id=$agenda->add();
      $agendadesc->agenda=$id;
      $agendadesc->add();
    }
		else{
      $agenda->maj();
      $agendadesc->maj();
    }
		
	} 
	
	if(isset($action) && $action == "modclassement"){
	   
	   $agenda = new Agenda();
     $agenda->charger($_REQUEST['id']);
     $agenda->changer_classement($_REQUEST['id'], $_REQUEST['type']);
     
	}
	
	if(isset($action) && $action == "visualiser"){
		$agenda->charger($id);
    $agendadesc=new Agendadesc($agenda->id,$lang);
		if($agenda->datedebut!="0000-00-00"&&$agenda->datedebut!=""){
		list($annee, $mois, $jour) = explode("-", $agenda->datedebut);
		$agenda->datedebut=$jour."/".$mois."/".$annee;
		}
		if($agenda->datefin!="0000-00-00"&&$agenda->datefin!=""){
		list($annee, $mois, $jour) = explode("-", $agenda->datefin);
		$agenda->datefin=$jour."/".$mois."/".$annee;
		}
?>
<div id="bloc_description">
<div class="entete_liste_config">
	<div class="titre">DETAIL DE LA DATE</div>
	<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit();">VALIDER LES MODIFICATIONS</a></div>
</div>
<form name="agenda" class="agenda" id="formulaire" action="module.php?nom=agenda" method="post">
<?php if($agenda->id){ ?>
   <ul class="ligne_claire_BlocDescription">
      <li style="width:110px"><?php echo trad('Changer_langue', 'admin'); ?></li>
      <li style="border-left:1px solid #C4CACE;">
<?php
			$langl = new Lang();
			$query = "select * from $langl->table";
			$resul = mysql_query($query);

			while($row = mysql_fetch_object($resul)){
				$langl->charger($row->id);
				$ttrad = new Agendadesc();
				if ( (! $ttrad->charger($agenda->id,$row->id)) && ActionsLang::instance()->get_action_si_trad_absente() == ActionsLang::UTILISER_LANGUE_INDIQUEE)
					continue;
	    ?>
	  		 <div class="flag<?php if($lang ==  $langl->id) { ?>Selected<?php } ?>"><a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=agenda&amp;action=visualiser&amp;id=<?php echo($revuedepresse->id); ?>&amp;lang=<?php echo($langl->id); ?>"><img src="gfx/lang<?php echo($langl->id); ?>.gif" alt="-" /></a></div>
	    <?php } ?>
      </li>
    </ul>
<?php } ?>
 <ul class="ligne_fonce_BlocDescription">
    <li style="width:110px"><input type="hidden" name="action" value="modifier"/><input type="hidden" name="id" value="<?php echo($agenda->id); ?>"/>Id</li>
    <li style="border-left:1px solid #C4CACE;"><?php echo($agenda->id); ?></li>
 </ul>
 <ul class="ligne_claire_BlocDescription">
    <li style="width:110px">Titre</li>
    <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="titre" value="<?php echo($agendadesc->titre); ?>"/></li>
 </ul>
 <ul class="ligne_fonce_BlocDescription">
    <li style="width:110px">Lieu</li>
    <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="lieu" value="<?php echo($agenda->lieu); ?>"/></li>
 </ul>
  <ul class="ligne_claire_BlocDescription">
    <li style="width:110px">Date début</li>
    <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form date" name="datedebut" value="<?php echo($agenda->datedebut); ?>"/> (jj/mm/aaaa)</li>
 </ul>
   <ul class="ligne_fonce_BlocDescription">
    <li style="width:110px">Date fin</li>
    <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form date" name="datefin" value="<?php echo($agenda->datefin); ?>"/> (jj/mm/aaaa)</li>
 </ul>
  <ul class="ligne_claire_BlocDescription">
    <li style="width:110px">Description</li>
    <li style="border-left:1px solid #C4CACE;"><textarea id="description" name="description" class="form" cols="53" rows="15"><?php echo($agendadesc->description); ?></textarea></li>
 </ul>
<ul class="ligne_fonce_BlocDescription">
    <li style="width:110px">Lien (avec http://)</li>
    <li style="border-left:1px solid #C4CACE;"><input type="texte" class="form_long" name="lien" value="<?php echo($agenda->lien); ?>"/></li>
 </ul>

</form>
</div>

<?php
	} elseif( (!isset($action)) || $action == "" || $action == "supprimer" || $action == "modifier" || $action == "modclassement"){	
?>

<div class="entete_liste_config">
	<div class="titre">AGENDA</div>
	<div class="fonction_ajout"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=agenda&action=visualiser&id=">AJOUTER UNE DATE</a></div>
</div>
<div class="bordure_bottom">
<ul class="Nav_bloc_description">
		<li style="height:25px; width:50px; border-left:1px solid #96A8B5;">Id</li>
		<li style="height:25px; width:250px; border-left:1px solid #96A8B5;">Titre</li>
		<li style="height:25px; width:160px; border-left:1px solid #96A8B5;">Lieu</li>
    <li style="height:25px; width:120px; border-left:1px solid #96A8B5;">Date début</li>
    <li style="height:25px; width:120px; border-left:1px solid #96A8B5;">Date fin</li>		
		<li style="height:25px; width:70px; border-left:1px solid #96A8B5;">Voir</li>
		<li style="height:25px; width:80px; border-left:1px solid #96A8B5;">Classement</li>
		<li style="height:25px; width:40px; border-left:1px solid #96A8B5;">Suppr.</li>
</ul>
<?php
	$agenda = new Agenda();
	$query_agenda = "select * from $agenda->table order by classement";
	$resul_agenda = mysql_query($query_agenda, $agenda->link);
	$i = 0;
	
	while($row = mysql_fetch_object($resul_agenda)){	
		
    $agendadesc=new Agendadesc($row->id);	
			
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
	<li style="width:243px;"><?php echo $agendadesc->titre; ?></li>
	<li style="width:153px;"><?php echo $row->lieu; ?></li>
	<li style="width:113px;"><?php echo $row->datedebut; ?></li>
	<li style="width:113px;"><?php echo $row->datefin; ?></li>
	<li style="width:63px;"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=agenda&action=visualiser&id=<?php echo $row->id; ?>">&eacute;diter</a></li>
	<li style="width:73px;">
	 <div class="bloc_classement">  
	    <div class="classement"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=agenda&action=modclassement&id=<?php echo($row->id); ?>&type=M"><img src="gfx/up.gif" border="0" /></a></div>
	    <div class="classement"><span id="classementdossier_<?php echo $row->id; ?>" class="classement_edit"><?php echo $row->classement; ?></span></div>
	    <div class="classement"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=agenda&action=modclassement&id=<?php echo($row->id); ?>&type=D"><img src="gfx/dn.gif" border="0" /></a></div>
	 </div>
	</li>
  <li style="width:43px; text-align:center"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?nom=agenda&action=supprimer&id=<?php echo $row->id; ?>" onclick="return(confirm('Etes-vous sur de vouloir supprimer cette date ?'));"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>
       
<?php
		}
?>
</div>
<?php
}	
?>


</div>