<?php

	include_once(dirname(__FILE__) . "/../../../fonctions/authplugins.php");
	autorisation("mondialrelay");

	include_once(dirname(__FILE__) . "/../../../fonctions/divers.php");

	include_once(dirname(__FILE__) . "/../../../classes/Venteprod.class.php");
	include_once(dirname(__FILE__) . "/../../../classes/Produit.class.php");
	include_once(dirname(__FILE__) . "/../../../classes/Commande.class.php");

	include_once(dirname(__FILE__) . "/Mondialrelay.class.php");

	function sanitize_phone($phone) {

		$phone = preg_replace('/[^0-9\+]/', '', $phone);

		if (strlen($phone) > 0) {

			if ($phone[0] != '+') {

				if ($phone[0] == '0') $phone = substr($phone, 1);

				$phone = "+33".$phone;
			}
		}

		return $phone;
	}

	$id_mr = intval($_REQUEST['id']);

	$mr = new Mondialrelay();

	if ($mr->charger_id($id_mr)) {

		$commande = new Commande();

		if ($commande->charger($mr->commande)) {

			// COMMANDE
			$enseigne = Variable::lire(Mondialrelay::NOM_VAR_CODE_ENSEIGNE);
			$modeCol = "CCC";
			$modeLiv= "24R";
			$nDossier= $mr->commande;
			$nClient = $mr->client;

			// EXPEDITEUR
			$expeLangage = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_LANGUE));
			$expeAd1 = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_AD_1));
			$expeAd2 = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_AD_2));
			$expeAd3 = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_AD_3));
			$expeAd4 = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_AD_4));
			$expeVille = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_VILLE));
			$expeCP   = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_CP));
			$expePays = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_PAYS));
			$expeTel1 = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_TEL_1));
			$expeTel2 = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_TEL_2));
			$expeMail = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_MAIL));


				// DESTINATAIRE
			$destLangage = Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_LANGUE);
			$destAd1 = supprAccent($mr->nom);
			$destAd2 = supprAccent($mr->adresse1);
			$destAd3 = supprAccent($mr->adresse2);
			$destAd4 = supprAccent($mr->adresse3);
			$destVille = supprAccent($mr->ville);
			$destCP   = supprAccent($mr->cpostal);
			$destPays = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_PAYS));
			$destTel1 = sanitize_phone($mr->tel);
			$destTel2 = "";
			$destMail = supprAccent($mr->email);

			// VERIFIER LE POIDS (grammes)
			$query = "select sum(p.poids * vp.quantite) as poids from ".Venteprod::TABLE." vp left join ".Produit::TABLE." p on p.ref = vp.ref where commande = $commande->id";

			$hdl = $mr->query($query);

			$unite = Variable::lire(Mondialrelay::NOM_VAR_UNITE_DE_POIDS);

			$poids = $mr->get_result($hdl);

			if ($unite == 'kg')
				$poids = intval(1000 * $poids);

			$longueur = "";
			$taille = "";
			$nbColis = "1";
			$crtValeur = 0 ;
			$crtDevise = "";
			$expValeur = "";
			$expDevise = "";
			$colRelPays = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_PAYS));
			$colRel = $mr->point;
			$livRelPays = supprAccent(Variable::lire(Mondialrelay::NOM_VAR_EXPEDITEUR_PAYS));
			$livRel = $mr->point;
			$tAvisage = "";
			$tReprise = "";
			$montage = "";
			$trdv = "";
			$assurance = "";
			$instructions = "";

			// PARAM  WEB-SERVICE (NUSOAP)
			$params = array(
				'Enseigne' => $enseigne,
				'ModeCol' => $modeCol,
				'ModeLiv' => $modeLiv,
				'NDossier' => $nDossier,
				'NClient' => $nClient,
				'Expe_Langage' => $expeLangage,
				'Expe_Ad1' => $expeAd1,
				'Expe_Ad2' => $expeAd2,
				'Expe_Ad3' => $expeAd3,
				'Expe_Ad4' => $expeAd4,
				'Expe_Ville' => $expeVille,
				'Expe_CP' => $expeCP,
				'Expe_Pays' => $expePays,
				'Expe_Tel1' => sanitize_phone($expeTel1),
				'Expe_Tel2' => sanitize_phone($expeTel2),
				'Expe_Mail' => $expeMail,
				'Dest_Langage' => $destLangage,
				'Dest_Ad1' => $destAd1,
				'Dest_Ad2' => $destAd2,
				'Dest_Ad3' => $destAd3,
				'Dest_Ad4' => $destAd4,
				'Dest_Ville' => $destVille,
				'Dest_CP' => $destCP,
				'Dest_Pays' => $destPays,
				'Dest_Tel1' => $destTel1,
				'Dest_Tel2' => $destTel2,
				'Dest_Mail' => $destMail,
				'Poids' => $poids,
				'Longueur' => $longueur,
				'Taille' => $taille,
				'NbColis' => $nbColis,
				'CRT_Valeur' => $crtValeur,
				'CRT_Devise' => $crtDevise,
				'Exp_Valeur' => $expValeur,
				'Exp_Devise' => $expDevise,
				'COL_Rel_Pays' => $colRelPays,
				'COL_Rel' => $colRel,
				'LIV_Rel_Pays' => $livRelPays,
				'LIV_Rel' => $livRel,
				'TAvisage' => $tAvisage,
				'TReprise' => $tReprise,
				'Montage' => $montage,
				'TRDV' => $trdv,
				'Assurance' => $assurance,
				'Instructions' => $instructions
			);

			//echo "<pre>";
			// print_r($params);

			$success = false;

			$result = $mr->call('WSI2_CreationExpedition', $params);

			if ($result && isset($result['WSI2_CreationExpeditionResult']['ExpeditionNum'])) {

				$mr->expedition = $result['WSI2_CreationExpeditionResult']['ExpeditionNum'];

				if (! empty($mr->expedition)) {

					$mr->maj();

					// Mettre a jour le numéro de colis de la commande
					$commande->colis = $mr->expedition;

					$commande->maj();

					$success = true;
				}
			}

			if (! $success) {

				if (isset($result['WSI2_CreationExpeditionResult']['STAT'])) {
					$err = $mr->get_error_label($result['WSI2_CreationExpeditionResult']['STAT']);
				}
				else {
					$err = $mr->last_error();
				}
				?>
				<p>Echec de la demande d'expedition: <?php echo $err ?></p>
				<p>Merci de vérifier la validité des données d'expediteur B.O -> Modules -> Mondial Relay.</p>
				<p><a href="<?php echo $_REQUEST['redir'] ?>">Continuer...</a></p>
				<p>&nbsp;</p>
				<p>Paramètres transmis au web Service Mondail Relay:</p>
				<?php


				echo "<pre>".print_r($params,1)."</pre>";

				exit();
			}

		}
	}

	if (! empty($_REQUEST['redir'])) redirige($_REQUEST['redir']);
?>