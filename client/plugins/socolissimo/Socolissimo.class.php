<?php
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsTransports.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Mail.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracval.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdispdesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Venteadr.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Modules.class.php");

class Socolissimo extends PluginsTransports
{
		function Socolissimo()
		{
				$this->PluginsTransports("socolissimo");
		}

		function init()
		{
				$this->ajout_desc("Socolissimo", "Socolissimo", "", 1);
				$test = new Message();
				if(! $test->charger("socolissimo")) {
						$message = new Message();
						$message->nom = "socolissimo";
						$lastid = $message->add();

						$messagedesc = new Messagedesc();
						$messagedesc->message = $lastid;
						$messagedesc->lang = 1;
						$messagedesc->titre = "Socolissimo";
						$messagedesc->description = "__RAISON__ __NOM__ __PRENOM__,\n\nNous vous remercions de votre commande sur notre site __URLSITE__\n\nUn colis concernant votre commande __COMMANDE__ du __DATE__ __HEURE__ a quitté nos entrepôts pour être pris en charge par La Poste le __DATEDJ__.\n\nSon numéro de suivi est le suivant : __COLIS__\nIl vous permet de suivre votre colis en ligne sur le site de La Poste : www.coliposte.net\nIl vous sera, par ailleurs, très utile si vous étiez absent au moment de la livraison de votre colis : en fournissant ce numéro de Colissimo Suivi, vous pourrez retirer votre colis dans le bureau de Poste le plus proche.\n\nATTENTION ! Si vous ne trouvez pas l'avis de passage normalement déposé dans votre boîte aux lettres au bout de 48 Heures jours ouvrables, n'hésitez pas à aller le réclamer à votre bureau de Poste, muni de votre numéro de Colissimo Suivi.\n\nNous restons à votre disposition pour toute information complémentaire.\nCordialement";
						$messagedesc->add();
				}
		}

    function calcule()
		{
      	require_once(SITE_DIR."/client/plugins/socolissimo/config.php");
      	return socolissimo_calcul($this->zone,$this->nbart,$this->total,$this->poids);
		}

		function statut($commande)
		{
				if($commande->statut == "4" && $this->est_module_de_transport_pour($commande)) {
						/*if(! $commande->colis) return;*/

						$message = new Message();
						$message->charger("socolissimo");

						$messagedesc = new Messagedesc();
						$messagedesc->charger($message->id);

						$client = new Client();
						$client->charger_id($commande->client);

						$emailcontact = new Variable();
						$emailcontact->charger("emailcontact");

	        	$nomsite = new Variable();
	        	$nomsite->charger("nomsite");

						$corps = $this->remplacerVariables($messagedesc->description,$commande);
	        	$corpstext = $this->remplacerVariables($messagedesc->descriptiontext,$commande);

						$messagedesc->titre = str_replace("__COMMANDE__", $commande->ref, $messagedesc->titre);
	        	$sujet = $messagedesc->titre;

						//envoi du mail
	        	$mail = new Mail();
	        	$mail->envoyer(
	        	/*to_name*/$client->prenom." ".$client->nom,
						/*to_adr*/$client->email,
						/*from_name*/$nomsite->valeur,
						/*from_adresse*/$emailcontact->valeur,
			    	/*sujet*/$sujet,
			    	/*corps_html*/$corps,
	        	/*corps_texte*/$corpstext);
				}
		}

    function remplacerVariables($texte, $commande)
		{
      	$client = new Client();
				$client->charger_id($commande->client);

				if ($client->raison == "1") $raison = "Madame";
				else if ($client->raison == "2") $raison = "Mademoiselle";
				else if($client->raison == "3") $raison = "Monsieur";

      	$urlsite = new Variable();
				$urlsite->charger("urlsite");

      	$livraison = new Venteadr();
      	$livraison->charger($commande->adrlivr);

      	if($livraison->raison == "1") $livraisonraison = "Madame";
				else if($client->raison == "2") $livraisonraison = "Mademoiselle";
				else if($client->raison == "3") $livraisonraison = "Monsieur";

      	$jour = substr($commande->date, 8, 2);
	    	$mois = substr($commande->date, 5, 2);
	    	$annee = substr($commande->date, 0, 4);
      	$heure = substr($commande->date, 11, 2);
      	$minute = substr($commande->date, 14, 2);
      	$seconde = substr($commande->date, 17, 2);

      	$texte = str_replace("__RAISON__", $raison, $texte);
				$texte = str_replace("__NOM__", $client->nom, $texte);
				$texte = str_replace("__PRENOM__", $client->prenom, $texte);
				$texte = str_replace("__URLSITE__", $urlsite->valeur, $texte);
				$texte = str_replace("__COMMANDE__", $commande->ref, $texte);
				$texte = str_replace("__DATE__", $jour . "/" . $mois . "/" . $annee, $texte);
				$texte = str_replace("__HEURE__", $heure . ":" . $minute . ":" . $seconde, $texte);
				$texte = str_replace("__DATEDJ__", date("d") . "/" . date("m") . "/" . date("Y"), $texte);
				$texte = str_replace("__COLIS__", $commande->colis, $texte);
        $texte = str_replace("__LIVRAISON__", $livraisonraison, $texte);
      	$texte = str_replace("__LIVENTREPRISE__", $livraison->entreprise, $texte);
      	$texte = str_replace("__LIVNOM__", $livraison->nom, $texte);
      	$texte = str_replace("__LIVPRENOM__", $livraison->prenom, $texte);
      	$texte = str_replace("__LIVADRESSE1__", $livraison->adresse1, $texte);
      	$texte = str_replace("__LIVADRESSE2__", $livraison->adresse2, $texte);
      	$texte = str_replace("__LIVADRESSE3__", $livraison->adresse3, $texte);
      	$texte = str_replace("__LIVCPOSTAL__", $livraison->cpostal, $texte);
      	$texte = str_replace("__LIVVILLE__", $livraison->ville, $texte);
      	$texte = str_replace("__LIVPAYS__", $livraison->pays, $texte);
      	$texte = str_replace("__LIVTEL__", $livraison->tel, $texte);

      	return $texte;
    }

    function action()
		{
      	//Debug
      	//if($_SERVER['REMOTE_ADDR']=="176.144.57.53") var_dump($_SESSION["socolissimo"]);

      	if(isset($_REQUEST['action'])) {
        	if($_REQUEST['action']=='transport') {

          		$module = new Modules();
          		$module->charger_id($_REQUEST['id']);

          		if($module->nom==$this->getNom()) {

            		$champs=array('CEPHONENUMBER',
															'CEDOORCODE1',
															'CEDOORCODE2',
															'CEENTRYPHONE',
															'DYFORWARDINGCHARGES',
															'CEDELIVERYINFORMATION',
															'DELIVERYMODE',
															'PRNAME',
															'PRCOMPLADRESS',
															'PRADRESS1',
															'PRADRESS2',
															'PRZIPCODE',
															'PRTOWN',
															'CENAME',
															'CEFIRSTNAME',
															'CECIVILITY',
															'CECOMPANYNAME',
															'CEADRESS1',
															'CEADRESS2',
															'CEADRESS3',
															'CEADRESS4',
															'CEZIPCODE',
															'CETOWN',
															'CEPAYS',
															'PRID'
														);

            		foreach($champs as $key => $value) {
              			if(isset($_REQUEST[$value])) $_SESSION["socolissimo"][$value] = $_REQUEST[$value];
              			else $_SESSION["socolissimo"][$value] = "";
            		}

          		}

        	}
      	}

    }

    function aprescommande($commande)
		{
      	if($this->est_module_de_transport_pour($commande)) {
						$livraison = new Venteadr();
						$livraison->charger($commande->adrlivr);

						if($_SESSION["socolissimo"]['CECIVILITY']=='MR') $_SESSION["socolissimo"]['CECIVILITY']=3;
						else $_SESSION["socolissimo"]['CECIVILITY']=1;

						if($_SESSION["socolissimo"]['CEPAYS']=='BE') $_SESSION["socolissimo"]['CEPAYS']=20;
						else $_SESSION["socolissimo"]['CEPAYS']=64;

						$deliverymode="";
						if($_SESSION["socolissimo"]['DELIVERYMODE']=="DOM") $deliverymode="A domicile";
						if($_SESSION["socolissimo"]['DELIVERYMODE']=="RDV") $deliverymode="Sur RDV";
						if($_SESSION["socolissimo"]['DELIVERYMODE']=="BPR") $deliverymode="En bureau de poste";
						if($_SESSION["socolissimo"]['DELIVERYMODE']=="BDP") $deliverymode="En bureau de poste";
						if($_SESSION["socolissimo"]['DELIVERYMODE']=="A2P") $deliverymode="Chez un commerçant";
						if($_SESSION["socolissimo"]['DELIVERYMODE']=="CIT") $deliverymode="Cityssimo";
						if($_SESSION["socolissimo"]['DELIVERYMODE']=="ACP") $deliverymode="Agence Colis";
						if($_SESSION["socolissimo"]['DELIVERYMODE']=="CDI") $deliverymode="Centre Courrier";
						if($_SESSION["socolissimo"]['DELIVERYMODE']=="CMT") $deliverymode="Commerçant Belge";

						$livraison->raison=$_SESSION["socolissimo"]['CECIVILITY'];
						$livraison->entreprise=$_SESSION["socolissimo"]['CECOMPANYNAME'];
						$livraison->nom=$_SESSION["socolissimo"]['CENAME'];
						$livraison->prenom=$_SESSION["socolissimo"]['CEFIRSTNAME'];

						if($_SESSION["socolissimo"]['PRNAME']!='') {
							//EN POINT RELAIS
						  	$livraison->adresse1=$deliverymode;
						  	$livraison->adresse2=$_SESSION["socolissimo"]['PRNAME']." N°".$_SESSION["socolissimo"]['PRID'];
						  	$livraison->adresse3=$_SESSION["socolissimo"]['PRADRESS1'].' '.$_SESSION["socolissimo"]['PRADRESS2'];
						  	$livraison->cpostal=$_SESSION["socolissimo"]['PRZIPCODE'];
						  	$livraison->ville=$_SESSION["socolissimo"]['PRTOWN'];
						}
						elseif($_SESSION["socolissimo"]['DELIVERYMODE']=="RDV"){
							//SUR RDV
						  	$livraison->adresse1=$deliverymode;
						  	$livraison->adresse2=$_SESSION["socolissimo"]['CEADRESS1'].' '.$_SESSION["socolissimo"]['CEADRESS2'];
						  	$livraison->adresse3=$_SESSION["socolissimo"]['CEADRESS3'].' '.$_SESSION["socolissimo"]['CEADRESS4'];
						  	if($_SESSION["socolissimo"]['CEDOORCODE1']!="")$livraison->adresse3.=' C. porte 1:'.$_SESSION["socolissimo"]['CEDOORCODE1'];
						  	if($_SESSION["socolissimo"]['CEDOORCODE2']!="")$livraison->adresse3.=' C. porte 2:'.$_SESSION["socolissimo"]['CEDOORCODE2'];
						  	if($_SESSION["socolissimo"]['CEENTRYPHONE']!="")$livraison->adresse3.=' Interphone:'.$_SESSION["socolissimo"]['CEENTRYPHONE'];
						  	$livraison->cpostal=$_SESSION["socolissimo"]['CEZIPCODE'];
						  	$livraison->ville=$_SESSION["socolissimo"]['CETOWN'];
						}
						else {
							//NORMAL
						  	$livraison->adresse1=$_SESSION["socolissimo"]['CEADRESS1'].' '.$_SESSION["socolissimo"]['CEADRESS2'];
						  	$livraison->adresse2=$_SESSION["socolissimo"]['CEADRESS3'];
						  	$livraison->adresse3=$_SESSION["socolissimo"]['CEADRESS4'];
						  	if($_SESSION["socolissimo"]['CEDOORCODE1']!="")$livraison->adresse3.=' C. porte 1:'.$_SESSION["socolissimo"]['CEDOORCODE1'];
						  	if($_SESSION["socolissimo"]['CEDOORCODE2']!="")$livraison->adresse3.=' C. porte 2:'.$_SESSION["socolissimo"]['CEDOORCODE2'];
						  	if($_SESSION["socolissimo"]['CEENTRYPHONE']!="")$livraison->adresse3.=' Interphone:'.$_SESSION["socolissimo"]['CEENTRYPHONE'];
						  	$livraison->cpostal=$_SESSION["socolissimo"]['CEZIPCODE'];
						  	$livraison->ville=$_SESSION["socolissimo"]['CETOWN'];
						}

						$livraison->tel=$_SESSION["socolissimo"]['CEPHONENUMBER'];
						$livraison->pays=$_SESSION["socolissimo"]['CEPAYS'];

						$livraison->maj();
						unset($_SESSION["socolissimo"]);
      	}
    }
}

?>
