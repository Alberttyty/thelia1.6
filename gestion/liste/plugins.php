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
function afficher_liste_plugins($type, $label) {

	?>
	<tr>
		<th class="entete_configuration" colspan="6"><a id="T<?php echo $type ?>"></a><?php echo $label ?></th>
	</tr>
	<?php
	$liste = ActionsAdminModules::instance()->lister($type);

	$fond = 'fonce';

	foreach($liste as $plugin) {

		$titre = ActionsAdminModules::instance()->lire_titre_module($plugin);

		$description = (string) $plugin->xml->descriptif->chapo;

		$documentation = '';

		if (trim($plugin->xml->documentation) != "") {

			$doc_file = sprintf("%s/%s",
				ActionsAdminModules::instance()->lire_chemin_module($plugin->nom),
				$plugin->xml->documentation
			);

			if (file_exists($doc_file))
				$documentation = sprintf("%s/%s/%s",
					ActionsAdminModules::instance()->lire_url_base(),
					$plugin->nom,
					$plugin->xml->documentation
				);
		}

		if (empty($description)) $description = (string) $plugin->xml->descriptif->description;

		if (empty($description)) $description = trad('Description non disponible', 'admin');

		?>
		<tr class="<?php echo $fond ?><?php if ($plugin->actif != 1) echo " plugininactif" ?>">

			<td class="titre">
				<a name="mod_<?php echo $plugin->id ?>"></a>
				<?php echo $titre; ?><?php echo $plugin->xml->version != '' ? " v".$plugin->xml->version : ''?>
			</td>

			<td rowspan="2" style="width: 50px;">
				<?php if ($plugin->activable && $plugin->type != Modules::FILTRE) { ?>
					<a href="plugins_modifier.php?nom=<?php echo $plugin->nom ?>&actif=0" class="txt_vert_11"><?php echo trad('Editer', 'admin'); ?></a>&nbsp;
				<?php } ?>
			</td>

			<td rowspan="2" style="width: 50px;">
				<?php if ($plugin->actif) { ?>
					<a href="plugins.php?action=desactiver&nom=<?php echo $plugin->nom ?>&actif=0#T<?php echo $type ?>" class="txt_vert_11"><?php echo trad('Desactiver', 'admin'); ?></a>&nbsp;
				<?php } else if ($plugin->activable) { ?>
					<a href="plugins.php?action=activer&nom=<?php echo $plugin->nom ?>&actif=1#T<?php echo $type ?>" class="txt_vert_11"><?php echo trad('Activer', 'admin'); ?></a>&nbsp;
				<?php } else { ?>
					<span style="text-align: center; color: #f00; font-weight: bold"><?php
					if (! empty($plugin->xml->thelia))
						echo trad('Nécessite Thelia %s', 'admin', $plugin->xml->thelia);
					else
						echo trad('Incompatible', 'admin');
					?></span>&nbsp;
				<?php } ?>
			</td>

			<td rowspan="2" style="width: 30px; text-align: center;">
				<?php if (!empty($documentation)) { ?>
					<a href="<?php echo $documentation; ?>" target="_doc_module" title="<?php echo trad("Lire la documentation ce plugin"); ?>"><?php echo trad('Documentation', 'admin'); ?></a>
				<?php } ?>
			</td>

			<td rowspan="2" style="width: 30px; text-align: center;">
			 <div class="bloc_classement">
			    <div class="classement"><a href="plugins.php?id=<?php echo($plugin->id); ?>&action=modclassement&type=M"><img src="gfx/up.gif" border="0" /></a></div>
			    <div class="classement"><span id="classementplugin_<?php echo $plugin->id; ?>" class="classement_edit"><?php echo $plugin->classement; ?></span></div>
			    <div class="classement"><a href="plugins.php?id=<?php echo($plugin->id); ?>&action=modclassement&type=D"><img src="gfx/dn.gif" border="0" /></a></div>
			 </div>
			</td>

			<td rowspan="2" style="width: 30px; text-align: center;">
				<a href="plugins.php?action=supprimer&nom=<?php echo $plugin->nom; ?>#T<?php echo $type ?>" title="<?php echo trad("Supprimer ce plugin", 'admin'); ?>" onclick="return supprimer('<?php echo str_replace("'", "\\'", $titre); ?>');"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a>
			</td>
		</tr>

		<tr class="<?php echo $fond ?> modules_info<?php if ($plugin->actif != 1) echo " plugininactif" ?>">
			<td><span style="font-size: 0.9em"><?php echo $description ?> (ID : <?php  echo $plugin->id; ?>)</span></td>
		</tr>
		<?php

		$fond = ($fond == 'fonce') ? 'claire' : 'fonce';
	}

	if (empty($liste))
	{
		?>
		<tr class="claire">
			<td colspan="6"><?php echo trad('Aucunplugin', 'admin'); ?></td>
		</tr>
		<?php
	}

	?>
	<tr>
		<td colspan="6" class="separateur"></td>
	</tr>
	<?php
}


function afficher_plugins() {

   echo '<table class="modules">';

   afficher_liste_plugins(Modules::CLASSIQUE, trad('LISTE_PLUGINS_CLASSIQUES', 'admin'));
   afficher_liste_plugins(Modules::PAIEMENT, trad('LISTE_PLUGINS_PAIEMENTS', 'admin'));
   afficher_liste_plugins(Modules::TRANSPORT, trad('LISTE_PLUGINS_TRANSPORTS', 'admin'));
   afficher_liste_plugins(Modules::FILTRE, trad('LISTE_FILTRE', 'admin'));

	echo '</table>';
}
?>