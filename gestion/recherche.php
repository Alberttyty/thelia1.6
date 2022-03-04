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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");?>
<script src="../lib/jquery/jeditable.js" type="text/javascript"></script>
<script src="../lib/jquery/menu.js" type="text/javascript"></script>

<script type="text/javascript">
function supprimer(id){
	if(confirm("Voulez-vous vraiment supprimer cette commande ?")) location="commande.php?action=supprimer&id=" + id;
}

function supprimer_produit(ref, parent){
  ref=encodeURIComponent(ref);
	if(confirm("Voulez-vous vraiment supprimer ce produit ?")) location="produit_modifier.php?ref=" + ref + "&action=supprimer&parent=" + parent;
}

function supprimer_rubrique(id, parent){
	if(confirm("Voulez-vous vraiment supprimer cette rubrique et tout son contenu (produits et sous-rubriques) ?")) location="rubrique_modifier.php?id=" + id + "&action=supprimer&parent=" + parent;
}
</script>
<?php $menu=""; ?>
</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php require_once("entete.php"); ?>

<div id="contenu_int">
    <p><a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">Résultats de la recherche </a>
    </p>

<?php if(est_autorise("acces_clients")) { ?>

<div class="entete_general">
	<div class="titre">RESULTATS CLIENT</div>
</div>
<ul id="Nav">
		<li style="width:160px;">N&deg; de client</li>
		<li style="width:200px; border-left:1px solid #96A8B5;">Nom</li>
		<li style="width:200px; border-left:1px solid #96A8B5;">Prénom</li>
		<li style="width:305px; border-left:1px solid #96A8B5;">E-mail</li>
		<li style="width:50px; border-left:1px solid #96A8B5;">&nbsp;</li>
</ul>
    <?php
    	$i=0;
  	$client = new Client();

  	$motcle = $client->escape_string(trim($motcle));

 	$search="and nom like \"%$motcle%\" or prenom like \"%$motcle%\" or ville like \"%$motcle%\" or email like \"%$motcle%\"";

 	$query = "SELECT * FROM $client->table WHERE 1 $search";
  	$cliresul = mysql_query($query, $client->link);
  	$clilist="";
   	while($row = mysql_fetch_object($cliresul)) {
  		$clilist .= "'$row->id', ";

			if(!($i%2)) $fond="ligne_claire_rub";
  		else $fond="ligne_fonce_rub";
  		$i++;

  	?>
	  	<ul class="<?php echo($fond); ?>">
				<li style="width:152px;"><a href="client_visualiser.php?ref=<?php echo($row->ref); ?>"><?php echo($row->ref); ?></a></li>
				<li style="width:193px;"><?php echo($row->nom); ?></li>
				<li style="width:193px;"><?php echo($row->prenom); ?></li>
				<li style="width:298px;"><a href="mailto:<?php echo($row->email); ?>"><?php echo($row->email); ?></a></li>
				<li><a href="client_visualiser.php?ref=<?php echo($row->ref); ?>">éditer</a></li>
			</ul>

 		<?php
		}

		$clilist = substr($clilist, 0, strlen($clilist)-2);
 }

 if(est_autorise("acces_commandes")) { ?>
 	<div class="entete_general" style="margin:10px 0 0 0">
		<div class="titre">RESULTATS COMMANDE</div>
	</div>
	<ul id="Nav">
			<li style="width:160px;">N&deg; de commande</li>
			<li style="width:200px; border-left:1px solid #96A8B5;">Date</li>
			<li style="width:200px; border-left:1px solid #96A8B5;">Nom</li>
			<li style="width:200px; border-left:1px solid #96A8B5;">Montant</li>
			<li style="width:100px; border-left:1px solid #96A8B5;">Statut</li>
			<li style="width:20px; border-left:1px solid #96A8B5;">Suppr.</li>
	</ul>
    <?php
  	$search="";

  	if($clilist!="") $search .= "where client in ($clilist) or ";
  	else $search .= "where client and  ";
  	$commande = new Commande();

  	$i=0;
  	$jour = substr($motcle, 0, 2);
  	$mois = substr($motcle, 3,2);
  	$annee = substr($motcle, 6);

		$ladate = "$annee-$mois-$jour";
		$ladate = str_replace("??", "%", $ladate);

  	$search .= " ref like '%$motcle%' or date like '$ladate'";

   	$query = "select * from $commande->table $search";
  	$resul = mysql_query($query, $commande->link);

  	$venteprod = new Venteprod();

  	while($row = mysql_fetch_object($resul)) {

  		$client = new Client();
  		$client->charger_id($row->client);

  		$statutdesc = new Statutdesc();
  		$statutdesc->charger($row->statut);

  		$query2 = "SELECT sum(prixu*quantite) as total FROM $venteprod->table where commande='$row->id'";
  		$resul2 = mysql_query($query2, $venteprod->link);
  		$total = round(mysql_result($resul2, 0, "total"), 2);

		$port = $row->port;
		$total -= $row->remise;
		$total += $port;
		if($total<0) $total = 0;

  		$jour = substr($row->date, 8, 2);
  		$mois = substr($row->date, 5, 2);
  		$annee = substr($row->date, 0, 4);

  		$heure = substr($row->date, 11, 2);
  		$minute = substr($row->date, 14, 2);
  		$seconde = substr($row->date, 17, 2);

  		if(!($i%2)) $fond="ligne_claire_rub";
  		else $fond="ligne_fonce_rub";
  		$i++;
  ?>
  		<ul class="<?php echo($fond); ?>">
				<li style="width:152px;"><a href="commande_details.php?ref=<?php echo($row->ref); ?>"><?php echo($row->ref); ?></a></li>
				<li style="width:193px;"><?php echo($jour . "/" . $mois . "/" . $annee . " " . $heure . ":" . $minute . ":" . $seconde); ?></li>
				<li style="width:193px;"><a href="client_visualiser.php?ref=<?php echo($client->ref); ?>"><?php echo($client->nom . " " . $client->prenom); ?></a></li>
				<li style="width:193px;"><?php echo($total); ?></li>
				<li style="width:93px;"><?php echo($statutdesc->titre); ?></li>
				<li><a href="#" onclick="supprimer('<?php echo($row->id); ?>')"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
			</ul>

	 <?php
		}
	}

 	if(est_autorise("acces_catalogue")) { ?>
 	<div class="entete_general" style="margin:10px 0 0 0">
		<div class="titre">RESULTATS PRODUITS</div>
	</div>
	<ul id="Nav">
		<li style="width:160px;">Référence</li>
		<li style="width:408px; border-left:1px solid #96A8B5;">Titre</li>
		<li style="width:308px; border-left:1px solid #96A8B5;">Prix</li>
		<li style="width:20px; border-left:1px solid #96A8B5;">Suppr.</li>
	</ul>

  <?php
  	$i=0;
  	$search="";

  	$search .= "and ref like '%$motcle%'";

  	$produit = new Produit();

   	$query = "select * from $produit->table where 1 $search";
  	$resul = mysql_query($query, $produit->link);

  	$produitdesc = new Produitdesc();

 		$prodlist="";

   	while($row = mysql_fetch_object($resul)){
		 $prodlist .= "'$row->id', ";
 	 }

  	$prodlist = substr($prodlist, 0, strlen($prodlist)-2);

  	$search="";

  	$search .= "and titre like '%$motcle%' or description like '%$motcle%'";

  	$produit = new Produit();

   	$query = "select * from $produitdesc->table where 1 $search";
  	$resul = mysql_query($query, $produitdesc->link);
	if(mysql_num_rows($resul) && $prodlist!="") $prodlist .= ",";

  	$produitdesc = new Produitdesc();

 	$num = 0;

   	while($row = mysql_fetch_object($resul)){
   		$num++;
		 $prodlist .= "'$row->produit', ";
 	 }

	if( substr($prodlist, strlen($prodlist)-2, 1) == ",")
 		$prodlist = substr($prodlist, 0, strlen($prodlist)-2);

	if($num == 1) $prodlist .= "'";

	if($prodlist == "") $search = "where 0";
	else $search = " where id in ($prodlist)";

   	$query = "select * from $produit->table $search";
  	$query = str_replace("'')", "')", $query);
	$resul = mysql_query($query, $produitdesc->link);

  	while($row = mysql_fetch_object($resul)){

  		$produitdesc->charger($row->id);
  	if(!($i%2)) $fond="ligne_claire_rub";
  		else $fond="ligne_fonce_rub";
  		$i++;

  ?>
  <ul class="<?php echo($fond); ?>">
	<li style="width:152px;"><a href="produit_modifier.php?ref=<?php echo(urlencode($row->ref)); ?>&rubrique=<?php echo($row->rubrique); ?>"><?php echo($row->ref); ?></a></li>
	<li style="width:400px;"><?php echo($produitdesc->titre); ?></li>
	<li style="width:303px;"><?php echo($row->prix); ?></li>
	<li style="width:20px;"><a href="javascript:supprimer_produit('<?php echo($row->ref); ?>','<?php echo($row->rubrique); ?>')"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
</ul>
<?php }
	}

	ActionsAdminModules::instance()->inclure_module_admin("recherche");
?>

</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>
