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

/**
 * Permet de rediriger vers une page donnée, ou la page par défait en fin d'action...
 *
 * @param string $urlindiquee l'URL fournie, ou false pour ne pas rediriger.
 * @param string $urldefaut l'URL par defaut.
 */
function redirige_action($urlindiquee, $urldefaut) {
	if ($urlindiquee !== '') {
		// Permettre aux plugins d'empêcher la redirection en indiquant 'false' pour urlok ou urlerr
		if ($urlindiquee !== false) redirige(str_replace("&amp;", "&", $urlindiquee));
	}
	else if ($urldefaut != "")
		redirige($urldefaut);
}

// ajout panier
function ajouter($ref, $quantite=1, $append=0, $nouveau=0, $parent=-1) {

	$testprod = new Produit();

	if(! $testprod->charger($ref))
	return 0;

	if (intval($quantite<= 0)) $quantite = 1;

	$perso = array();

	// vérification si un produit avec la même déclinaison est déjà  présent
	foreach ($_REQUEST as $key => $valeur) {

		if(strstr($key, "declinaison")){
			$ps = new Perso();
			$ps->declinaison = substr($key, 11);
			$ps->valeur = stripslashes($valeur);

			$perso[] = $ps;
		}
	}

	$indicePanier = $_SESSION['navig']->panier->ajouter($ref, $quantite, $perso, $append, $nouveau, $parent);
}

// changement de transport
function transport($id){
	$transzone = new Transzone();

	$pays = new Pays();

	if (intval($_SESSION['navig']->adresse) > 0){
		$adr = new Adresse();
		$adr->charger($_SESSION['navig']->adresse);
		$pays->charger($adr->pays);
		
	} else $pays->charger($_SESSION['navig']->client->pays);

	if( ! $transzone->charger($id, $pays->zone)) {
		$_SESSION['navig']->commande->transport = "";
		return;
	}

	$_SESSION['navig']->commande->transport = $id;

}

// on fixe le code promo
function codepromo($code){

	ActionsModules::instance()->appel_module("avantpromo",$code);

	$promo = new Promo();
	$promo->charger($code);

	$_SESSION['navig']->promo = $promo;

	ActionsModules::instance()->appel_module("aprespromo",$code);
}

function calc_remise($total){

	$remise = 0;
	$promo = &$_SESSION['navig']->promo;

	if(intval($promo->id) > 0){

		if ($promo->mini <= $total) {

			if ($promo->type == Promo::TYPE_SOMME)
				$remise = $promo->valeur;
			else if($promo->type == Promo::TYPE_POURCENTAGE)
				$remise += $total * $promo->valeur / 100;
		}

		if($remise > $total) $remise = $total;
	}

	// Remise est passé par référence
	ActionsModules::instance()->appel_module("calc_remise", $remise, $total);

	return $remise;
}

// suppression d'un article du panier
function supprimer($article){
	$_SESSION['navig']->panier->supprimer($article);
}

// modification de la quantité d'un article
function modifier($article, $quantite, $parent=-1) {

	if(intval($quantite) >= 0)
		$_SESSION['navig']->panier->modifier($article, $quantite, $parent);
}

// connexion du client
function connexion($email,$motdepasse, $urlok="", $urlerr=""){

	$client = new Client();

	if ($client->charger($email, $motdepasse)) {

		$_SESSION['navig']->client = $client;
		$_SESSION['navig']->connecte = 1;

		ActionsModules::instance()->appel_module("apresconnexion", $client);

		if($_SESSION['navig']->urlpageret)
			redirige_action($urlok, $_SESSION['navig']->urlpageret);
		else
			redirige_action($urlok, urlfond());
	}
	else {
		redirige_action($urlerr, urlfond("connexion", "errconnex=1"));
	}
}

// déconnexion du client
function deconnexion(){

    ActionsModules::instance()->appel_module("avantdeconnexion", $_SESSION['navig']->client);

	$_SESSION['navig']->client= new Client();

	$_SESSION['navig']->connecte = 0;
	$_SESSION['navig']->adresse = 0;

	$_SESSION['navig']->urlpageret = supprimer_deconnexion($_SESSION['navig']->urlpageret);

	ActionsModules::instance()->appel_module("apresdeconnexion");
}

// modification de l'adresse en cours
function modadresse($adresse){

	if (intval($adresse) == 0) {
		$_SESSION['navig']->adresse=0;
	}
	else {
		$verif = new Adresse($adresse);

		if($verif->client == $_SESSION['navig']->client->id)
			$_SESSION['navig']->adresse=$adresse;
	}
}

// procédure de paiement
function paiement($type_paiement){

	if (! $_SESSION['navig']->client->id || $_SESSION['navig']->panier->nbart < 1)
		redirige(urlfond());

	$total = 0;
	$nbart = 0;
	$poids = 0;
	$unitetr = 0;

	ActionsModules::instance()->appel_module("avantcommande");

	$modules = new Modules();
	$modules->charger_id($type_paiement);

	if(! $modules->actif) return 0;

	try {

		$modpaiement = ActionsModules::instance()->instancier($modules->nom);

		$commande = new Commande();
		$commande->transport = $_SESSION['navig']->commande->transport;
		$commande->client = $_SESSION['navig']->client->id;
		$commande->remise = 0;

		$devise = ActionsDevises::instance()->get_devise_courante();
		$commande->devise = $devise->id;
		$commande->taux = $devise->taux;

		$client = new Client();
		$client->charger_id($_SESSION['navig']->client->id);

		$adr = new Venteadr();
		$adr->raison = $client->raison;
		$adr->entreprise = $client->entreprise;
		$adr->nom = $client->nom;
		$adr->prenom = $client->prenom;
		$adr->adresse1 = $client->adresse1;
		$adr->adresse2 = $client->adresse2;
		$adr->adresse3 = $client->adresse3;
		$adr->cpostal = $client->cpostal;
		$adr->ville = $client->ville;
		$adr->tel = $client->telfixe . "  " . $client->telport;
		$adr->pays = $client->pays;
		$adrcli = $adr->add();
		$commande->adrfact = $adrcli;

		$adr = new Venteadr();
		$livraison = new Adresse();

		if($livraison->charger($_SESSION['navig']->adresse)){

			$adr->raison = $livraison->raison;
			$adr->entreprise = $livraison->entreprise;
			$adr->nom = $livraison->nom;
			$adr->prenom = $livraison->prenom;
			$adr->adresse1 = $livraison->adresse1;
			$adr->adresse2 = $livraison->adresse2;
			$adr->adresse3 = $livraison->adresse3;
			$adr->cpostal = $livraison->cpostal;
			$adr->ville = $livraison->ville;
			$adr->tel = $livraison->tel;
			$adr->pays = $livraison->pays;

		}
		else {
			$adr->raison = $client->raison;
			$adr->entreprise = $client->entreprise;
			$adr->nom = $client->nom;
			$adr->prenom = $client->prenom;
			$adr->adresse1 = $client->adresse1;
			$adr->adresse2 = $client->adresse2;
			$adr->adresse3 = $client->adresse3;
			$adr->cpostal = $client->cpostal;
			$adr->ville = $client->ville;
			$adr->tel = $client->telfixe . "  " . $client->telport;
			$adr->pays = $client->pays;
		}
    
		$adrlivr = $adr->add();
		$commande->adrlivr = $adrlivr;

		$commande->facture = 0;

		$commande->statut=Commande::NONPAYE;
		$commande->paiement = $type_paiement;

		$commande->lang = ActionsLang::instance()->get_id_langue_courante();

		$commande->id = $commande->add();

		$pays = new Pays();
		$pays->charger($adr->pays);

		$correspondanceParent = array(null);

		foreach($_SESSION['navig']->panier->tabarticle as $pos => &$article) {
			$venteprod = new Venteprod();

			$dectexte = "\n";

			$produit = new Produit();

			$stock = new Stock();

			foreach($article->perso as $perso) {

				$declinaison = new Declinaison();
				$declinaisondesc = new Declinaisondesc();

				if(is_numeric($perso->valeur) && $modpaiement->defalqcmd) {

					// diminution des stocks de déclinaison si on est sur un module de paiement qui défalque de suite
					$stock->charger($perso->valeur, $article->produit->id);
					$stock->valeur-=$article->quantite;
					$stock->maj();
				}

				$declinaison->charger($perso->declinaison);
				$declinaisondesc->charger($declinaison->id);

				// recup valeur declidisp ou string
				if($declinaison->isDeclidisp($perso->declinaison)){
					$declidisp = new Declidisp();
					$declidispdesc = new Declidispdesc();
					$declidisp->charger($perso->valeur);
					$declidispdesc->charger_declidisp($declidisp->id);
					$dectexte .= "- " . $declinaisondesc->titre . " : " . $declidispdesc->titre . "\n";
				}
				else
					$dectexte .= "- " . $declinaisondesc->titre . " : " . $perso->valeur . "\n";

			}
      
			// diminution des stocks classiques si on est sur un module de paiement qui défalque de suite

			$produit = new Produit($article->produit->ref);

			if($modpaiement->defalqcmd){
				$produit->stock-=$article->quantite;
				$produit->maj();
			}

			/* Gestion TVA */
			$prix = $article->produit->prix;
			$prix2 = $article->produit->prix2;
			$tva = $article->produit->tva;

            if($pays->tva != "" && (! $pays->tva || ($pays->tva && $_SESSION['navig']->client->intracom != "" && !$pays->boutique))) {
				$prix = round($prix/(1+($tva/100)), 2);
				$prix2 = round($prix2/(1+($tva/100)), 2);
				$tva = 0;
			}

			$venteprod->quantite =  $article->quantite;

			if( ! $article->produit->promo)
				$venteprod->prixu =  $prix;
			else
				$venteprod->prixu =  $prix2;

			$venteprod->ref = $article->produit->ref;
			$venteprod->titre = $article->produitdesc->titre . " " . $dectexte;
			$venteprod->chapo = $article->produitdesc->chapo;
			$venteprod->description = $article->produitdesc->description;
			$venteprod->tva =  $tva;

			$venteprod->commande = $commande->id;
			$venteprod->id = $venteprod->add();

			$correspondanceParent[]=$venteprod->id;

			// ajout dans ventedeclisp des declidisp associées au venteprod
			foreach($article->perso as $perso){
				$declinaison = new Declinaison();
				$declinaison->charger($perso->declinaison);

				// si declidisp (pas un champs libre)
				if($declinaison->isDeclidisp($perso->declinaison)){
					$vdec = new Ventedeclidisp();
					$vdec->venteprod = $venteprod->id;
					$vdec->declidisp = $perso->valeur;
					$vdec->add();
				}
			}
      
			ActionsModules::instance()->appel_module("apresVenteprod", $venteprod, $pos);
      
			$total += $venteprod->prixu * $venteprod->quantite;
			$nbart++;
			$poids+= $article->produit->poids;
		}

		foreach($correspondanceParent as $id_panier => $id_venteprod) {

			if($_SESSION['navig']->panier->tabarticle[$id_panier]->parent>=0) {

				$venteprod->charger($id_venteprod);
				$venteprod->parent = $correspondanceParent[$_SESSION['navig']->panier->tabarticle[$id_panier]->parent];
				$venteprod->maj();
			}
		}

		$pays = new Pays($_SESSION['navig']->client->pays);

		if ($_SESSION['navig']->client->pourcentage>0) $commande->remise = $total * $_SESSION['navig']->client->pourcentage / 100;

		$total -= $commande->remise;

		if($_SESSION['navig']->promo->id != ""){

			$commande->remise += calc_remise($total);

			$_SESSION['navig']->promo->utilise = 1;

			if(!empty($commande->remise))
				$commande->remise = round($commande->remise, 2);

			$commande->maj();
			$temppromo = new Promo();
			$temppromo->charger_id($_SESSION['navig']->promo->id);

			$temppromo->utilise++;

			$temppromo->maj();

			$promoutil = new Promoutil();
			$promoutil->commande = $commande->id;
			$promoutil->promo = $temppromo->id;
			$promoutil->code = $temppromo->code;
			$promoutil->type = $temppromo->type;
			$promoutil->valeur = $temppromo->valeur;
			$promoutil->add();
		}

		if($commande->remise > $total)
			$commande->remise = $total;

		$commande->port = port();
		if(intval($commande->port) <= 0) $commande->port = 0;

		$_SESSION['navig']->promo = new Promo();
		$_SESSION['navig']->commande = $commande;

		$commande->transaction = genid($commande->id, 6);
    
		$commande->maj();

		$total = $_SESSION['navig']->panier->total(1,$_SESSION['navig']->commande->remise) + $_SESSION['navig']->commande->port;

		if($total<$_SESSION['navig']->commande->port)
		$total = $_SESSION['navig']->commande->port;

		$_SESSION['navig']->commande->total = $total;
    
		ActionsModules::instance()->appel_module("aprescommande", $commande);
    
		// Appeler la méthode mail du plugin de paiement...
		$modpaiement->mail($commande);
    
		// ... et la méthode paiement
		$modpaiement->paiement($commande);
     
	} catch (Exception $e) {
		// FIXME: Echec de commande -> cas à traiter ?
	}
}

// création d'un compte
function creercompte(
			$raison, $entreprise, $siret, $intracom,
			$prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $pays,
			$telfixe, $telport,
			$email1, $email2,
			$motdepasse1, $motdepasse2,
			$parrain, $obligetel=0, $urlok="", $urlerr=""){

	$client = new Client();
	$client->raison = strip_tags($raison);
	$client->nom = strip_tags($nom);
	$client->entreprise = strip_tags($entreprise);
	$client->prenom = strip_tags($prenom);
	$client->telfixe = strip_tags($telfixe);
	$client->telport =strip_tags($telport);
	if(filter_var($email1, FILTER_VALIDATE_EMAIL) && $email1==$email2)
		$client->email = strip_tags($email1);
	$client->adresse1 = strip_tags($adresse1);
	$client->adresse2 = strip_tags($adresse2);
	$client->adresse3 = strip_tags($adresse3);
	$client->cpostal = strip_tags($cpostal);
	$client->ville = strip_tags($ville);
	$client->siret = strip_tags($siret);
	$client->intracom = strip_tags($intracom);
	$client->pays = strip_tags($pays);
	$client->type = "0";
	$client->lang = ActionsLang::instance()->get_id_langue_courante();

	$testcli = new Client();
	if($parrain != "")
		if($testcli->charger_mail($parrain))
			$parrain=$testcli->id;
		else
			$parrain=-1;
	else
		$parrain=0;

	if($testcli->id != "") $client->parrain=$testcli->id;

	if ($motdepasse1 == $motdepasse2 && strlen($motdepasse1) > 3 ) $client->motdepasse = strip_tags($motdepasse1);

	$_SESSION['navig']->formcli = $client;

	$obligeok = 1;

	// obligetel : 0 non, 1 fixe, 2 portable, 3 au moins un des deux, 4 les deux
	switch($obligetel){
		case 0 : $obligeok = 1; break;
		case 1 : if($client->telfixe == "") $obligeok = 0; break;
		case 2 : if($client->telport == "") $obligeok = 0; break;
		case 3 : if($client->telfixe == "" && $client->telport == "") $obligeok = 0; break;
		case 4 : if($client->telfixe == "" || $client->telport == "") $obligeok = 0; break;
		default : $obligeok = 1;
	}

	ActionsModules::instance()->appel_module("avantclient");

	if($client->raison!="" && $client->prenom!="" && $client->nom!="" && $client->email!="" && $client->motdepasse!=""
	&& $client->email && ! $client->existe($email1) && $client->adresse1 !="" && $client->cpostal!="" && $client->ville !="" && $client->pays !="" && $obligeok){
		$_SESSION['navig']->client = $client;

		$client->crypter();

		$client->id = $client->add();

		if($client->charger_mail($client->email)) {
			$_SESSION['navig']->client = $client;
			$_SESSION['navig']->connecte = 1;
		}
		else
			return 0;

		ActionsModules::instance()->appel_module("apresclient", $client);

		redirige_action($urlok, urlfond("adresse"));
	}
	else {
		redirige_action($urlerr, urlfond("formulerr", "errform=1"));
	}
}

// modification de compte
function modifiercompte(
			$raison, $entreprise, $siret, $intracom,
			$prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $pays,
			$telfixe, $telport,
			$email1, $email2,
			$motdepasse1, $motdepasse2,
			$obligetel=0, $urlok="", $urlerr="") {

	$client = new Client();

	$client->charger_id($_SESSION['navig']->client->id);

	if( $motdepasse1 == "") {

		$client->id = $_SESSION['navig']->client->id;
		$client->raison = strip_tags($raison);
		$client->siret = strip_tags($siret);
		$client->intracom = strip_tags($intracom);
		$client->entreprise = strip_tags($entreprise);
		$client->nom = strip_tags($nom);
		$client->prenom = strip_tags($prenom);
		$client->telfixe = strip_tags($telfixe);
		$client->telport =strip_tags($telport);

		$errEmail = 0;
		if($email1 != $client->email){
			$test = new Client();
			if($test->existe($email1)){
				$errEmail = 1;
			}
		}

		if ($email1==$email2 && $email1 != "" && filter_var($email1, FILTER_VALIDATE_EMAIL))
			$client->email = strip_tags($email1);
		else
			$client->email = "";

		$client->adresse1 = strip_tags($adresse1);
		$client->adresse2 = strip_tags($adresse2);
		$client->adresse3 = strip_tags($adresse3);
		$client->cpostal = strip_tags($cpostal);
		$client->ville = strip_tags($ville);
		$client->pays = strip_tags($pays);
		$client->motdepasse = $_SESSION['navig']->client->motdepasse;

		$_SESSION['navig']->formcli = $client;

		$obligeok = 1;

		switch($obligetel){
			case 0 : $obligeok = 1; break;
			case 1 : if($client->telfixe == "") $obligeok = 0; break;
			case 2 : if($client->telport == "") $obligeok = 0; break;
			case 3 : if($client->telfixe == "" && $client->telport == "") $obligeok = 0; break;
			case 4 : if($client->telfixe == "" || $client->telport == "") $obligeok = 0; break;
			default : $obligeok = 1;
		}

		ActionsModules::instance()->appel_module("avantmodifcompte");

		if($client->raison!="" && $client->prenom!="" && $client->nom!="" && $client->email!=""
			&& $client->email && $client->adresse1 !="" && $client->cpostal!="" && $client->ville !="" && $client->pays !="" && $obligeok && !$errEmail) {

			$client->maj();
			$_SESSION['navig']->client = $client;
			ActionsModules::instance()->appel_module("apresmodifcompte", $client);

			redirige_action($urlok, $_SESSION['navig']->urlpageret);
		}
		else {
			redirige_action($urlerr, urlfond("compte_modifiererr", "errform=1"));
		}
	}
	else {
		modifiermotdepasse($motdepasse1, $motdepasse2, $urlok, $urlerr);
	}
}

// modification de mot de passe
function modifiermotdepasse($motdepasse1, $motdepasse2, $urlok="", $urlerr="") {

	$client = new Client($_SESSION['navig']->client->id);

	if(  $motdepasse1 == $motdepasse2 && strlen($motdepasse1) > 3) {

		$client->motdepasse = strip_tags($motdepasse1);
		$client->crypter();
		$client->maj();

		$_SESSION['navig']->client = $client;

		ActionsModules::instance()->appel_module("apresmodifcompte", $client);

		redirige_action($urlok, $_SESSION['navig']->urlpageret);
	}
	else  {
		$_SESSION['navig']->formcli->motdepasse = "";

		redirige_action($urlerr, urlfond("compte_modifiererr", "errform=1"));
	}
}

// création d'une adresse de livraison
function creerlivraison($id, $libelle, $raison, $entreprise, $prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $tel, $pays, $urlok="", $urlerr=""){

	$adresse = new Adresse();
	$adresse->libelle = strip_tags($libelle);
	$adresse->raison = strip_tags($raison);
	$adresse->entreprise = strip_tags($entreprise);
	$adresse->prenom = strip_tags($prenom);
	$adresse->nom = strip_tags($nom);
	$adresse->adresse1 = strip_tags($adresse1);
	$adresse->adresse2 = strip_tags($adresse2);
	$adresse->adresse3 = strip_tags($adresse3);
	$adresse->cpostal = strip_tags($cpostal);
	$adresse->ville = strip_tags($ville);
	$adresse->tel = strip_tags($tel);
	$adresse->pays = strip_tags($pays);
	$adresse->client = $_SESSION['navig']->client->id;

	$_SESSION['navig']->formadr = $adresse;

	if ($libelle != "" && $raison != "" && $prenom != "" && $nom != "" && $adresse1 != "" && $cpostal != "" && $ville != "" && $pays != "") {

		$adresse->id = $adresse->add();

		$_SESSION['navig']->adresse=$adresse->id;

		ActionsModules::instance()->appel_module("apres_creerlivraison", $adresse);
	}
	else
		redirige_action($urlerr, '' /* Page erreur pas prevue initialemet */);

	redirige_action($urlok, $_SESSION['navig']->urlpageret);
}


// suppression d'une adresse de livraison
function supprimerlivraison($id){

	$adresse = new Adresse();
	$adresse->charger($id);

	if($adresse->client != $_SESSION['navig']->client->id) return;

	$adresse->delete();

	if($_SESSION['navig']->adresse == $id)
	$_SESSION['navig']->adresse = 0;

}

// modification d'une adresse de livraison
function modifierlivraison($id, $libelle, $raison, $entreprise, $prenom, $nom, $adresse1, $adresse2, $adresse3, $cpostal, $ville, $tel, $pays, $urlok="", $urlerr=""){

	$adresse = new Adresse($id);

	if($adresse->client != $_SESSION['navig']->client->id) return;

	$adresse->libelle = strip_tags($libelle);
	$adresse->raison = strip_tags($raison);
	$adresse->entreprise = strip_tags($entreprise);
	$adresse->prenom = strip_tags($prenom);
	$adresse->nom = strip_tags($nom);
	$adresse->adresse1 = strip_tags($adresse1);
	$adresse->adresse2 = strip_tags($adresse2);
	$adresse->adresse3 = strip_tags($adresse3);
	$adresse->cpostal = strip_tags($cpostal);
	$adresse->ville = strip_tags($ville);
	$adresse->tel = strip_tags($tel);
	$adresse->pays = strip_tags($pays);

	$_SESSION['navig']->formadr = $adresse;

	if ($libelle != "" && $raison != "" && $prenom != "" && $nom != "" && $adresse1 != "" && $cpostal != "" && $ville != "" && $pays != "") {

		$adresse->maj();

		ActionsModules::instance()->appel_module("apres_modifierlivraison", $adresse);
	}
	else {
		redirige_action($urlerr, '' /* Page erreur pas prevue initialemet */);
	}

	redirige_action($urlok, $_SESSION['navig']->urlpageret);
}

// changement du mot de passe
function chmdp($email, $urlok="", $urlerr=""){

	$tclient  = new Client();

	if( $tclient->charger_mail($email)) {

		$msg = new Message();
		$msgdesc = new Messagedesc();

		$pass = genpass(8);
		$tclient->motdepasse = $pass;
		$tclient->crypter();
		$tclient->maj();

		$msg->charger("changepass");
		$msgdesc->charger($msg->id);

		$sujet = $msgdesc->titre;

		$corps = $msgdesc->description;
		$corpstext = $msgdesc->descriptiontext;

		$nomsite = Variable::lire("nomsite");

		$corps = str_replace("__NOMSITE__",$nomsite,$corps);
		$corps = str_replace("__MOTDEPASSE__",$pass,$corps);
		$corps = str_replace("__URLSITE__", urlfond(),$corps);
		$corps = str_replace("__NOM__", $tclient->nom,$corps);
		$corps = str_replace("__PRENOM__", $tclient->prenom,$corps);
		$corps = str_replace("__EMAIL__", $tclient->email,$corps);

		$corpstext = str_replace("__NOMSITE__",$nomsite,$corpstext);
		$corpstext = str_replace("__MOTDEPASSE__",$pass,$corpstext);
		$corpstext = str_replace("__URLSITE__", urlfond(),$corpstext);
		$corpstext = str_replace("__NOM__", $tclient->nom,$corpstext);
		$corpstext = str_replace("__PRENOM__", $tclient->prenom,$corpstext);
		$corpstext = str_replace("__EMAIL__", $tclient->email,$corpstext);

		$emailfrom = Variable::lire("emailfrom");

		Mail::envoyer(
			$tclient->prenom . " " . $tclient->nom, $tclient->email,
			$nomsite, $emailfrom,
			$sujet,
			$corps, $corpstext);

		redirige_action($urlok, '' /* Pas prevu initialement */);
	}
	else {
		redirige_action($urlerr, urlfond("mdperreur"));
	}
}

function reprise_paiement($id_paiement, $id_commande) {
    if (!$_SESSION['navig']->client->id) {
        redirige(urlfond());
    }

    $commande = new Commande();
    $paiement = new Modules();

    if ($commande->charger_id($id_commande) && $paiement->charger_id($id_paiement)) {
        if ($commande->client != $_SESSION['navig']->client->id) {
            redirige(urlfond());
        }

        $_SESSION['navig']->panier = new Panier();

        $commande->total = $commande->total(true, true);
        $commande->paiement = $paiement->id;

        $commande->maj();

        $_SESSION['navig']->commande = $commande;

        $className = $paiement->nom;

        $modpaiement = ActionsModules::instance()->instancier($paiement->nom);

        $modpaiement->paiement($commande);
    }
}
?>
