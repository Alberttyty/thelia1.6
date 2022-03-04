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

if (isset($action) && $action != "") {
    if ($action == "modifier") {

        // Mettre à jour les paramètres
        ActionsAdminLang::instance()->maj_parametres(
            $_REQUEST['un_domaine_par_langue'],
            $_REQUEST['action_si_trad_absente'],
            $_REQUEST['urlsite']
        );

        // Appliquer les modifications
        foreach($_REQUEST['description'] as $id => $description) {
            ActionsAdminLang::instance()->modifier(
                $id,
                $description,
                $_REQUEST['code'][$id],
                $_REQUEST['url'][$id],
                ($id == $_REQUEST['defaut']) ? 1 : 0
            );
        }

        // Ajout éventuel d'une langue
        if (intval($_REQUEST['flag_ajouter']) != 0) {
            ActionsAdminLang::instance()->ajouter(
                $_REQUEST['ajout_description'],
                $_REQUEST['ajout_code'],
                $_REQUEST['ajout_url']
            );
        }
    }
    else if ($action == "supprimer") {
        ActionsAdminLang::instance()->supprimer($id);
    }

    redirige("langue.php");
}

$langues = ActionsAdminLang::instance()->lister();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");?>

<script type="text/javascript">
	function change_lang_type(mode, select) {

		if (mode == 1) {
			$('.urlsite').attr("disabled", "disabled");
			$('.urllangue').removeAttr("disabled");

			if (select) $('.urllangue')[0].select();
		}
		else {
			$('.urllangue').attr("disabled", "disabled");
			$('.urlsite').removeAttr("disabled");

			if (select) $('.urlsite').select();
		}

		$("input:not(disabled)").css("color", "#000000");
		$("input:disabled").css("color", "#bbbbbb");
	}

	function confirmer_suppression() {
		return confirm("<?php echo trad('Supprimer définitivement cette langue ?', 'admin'); ?>");
	}

	function show_form_ajouter(afficher) {
		if (afficher) {
			$('.bouton_ajouter').hide();
			$('.form_ajouter').show();
			$('input[name=ajout_description]').focus();
		}
		else {
			$('.bouton_ajouter').show();
			$('.form_ajouter').hide();
		}
	}

	function validation() {

		if ($('.form_ajouter').is(':visible')) {

			if ($('input[name=ajout_description]').val() == '') {
				alert("<?php echo trad("Merci d'indiquer le nom de cette langue", 'admin'); ?>");
				$('input[name=ajout_description]').focus();

				return false;
			}

			if ($('input[name=ajout_code]').val() == '') {
				alert("<?php echo trad("Merci d'indiquer le code ISO 639-1 de cette langue", 'admin'); ?>");
				$('input[name=ajout_code]').focus();

				return false;
			}

			$('input[name=flag_ajouter]').val('1');
		}

		$('#formlangue').submit();
	}

	$(document).ready(function() {

		change_lang_type(<?php echo ActionsAdminLang::instance()->get_un_domaine_par_langue(); ?>, false);

		$('#action_si_trad_absente').val("<?php echo ActionsAdminLang::instance()->get_action_si_trad_absente() ?>");
	});
</script>

</head>

<body>
	<div id="wrapper">
		<div id="subwrapper">
			<?php
				$menu="configuration";
				require_once("entete.php");
			?>
			<div id="contenu_int">
			   <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="configuration.php" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="langue.php" class="lien04"><?php echo trad('Gestion_langue', 'admin'); ?></a></p>

				<div id="bloc_description">

				    <form action="langue.php" id="formlangue" method="post">
						<input type="hidden" name="action" value="modifier" />

					<!-- Gestion des langues ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

						<div class="entete_liste_config">
							<div class="titre"><?php echo trad('GERER LES LANGUES', 'admin'); ?></div>
							<div class="fonction_valider"><a href="#" onclick="validation(); return false;"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
						</div>

						<ul class="ligne_fonce_BlocDescription lignetop">
							<li style="width:300px;"><?php echo trad('Nom de la langue', 'admin'); ?></li>
							<li style="width:105px; text-align: center;"><a title="<?php echo trad('Voir la liste complète des codes ISO 639-1', 'admin'); ?>" style="color: #2F3D46; font-weight: normal" href="http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1" target="_blank"><?php echo trad('Code ISO 639', 'admin'); ?></a></li>
							<li style="width:60px; text-align: center;"><?php echo trad('Par défaut', 'admin'); ?></li>
							<li style="width:60px; text-align: center;"><?php echo trad('Supprimer', 'admin'); ?></li>
						</ul>

						<?php
					  	$fond = 'claire';

					  	foreach($langues as $langue) {

					  		?>
							<ul class="ligne_<?php echo $fond; ?>_BlocDescription">
								<li style="width:300px;"><input type="text" style="width: 290px;" name="description[<?php echo($langue->id); ?>]" value="<?php echo(htmlspecialchars($langue->description)); ?>" /></li>
								<li style="width:105px; text-align: center;"><input type="text" style="width: 40px;" name="code[<?php echo($langue->id); ?>]" value="<?php echo(htmlspecialchars($langue->code)); ?>" /></li>
								<li style="width:60px; text-align: center;"><input type="radio" name="defaut" value="<?php echo($langue->id); ?>" <?php if ($langue->defaut) echo 'checked="checked"'; ?>/></li>
								<li style="width:60px; text-align: center;"><a onclick="return confirmer_suppression();" href="langue.php?action=supprimer&id=<?php echo($langue->id); ?>" title="<?php echo trad('Supprimer cette langue', 'admin'); ?>"><img src="gfx/supprimer.gif" alt="X" /></a></li>
							</ul>
							<?php

						 	$fond = ($fond == 'claire') ?  'fonce' : 'claire';
						 }
						 ?>
						<ul class="bouton_ajouter ligne_<?php echo $fond; ?>_BlocDescription lignebottom">
							<li style="width:98%; text-align: right; height: 28px"><button onclick="show_form_ajouter(true); return false;"><?php echo trad('Ajouter une langue', 'admin'); ?></button></li>
						</ul>

						<div class="form_ajouter" style="display: none;">
							<input type="hidden" name="flag_ajouter" value="0" />
							<ul class="ligne_<?php echo $fond; ?>_BlocDescription">
								<li style="width:300px;"><input type="text" style="width: 290px;" name="ajout_description" value="" /></li>
								<li style="width:105px; text-align: center;"><input type="text" style="width: 40px;" name="ajout_code" value="" /></li>
								<li style="width:60px; text-align: center;"></li>
								<li style="width:60px; text-align: center;"><a onclick="show_form_ajouter(false); return false;" href="#" title="<?php echo trad('Annuler', 'admin'); ?>"><img src="gfx/supprimer.gif" alt="X" /></a></li>
							</ul>
						</div>

						<!-- Paramètres ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

						<div class="entete_liste_config" style="margin-top: 20px">
							<div class="titre"><?php echo trad('PARAMETRES', 'admin'); ?></div>
							<div class="fonction_valider"><a href="#" onclick="validation(); return false;"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
						</div>

						<ul class="ligne_fonce_BlocDescription lignebottom">
							<li style="width:265px;"><?php echo trad('Si une traduction est absente ou incomplète :', 'admin'); ?></li>
							<li style="width:291px;">
								<select style="width:290px" name="action_si_trad_absente" id="action_si_trad_absente">
									<option value="<?php echo ActionsLang::UTILISER_LANGUE_PAR_DEFAUT ?>"><?php echo trad('Remplacer par la langue par défaut', 'admin'); ?></option>
									<option value="<?php echo ActionsLang::UTILISER_LANGUE_INDIQUEE ?>"><?php echo trad('Utiliser strictement la langue demandée', 'admin'); ?></option>
								</select>
							</li>
						</ul>

						<!-- Association URL - LANGUE ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

						<div class="entete_liste_config" style="margin-top: 20px">
							<div class="titre"><?php echo trad('ASSOCIATION LANGUE - URL', 'admin'); ?></div>
							<div class="fonction_valider"><a href="#" onclick="validation(); return false;"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
						</div>

						<ul class="ligne_claire_BlocDescription lignetop">
							<li style="width:561px;"><input type="radio" onclick="change_lang_type(0, true);" name="un_domaine_par_langue" value="0" <?php if (ActionsAdminLang::instance()->get_un_domaine_par_langue() == 0) echo 'checked="checked"' ?> /> <?php echo trad('Utiliser le même domaine pour toutes les langues', 'admin'); ?></li>
						</ul>

						<ul class="ligne_fonce_BlocDescription">
							<li style="width:151px;"><?php echo trad('URL du site', 'admin'); ?></li>
							<li style="width:406px;"><input style="width:406px;" name="urlsite" type="text" class="form urlsite" value="<?php echo  Variable::lire('urlsite'); ?>" /></li>
						</ul>

						<ul class="ligne_claire_BlocDescription lignetop">
							<li style="width:561px;"><input type="radio" onclick="change_lang_type(1, true);" name="un_domaine_par_langue" value="1" <?php if (ActionsAdminLang::instance()->get_un_domaine_par_langue() == 1) echo 'checked="checked"' ?> /> <?php echo trad('Utiliser un domaine ou sous-domaine pour chaque langue', 'admin'); ?></li>
						</ul>

						<ul class="ligne_fonce_BlocDescription">
							<li style="width:151px;"><?php echo trad('Langue', 'admin'); ?></li>
							<li style="width:406px;"><?php echo trad('URL associée', 'admin'); ?></li>
						</ul>

						<?php
					  	$fond = 'claire';

					  	foreach($langues as $langue) {
					  		?>
							<ul class="ligne_<?php echo $fond; ?>_BlocDescription">
								<li style="width:151px;"><?php echo($langue->description); ?></li>
								<li style="width:406px;"><input type="text"  style="width: 406px;" name="url[<?php echo($langue->id); ?>]" class="form urllangue" value="<?php echo  ($langue->url); ?>" size="50" /></li>
							</ul>
							<?php

						 	$fond = ($fond == 'claire') ?  'fonce' : 'claire';
						 }
						 ?>
					</form>
				</div>
			</div>
		</div>
	<?php require_once("pied.php");?>
	</div>
</body>
</html>