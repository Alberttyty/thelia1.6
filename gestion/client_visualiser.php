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
	require_once("../fonctions/divers.php");


	if(!isset($action)) $action="";
	if(!isset($type)) $type="";

?>
<?php
	switch($action){
		case 'modifier' : modifier($raison, $entreprise, $siret,$intracom, $nom, $prenom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $pays, $telfixe, $telport, $email, $pourcentage, $ref, $type); break;

		case 'supprimer' : supprimer($ref);break;
		case 'supprcmd' : supprcmd($id);
	}
?>

<?php
    function modifier($raison, $entreprise, $siret, $intracom, $nom, $prenom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $pays, $telfixe, $telport, $email, $pourcentage, $ref, $type){

        $client = new Client();
        $client->charger_ref($ref);

        $client->pourcentage = $pourcentage;

        $client->raison = $raison;
        $client->entreprise = $entreprise;
        $client->siret = $siret;
        $client->intracom = $intracom;
        $client->nom = $nom;
        $client->prenom = $prenom;
        $client->adresse1 = $adresse1;
        $client->adresse2 = $adresse2;
        $client->adresse3 = $adresse3;
        $client->cpostal = $cpostal;
        $client->ville = $ville;
        $client->pays = $pays;
        $client->telfixe = $telfixe;
        $client->telport = $telport;

        $client->pourcentage = $pourcentage;
        if($type != "") $client->type=1; else $client->type=0;

        if( filter_var($email, FILTER_VALIDATE_EMAIL) && $email != $client->email && !$client->existe($email)) {
            $client->email = strip_tags($email);
        }

        $client->maj();

        ActionsModules::instance()->appel_module("modcli", $client);

        redirige("client_visualiser.php?ref=" . $ref);

    }

	function supprimer($ref){

		$client = new Client();
		$client->charger_ref($ref);
		$client->delete();

		ActionsModules::instance()->appel_module("supcli", $client);

		redirige("client.php");
	}

	function supprcmd($id) {

		$tempcmd = new Commande();

		if ($tempcmd->charger($id)) $tempcmd->annuler();
	}


	$client = new Client();
	$client->charger_ref($ref);

	$raisondesc = new Raisondesc($client->raison);

	if($client->parrain){
		$parrain = new Client();
		$parrain->charger_id($client->parrain);
	}

	$paysdesc = new Paysdesc();
	$paysdesc->charger($client->pays);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php"); ?>

<script type="text/javascript">

function supprimer(id){
	if(confirm("<?php echo trad('Voulez-vous vraiment annuler cette commande ?', 'admin'); ?>")) {
		location="client_visualiser.php?action=supprcmd&id=" + id + "&ref=<?php echo($ref); ?>";
	}
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
<p><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="client.php" class="lien04"><?php echo trad('Gestion_clients', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04"><?php echo trad('Visualiser', 'admin'); ?></a></p>

<!-- Début de la colonne de gauche -->
<div id="bloc_description" >

<div class="entete_liste_client">
	<div class="titre"><?php echo trad('INFO_CLIENT', 'admin'); ?></div>
	<div class="fonction_valider"><a href="client_modifier.php?ref=<?php echo($client->ref); ?>"><?php echo trad('MODIFIER_COORD_CLIENT', 'admin'); ?></a></div>
</div>
   <table width="100%" cellpadding="5" cellspacing="0" class="tabclient">
    <tr class="claire">
        	<th class="designation" width="290"><?php echo trad('Societe', 'admin'); ?></th>
       		<th><?php echo($client->entreprise); ?></th>
     </tr>
     <tr class="fonce">
       <td class="designation"><?php echo trad('Siret', 'admin'); ?></td>
       <td><?php echo($client->siret); ?></td>
     </tr>
     <tr class="claire">
       <td class="designation"><?php echo trad('Numintracom', 'admin'); ?></td>
       <td><?php echo($client->intracom); ?></td>
     </tr>
     <tr class="fonce">
       <td class="designation"><?php echo trad('Civilite', 'admin'); ?></td>
       <td><?php echo($raisondesc->long); ?></td>
     </tr>
     <tr class="claire">
       <td class="designation"><?php echo trad('Nom', 'admin'); ?></td>
       <td><?php echo($client->nom); ?> </td>
     </tr>
     <tr class="fonce">
       <td class="designation"><?php echo trad('Prenom', 'admin'); ?></td>
       <td><?php echo($client->prenom); ?></td>
     </tr>
     <tr class="claire">
       <td class="designation"><?php echo trad('Adresse', 'admin'); ?></td>
       <td><?php echo($client->adresse1); ?></td>
     </tr>
     <tr class="fonce">
       <td class="designation"><?php echo trad('Adressesuite', 'admin'); ?></td>
       <td><?php echo($client->adresse2); ?></td>
     </tr>
     <tr class="claire">
       <td class="designation"><?php echo trad('Adressesuite', 'admin'); ?> 2</td>
       <td><?php echo($client->adresse3); ?></td>
     </tr>
     <tr class="fonce">
       <td class="designation"><?php echo trad('CP', 'admin'); ?></td>
       <td><?php echo($client->cpostal); ?></td>
     </tr>
     <tr class="claire">
       <td class="designation"><?php echo trad('Ville', 'admin'); ?></td>
       <td><?php echo($client->ville); ?></td>
     </tr>
     <tr class="fonce">
       <td class="designation"><?php echo trad('Pays', 'admin'); ?></td>
       <td><?php echo($paysdesc->titre); ?></td>
     </tr>
     <tr class="claire">
       <td class="designation"><?php echo trad('Telfixe', 'admin'); ?></td>
       <td><?php echo($client->telfixe); ?></td>
     </tr>
     <tr class="fonce">
       <td class="designation"><?php echo trad('Telport', 'admin'); ?></td>
       <td><?php echo($client->telport); ?></td>
     </tr>
     <tr class="claire">
       <td class="designation"><?php echo trad('E-mail', 'admin'); ?></td>
       <td><a href="mailto:<?php echo($client->email); ?>" class="txt_vert_11"><?php echo($client->email); ?> </a> </td>
     </tr>
     <tr class="foncebottom">
       <td class="designation"><?php echo trad('Remise', 'admin'); ?></td>
       <td><?php echo($client->pourcentage); ?> %</td>
     </tr>
     <?php if(isset($parrain)) { ?>
     <tr class="clairebottom">
       <td class="designation"><?php echo trad('Parrain', 'admin'); ?></td>
       <td><a href="client_visualiser.php?ref=<?php echo $parrain->ref ?>" class="txt_vert_11"><?php echo $parrain->prenom . " " . $parrain->nom; ?> </a> </td>
     </tr>
	<?php } ?>
   </table>

<?php
	ActionsAdminModules::instance()->inclure_module_admin("clientvisualiser");
?>

<!-- -->

		<div class="entete_liste_client">
			<div class="titre" style="cursor:pointer" onclick="$('#pliantlistecommandes').show('slow');"><?php echo trad('LISTE_COMMANDES_CLIENT', 'admin'); ?></div>
		</div>
		<div class="blocs_pliants_prod" id="pliantlistecommandes">

	<ul class="ligne1">
		<li class="cellule" style="width:130px;"><?php echo trad('NUM_COMMANDE', 'admin'); ?></li>
		<li class="cellule" style="width:130px;"><?php echo trad('DATE_HEURE', 'admin'); ?></li>
		<li class="cellule" style="width:120px;"><?php echo trad('MONTANT_euro', 'admin'); ?></li>
		<li class="cellule" style="width:90px;"><?php echo trad('STATUT', 'admin'); ?></li>
		<li class="cellule" style="width:60px;"></li>
		<li class="cellule" style="width:20px;"></li>

	</ul>
<?php
  	$i=0;

	$commande = new Commande();
  	$client = new Client();
  	$client->charger_ref($ref);

    $query = "select * from $commande->table where client='" . $client->id . "' order by date desc";
  	$resul = $commande->query($query);

  	while($commande && $cmd = $commande->fetch_object($resul, 'Commande')) {

  		$statutdesc = new Statutdesc();
  		$statutdesc->charger($cmd->statut);

  		$total = $cmd->total(true, true);

		$date = strftime("%d/%m/%Y %H:%M:%S", strtotime($cmd->date));

  		$fond= $i++%2 ? "claire" : "fonce";
  ?>

	<ul class="lignesimple">
		<li class="cellule" style="width:130px;"><?php echo($cmd->ref); ?></li>
		<li class="cellule" style="width:130px;"><?php echo($date); ?></li>
		<li class="cellule" style="width:120px;"><?php echo(formatter_somme($total)); ?></li>
		<li class="cellule" style="width:90px;"><?php echo($statutdesc->titre); ?></li>
		<li class="cellule" style="width:60px;"><a href="commande_details.php?ref=<?php echo($cmd->ref); ?>"><?php echo trad('editer', 'admin'); ?></a></li>
		<?php if ($cmd->statut != Commande::ANNULE) { ?>
			<li class="cellule" style="width:20px;"><a title="<?php echo trad('Annuler cette commande', 'admin'); ?>" href="#" onclick="supprimer('<?php echo($cmd->id); ?>'); return false;"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
		<?php } ?>
	</ul>

<?php } ?>

<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantlistecommandes').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
</div>

<!-- -->

<!-- -->

		<div class="entete_liste_client">
			<div class="titre" style="cursor:pointer" onclick="$('#pliantfilleul').show('slow');"><?php echo trad('LISTE_FILLEULS_CLIENT', 'admin'); ?></div>
		</div>
		<div class="blocs_pliants_prod" id="pliantfilleul">

	<ul class="ligne1">
		<li class="cellule" style="width:160px;"><?php echo trad('NOM', 'admin'); ?></li>
		<li class="cellule" style="width:160px;"><?php echo trad('PRENOM', 'admin'); ?></li>
		<li class="cellule" style="width:155px;"><?php echo trad('E-MAIL', 'admin'); ?></li>
		<li class="cellule" style="width:90px;"></li>
	</ul>

<?php

	$listepar = new Client();

	$query = "select * from $listepar->table where parrain=" . $client->id;
	$resul = $listepar->query($query);

	$i=0;

	while($resul && $parrain = $listepar->fetch_object($resul, 'Client')) {
		if(!($i%2)) $fond=$i++%2 ? "claire" : "fonce";
   ?>
	<ul class="lignesimple">
		<li class="cellule" style="width:160px;"><?php echo $parrain->nom; ?></li>
		<li class="cellule" style="width:160px;"><?php echo $parrain->prenom; ?></li>
		<li class="cellule" style="width:155px;"><a href="mailto:<?php echo $parrain->email ?>"><?php echo $parrain->email; ?></a></li>
		<li class="cellule" style="width:90px;"><a href="client_visualiser.php?ref=<?php echo $parrain->ref ?>"><?php echo trad('editer', 'admin'); ?></a></li>

	</ul>
<?php } ?>


<div class="bloc_fleche" style="cursor:pointer" onclick="$('#pliantfilleul').hide();"><img src="gfx/fleche_accordeon_up.gif" /></div>
</div>

<!-- -->


</div>

</div>
<?php require_once("pied.php");?>
</div>
</div>

</body>
</html>