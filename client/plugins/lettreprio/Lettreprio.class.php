<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) Octolys Development		                                     */
/*		email : thelia@octolys.fr		        	                             	 */
/*      web : http://www.octolys.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/
?>
<?php

	include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsTransports.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Mail.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracval.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdispdesc.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");

	class Lettreprio extends PluginsTransports{


		function Lettreprio(){
			$this->PluginsTransports("lettreprio");
		}
		
		function init(){
			$this->ajout_desc("Lettreprio", "Lettreprio", "", 1);
			$test = new Message();
			if(! $test->charger("lettreprio")){
				$message = new Message();
				$message->nom = "lettreprio";
				$lastid = $message->add();

				$messagedesc = new Messagedesc();
				$messagedesc->message = $lastid;
				$messagedesc->lang = 1;
				$messagedesc->titre = "Lettreprio";
				$messagedesc->description = "__RAISON__ __NOM__ __PRENOM__,\n\nNous vous remercions de votre commande sur notre site __URLSITE__\n\nUn colis concernant votre commande __COMMANDE__ du __DATE__ __HEURE__ a quitt?? nos entrep??ts pour ??tre pris en charge par La Poste le __DATEDJ__.\n\nSon num??ro de suivi est le suivant : __COLIS__\nIl vous permet de suivre votre colis en ligne sur le site de La Poste : www.coliposte.net\nIl vous sera, par ailleurs, tr??s utile si vous ??tiez absent au moment de la livraison de votre colis : en fournissant ce num??ro de Colissimo Suivi, vous pourrez retirer votre colis dans le bureau de Poste le plus proche.\n\nATTENTION ! Si vous ne trouvez pas l'avis de passage normalement d??pos?? dans votre bo??te aux lettres au bout de 48 Heures jours ouvrables, n'h??sitez pas ?? aller le r??clamer ?? votre bureau de Poste, muni de votre num??ro de Colissimo Suivi.\n\nNous restons ?? votre disposition pour toute information compl??mentaire.\nCordialement";
				$messagedesc->add();

			}
		}
    
    function calcule(){
      require_once(SITE_DIR."/client/plugins/lettreprio/config.php");
      return lettreprio_calcul($this->zone,$this->nbart,$this->total,$this->poids);
		}
    
    private function substitutions($texte, $client, $commande) {

		$datecommande = strtotime($commande->date);

		$raisondesc = new Raisondesc();
		$raisondesc->charger($client->raison, $commande->lang);

		$texte = str_replace("__RAISON__", $raisondesc->long, $texte);
		$texte = str_replace("__NOM__", $client->nom, $texte);
		$texte = str_replace("__PRENOM__", $client->prenom, $texte);

		$texte = str_replace("__URLSITE__", Variable::lire('urlsite'), $texte);
		$texte = str_replace("__NOMSITE__", Variable::lire('nomsite'), $texte);

		$texte = str_replace("__COMMANDE__", $commande->ref, $texte);
		$texte = str_replace("__DATE__", strftime("%d/%m/%Y", $datecommande), $texte);
		$texte = str_replace("__HEURE__", strftime("%H:%M:%S", $datecommande), $texte);
		$texte = str_replace("__DATEDJ__", strftime("%d/%m/%Y"), $texte);
		$texte = str_replace("__COLIS__", $commande->colis, $texte);

		return $texte;
	 }
			
		function statut($commande){

			if ($commande->statut == Commande::EXPEDIE && $this->est_module_de_transport_pour($commande)){

				/*if(! $commande->colis)
					return;*/

				$message = new Message("lettreprio");
  			$messagedesc = new Messagedesc($message->id, $commande->lang);
  			$client = new Client($commande->client);

        $sujet = $this->substitutions($messagedesc->titre, $client, $commande);
  			$texte = $this->substitutions($messagedesc->descriptiontext, $client, $commande);
  			$html  = $this->substitutions($messagedesc->description, $client, $commande);
				
				//envoi du mail
    		Mail::envoyer("$client->prenom $client->nom", $client->email,Variable::lire('nomsite'),Variable::lire('emailcontact'),$sujet,$html,$texte);

			}

		}

	}

?>