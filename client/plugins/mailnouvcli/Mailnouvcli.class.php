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
include_once(dirname(__FILE__) . "/../../../classes/PluginsClassiques.class.php");

include_once(dirname(__FILE__) . "/../../../classes/Message.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Messagedesc.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Paysdesc.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Variable.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Lang.class.php");
include_once(dirname(__FILE__) . "/../../../classes/Mail.class.php");

class Mailnouvcli extends PluginsClassiques
{
		public function __construct()
		{
				parent::__construct('mailnouvcli');
		}

		public function init()
		{
				$message = new Message();

				if (! $message->charger("mailnouvcli")) {
						$message->nom = "mailnouvcli";

						$lastid = $message->add();

						$messagedesc = new Messagedesc();
						$messagedesc->message = $lastid;
						$messagedesc->lang = ActionsLang::instance()->get_id_langue_defaut();

						$messagedesc->intitule = "Mail envoyé lors de l'inscription d'un client";
						$messagedesc->titre = "Votre compte sur __NOMSITE__";
						$messagedesc->chapo = "";
						$messagedesc->descriptiontext =
							"__CLIENT_RAISON__ __CLIENT_NOM__ __CLIENT_PRENOM__,\n\n"
							. "Nous vous remercions d'avoir crée votre compte sur __NOMSITE__. Pour mémoire, voici vos informations de connexion: \n\n"
							. "Adresse e-mail : __CLIENT_EMAIL__\n"
							. "Mot de passe : __CLIENT_MOTDEPASSE__\n\n"
							. "A bientôt sur __NOMSITE__\n\n"
							. "L'équipe __NOMSITE__"
						;
						$messagedesc->description = nl2br($messagedesc->descriptiontext);

						$messagedesc->add();
				}
		}

		public function destroy()
		{
				$message = new Message();

				if ($message->charger("mailnouvcli")) {
						if (method_exists($message, 'supprimer')) $message->supprimer();
						else $message->delete();
				}
		}

		public function apresclient($client)
		{
				$msgcli = new Message();
				$msgclidesc = new Messagedesc();

				$msgcli->charger("mailnouvcli");
				$msgclidesc->charger($msgcli->id);

				$client = &$_SESSION['navig']->client;

		    $mail = new Mail();
		    $mail->envoyer(
			      /*to_name*/$client->prenom." ".$client->nom,
			      /*to_adr*/$client->email,
			      /*from_name*/Variable::lire('nomsite'),
			      /*from_adresse*/Variable::lire('emailcontact'),
				    /*sujet*/$this->substitutions_mail($msgclidesc->titre, $client),
				    /*corps_html*/$this->substitutions_mail($msgclidesc->description, $client),
			      /*corps_texte*/$this->substitutions_mail($msgclidesc->descriptiontext, $client)
				);
		}

		private function substitutions_mail($str, $client)
		{
				$raisondesc = new Raisondesc($client->raison);
				$paysdesc = new Paysdesc($client->pays);

				$str = str_replace("__CLIENT_RAISON__", $raisondesc->long, $str);
				$str = str_replace("__CLIENT_ENTREPRISE__", $client->entreprise, $str);
				$str = str_replace("__CLIENT_SIRET__", $client->siret, $str);
				$str = str_replace("__CLIENT_INTRACOM__", $client->intracom, $str);
				$str = str_replace("__CLIENT_NOM__", $client->nom, $str);
				$str = str_replace("__CLIENT_PRENOM__", $client->prenom, $str);
				$str = str_replace("__CLIENT_ADRESSE1__", $client->adresse1, $str);
				$str = str_replace("__CLIENT_ADRESSE2__", $client->adresse2, $str);
				$str = str_replace("__CLIENT_ADRESSE3__", $client->adresse3, $str);
				$str = str_replace("__CLIENT_CPOSTAL__", $client->cpostal, $str);
				$str = str_replace("__CLIENT_VILLE__", $client->ville, $str);
				$str = str_replace("__CLIENT_PAYS__", $paysdesc->titre, $str);
				$str = str_replace("__CLIENT_TELFIXE__", $client->telfixe, $str);
				$str = str_replace("__CLIENT_TELPORT__", $client->telport, $str);
				$str = str_replace("__CLIENT_EMAIL__", $client->email, $str);
				$str = str_replace("__CLIENT_TYPE__", $client->type, $str);
				$str = str_replace("__CLIENT_POURCENTAGE__", $client->pourcentage, $str);

				$str = str_replace("__CLIENT_MOTDEPASSE__", lireParam('motdepasse1', 'string'), $str);

				$str = str_replace("__URLSITE__", Variable::lire('urlsite'), $str);
				$str = str_replace("__NOMSITE__", Variable::lire('nomsite'), $str);

				return $str;
		}
}
?>
