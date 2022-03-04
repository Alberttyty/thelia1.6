<?php
include_once(realpath(dirname(__file__)) . "/Mondialrelay.class.php");
autorisation(Mondialrelay::MODULE);

class AdmMondialrelay extends Mondialrelay {

	function update_config() {
        foreach($_REQUEST as $var => $value)
        {
            if (! preg_match('/^'.Mondialrelay::PREFIXE.'/', $var)) continue;

            $cv = new Variable($var);
            $cv->valeur = $value;
            $cv->maj();
        }
	}

    public function make_yes_no_radio($var_name)
    {
        $val = Variable::lire($var_name);

        echo '<input type="radio" name="'.$var_name.'" value="1"'.($val == 1 ? ' checked="checked"':'').'>Oui
              <input type="radio" name="'.$var_name.'" value="0"'.($val == 0 ? ' checked="checked"':'').'>Non';
    }
}

$adm = new AdmMondialrelay();

$commande = $_REQUEST['commande'];

switch($commande) {
        case 'maj_config' :
            $adm->update_config();
        break;
}
?>

<div id="contenu_int">

	<p>
		<a class="lien04" href="accueil.php">Accueil</a>
		<img width="12" height="9" border="0" src="gfx/suivant.gif"><a class="lien04" href="module_liste.php">Modules</a>
		<img width="12" height="9" border="0" src="gfx/suivant.gif"><a class="lien04" href="module.php?nom=<?php echo Mondialrelay::MODULE ?>"><?php echo Mondialrelay::NOMMODULE ?> v<?php echo Mondialrelay::VERSION ?></a>
	</p>

	<div id="bloc_description" style="position: relative;">

		<div class="entete">
			<div class="titre">CONFIGURATION</div>
			<div class="fonction_valider"><a href="#" onclick="document.getElementById('<?php echo Mondialrelay::PREFIXE ?>_form').submit(); return false;">VALIDER LES MODIFICATIONS</a></div>
		</div>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" id="<?php echo Mondialrelay::PREFIXE ?>_form">

        <input type="hidden" name="nom" value="<?php echo Mondialrelay::MODULE ?>" />
        <input type="hidden" name="commande" id="commande" value="maj_config" />

		 	<table width="100%" cellpadding="5" cellspacing="0">
			 	<tr class="claire">
					<td class="designation">Code enseigne Mondialrelay</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_CODE_ENSEIGNE ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_CODE_ENSEIGNE); ?>" /></td>
				</tr>
			 	<tr class="fonce">
					<td class="designation">Clef privée</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_CLE_PRIVEE ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_CLE_PRIVEE); ?>" /></td>
				</tr>
			 	<tr class="claire">
					<td class="designation">Code marque Mondialrelay</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_CODE_MARQUE ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_CODE_MARQUE); ?>" /></td>
				</tr>
			 	<tr class="fonce">
					<td class="designation">Code Pays (FR conseillé)</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_PAYS ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_PAYS); ?>" /></td>
				</tr>
			 	<tr class="fonce">
					<td class="designation">Unité de poids de vos produits</td>
					<td><select name="<?php echo Mondialrelay::NOM_VAR_UNITE_DE_POIDS ?>">
						<option value="kg">Kilogramme</option>
						<option value="g" <?php if (Variable::lire(Mondialrelay::NOM_VAR_UNITE_DE_POIDS) != 'kg') echo 'selected="selected"'; ?>>Gramme</option>
					</select>
				</tr>


				<tr>
					<th colspan="2">INFORMATIONS SUR L'EXPEDITEUR</th>
				</tr>

			 	<tr class="fonce">
					<td class="designation">Nom complet de votre boutique</td>
					<td>
						<input type="text" name="<?php echo Mondialrelay::NOM_VAR_EXPEDITEUR_AD_1 ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_AD_1); ?>" />
						<!-- AD 2 pas utilisée -->
						<input type="hidden" name="<?php echo Mondialrelay::NOM_VAR_EXPEDITEUR_AD_2 ?>" value="" />
					</td>
				</tr>

			 	<tr class="claire">
					<td class="designation">Adresse de votre boutique</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_EXPEDITEUR_AD_3 ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_AD_3); ?>" /></td>
				</tr>

			 	<tr class="fonce">
					<td class="designation">Adresse (suite 1)</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_EXPEDITEUR_AD_4 ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_AD_4); ?>" /></td>
				</tr>
			 	<tr class="claire">
					<td class="designation">Code postal</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_EXPEDITEUR_CP ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_CP); ?>" /></td>
				</tr>
			 	<tr class="fonce">
					<td class="designation">Ville</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_EXPEDITEUR_VILLE ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_VILLE); ?>" /></td>
				</tr>
			 	<tr class="claire">
					<td class="designation">Pays</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_EXPEDITEUR_PAYS ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_PAYS); ?>" /></td>
				</tr>
			 	<tr class="fonce">
					<td class="designation">Langue de l'expédieur (FR conseillé)</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_EXPEDITEUR_LANGUE ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_LANGUE); ?>" /></td>
				</tr>
			 	<tr class="claire">
					<td class="designation">Téléphone fixe</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_EXPEDITEUR_TEL_1 ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_TEL_1); ?>" /></td>
				</tr>
			 	<tr class="fonce">
					<td class="designation">Téléphone mobile</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_EXPEDITEUR_TEL_2 ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_TEL_2); ?>" /></td>
				</tr>
			 	<tr class="clairebottom">
					<td class="designation">Adresse e-mail</td>
					<td><input type="text" name="<?php echo Mondialrelay::NOM_VAR_EXPEDITEUR_MAIL ?>" value="<?php echo Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_MAIL); ?>" /></td>
				</tr>

			</table>
		</form>
	</div>

	<p style="clear: both; margin-top: 30px;color:#505050;font-size:9px;text-align:left;">Plugin Thelia réalisé par <a href="http://www.cqfdev.fr" target="_blank">Franck Allimant / CQFDev</a>, d'après Benoît PASCAL (benoit@benoitpascal.fr). Contact: <a href="http://www.cqfdev.fr" target="_blank">www.cqfdev.fr</a> - +33(0)6.62.38.28.69</p>
</div>
