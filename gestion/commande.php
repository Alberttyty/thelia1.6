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
require_once("../classes/Cnx.class.php");

if(! est_autorise("acces_commandes")) exit; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	require_once("title.php");
?>
<script src="../lib/jquery/menu.js" type="text/javascript"></script>
<script type="text/javascript">

	function tri(order,critere){
		$.ajax({
			type:"GET",
			url:"ajax/tricommande.php",
			data : 'order='+order+'&critere='+critere,
			success : function(html){
				$("#resul").html(html);
			}
		})
	}

	function supprimer(id){
		if(confirm("<?php echo trad('Voulez-vous vraiment annuler cette commande ?', 'admin'); ?>")) location="commande.php?action=supprimer&id=" + id;
	}
</script>
</head>
<?php
require_once("../fonctions/divers.php");
require_once("liste/commande.php");

if(!isset($action)) $action="";
if(!isset($client)) $client="";
if(!isset($page)) $page=0;
if(!isset($classement)) $classement="";

if($action == "supprimer"){
	$tempcmd = new Commande();
	if ($tempcmd->charger($id)) $tempcmd->annuler();
}
	
// STATUT COMMANDES
if(empty($_GET['statut'])) {
   	$_GET['statut'] = Commande::NONPAYE . "," . Commande::PAYE . "," . Commande::TRAITEMENT;
}
if ($_GET['statut'] == '*') {
    $search = '';
} else if($_GET['statut'] != '') {
	$statut = $_GET['statut'];
    $search = 'AND statut IN('.$statut.')';
}
	
// SAV STATUS
if(empty($_GET['status'])) {
   	$_GET['status'] = Commande::NONPAYE.','.Commande::TRAITEMENT;
}
if ($_GET['status'] == '*') {
    $recherche = '';
} else if($_GET['status'] != '') {
	$status = $_GET['status'];
    $recherche = 'AND statut IN('.$status.')';
}

/**/
if($client != '') $search .= ' AND client="'.$client.'"';
$commande = new Commande();

if($page=="") $page=1;

$query = 'SELECT COUNT(*) FROM '.$commande->table.' WHERE 1 '.$search;
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

if($classement == 'client') {
	$critere = 'client';
	$order = 'ASC';
} else if($classement == 'statut') {
	$critere = 'statut';
	$order = 'ASC';
} else {
	$critere = 'date';
	$order = 'DESC';
}
?>
<body>
<div id="wrapper">
	<div id="subwrapper">
<?php
	$menu="commande";
	require_once("entete.php");
?>

<div id="contenu_int">
    <p align="left">
		<a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a>
		<img src="gfx/suivant.gif" width="12" height="9" border="0" />
		<a href="#" class="lien04"><?php echo trad('Gestion_commandes', 'admin'); ?></a>
    </p>
	<div class="entete_liste_client">
		<div class="titre">
			<?php echo trad('LISTE_COMMANDES', 'admin'); ?>
		</div>
		<div class="fonction_ajout">
			<a href="commande_creer.php"><?php echo trad('CREER_COMMANDE', 'admin'); ?></a>
		</div>
	</div>
	<ul id="Nav">
		<li style="height:25px; width:149px; border-left:1px solid #96A8B5;">
			<?php echo trad('Num_commande', 'admin'); ?>
		</li>
		<li style="height:25px; width:111px; border-left:1px solid #96A8B5;">
			<?php echo trad('Date_Heure', 'admin'); ?>
		</li>
		<li style="height:25px; width:207px; border-left:1px solid #96A8B5;">
			<?php echo trad('Societe', 'admin'); ?>
		</li>
		<li style="height:25px; width:207px; border-left:1px solid #96A8B5;">
			<?php echo trad('Nom', 'admin'); ?> &amp; <?php echo trad('Prenom', 'admin'); ?>
		</li>
		<li style="height:25px; width:66px; border-left:1px solid #96A8B5;">
			<?php echo trad('Montant', 'admin'); ?>
		</li>
		<li style="height:25px; width:77px; border-left:1px solid #96A8B5;
			background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;">
			<?php echo trad('Statut', 'admin'); ?>
			<ul class="Menu">
			 <?php
       	 		$statut = new Statut();
       	 		$query_stat = "select * from $statut->table";
       	 		$resul_stat = $statut->query($query_stat);
       	 		while($resul_stat && $row_stat = $statut->fetch_object($resul_stat)){
       	 			$statutdesc = new Statutdesc();
       	 			$statutdesc->charger($row_stat->id);
       	 		?>
					<li style="width:84px;">
						<a href="commande.php?statut=<?php echo $row_stat->id; ?>" name="<?php echo $row_stat->id; ?>"><?php echo $statutdesc->titre; ?></a>
					</li>
				<?php
       	 		}
       	 		?>
				<li style="width:84px;"><a href="commande.php">En cours</a></li>
				<li style="width:84px;"><a href="commande.php?statut=*">Toutes</a></li>
			</ul>
		</li>
		<li style="height:25px; width:47px; border-left:1px solid #96A8B5;"></li>
		<li style="height:25px; width:42px; border-left:1px solid #96A8B5;">
			<?php echo trad('Annuler', 'admin'); ?>
		</li>
	</ul>

<div id="resul">
 	<?php lister_commandes($critere, $order, $debut, $nbres, $search); ?>
</div>

<p id="pages">
<?php if($page > 1){ ?>
   <a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo($pageprec); ?>&statut=<?php echo $_GET['statut']; ?>" >Page précédente</a> |
	<?php } ?>
	<?php if($totnbpage > $nbpage){?>
		<?php if($page>1) {?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=1&statut=<?php echo $_GET['statut']; ?>">...</a> | <?php } ?>
		<?php if($page+$nbpage-1 > $totnbpage){ $max = $totnbpage; $min = $totnbpage-$nbpage;} else{$min = $page-1; $max=$page+$nbpage-1; }?>
     <?php for($i=$min; $i<$max; $i++){ ?>
    	 <?php if($page != $i+1){ ?>
  	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo($i+1); ?>&classement=<?php echo($classement); ?>&statut=<?php echo $_GET['statut']; ?>" ><?php echo($i+1); ?></a> |
    	 <?php } else {?>
    		 <span class="selected"><?php echo($i+1); ?></span>
    		 |
   		  <?php } ?>
     <?php } ?>
		<?php if($page < $totnbpage){?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo $totnbpage; ?>&statut=<?php echo $_GET['statut']; ?>">...</a> | <?php } ?>
	<?php }
	else{
		for($i=0; $i<$totnbpage; $i++){ ?>
	    	 <?php if($page != $i+1){ ?>
	  	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo($i+1); ?>&statut=<?php echo $_GET['statut']; ?><?php echo $lien_voir; ?>"><?php echo($i+1); ?></a> |
	    	 <?php } else {?>
	    		 <span class="selected"><?php echo($i+1); ?></span>
	    		|
	   		  <?php } ?>
	     <?php } ?>
	<?php } ?>
     <?php if($page < $totnbpage){ ?>
<a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo($pagesuiv); ?>&statut=<?php echo $_GET['statut']; ?>">Page suivante</a></p>
	
	<?php }
	/**
	 * FIN LISTE COMMANDE EN COURS
	 * => AFFICHAGE COMMANDES SERVICE APRES-VENTE (statut = sav_old)
	 */
	$mod_savente = new Cnx();
	$mod_savente_query = 'SELECT commande_old FROM commande_sav';
	$mod_savente_actif = $mod_savente->query_liste($mod_savente_query);
	if($mod_savente_actif[0]) {
?>
	<div class="entete_liste_client">
		<div class="titre">
			<?php echo trad('LISTE_SAV', 'admin'); ?>
		</div>
	</div>
	<ul id="Nav">
		<li style="height:25px; width:149px; border-left:1px solid #96A8B5;">
			<?php echo trad('Num_commande', 'admin'); ?>
		</li>
		<li style="height:25px; width:127px; border-left:1px solid #96A8B5;">
			<?php echo trad('Date_Heure', 'admin'); ?>
		</li>
		<li style="height:25px; width:207px; border-left:1px solid #96A8B5;">
			<?php echo trad('Societe', 'admin'); ?>
		</li>
		<li style="height:25px; width:207px; border-left:1px solid #96A8B5;">
			<?php echo trad('Nom', 'admin'); ?> &amp; <?php echo trad('Prenom', 'admin'); ?>
		</li>
		<li style="height:25px; width:66px; border-left:1px solid #96A8B5;">
			<?php echo trad('Montant', 'admin'); ?>
		</li>
		<li style="height:25px; width:117px; border-left:1px solid #96A8B5; border-right:1px solid #96A8B5;
			background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;">
			<?php echo trad('Statut', 'admin'); ?>
			<ul class="Menu">
			 <?php
       	 		$statut = new Statut();
       	 		$query_stat = 'SELECT * FROM '.$statut->table;
       	 		$resul_stat = $statut->query($query_stat);
       	 		while($resul_stat && $row_stat = $statut->fetch_object($resul_stat)){
       	 			$statutdesc = new Statutdesc();
       	 			$statutdesc->charger($row_stat->id);
					if($row_stat->id == 1 || $row_stat->id == 3 || $row_stat->id == 4 || $row_stat->id == 5) { // On affiche uniquement les statuts SAV
       	 		?>
					<li style="width:180px;">
						<a href="commande.php?status=<?php echo $row_stat->id; ?>" name="<?php echo $row_stat->id; ?>">
							<?php echo(trad('SAV_'.str_replace(' ','_',$statutdesc->titre),'admin')); ?>
						</a>
					</li>
				<?php
					}
       	 		}
       	 		?>
			</ul>
		</li>
	</ul>

<div id="resul">
 	<?php lister_sav($critere, $order, $debut, $nbres, $recherche);	?>
</div>
<?php
	}
?>

</div>

<?php require_once("pied.php"); ?>
</div>
</div>
</body>
</html>