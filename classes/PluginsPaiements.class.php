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
require_once __DIR__ . "/../fonctions/autoload.php";
require_once(__DIR__ . "/../fonctions/url.php");

class PluginsPaiements extends PluginsClassiques
{
		public $defalqcmd = 1;

		public function __construct($nom="")
		{
				parent::__construct($nom);
		}

		/* Compatibilité avec les anciens plugins */
		public function PluginsPaiements($nom="")
		{
				parent::__construct($nom);
		}

		public function paiement($commande)
		{
		}

		public function getDevise()
		{
				return $this->modulesdesc->devise;
		}

		/*
		 * Permet de déterminer si ce module est le module de paiement pour
		 * une commande donnée
		 */
		public function est_module_de_paiement_pour($commande)
		{
				$module = new Modules();
				return $module->charger_id($commande->paiement) && $module->nom == $this->getNom();
		}

		public function mail($commande = null)
		{
				// Les mails sont envoyés en ISO.
				// En effet les clients Mail ne sont pas tous au point pour l'UTF-8
				$sujet="";
				$corps="";

				$emailcontact = Variable::lire("emailcontact");
				$emailfrom = Variable::lire("emailfrom");
				$nomsite = Variable::lire("nomsite");

				/* Message client */
				$msg = new Message("mailconfirmcli");

				$msgdesc = new Messagedesc($msg->id,$commande->lang);

				$sujet = $this->substitmail($msgdesc->titre, $commande);
				$corps = $this->substitmail($msgdesc->description, $commande);
				$corpstext = $this->substitmail($msgdesc->descriptiontext,$commande);

				$client = new Client($commande->client);

				// Envoi du mail au client
				Mail::envoyer(
						$client->prenom . " " . $client->nom, $client->email,
						$nomsite, $emailfrom,
						$sujet,
						$corps, $corpstext
				);

				/* Message admin */
				$msg->charger("mailconfirmadm");

				$msgdesc = new Messagedesc($msg->id);

				$sujet = $this->substitmail($msgdesc->titre, $commande);
				$corps = $this->substitmail($msgdesc->description, $commande);
				$corpstext = $this->substitmail($msgdesc->descriptiontext,$commande);

				// Notifier le ou les administrateurs
				$emailscommande = Variable::lire("emailscommande");

				if (trim($emailscommande) == '') $emailscommande = $emailcontact;

				$emails = explode(',', $emailscommande);

				foreach($emails as $email) {
						$email = trim($email);

						if (empty($email)) continue;

						Mail::envoyer(
								$nomsite, $email,
								$nomsite, $emailfrom,
								$sujet,
								$corps, $corpstext
						);
				}
		}

		public function substitmail($corps, $commande)
		{
				ActionsModules::instance()->appel_module("preSubstitmail", $corps, $commande);

				$nomsite = Variable::lire("nomsite");

				$jour = substr($commande->date, 8, 2);
				$mois = substr($commande->date, 5, 2);
				$annee = substr($commande->date, 0, 4);

				$heure = substr($commande->date, 11, 2);
				$minute = substr($commande->date, 14, 2);
				$seconde = substr($commande->date, 17, 2);

				$client = new Client($commande->client);

				$paiement = new Modules($commande->paiement);
				$paiementdesc = new Modulesdesc($paiement->nom, $commande->lang);

				$transport = new Modules($commande->transport);
				$transportdesc = new Modulesdesc($transport->nom, $commande->lang);

				$total = $commande->total();
				$totcmdport = $commande->port + $total;

				$adresse = new Venteadr($commande->adrlivr);

				$raisondesc = new Raisondesc();
				$raisondesc->charger($adresse->raison, $commande->lang);

				$nom = $adresse->nom;
				$prenom = $adresse->prenom;
				$entreprise = $adresse->entreprise;
				$adresse1 = $adresse->adresse1;
				$adresse2 = $adresse->adresse2;
				$adresse3 =  $adresse->adresse3;
				$cpostal =  $adresse->cpostal;
				$ville = $adresse->ville;
				$pays = new Paysdesc($adresse->pays,$commande->lang);

				$corps = str_replace("__COMMANDE_REF__", $commande->ref, $corps);
				$corps = str_replace("__COMMANDE_DATE__", $jour . "/" . $mois . "/" . $annee, $corps);
				$corps = str_replace("__COMMANDE_HEURE__", $heure . ":" . $minute, $corps);
				$corps = str_replace("__COMMANDE_TRANSACTION__", $commande->transaction, $corps);
				$corps = str_replace("__COMMANDE_PAIEMENT__", $paiementdesc->titre, $corps);
				$corps = str_replace("__COMMANDE_TOTALPORT__", $totcmdport-$commande->remise, $corps);
				$corps = str_replace("__COMMANDE_TOTAL__", $total, $corps);
				$corps = str_replace("__COMMANDE_PORT__", $commande->port, $corps);
				$corps = str_replace("__COMMANDE_REMISE__", $commande->remise, $corps);
				$corps = str_replace("__COMMANDE_TRANSPORT__", $transportdesc->titre, $corps);
				$corps = str_replace("__COMMANDE_TRANSPORTCHAPO__", $transportdesc->chapo, $corps);
				$corps = str_replace("__COMMANDE_LIVRRAISON__", $raisondesc->court, $corps);
				$corps = str_replace("__COMMANDE_LIVRNOM__",$nom, $corps);
				$corps = str_replace("__COMMANDE_LIVRPRENOM__", $prenom, $corps);
				$corps = str_replace("__COMMANDE_LIVRENTREPRISE__",$entreprise, $corps);
				$corps = str_replace("__COMMANDE_LIVRADRESSE1__", $adresse1, $corps);
				$corps = str_replace("__COMMANDE_LIVRADRESSE2__", $adresse2, $corps);
				$corps = str_replace("__COMMANDE_LIVRADRESSE3__", $adresse3, $corps);
				$corps = str_replace("__COMMANDE_LIVRCPOSTAL__", $cpostal, $corps);
				$corps = str_replace("__COMMANDE_LIVRVILLE__", $ville, $corps);
				$corps = str_replace("__COMMANDE_LIVRPAYS__", $pays->titre, $corps);
				$corps = str_replace("__COMMANDE_LIVRTEL__", $adresse->tel, $corps);

				$corps = str_replace("__NOMSITE__", $nomsite, $corps);
				$corps = str_replace("__URLSITE__", urlfond(), $corps);

				$adresse = new Venteadr($commande->adrfact);

				$raisondesc = new Raisondesc();
				$raisondesc->charger($adresse->raison);

				$pays = new Paysdesc();
				$pays->charger($adresse->pays);

				$corps = str_replace("__CLIENT_REF__", $client->ref, $corps);
				$corps = str_replace("__CLIENT_RAISON__",$raisondesc->court, $corps);
				$corps = str_replace("__CLIENT_ENTREPRISE__", $client->entreprise, $corps);
				$corps = str_replace("__CLIENT_SIRET__", $client->siret, $corps);
				$corps = str_replace("__CLIENT_FACTNOM__", $adresse->nom, $corps);
				$corps = str_replace("__CLIENT_FACTPRENOM__", $adresse->prenom, $corps);
				$corps = str_replace("__CLIENT_ADRESSE1__", $adresse->adresse1, $corps);
				$corps = str_replace("__CLIENT_ADRESSE2__", $adresse->adresse2, $corps);
				$corps = str_replace("__CLIENT_ADRESSE3__", $adresse->adresse3, $corps);
				$corps = str_replace("__CLIENT_CPOSTAL__", $adresse->cpostal, $corps);
				$corps = str_replace("__CLIENT_VILLE__", $adresse->ville, $corps);
				$corps = str_replace("__CLIENT_PAYS__", $pays->titre, $corps);
				$corps = str_replace("__CLIENT_EMAIL__", $client->email, $corps);
				$corps = str_replace("__CLIENT_TELFIXE__", $client->telfixe, $corps);
				$corps = str_replace("__CLIENT_TELPORT__", $client->telport, $corps);

				$pattern = '{<VENTEPROD>((?:(?:(?!<VENTEPROD[^>]*>|</VENTEPROD>).)++|<VENTEPROD[^>]*>(?1)</VENTEPROD>)*)</VENTEPROD>}si';

				if (preg_match($pattern, $corps, $cut)) {
						$corps = str_replace("<VENTEPROD>", "", $corps);
						$corps = str_replace("</VENTEPROD>", "", $corps);

						$res="";

						$venteprod = new Venteprod();

						$query = "select * from $venteprod->table where commande=\"" . $commande->id . "\"";
						$resul = $venteprod->query($query);

						while($resul && $row = $venteprod->fetch_object($resul)) {
								$temp = str_replace("__VENTEPROD_TITRE__", $row->titre, $cut[1]);
								$temp =  str_replace("__VENTEPROD_REF__", $row->ref, $temp);
								$temp =  str_replace("__VENTEPROD_CHAPO__", $row->chapo, $temp);
								$temp =  str_replace("__VENTEPROD_QUANTITE__", $row->quantite, $temp);
								$temp =  str_replace("__VENTEPROD_PRIXU__", $row->prixu, $temp);
								$temp =  str_replace("__VENTEPROD_TOTAL__", $row->prixu * $row->quantite, $temp);

								ActionsModules::instance()->appel_module("substitutionsventeprodmailcommande", $temp, $row);

								$res .= $temp;
						}

						$corps = str_replace($cut[1], $res, $corps);
				}

				// Substitutions mail "devise"
				$devise = new Devise($commande->devise);
				ActionsDevises::instance()->subsititutions_mail($devise, $corps);

				ActionsModules::instance()->appel_module("substitutionsmailcommande", $corps, $commande);

				return $corps;
		}
}
?>
