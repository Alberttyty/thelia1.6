<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../../fonctions/authplugins.php");

	autorisation("newsletter");

	include_once(realpath(dirname(__FILE__)) . "/../Newsletter.class.php");
?>

	    <p><a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module.php?nom=newsletter" class="lien04">Newsletter</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">Statistiques</a></p>
<div class="entete_liste_config">
	<div class="titre">MES CAMPAGNES</div>
</div>
<ul class="Nav_bloc_description">
	<li style="height:25px; width:400px; border-left:1px solid #96A8B5;">Campagne</li>
	<li style="height:25px; width:57px; border-left:1px solid #96A8B5;">Total</li>
	<li style="height:25px; width:90px; border-left:1px solid #96A8B5;">Envoy&eacute;</li>
	<li style="height:25px; width:90px; border-left:1px solid #96A8B5;">Ouvert</li>
	<li style="height:25px; width:90px; border-left:1px solid #96A8B5;">Clic</li>
	<li style="height:25px; width:90px; border-left:1px solid #96A8B5;">Non reçu</li>
	<li style="height:25px; width:60px; border-left:1px solid #96A8B5;">Spam</li>
</ul>
	
	<div class="bordure_bottom">

<?php

	$urlsite = new Variable();
	$urlsite->charger("urlsite");
?>

<?php
	$campagne = new Newsletter_campagne();
	
	$query_campagne = "select * from $campagne->table where statut=2 order by date desc";
	$resul_campagne = mysql_query($query_campagne, $campagne->link);

	$i = 0;


	while($row_campagne = mysql_fetch_object($resul_campagne)){
	
		$newsletter = new Newsletter_campagne();
		$newsletter->charger($row_campagne->campagne);
		
		$i++;
		
		if($i%2)
			$fond = "ligne_claire_rub";
		else
			$fond = "ligne_fonce_rub";
	
    $stats=$newsletter->allStats();
		$total = $stats->total;
		$sent = $stats->sent;
		$open = $stats->open;
		$click = $stats->click;
		$bounce = $stats->bounce;
		$spam =	$stats->spam;
		
		if($total != ""){			
			$pourccent = $sent * 100 / $total;
			$pourcopen = $open * 100 / $total;
			$pourcclick = $click * 100 / $total;
			$pourcbounce = $bounce * 100 / $total;
			$pourcspam = $spam * 100 / $total;
		} else {
			$pourccent = 0;
			$pourcopen = 0;
			$pourcclick = 0;
			$pourcbounce = 0;
			$pourcspam = 0;			
		}
		
?>

<ul class="<?php echo $fond; ?>">
	<li style="border-left:1px solid #C4CACE; width:393px">Campagne N° <?php echo $row_campagne->id; ?> / <?php echo substr($row_campagne->titre, 0, 26); ?> (envoyée le <?php echo date('d-m-y',strtotime($row_campagne->date)); ?>)</li>
	<li style="border-left:1px solid #C4CACE; width:50px"><?php if($total !="") { ?> <?php echo $total; ?> <?php } else { ?> 0 <?php } ?></li>
	<li style="border-left:1px solid #C4CACE; width:83px"><?php if($sent !="") { ?> <?php echo $sent; ?> <?php } else { ?> 0 <?php } ?> (<?php if($pourccent !="") { ?><?php echo round($pourccent,1); ?><?php } else { ?>0 <?php } ?>%)</li>
	<li style="border-left:1px solid #C4CACE; width:83px"><?php if($open !="") { ?> <?php echo $open; ?> <?php } else { ?> 0 <?php } ?> (<?php if($pourcopen !="") { ?><?php echo round($pourcopen,1); ?><?php } else { ?>0 <?php } ?>%)</li>
	<li style="border-left:1px solid #C4CACE; width:83px"><?php if($click !="") { ?> <?php echo $click; ?> <?php } else { ?> 0 <?php } ?> (<?php if($pourcclick !="") { ?><?php echo round($pourcclick,1); ?><?php } else { ?>0 <?php } ?>%)</li>
  <li style="border-left:1px solid #C4CACE; width:83px"><?php if($bounce !="") { ?> <?php echo $bounce; ?> <?php } else { ?> 0 <?php } ?> (<?php if($pourcbounce !="") { ?><?php echo round($pourcbounce,1); ?><?php } else { ?>0 <?php } ?>%)</li>
  <li style="border-left:1px solid #C4CACE; width:83px"><?php if($spam !="") { ?> <?php echo $spam; ?> <?php } else { ?> 0 <?php } ?> (<?php if($pourcspam !="") { ?><?php echo round($pourcspam,1); ?><?php } else { ?>0 <?php } ?>%)</li>
  </li>
</ul>

<?php
	}
?>
</div>
