<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) OpenStudio		                                             */
/*		email : thelia@openstudio.fr		        	                          	 */
/*      web : http://www.openstudio.fr						   						 */
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
/*	    along with this program.  If not, see <http://www.gnu.org/licenses/>.		 */
/*                                                                                   */
/*************************************************************************************/
require_once("pre.php");
require_once("auth.php");

if(! est_autorise("acces_configuration")) exit;

class TlogAdmin extends Tlog
{
    public $niveau;

    function __construct()
    {
    	parent::init();
    }

    private function maj_variable($nom, $valeur) {

        $variable = new Variable();

        if ($variable->charger($nom)) {
            $variable->valeur = $valeur;
            $variable->maj();
        }
        else {
            $variable->nom = $nom;
            $variable->valeur = $valeur;
            $variable->protege = 1;
            $variable->cache = 1;

            $variable->add();
        }
    }

    public function update_config()
    {
    	if (! empty($_REQUEST['fichier'])) {
            $_REQUEST[Tlog::VAR_FILES] = ltrim($_REQUEST[Tlog::VAR_FILES] . ";" . trim($_REQUEST['fichier']), ";");
        }

        foreach($_REQUEST as $var => $value)
        {
            if (! preg_match('/^tlog_/', $var)) continue;

            $this->maj_variable($var, $value);
        }

        // Mise à jour des destinations
        $actives = "";

        foreach($_REQUEST['destinations'] as $classname) {

            if (isset($_REQUEST["${classname}_actif"])) {

                $actives .= $classname . ";";

                foreach($_REQUEST as $var => $valeur) {
                    if (strpos($var, "${classname}_") !== false) {
                        $nom = str_replace("${classname}_", "", $var);

                        if ($nom == 'actif') continue;

                        $this->maj_variable($nom, $valeur);
                    }
                }
            }
        }

        $this->maj_variable(self::VAR_DESTINATIONS, rtrim($actives, ";"));

        redirige("logs.php");
    }

    public function prepare_page() {
            $this->niveau = Variable::lire(Tlog::VAR_NIVEAU, Tlog::DEFAUT_NIVEAU);
    }

    public function liste_destinations() {

        $destinations = array();

        // Charger (et instancier) toutes les destinations.
        $this->charger_classes_destinations($destinations);

        //valeurs bidons à remplacer

        return $destinations;
    }

    public function liste_destinations_actives() {
    	return explode(";", Variable::lire(self::VAR_DESTINATIONS, self::DEFAUT_DESTINATIONS));
    }
}

// -- MAIN -------------------------------------------------------------------------------------

$adm = new TlogAdmin();

$command = $_REQUEST['commande'];

switch($command)
{
    case 'maj_config' :
        $adm->update_config();
    break;
}

$adm->prepare_page();
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

<script type="text/javascript">

    function ms_submit_form()
    {
    	$('#ms_form').submit();
     }

    function delete_file(idx)
    {
    	var files = $('input[name=<?php echo Tlog::VAR_FILES ?>]').val().split(";");

    	files.splice(idx, 1);

    	$('input[name=<?php echo Tlog::VAR_FILES ?>]').val(files.join(';'));

    	$('#ms_form').submit();
    }

    function toggle_destination(id) {
		if ($('input[name='+id+'_actif]').is(':checked')) {
			$('td[rel='+id+'] input, td[rel='+id+'] textarea').removeAttr('disabled');
			$('tr.'+id).show();
		}
		else {
			$('td[rel='+id+'] input, td[rel='+id+'] textarea').attr('disabled', 'disabled');
			$('tr.'+id).hide();
		}
    }

</script>

	<div id="contenu_int">
		<p align="left">
			<span class="lien04"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a></span>
			<img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="configuration.php" class="lien04"> <?php echo trad('Configuration', 'admin'); ?></a>
			<img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="logs.php" class="lien04"> <?php echo trad('Gestion_log', 'admin'); ?></a></p>
		</p>

	    <div id="bloc_description" style="width: 70%; float: left;">

	        <form method="get" action="<?php echo $_SERVER['PHP_SELF'] ?>" id="ms_form">
		        <input type="hidden" name="commande" value="maj_config" />

				<div class="entete">
					<div class="titre">CONFIGURATION</div>
					<div class="fonction_valider"><a href="#" onclick="ms_submit_form(); return false;">VALIDER LES MODIFICATIONS</a></div>
				</div>

		        <table width="100%" cellpadding="5" cellspacing="0">
		 			<tr class="fonce">
						<td class="designation">Définit les messages qui seront affichés<div style="font-size: 0.8em">Tous les messages de niveau supérieur ou égal au niveau courant seront affichés.</div></td>
						<td nowrap="nowrap">
							<select name="<?php echo Tlog::VAR_NIVEAU ?>">
								<option value="<?php echo Tlog::MUET ?>">Désactivé</option>
								<option value="<?php echo Tlog::TRACE ?>" <?php if ($adm->niveau == Tlog::TRACE) echo 'selected="selected"'; ?> >Trace de bas niveau</option>
								<option value="<?php echo Tlog::DEBUG ?>" <?php if ($adm->niveau == Tlog::DEBUG) echo 'selected="selected"'; ?> >Déboguage</option>
								<option value="<?php echo Tlog::INFO ?>" <?php if ($adm->niveau == Tlog::INFO) echo 'selected="selected"'; ?> >Information</option>
								<option value="<?php echo Tlog::WARNING ?>" <?php if ($adm->niveau == Tlog::WARNING) echo 'selected="selected"'; ?> >Avertissement</option>
								<option value="<?php echo Tlog::ERROR ?>" <?php if ($adm->niveau == Tlog::ERROR) echo 'selected="selected"'; ?> >Erreur</option>
								<option value="<?php echo Tlog::FATAL ?>" <?php if ($adm->niveau == Tlog::FATAL) echo 'selected="selected"'; ?> >Erreur fatale</option>
							</select>
						</td>
					</tr>

		 			<tr class="claire">
						<td class="designation">En-tête des lignes de log :<div style="font-size: 0.8em"><ul>
						<li>#NUM : numéro d'ordre</li>
						<li>#NIVEAU : niveau du message</li>
						<li>#FICHIER : nom du fichier</li>
						<li>#FONCTION : nom de la fonction</li>
						<li>#LIGNE : numéro de ligne</li>
						<li>#DATE : date au format aaaa-mm-dd</li>
						<li>#HEURE : heure au format hh:mm:ss</li>
						</ul></div></td>
						<td nowrap="nowrap">
							<input type="text" name="<?php echo Tlog::VAR_PREFIXE ?>" style="width: 400px;" value="<?php echo htmlspecialchars(Variable::lire(Tlog::VAR_PREFIXE, Tlog::DEFAUT_PREFIXE)); ?>" />
						</td>
					</tr>

		 			<tr class="fonce">
						<td class="designation">Afficher les redirections:<div style="font-size: 0.8em">Les redirections via redirige() seront affichées sous forme de liens cliquables.</div></td>
						<td nowrap="nowrap">
							<input type="radio" name="<?php echo Tlog::VAR_SHOW_REDIRECT ?>" value="1" <?php if (Variable::lire(Tlog::VAR_SHOW_REDIRECT, Tlog::DEFAUT_SHOW_REDIRECT) == 1) echo 'checked="checked"'?> /> Oui
							<input type="radio" name="<?php echo Tlog::VAR_SHOW_REDIRECT ?>" value="0" <?php if (Variable::lire(Tlog::VAR_SHOW_REDIRECT, Tlog::DEFAUT_SHOW_REDIRECT) == 0) echo 'checked="checked"'?> /> Non
						</td>
					</tr>

		 			<tr class="fonce">
						<td class="designation">Afficher uniquement pour l'adresse IP :<div style="font-size: 0.8em">Une ou plusieurs adresses IP, séparées par des points-virgules (;). Laisser vide pour afficher les logs pour toutes les adresses IP.</div></td>
						<td nowrap="nowrap">
							<input type="text" name="<?php echo Tlog::VAR_IP ?>" style="width: 400px;" value="<?php echo htmlspecialchars(Variable::lire(Tlog::VAR_IP, Tlog::DEFAUT_IP)); ?>" />

							<p>Votre IP actuelle est <?php echo $_SERVER['REMOTE_ADDR']; ?></p>
						</td>
					</tr>

		 			<tr class="clairebottom">
						<td class="designation">Activer les logs uniquement pour les fichiers ci contre.<div style="font-size: 0.8em">Indiquer le nom du fichier, sans le chemin. ! avant le nom du fichier permet de l'exclure. Utiliser * pour activer les logs sur tous les fichiers</div></td>

						<td nowrap="nowrap">
							<input type="hidden" name="<?php echo Tlog::VAR_FILES ?>" value="<?php echo htmlspecialchars(Variable::lire(Tlog::VAR_FILES, Tlog::DEFAUT_FILES)); ?>" />
							<?php if (Variable::lire(Tlog::VAR_FILES, Tlog::DEFAUT_FILES) != '') { ?>

								<ul style="border-bottom: 1px solid #aaa; margin-bottom: 10px;padding-bottom: 10px;">
								<?php
								$files = explode(";", Variable::lire(Tlog::VAR_FILES, Tlog::DEFAUT_FILES));
								$idx = 0;
								if ($files) foreach($files as $file) {
									?>
									<li>
										<a href="javascript:delete_file(<?php echo $idx; ?>);" title="Désactiver les logs pour ce fichier"><img src="gfx/supprimer.gif" /></a>
										<?php echo $file; if ($file == '*') echo " (tous les fichiers)"; ?>
									</li>
									<?php
									$idx++;
								}
								?>
								</ul>
							<?php } ?>
							Ajouter le fichier : <input type="text" name="fichier" value="" /><input type="submit" value="OK" />
						</td>
					</tr>

		       	</table>

				<div class="entete">
					<div class="titre">DESTINATIONS</div>
					<div class="fonction_valider"><a href="#" onclick="ms_submit_form(); return false;">VALIDER LES MODIFICATIONS</a></div>
				</div>

		        <table width="100%" cellpadding="5" cellspacing="0">
		        	<tr class="fonce">
						<td colspan="2">
						Les destinations sont chargées de stocker ou d'afficher les logs. Par exemple, ils seront affichés à l'écran, stockés dans un fichier, ou envoyés par e-mail...
						<br />
						Vous pouvez choisir et paramétrer une ou plusieurs destinations ci-dessous.
						</td>
					</tr>
                <?php
                $actives = $adm->liste_destinations_actives();

				foreach($adm->liste_destinations() as $nomclasse => $destination) {

                	$titre = $destination->get_titre();
                	$label = $destination->get_description();

                	$active = in_array($nomclasse, $actives);
                	?>

                    <tr>
                        <th colspan="2">
                        	<input type="hidden" name="destinations[]" value="<?php echo $nomclasse;?>" />
                            <input type="checkbox" name="<?php echo $nomclasse;?>_actif" onclick="toggle_destination('<?php echo $nomclasse; ?>');" <?php echo ($active ? 'checked="checked"' : "") ?> /> <strong><?php echo $titre; ?></strong>
                            <?php if(! empty($label)) { ?>
                            <div style="font-wheight: normal; font-size: 0.85em;"><?php echo $label; ?></div>
                            <?php } ?>
                        </th>
                    </tr>

                    <?php
					$disabled = $active ? '' : 'disabled="disabled"';

					$configs = $destination->get_configs();

					$idx = 0;

					foreach($configs as $config) {
						$classe = ($idx++) % 2 ? "fonce" : "claire";
						?>
		                    <tr class="<?php echo "$classe $nomclasse"; ?>" <?php if ($disabled) echo 'style="display: none"'; ?>>
		                        <td class="designation">
		                            <?php echo $config->titre; ?>:
		                            <div style="font-wheight: normal; font-size: 0.85em;"><?php echo $config->label; ?></div>
		                        </td>
		                        <td nowrap="nowrap" class="valeur-destination" rel="<?php echo $nomclasse; ?>">
		                                <?php
		                                switch($config->type) {
		                                    default:
		                                    case TlogDestinationConfig::TYPE_TEXTFIELD:
		                                        ?>
		                                        <input style="width: 400px;" type="text" name="<?php echo $nomclasse;?>_<?php echo $config->nom; ?>" value="<?php echo htmlspecialchars($config->valeur); ?>" <?php echo $disabled ?>/>
		                                        <?php
		                                    break;

		                                    case TlogDestinationConfig::TYPE_TEXTAREA:
		                                        ?>
		                                        <textarea style="width: 400px;height: 100px;" name="<?php echo $nomclasse;?>_<?php echo $config->nom; ?>" <?php echo $disabled ?>><?php echo $config->valeur; ?></textarea>
		                                        <?php
		                                    break;
		                                }
		                                ?>
		                        </td>
		                    </tr>
	 					<?php
					}

					if (count($configs) == 0) {
						?>
      					<tr class="fonce <?php echo $nomclasse; ?>" <?php if ($disabled) echo 'style="display: none"'; ?>>
							<td class="designation" colspan="2">
								Cette destination n'offre pas de possibilité de configuration.
							</td>
						</tr>
						<?php
					}
                }
				?>
				</table>

	         </form>
	     </div>
	</div>

<!-- fin du bloc de description / colonne de gauche -->
</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>