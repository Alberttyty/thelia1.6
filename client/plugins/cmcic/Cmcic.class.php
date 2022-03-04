<?php
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsPaiements.class.php");

class Cmcic extends PluginsPaiements
{
		public $defalqcmd = 0;

		/***************
		 * ECM VOYAGES *
		 ***************/
		private $valeurs = [
	      "nom"  		=> '',
	      "prenom"  	=> '',
	      "entreprise"=> '',
	      "ad_email"  => '',
	      "numero_devis" => '',
	      "montant"  	=> ''
	  ];

		/******************
		 * MODULE METHODS *
		 ******************/
		public function Cmcic()
		{
				$this->PluginsPaiements("cmcic");
		}

		public function init()
		{
				$this->ajout_desc('Carte Bancaire', 'Carte Bancaire', 'Paiement crypté et sécurisé par carte bancaire.<br/><img src="/client/plugins/cmcic/logo_cm-paiement-petit.jpg" alt="Crédit Mutuel - Master Card - Visa" class="logo_paiement" />', 1);
		}

		public function paiement($commande)
		{
				if ($commande->transport != null) header("Location: " . "client/plugins/cmcic/paiement.php");
				else header("Location: " . urlfond("adresse"));
		}

		public function mail($commande = null)
		{
	  }

		public function confirmation($commande)
		{
				$module = new Modules();
				$module->charger_id($commande->paiement);

				if ($module->nom==$this->getNom()) {
						if ($commande->statut == 2){
								parent::mail($commande);
								//mail('thierry@pixel-plurimedia.fr', 'Test CMCIC', $commande->ref);
								//modules_fonction("statut", $commande);
						}
				}
		}

		/**********************
		 * ECM-VOYAGES - DONS *
		 **********************/
		public function action()
		{
				if (isset($_POST['action']) && $_POST['action'] == "formulairecmcic") {
						//echo('<pre>');print_r($_SESSION['navig']);echo('</pre>');exit();

						if (!function_exists('dsp_crypt')) {
	        			$cryptinstall="lib/crypt/cryptographp.fct.php";
	        			require_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
	      		}

	      		if (chk_crypt($_POST['txt_securite'])) {
			        	$erreur=false;

			        	if ((!filter_var($_POST['ad_email'], FILTER_VALIDATE_EMAIL)) || $_POST['ad_email'] == "") {
			          		$_POST['formulairecmcic_erreur_email']=true;
			          		$erreur=true;
			        	}

			        	if ($_POST['nom'] == "" || $_POST['numero_devis'] == "") {
			          		$_POST['formulairecmcic_erreur_champs']=true;
			          		$erreur=true;
			        	}

								$_POST['montant'] = str_replace(",",".",$_POST['montant']);
	        			$_POST['montant'] = floatval($_POST['montant']);

								if ($_POST['montant'] == "" || $_POST['montant'] == 0) {
				         		$_POST['formulairecmcic_erreur_montant']=true;
				         		$erreur=true;
				        }

				        if (! $erreur) {
									 	require_once("config_oldki5945.php");
									 	require_once("CMCIC_Tpe.inc.php");
						       	session_start();

										// Currency : ISO 4217 compliant
										$sDevise  = "EUR";
										$sMontant = (string)round($_POST['montant'], 2).$sDevise;

										// between 2 and 4
										//$sNbrEch = "4";
										$sNbrEch = "";

										// date echeance 1 - format dd/mm/yyyy
										//$sDateEcheance1 = date("d/m/Y");
										$sDateEcheance1 = "";

										// montant échéance 1 - format  "xxxxx.yy" (no spaces)
										//$sMontantEcheance1 = "0.26" . $sDevise;
										$sMontantEcheance1 = "";

										$sDateEcheance2 = "";
										$sMontantEcheance2 = "";

										$sDateEcheance3 = "";
										$sMontantEcheance3 = "";

										$sDateEcheance4 = "";
										$sMontantEcheance4 = "";

										// ----------------------------------------------------------------------------

										$oTpe = new CMCIC_Tpe('FR');
										$oHmac = new CMCIC_Hmac($oTpe);

										$clientNom = ereg_caracspec($_POST['nom']);
										$clientPrenom = ereg_caracspec($_POST['prenom']);
										$clientEntreprise = ereg_caracspec($_POST['entreprise']);
										//$clientAdresse1 = $_SESSION["navig"]->client->adresse1;
										//$clientAdresse2 = $_SESSION["navig"]->client->adresse2;
										//$clientAdresse3 = $_SESSION["navig"]->client->adresse3;
										//$clientVille = $_SESSION["navig"]->client->ville;
										//$clientCpostal = $_SESSION["navig"]->client->cpostal;
										//$pays = new Pays($_SESSION["navig"]->client->pays);

										$sContexte = [
												"billing" => [
														"name" => substr($clientPrenom." ".$clientNom." ".$clientEntreprise, 0, 45),
														"firstName" => substr($clientPrenom, 0, 45),
														"lastName" => substr($clientNom, 0, 45),
														//"addressLine1" => substr($clientAdresse1, 0, 50),
														//"city" => substr($clientVille, 0, 50),
														//"postalCode" => $clientCpostal,
														//"country" => $pays->isoalpha2,
												],
										];
										/*
										if (!empty($clientAdresse2)) {
												$sContexte['billing']['addressLine2'] = substr($clientAdresse2, 0, 50);
												$sContexte['shipping']['addressLine2'] = substr($clientAdresse2, 0, 50);
										}
										if (!empty($clientAdresse3)) {
												$sContexte['billing']['addressLine3'] = substr($clientAdresse3, 0, 50);
												$sContexte['shipping']['addressLine3'] = substr($clientAdresse3, 0, 50);
										}
										*/
										$sContexteJson = json_encode($sContexte);
										$sContexteUtf8 = utf8_encode($sContexteJson);
										$sContexte = base64_encode($sContexteUtf8);

										 // Data to certify
										$inputs = [
												'version' => $oTpe->sVersion,
												'TPE' => $oTpe->sNumero,
												'date' => date("d/m/Y:H:i:s"),
												'montant' => $sMontant,
												'reference' => $oHmac->harmonise(ereg_caracspec($_POST['numero_devis']), 'numeric', 12),
												'url_retour_ok' => $oTpe->sUrlOK,
												'url_retour_err' => $oTpe->sUrlKO,
												'lgue' => $oTpe->sLangue,
												'contexte_commande' => $sContexte,
												'societe' => $oTpe->sCodeSociete,
												"texte-libre" => ereg_caracspec($_POST['numero_devis']),
												'mail' => $_POST['ad_email'],
												'3dsdebrayable' => "0",
												'ThreeDSecureChallenge' => "challenge_preferred",
												// Uniquement pour le Paiement fractionné
												//'nbrech' => $sNbrEch,
												//'dateech1' => $sDateEcheance1,
												//'montantech1' => $sMontantEcheance1,
												//'dateech2' => $sDateEcheance2,
												//'montantech2' => $sMontantEcheance2,
												//'dateech3' => $sDateEcheance3,
												//'montantech3' => $sMontantEcheance3,
												//'dateech4' => $sDateEcheance4,
												//'montantech4' => $sMontantEcheance4,
										];
										// MAC computation
										$inputs["MAC"] = $oHmac->computeHmac(CMCIC_Hmac::getHashable($inputs));

									  echo('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
										  	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
										  	<head>
												  	<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
												  	<meta http-equiv="cache-control" content="no-store, no-cache, must-revalidate, post-check=0, pre-check=0" />
												  	<meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT" />
												  	<meta http-equiv="pragma" content="no-cache" />
												  	<title>Connexion au serveur de paiement</title>
												  	<link type="text/css" rel="stylesheet" href="/client/plugins/cmcic/CMCIC.css" />
										  	</head>
										  	<body>
												  	<img src="/client/plugins/cmcic/logo_cm-paiement-grd.jpg" alt="" />
												  	<h1>Connexion au serveur de paiement / <span class="anglais">Connection to the payment server</span></h1>
												  	<div id="frm">
														  	<p>Cliquez sur le bouton ci-dessous pour vous connecter au serveur de paiement.<br /><span class="anglais">Click on the following button to be redirected to the payment server.</span></p>
														  	<form action="'.$oTpe->sUrlPaiement.'" method="post" id="PaymentRequest">
																  	<p>');
																		foreach ($inputs as $name => $value) {
																				echo('<input name="'.$name.'" type="hidden" value="'.$value.'" />');
																		}
																		echo('<input name="bouton" type="hidden" value="Connexion / Connection" />');
																		echo('
																  	</p>
														  	</form>
														  	<script>
														  			document.forms["PaymentRequest"].submit();
														  	</script>
												  	</div>
										  	</body>
										  	</html>
									  ');
									  exit();
								}
						}
						else $_POST['formulairecmcic_erreur_code']=true;
	    	}
	  }

  	public function post()
		{
      	global $res;

      	if (isset($_REQUEST['retour_paiement'])) {

						if ($_REQUEST['retour_paiement']=="ok") {
	        			$res = preg_replace("`\#FORMULAIRECMCIC_OK\[([^]]*)\]`", "\\1", $res);
	        	}

	        	if ($_REQUEST['retour_paiement']=="ko") {
	        			$res = preg_replace("`\#FORMULAIRECMCIC_KO\[([^]]*)\]`", "\\1", $res);
	        	}
      	}

      	$res = preg_replace("`\#FORMULAIRECMCIC_OK\[([^]]*)\]`", "", $res);
      	$res = preg_replace("`\#FORMULAIRECMCIC_KO\[([^]]*)\]`", "", $res);

      	if(!function_exists('dsp_crypt')) {
	      		$cryptinstall="lib/crypt/cryptographp.fct.php";
	      		require_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
      	}
      	$res = str_replace("#FORMULAIRECMCIC_ANTISPAM", dsp_crypt(0,1,0), $res);

      	if (isset($_POST['formulairecmcic_erreur_code'])) {
  					$res = preg_replace("`\#FORMULAIRECMCIC_ERREUR_CODE\[([^]]*)\]`", "\\1", $res);
      	}
				else {
      			$res = preg_replace("`\#FORMULAIRECMCIC_ERREUR_CODE\[([^]]*)\]`", "", $res);
      	}

      	if (isset($_POST['formulairecmcic_erreur_email'])) {
  					$res = preg_replace("`\#FORMULAIRECMCIC_ERREUR_EMAIL\[([^]]*)\]`", "\\1", $res);
      	}
				else {
      			$res = preg_replace("`\#FORMULAIRECMCIC_ERREUR_EMAIL\[([^]]*)\]`", "", $res);
      	}

      	if (isset($_POST['formulairecmcic_erreur_champs'])) {
  					$res = preg_replace("`\#FORMULAIRECMCIC_ERREUR_CHAMPS\[([^]]*)\]`", "\\1", $res);
      	}
				else {
      			$res = preg_replace("`\#FORMULAIRECMCIC_ERREUR_CHAMPS\[([^]]*)\]`", "", $res);
      	}

      	if (isset($_POST['formulairecmcic_erreur_montant'])) {
  					$res = preg_replace("`\#FORMULAIRECMCIC_ERREUR_MONTANT\[([^]]*)\]`", "\\1", $res);
      	}
				else {
      			$res = preg_replace("`\#FORMULAIRECMCIC_ERREUR_MONTANT\[([^]]*)\]`", "", $res);
      	}

      	foreach($this->valeurs as $key => $value) {
	        	if(isset($_POST[$key])) $mavaleur = $_POST[$key];
	        	else $mavaleur = "";
	        	$res = str_replace("#FORMULAIRECMCIC_".strtoupper($key),$mavaleur, $res);
      	}

  	}

  	private function substitutions($texte, $donnees)
		{
				$texte = str_replace("__NOM__", $donnees['nom'], $texte);
				$texte = str_replace("__PRENOM__", $donnees['prenom'], $texte);
				$texte = str_replace("__ENTREPRISE__", $donnees['entreprise'], $texte);
				$texte = str_replace("__NUMERO_DEVIS__", $donnees['numero_devis'], $texte);
	    	$texte = str_replace("__AD_EMAIL__", $donnees['ad_email'], $texte);
	    	$texte = str_replace("__MONTANT__", $donnees['montant'], $texte);

				return $texte;
		}

  	public function enregistrement($donnees)
		{
				$cmcic=new Cmcic();
				$cmcic->nom=$donnees['nom'];
				$cmcic->prenom=$donnees['prenom'];
				$cmcic->entreprise=$donnees['entreprise'];
				$cmcic->numero_devis=$donnees['numero_devis'];
				$cmcic->ad_email=$donnees['ad_email'];
				$cmcic->montant=$donnees['montant'];
				$cmcic->date=date('Y-m-d H:i:s');
				$cmcic->add();
    }

  	public function notification($donnees)
		{
				$test = new Message();

				if(! $test->charger("cmcicadmin")) {
						$message = new Message();
						$message->nom = "cmcicadmin";
						$lastid = $message->add();

						$messagedesc = new Messagedesc();
						$messagedesc->message = $lastid;
						$messagedesc->lang = 1;
						$messagedesc->titre = "CMCIC Admin";
						$messagedesc->description = "Paiement enregistré";
						$messagedesc->add();
				}

				$message = new Message("cmcic");
				$messagedesc = new Messagedesc($message->id, $commande->lang);
				$sujet = $this->substitutions($messagedesc->titre, $donnees);
				$texte = $this->substitutions($messagedesc->descriptiontext, $donnees);
				$html  = $this->substitutions($messagedesc->description, $donnees);
				Mail::envoyer($donnees['prenom']." ".$donnees['nom'], $donnees['ad_email'],Variable::lire('nomsite'),Variable::lire('emailcontact'),$sujet,$html,$texte);
				$message = new Message("cmcicadmin");
				$messagedesc = new Messagedesc($message->id, $commande->lang);
				$sujet = $this->substitutions($messagedesc->titre, $donnees);
				$texte = $this->substitutions($messagedesc->descriptiontext, $donnees);
				$html  = $this->substitutions($messagedesc->description, $donnees);
				Mail::envoyer(Variable::lire('nomsite'),Variable::lire('emailcontact'),$donnees['prenom']." ".$donnees['nom'], $donnees['ad_email'],$sujet,$html,$texte);
  	}
}
?>
