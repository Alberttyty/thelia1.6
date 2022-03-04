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

require_once("liste/plugins.php");

/* -- Traitement des actions ---------------------------------- */

$message_erreur = false;

try {

	if (isset($action))
	{
		switch($action){
			case "supprimer":
				ActionsAdminModules::instance()->supprimer($nom);
			break;

			case 'activer':
				ActionsAdminModules::instance()->activer($nom);
			break;

			case 'desactiver':
				ActionsAdminModules::instance()->desactiver($nom);
			break;

			case 'ajouter' :
				if(isset($_FILES['plugin'])) {
					if ($_FILES['plugin']['error'] == UPLOAD_ERR_OK) {
						$plugin = $_FILES['plugin']['tmp_name'];
						$plugin_name = $_FILES['plugin']['name'];

						ActionsAdminModules::instance()->installer($plugin, $plugin_name);

					} else throw new TheliaException(trad("L'envoi du fichier a échoué", 'admin'), TheliaException::MODULE_ECHEC_UPLOAD);
				}
			break;

			case 'modclassement' :
			    $module = new Modules($id);
			    $module->changer_classement($id, $type);

    			redirige("plugins.php#mod_$id");
			break;
		}

		redirige($_SERVER['PHP_SELF']);
	}

	// Mise a jour de la base suivant le contenu du repertoire plugins
	ActionsAdminModules::instance()->mettre_a_jour();

} catch (Exception $ex) {
	$message_erreur = $ex->getMessage() . ' (erreur '.$ex->getCode().')';

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php require_once("title.php");?>

<script src="../lib/jquery/jeditable.js" type="text/javascript"></script>

<script type="text/javascript">

	function supprimer(nom) {
		return confirm("<?php echo trad("Voulez-vous supprimer définitivement le plugin ", 'admin') ?>" + nom + " ?");
	}

	function edit() {
       $(".classement_edit").editable("ajax/classement.php", {
            select : true,
            onblur: "submit",
            cssclass : "ajaxedit",
            tooltip   : "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
            placeholder: "<?php echo trad('Cliquer pour modifier...', 'admin'); ?>",
            indicator : '<img src="gfx/indicator.gif" />',
            callback : function(value, settings) {
            	$("#liste_plugins").load("ajax/plugins.php", function() {
                	edit();
            	});
            }
		});
	}

	$(document).ready(function() {
		edit();
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
   <p align="left">
	   	<a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?> </a>
	   	<img src="gfx/suivant.gif" width="12" height="9" border="0" />
	   	<a href="configuration.php" class="lien04"><?php echo trad('Configuration', 'admin'); ?></a>
	   	<img src="gfx/suivant.gif" width="12" height="9" border="0" />
	   	<a href="plugins.php" class="lien04"><?php echo trad('Gestion_plugins', 'admin'); ?></a>
    </p>

	<div id="bloc_informations">
		<ul style="width:956px; margin-bottom:10px; background-color:red">
			<li class="entete_configuration" style="width:451px"><?php echo trad('AJOUTER_PLUGIN', 'admin'); ?></li>
			<li class="entete_configuration" style="padding:4px 0 5px 0; width:500px" >
				<div class="fonction_ajout" style="padding-top:-10px; margin-right: 5px;">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
						<input type="hidden" name="action" value="ajouter" />
		                <input type="file" name="plugin" class="form" />
		               	<input type="submit" value="<?php echo trad('Valider', 'admin'); ?>" />
					</form>
				</div>
			</li>
		</ul>

	    <?php if ($message_erreur !== false) { ?>
		    <ul style="width:956px; margin-bottom:10px; background-color:red">
		    <li style="padding: 5px; height: auto; width:100%; font-weight: bold; color: #fff;"><?php echo $message_erreur; ?></li>
		    </ul>
	    <?php } ?>

	    <div id="liste_plugins" style="clear: both;">
	    <?php afficher_plugins(); ?>
	    </div>
	</div>

</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>
