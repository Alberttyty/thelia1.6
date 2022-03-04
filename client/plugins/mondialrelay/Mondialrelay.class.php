<?php
include_once(dirname(__FILE__) . "/../../../classes/PluginsTransports.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Commande.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Variable.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Mail.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Client.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Raisondesc.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Message.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Messagedesc.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Venteadr.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");

require_once(dirname(__FILE__) . '/lib/nusoap.php');

class Mondialrelay extends PluginsTransports {

	const VERSION   = '2.0.2';
	const MODULE    = 'mondialrelay';
	const NOMMODULE = 'Mondial Relay';
	const PREFIXE   = 'mr_';

	const NOM_VAR_CODE_ENSEIGNE = "mr_code_enseigne";
	const NOM_VAR_CLE_PRIVEE = "mr_cle_privee";
	const NOM_VAR_CODE_MARQUE = "mr_code_marque";
	const NOM_VAR_PAYS = "mr_pays";

	const NOM_VAR_EXPEDITEUR_AD_1 = "mr_expediteur_ad_1";
	const NOM_VAR_EXPEDITEUR_AD_2 = "mr_expediteur_ad_2";
	const NOM_VAR_EXPEDITEUR_AD_3 = "mr_expediteur_ad_3";
	const NOM_VAR_EXPEDITEUR_AD_4 = "mr_expediteur_ad_4";
	const NOM_VAR_EXPEDITEUR_VILLE = "mr_expediteur_ville";
	const NOM_VAR_EXPEDITEUR_CP = "mr_expediteur_cp";
	const NOM_VAR_EXPEDITEUR_PAYS = "mr_expediteur_pays";
	const NOM_VAR_EXPEDITEUR_TEL_1 = "mr_expediteur_tel_1";
	const NOM_VAR_EXPEDITEUR_TEL_2 = "mr_expediteur_tel_2";
	const NOM_VAR_EXPEDITEUR_MAIL = "mr_expediteur_mail";
	const NOM_VAR_EXPEDITEUR_LANGUE = "mr_expediteur_langue";
	const NOM_VAR_UNITE_DE_POIDS = "mr_unite_de_poids";

	public $id;
	public $commande;
	public $point;
	public $nom;
	public $adresse1;
	public $adresse2;
	public $adresse3;
	public $cpostal;
	public $ville;
	public $client;
	public $expedition;
	public $tel;
	public $email;

	public $table = "mondialrelay_commande";

	public $bddvars = array("id", "commande",  "point", "nom", "adresse1", "adresse2", "adresse3", "cpostal", "ville", "client", "expedition", "tel", "email");

	private $soap_client = false;

	private $last_error = "Pas d'erreur";

	private $statCode = array(
		'1' => 'Enseigne invalide',
		'2'	=> 'Numéro d\'enseigne vide ou inexistant',
		'3' => 'Numéro de compte enseigne invalide',
		'5'	=> 'Numéro de dossier enseigne invalide',
		'7' => 'Numéro de client enseigne invalide',
		'9' => 'Nom de ville non reconnu ou non unique',
		'10' => 'Type de collecte invalide ou incorrect (1/D > Domicile -- 3/R > Relais)',
		'11' => 'Numéro de Point Relais de collecte invalide',
		'12' => 'Pays du Point Relais de collecte invalide',
		'13' => 'Type de livraison invalide ou incorrect (1/D > Domicile -- 3/R > Relais)',
		'14' => 'Numéro du Point Relais de livraison invalide',
		'15' => 'Pays du Point Relais de livraison invalide',
		'16' => 'Code pays invalide',
		'17' => 'Adresse invalide',
		'18' => 'Ville invalide',
		'19' => 'Code postal invalide',
		'20' => 'Poids du colis invalide',
		'21' => 'Taille (Longueur + Hauteur) du colis invalide',
		'22' => 'Taille du Colis invalide',
		'24' => 'Numéro de Colis Mondial Relay invalide',
		'29' => 'Mode de livraison invalide',
		'30' => 'Adresse (L1) de l\'expéditeur invalide',
		'31' => 'Adresse (L2) de l\'expéditeur invalide',
		'33' => 'Adresse (L3) de l\'expéditeur invalide',
		'34' => 'Adresse (L4) de l\'expéditeur invalide',
		'35' => 'Ville de l\'expéditeur invalide',
		'36' => 'Code postal de l\'expéditeur invalide',
		'37' =>	'Pays de l\'expéditeur invalide',
		'38' => 'Numéro de téléphone de l\'expéditeur invalide',
		'39' => 'Adresse e-mail de l\'expéditeur invalide',
		'40' => 'Action impossible sans ville ni code postal',
		'41' => 'Mode de livraison invalide',
		'42' => 'Montant CRT invalide',
		'43' => 'Devise CRT invalide',
		'44' => 'Valeur du colis invalide',
		'45' => 'Devise de la valeur du colis invalide',
		'46' => 'Plage de numéro d\'expédition épuisée',
		'47' => 'Nombre de colis invalide',
		'48' => 'Multi-colis en Point Relais Interdit',
		'49' => 'Mode de collecte ou de livraison invalide',
		'50' => 'Adresse (L1) du destinataire invalide',
		'51' => 'Adresse (L2) du destinataire invalide',
		'53' => 'Adresse (L3) du destinataire invalide',
		'54' => 'Adresse (L4) du destinataire invalide',
		'55' => 'Ville du destinataire invalide',
		'56' => 'Code postal du destinataire invalide',
		'57' => 'Pays du destinataire invalide',
		'58' => 'Numéro de téléphone du destinataire invalide',
		'59' => 'Adresse e-mail du destinataire invalide',
		'60' => 'Champ texte libre invalide',
		'61' => 'Top avisage invalide',
		'62' => 'Instruction de livraison invalide',
		'63' => 'Assurance invalide ou incorrecte',
		'64' => 'Temps de montage invalide',
		'65' => 'Top rendez-vous invalide',
		'66' => 'Top reprise invalide',
		'70' => 'Numéro de Point Relais invalide',
		'72' => 'Langue expéditeur invalide',
		'73' => 'Langue destinataire invalide',
		'74' => 'Langue invalide',
		'80' => 'Code tracing : Colis enregistré',
		'81' => 'Code tracing : Colis en traitement chez Mondial Relay',
		'82' => 'Code tracing : Colis livré',
		'83' => 'Code tracing : Anomalie',
		'90' => 'AS400 indisponible',
		'91' => 'Numéro d\'expédition invalide',
		'94' => 'Colis Inexistant',
		'95' => 'Compte Enseigne non activé',
		'96' => 'Type d\'enseigne incorrect en Base',
		'97' => 'Clé de sécurité invalide',
		'98' => 'Service Indisponible',
		'99' => 'Erreur générique du service. Cette erreur peut être dû autant à un problème technique du service qu\'à des données incorrectes ou inexistantes dans la Base de Données. Lorsque vous avez cette erreur veuillez la notifier à Mondial Relay en précisant la date et l\'heure de la connexion ainsi que les informations envoyés au WebService afin d\'effectuer une vérification.'
	);

	public function __construct() {
		parent::__construct(self::NOMMODULE);
	}

	public function init() {

		$this->ajout_desc(
				self::NOMMODULE,
				"Mondial Relay",
				"Livraison par Mondial Relay",
				1);

		$query = "CREATE TABLE IF NOT EXISTS `$this->table` (
						 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						 `commande` INT NOT NULL ,
						 `point` TEXT NOT NULL ,
						 `nom` TEXT NOT NULL ,
						 `adresse1` TEXT NOT NULL ,
						 `adresse2` TEXT NOT NULL,
						 `adresse3` TEXT NOT NULL,
						 `cpostal` TEXT NOT NULL,
						 `ville` TEXT NOT NULL,
						 `client`	INT NOT NULL,
						 `expedition` TEXT NOT NULL,
						 `tel` TEXT NOT NULL,
						 `email` TEXT NOT NULL
						 )";

		$resul = $this->query($query);

		$this->query("ALTER TABLE `$this->table` CHANGE `expedition` `expedition` TEXT NOT NULL ");
		$this->query("ALTER TABLE `$this->table` CHANGE `tel` `tel` TEXT NOT NULL ");

		$message = new Message();

		if (! $message->charger("mondialrelay")) {
			$message->nom = "mondialrelay";

			$lastid = $message->add();

			$messagedesc = new Messagedesc();
			$messagedesc->message = $lastid;
			$messagedesc->lang = 1;
			$messagedesc->titre = "Expédition de votre commande __COMMANDE__";
			$messagedesc->intitule = "Mail d'expédition Mondial Relay";
			$messagedesc->description = "
Bonjour __RAISON__ __NOM__,<br />
<br />
Nous vous remercions de votre commande chez __NOMSITE__<br />
<br />
Un colis concernant votre commande __COMMANDE__ du __DATE__ __HEURE__ a quitté nos entrepôts pour être pris en charge par Mondial Relay le __DATEDJ__.<br />
Son numéro de suivi est le suivant : <strong>__COLIS__</strong><br />
Il vous permet de suivre votre colis en ligne <a href=\"__URL_SUIVI__\">sur le site de Mondial Relay</a>.<br />
Votre colis vous sera livré au point relais suivant:<br />
<br />
__INFO_POINT_RELAIS__<br />
<br />
Pour obtenir tous les détails sur ce point relais, <a href=\"__URL_DETAIL__\">merci de cliquer ici</a>.<br />
<br />
Nous restons à votre disposition pour toute information complémentaire.<br />
<br />
Bien cordialement,<br />
<br />
L'équipe de __NOMSITE__.<br />
";

						$messagedesc->descriptiontext = "
Bonjour __RAISON__ __NOM__,

Nous vous remercions de votre commande chez __NOMSITE__.

Un colis concernant votre commande __COMMANDE__ du __DATE__ __HEURE__ a quitté nos entrepôts pour être pris en charge par Mondial Relay le __DATEDJ__.
Son numéro de suivi est le suivant : __COLIS__
Il vous permet de suivre votre colis en ligne à l'adresse suivante:

__URL_SUIVI__

Votre colis vous sera livré au point relais suivant:

__INFO_POINT_RELAIS__

Pour obtenir tous les détails sur ce point relais, merci de vous rendre à l'adresse suivante:

__URL_DETAIL__

Nous restons à votre disposition pour toute information complémentaire.

Bien cordialement,

L'équipe de __NOMSITE__.
";

			$messagedesc->add();
		}

		$this->create_var(self::NOM_VAR_CODE_ENSEIGNE, 'code enseigne');
		$this->create_var(self::NOM_VAR_CLE_PRIVEE, 'clef privée');
		$this->create_var(self::NOM_VAR_CODE_MARQUE, 'code marque');
		$this->create_var(self::NOM_VAR_PAYS, 'FR');

		$this->create_var(self::NOM_VAR_EXPEDITEUR_AD_1, Variable::lire('nomsite'));
		$this->create_var(self::NOM_VAR_EXPEDITEUR_AD_2, 'inutilisé');
		$this->create_var(self::NOM_VAR_EXPEDITEUR_AD_3, 'Adresse');
		$this->create_var(self::NOM_VAR_EXPEDITEUR_AD_4, 'Adresse (suite 1)');
		$this->create_var(self::NOM_VAR_EXPEDITEUR_VILLE, 'Ville');
		$this->create_var(self::NOM_VAR_EXPEDITEUR_CP, 'Code postal');
		$this->create_var(self::NOM_VAR_EXPEDITEUR_PAYS, 'FR');
		$this->create_var(self::NOM_VAR_EXPEDITEUR_LANGUE, 'FR');
		$this->create_var(self::NOM_VAR_EXPEDITEUR_TEL_1, 'Téléphone');
		$this->create_var(self::NOM_VAR_EXPEDITEUR_TEL_2, 'Téléphone');
		$this->create_var(self::NOM_VAR_EXPEDITEUR_MAIL, Variable::lire('emailcontact'));
		$this->create_var(self::NOM_VAR_UNITE_DE_POIDS, 'Kg');
	}

	private function create_var($nom, $valeur) {

		$var = new Variable();

		if ($var->charger($nom)) {
			$var->valeur = $valeur;
			$var->maj();
		}
		else {
			$var->nom = $nom;
			$var->valeur = $valeur;
			$var->cache = 1;
			$var->protege = 1;

			$var->add();
		}
	}

	public function destroy() {
		$message = new Message();
		if ($message->charger("mondialrelay")) $message->delete();
	}

	public function charger_par_commande($idcommande) {
		return $this->getVars("select * from $this->table where commande=".intval($idcommande));
	}

	public function charger_id($id) {
		return $this->getVars("select * from $this->table where id=".intval($id));
	}

	private function get_soap_client() {

		if ($this->soap_client == false) {
			$this->soap_client = new nusoap_client("http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL", true);
			$this->soap_client->soap_defencoding = 'utf-8';
		}

		return $this->soap_client;
	}

  function calcule(){
    require_once(SITE_DIR."client/plugins/mondialrelay/config.php");
    return mondialrelay_calcul($this->zone,$this->nbart,$this->total,$this->poids);
	}

	public function action() {

		if ($_REQUEST["action"] == "transport") {
			unset($_SESSION["num_point_relay"]);
			if(isset($_REQUEST["num_relay"])) $_SESSION["num_point_relay"] = $_REQUEST["num_relay"];
		}
	}

	public function aprescommande($commande) {

		// Tlog::debug("Apres commande:", $adr);

		if ($this->est_module_de_transport_pour($commande) && isset($_SESSION["num_point_relay"])) {

			$this->commande = $commande->id;

			$this->point = $_SESSION["num_point_relay"];

			$rd = new Raisondesc();
			$rd->charger($_SESSION['navig']->client->raison);

			$this->nom =  $rd->court ." ". $_SESSION["navig"]->client->nom ." ". $_SESSION["navig"]->client->prenom;
			$this->client = $_SESSION['navig']->client->id;
			$this->adresse1 = $_SESSION['navig']->client->adresse2;
			$this->adresse2 = $_SESSION['navig']->client->adresse1;
			$this->adresse3 = $_SESSION['navig']->client->adresse3;
			$this->cpostal = $_SESSION['navig']->client->cpostal;
			$this->ville = $_SESSION['navig']->client->ville;
			$this->tel = empty($_SESSION['navig']->client->telfixe) ? $_SESSION['navig']->client->telport : $_SESSION['navig']->client->telfixe;
			$this->email = $_SESSION['navig']->client->email;

			$result = $this->add();

			// Mise à jour de l'adresse de livraison
			$detailPoint = $this->infosPointRelais($this->point);

			if ($detailPoint !== false) {

				$adr = new Venteadr();

				// Tlog::debug("Adrlivr: $commande->adrlivr");

				if ($adr->charger($commande->adrlivr)) {

					$adr->nom = $detailPoint['LgAdr1'];
					$adr->prenom = "Point relais Mondial Relay n° ".$detailPoint['Num'];
					$adr->adresse1 = $detailPoint['LgAdr2'];
					$adr->adresse2 = $detailPoint['LgAdr3'];
					$adr->adresse3 = $detailPoint['LgAdr4'];
					$adr->cpostal = $detailPoint['CP'];
					$adr->ville = $detailPoint['Ville'];

					$adr->tel = '';

					// Tlog::debug("Adresse:", $adr);

					$adr->maj();
				}
			}

			//unset($_SESSION["num_point_relay"]);
		}
	}

	public function statut ($commande) {
		if ($commande->statut == "4" && $this->est_module_de_transport_pour($commande)) // Envoyé
		{
			// On n'envoie pas de mail si il n'y a pas de numéro de suivi indiqué
			if (empty($commande->colis)) return;

			if ($this->charger_par_commande($commande->id)) {

				$detailPoint = $this->infosPointRelais($this->point);

				if ($detailPoint !== false) {

					$message = new Message();
					$message->charger("mondialrelay");

					$messagedesc = new Messagedesc();
					$messagedesc->charger($message->id);

					$client = new Client();
					$client->charger_id($commande->client);

					$sujet = $this->substitutions_mail($messagedesc->titre, $commande, $client, $detailPoint);
					$messagetext = $this->substitutions_mail($messagedesc->descriptiontext, $commande, $client, $detailPoint);
					$messagehtml = $this->substitutions_mail($messagedesc->description, $commande, $client, $detailPoint);

					$emailcontact = new Variable();
					$emailcontact->charger("emailcontact");

					//envoi du mail au client
					Mail::envoyer (
						$client->nom." ".$client->prenom, $client->email,
						Variable::lire('nomsite'), Variable::lire('emailcontact'),
						$sujet,
						$messagehtml, $messagetext
					);
				}
			}
		}
	}

	private function substitutions_mail($message, $commande, $client, $detailPoint) {

		$date = strtotime($commande->date);

		$rd = new Raisondesc();
		$rd->charger($client->raison);

		$message = str_replace("__RAISON__", $rd->long, $message);
		$message = str_replace("__NOM__", $client->nom, $message);
		$message = str_replace("__PRENOM__", $client->prenom, $message);
		$message = str_replace("__URLSITE__", Variable::lire('urlsite'), $message);
		$message = str_replace("__NOMSITE__", Variable::lire('nomsite'), $message);

		$message = str_replace("__COMMANDE__", $commande->ref, $message);
		$message = str_replace("__DATE__", strftime("%d/%m/%Y", $date), $message);
		$message = str_replace("__HEURE__", strftime("%H:%M:%S", $date), $message);
		$message = str_replace("__DATEDJ__", strftime("%d/%m/%Y", time()), $message);
		$message = str_replace("__COLIS__", $commande->colis, $message);

		$libelle =
			 $detailPoint['LgAdr1'].' '
			.$detailPoint['LgAdr2'].' '
			.$detailPoint['LgAdr3'].' '
			.$detailPoint['LgAdr4'].', '
			.$detailPoint['CP'].' '
			.$detailPoint['Ville']
		;

		$message = str_replace("__URL_SUIVI__", $this->url_popup_suivi($commande->colis), $message);
		$message = str_replace("__URL_DETAIL__", $this->url_popup_point_relais($detailPoint['Num']), $message);
		$message = str_replace("__INFO_POINT_RELAIS__", $libelle, $message);

		return $message;
	}

	public function boucle ($texte, $args) {

		$res = "";

		$name = strtolower(lireTag ($args,"nom"));

		if ($name == "suivirelay") {

			$cmd = intval(lireTag($args, "commande"));

			if ($this->charger_par_commande($cmd) && !empty($this->expedition) ) {

				$detailPoint = $this->infosPointRelais($this->point);

				if ($detailPoint !== false) {

					$res = $texte;

					$res = str_replace("#NUM", $detailPoint['Num'], $res);
					$res = str_replace("#VILLE", $detailPoint['Ville'], $res);
					$res = str_replace("#NOM", $detailPoint['LgAdr1'], $res);
					$res = str_replace("#CP", $cp, $res);
					$res = str_replace("#ADRESSE1", $detailPoint['LgAdr1'], $res);
					$res = str_replace("#ADRESSE2", $detailPoint['LgAdr2'], $res);
					$res = str_replace("#ADRESSE3", $detailPoint['LgAdr3'], $res);
					$res = str_replace("#DETAILPR", $this->url_popup_point_relais($detailPoint['Num']), $res);
					$res = str_replace("#SUIVI", $this->url_popup_suivi($this->expedition), $res);
					$res = str_replace("#URLPLAN", $detailPoint['URL_Plan'], $res);
				}
			}
		}
		else if ($name == "adresserelay") {

			if (isset($_SESSION["num_point_relay"])) {

				// Mise à jour de l'adresse de livraison
				$detailPoint = $this->infosPointRelais($_SESSION["num_point_relay"]);

				if ($detailPoint !== false) {

					$res = $texte;

					$res = str_replace("#NUM", $detailPoint['Num'], $res);
					$res = str_replace("#VILLE", $detailPoint['Ville'], $res);
					$res = str_replace("#NOM", $detailPoint['LgAdr1'], $res);
					$res = str_replace("#CP", $cp, $res);
					$res = str_replace("#ADRESSE1", $detailPoint['LgAdr1'], $res);
					$res = str_replace("#ADRESSE2", $detailPoint['LgAdr2'], $res);
					$res = str_replace("#ADRESSE3", $detailPoint['LgAdr3'], $res);
					$res = str_replace("#DETAILPR", $this->url_popup_point_relais($detailPoint['Num']), $res);
					$res = str_replace("#URLPLAN", $detailPoint['URL_Plan'], $res);
				}
			}
		}
		else if ($name == "mondialrelay") {

			$num = lireTag ($args,"num");

			if ($num == '') $num = PHP_INT_MAX;

			if($_REQUEST["action"] == "recherche_cp")
			{
				$ville = "";
				$cp = $_REQUEST["cp_relay"];
			}
			else
			{
				$ville = $_SESSION["navig"]->client->ville;
				$cp = $_SESSION["navig"]->client->cpostal;
			}

			$taille = "";
			$poids = $this->poids;
			$action = "";

			$PR = $this->rechercheRelay($ville, $cp, $taille, $poids, $action);

			// Tlog::debug($PR);

			if ($PR) foreach($PR as $key => $relais) {

				foreach($relais as &$item) $item = trim($item);

				// if (substr($key, 0, 2) != 'PR' || empty($relais['Num'])) continue;

				$num_p = $relais['Num'];
				$nom = $relais['LgAdr1'];
				$cp = $relais['CP'];
				$adresse1 = $relais['LgAdr1'];
				$adresse2 = $relais['LgAdr2'];
				$adresse3 = $relais['LgAdr3'];
				$ville = $relais['Ville'];
				$pays = $relais['Pays'];

				$tmp = $texte;

				$tmp = str_replace("#NUM", $num_p, $tmp);
				$tmp = str_replace("#VILLE", ucwords(strtolower($ville)), $tmp);
				$tmp = str_replace("#NOM", $nom, $tmp);
				$tmp = str_replace("#CP", $cp, $tmp);
				$tmp = str_replace("#ADRESSE1", ucwords(strtolower($adresse1)), $tmp);
				$tmp = str_replace("#ADRESSE2", ucwords(strtolower($adresse2)), $tmp);
				$tmp = str_replace("#ADRESSE3", ucwords(strtolower($adresse3)), $tmp);
				$tmp = str_replace("#DETAILPR", $this->url_popup_point_relais($num_p), $tmp);
				$tmp = str_replace("#LATITUDE", floatval(str_replace(',', '.', $relais['Latitude'])), $tmp);
				$tmp = str_replace("#LONGITUDE", floatval(str_replace(',', '.', $relais['Longitude'])), $tmp);

				$res .= $tmp;

				if ($num-- <= 0) break;
			}
		}

		return $res;
	}

	public function url_popup_point_relais($num) {

		$crc = md5("<".Variable::lire(self::NOM_VAR_CODE_MARQUE).">$num".Variable::lire(self::NOM_VAR_PAYS)."<".Variable::lire(self::NOM_VAR_CLE_PRIVEE).">");

		return "http://www.mondialrelay.com/public/permanent/details_relais.aspx?ens=".Variable::lire(self::NOM_VAR_CODE_MARQUE)."&num=$num&pays=".Variable::lire(self::NOM_VAR_PAYS)."&crc=$crc";
	}

	public function url_popup_suivi($expedition) {

		if (! empty($expedition)) {
			$crc = md5("<".Variable::lire(self::NOM_VAR_CODE_MARQUE).">$expedition<".Variable::lire(self::NOM_VAR_CLE_PRIVEE).">");

			return "http://www.mondialrelay.fr/lg_fr/espaces/url/popup_exp_details.aspx?cmrq=".Variable::lire(self::NOM_VAR_CODE_MARQUE)."&nexp=$expedition&crc=$crc";
		}

		return "";
	}

	public function infosPointRelais($numero) {
		$client = $this->get_soap_client();

		$params = array(
			'Enseigne' => Variable::lire(self::NOM_VAR_CODE_ENSEIGNE),
			'Num'      => $numero,
			'Pays'     => Variable::lire(self::NOM_VAR_PAYS)
		);

		$result = $this->call('WSI2_DetailPointRelais', $params);

		if ($result) {
			$result = $result['WSI2_DetailPointRelaisResult'];
		}

		return $result;
	}

	private function rechercheRelay($ville, $cp, $taille, $poids, $action) {
		$res = array();

		$params = array(
			'Enseigne' => Variable::lire(self::NOM_VAR_CODE_ENSEIGNE),
			'Pays' => Variable::lire(self::NOM_VAR_PAYS),
			'Ville' => $ville,
			'CP' => $cp,
			'Taille' => $taille,
			'Poids' => $poids,
			'Action' => $action
		);

    //var_dump($params);

		$result = $this->call('WSI2_RecherchePointRelaisAvancee',$params);

    //var_dump($result);

		if ($result) {
			$result = $result['WSI2_RecherchePointRelaisAvanceeResult']['ListePR']['ret_WSI2_sub_PointRelaisAvancee'];
		}

		return $result;
	}

	public function getUrlEtiquettes($expeditions, $langue) {
		$params = array(
			'Enseigne' => Variable::lire(self::NOM_VAR_CODE_ENSEIGNE),
			'Expeditions' => $expeditions,
			'Langue' => $langue,
		);

		$result = $this->call('WSI2_GetEtiquettes',$params);

		if ($result) {
			$result = $result['WSI2_GetEtiquettesResult']['URL_PDF_A5'];
		}

		return $result;
	}

	public function expedition($expeditions, $langue) {
		$params = array(
				'Enseigne' => Variable::lire(self::NOM_VAR_CODE_ENSEIGNE),
				'Expeditions' => $expeditions,
				'Langue' => $langue,
		);

		$result = $this->call('WSI2_GetEtiquettes',$params);

		if ($result) {
			$result = $result['WSI2_GetEtiquettesResult']['URL_PDF_A5'];
		}

		return $result;
	}

	public function call($ws_name, &$params) {

		$this->last_error = "Pas d'erreur";

		$client = $this->get_soap_client();

		$key = "";

		foreach($params as $nom => $valeur) {
			$key .= $valeur;
		}

		$params['Security'] = strtoupper(md5($key.Variable::lire(self::NOM_VAR_CLE_PRIVEE)));

		$result = $client->call(
				$ws_name,
				$params,
				'http://www.mondialrelay.fr/webservice/',
				'http://www.mondialrelay.fr/webservice/'.$ws_name
		);

		if ($client->fault) {
			$this->last_error = "SOAP Fault: ".print_r($result,1);
		}
		else {
			$err = $client->getError();

			if ($err) {
				$this->last_error = $err;
			}
			else {
				return $result;
			}
		}

		return false;
	}

	public function last_error() {
		return $this->last_error;
	}

	public function get_error_label($code) {
		if (! isset($this->statCode[$code])) $code = 99;

		return $this->statCode[$code];
	}
}
?>
