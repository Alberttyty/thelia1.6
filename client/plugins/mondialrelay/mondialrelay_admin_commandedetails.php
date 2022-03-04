<?php

	include_once(dirname(__FILE__) . "/../../../fonctions/authplugins.php");
	autorisation("mondialrelay");

	include_once(dirname(__FILE__) . "/Mondialrelay.class.php");

	$admin_dir = basename(dirname($_SERVER['REQUEST_URI']));

	$this_page_url = Variable::lire('urlsite') . "/$admin_dir/commande_details.php?ref=".$_REQUEST['ref'];

    $mr = new Mondialrelay();

	$commande = new Commande();

	if ($commande->charger_ref($_REQUEST['ref']) && $mr->charger_par_commande($commande->id)) {

		$detailPoint = $mr->infosPointRelais($mr->point);

		if ($detailPoint !== false) {

			$libelle = $detailPoint['LgAdr1'].' '.$detailPoint['LgAdr2'].' '.$detailPoint['LgAdr3'].' '.$detailPoint['LgAdr4'].', '.$detailPoint['CP'].' '.$detailPoint['Ville'];

			?>
			<script type="text/javascript">
				function show_relay(url) {
 					window.open(url, "mondialrelay", 'width=772,height=570,status=0,menubar=0,location=0,titlebar=0');
 				}

				function expedition(url) {
 					window.open(url, "mondialrelay", 'width=772,height=570,status=0,menubar=0,location=0,titlebar=0');
 				}
 			</script>

			<a id="mondialrelay"></a>

			<div class="entete_liste_client">
				<div class="titre">TRANSPORT PAR MONDIAL RELAY</div>
			</div>

			<ul style="background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;" class="ligne_claire_BlocDescription">
				<li style="width: 100%; padding-top: 15px;background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;"><?php echo $libelle; ?></li>
			</ul>

			<ul class="ligne_fonce_BlocDescription">
				<li style="width:290px;" class="designation">Numéro du point relais</li>
				<li><?php echo $detailPoint['Num'] ?></li>
			</ul>

			<ul class="ligne_claire_BlocDescription">
				<li style="width:290px;" class="designation">Informations détaillées</li>
				<li><a href="#" onclick="show_relay('<?php echo $mr->url_popup_point_relais($mr->point) ?>'); return false;">Afficher</a></li>
			</ul>
      <!--
			<?php if (empty($mr->expedition)) { ?>

				<ul class="ligne_fonce_BlocDescription">
					<li style="width:290px;" class="designation">Expedition</li>
					<li><a href="../client/plugins/mondialrelay/expedition.php?id=<?php echo $mr->id ?>&redir=<?php echo urlencode($this_page_url); ?>#mondialrelay">Expédier la commande</a></li>
				</ul>

			<?php } else { ?>

				<ul class="ligne_fonce_BlocDescription">
					<li style="width:290px;" class="designation">Numéro d'expedition</li>
					<li><?php echo $mr->expedition ?> (<a href="../client/plugins/mondialrelay/expedition.php?id=<?php echo $mr->id ?>&redir=<?php echo urlencode($this_page_url); ?>#mondialrelay">Ré-expédier</a>)</li>
				</ul>

				<ul class="ligne_claire_BlocDescription">
					<li style="width:290px;" class="designation">Etiquette d'expédition</li>
					<li><a href="http://www.mondialrelay.com/<?php echo $mr->getUrlEtiquettes($mr->expedition, "FR") ?>">Télécharger l'étiquette PDF</a></li>
				</ul>

				<ul class="ligne_fonce_BlocDescription">
					<li style="width:290px;" class="designation">Suivi du colis</li>
					<li><a href="#" onclick="show_relay('<?php echo $mr->url_popup_suivi($mr->expedition) ?>'); return false;">Afficher</a></li>
				</ul>

			<?php } ?>
      -->
		<?php
		}
	}
?>