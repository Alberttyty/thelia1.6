<?php
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/SaventeEchange.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/SaventeRetour.class.php");

class Savente extends PluginsClassiques
{
	const OK 	= 1; // Validé
	const EC 	= 3; // En Cours
	const END	= 4; // FINI
	const KO 	= 5; // Echec
	const DAY_LIMIT = 60; // durée limite de la demande

	public $id;
	public $cmd;
	public $commande_old;
	public $commande_new;
	public $delta; // Si < 0 => montant en faveur de la boutique
	public $statut;
	public $montant_retour;
	public $montant_echange;
	public $date_sav;

	public $table = 'commande_sav';
	public $bddvars = array('id', 'cmd', 'commande_old', 'commande_new', 'delta', 'statut', 'montant_retour', 'montant_echange', 'date_sav');

	public function Savente()
	{
			$this->Baseobj();
	}

	public function charger($id=null, $var2 = null)
	{
			if ($id != null) return $this->getVars('SELECT * FROM '.$this->table.' WHERE id = '.$id);
	}

	public function charger_old($id=1)
	{
			return $this->getVars('SELECT * FROM '.$this->table.' WHERE commande_old = '.$id);
	}

	public function charger_cmd($id=1)
	{
			return $this->getVars('SELECT * FROM '.$this->table.' WHERE cmd = '.$id);
	}

	/**
	 * ACTIVATION DU PLUGIN
	 */
	public function init()
	{
			/**
			 * Table sauvegardant les historiques d'échange (lien entre la commande d'origine et la version modifiée)
			 * commande_old 	=> commande en cours de SAV (sav_statut = 3)
			 * commande_new 	=> commande échangée si SAV ACCEPTE (sav_statut = 1)
			 * sav_statut = 5	=> annulé, rien ne change
			 */
			$query_savente = "CREATE TABLE IF NOT EXISTS `$this->table` (
			  	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  	`cmd` INT(11) UNSIGNER NOT NULL,
			  	`commande_old` 	INT(11) UNSIGNED NOT NULL,
			   	`commande_new` 	INT(11) UNSIGNED NOT NULL,
					`delta` 	FLOAT(11) DEFAULT NULL,
					`montant_retour` 	VARCHAR(11),
					`montant_echange` 	VARCHAR(11),
					`statut` 	INT(11) UNSIGNED NOT NULL,
					`date_sav` 	DATETIME NOT NULL
			);"
			;
			$resul_savente = mysql_query($query_savente, $cnx->link);

			/**/
			$SaventeRetour = new SaventeRetour();
			$SaventeRetour->init();

			/**/
			$SaventeEchange = new SaventeEchange();
			$SaventeEchange->init();

			/**
			 * Création des Mails de confirmation
			 */
			$test = new Message();
			// Mail Admin de demande SAV
			if (!$test->charger("sav_ec_admin")) {
					$message = new Message();
					$message->nom = "sav_ec_admin";
					$lastid = $message->add();

					$messagedesc = new Messagedesc();
					$messagedesc->message = $lastid;
					$messagedesc->lang = 1;
					$messagedesc->intitule = "S.A.V Confirmation Admin";
					$messagedesc->titre = "Nouvelle demande de S.A.V";
					$messagedesc->description = '<div class="contenu">
													<h2>Une nouvelle demande de SAV a été déposée</h2>
													<h3>pour la commande __COMMANDE__</h3>
													<p>Retrouvez-la dans le menu Commandes de votre d\'espace d\'aministration.</p>
													<p id="liencompte"><a href="__URLSITE__/gestion">__URLSITE__/gestion</a></p>
													<p>Cordialement.</p>
												</div>';
					$messagedesc->descriptiontext = "Une nouvelle demande de SAV a été déposée pour la commande __COMMANDE__.\nRetrouvez-la dans le menu Commandes de votre d'espace d'aministration.\nCordialement.";
					$messagedesc->add();
			}
			// Mail Client de demande SAV
			if(!$test->charger("sav_ec")) {
					$message = new Message();
					$message->nom = "sav_ec";
					$lastid = $message->add();

					$messagedesc = new Messagedesc();
					$messagedesc->message = $lastid;
					$messagedesc->lang = 1;
					$messagedesc->intitule = "S.A.V Confirmation Client";
					$messagedesc->titre = "__NOMSITE__ : Votre demande de retour";
					$messagedesc->description = '<div class="contenu">
													<h2>Confirmation de votre demande de retour</h2>
													<p>Nous accusons bonne réception de votre demande de prise en charge par notre
														Service Après Vente de la commande __COMMANDE__.</p>
													<p>Vous recevrez un second e-mail vous informant du statut de votre demande ainsi que
														des modalités de retour en cas d\'acceptation.</p>
													<h2>Récapitulatif de votre demande :</h2>
													__RETOURPRODUITS__
													__ECHANGEPRODUITS__
													<h3>3. Différence des montants :</h3> __DIFFERENCE__
													<p><strong>Important : </strong>
														gardez ce mail, vous en aurez besoin en cas d\'acceptation de votre demande par nos services.</p>
													<p>A bientôt sur __NOMSITE__.</p>
												</div>';
					$messagedesc->descriptiontext = "Nous accusons bonne réception de votre demande de prise en charge par notre Service Après Vente de la commande __COMMANDE__.\nVous recevrez un second e-mail vous informant du statut de votre demande ainsi que des modalités de retour en cas d'acceptation.\n\nRécapitulatif de votre demande :\n\n1.\n\n__RETOURPRODUITS__\n\n__ECHANGEPRODUITS__\n\n3. Différence des montants : __DIFFERENCE__\n\nImportant : gardez ce mail, vous en aurez besoin en cas d'acceptation de votre demande par nos services.\n\nA bientôt sur __NOMSITE__.";
					$messagedesc->add();
			}
			// Mail de confirmation de prise en charge
			if(!$test->charger("sav_ok")) {
					$message = new Message();
					$message->nom = "sav_ok";
					$lastid = $message->add();

					$messagedesc = new Messagedesc();
					$messagedesc->message = $lastid;
					$messagedesc->lang = 1;
					$messagedesc->intitule = "S.A.V Mail Acceptation";
					$messagedesc->titre = "__NOMSITE__ : Acceptation retour gratuit";
					$messagedesc->description = '
					<div class="contenu">
							<p>Bonjour,<br/>
							Suite à l\'acceptation de votre demande, veuillez trouver ci-après la procédure de retour de vos produits :</p>
							<h2>Merci de suivre attentivement les points suivants :</h2>
							<ol>
								<li>Les produits doivent être dans un emballage adaptée à la taille du produit.
										Nous vous demandons de prendre toutes les mesures nécessaires afin de les protéger pendant le transport.</li>
								<li>Veuillez ne pas scotcher directement la boite d’origine du produit, celui ci pouvant être remis en vente.
										Des frais de reconditionnement pourraient être réclamés (ou déduits dans le cas d’un remboursement)
										si cela n’est pas respecté.</li>
								<li>A l\'intérieur du colis, joindre le mail imprimé de prise en charge de votre demande,
										ainsi que le chèque correspondant aux éventuels frais de retour et / ou d\'échanges.</li>
								<li>Imprimer votre étiquette de retour via Mondial Relay que vous retrouverez en pièce jointe de ce mail.
										Valable uniquement pour les commandes en France métropolitaine.</li>
								<li>Collez l’étiquette Mondial Relay imprimée sur votre colis et déposez-le dans le point
										Mondial Relay de votre choix.</li>
							</ol>
							<p>Pour connaitre les relais les plus proches de chez vous, vous pouvez consulter le site internet
								<a href="https://www.mondialrelay.fr">mondialrelay.fr</a></p>
							<p><strong>Remarque :</strong> le ou les produits retournés doivent être neufs et dans leurs emballages d’origine.
								Ils doivent être réceptionnés par nos services dans un délai maximum de 14 jours
								après l\'acceptation de votre demande de retour. Passé ce délai, nous ne pourrons accepter les demandes d’échange
								ou de remboursement.</p>
							<h3>Important</h3>
							<p>Si le montant du/des produit(s) que vous souhaitez recevoir en échange est supérieur au montant du/des produit(s)
								de départ, merci de joindre un chèque équivalent à la différence. Le produit ne sera renvoyé qu’après vérification
								du montant.</p>
							<p><strong>Attention :</strong> s’il s’avère que l’une des conditions de retour n’est pas respectée
								(produit utilisé par exemple), __NOMSITE__ ne procédera pas à l’échange et sera en droit de réclamer le paiement
								des frais de renvoi.</p>
								<p>__FRAIS_RETOUR__</p>
								<p>__FRAIS_ECHANGE__</p>
								<p>__RAPPEL_DELTA__</p>
							<p>A bientôt sur <a href="__URLSITE__">__NOMSITE__</a>.</p>
					</div>';
					$messagedesc->descriptiontext = "Bonjour,\n\nSuite à l'acceptation de votre demande, veuillez trouver ci-après la procédure de retour de vos produits.\n\nMerci de suivre attentivement les points suivants :\n\n1. Les produits doivent être dans un emballage adapteé à la taille du produit. Nous vous demandons de prendre toutes les mesures nécessaires afin de les protéger pendant le transport.\n2. Veuillez ne pas scotcher directement la boite d’origine du produit, celui ci pouvant être remis en vente. Des frais de reconditionnement pourraient être réclamés (ou déduits dans le cas d’un remboursement) si cela n’est pas respecté.\n3. Joindre au colis (à l'intérieur) le mail vous informant de la prise en charge de votre demande.\n4. Imprimer votre étiquette de retour gratuit que vous retrouverez en pièce jointe de ce mail. Valable uniquement pour les commandes en France métropolitaine.\n5. Collez l’étiquette Mondial Relay imprimée sur votre colis et déposez-le dans le point Mondial Relay de votre choix.\n\nPour connaitre les relais les plus proches de chez vous, vous pouvez consulter la carte interactive de Mondial Relay.\n\nRemarque : le ou les produits retournés doivent être neufs et dans leurs emballages d’origine. Ils doivent être réceptionnés par nos services dans un délai maximum de 14 jours après l'acceptation de votre demande de retour. Passé ce délai, nous ne pourrons accepter les demandes d’échange ou de remboursement.\n\nImportant : Si le montant du/des produit(s) que vous souhaitez recevoir en échange est supérieur au montant du/des produit(s) de départ, merci de joindre un chèque équivalent à la différence. Le produit ne sera renvoyé qu’après vérification du montant.\n\nAttention : s’il s’avère que l’une des conditions de retour n’est pas respectée (produit utilisé par exemple), __NOMSITE__ ne procédera pas à l’échange et sera en droit de réclamer le paiement des frais de renvoi.\n\n__FRAIS_RETOUR__\n__FRAIS_ECHANGE__\n__RAPPEL_DELTA__\n\nA bientôt sur __NOMSITE__.";
					$messagedesc->add();
			}
			// Mail de refus de prise en charge
			if(!$test->charger("sav_ko")) {
					$message = new Message();
					$message->nom = "sav_ko";
					$lastid = $message->add();

					$messagedesc = new Messagedesc();
					$messagedesc->message = $lastid;
					$messagedesc->lang = 1;
					$messagedesc->intitule = "S.A.V Mail Refus";
					$messagedesc->titre = "__NOMSITE__ : Refus de votre demande SAV";
					$messagedesc->description = '<div class="contenu">
													<p>Nous avons le regret de vous informer que votre demande de retour gratuit pour la commande __COMMANDE__
														a été refusé par notre Service Après Vente.</p>
													<p>Nous restons bien sûr à votre entière disposition pour toutes questions relatives à votre demande.</p>
													<p>A bientôt sur __NOMSITE__.</p>
												</div>';
					$messagedesc->descriptiontext = "Nous avons le regret de vous informer que votre demande de retour gratuit pour la commande __COMMANDE__ a été refusé par notre Service Après Vente.\n\nNous restons bien sûr à votre entière disposition pour toutes questions relatives à votre demande.\nA bientôt sur __NOMSITE__.";
					$messagedesc->add();
			}
	}

	/**
	 * DESACTIVATION DU PLUGIN
	 */
	public function destroy() {}


	public function boucle($texte, $args)
	{
			// récupération des arguments
			$client		= lireTag($args, 'client');
			$commande 	= lireTag($args, 'commande');
			$date 		= lireTag($args, 'date');
			$statut 	= lireTag($args, 'statut');

			// On exclue les revendeurs
			if ($client == 1) return '';

			$search = '';

			// On exclue les commandes déjà en SAV
			if(!empty($commande)) {
					$search .= ' AND cmd = '.$commande;
					$Commande = new Commande($commande);
			}
			// On exclue les commandes de plus de X jours
			if (!empty($date)) $search .= ' AND (DATEDIFF(NOW(),"'.$date.'") < '.self::DAY_LIMIT.')';
			// Trie par statut
			if (!empty($statut)) $search .= ' AND statut = '.$statut;
			else $search .= ' AND statut != '.self::END;

			$query = 'SELECT * FROM '.$this->table.' WHERE commande_new = 0 AND commande_old = 0 '.$search;

			$resul = mysql_query($query, $this->link);
			$nb = mysql_numrows($resul);

			if (!$nb) return '';

			while($row = mysql_fetch_object($resul)) {
					$temp = str_replace('#ID', $row->id, $texte);
					$temp = str_replace('#STATUT', $row->statut, $temp);
					$temp = str_replace('#REF', $Commande->ref, $temp);
					$temp = str_replace('#DATELIVRAISON', $Commande->datelivraison, $temp);
					$temp = str_replace('#DATE', $Commande->datefact, $temp);
					$temp = str_replace('#TOTALARTICLESTTC', $Commande->total(), $temp);
					$temp = str_replace('#CMD', $Commande->id, $temp);
					$res .= $temp;
			}
			return $res;
	}

	/**/

	/**
	 * HOOK ACTION
	 */
	public function aprescommande($Commande)
	{
			if( !empty($Commande->id) ) {
					$this->cmd = $Commande->id;
					$this->add();
			}
	}

	/**
	 * CLASS ACTION
	 */
	public function action()
	{
			if ($_POST['action'] == 'demandesav' && !empty($_POST['commande']) && !empty($_POST['id_venteprod']) ) {
					/**
					 * Récupération de la commande en cours
					 */
					$Commande = new Commande();
					$Commande->charger_ref($_POST['commande']);

					$this->charger_cmd($Commande->id);
					$this->commande_old = $Commande->id;
					$this->statut = self::EC; //3 - En Cours d'étude
					$this->date_sav = date('Y-m-d H:i:s');
					$this->maj();

					/**
					 * Récupération du tableau des ID venteprod à retourner
					 */
					if(!empty($_POST['id_venteprod'])) {
							foreach($_POST['id_venteprod'] AS $id_venteprod) {
									// On ajoute à la BD en évitant les doublons
									$SaventeRetour = new SaventeRetour();
									if(!$SaventeRetour->charger_venteprod($id_venteprod)) {
											$SaventeRetour->sav = $this->id;
											$SaventeRetour->venteprod = $id_venteprod;
											$SaventeRetour->add();
									}
							}
					}

					/**
					 * Récupération des produits demandés en échange
					 * On range les produits, leurs déclinaisons et leurs caractéristiques éventuelles
					 * dans un tableau associatif.
					 */
					if(!empty($_POST['ref'][0][0])) {
							$tabProd = array();

							foreach($_POST['ref'] AS $cle => $ref) {
									$Produit = new Produit();
									$Produit->charger($ref);
									$tabProd[$cle]['ref'] = $Produit->id;
							}
							foreach($_POST['declidisp'] AS $cle => $declidisp) {
									$tabProd[$cle]['declidisp'] = $declidisp;
							}
							foreach($_POST['taille'] AS $cle => $taille) {
									$tabProd[$cle]['taille'] = $taille;
							}
							foreach($_POST['couleur'] AS $cle => $couleur) {
									$tabProd[$cle]['couleur'] = $couleur;
							}
							foreach($_POST['carac'] AS $cle => $carac) {
									$tabProd[$cle]['carac'] = $carac;
							}
							foreach($_POST['quantite'] AS $cle => $quantite) {
									if(empty($quantite)) $quantite = 1;
									$tabProd[$cle]['qte'] = $quantite;
							}
					}
					/**
					 * Enregistrement des échanges demandés en BDD
					 */
					if(isset($tabProd)) {
							foreach($tabProd AS $prod) {
									$SaventeEchange = new SaventeEchange();
									// Hydratation
									foreach($prod AS $carac => $valeur) {
											$SaventeEchange->sav = $this->id;
											$SaventeEchange->$carac = $valeur;
									}
									$SaventeEchange->add();
							}
					}

					/**
					 * MAIL CLIENT de confirmation de la demande
					 */
					$pj = array();
					$this->sav_mail('sav_ec',$Commande,$pj,$this);

					/**
					 * MAIL ADMIN de confirmation de la demande
					 */
					$this->sav_mail('sav_ec_admin',$Commande);
			}
	}

    public function setStatutAndSave($statut)
		{
        if($statut == self::KO) $this->annuler();
        else $this->updateStatut($statut);
    }


	/**
	 * PRIVATE STATUS METHODS
	 */
	private function annuler()
	{
    	if ($this->statut != self::KO) {
          $ancienStatut = $this->statut;
          $this->statut = self::KO;

					$Commande = new Commande();
					$Commande->charger_ref($_POST['ref']);

					$this->sav_mail('sav_ko',$Commande);
          $this->maj();
      }
	}

	private function updateStatut($statut)
	{
      $ancienStatut = $this->statut;
      $this->statut = $statut;
			$Commande = new Commande();
			$Commande->charger($this->commande_old);

      if ($statut == self::OK) {
					/**
					 * GESTION PIECE JOINTE (Bon de retour PDF)
					 */
					$erreur = true;
					if(empty($_FILES['colis']['error'])) {
							if($_FILES['colis']['size'] < 2000000) {
									$this->montant_retour = $_POST['frais-retour'];
									$this->montant_echange = $_POST['frais-echange'];
									$this->maj();
									$extension = strtolower(substr(strrchr($_FILES['colis']['name'],'.'),1 ));
									if($extension == 'pdf' && $_FILES['colis']['type'] == 'application/pdf') {
											$colis = $_FILES['colis'];
											$this->sav_mail('sav_ok', $Commande, $colis, false, $_POST['frais-retour'], $_POST['frais-echange']);
											$erreur = false;
									}

							}
					}
					if($erreur) $this->statut = $ancienStatut;
		}
		else if ($statut == self::END && !$this->commande_new) {
			// ANNULATION COMMANDE_OLD
			$Commande->annuler();

			// CREATION COMMANDE_NEW
			$Commande_new = new Commande();
			$Client = new Client($Commande->client);
			/**
			 * On copie les éléments de la commande qui restent identiques
			 */

			$Commande_new->client 	= $Commande->client;
			$Commande_new->adrfact 	= $Commande->adrfact;
			$Commande_new->adrlivr 	= $Commande->adrlivr;
			$Commande_new->facture	= 0;
			$Commande_new->transport= $Commande->transport;
			$Commande_new->devise 	= $Commande->devise;
			$Commande_new->taux 	= $Commande->taux;
			$Commande_new->paiement = $Commande->paiement;
			$Commande_new->statut	= Commande::EXPEDIE;
			$Commande_new->lang 	= $Commande->lang;

			$idcmd = $Commande_new->add();
			$Commande_new->charger($idcmd);

			/**
			 * On copie les venteprod gardés par le client
			 */
			$tab_venteprod = array();
			$tab_venteprod = $Commande->getProduits();

			$Produit = new Produit();
			$Venteprod = new Venteprod();
			$SaventeRetour = new SaventeRetour();

			$total = 0;
			foreach ($tab_venteprod AS $prod) {
				if(!$SaventeRetour->charger_venteprod($prod->id)) {
					/*
					if($Produit->charger($prod->ref)) {
						$Produit->stock -= $prod->quantite;
						$Produit->maj();
					}
					*/
					$Venteprod->ref		= $prod->ref;
					$Venteprod->titre	= $prod->titre;
					$Venteprod->quantite= $prod->quantite;
					$Venteprod->tva		= $prod->tva;
					$Venteprod->prixu	= $prod->prixu;
					$Venteprod->commande= $idcmd;
					$Venteprod->add();

					$total += $Venteprod->prixu * $Venteprod->quantite;
				}
			}

			/**
			 * On y ajoute les produits demandés en échanges
			 */
			$Declidisp = new Declidisp();
			$Declidispdesc = new Declidispdesc();
			$Declinaisondesc = new Declinaisondesc();

			$Produitdesc = new Produitdesc();

			//$Stock = new Stock();
			$Ventedeclidisp = new Ventedeclidisp();

			$SaventeEchange = new SaventeEchange();
			$tab_echanges = $SaventeEchange->lister_echanges($this->id);
			foreach ($tab_echanges AS $prod) {

				$Produit->charger_id($prod->ref);
				$Produitdesc->charger($Produit->id);
				// GESTION DECLINAISONS
				$Declidisp->charger($prod->declidisp);
				$Declidispdesc->charger($prod->declidisp);
				$Declinaisondesc->charger($Declidisp->declinaison);
				/*
				if($Stock->charger($prod->declidisp,$Produit->id)) {
					$Stock->valeur -= $prod->quantite;
				}
				if($Produit->charger_id($Produit->id)) {
					$Produit->stock -= $prod->quantite;
					$Produit->maj();
				}
				*/
				$Venteprod->ref		= $Produit->ref;
				$Venteprod->titre	= $Produitdesc->titre.'<br/> - '.$Declinaisondesc->titre.' : '.$Declidispdesc->titre;
				$Venteprod->quantite= $prod->qte;
				$Venteprod->tva		= $Produit->tva;
				$Venteprod->prixu	= $Produit->prix;
				$Venteprod->commande= $idcmd;

				$idvp = $Venteprod->add();
				$Ventedeclidisp->venteprod = $idvp;
				$Ventedeclidisp->declidisp = $prod->declidisp;
				$Ventedeclidisp->add();

				$total += $Venteprod->prixu * $Venteprod->quantite;
			}

			// On termine la commande
			$Commande_new->remise = 0;
			if($Client->pourcentage>0) $Commande_new->remise = $total * $Client->pourcentage / 100;
			if($remise != '') $Commande_new->remise += $remise;

			$Commande_new->transaction = genid($Commande_new->id, 6);
			$Commande_new->port = $Commande->port;
			$Commande_new->maj();

			ActionsModules::instance()->appel_module("aprescommande", $commande);

			$this->commande_new = $idcmd;
			$this->maj();
			redirige('commande.php');
		}

        $this->maj();
    }


	/**
	 * PRIVATE MAIL FUNCTIONS
	 */
	private function sav_mail($nom = '', $Commande, $attach = array(), $Savente = false, $fraisRetour = 0, $fraisEchange = 0) {
		if(!empty($nom) && !empty($Commande)) {
			$Message = new Message($nom);
			$Messagedesc = new Messagedesc($Message->id, $Commande->lang);
			$Client = new Client($Commande->client);

			$sujet = $this->substitutions($Messagedesc->titre, $Client, $Commande, $Savente);
			$texte = $this->substitutions($Messagedesc->descriptiontext, $Client, $Commande, $Savente, $fraisRetour, $fraisEchange);
			$html  = $this->substitutions($Messagedesc->description, $Client, $Commande, $Savente, $fraisRetour, $fraisEchange);

			//Formatage texte
			$texte = str_replace('<h3>','',$texte);
			$texte = str_replace('</h3>','',$texte);

			//envoi du mail
			if($nom != 'sav_ec_admin') {
					Mail::envoyer($Client->prenom.' '.$Client->nom, $Client->email, Variable::lire('nomsite'), Variable::lire('emailcontact'), $sujet, $html, $texte, $attach);
			} else {
					Mail::envoyer(Variable::lire('nomsite'), Variable::lire('emailcontact'),Variable::lire('nomsite'),Variable::lire('emailcontact'),$sujet,$html,$texte);
			}
		}
	}

	private function substitutions($texte, $Client, $Commande, $Savente = false, $fraisRetour = 0, $fraisEchange = 0) {

		$datecommande = strtotime($Commande->date);

		$raisondesc = new Raisondesc();
		$raisondesc->charger($Client->raison, $Commande->lang);

		$texte = str_replace("__RAISON__", $raisondesc->long, $texte);
		$texte = str_replace("__NOM__", $Client->nom, $texte);
		$texte = str_replace("__PRENOM__", $Client->prenom, $texte);

		$texte = str_replace("__URLSITE__", Variable::lire('urlsite'), $texte);
		$texte = str_replace("__NOMSITE__", Variable::lire('nomsite'), $texte);

		$texte = str_replace("__COMMANDE__", $Commande->ref, $texte);
		$texte = str_replace("__DATE__", strftime("%d/%m/%Y", $datecommande), $texte);
		$texte = str_replace("__HEURE__", strftime("%H:%M:%S", $datecommande), $texte);
		$texte = str_replace("__DATEDJ__", strftime("%d/%m/%Y"), $texte);
		$texte = str_replace("__COLIS__", $Commande->colis, $texte);

		$texteRetour = "Montant du bon de retour à votre charge : ".$fraisRetour."€ TTC";
		$texte = str_replace("__FRAIS_RETOUR__", $texteRetour, $texte);

		$texteEchange = "Montant des frais de livraisons à votre charge de votre colis d'échange  : ".$fraisEchange."€ TTC";
		$texte = str_replace("__FRAIS_ECHANGE__", $texteEchange, $texte);

		if ($this->delta > 0) {
				$delta = "Montant à déduire dû à la différence de retour et d'échanges : ".$this->delta."€ TTC";
		} else if ($this->delta < 0) {
				$delta = "Montant à ajouter dû à la différence de retour et d'échanges : ".str_replace('-', '', $this->delta)."€ TTC";
		}
		if (!empty($this->delta)) $texte = str_replace("__RAPPEL_DELTA__", $delta, $texte);
		else $texte = str_replace("__RAPPEL_DELTA__", '', $texte);

		/**/
		if($Savente) {
			$venteprod = new Venteprod();
			$SaventeRetour = new SaventeRetour();
			$tab_retours = $SaventeRetour->lister_retours($Savente->id);

			$retours = PHP_EOL;
			$retours .= '<h3>1. Vous souhaitez retourner les produits suivants :</h3>'.PHP_EOL;
			$retours .= PHP_EOL.'<table id="tableprix" border="0"><tbody>'.PHP_EOL.'
									<tr id="intitules">
										<th id="ref">Référence</th>
										<th id="designation">Désignation</th>
										<th id="pu">P.U.</th>
										<th id="qte">Qté</th></tr>'.PHP_EOL;
			$total_retour = 0;
			foreach($tab_retours AS $prod) {
				$venteprod->charger($prod->venteprod);

				//$tva = $venteprod->tva/100;
				//$tva += 1;
				//$prix_ttc = $venteprod->prixu*$tva;
				$prix_ttc = $venteprod->prixu;
				$sous_total = $venteprod->quantite*$prix_ttc;

				$total_retour += $venteprod->quantite*$prix_ttc;
				$retours .= '<tr class="ligneproduit">
								<td class="cellref">'.$venteprod->ref.'</td>
								<td class="celldsg">'.str_replace("\n", "<br />", $venteprod->titre).'</td>
								<td class="cellpu">'.round($prix_ttc, 2).'</td>
								<td class="cellqte">'.$venteprod->quantite.'</td></tr>'.PHP_EOL;
			}
			$retours .= '<tr class="ligneproduit"><td class="lignevide" colspan="4"></td></tr>'.PHP_EOL;
			$retours .= '<tr class="ligneproduit"><td class="totauxprix" colspan="3"></td><td>'.$total_retour.'</td></tr>'.PHP_EOL;
			$retours .= '</tbody></table>'.PHP_EOL;
			$texte = str_replace('__RETOURPRODUITS__',$retours,$texte);

			/**/
			$SaventeEchange = new SaventeEchange();
			$tab_echanges = $SaventeEchange->lister_echanges($Savente->id);

			$echanges = PHP_EOL;
			$echanges .= '<h3>2. Vous souhaiter commander les produits suivants en échange :</h3>'.PHP_EOL;
			$echanges .= '<table id="tableprix" border="0"><tbody>'.PHP_EOL.'
									<tr id="intitules">
										<th id="ref">Référence</th>
										<th id="designation">Désignation</th>
										<th id="pu">P.U.</th>
										<th id="qte">Qté</th></tr>'.PHP_EOL;
			$total_echange = 0;
			foreach($tab_echanges AS $prod) {
				$produit = new Produit();
				$produit->charger_id($prod->ref);

				$produitdesc = new Produitdesc();
				$produitdesc->charger($produit->id);

				if(!$produit->promo) $prix = $produit->prix;
				else $prix = $produit->prix2;

				//$tva = $prod->tva/100;
				//$tva += 1;
				//$prix_ttc = $prix*$tva;
				$prix_ttc = $prix;
				$sous_total = $prod->qte*$prix_ttc;

				$total_echange += $sous_total;

				$declidisp = new Declidisp();
				$declidisp->charger($prod->declidisp);

				$declidispdesc = new Declidispdesc();
				$declidispdesc->charger($prod->declidisp);

				$declinaisondesc = new Declinaisondesc();
				$declinaisondesc->charger($declidisp->declinaison);

				$echanges .= '<tr class="ligneproduit">
								<td class="cellref">'.$produit->ref.'</td>
								<td class="celldsg">'.str_replace("\n", "<br />", $produitdesc->titre).'</td>
								<td class="cellpu">'.$prix_ttc.'</td>
								<td class="cellqte">'.$prod->qte.'</td></tr>'.PHP_EOL;
			}
			$echanges .= '<tr class="ligneproduit"><td class="lignevide" colspan="4"></td></tr>'.PHP_EOL;
			$echanges .= '<tr class="ligneproduit"><td class="totauxprix" colspan="3"></td><td>'.$total_echange.'</td></tr>'.PHP_EOL;
			$echanges .= '</tbody></table>'.PHP_EOL;

			$texte = str_replace('__ECHANGEPRODUITS__',$echanges,$texte);

			/**/
			$Savente->delta = $total_echange-$total_retour;
			$Savente->maj();

			if($total_echange >= $total_retour) {
					$delta = $total_echange-$total_retour;
					$difference = '<p>Vous devrez : '.$delta.' € T.T.C à '.Variable::lire('nomsite').'</p>';

			} else {
					$delta = $total_retour-$total_echange;
					$difference = '<p>'.Variable::lire('nomsite').' vous devra : '.$delta.' € T.T.C</p>';
			}

			$texte = str_replace('__DIFFERENCE__',$difference,$texte);
		}

		return $texte;
	}

}

?>
