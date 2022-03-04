<?php
#####################################################################################################
#
#					Module pour la plateforme de paiement Systempay
#						Version : 1.0 (révision 37181)
#									########################
#					Développé pour Thelia
#						Version : 1.5.1
#						Compatibilité plateforme : V2
#									########################
#					Développé par Lyra Network
#						http://www.lyra-network.com/
#						10/07/2012
#						Contact : supportvad@lyra-network.com
#
#####################################################################################################

include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
require_once(SITE_DIR."/client/plugins/systempay/config.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");


// Recuperer l'url du site
$variable_loader = new Variable();
$variable_loader->charger("urlsite");
$urlsite = $variable_loader->valeur;
$urlsite = substr($urlsite, -1) == '/' ? $urlsite : $urlsite . '/';	// lors des tests sur certaines config, urlsite n'avait pas de / a la fin

$variable_loader->charger("nomsite");
$nomsite = $variable_loader->valeur;

$fields = array(
		// Module informations
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_DEVELOPPED_BY',
				'label' => 'D&eacute;velopp&eacute; par',
				'default' => '<a href="http://www.lyra-network.com/">Lyra network</a>',
				'type' => 'fixed'
		),
		array(
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_CONTACT_EMAIL',
				'label' => 'Courriel de contact',
				'default' => '<a href="mailto:supportvad@lyra-network.com">supportvad@lyra-network.com</a>',
				'type' => 'fixed'
		),
		array(
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_CONTRIB_VERSION',
				'label' => 'Version du module',
				'default' => '1.0',
				'type' => 'fixed'
		),
		array(
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_GATEWAY_VERSION',
				'label' => 'Version de la plateforme',
				'default' => 'V2',
				'type' => 'fixed'
		),
		array(
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_CMS_VERSION',
				'label' => 'Test&eacute; avec',
				'default' => 'Thelia 1.5.1',
				'type' => 'fixed'
		),
	
		// payment platform access params
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_SITE_ID',
				'default' => '12345678',
				'label' => 'Identifiant de votre site',
				'type' => 'text'
		),
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_KEY_TEST',
				'default' => '1111111111111111',
				'label' => 'Certificat en mode test',
				'type' => 'text'
		),
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_KEY_PROD',
				'default' => '2222222222222222',
				'label' => 'Certificat en mode production',
				'type' => 'text'
		),
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_CTX_MODE',
				'default' => 'TEST',
				'label' => 'Mode de fonctionnement',
				'type' => 'radio',
				'options' => array('TEST', 'PRODUCTION') 
		),
		array(
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_PLATFORM_URL',
				'default' => 'https://paiement.systempay.fr/vads-payment/',
				'label' => 'Url de la page de paiement',
				'type' => 'text'
		),
		
		// payment page params
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_LANGUAGE',
				'default' => 'fr',
				'label' => 'Langue par d&eacute;faut',
				'type' => 'select',
				'options' => array(
						'fr' => 'Fran&ccedil;ais',
		    			'de' => 'Allemand',
		    			'en' => 'Anglais',
		    			'es' => 'Espagnol',
		    			'zh' => 'Chinois',
		    			'it' => 'Italien',
		    			'ja' => 'Japonnais',
		    			'pt' => 'Portugais',
		    			'nl' => 'N&eacute;erlandais'						
				)
		),
		array(
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_AVAILABLE_LANGUAGES',
				'default' => '',
				'label' => 'Langues disponibles',
				'type' => 'multiselect',
				'options' => array(
						'' => 'Toutes',
						'fr' => 'Fran&ccedil;ais',
						'de' => 'Allemand',
						'en' => 'Anglais',
						'es' => 'Espagnol',
						'zh' => 'Chinois',
						'it' => 'Italien',
						'ja' => 'Japonnais',
						'pt' => 'Portugais',
						'nl' => 'N&eacute;erlandais'
				)
		),
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_CAPTURE_DELAY',
				'default' => '',
				'label' => 'D&eacute;lai avant remise en banque',
				'type' => 'text'
		),
		array(
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_VALIDATION_MODE',
				'default' => '',
				'label' => 'Mode de validation',
				'type' => 'select',
				'options' => array(
						'' => 'Par d&eacute;faut',
						'0' => 'Automatique',
						'1' => 'Manuel'
				)
		),
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_PAYMENT_CARDS',
				'default' => '',
				'label' => 'Types de carte',
				'type' => 'multiselect',
				'options' => array(
						'' => 'Toutes',
	    				'AMEX' => 'American express',
	    				'CB' => 'CB',
	    				'MASTERCARD' => 'Mastercard',
	    				'VISA' => 'Visa'
				)
		),
	
		// amount restrictions
		array(
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_AMOUNT_MIN',
				'default' => '',
				'label' => 'Montant minimum',
				'type' => 'text'
		),
		array(
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_AMOUNT_MAX',
				'default' => '',
				'label' => 'Montant maximum',
				'type' => 'text'
		),
	
		// return to store params
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_REDIRECT_ENABLED', 
				'default' => 'False',
				'label' => 'Redirection automatique vers la boutique &agrave; la fin du paiement',
				'type' => 'radio',
				'options' => array('True', 'False')
		),
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_REDIRECT_SUCCESS_TIMEOUT',
				'default' => '5',
				'label' => 'Temps avant redirection (paiement avec succ&egrave;s)',
				'type' => 'text'
		),
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_REDIRECT_SUCCESS_MESSAGE',
				'default' => 'Paiement accept&eacute;, vous allez &ecirc;tre redirig&eacute; dans quelques instants',
				'label' => 'Message avant redirection (paiement avec succ&egrave;s)',
				'type' => 'text'
		),
		array(
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_REDIRECT_ERROR_TIMEOUT',
				'default' => '5',
				'label' => 'Temps avant redirection (paiement &eacute;chou&eacute;)',
				'type' => 'text'
		),
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_REDIRECT_ERROR_MESSAGE',
				'default' => 'Un probl&egrave;me est survenu, vous allez &ecirc;tre redirig&eacute; dans quelques instants',
				'label' => 'Message avant redirection (paiement &eacute;chou&eacute;)',
				'type' => 'text'
		),
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_RETURN_MODE',
				'default' => 'GET',
				'label' => 'Mode de retour',
				'type' => 'select',
				'options' => array(
						'GET' => 'GET',
						'POST' => 'POST'
				)
		),
		array(	
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_URL_RETURN', 
				'default' => $urlsite . 'client/plugins/systempay/confirmation.php',
				'label' => 'URL de retour &agrave; la boutique',
				'type' => 'text'
		),		
		array(
				'name' => 'MODULE_PAYMENT_SYSTEMPAY_URL_CHECK',
				'default' => $urlsite . 'client/plugins/systempay/confirmation.php',
				'label' => 'Url serveur &agrave; renseigner dans le back office Systempay',
				'type' => 'fixed'
		)
);

function generate_config($data) {
	//TODO pas dramatique (utilisateur admin) : filtrer les donnees du formulaire
	global $fields;
	
	$conf_file = realpath(dirname(__FILE__)) . "/config.php";
	$f_socket = fopen($conf_file, "w");
	if($f_socket === false) {
		die("Impossible d'ouvrir le fichier $conf_file en &eacute;criture. V&eacute;rifiez que votre serveur dispose des droits n&eacute;cessaires.");
	}
	
	fwrite($f_socket,"<?php\n");
	fwrite($f_socket,"#####################################################################################################
#
#					Module pour la plateforme de paiement Systempay
#						Version : 1.0 (révision 37181)
#									########################
#					Développé pour Thelia
#						Version : 1.5.1
#						Compatibilité plateforme : V2
#									########################
#					Développé par Lyra Network
#						http://www.lyra-network.com/
#						10/07/2012
#						Contact : supportvad@lyra-network.com
#
#####################################################################################################");
	fwrite($f_socket,"\n");
	
	foreach($fields as $i => $field) {
		$default = defined($field['name']) ? constant($field['name']) : $field['default'];
		$value = array_key_exists($field['name'], $data) ? $data[$field['name']] : $default;
		
		if(is_array($value)) {
			$value = implode(';', $value);
		}
		
		$value = $field['type'] == 'text' ? addslashes($value) : $value;
		
		$fields[$i]['value'] = $value; // $fields[$i] is the real thing, $field is just a copy (php foreach mechanism)
		fwrite($f_socket, "define('" . $field['name'] . "', '" . $value . "');\n");
	}
	
	fwrite($f_socket,"?>");
}

generate_config($_POST);

// Inclusion du fichier de conf tout juste genere
include_once(realpath(dirname(__FILE__)) . "/config.php");
?>

<div id="contenu_int">
	<form action="" method="POST">
		<fieldset style="margin : 5px; background-color: white;">
			<legend>Param&eacute;trage du module de paiement Systempay</legend>
			<?php
			foreach($fields as $field) {
				// Label
				echo '<label style="width:250px; display:block; float:left; margin-right:20px;text-align:right;">'.$field["label"].'</label>';
				
				// Input
				echo '<div style="display:block; float:left;">';
					
				$value = stripslashes($field['value']);				
				switch ($field['type']) {
					case 'text':
						echo '<input type="text" name="'.$field['name'].'" value="'.$value.'" size="75"/>';
						break;
						
					case 'radio':
						foreach($field['options'] as $option) {
							$checked = ($value == $option) ? ' checked="checked"' : '';
							echo '<input type="radio" name="'.$field['name'].'" value="'.$option.'"'.$checked.'/>';
							echo $option."&nbsp;&nbsp;";
						}
						break;
						
					case 'select':
						echo '<select name="'.$field['name'].'">';
						foreach ($field['options'] as $key => $option) {
							echo '<option value="'. $key . '"' . ($key == $value ? ' selected="selected"' : '') . '>';
							echo $option;
							echo '</option>';
						}
						echo ' </select>';
						break;
						
					case 'multiselect':
						echo '<select name="'.$field['name'].'[]" multiple="multiple">';

						$valueArray = explode(';', $value);
						if(!is_array($valueArray) || empty($valueArray) || in_array('', $valueArray)) {
							$valueArray = array(''); // if all is select, other selections are ignored
						}
						
						foreach ($field['options'] as $key => $option) {
							echo '<option value="'. $key . '"' . (in_array($key, $valueArray) ? ' selected="selected"' : '') . '>';
							echo $option;
							echo '</option>';
						}
						echo ' </select>';
						break;
						
					default:
						echo $value;
						break;
				}
				
				echo '</div>';
				
				// css clear
				echo '<div style="clear:both; height:0.5em;"></div>';
			}
			?>
			<br/>
			<input type="submit" value="Valider"/>
		</fieldset>
	</form>
</div>