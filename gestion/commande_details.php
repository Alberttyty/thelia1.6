<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/
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
	if(!isset($statutch)) $statutch="";

	$commande = new Commande();
	$commande->charger_ref($ref);

	$modules = new Modules();
	$modules->charger_id($commande->paiement);

	$devise = new Devise();
	$devise->charger($commande->devise);

	if($statutch) $commande->setStatutAndSave($statutch);

	if (isset($colis) && $colis != "") {
		$commande->colis = $colis;
		$commande->maj();
		ActionsModules::instance()->appel_module("statut", $commande, $commande->statut);
	}
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
				<li style="width:80px; border-left:1px solid #96A8B5;"><?php echo trad('Prix_unitaire', 'admin'); ?></li>
				<li style="width:30px; border-left:1px solid #96A8B5;"><?php echo trad('Qte', 'admin'); ?></li>
				<li style="border-left:1px solid #96A8B5;"><?php echo trad('Total', 'admin'); ?></li>
			</ul>
		<?php
		function liste_parent($parent, $niveau, $commande, $grandParent, $debutLie=0) {
			global $deco, $listePassee;
			$venteprod = new Venteprod();

			$query = "select * from $venteprod->table where commande='$commande->id' AND parent='$parent'";
			$resul = $venteprod->query($query);

			$memFin = "";
			while($resul && $row = $venteprod->fetch_object($resul)) {

				$venteprod->charger($row->id);
				$baseIndentation = 25;
				$paddingIndentation = 5; //base
				$largeurDesignation = 399;
				$indentation = "";

				if($debutLie) {
					$indentation = "╓";
				} elseif($grandParent!=0 && $grandParent==$venteprod->id) {
					$indentation = "╙";
				} elseif($niveau>0) {
					/*for($niv=0;$niv<$niveau;$niv++)
					{
						$indentation .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}*/
					$paddingIndentation += $baseIndentation * $niveau;
					$indentation .= "↳";
				}

				$largeurDesignation -= $paddingIndentation;

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

				if($grandParent!=0 && $grandParent==$venteprod->id) {
					$memFin = "<ul class=\"__COULEUR_FOND__\">
					<li style=\"width:".$largeurDesignation."px;padding-left:".$paddingIndentation."px\">
						$indentation $venteprod->ref - $titrerub - ".str_replace("\n", "<br />", $venteprod->titre)."
					</li>
					<li style=\"width:73px;\">".round($venteprod->prixu, 2)."</li>
					<li style=\"width:23px;\">".$venteprod->quantite."</li>
					<li style=\"width:20px;\">".round($venteprod->quantite*$venteprod->prixu, 2)."</li>
					</ul>";
				} else {
					$deco++;
					?>
					<ul class="<?php echo($fond); ?>">
						<li style="width:<?php echo $largeurDesignation; ?>px; padding-left:<?php echo $paddingIndentation; ?>px;">
							<?php echo $indentation . " " . $venteprod->ref . " - " . $titrerub; ?>
							- <?php echo(str_replace("\n", "<br />", $venteprod->titre)); ?>
						</li>
						<li style="width:73px;"><?php echo(round($venteprod->prixu, 2)); ?></li>
						<li style="width:23px;"><?php echo($venteprod->quantite); ?></li>
						<li style="width:20px;"><?php echo(round($venteprod->quantite*$venteprod->prixu, 2)); ?></li>
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
			$q = "SELECT parent FROM $venteprod->table WHERE commande='$commande->id'";
			if(count($listePassee)>0)
				$q .= " AND id NOT IN(" . implode(',', $listePassee) . ")";
			$q .= " LIMIT 1";

			$r = $venteprod->query($q);
			$nbRestant = $venteprod->num_rows($r);
			if($nbRestant>0) {
				liste_parent($venteprod->get_result($r, 0), 0, $commande, 0, 1);
			}
		} while($nbRestant>0);

		$client = new Client();
		$client->charger_id($commande->client);

		$total = $commande->total();

		$totalremise = $total - $commande->remise;

		$port = $commande->port;
		if($port<0) $port=0;

		$statutdesc = new Statutdesc();
		$statutdesc->charger($commande->statut);

		$time = strtotime($commande->date);

		$dateaff = strftime("%d/%m/%y", $time);
		$heureaff =  strftime("%H:%M:%S", $time);
		?>
		<ul class="ligne_total_BlocDescription">
			<li style="width:392px;"><?php echo trad('Total', 'admin'); ?></li>
			<li><?php echo(round($total, 2)); ?> <?php echo $devise->symbole; ?></li>
		</ul>

<div class="bordure_bottom" style="margin:0 0 10px 0;">
	<div class="entete_liste_client">
		<div class="titre"><?php echo trad('INFO_FACTURE', 'admin'); ?></div>
	</div>
	<ul class="Nav_bloc_description">
		<li style="width:60px;">
			<?php echo trad('Num_Fact', 'admin'); ?>
		</li>
		<li style="width:240px; border-left:1px solid #96A8B5;">
			<?php echo trad('Societe', 'admin'); ?>
		</li>
		<li style="width:150px; border-left:1px solid #96A8B5;">
			<?php echo trad('Nom', 'admin'); ?> &amp; <?php echo trad('Prenom', 'admin'); ?>
		</li>
		<li style="border-left:1px solid #96A8B5;">
			<?php echo trad('Date_Heure', 'admin'); ?>
		</li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li style="width:59px;"><?php echo($commande->facture); ?></li>
		<li style="width:240px;"><?php echo($client->entreprise); ?></li>
		<li style="width:150px;">
			<a href="client_visualiser.php?ref=<?php echo($client->ref); ?>">
				<?php echo($client->nom); ?> <?php echo($client->prenom); ?>
			</a>
		</li>
		<li><?php echo($dateaff . " " . $heureaff); ?></li>
	</ul>
</div>

<?php
	$moduletransport = new Modules();
	$moduletransport->charger_id($commande->transport);

	$moduletransportdesc = new Modulesdesc();
	$moduletransportdesc->charger($moduletransport->nom);
?>

<div class="bordure_bottom" style="margin:0 0 10px 0;">
	<div class="entete_liste_client">
		<div class="titre"><?php echo trad('INFO_TRANSPORT', 'admin'); ?></div>
	</div>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Mode_transport', 'admin'); ?></li>
		<li><?php echo $moduletransportdesc->titre; ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Description'); ?></li>
		<li><?php echo $moduletransportdesc->description; ?></li>
	</ul>
</div>

<div class="bordure_bottom" style="margin:0 0 10px 0;">
	<div class="entete_liste_client">
		<div class="titre"><?php echo trad('INFO_REGLEMENT', 'admin'); ?></div>
	</div>
	<ul class="ligne_claire_BlocDescription" style="background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">
		<li class="designation" style="width:290px; background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">
			<?php echo trad('Type_de_reglement', 'admin'); ?>
		</li>
		<li><?php
			try {
				$tmpobj = ActionsAdminModules::instance()->instancier($modules->nom);
				echo $tmpobj->getTitre();
			} catch (Exception $ex) {
				echo trad('Inconnu', 'admin');
			}
			 ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Ref_transaction', 'admin'); ?></li>
		<li><?php echo($commande->transaction); ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Total_commande_avant_remise', 'admin'); ?></li>
		<li><?php echo(round($total, 2)); ?> <?php echo $devise->symbole; ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Remise', 'admin'); ?></li>
		<li><?php echo(round($commande->remise, 2)); ?> <?php echo $devise->symbole; ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Code_promo', 'admin'); ?></li>
		<li><?php
		$promoutil = new Promoutil();
		if($promoutil->charger_commande($commande->id)){
			$promo = new Promo();
			$promo->charger_id($promoutil->promo);
			if($promoutil->code == $promo->code && $promoutil->type == $promo->type && $promoutil->valeur == $promo->valeur){ ?>
			<a href="promo_modifier.php?id=<?php echo $promoutil->promo; ?>"><?php echo $promoutil->code; ?></a>
			<?php
			} else {
				echo $promoutil->code; ?> (<?php echo $promoutil->valeur; echo($promoutil->type==Promo::TYPE_SOMME)?'€':'%'; ?>)
			<?php
			}
		}
		?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Total_avec_remise', 'admin'); ?></li>
		<li><?php echo(round($totalremise, 2)); ?> <?php echo $devise->symbole; ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Frais_transport', 'admin'); ?></li>
		<li><?php echo(round($port, 2)); ?> <?php echo $devise->symbole; ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Total', 'admin'); ?></li>
		<li><?php echo(round($totalremise + $port, 2)); ?> <?php echo $devise->symbole; ?></li>
	</ul>
</div>

<?php
	$adr = new Venteadr();
	$adr->charger($commande->adrfact);

	$nompays = new Paysdesc();
	$nompays->charger($adr->pays);
?>
<div class="bordure_bottom" style="margin:0 0 10px 0;">
	<div class="entete_liste_client">
		<div class="titre"><?php echo trad('ADRESSE_FACTURATION', 'admin'); ?></div>
	</div>
	<ul class="ligne_claire_BlocDescription" style="background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">
		<li class="designation" style="width:290px; background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">
			<?php echo trad('Societe', 'admin'); ?>
		</li>
		<li><?php echo $adr->entreprise; ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Prenom', 'admin'); ?></li>
		<li><?php echo $adr->prenom; ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Nom', 'admin'); ?></li>
		<li><?php echo $adr->nom; ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Adresse', 'admin'); ?></li>
		<li><?php echo $adr->adresse1;?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Adressesuite', 'admin'); ?></li>
		<li><?php echo $adr->adresse2; ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Complement_adresse', 'admin'); ?></li>
		<li><?php echo $adr->adresse3; ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('CP', 'admin'); ?></li>
		<li><?php echo $adr->cpostal; ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Ville', 'admin'); ?></li>
		<li><?php echo $adr->ville; ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Pays', 'admin'); ?></li>
		<li><?php echo $nompays->titre; ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Telephone', 'admin'); ?></li>
		<li><?php echo $adr->tel; ?></li>
	</ul>
</div>

<?php
	$adr = new Venteadr();
	$adr->charger($commande->adrlivr);

	$nompays = new Paysdesc();
	$nompays->charger($adr->pays);
?>
<div class="bordure_bottom" style="margin:0 0 10px 0;">
	<div class="entete_liste_client">
		<div class="titre"><?php echo trad('ADRESSE_LIVRAISON', 'admin'); ?></div>
	</div>
	<ul class="ligne_claire_BlocDescription" style="background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">
		<li class="designation" style="width:290px; background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">Société</li>
		<li><?php echo $adr->entreprise; ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Prenom', 'admin'); ?></li>
		<li><?php echo $adr->prenom; ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Nom', 'admin'); ?></li>
		<li><?php echo $adr->nom; ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Adresse', 'admin'); ?></li>
		<li><?php echo $adr->adresse1;?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Adressesuite', 'admin'); ?></li>
		<li><?php echo $adr->adresse2; ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Complement_adresse', 'admin'); ?></li>
		<li><?php echo $adr->adresse3; ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('CP', 'admin'); ?></li>
		<li><?php echo $adr->cpostal; ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Ville', 'admin'); ?></li>
		<li><?php echo $adr->ville; ?></li>
	</ul>
	<ul class="ligne_fonce_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Pays', 'admin'); ?></li>
		<li><?php echo $nompays->titre; ?></li>
	</ul>
	<ul class="ligne_claire_BlocDescription">
		<li class="designation" style="width:290px;"><?php echo trad('Telephone', 'admin'); ?></li>
		<li><?php echo $adr->tel; ?></li>
	</ul>
</div>

<?php
	///////////////////////////////////////////////////////////////////////////////
	   ActionsAdminModules::instance()->inclure_module_admin("commandedetails"); //
	///////////////////////////////////////////////////////////////////////////////
?>

</div>
<!-- fin du bloc description -->

<!-- bloc colonne de droite -->
<div id="bloc_colonne_droite">
	<div class="entete_client">
		<div class="titre"><?php echo trad('STATUT_REGLEMENT', 'admin'); ?></div>
		<div class="statut">
			<form action="<?php echo($_SERVER['PHP_SELF']); ?>" name="formchange" method="post">
				<input type="hidden" name="ref" value="<?php echo($ref); ?>" />
				<select name="statutch" id="statutch" onchange="formchange.submit()" class="form">
				<?php
				$statut = new Statut();
				$query = 'SELECT * FROM '.$statut->table;
				$resul = $statut->query($query);
				while($resul && $row = $statut->fetch_object($resul)) {
					$statutcurdes = new Statutdesc();
					$statutcurdes->charger($row->id);
					if($row->id == $statutdesc->statut) $selected='selected';
					else $selected='';
            		if( ($commande->statut==5 || $commande->statut==1) && ($row->id==3 || $row->id==4) ) {
						$disabled='disabled="disabled"';
					} else if ( ($commande->statut==6 || $commande->statut==7 || $commande->statut==9)
							 && ($row->id==1 || $row->id==2 || $row->id==3 || $row->id==5 || $row->id==6 || $row->id==7 || $row->id==9)) {
						$disabled='disabled="disabled"';
					} else { $disabled = ''; }
					?>
					<option value="<?php echo($row->id); ?>" <?php echo($disabled); ?> <?php echo($selected); ?>>
						<?php echo($statutcurdes->titre); ?>
					</option>
				<?php
				}
				?>
				</select>
			</form>
		</div>
	</div>
	<!-- fin du bloc statuts -->
	<div class="entete_client" style="margin:10px 0 0 0;">
		<div class="titre"><?php echo trad('SUIVI_COLIS', 'admin'); ?></div>
	</div>
	<ul class="claire">
		<li class="designation"><?php echo trad('Num_colis', 'admin'); ?></li>
		<li>
			<form action="<?php echo($_SERVER['PHP_SELF']); ?>" name="formcolis" method="post">
				<input type="hidden" name="ref" value="<?php echo($ref); ?>" />
				<input type="text" name="colis" value="<?php echo htmlspecialchars($commande->colis) ?>" /> <input type="submit" value="Valider" />
			</form>
		</li>
	</ul>
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
</div>
<!-- fin du bloc colonne de droite -->

	 </div>
	 <?php require_once("pied.php");?>
</div>
</div>

</body>
</html>
