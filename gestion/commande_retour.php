<?php
require_once("pre.php");
require_once("auth.php");

if(! est_autorise("acces_commandes")) exit; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	require_once("title.php");
	require_once("../fonctions/divers.php");

	if(!isset($action)) $action="";
	if(!isset($statusch)) $statusch="";

	$commande = new Commande();
	$commande->charger_ref($ref);

	$modules = new Modules();
	$modules->charger_id($commande->paiement);

	$devise = new Devise();
	$devise->charger($commande->devise);

	$Savente = new Savente();
	$Savente->charger_old($commande->id);

	if($statusch) $Savente->setStatutAndSave($statusch);
?>
</head>

<body>
<div id="wrapper">
	<div id="subwrapper">
<?php
	$menu = "commande";
	require_once("entete.php");
?>
	<div id="contenu_int">
		<p>
			<a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin', 'admin'); ?> </a>
			<img src="gfx/suivant.gif" width="12" height="9" border="0" />
			<a href="commande.php" class="lien04"><?php echo trad('Gestion_commandes', 'admin'); ?></a>
		</p>

		<!-- Début de la colonne de gauche -->
		<div id="bloc_description">
			<div class="entete_liste_client">
				<div class="titre"><?php echo trad('INFO_COMMANDE', 'admin'); ?> <?php echo $commande->ref ?></div>
			</div>
			<ul class="Nav_bloc_description">
				<li style="width:400px;"><?php echo trad('Designation', 'admin'); ?></li>
				<li style="width:75px;"><?php echo trad('Prix_unitaire', 'admin'); ?></li>
				<li style="width:30px;"><?php echo trad('Qte', 'admin'); ?></li>
				<li style="width:30px;"><?php echo trad('Total', 'admin'); ?></li>
			</ul>
		<?php
		function liste_parent($parent, $niveau, $commande, $grandParent, $debutLie=0) {
			global $deco, $listePassee;
			$venteprod = new Venteprod();

			$query = 'SELECT * FROM '.$venteprod->table.' WHERE commande = '.$commande->id.' AND parent = '.$parent;
			$resul = $venteprod->query($query);

			$memFin = '';
			while($resul && $row = $venteprod->fetch_object($resul)) {

				$venteprod->charger($row->id);

				$produit = new Produit();
				$produitdesc = new Produitdesc();

				$produit->charger($venteprod->ref);
				$produitdesc->charger($produit->id);

				$rubrique = new Rubrique();
				$rubrique->charger($produit->rubrique);

				$rubriquedesc = new Rubriquedesc();
				$rubriquedesc->charger($rubrique->id);

				if($rubriquedesc->titre !="") $titrerub = $rubriquedesc->titre;
				else $titrerub = "//";

				if(!($deco%2)) $fond="ligne_fonce_BlocDescription";
				else $fond="ligne_claire_BlocDescription";

				$listePassee[] = $venteprod->id;

				//$tva = $venteprod->tva/100;
				//$tva += 1;
				$prix_ttc = $venteprod->prixu;

				if($grandParent!=0 && $grandParent==$venteprod->id) {
					$memFin = '<ul class="'.__COULEUR_FOND__.'">
					<li style="width:400px;">'.$venteprod->ref.' - '.$titrerub.' - '.str_replace("\n", "<br />", $venteprod->titre).'</li>
					<li style="width:60px;">'.round($prix_ttc, 2).'</li>
					<li style="width:20px;">'.$venteprod->quantite.'</li>
					<li style="width:30px;">'.round($venteprod->quantite*$prix_ttc, 2).'</li>
					</ul>';
				} else {
					$deco++;
					?>
					<ul class="<?php echo($fond); ?>">
						<li style="width:400px">
							<?php echo $venteprod->ref .' - '.$titrerub.' '.str_replace("\n", "<br />", $venteprod->titre); ?>
						</li>
						<li style="width:60px;"><?php echo(round($prix_ttc, 2)); ?></li>
						<li style="width:20px;"><?php echo($venteprod->quantite); ?></li>
						<li style="width:30px;"><?php echo(round($venteprod->quantite*$prix_ttc, 2)); ?></li>
					</ul>
					<?php
				}

				if($grandParent != $venteprod->id) {
					liste_parent($venteprod->id, $niveau+1, $commande, $parent);
				}
			}

			if($memFin!="") {
				if(!($deco%2)) $fond="ligne_fonce_BlocDescription";
				else $fond="ligne_claire_BlocDescription";

				$deco++;
				echo str_replace("__COULEUR_FOND__", $fond, $memFin);
			}
		}

		$deco = 0;
		$listePassee = array();
		liste_parent(0, 0, $commande, 0);

		$venteprod = new Venteprod();
		do {
			$q = 'SELECT parent FROM '.$venteprod->table.' WHERE commande = '.$commande->id;
			if(count($listePassee)>0)
				$q .= " AND id NOT IN(" . implode(',', $listePassee) . ")";
			$q .= " LIMIT 1";

			$r = $venteprod->query($q);
			$nbRestant = $venteprod->num_rows($r);
			if($nbRestant>0) {
				liste_parent($venteprod->get_result($r, 0), 0, $commande, 0, 1);
			}
		} while($nbRestant>0);

		$total = $commande->total();
		$totalremise = $total - $commande->remise;

		$port = $commande->port;
		if($port<0) $port=0;

		$time = strtotime($commande->date);

		$dateaff = strftime("%d/%m/%y", $time);
		$heureaff =  strftime("%H:%M:%S", $time);
		?>
			<ul class="ligne_total_BlocDescription">
				<li style="width:400px;"><?php echo trad('Total', 'admin'); ?></li>
				<li><?php echo(round($total, 2)); ?> <?php echo $devise->symbole; ?> T.T.C</li>
			</ul>

			<div id="bloc_description">
				<div class="entete_liste_client">
					<div class="titre"><?php echo trad('INFO_RETOUR', 'admin'); ?> <?php echo $commande->ref ?></div>
				</div>
				<ul class="Nav_bloc_description">
					<li style="width:400px;"><?php echo trad('Designation', 'admin'); ?></li>
					<li style="width:75px;"><?php echo trad('Prix_unitaire', 'admin'); ?></li>
					<li style="width:30px;"><?php echo trad('Qte', 'admin'); ?></li>
					<li style="width:30px"><?php echo trad('Total', 'admin'); ?></li>
				</ul>
				<?php
					$SaventeRetour = new SaventeRetour();
					$tab_retours = $SaventeRetour->lister_retours($Savente->id);

					$total_retour = 0;
					foreach($tab_retours AS $prod) {
						$venteprod->charger($prod->venteprod);

						//$tva = $venteprod->tva/100;
						//$tva += 1;
						//$prix_ttc = $venteprod->prixu*$tva;
						$prix_ttc = $venteprod->prixu;
						$sous_total = $venteprod->quantite*$prix_ttc;

						$total_retour += $sous_total;
						?>
						<ul class="ligne_BlocDescription">
							<li style="width:410px;"><?php echo $venteprod->ref . " - " . str_replace("\n", "<br />", $venteprod->titre); ?></li>
							<li style="width:75px;"><?php echo(round($prix_ttc, 2)); ?></li>
							<li style="width:30px;"><?php echo($venteprod->quantite); ?></li>
							<li style="width:30px;"><?php echo(round($sous_total, 2)); ?></li>
						</ul>
						<?php
					}
				?>
				<ul class="ligne_total_BlocDescription">
					<li style="width:400px;"><?php echo trad('Total', 'admin'); ?></li>
					<li><?php echo(round($total_retour, 2)); ?> <?php echo $devise->symbole; ?> T.T.C</li>
				</ul>

			</div><!--bloc_description-->
			<?php /******************************************************************************/ ?>
			<div id="bloc_description">
				<div class="entete_liste_client">
					<div class="titre"><?php echo trad('INFO_ECHANGE', 'admin'); ?> <?php echo $commande->ref ?></div>
				</div>
				<ul class="Nav_bloc_description">
					<li style="width:400px;"><?php echo trad('Designation', 'admin'); ?></li>
					<li style="width:75px;"><?php echo trad('Prix_unitaire', 'admin'); ?></li>
					<li style="width:30px;"><?php echo trad('Qte', 'admin'); ?></li>
					<li style="width:30px"><?php echo trad('Total', 'admin'); ?></li>
				</ul>
				<?php
					$SaventeEchange = new SaventeEchange();
					$tab_echanges = $SaventeEchange->lister_echanges($Savente->id);

					$total_echange = 0;
					foreach($tab_echanges AS $prod) {
						$produit = new Produit();
						$produit->charger_id($prod->ref);

						$produitdesc = new Produitdesc();
						$produitdesc->charger($produit->id);

						if(!$produit->promo) $prix = $produit->prix;
						else $prix = $produit->prix2;

						//$tva = $prod->tva/100;
						//$tva += 1;
						//$prix_ttc = $prix*$tva;
						$prix_ttc = $prix;
						$sous_total = $prod->qte*$prix_ttc;

						$total_echange += $sous_total;

						$declidisp = new Declidisp();
						$declidisp->charger($prod->declidisp);

						$declidispdesc = new Declidispdesc();
						$declidispdesc->charger($prod->declidisp);

						$declinaisondesc = new Declinaisondesc();
						$declinaisondesc->charger($declidisp->declinaison);
						?>
						<ul class="ligne_BlocDescription">
							<li style="width:410px;">
								<?php echo $produit->ref . " - " . str_replace("\n", "<br />", $produitdesc->titre); ?><br/>
								<?php if ($declinaisondesc->titre) echo('- '.$declinaisondesc->titre.' : '.$declidispdesc->titre); ?>
								<?php if ($prod->taille) echo('<br/>- Taille : '.$prod->taille); ?>
								<?php if ($prod->couleur) echo('<br/>- Couleur : '.$prod->couleur); ?>
								<?php if ($prod->carac) echo('<br/>- Détails : '.$prod->carac); ?>
							</li>
							<li style="width:75px;"><?php echo(round($prix_ttc, 2)); ?></li>
							<li style="width:30px;"><?php echo($prod->qte); ?></li>
							<li style="width:30px;"><?php echo(round($sous_total, 2)); ?></li>
						</ul>
						<?php
					}
				?>
				<ul class="ligne_total_BlocDescription">
					<li style="width:400px;"><?php echo trad('Total', 'admin'); ?></li>
					<li><?php echo(round($total_echange, 2)); ?> <?php echo $devise->symbole; ?> T.T.C</li>
				</ul>

			</div><!--bloc_description-->

</div><!--END LEFT COL-->

<!-- bloc colonne de droite -->
<div id="bloc_colonne_droite">
	<form action="<?php echo($_SERVER['PHP_SELF']); ?>" name="formchange" method="post" enctype="multipart/form-data">
		<div class="entete_client">
				<div class="titre"><?php echo trad('BON_RETOUR', 'admin'); ?></div>
		</div>
		<ul class="claire">
				<li><input type="file" name="colis"/></li>
				<li><label for="frais-retour">Frais de retour</label>
						<input type="text" name="frais-retour" id="frais-retour" value="<?php echo($Savente->montant_retour); ?>" /> € TTC</li>
				<li><label for="frais-de-retour">Frais d'échange'</label>
						<input type="text" name="frais-echanges" id="frais-echange" value="<?php echo($Savente->montant_echange); ?>" /> € TTC</li>
		</ul>

		<div class="entete_client" style="margin:10px 0 0 0;">
			<div class="titre"><?php echo trad('STATUT_SAV', 'admin'); ?></div>
			<div class="statut">
				<input type="hidden" name="ref" value="<?php echo($ref); ?>" />
				<select name="statusch" id="statusch" onchange="formchange.submit()" class="form">
				<?php
				$statut = new Statut();
				$query = 'SELECT * FROM '.$statut->table;
				$resul = $statut->query($query);
				while($resul && $row = $statut->fetch_object($resul)) {
					$statutcurdes = new Statutdesc();
					$statutcurdes->charger($row->id);
					$Savente = new Savente();
					$Savente->charger_old($commande->id);
					if($row->id == 1 || $row->id == 3 || $row->id == 4 || $row->id == 5) { // On affiche uniquement les statuts SAV

						if($row->id == $Savente->statut) $selected='selected';
						else $selected='';

						$disabled = '';
						if( ($Savente->statut == 3) && ($row->id == 3 || $row->id == 4) ) {
							$disabled = 'disabled="disabled"';
						} else if( ($Savente->statut == 1) && ($row->id != 4) ) {
							$disabled = 'disabled="disabled"';
						} else if( $Savente->statut == 4 ) {
							//$disabled = 'disabled="disabled"';
						} else if( ($Savente->statut == 5) && ($row->id != 3) ) {
							$disabled = 'disabled="disabled"';
						}

						echo('<option value="'.$row->id.'" '.$disabled.' '.$selected.'>'.
							trad('SAV_'.str_replace(' ','_',$statutcurdes->titre),'admin').'</option>');
					}
				}
				?>
				</select>
			</div>
		</div>
		<!-- fin du bloc statuts -->
	</form>

	<div class="entete_client" style="margin:10px 0 0 0;">
		<div class="titre"><?php echo trad('DOCUMENTS_PDF', 'admin'); ?></div>
	</div>
	<ul class="claire">
		<li class="designation"><?php echo trad('Facture', 'admin'); ?></li>
		<li><a href="../client/pdf/facture.php?ref=<?php echo($commande->ref); ?>" target="_blank"><?php echo trad('Visualiser_format_PDF', 'admin'); ?></a></li>
	</ul>
	<ul class="fonce">
		<li class="designation"><?php echo trad('Bon_livraison', 'admin'); ?></li>
		<li><a href="livraison.php?ref=<?php echo($commande->ref); ?>" target="_blank"><?php echo trad('Visualiser_format_PDF', 'admin'); ?></a></li>
	</ul>
	<!-- fin du bloc pdfs -->

	<div class="entete_client" style="margin:10px 0 0 0;">
		<div class="titre"><?php echo trad('SAV_SOLDE', 'admin'); ?></div>
	</div>
	<ul class="claire"><?php
		if($total_echange > $total_retour) {
			$delta = $total_echange-$total_retour;
			echo('<li>Le client doit : '.$delta.' € T.T.C à la boutique</li>');
		} else {
			$delta = $total_retour-$total_echange;
			echo('<li>La boutique doit : '.$delta.' € T.T.C au client</li>');
		}
	?></ul>
</div>
<!-- fin du bloc colonne de droite -->

	 </div>
	 <?php require_once("pied.php");?>
</div>
</div>

</body>
</html>
