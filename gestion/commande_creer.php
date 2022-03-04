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
require_once("../fonctions/divers.php");

if(! est_autorise("acces_commandes")) exit;

if(isset($_POST["action"]) && $_POST["action"] == "ajouter") {
	$total = 0;
	$poids = 0;

	$modules = new Modules();
	$modules->charger_id($type_paiement);

	$client = new Client();
	$client->charger_ref($id_client);

	$nbart = $_SESSION["commande"]->nbart;
	$sessionventeprod = $_SESSION["commande"]->venteprod;

	$commande = new Commande();
	$commande->client = $client->id;
	$commande->transport = $type_livraison;
	$commande->paiement = $type_paiement;
	$commande->statut=Commande::NONPAYE;

	$adr = new Venteadr();
	$adr->raison = $client->raison;
	$adr->entreprise = $client->entreprise;
	$adr->nom = $client->nom;
	$adr->prenom = $client->prenom;
	$adr->adresse1 = $client->adresse1;
	$adr->adresse2 = $client->adresse2;
	$adr->adresse3 = $client->adresse3;
	$adr->cpostal = $client->cpostal;
	$adr->ville = $client->ville;
	$adr->tel = $client->telfixe . "  " . $client->telport;
	$adr->pays = $client->pays;
	$adrcli = $adr->add();
	$commande->adrfact = $adrcli;
	$commande->adrlivr = $adrcli;

	$commande->facture = 0;

	$commande->lang = ActionsLang::instance()->get_id_langue_courante();
                
    $devise = ActionsDevises::instance()->get_devise_courante();
	$commande->devise = $devise->id;
	$commande->taux = $devise->taux;

	$idcmd = $commande->add();
	$commande->charger($idcmd);

	for($i=0;$i<$nbart;$i++){
		$produit = new Produit();
		$venteprod = new Venteprod();

		if($produit->charger($sessionventeprod[$i]->ref)){
			$produit->stock -=$sessionventeprod[$i]->quantite;
			$poids += $produit->poids;
			$produit->maj();
		}

		$venteprod->ref = $sessionventeprod[$i]->ref;
		$venteprod->titre = $sessionventeprod[$i]->titre;
		$venteprod->quantite = $sessionventeprod[$i]->quantite;
		$venteprod->tva = $sessionventeprod[$i]->tva;
		$venteprod->prixu = $sessionventeprod[$i]->prixu;
		$venteprod->commande = $idcmd;
		$venteprod->add();

		$total += $venteprod->prixu * $venteprod->quantite;

	}
	$commande->remise = 0;
	if($client->pourcentage>0) $commande->remise = $total * $client->pourcentage / 100;
	if($remise != "") $commande->remise+=$remise;

	$commande->transaction = genid($commande->id, 6);
	$commande->port = $fraisport;
	$commande->maj();

	ActionsModules::instance()->appel_module("aprescommande", $commande);

	// Appeler la fonction mail() du module
	try {
		$module = ActionsModules::instance()->instancier($modules->nom);

		$module->mail($commande);
	} catch (Exception $e) { }

	redirige("commande_details.php?ref=".$commande->ref);
}

$_SESSION["commande"] = "";
$_SESSION["commande"]->nbart = 0;
$_SESSION["commande"]->venteprod = array();
//$_SESSION["commande"]->commande = new Commande();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	require_once("title.php");
?>
<script type="text/javascript" src="../lib/jquery/thickbox.js"></script>
<link rel="stylesheet" href="../lib/jquery/thickbox.css" type="text/css" media="screen" />

<script type="text/javascript">
function lookup(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		$.get("listecli.php", {queryString: ""+inputString+""}, function(data){
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
}// lookup


function fill(str) {
	if (str != "") {
		var tableau = str.split("|");
		$('#inputstring').val(tableau[1]);
		$('#id_client').val(tableau[0]);
		setTimeout("$('#suggestions').hide();", 200);
	} else {
		$('#inputstring').val(str);
		setTimeout("$('#suggestions').hide();", 200);
	}
}

function creercli() {
	if(document.getElementById("raison").value != "" &&
	document.getElementById("nom").value != "" &&
	document.getElementById("prenom").value != "" &&
	document.getElementById("adresse1").value != "" &&
	document.getElementById("cpostal").value != "" &&
	document.getElementById("ville").value != "" &&
	document.getElementById("email1").value != "" &&
	document.getElementById("email2").value != "") {

		if(document.getElementById("email1").value == document.getElementById("email2").value) {
			args = "";
			args += "&raison=" + document.getElementById("raison").value;
			args += "&nom=" + document.getElementById("nom").value;
			args += "&prenom=" + document.getElementById("prenom").value;
			args += "&adresse1=" + document.getElementById("adresse1").value;
			args += "&cpostal=" + document.getElementById("cpostal").value;
			args += "&ville=" + document.getElementById("ville").value;
			args += "&email1=" + document.getElementById("email1").value;
			args += "&email2=" + document.getElementById("email2").value;
			args += "&entreprise=" + document.getElementById("entreprise").value;
			args += "&siret=" + document.getElementById("siret").value;
			args += "&intracom=" + document.getElementById("intracom").value;
			args += "&adresse2=" + document.getElementById("adresse2").value;
			args += "&adresse3=" + document.getElementById("adresse3").value;
			args += "&pays=" + document.getElementById("pays").value;
			args += "&telfixe=" + document.getElementById("telfixe").value;
			args += "&telport=" + document.getElementById("telport").value;
			args += "&remise=" + document.getElementById("remise").value;
			args += "&type=" + document.getElementById("type").value;
			//ajax
			$.ajax({
				type:'GET',
				url:"ajoutcli.php",
				data:'action=ajouter'+args,
				success:function(html){
					$('#nclient').html(html)
				}
			})
			tb_remove();
		} else {
			alert("vérifier le mail");
		}

	} else {
		alert("vérifier les champs obligatoires");
	}
}

function verifref(){
	var ref = document.getElementById("ref").value;
	$.ajax({
		type:'GET',
		url:"verifref.php",
		data:"ref="+ref,
		success:function(html){
			$('#titre').val(html.split("|")[0]);
			$('#prixu').val(html.split("|")[1]);
			$('#tva').val(html.split("|")[2]);
			document.getElementById("qtite").value="";
		}
	})
}

function addcom(){
	$.ajax({
		type:'GET',
		url:'addcom.php',
		data:"ref="+document.getElementById('ref').value+"&titre="+document.getElementById('titre').value+"&prixu="+document.getElementById('prixu').value+"&tva="+document.getElementById('tva').value+"&qtite="+document.getElementById('qtite').value,
		success:function(html){
			$('#listecom').html(html);
			document.getElementById("prod").reset();
		}
	})
}

function valid() {
	if(document.getElementById('id_client').value != "" && document.getElementById('port') != ""){
		document.getElementById('formulaire').submit();
	} else{
		alert("vérifier le client et les frais de port");
	}
}
</script>
</head>
<body>

<div id="wrapper">
	<div id="subwrapper">

<?php
	$menu="commande";
	require_once("entete.php");
?>

<div id="contenu_int">
	<p>
		<a href="accueil.php" class="lien04">
			<?php echo trad('Accueil', 'admin'); ?>
		</a>
		<img src="gfx/suivant.gif" width="12" height="9" border="0" />
		<a href="commande.php" class="lien04">
			<?php echo trad('Gestion_commandes', 'admin'); ?>
		</a>
    </p>
    <!-- Début de la colonne de gauche -->
	<div id="bloc_description">
		<div class="bordure_bottom">
			<div class="entete_liste_client">
				<div class="titre"><?php echo trad('CREATION_COMMANDE', 'admin'); ?></div>
				<div class="fonction_valider"><!--<a href="#" onclick="valid()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a>--></div>
			</div>
			<form action="commande_creer.php" method="post" id="formulaire">
				<input type="hidden" name="action" value="ajouter" />
					<ul class="ligne_claire_BlocDescription" style="background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">
						<li class="designation" style="width:280px; background-image: url(gfx/degrade_ligne1.png); background-repeat: repeat-x;">
							<?php echo trad('Choix_client', 'admin'); ?>
							<span class="note"><?php echo trad('commencer', 'admin'); ?></span>
						</li>
						<li>
							<div id="nclient">
								<input type="hidden" name="id_client" id="id_client" value="" />
								<input name="choixdecli" id="inputstring" onkeyup="lookup(this.value);" autocomplete="off" type="text" class="form" size="40" />
							</div>
						</li>
					</ul>
					<div class="suggestionsBox" id="suggestions" style="display: none;">
						<img src="gfx/upArrow.png" style="float:left; margin:-10px 0 0 140px;" alt="upArrow" />
						<div class="suggestionList" id="autoSuggestionsList">
							&nbsp;
						</div>
					</div>
			
					<ul class="ligne_fonce_BlocDescription">
						<li class="designation" style="width:280px;"><?php echo trad('Ou_creer_client', 'admin'); ?></li>
						<li>
							<a href="#TB_inline?height=400&amp;width=800&amp;inlineId=contenu_cli&amp;modal=true" class="thickbox">
								<?php echo trad('creer_client', 'admin'); ?>
							</a>
						</li>
					</ul>
					<ul class="ligne_claire_BlocDescription">
						<li class="designation" style="width:280px;"><?php echo trad('Type_paiement', 'admin'); ?></li>
						<li>
							<select name="type_paiement" class="form_client">
								<option value=""><?php echo trad('Choisir', 'admin'); ?>... </option>
								<?php
									$modules = new Modules();
									$query = "select * from $modules->table where type=1 and actif=1";
									$resul = mysql_query($query,$modules->link);
									while($row = mysql_fetch_object($resul)){
										$modulesdesc = new Modulesdesc();
										$modulesdesc->charger($row->nom);
										?>
										<option value="<?php echo $row->id; ?>"><?php echo $modulesdesc->titre; ?></option>
										<?php
									}
								?>
							</select>
						</li>
					</ul>
					<ul class="ligne_fonce_BlocDescription">
						<li class="designation" style="width:280px;"><?php echo trad('Type_transport', 'admin'); ?></li>
						<li>
							<select name="type_livraison" class="form_client">
								<option value=""><?php echo trad('Choisir', 'admin'); ?>... </option>
								<?php
									$modules = new Modules();
									$query = "select * from $modules->table where type=2 and actif=1";
									$resul = CacheBase::getCache()->mysql_query($query, $modules->link);
				
									foreach($resul as $row) {
										$modulesdesc = new Modulesdesc();
										$modulesdesc->charger($row->nom);
										?>
										<option value="<?php echo $row->id; ?>"><?php echo $modulesdesc->titre; ?></option>
										<?php
									}
								?>
							</select>
						</li>
					</ul>
					<ul class="ligne_claire_BlocDescription">
						<li class="designation" style="width:280px;"><?php echo trad('Montant_frais_port', 'admin'); ?></li>
						<li><input type="text" name="fraisport" class="form" size="40"></li>
					</ul>
					<ul class="ligne_fonce_BlocDescription">
						<li class="designation" style="width:280px;"><?php echo trad('Remise', 'admin'); ?></li>
						<li><input type="text" name="remise" class="form" size="40" /></li>
					</ul>
				</form>
			</div>
			
			<div class="bordure_bottom" style="margin:10px 0 0px 0;">
				<div class="entete_liste_client">
					<div class="titre"><?php echo trad('AJOUT_PRODUITS', 'admin'); ?></div>
					<div class="fonction_valider"><a href="#" onclick="addcom()"><?php echo trad('AJOUTER_PANIER', 'admin'); ?></a></div>
				</div>
				<form id="prod">
					<ul class="ligne_claire_BlocDescription">
						<li class="designation" style="width:280px;"><?php echo trad('Ref_produit', 'admin'); ?></li>
						<li><input type="text" name="ref" id="ref" value="" class="form" size="40" onblur="verifref()" /></li>
					</ul>
					<ul class="ligne_fonce_BlocDescription">
						<li class="designation" style="width:280px;"><?php echo trad('Titre_produit', 'admin'); ?></li>
						<li><input type="text" name="titre" id="titre" value="" class="form" size="40" /></li>
					</ul>
					<ul class="ligne_claire_BlocDescription">
						<li class="designation" style="width:280px;"><?php echo trad('Prix_unitaire', 'admin'); ?></li>
						<li><input type="text" name="prixu" id="prixu" value="" class="form" size="40" /></li>
					</ul>
					<ul class="ligne_fonce_BlocDescription">
						<li class="designation" style="width:280px;"><?php echo trad('TVA_applicable_sur_produit', 'admin'); ?></li>
						<li><input type="text" name="tva" id="tva" value="" class="form" size="40" /></li>
					</ul>
					<ul class="ligne_claire_BlocDescription">
						<li class="designation" style="width:280px;"><?php echo trad('Quantite', 'admin'); ?></li>
						<li><input type="text" name="qtite" id="qtite" value="" class="form" size="40" /></li>
					</ul>
				</form>
			</div>

<div id="contenu_cli" style="display:none">
	<div id="bloc_description">
		<div class="entete_liste_client">
			<div class="titre"><?php echo trad('CREATION_CLIENT', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onclick="creercli()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
		</div>
		<table width="100%" cellpadding="5" cellspacing="0">
    	<tr class="claire">
        	<th class="designation" width="290"><?php echo trad('Societe', 'admin'); ?></th>
		    <th><input name="entreprise" id="entreprise" type="text" class="form" size="40" <?php if(isset($entreprise)) echo "value=\"".htmlspecialchars($entreprise)."\""; ?> /></th>
	</tr>
	<tr class="fonce">
			<td class="designation"><?php echo trad('Siret', 'admin'); ?></td>
		    <td><input name="siret" id="siret" type="text" class="form" size="40" <?php if(isset($siret)) echo "value=\"".htmlspecialchars($siret)."\""; ?> /></td>
	</tr>
	<tr class="claire">
		    <td class="designation"><?php echo trad('Numintracom', 'admin'); ?></td>
		    <td><input name="intracom" id="intracom" type="text" class="form" size="40" <?php if(isset($intracom)) echo "value=\"".htmlspecialchars($intracom)."\""; ?> /></td></tr>
	<tr class="fonce">
			<td class="designation">Civilité <?php if($erreurraison) echo trad('Obligatoire', 'admin'); ?></td>
			<td><input name="raison" type="radio" id="raison" class="form" value="1" <?php if(isset($raison) && $raison == 1) echo "checked"; ?>/>
		<?php echo trad('Mme', 'admin'); ?>
		<input name="raison" type="radio" class="form" value="2" <?php if(isset($raison) && $raison == 2) echo "checked"; ?>/>
		<?php echo trad('Mlle', 'admin'); ?>
		<input name="raison" type="radio" class="form" value="3" <?php if(isset($raison) && $raison == 3) echo "checked"; ?>/>
		<?php echo trad('M.', 'admin'); ?></td>
	</tr>
	<tr class="claire">
		   	<td class="designation"><?php echo trad('Nom', 'admin'); ?> <?php if($erreurnom) echo trad('Obligatoire', 'admin');  ?></td>
		    <td><input name="nom" id="nom" type="text" class="form" size="40" <?php if(isset($nom)) echo "value=\"".htmlspecialchars($nom)."\""; ?> /></td>
	</tr>
	<tr class="fonce">
		    <td class="designation"><?php echo trad('Prenom', 'admin'); ?> <?php if($erreurprenom) echo trad('Obligatoire', 'admin'); ?></td>
		    <td><input name="prenom" id="prenom" type="text" class="form" size="40" <?php if(isset($prenom)) echo "value=\"".htmlspecialchars($prenom)."\""; ?> /></td>
	</tr>
	<tr class="claire">
		    <td class="designation"><?php echo trad('Adresse', 'admin'); ?> <?php if($erreuradresse) echo trad('Obligatoire', 'admin'); ?></td>
		    <td><input name="adresse1" id="adresse1" type="text" class="form" size="40" <?php if(isset($adresse1)) echo "value=\"".htmlspecialchars($adresse1)."\""; ?>/></td>
	</tr>
	<tr class="fonce">
		    <td class="designation"><?php echo trad('Adressesuite', 'admin'); ?></td>
		    <td><input name="adresse2" id="adresse2" type="text" class="form" size="40" <?php if(isset($adresse2)) echo "value=\"".htmlspecialchars($adresse2)."\""; ?>/></td>
	</tr>
	<tr class="claire">
		    <td class="designation"><?php echo trad('Adressesuite', 'admin'); ?> 2</td>
		    <td><input name="adresse3" id="adresse3" type="text" class="form" size="40" <?php if(isset($adresse3)) echo "value=\"".htmlspecialchars($adresse3)."\""; ?>/></td>
	</tr>
	<tr class="fonce">
		    <td class="designation"><?php echo trad('CP', 'admin'); ?> <?php if($erreurcpostal) echo trad('Obligatoire', 'admin'); ?></td>
		    <td><input name="cpostal" id="cpostal" type="text" class="form" size="40" <?php if(isset($cpostal)) echo "value=\"".htmlspecialchars($cpostal)."\""; ?>/></td>
	</tr>
	<tr class="claire">
			<td class="designation"><?php echo trad('Ville', 'admin'); ?> <?php if($erreurville) echo trad('Obligatoire', 'admin'); ?></td>
			<td><input name="ville" id="ville" type="text" class="form" size="40" <?php if(isset($ville)) echo "value=\"".htmlspecialchars($ville)."\""; ?>/></td>
	</tr>
	<tr class="fonce">
		    <td class="designation">Pays <?php if($erreurpays) echo trad('Obligatoire', 'admin'); ?></td>
		    <td><select name="pays" id="pays" class="form_client">
		     <?php
		      	$pays = new Pays();
		      	$query ="select * from $pays->table";

		      	$resul = mysql_query($query, $pays->link);
		      	while($row = mysql_fetch_object($resul)){
					$paysdesc = new Paysdesc();
					if ($paysdesc->charger($row->id)) {

		      ?>
		      <option value="<?php echo $row->id; ?>" <?php if($paysform == $row->id || $row->defaut){ echo "selected"; } ?> ><?php echo($paysdesc->titre); ?></option>
		      <?php }
			  }?>
		      </select>
			</td>
	</tr>
	<tr class="claire">
		    <td class="designation"><?php echo trad('Telfixe', 'admin'); ?></td>
		    <td><input name="telfixe" id="telfixe" type="text" class="form" size="40" <?php if(isset($telfixe)) echo "value=\"".htmlspecialchars($telfixe)."\""; ?>/></td>
	</tr>
	<tr class="fonce">
		    <td class="designation"><?php echo trad('Telport', 'admin'); ?></td>
		    <td><input name="telport" id="telport" type="text" class="form" size="40" <?php if(isset($telport)) echo "value=\"".htmlspecialchars($telport)."\""; ?>/></td>
	</tr>
	<tr class="claire">
		    <td class="designation"><?php echo trad('E-mail', 'admin'); ?> <?php if($erreurmail) echo trad('Obligatoire', 'admin'); else if($erreurmailexiste) echo "existe déjà"; ?></td>
		    <td><input name="email1" id="email1" type="text" class="form" size="40" /></td>
	</tr>
	<tr class="fonce">
		    <td class="designation"><?php echo trad('Confirme-mail', 'admin'); ?></td>
		    <td><input name="email2" id="email2" type="text" class="form" size="40" /></td>
	</tr>
	<tr class="claire">
		    <td class="designation"><?php echo trad('Remise', 'admin'); ?> </td>
		    <td><input name="pourcentage" id="remise" type="text" class="form" size="40" <?php if(isset($remise)) echo "value=\"".htmlspecialchars($remise)."\""; ?>/></td>
	</tr>
	<tr class="foncebottom">
		    <td class="designation"><?php echo trad('Revendeur', 'admin'); ?> </td>
		    <td><input type="checkbox" name="type" id="type" class="form" <?php if(isset($type)) echo "checked"; ?>/></td>
	</tr>
</table>
		<input type="button" value="annuler" onclick="tb_remove()" />
	</div>
	</div>
</div>
<!-- fin du bloc description -->
<!-- bloc colonne de droite -->
<div id="bloc_colonne_droite">
		<div id="listecom"> </div>
</div>
</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>
