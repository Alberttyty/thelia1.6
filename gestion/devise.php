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

    	if (is_array($_REQUEST['nom'])) {

	        foreach($_REQUEST['nom'] as $id => $nom) {

	            ActionsAdminDevises::instance()->modifier(
	                            $id,
	                            trim($nom),
	                            trim($_REQUEST['taux'][$id]),
	                            trim($_REQUEST['symbole'][$id]),
	                            trim($_REQUEST['code'][$id]),
	                            $id == $_REQUEST['defaut'] ? 1 : 0
	            );
	        }
    	}

        // Ajout éventuel d'une devise
        if (intval($_REQUEST['flag_ajouter']) != 0) {

            ActionsAdminDevises::instance()->ajouter(
                            trim($ajout_nom),
                            trim($ajout_taux),
                            trim($ajout_symbole),
                            trim($ajout_code)
            );
        }
    }
    else if ($action == "supprimer") {

        ActionsAdminDevises::instance()->supprimer($id);
    }
    else if ($action == "refresh") {
        ActionsAdminDevises::instance()->refresh();
    }

    redirige("devise.php");
}

$devises = ActionsAdminDevises::instance()->lister();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php require_once("title.php");?>

<script type="text/javascript">

	function confirmer_suppression() {
            return confirm("<?php echo trad('Supprimer définitivement cette devise ?', 'admin'); ?>");
	}

	function show_form_ajouter(afficher) {
            if (afficher) {
                $('.bouton_ajouter').hide();
                $('.form_ajouter').show();
                $('input[name=ajout_nom]').focus();
            }
            else {
                $('.bouton_ajouter').show();
                $('.form_ajouter').hide();
            }
	}

	function validation() {

            if ($('.form_ajouter').is(':visible')) {

                var ok = true;

                var fields = {
                            ajout_nom: '<?php echo trad('le nom', 'admin'); ?>',
                            ajout_code: '<?php echo trad('le code ISO 4217', 'admin'); ?>',
                            ajout_symbole: '<?php echo trad('le symbole', 'admin'); ?>',
                            ajout_taux: '<?php echo trad('le taux de change', 'admin'); ?>'
                        };

                $.each(fields, function(key, value) {
                    if ($('input[name='+key+']').val() == '') {
                        alert("<?php echo trad("Merci d'indiquer ", 'admin'); ?>" + value);
                        $('input[name='+key+']').focus();

                        ok = false;

                        return false;
                    }
                });

                if (! ok) return false;

                $('input[name=flag_ajouter]').val('1');
            }

            $('#formdevise').submit();
	}
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
			   <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="configuration.php" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="devise.php" class="lien04"><?php echo trad('Gestion_devises', 'admin'); ?></a></p>

				<div id="bloc_description">

				    <form action="devise.php" id="formdevise" method="post">
						<input type="hidden" name="action" value="modifier" />

					<!-- Gestion des devises ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

						<div class="entete_liste_config">
							<div class="titre"><?php echo trad('LISTE_DEVISES', 'admin'); ?></div>
							<div class="fonction_valider"><a href="#" onclick="validation(); return false;"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
						</div>

						<ul class="ligne_fonce_BlocDescription lignetop">
							<li style="width:15px;"><?php echo trad('ID', 'admin'); ?></li>
							<li style="width:89px;"><?php echo trad('Designation', 'admin'); ?></li>
							<li style="width:105px; text-align: center;"><a title="<?php echo trad('Voir la liste complète des codes ISO 4217', 'admin'); ?>" style="color: #2F3D46; font-weight: normal" href="http://fr.wikipedia.org/wiki/ISO_4217" target="_blank"><?php echo trad('Code ISO 4217', 'admin'); ?></a></li>
							<li style="width:55px;"><?php echo trad('Symbole', 'admin'); ?></li>
							<li style="width:105px;"><?php echo trad('Taux_actuels', 'admin'); ?></li>
							<li style="width:40px; text-align: center;"><?php echo trad('Défaut', 'admin'); ?></li>
							<li style="width:60px; text-align: center;"><?php echo trad('Supprimer', 'admin'); ?></li>
						</ul>

						<?php
					  	$fond = 'claire';

					  	foreach($devises as $devise) {

					  		?>
							<ul class="ligne_<?php echo $fond; ?>_BlocDescription">
								<li style="width:15px;"><?php echo($devise->id); ?></li>
								<li style="width:89px;"><input type="text" style="width: 80px;" name="nom[<?php echo($devise->id); ?>]" value="<?php echo(htmlspecialchars($devise->nom)); ?>" /></li>
								<li style="width:105px; text-align: center;"><input type="text" style="width: 40px;" name="code[<?php echo($devise->id); ?>]" value="<?php echo(htmlspecialchars($devise->code)); ?>" /></li>
								<li style="width:55px; text-align: center;"><input type="text" style="width: 40px;" name="symbole[<?php echo($devise->id); ?>]" value="<?php echo(htmlspecialchars($devise->symbole)); ?>" /></li>
								<li style="width:105px; text-align: center;"><input type="text" style="width: 60px;" name="taux[<?php echo($devise->id); ?>]" value="<?php echo(htmlspecialchars($devise->taux)); ?>" /></li>
								<li style="width:40px; text-align: center;"><input type="radio" name="defaut" value="<?php echo($devise->id); ?>" <?php if ($devise->defaut) echo 'checked="checked"'; ?>/></li>
								<li style="width:60px; text-align: center;"><a onclick="return confirmer_suppression();" href="devise.php?action=supprimer&id=<?php echo($devise->id); ?>" title="<?php echo trad('Supprimer cette devise ?', 'admin'); ?>"><img src="gfx/supprimer.gif" alt="X" /></a></li>
							</ul>
							<?php

						 	$fond = ($fond == 'claire') ?  'fonce' : 'claire';
						 }
						 ?>
						<ul class="bouton_ajouter ligne_<?php echo $fond; ?>_BlocDescription lignebottom" style="line-height: 28px;">
							<li style="height: 28px; width:277px; border-right: none;"><a href="devise.php?action=refresh" title="<?php echo trad('Mise à jour des taux avec le service en ligne de la BCE', 'admin'); ?>"><?php echo trad('Mettre les taux de change à jour', 'admin'); ?></a></li>
							<li style="height: 28px; width:280px; border-left: none; text-align: right;"><button onclick="show_form_ajouter(true); return false;"><?php echo trad('Ajouter une devise', 'admin'); ?></button></li>
						</ul>

						<div class="form_ajouter" style="display: none;">
							<input type="hidden" name="flag_ajouter" value="0" />
							<ul class="ligne_<?php echo $fond; ?>_BlocDescription">
								<li style="width:15px;">&nbsp;</li>
								<li style="width:89px;"><input type="text" style="width: 80px;" name="ajout_nom" /></li>
								<li style="width:105px; text-align: center;"><input type="text" style="width: 40px;" name="ajout_code" /></li>
								<li style="width:55px; text-align: center;"><input type="text" style="width: 40px;" name="ajout_symbole" /></li>
								<li style="width:105px; text-align: center;"><input type="text" style="width: 60px;" name="ajout_taux" /></li>
								<li style="width:40px; text-align: center;">&nbsp;</li>
								<li style="width:60px; text-align: center;"><a onclick="show_form_ajouter(false); return false;" href="#" title="<?php echo trad('Annuler', 'admin'); ?>"><img src="gfx/supprimer.gif" alt="X" /></a></li>
							</ul>
						</div>
					</form>
				</div>
			</div>
		</div>
	<?php require_once("pied.php");?>
	</div>
</body>
</html>
