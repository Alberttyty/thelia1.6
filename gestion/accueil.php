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

require_once("../lib/magpierss/extlib/Snoopy.class.inc");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php require_once("title.php");?></head>

<?php
	$cnx = new Cnx();

	function get_result($query) {
		global $cnx;

		$resul = $cnx->query($query);

		return $resul ? $cnx->get_result($resul, 0, 0) : 0;
	}

	$cond_commande_paye = "statut >= ".Commande::PAYE." and statut <> ".Commande::ANNULE;
	$in_commande_paye = "select id from ".Commande::TABLE." where $cond_commande_paye";

	$nbclient = get_result("select count(*) as nb from ".Client::TABLE);
	$nbproduit = get_result("select count(*) as nb from ".Produit::TABLE);
	$nbcmdinstance = get_result("select count(*) as nb from ".Commande::TABLE." where statut < ".Commande::TRAITEMENT);
	$nbcmdtraitement = get_result("select count(*) as nb from ".Commande::TABLE." where statut = ".Commande::TRAITEMENT);
	$nbcmdlivree = get_result("select count(*) as nb from ".Commande::TABLE." where statut = ".Commande::EXPEDIE);
	$nbproduitenligne = get_result("select count(*) as nb from ".Produit::TABLE." where ligne=1");

	$nbproduithorsligne = $nbproduit-$nbproduitenligne;

	$port = get_result("select sum(port) as totport from ".Commande::TABLE." where $cond_commande_paye");

	$ca  = round(get_result("SELECT sum(quantite*prixu) as ca FROM ".Venteprod::TABLE." where commande in ($in_commande_paye)"), 2);
	$ca += get_result("SELECT sum(port) as ca FROM ".Commande::TABLE." where id in ($in_commande_paye)");
	$ca -= get_result("SELECT sum(remise) as ca FROM ".Commande::TABLE." where id in ($in_commande_paye)");

	$casf = $ca - $port;

	$nbCommande = get_result("SELECT count(*) as nbCommande FROM ".Commande::TABLE." where $cond_commande_paye");

	if($nbCommande > 0)
	$panierMoyen = round(($ca/$nbCommande),2);
	else
	$panierMoyen = 0;

	// La liste des commandes du mois courant
	$query = "select id from ".Commande::TABLE." where datefact like '".date("Y")."-".date("m")."-%%' and $cond_commande_paye";

	$resul = $cnx->query($query);

	$list = array();

	while($resul && $row = $cnx->fetch_object($resul)) {
		$list[] = $row->id;
	}

	if (count($list) == 0) $list = '0';
	else $list = implode(',', $list);

	$camois  = round(get_result("SELECT sum(quantite*prixu) as camois FROM ".Venteprod::TABLE." where commande in ($list)"), 2);
	$camois += get_result("SELECT sum(port) as port FROM ".Commande::TABLE." where id in ($list)");
	$camois -= get_result("SELECT sum(remise) as remise FROM ".Commande::TABLE." where id in ($list)");

	$nbcmdannulee = get_result("select count(*) as nbcmdannulee from ".Commande::TABLE." where statut = ".Commande::ANNULE);

	$nbrubrique = get_result("select count(id) as nbRubrique from ".Rubrique::TABLE);

	// Dernière version
	$snoopy = new Snoopy();

	if($snoopy->fetch("http://thelia.net/version.php")) {
		$versiondispo = trim($snoopy->results);
		if(! preg_match("/^[0-9.]*$/", $versiondispo))
		$versiondispo = "";
	}
	else {
		$versiondispo = "";
	}
?>

<body>
<div id="wrapper">
	<div id="subwrapper">
		<?php $menu = "accueil"; require_once("entete.php"); ?>

		<div id="contenu_int">
			 	<?php if(est_autorise("acces_commandes")) { ?>
				<img src="graph.php" id="graph_accueil" alt="-" />
				<?php } ?>

				<?php ActionsAdminModules::instance()->inclure_module_admin("accueil"); ?>

				<div id="bloc_informations">
				<?php if(est_autorise("acces_clients")||est_autorise("acces_catalogue")||est_autorise("acces_commandes")) { ?>
				<ul>
					<li class="entete"><?php echo trad('INFORMATIONS_SITE', 'admin'); ?></li>

					 <?php if(est_autorise("acces_clients")){ ?>
					<li class="lignetop" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Clients', 'admin'); ?></li>
					<li class="lignetop" style="width:72px;"><?php echo($nbclient); ?></li>
					<?php } ?>

					<?php if(est_autorise("acces_catalogue")){ ?>
					<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Rubriques', 'admin'); ?></li>
					<li class="fonce" style="width:72px;"><?php echo($nbrubrique); ?></li>
					<li class="claire" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Produits', 'admin'); ?></li>
					<li class="claire" style="width:72px;"><?php echo($nbproduit); ?></li>
					<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Produits_en_ligne', 'admin'); ?></li>
					<li class="fonce" style="width:72px;"><?php echo($nbproduitenligne); ?></li>
					<li class="claire" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Produits_hors_ligne', 'admin'); ?></li>
					<li class="claire" style="width:72px;"><?php echo($nbproduithorsligne); ?></li>
					<?php } ?>

					<?php if(est_autorise("acces_commandes")){ ?>
					<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Commandes', 'admin'); ?></li>
					<li class="fonce" style="width:72px;"><?php echo($nbCommande); ?></li>
					<li class="claire" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Commandes_en_instance', 'admin'); ?></li>
					<li class="claire" style="width:72px;"><?php echo($nbcmdinstance); ?></li>
					<li class="fonce" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Commandes_en_traitement', 'admin'); ?></li>
					<li class="fonce" style="width:72px;"><?php echo($nbcmdtraitement); ?></li>
					<li class="lignebottom" style="width:222px; background-color:#9eb0be;"><?php echo trad('Commandes_annulees', 'admin'); ?></li>
					<li class="lignebottom" style="width:72px;"><?php echo($nbcmdannulee); ?></li>
					<?php } ?>
				</ul>
				<?php } ?>

				<?php if(est_autorise("acces_commandes")){ ?>
				<ul>
					<li class="entete"><?php echo trad('STATISTIQUES_DE_VENTE', 'admin'); ?></li>
					<li class="lignetop" style="width:202px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('CA_TTC', 'admin'); ?></li>
					<li class="lignetop" style="width:92px;"><?php echo(round($ca, 2)); ?> €</li>
					<li class="fonce" style="width:202px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('CA_hors_frais_de_port', 'admin'); ?></li>
					<li class="fonce" style="width:92px;"><?php echo(round($casf, 2)); ?> €</li>
					<li class="claire" style="width:202px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('CA_mois_en_cours', 'admin'); ?></li>
					<li class="claire" style="width:92px;"><?php echo(round($camois, 2)); ?> €</li>
					<li class="lignebottomfonce" style="width:202px; background-color:#9eb0be;"><?php echo trad('Panier_moyen', 'admin'); ?></li>
					<li class="lignebottomfonce" style="width:92px;"><?php echo $panierMoyen; ?> €</li>
				</ul>
				<?php } ?>

				<ul>
					<li class="entete" ><?php echo trad('INFOS_ADMIN', 'admin'); ?></li>
					<li class="lignetop" style="width:222px; background-color:#9eb0be;border-bottom: 1px dotted #FFF;"><?php echo trad('Version_en_cours', 'admin'); ?></li>
					<li class="lignetop" style="width:72px;">V<?php echo rtrim(preg_replace("/(.)/", "$1.", Variable::lire('version')), "."); ?></li>
					<li class="lignebottom" style="width:222px; background-color:#9eb0be;"><?php echo trad('Assistance', 'admin'); ?></li>
					<li class="lignebottom" style="width:72px;"><a href="mailto:assistance@pixel-plurimedia.fr" target="_blank"><?php echo trad('cliquer_ici', 'admin'); ?></a></li>
				</ul>
			</div>
		</div>

		<?php require_once("pied.php");?>
	</div>
</div>
</body>
</html>
