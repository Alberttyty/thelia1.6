<?php
require_once("Chequecadeau.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("chequecadeau");

include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
$chequecadeau = new Chequecadeau();
$commande = new Commande();

if(!isset($action)) $action="";
if(!isset($page)) $page=0;
if(!isset($classement)) $classement="";

$retour="";

?>

<?php

	if($action == "reinitialiser"){
	  $chequecadeau->charger($id);
	  $chequecadeau->utilise=0;
	  //$chequecadeau->commande=0;
	  $chequecadeau->commande_utilise=0;
	  //$chequecadeau->date=date('Y-m-d H:i:s');
	  $chequecadeau->dateutilisation="0000-00-00 00:00:00";
	  $chequecadeau->maj();
	}
	
	if($action == "utiliser"){
	  $chequecadeau->charger($id);
	  if($chequecadeau->utilise==0&&$chequecadeau->verifierDate()){
  	  $chequecadeau->utilise=1;
  	  $chequecadeau->commande_utilise=-1;
  	  $chequecadeau->dateutilisation=date('Y-m-d H:i:s');
  	  $chequecadeau->maj();
	  }
	}
	
	if($action == "utiliser_code"){
	  $chequecadeau->charger_code(md5($_POST['code']));
	  if($chequecadeau->id!=0) {
	  if($chequecadeau->utilise==0){
	    if($chequecadeau->verifierDate()){
  	  $chequecadeau->utilise=1;
  	  $chequecadeau->commande_utilise=-1;
  	  $chequecadeau->dateutilisation=date('Y-m-d H:i:s');
  	  $chequecadeau->maj();
  	  $retour="OK : Le chèque de ".$chequecadeau->montant." euro a été utilisé";
  	  }
  	  else {
      $retour="Erreur : Chèque périmé";
      }
	  }
	  else {
    $retour="Erreur : Le chèque a déjà été utilisé";
    }
    }
    else {
    $retour="Erreur : Pas de chèque avec ce code ou bien chèque déjà utilisé";
    }
	}
	
	if($action == "supprimer"){
	  $chequecadeau->charger($id);
	  if($chequecadeau->id!=0){
  	  $chequecadeau->delete();
	  }
	}
  
  if($action == "renvoyer"){
	  $chequecadeau->charger($id);
	  if($chequecadeau->id!=0){
  	  $chequecadeau->renvoyer();
	  }
	}
	
?>

<script type="text/JavaScript">

function reinitialiser(id){
	if(confirm("Voulez-vous vraiment réinitialiser le chèque N°"+id+" (il sera à nouveau utilisable) ?")) location="module.php?nom=chequecadeau&action=reinitialiser&id=" + id;

}

function utiliser(id){
	if(confirm("Voulez-vous vraiment utiliser le chèque N°"+id+" ?")) location="module.php?nom=chequecadeau&action=utiliser&id=" + id;

}

function supprimer(id){
	if(confirm("Voulez-vous vraiment supprimer le chèque N°"+id+" ?")) location="module.php?nom=chequecadeau&action=supprimer&id=" + id;

}

function renvoyer(id){
	if(confirm("Voulez-vous vraiment re-générer et renvoyer le mail du chèque N°"+id+" ? L'ancien chèque sera inutilisable")) location="module.php?nom=chequecadeau&action=renvoyer&id=" + id;

}

</script>



<?php

if ($retour!=""){

?>

<script type="text/JavaScript">
   
   <?php
   echo "alert('".$retour."');";
   ?>
   
</script>

<?php

}

?>

<?php
    $search="";

  	if($page=="") $page=1;

   	$query = "select * from $chequecadeau->table where 1 $search";
  	$resul = mysql_query($query, $chequecadeau->link);
  	$num = mysql_num_rows($resul);

  	$nbpage = 20;
  	$totnbpage = ceil($num/30);

  	$debut = ($page-1) * 30;

  	if($page>1) $pageprec=$page-1;
  	else $pageprec=$page;

  	if($page<$totnbpage) $pagesuiv=$page+1;
  	else $pagesuiv=$page;

  	$ordclassement = "order by id desc";

?>
  
<div id="contenu_int">
    	<p align="left"><a href="accueil.php" class="lien04">Accueil </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Liste des modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">Chèques Cadeaux</a>              
	</p>
<div class="entete_liste_client">
	<div class="titre">LISTE DES CHEQUES CADEAUX</div>
</div>
<ul id="Nav">
		<li style="height:25px; width:59px; border-left:1px solid #96A8B5;">N°</li>
    <li style="height:25px; width:66px; border-left:1px solid #96A8B5;">Montant</li>
		<li style="height:25px; width:111px; border-left:1px solid #96A8B5;">Emission</li>
		<li style="height:25px; width:111px; border-left:1px solid #96A8B5;">Utilisation</li>
		<li style="height:25px; width:149px; border-left:1px solid #96A8B5;">Commande émission</li>
		<li style="height:25px; width:149px; border-left:1px solid #96A8B5;">Commande utilisation</li>
		<li style="height:25px; width:46px; border-left:1px solid #96A8B5;">Utilisé</li>
		<li style="height:25px; width:86px; border-left:1px solid #96A8B5;"></li>
		<li style="height:25px; width:51px; border-left:1px solid #96A8B5;"></li>
		<li style="height:25px; width:30px; border-left:1px solid #96A8B5;"></li>   
    <li style="height:25px; width:30px; border-left:1px solid #96A8B5;"></li>  
</ul>

<span id="resul">

  <?php
  	$i=0;

    $query = "select * from $chequecadeau->table where 1 $search $ordclassement limit $debut,30";
  	$resul = mysql_query($query, $chequecadeau->link);

  	while($row = mysql_fetch_object($resul)){

  		$jour = substr($row->date, 8, 2);
  		$mois = substr($row->date, 5, 2);
  		$annee = substr($row->date, 2, 2);

  		$heure = substr($row->date, 11, 2);
  		$minute = substr($row->date, 14, 2);
  		$seconde = substr($row->date, 17, 2);
  		
  		$jour2 = substr($row->dateutilisation, 8, 2);
  		$mois2 = substr($row->dateutilisation, 5, 2);
  		$annee2 = substr($row->dateutilisation, 2, 2);

  		$heure2 = substr($row->dateutilisation, 11, 2);
  		$minute2 = substr($row->dateutilisation, 14, 2);
  		$seconde2 = substr($row->dateutilisation, 17, 2);
  		
  		$commandeemission="";
  		$commande->ref="";
  		if($row->commande==-1){
        $commandeemission="En Magasin";
      }
      else {
        $commande->charger($row->commande);
  		  $commandeemission=$commande->ref;
      }
  		
  		$commandeutilisation="";
  		$commande->ref="";
  		if($row->commande_utilise==-1){
        $commandeutilisation="En Magasin";
      }
      else {
        $commande->charger($row->commande_utilise);
  		  $commandeutilisation=$commande->ref;
      }
  		
  		
  		if($row->utilise==1)
  		{$utilise="Oui";}
  		else
      {$utilise="Non";} 
      
      $chequecadeau->charger($row->id);
      
      $dateverif=$chequecadeau->verifierDate();

  		if(!($i%2)) $fond="ligne_claire_rub";
  		else $fond="ligne_fonce_rub";
  		$i++;
  ?>
<ul class="<?php echo($fond); ?>">
	<li style="width:52px;"><?php echo($row->id); ?></li>
	<li style="width:59px;"><?php echo($row->montant); ?> &euro;</li>
	<li style="width:104px;"><?php echo($jour . "/" . $mois . "/" . $annee . " " . $heure . ":" . $minute . ":" . $seconde); ?></li>
	<li style="width:104px;"><?php echo($jour2 . "/" . $mois2 . "/" . $annee2 . " " . $heure2 . ":" . $minute2 . ":" . $seconde2); ?></li>
	<li style="width:142px;"><?php if($row->commande!=-1){ ?><a href="commande_details.php?ref=<?php echo($commandeemission); ?>"><?php } ?><?php echo($commandeemission); ?><?php if($row->commande!=-1){ ?></a><?php } ?></li>
	<li style="width:142px;"><?php if($row->commande_utilise!=-1){ ?><a href="commande_details.php?ref=<?php echo($commandeutilisation); ?>"><?php } ?><?php echo($commandeutilisation); ?><?php if($row->commande_utilise!=-1){ ?></a><?php } ?></li>
	<li style="width:39px;"><?php echo($utilise); ?></li>
	<li style="width:79px;"><a href="#" onclick="reinitialiser('<?php echo($row->id); ?>')">Réinitialiser</a></li>
	<li style="width:44px;"><?php if($dateverif&&!$row->utilise) {?><a href="#" onclick="utiliser('<?php echo($row->id); ?>')">Utiliser</a><?php } if(!$dateverif) {?><strong style="color:red;">Périmé</strong><?php }?></li>
  <li style="width:23px;text-align:center;"><a href="#" onclick="supprimer('<?php echo($row->id); ?>')"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
  <li style="width:23px;text-align:center;"><?php if($row->venteprod!=0&&$utilise=="Non"){ ?><a href="#" style="text-decoration:none;" onclick="renvoyer('<?php echo($row->id); ?>')">&gt;</a><?php } ?></li>
</ul>
<?php
	}
?>
</span>

<p id="pages">
<?php if($page > 1){ ?>
   <a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=chequecadeau&page=<?php echo($pageprec); ?>&statut=<?php echo $_GET['statut']; ?>" >Page précédente</a> |
	<?php } ?>
	<?php if($totnbpage > $nbpage){?>
		<?php if($page>1) {?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=chequecadeau&page=1&statut=<?php echo $_GET['statut']; ?>">...</a> | <?php } ?>
		<?php if($page+$nbpage-1 > $totnbpage){ $max = $totnbpage; $min = $totnbpage-$nbpage;} else{$min = $page-1; $max=$page+$nbpage-1; }?>
     <?php for($i=$min; $i<$max; $i++){ ?>
    	 <?php if($page != $i+1){ ?>
  	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=chequecadeau&page=<?php echo($i+1); ?>&classement=<?php echo($classement); ?>&statut=<?php echo $_GET['statut']; ?>" ><?php echo($i+1); ?></a> |
    	 <?php } else {?>
    		 <span class="selected"><?php echo($i+1); ?></span>
    		 |
   		  <?php } ?>
     <?php } ?>
		<?php if($page < $totnbpage){?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=chequecadeau&page=<?php echo $totnbpage; ?>&statut=<?php echo $_GET['statut']; ?>">...</a> | <?php } ?>
	<?php }
	else{
		for($i=0; $i<$totnbpage; $i++){ ?>
	    	 <?php if($page != $i+1){ ?>
	  	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=chequecadeau&page=<?php echo($i+1); ?>&statut=<?php echo $_GET['statut']; ?><?php echo $lien_voir; ?>"><?php echo($i+1); ?></a> |
	    	 <?php } else {?>
	    		 <span class="selected"><?php echo($i+1); ?></span>
	    		|
	   		  <?php } ?>
	     <?php } ?>
	<?php } ?>
     <?php if($page < $totnbpage){ ?>
<a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=chequecadeau&page=<?php echo($pagesuiv); ?>&statut=<?php echo $_GET['statut']; ?>">Page suivante</a></p>
	<?php } ?>





<div id="bloc_description">

<div class="entete_liste_client" style="margin-top:30px;">
	<div class="titre">CREER UN CHEQUE CADEAU</div>
	<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire1').submit()">VALIDER</a></div>
</div>

<?php
$rubrique = new Variable();
$rubrique->charger("chequecadeau_rubrique");
$produit = new Produit();
?>

<form action="../client/plugins/chequecadeau/nouveau.php" method="post" id="formulaire1">
<input name="action" value="nouveau" type="hidden">
<table style="clear: both;" cellpadding="5" cellspacing="0" width="100%">
    <tbody>
      <tr class="claire">
        <th class="designation" width="290">Montant</th>
    		<th>
        <select name="montant">
<?php
  $query = "select * from $produit->table where rubrique=\"$rubrique->valeur\" order by classement";
  $resul = mysql_query($query, $chequecadeau->link);

  while($row = mysql_fetch_object($resul)){
  
?>        
        <option value="<?php echo $row->ref; ?>"><?php echo $row->prix; ?></option>

<?php

  }

?>
                
        </select>
        </th>    
    	</tr>
    	<tr class="claire">
        <td class="designation" width="290">Type</th>
    		<td>
        <select name="type">
         <option value="cheque">Chèque</option>
         <option value="bon">Bon d'achat</option>
        </select>
        </td>    
    	</tr>
    </tbody>
</table>
</form>
</div>

<div id="bloc_description">

<div class="entete_liste_client" style="margin-top:30px;">
	<div class="titre">UTILISER UN CHEQUE CADEAU</div>
	<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire2').submit()">VALIDER</a></div>
</div>

<?php
$rubrique = new Variable();
$rubrique->charger("chequecadeau_rubrique");
$produit = new Produit();
?>
                  
<form action="module.php" method="post" id="formulaire2">
<input name="action" value="utiliser_code" type="hidden">
<input name="nom" value="chequecadeau" type="hidden">
<table style="clear: both;" cellpadding="5" cellspacing="0" width="100%">
    <tbody>
      <tr class="claire">
        <th class="designation" width="290">Code</th>
    		<th>
        <input type="text" name="code" />
        </th>    
    	</tr>
    </tbody>
</table>
</form>
</div>


</div>