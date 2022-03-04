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
?>
<?php if(! est_autorise("acces_clients")) exit; ?>
<?php
	$erreurnom = 0;
	$erreurprenom = 0;
	$erreuradresse = 0;
	$erreurraison = 0;
	$erreurmail = 0;
	$erreurcpostal = 0;
	$erreurmailexiste = 0;
	$erreurville = 0;
	$erreurpays = 0;

	if($action == "ajouter"){

		$client = new Client();
		$client->raison = strip_tags($raison);
		$client->nom = strip_tags($nom);
		$client->entreprise = strip_tags($entreprise);
		$client->prenom = strip_tags($prenom);
		$client->telfixe = strip_tags($telfixe);
		$client->telport =strip_tags($telport);
		if( filter_var($email1, FILTER_VALIDATE_EMAIL) && $email1==$email2) $client->email = strip_tags($email1);
		$client->adresse1 = strip_tags($adresse1);
		$client->adresse2 = strip_tags($adresse2);
		$client->adresse3 = strip_tags($adresse3);
		$client->cpostal = strip_tags($cpostal);
		$client->ville = strip_tags($ville);
		$client->siret = strip_tags($siret);
		$client->intracom = strip_tags($intracom);
		$client->pays = strip_tags($pays);
		$client->pourcentage = strip_tags($pourcentage);
        if($type == "on") $client->type = 1;
        else $client->type = 0;
		$client->lang = ActionsLang::instance()->get_id_langue_courante();

                $parrain = new Client();
                if($parrain->charger_ref($id_parrain))
                    $client->parrain = $parrain->id;
                else
                    $parrain=0;

		$client->motdepasse = genpass(8);
		$pass = $client->motdepasse;

		if($client->raison!="" && $client->prenom!="" && $client->nom!="" && $client->email!="" && $client->motdepasse!=""
			&& $client->email && ! $client->existe($email1) && $client->adresse1 !="" && $client->cpostal!="" && $client->ville !="" && $client->pays !=""){
				$client->crypter();
				$client->add();

				$raisondesc = new Raisondesc($client->raison, ActionsLang::instance()->get_id_langue_courante());

				$paysdesc = new Paysdesc();
				$paysdesc->charger($client->pays);

				$rec = $client->charger_mail($client->email);

				$message = new Message();
				$message->charger("creation_client");

				$messagedesc = new Messagedesc();
				$messagedesc->charger($message->id);

				$nomsite = new Variable("nomsite");
				$urlsite = new Variable("urlsite");
				$emailcontact = new Variable("emailcontact");

				$messagedesc->description = str_replace("__NOMSITE__",$nomsite->valeur,$messagedesc->description);
				$messagedesc->description = str_replace("__EMAIL__",$client->email,$messagedesc->description);
				$messagedesc->description = str_replace("__MOTDEPASSE__",$pass,$messagedesc->description);
				$messagedesc->description = str_replace("__URLSITE__",$urlsite->valeur,$messagedesc->description);
				$messagedesc->description = str_replace("__NOM__",$client->nom,$messagedesc->description);
				$messagedesc->description = str_replace("__PRENOM__",$client->prenom,$messagedesc->description);
				$messagedesc->description = str_replace("__ADRESSE1__",$client->adresse1,$messagedesc->description);
				$messagedesc->description = str_replace("__ADRESSE2__",$client->adresse2,$messagedesc->description);
				$messagedesc->description = str_replace("__ADRESSE3__",$client->adresse3,$messagedesc->description);
				$messagedesc->description = str_replace("__VILLE__",$client->ville,$messagedesc->description);
				$messagedesc->description = str_replace("__CPOSTAL__",$client->cpostal,$messagedesc->description);
				$messagedesc->description = str_replace("__TELEPHONE__",$client->telfixe,$messagedesc->description);
				$messagedesc->description = str_replace("__CIVILITE__",$raisondesc->court,$messagedesc->description);
				$messagedesc->description = str_replace("__PAYS__",$paysdesc->titre,$messagedesc->description);

				$messagedesc->descriptiontext = str_replace("__NOMSITE__",$nomsite->valeur,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__EMAIL__",$client->email,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__MOTDEPASSE__",$pass,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__URLSITE__",$urlsite->valeur,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__NOM__",$client->nom,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__PRENOM__",$client->prenom,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__ADRESSE1__",$client->adresse1,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__ADRESSE2__",$client->adresse2,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__ADRESSE3__",$client->adresse3,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__VILLE__",$client->ville,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__CPOSTAL__",$client->cpostal,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__TELEPHONE__",$client->telfixe,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__CIVILITE__",$raisondesc->court,$messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__PAYS__",$paysdesc->titre,$messagedesc->descriptiontext);

				Mail::envoyer(
					$client->prenom . " " . $client->nom, $client->email,
					$nomsite->valeur, $emailfrom->valeur,
					$messagedesc->titre,
					$messagedesc->description, $messagedesc->descriptiontext
				);

				ActionsModules::instance()->appel_module("ajoutclient", $client);

				redirige("client_visualiser.php?ref=".$client->ref);
		}
		else{
			//traitement des erreurs
			if($nom == "") $erreurnom = 1;
			if($prenom == "") $erreurprenom = 1;
			if($adresse1 == "") $erreuradresse = 1;
			if($raison == "") $erreurraison = 1;
			if($client->email == "") $erreurmail = 1;
			if($cpostal == "") $erreurcpostal = 1;
			if($client->existe($email1)) $erreurmailexiste = 1;
			if($ville == "") $erreurville = 1;
			if($pays == "") $erreurpays = 1;
			$paysform =$pays;
		}

	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php"); ?>
<script type="text/javascript">
function lookup(inputString) {
        if(inputString.length == 0) {
                // Hide the suggestion box.
                $('#suggestion_parrain').hide();
        } else {
                $.get("listecli.php", {queryString: ""+inputString+""}, function(data){
                        if(data.length >0) {
                                $('#suggestion_parrain').show();
                                $('#autoSuggestionsList').html(data);
                        }
                });
        }
} // lookup

function fill(str) {
        if (str != "") {
                var tableau = str.split("|");
                $('#inputstring').val(tableau[1]);
                $('#id_parrain').val(tableau[0]);
                setTimeout("$('#suggestion_parrain').hide();", 200);
        }
        else{
                $('#inputstring').val(str);
                setTimeout("$('#suggestion_parrain').hide();", 200);
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
      <p><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a><img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="client.php" class="lien04"><?php echo trad('Gestion_clients', 'admin'); ?></a>
    </p>

<!-- Début de la colonne de gauche -->
<div id="bloc_description">

<!-- bloc de création client -->
	<form action="client_creer.php" method="post" id="formulaire">
		<input type="hidden" name="action" value="ajouter" />
		<div class="entete_liste_client">
			<div class="titre"><?php echo trad('CREATION_CLIENT', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
		</div>
		<table width="100%" cellpadding="5" cellspacing="0" style="clear: both;">
		    <tr class="claire">
		        	<th class="designation" width="290"><?php echo trad('Societe', 'admin'); ?></th>
				    <th><input name="entreprise" type="text" class="form" size="40" <?php if(isset($entreprise)) echo "value=\"".htmlspecialchars($entreprise)."\""; ?> /></th>
			</tr>
			<tr class="fonce">
					<td class="designation"><?php echo trad('Siret', 'admin'); ?></td>
				    <td><input name="siret" type="text" class="form" size="40" <?php if(isset($siret)) echo "value=\"".htmlspecialchars($siret)."\""; ?> /></td>
			</tr>
			<tr class="claire">
				    <td class="designation"><?php echo trad('Numintracom', 'admin'); ?></td>
				    <td><input name="intracom" type="text" class="form" size="40" <?php if(isset($intracom)) echo "value=\"".htmlspecialchars($intracom)."\""; ?> /></td></tr>
			<tr class="fonce">
					<td class="designation"><?php echo trad('Civilite', 'admin'); ?> <span class="erreur_crea_client"><?php if($erreurraison) echo trad('Obligatoire', 'admin'); ?></span></td>
					<td>
<?php
$q = "SELECT * FROM " . Raisondesc::TABLE  . " WHERE lang=" . ActionsLang::instance()->get_id_langue_courante();
$r = mysql_query($q);
while($r && $a = mysql_fetch_object($r))
{
?>
                                            <input name="raison" type="radio" class="form" value="<?php echo $a->raison; ?>" <?php if(isset($raison) && $raison == $a->raison) echo "checked"; ?>/>
                                            <?php echo $a->court; ?>
<?php
}
?>
                                        </td>
			</tr>
			<tr class="claire">
				   	<td class="designation"><?php echo trad('Nom', 'admin'); ?> <span class="erreur_crea_client"><?php if($erreurnom) echo trad('Obligatoire', 'admin');  ?></span></td>
				    <td><input name="nom" type="text" class="form" size="40" <?php if(isset($nom)) echo "value=\"".htmlspecialchars($nom)."\""; ?> /></td>
			</tr>
			<tr class="fonce">
				    <td class="designation"><?php echo trad('Prenom', 'admin'); ?> <span class="erreur_crea_client"><?php if($erreurprenom) echo trad('Obligatoire', 'admin'); ?></span></td>
				    <td><input name="prenom" type="text" class="form" size="40" <?php if(isset($prenom)) echo "value=\"".htmlspecialchars($prenom)."\""; ?> /></td>
			</tr>
			<tr class="claire">
				    <td class="designation"><?php echo trad('Adresse', 'admin'); ?> <span class="erreur_crea_client"><?php if($erreuradresse) echo trad('Obligatoire', 'admin'); ?></span></td>
				    <td><input name="adresse1" type="text" class="form" size="40" <?php if(isset($adresse1)) echo "value=\"".htmlspecialchars($adresse1)."\""; ?>/></td>
			</tr>
			<tr class="fonce">
				    <td class="designation"><?php echo trad('Adressesuite', 'admin'); ?></td>
				    <td><input name="adresse2" type="text" class="form" size="40" <?php if(isset($adresse2)) echo "value=\"".htmlspecialchars($adresse2)."\""; ?>/></td>
			</tr>
			<tr class="claire">
				    <td class="designation"><?php echo trad('Adressesuite', 'admin'); ?> 2</td>
				    <td><input name="adresse3" type="text" class="form" size="40" <?php if(isset($adresse3)) echo "value=\"".htmlspecialchars($adresse3)."\""; ?>/></td>
			</tr>
			<tr class="fonce">
				    <td class="designation"><?php echo trad('CP', 'admin'); ?> <span class="erreur_crea_client"><?php if($erreurcpostal) echo trad('Obligatoire', 'admin'); ?></span></td>
				    <td><input name="cpostal" type="text" class="form" size="40" <?php if(isset($cpostal)) echo "value=\"".htmlspecialchars($cpostal)."\""; ?>/></td>
			</tr>
			<tr class="claire">
					<td class="designation"><?php echo trad('Ville', 'admin'); ?> <span class="erreur_crea_client"><?php if($erreurville) echo trad('Obligatoire', 'admin'); ?></span></td>
					<td><input name="ville" type="text" class="form" size="40" <?php if(isset($ville)) echo "value=\"".htmlspecialchars($ville)."\""; ?>/></td>
			</tr>
			<tr class="fonce">
				    <td class="designation"><?php echo trad('Pays', 'admin'); ?> <span class="erreur_crea_client"><?php if($erreurpays) echo trad('Obligatoire', 'admin'); ?></span></td>
				    <td><select name="pays" class="form_client">
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
						} ?>
				      </select>
					</td>
			</tr>
			<tr class="claire">
				    <td class="designation"><?php echo trad('Telfixe', 'admin'); ?></td>
				    <td><input name="telfixe" type="text" class="form" size="40" <?php if(isset($telfixe)) echo "value=\"".htmlspecialchars($telfixe)."\""; ?>/></td>
			</tr>
			<tr class="fonce">
				    <td class="designation"><?php echo trad('Telport', 'admin'); ?></td>
				    <td><input name="telport" type="text" class="form" size="40" <?php if(isset($telport)) echo "value=\"".htmlspecialchars($telport)."\""; ?>/></td>
			</tr>
			<tr class="claire">
				    <td class="designation"><?php echo trad('E-mail', 'admin'); ?> <span class="erreur_crea_client"><?php if($erreurmail) echo trad('Obligatoire', 'admin'); else if($erreurmailexiste) echo "existe déjà"; ?></span></td>
				    <td><input name="email1" type="text" class="form" size="40" <?php if(isset($email1)) echo "value=\"".htmlspecialchars($email1)."\""; ?>/></td>
			</tr>
			<tr class="fonce">
				    <td class="designation"><?php echo trad('Confirme-mail', 'admin'); ?></td>
				    <td><input name="email2" type="text" class="form" size="40" <?php if(isset($email2)) echo "value=\"".htmlspecialchars($email2)."\""; ?>/></td>
			</tr>
			<tr class="claire">
				    <td class="designation"><?php echo trad('Remise', 'admin'); ?> </td>
				    <td><input name="pourcentage" type="text" class="form" size="40" <?php if(isset($remise)) echo "value=\"".htmlspecialchars($remise)."\""; ?>/></td>
			</tr>
			<tr class="fonce">
			    <td class="designation"><?php echo trad('Revendeur', 'admin'); ?> </td>
				    <td><input type="checkbox" name="type" class="form" <?php if(isset($type)) echo "checked"; ?>/></td>
			</tr>
                        <tr class="clairebottom">
			    <td class="designation"><?php echo trad('Choix_parrain', 'admin'); ?> <span class="note"><?php echo trad('commencer', 'admin'); ?></span></td>
				    <td>
                                            <input type="hidden" name="id_parrain" id="id_parrain" value="<?php if(isset($parrain->ref)) echo $parrain->ref; ?>" />
                                            <input name="choixdeparrain" id="inputstring" onkeyup="lookup(this.value);" type="text" class="form" size="40" <?php if(isset($parrain->ref)) echo 'value="' . $parrain->nom . ' ' . $parrain->prenom . '"'; ?> />
                                            <div class="suggestionsBox" id="suggestion_parrain" style="display: none;">
                                                    <img src="gfx/upArrow.png" style="float:left; margin:-10px 0 0 140px;" alt="upArrow" />
                                                    <div class="suggestionList" id="autoSuggestionsList">
                                                            &nbsp;
                                                    </div>
                                            </div>
                                    </td>
			</tr>
		</table>
	</form>
</div>
<!-- fin du bloc description -->
</div>
<?php require_once("pied.php");?>   
</div>
</div>
</body>
</html>
