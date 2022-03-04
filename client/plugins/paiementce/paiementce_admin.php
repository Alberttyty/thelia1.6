<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
  
  require_once(realpath(dirname(__FILE__)) . "/../../../gestion/liste/commande.php");

	autorisation("paiementce");
  
      if(empty($_GET['statut']))
        $_GET['statut'] = Commande::NONPAYE . "," . Commande::PAYE . "," . Commande::TRAITEMENT;
    
    if ($_GET['statut'] == '*')
        $search="";
    else if($_GET['statut'] != "")
        $search="and statut IN(" . $_GET['statut'] . ")";

  	if($client != "") $search .= " and client=\"$client\"";
    
    $module=new Modules();
    $module->charger('paiementce');
    $search .= " and paiement=\"$module->id\"";
    
    
  	$commande = new Commande();
    
  	if($page=="") $page=1;

   	$query = "select count(*) from $commande->table where 1 $search";
  	$resul = $commande->query($query);
	 $num = $resul ? $commande->get_result($resul,0) : 0;

  	$nbpage = 20;
        $nbres = 30;
  	$totnbpage = ceil($num/$nbres);

  	$debut = ($page-1) * $nbres;

  	if($page>1) $pageprec=$page-1;
  	else $pageprec=$page;

  	if($page<$totnbpage) $pagesuiv=$page+1;
  	else $pagesuiv=$page;


  	if($classement == "client") {
  		$critere = "client";
  		$order = "asc";
  	}
  	else if($classement == "statut") {
  		$critere = "statut";
  		$order = "asc";
  	}
  	else {
  		$critere = "date";
  		$order = "desc";
  	}
  
?>

<script src="../lib/jquery/menu.js" type="text/javascript"></script>

<div id="contenu_int">
    <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">Commande faites par des CE</a>
    </p>
<div class="entete_liste_client">
	<div class="titre"><?php echo trad('LISTE_COMMANDES', 'admin'); ?> CE</div>
</div>
<ul id="Nav">
		<li style="height:25px; width:149px; border-left:1px solid #96A8B5;"><?php echo trad('Num_commande', 'admin'); ?></li>
		<li style="height:25px; width:111px; border-left:1px solid #96A8B5;"><?php echo trad('Date_Heure', 'admin'); ?></li>
		<li style="height:25px; width:207px; border-left:1px solid #96A8B5;"><?php echo trad('Societe', 'admin'); ?></li>
		<li style="height:25px; width:207px; border-left:1px solid #96A8B5;"><?php echo trad('Nom', 'admin'); ?> &amp; <?php echo trad('Prenom', 'admin'); ?></li>
		<li style="height:25px; width:66px; border-left:1px solid #96A8B5;"><?php echo trad('Montant', 'admin'); ?></li>
		<li style="height:25px; width:77px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;"><?php echo trad('Statut', 'admin'); ?>
			<ul class="Menu">
			 <?php
       	 		$statut = new Statut();
       	 		$query_stat = "select * from $statut->table";
       	 		$resul_stat = $statut->query($query_stat);
       	 		while($resul_stat && $row_stat = $statut->fetch_object($resul_stat)){
       	 			$statutdesc = new Statutdesc();
       	 			$statutdesc->charger($row_stat->id);
       	 	?>
				<li style="width:84px;"><a href="module.php?nom=paiementce&statut=<?php echo $row_stat->id; ?>" name="<?php echo $row_stat->id; ?>"><?php echo $statutdesc->titre; ?></a></li>
			<?php
       	 	}
       	 	?>
				<li style="width:84px;"><a href="module.php?nom=paiementce">En cours</a></li>
				<li style="width:84px;"><a href="module.php?nom=paiementce&statut=*">Toutes</a></li>
			</ul>
		</li>
		<li style="height:25px; width:47px; border-left:1px solid #96A8B5;"></li>
		<li style="height:25px; width:42px; border-left:1px solid #96A8B5;"><?php echo trad('Annuler', 'admin'); ?></li>
</ul>

<div id="resul">
<?php
  	lister_commandes($critere, $order, $debut, $nbres, $search);
?>
</div>

<p id="pages">
<?php if($page > 1){ ?>
   <a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=paiementce&page=<?php echo($pageprec); ?>&statut=<?php echo $_GET['statut']; ?>" >Page précédente</a> |
	<?php } ?>
	<?php if($totnbpage > $nbpage){?>
		<?php if($page>1) {?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=paiementce&page=1&statut=<?php echo $_GET['statut']; ?>">...</a> | <?php } ?>
		<?php if($page+$nbpage-1 > $totnbpage){ $max = $totnbpage; $min = $totnbpage-$nbpage;} else{$min = $page-1; $max=$page+$nbpage-1; }?>
     <?php for($i=$min; $i<$max; $i++){ ?>
    	 <?php if($page != $i+1){ ?>
  	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=paiementce&page=<?php echo($i+1); ?>&classement=<?php echo($classement); ?>&statut=<?php echo $_GET['statut']; ?>" ><?php echo($i+1); ?></a> |
    	 <?php } else {?>
    		 <span class="selected"><?php echo($i+1); ?></span>
    		 |
   		  <?php } ?>
     <?php } ?>
		<?php if($page < $totnbpage){?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=paiementce&page=<?php echo $totnbpage; ?>&statut=<?php echo $_GET['statut']; ?>">...</a> | <?php } ?>
	<?php }
	else{
		for($i=0; $i<$totnbpage; $i++){ ?>
	    	 <?php if($page != $i+1){ ?>
	  	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=paiementce&page=<?php echo($i+1); ?>&statut=<?php echo $_GET['statut']; ?><?php echo $lien_voir; ?>"><?php echo($i+1); ?></a> |
	    	 <?php } else {?>
	    		 <span class="selected"><?php echo($i+1); ?></span>
	    		|
	   		  <?php } ?>
	     <?php } ?>
	<?php } ?>
     <?php if($page < $totnbpage){ ?>
<a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=paiementce&page=<?php echo($pagesuiv); ?>&statut=<?php echo $_GET['statut']; ?>">Page suivante</a></p>
	<?php } ?>
</div>
<?php
	require_once("pied.php");
?>
</div>
</div>