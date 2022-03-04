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
?>
<?php if(! est_autorise("acces_clients")) exit; ?>
<?php
	if(!isset($action)) $action="";
	if(!isset($page)) $page=0;
?>
<?php

	require_once("liste/client.php");
?>

<?php
	if($action == "supprimer"){

		$tempcli = new Client();
		$tempcli->charger_ref($ref);

		ActionsModules::instance()->appel_module("supcli", $tempcli);

		$tempcli->delete();
	}
?>

<?php
	$client = new Client();


	if($page=="") $page=1;

	$query = "select count(*) from $client->table";
  	$resul = mysql_query($query, $client->link);
  	$num = mysql_result($resul,0);

  	$nbpage = 20;
  	$totnbpage = ceil($num/20);

  	$debut = ($page-1) * 20;

  	if($page>1) $pageprec=$page-1;
  	else $pageprec=$page;

  	if($page<$totnbpage) $pagesuiv=$page+1;
  	else $pagesuiv=$page;
  	if(isset($classement) && $classement != "") $ordclassement = "order by ".$classement;
  	else $ordclassement = "order by nom asc";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php"); ?>
<script src="../lib/jquery/jeditable.js" type="text/javascript"></script>
<script src="../lib/jquery/menu.js" type="text/javascript"></script>

<script type="text/javascript">

	function confirmSupp(ref){
		if(confirm("Voulez-vous vraiment supprimer ce client ?")) location="<?php echo($_SERVER['PHP_SELF']); ?>?action=supprimer&ref=" + ref;
	}

	function tri(order,critere,debut){
		$.ajax({
			type:"GET",
			url:"ajax/triclient.php",
			data : "order="+order+'&critere='+critere+"&debut="+debut,
			success : function(html){
				$("#resul").html(html);
			}
		})
	}
</script>

</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php
	$menu="client";
	require_once("entete.php");
?>

<div id="contenu_int">
      <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04"><?php echo trad('Gestion_clients', 'admin'); ?></a>
    </p>
<div class="entete_liste_client">
	<div class="titre">LISTE DES CLIENTS</div><div class="fonction_ajout"><a href="client_creer.php"><?php echo trad('CREER_CLIENT', 'admin'); ?></a> </div>
</div>
<ul id="Nav">
		<li style="height:25px; width:129px; border-left:1px solid #96A8B5;"><?php echo trad('Num_client', 'admin'); ?></li>
		<li style="height:25px; width:117px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;"><?php echo trad('Création', 'admin'); ?>
			<ul class="Menu">
				<li style="width:267px;"><a href="javascript:tri('ASC','datecrea','<?php echo $debut; ?>')"><?php echo trad('Tri_croissant', 'admin'); ?></a></li>
				<li style="width:267px;"><a href="javascript:tri('DESC','datecrea','<?php echo $debut; ?>')"><?php echo trad('Tri_decroissant', 'admin'); ?></a></li>
				<li style="width:267px;"><a href="javascript:tri('ASC','datecrea','<?php echo $debut; ?>')"><?php echo trad('Tri_par_defaut', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:150px; border-left:1px solid #96A8B5;"><?php echo trad('Societe', 'admin'); ?></li>
		<li style="height:25px; width:250px; border-left:1px solid #96A8B5; background-image: url(gfx/picto_menu_deroulant.gif); background-position:right bottom; background-repeat: no-repeat;"><?php echo trad('Nom', 'admin'); ?> &amp; <?php echo trad('Prenom', 'admin'); ?>
			<ul class="Menu">
				<li style="width:267px;"><a href="javascript:tri('ASC','nom','<?php echo $debut; ?>')"><?php echo trad('Tri_alphabetique', 'admin'); ?></a></li>
				<li style="width:267px;"><a href="javascript:tri('DESC','nom','<?php echo $debut; ?>')"><?php echo trad('Tri_alphabetique_inverse', 'admin'); ?></a></li>
				<li style="width:267px;"><a href="javascript:tri('ASC','nom','<?php echo $debut; ?>')"><?php echo trad('Tri_par_defaut', 'admin'); ?></a></li>
			</ul>
		</li>
		<li style="height:25px; width:117px; border-left:1px solid #96A8B5;"><?php echo trad('Derniere_commande', 'admin'); ?></li>
		<li style="height:25px; width:70px; border-left:1px solid #96A8B5;"><?php echo trad('Montant_commande', 'admin'); ?></li>
		<li style="height:25px; width:47px; border-left:1px solid #96A8B5;"></li>
		<li style="height:25px; width:22px; border-left:1px solid #96A8B5;"><?php echo trad('Suppr', 'admin'); ?></li>
</ul>

<div id="resul">
	<?php
	liste_clients('ASC','nom',$debut);
	?>
</div>

<p id="pages">
	<?php if($page>1){ ?>
	<a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo($pageprec); ?>">Page précédente</a> |
	<?php } ?>
	<?php if($totnbpage > $nbpage){?>
		<?php if($page>1) {?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=1">...</a> | <?php } ?>
		<?php if($page+$nbpage-1 > $totnbpage){ $max = $totnbpage; $min = $totnbpage-$nbpage;} else{$min = $page-1; $max=$page+$nbpage-1; }?>
    <?php for($i=$min; $i<$max; $i++){ ?>
   	 <?php if($page != $i+1){ ?>
 	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo($i+1); ?>&classement=<?php echo($classement); ?>"><?php echo($i+1); ?></a> |
   	 <?php } else {?>
   		  <span class="selected"><?php echo($i+1); ?></span>
   		|
  		  <?php } ?>
    <?php } ?>
		<?php if($page < $totnbpage){?><a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo $totnbpage; ?>">...</a> | <?php } ?>
	<?php }
	else{
		for($i=0; $i<$totnbpage; $i++){ ?>
	    	 <?php if($page != $i+1){ ?>
	  	  		 <a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo($i+1); ?>&classement=<?php echo($classement); ?><?php echo $lien_voir; ?>"><?php echo($i+1); ?></a> |
	    	 <?php } else {?>
	    		 <span class="selected"><?php echo($i+1); ?></span>
	    		 |
	   		  <?php } ?>
	     <?php } ?>
	<?php } ?>


    <?php if($page < $totnbpage){ ?>
    <a href="<?php echo($_SERVER['PHP_SELF']); ?>?page=<?php echo($pagesuiv); ?>">Page suivante</a></p>
	<?php } ?>
</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>
