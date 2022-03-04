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

	if(! est_autorise("acces_configuration")) exit;

	if (!isset($lang)) $lang=$_SESSION["util"]->lang;

	if (isset($action) && $action == "modifier") {
		ActionsAdminModules::instance()->mise_a_jour_description($nom, $lang, $titre, $chapo, $description, $devise);
	}

	// Charger les infos modules

	$module = new Modules();

	$module->charger($nom);

	$moduledesc = new Modulesdesc();

	$moduledesc->charger($nom, $lang);

	$existe = $moduledesc->verif($nom, $lang);

    // Initialiser si la description n'existe pas dans cette langue.
	if (! $existe)
    {
        $moduledesc->lang = $lang;
        $moduledesc->plugin = $nom;
        $moduledesc->devise = 0;

        $moduledesc->titre = '';
        $moduledesc->chapo = '';
        $moduledesc->description = '';
        $moduledesc->devise = 0;
    }

    // Charger les devises
    $devises = array();

    $result = mysql_query('select * from '.Devise::TABLE.' order by nom');

    while($result && $row = mysql_fetch_object($result))
    {
    	$devises[$row->id] = $row;
    }

    // Charger les langues
    $langues = array();

    $result = mysql_query('select * from '.Lang::TABLE);

    while($result && $row = mysql_fetch_object($result))
    {
    	$langues[$row->id] = $row;
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php require_once("title.php");?>
</head>

<body>
	<div id="wrapper">
		<div id="subwrapper">
			<?php
				$menu="configuration";
				require_once("entete.php");
			?>

			<div id="contenu_int">
			   <p align="left">
				   	<a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a>
				   	<img src="gfx/suivant.gif" width="12" height="9" border="0" />
				   	<a href="configuration.php" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a>
				   	<img src="gfx/suivant.gif" width="12" height="9" border="0" />
				   	<a href="plugins.php" class="lien04"><?php echo trad('Gestion_plugins', 'admin'); ?></a>
				   	<img src="gfx/suivant.gif" width="12" height="9" border="0" />
				   	<?php echo ActionsAdminModules::instance()->lire_titre_module($module); ?>
			    </p>

				<div id="bloc_description">
					<div class="entete_liste_config">
						<div class="titre"><?php echo trad('DESCRIPTION DU PLUGIN', 'admin'); ?></div>
						<div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
					</div>

					<form action="<?php echo($_SERVER['PHP_SELF']); ?>" id="formulaire" method="post">

					  	<input type="hidden" name="action" value="modifier" />
						<input type="hidden" name="nom" value="<?php echo($module->nom); ?>" />
						<input type="hidden" name="id" value="<?php echo($moduledesc->id); ?>" />
						<input type="hidden" name="lang" value="<?php echo($lang); ?>" />

						<!-- bloc descriptif de la rubrique -->
						<table width="100%" cellpadding="5" cellspacing="0">

							<?php if($module->id != ""){?>
						    <tr class="claire">
						        <th class="designation"><?php echo trad('Changer_langue', 'admin'); ?></th>
						        <th>
						        <?php
								foreach($langues as $langl) {
								?>
								<div class="flag<?php if($lang ==  $langl->id) { ?>Selected<?php } ?>">
									<a href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=<?php echo $module->nom; ?>&id=<?php echo($id); ?>&lang=<?php echo($langl->id); ?>">
										<img src="gfx/lang<?php echo($langl->id); ?>.gif" />
									</a>
								</div>
								<?php } ?>
								</th>
						   	</tr>
							<?php } ?>

						   	<tr class="fonce">
						        <td class="designation"><?php echo trad('Titre', 'admin'); ?></td>
						        <td><input name="titre" id="titre" type="text" class="form_long" value="<?php echo htmlspecialchars($moduledesc->titre); ?>"/></td>
						   	</tr>

						   	<tr class="claire">
						        <td class="designation"><?php echo trad('Chapo', 'admin'); ?><br /><span class="note"><?php echo trad('courte_descript_intro', 'admin'); ?></span></td>
						        <td> <textarea name="chapo" id="chapo" cols="40" rows="2" class="form_long"><?php echo($moduledesc->chapo); ?></textarea></td>
						   	</tr>

						   	<tr class="fonce<?php echo ($module->type != Modules::PAIEMENT) ? 'bottom' : '' ?>">
						        <td class="designation"><?php echo trad('Description', 'admin'); ?><br /><span class="note"><?php echo trad('description_complete', 'admin'); ?></span></td>
						        <td><textarea name="description" id="description" cols="40" rows="2" class="form"><?php echo($moduledesc->description); ?></textarea></td>
						   	</tr>

						   	<?php if ($module->type == Modules::PAIEMENT) { ?>
						   	<tr class="clairebottom">
						        <td class="designation"><?php echo trad('Devise', 'admin'); ?><br /><span class="note"><?php echo trad('devis_complete', 'admin'); ?></span></td>
						        <td>
									<select name="devise">
										<option value="0"><?php echo trad('Par défaut', 'admin') ?></option>
					                    <?php
					                    foreach($devises as $devise)
					                    {
					                    	?>
					                        <option value="<?php echo $devise->id ?>" <?php echo $devise->id == $moduledesc->devise ? 'selected="selected"' : '' ?>><?php echo $devise->nom ?></option>'
					                        <?php
					                    }
					                    ?>
					                </select>
						        </td>
						   	</tr>
						   	<?php } ?>
						</table>
					</form>
				</div>
			</div>
			<?php require_once("pied.php");?>
		</div>
	</div>
</body>
</html>