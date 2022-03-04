<?php

	include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsTransports.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Mail.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracval.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdispdesc.class.php");

	class Colissimorecommande extends PluginsTransports{


		function Colissimorecommande(){
			$this->PluginsTransports("colissimorecommande");
		}
		
		function init(){
			$this->ajout_desc("Colissimo recommandé", "Colissimo recommandé", "", 1);
			$test = new Message();
			if(! $test->charger("colissimorecommande")){
				$message = new Message();
				$message->nom = "colissimorecommande";
				$lastid = $message->add();

				$messagedesc = new Messagedesc();
				$messagedesc->message = $lastid;
				$messagedesc->lang = 1;
				$messagedesc->titre = "Colissimo recommande";
				$messagedesc->description = "__RAISON__ __NOM__ __PRENOM__,\n\nNous vous remercions de votre commande sur notre site __URLSITE__\n\nUn colis concernant votre commande __COMMANDE__ du __DATE__ __HEURE__ a quitté nos entrepôts pour être pris en charge par La Poste le __DATEDJ__.\n\nSon numéro de suivi est le suivant : __COLIS__\nIl vous permet de suivre votre colis en ligne sur le site de La Poste : www.coliposte.net\nIl vous sera, par ailleurs, très utile si vous étiez absent au moment de la livraison de votre colis : en fournissant ce numéro de Colissimo Suivi, vous pourrez retirer votre colis dans le bureau de Poste le plus proche.\n\nATTENTION ! Si vous ne trouvez pas l'avis de passage normalement déposé dans votre boîte aux lettres au bout de 48 Heures jours ouvrables, n'hésitez pas à aller le réclamer à votre bureau de Poste, muni de votre numéro de Colissimo Suivi.\n\nNous restons à votre disposition pour toute information complémentaire.\nCordialement";
				$messagedesc->add();

			}
		}
    
    function calcule(){
      require_once(SITE_DIR."/client/plugins/colissimorecommande/config.php");
      return colissimorecommande_calcul($this->zone,$this->nbart,$this->total,$this->poids);
		}
			
		function statut($commande){

			if($commande->statut == "4" && $this->est_module_de_transport_pour($commande)){

				/*if(! $commande->colis)
					return;*/

				$message = new Message();
				$message->charger("colissimorecommande");

				$messagedesc = new Messagedesc();
				$messagedesc->charger($message->id);

				$client = new Client();
				$client->charger_id($commande->client);

				if($client->raison == "1")
					$raison = "Madame";
				else
					if($client->raison == "2")
						$raison = "Mademoiselle";
				else
					if($client->raison == "3")
						$raison = "Monsieur";

				$urlsite = new Variable();
				$urlsite->charger("urlsite");

				$emailcontact = new Variable();
				$emailcontact->charger("emailcontact");

          		$nomsite = new Variable();
        $nomsite->charger("nomsite");

        $jour = substr($commande->date, 8, 2);
		    $mois = substr($commande->date, 5, 2);
		    $annee = substr($commande->date, 0, 4);
        $heure = substr($commande->date, 11, 2);
        $minute = substr($commande->date, 14, 2);
        $seconde = substr($commande->date, 17, 2);

				$messagedesc->description = str_replace("__RAISON__", "$raison", $messagedesc->description);
				$messagedesc->description = str_replace("__NOM__", $client->nom, $messagedesc->description);
				$messagedesc->description = str_replace("__PRENOM__", $client->prenom, $messagedesc->description);
				$messagedesc->description = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->description);
				$messagedesc->description = str_replace("__COMMANDE__", $commande->ref, $messagedesc->description);
				$messagedesc->description = str_replace("__DATE__", $jour . "/" . $mois . "/" . $annee, $messagedesc->description);
				$messagedesc->description = str_replace("__HEURE__", $heure . ":" . $minute . ":" . $seconde, $messagedesc->description);
				$messagedesc->description = str_replace("__DATEDJ__", date("d") . "/" . date("m") . "/" . date("Y"), $messagedesc->description);
				$messagedesc->description = str_replace("__COLIS__", $commande->colis, $messagedesc->description);
				$corps=$messagedesc->description;
				
				$messagedesc->descriptiontext = str_replace("__RAISON__", "$raison", $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__NOM__", $client->nom, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__PRENOM__", $client->prenom, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__COMMANDE__", $commande->ref, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__DATE__", $jour . "/" . $mois . "/" . $annee, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__HEURE__", $heure . ":" . $minute . ":" . $seconde, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__DATEDJ__", date("d") . "/" . date("m") . "/" . date("Y"), $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__COLIS__", $commande->colis, $messagedesc->descriptiontext);
				$corpstext=$messagedesc->descriptiontext;
				
				$messagedesc->titre = str_replace("__COMMANDE__", $commande->ref, $messagedesc->titre);
        $sujet=$messagedesc->titre;
				
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

	}

?>