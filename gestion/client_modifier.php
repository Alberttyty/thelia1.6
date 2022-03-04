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

if(! est_autorise("acces_clients")) exit;

require_once("../fonctions/divers.php");

$client = new Client();

$client->charger_ref($ref);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php require_once("title.php"); ?>
</head>

<body>
	<div id="wrapper">
		<div id="subwrapper">

    		<?php
    		$menu="client";
    		require_once("entete.php");
    		?>

    		<div id="contenu_int">
    			<p>
    				<a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a>
    				<img src="gfx/suivant.gif" width="12" height="9" border="0" />
    				<a href="client.php" class="lien04"><?php echo trad('Gestion_clients', 'admin'); ?></a>
    				 <img src="gfx/suivant.gif" width="12" height="9" border="0" />
    				 <a href="#" class="lien04"><?php echo trad('Modifier', 'admin'); ?> </a>
    			</p>

    			<!-- Début de la colonne de gauche -->
    			<div id="bloc_description">

    				<form action="client_visualiser.php" id="formulaire" method="post">

    					<input type="hidden" name="action" value="modifier" /> <input type="hidden" name="ref" value="<?php echo($ref); ?>" />

    					<!-- bloc de modification client -->
    					<div class="entete_liste_client">
    						<div class="titre"><?php echo trad('INFO_CLIENT', 'admin'); ?></div>
    						<div class="fonction_valider">
    							<a href="#" onclick="document.getElementById('formulaire').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a>
    						</div>
    					</div>

    					<table width="100%" cellpadding="5" cellspacing="0" style="clear: both;">
    						<tr class="claire">
    							<th class="designation" width="290"><?php echo trad('Societe', 'admin'); ?></th>
    							<th><input name="entreprise" type="text" class="form" value="<?php echo htmlspecialchars($client->entreprise); ?>" size="40" /></th>
    						</tr>

    						<tr class="fonce">
    							<td class="designation"><?php echo trad('Siret', 'admin'); ?></td>
    							<td><input name="siret" type="text" class="form" value="<?php echo htmlspecialchars($client->siret); ?>" size="40" /></td>
    						</tr>

    						<tr class="claire">
    							<td class="designation"><?php echo trad('Numintracom', 'admin'); ?></td>
    							<td><input name="intracom" type="text" class="form" value="<?php echo htmlspecialchars($client->intracom); ?>" size="40" /></td>
    						</tr>

    						<tr class="fonce">
    							<td class="designation"><?php echo trad('Civilite', 'admin'); ?></td>
    							<td><select name="raison">
    								<?php
    								$raison = new Raison();

    								$result = $raison->query("select * from $raison->table");

    								while ($result && $row = $raison->fetch_object($result, 'Raison')) {

    									$raisondesc = new Raisondesc($row->id, $_SESSION['util']->lang);

    									$selected = ($client->raison == $row->id) ? 'selected="selected"' : '';
    									?>
    										<option <?php echo $selected; ?> value="<?php echo $raisondesc->raison; ?>"><?php echo $raisondesc->long; ?></option>
    									<?php
    								}

    								?>
    								</select>
    							</td>
    						</tr>

    						<tr class="claire">
    							<td class="designation"><?php echo trad('Nom', 'admin'); ?></td>
    							<td><input name="nom" type="text" class="form" value="<?php echo htmlspecialchars($client->nom); ?>" size="40" /></td>
    						</tr>

    						<tr class="fonce">
    							<td class="designation"><?php echo trad('Prenom', 'admin'); ?></td>
    							<td class="cellule_claire"><input name="prenom" type="text" class="form" value="<?php echo htmlspecialchars($client->prenom); ?>" size="40" /></td>
    						</tr>

    						<tr class="claire">
    							<td class="designation"><?php echo trad('Adresse', 'admin'); ?></td>
    							<td><input name="adresse1" type="text" class="form" value="<?php echo htmlspecialchars($client->adresse1); ?>" size="40" /></td>
    						</tr>

    						<tr class="fonce">
    							<td class="designation"><?php echo trad('Adressesuite', 'admin'); ?></td>
    							<td><input name="adresse2" type="text" class="form" value="<?php echo htmlspecialchars($client->adresse2); ?>" size="40" /></td>
    						</tr>

    						<tr class="claire">
    							<td class="designation"><?php echo trad('Adressesuite', 'admin'); ?> 2</td>
    							<td><input name="adresse3" type="text" class="form" value="<?php echo htmlspecialchars($client->adresse3); ?>" size="40" /></td>
    						</tr>

    						<tr class="fonce">
    							<td class="designation"><?php echo trad('CP', 'admin'); ?></td>
    							<td><input name="cpostal" type="text" class="form" value="<?php echo htmlspecialchars($client->cpostal); ?>" size="40" /></td>
    						</tr>

    						<tr class="claire">
    							<td class="designation"><?php echo trad('Ville', 'admin'); ?></td>
    							<td><input name="ville" type="text" class="form" value="<?php echo htmlspecialchars($client->ville); ?>" size="40" /></td>
    						</tr>

    						<tr class="fonce">
    							<td class="designation"><?php echo trad('Pays', 'admin'); ?></td>
    							<td><select name="pays" class="form_client">
    								<?php
    								$pays = new Pays();
    								$query ="select * from $pays->table";

    								$resul = $pays->query($query);

    								while($resul && $row = $pays->fetch_object($resul, 'Pays')) {

    									$paysdesc = new Paysdesc();

    									if ($paysdesc->charger($row->id)) {

    										if($row->id == $client->pays || $pays->defaut) $selected="selected=\"selected\""; else $selected="";
    										?>
    										<option value="<?php echo($row->id); ?>" <?php echo($selected); ?>><?php echo($paysdesc->titre); ?></option>
    									    <?php
    									}
    								}
    	  							?>
          							</select>
    							</td>
    						</tr>

    						<tr class="claire">
    							<td class="designation"><?php echo trad('Telfixe', 'admin'); ?></td>
    							<td><input name="telfixe" type="text" class="form" value="<?php echo htmlspecialchars($client->telfixe); ?>" size="40" /></td>
    						</tr>

    						<tr class="fonce">
    							<td class="designation"><?php echo trad('Telport', 'admin'); ?></td>
    							<td><input name="telport" type="text" class="form" value="<?php echo htmlspecialchars($client->telport); ?>" size="40" /></td>
    						</tr>

    						<tr class="claire">
    							<td class="designation"><?php echo trad('E-mail', 'admin'); ?></td>
    							<td><input name="email" type="text" class="form" value="<?php echo htmlspecialchars($client->email); ?>" size="40" /></td>
    						</tr>

    						<tr class="fonce">
    							<td class="designation"><?php echo trad('Remise', 'admin'); ?></td>
    							<td><input name="pourcentage" type="text" class="form" value="<?php echo htmlspecialchars($client->pourcentage); ?>" size="40" /></td>
    						</tr>

    						<tr class="clairebottom">
    							<td class="designation"><?php echo trad('Revendeur', 'admin'); ?></td>
    							<td><input type="checkbox" name="type" <?php echo $client->type ? 'checked="checked"' : ''; ?> class="form" /></td>
    						</tr>
    					</table>

    					<!-- début du bloc point d'entrée -->
    					<div class="patchplugin">
    						<?php ActionsAdminModules::instance()->inclure_module_admin("clientmodifier"); ?>
    					</div>
    					<!-- fin du bloc point d'entrée -->
    					<!-- fin du bloc description -->
    				</form>
    			</div>
            </div>

            <?php require_once("pied.php");?>
        </div>
	</div>
</body>
</html>
