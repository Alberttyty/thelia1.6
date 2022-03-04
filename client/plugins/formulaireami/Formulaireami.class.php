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
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");

class Formulaireami extends PluginsClassiques
{
    public $valeurs=[
	      "nom"  =>"",
	      "prenom"  =>"",
	      "entreprise"  =>"",
	      "adresse"  =>"",
	      "code_postal"  =>"",
	      "ville"  =>"",
	      "telephone"  =>"",
	      "fax"  =>"",
	      "tel"  =>"",
	      "horaires"  =>"",
	      "ad_email"  =>"",
	      "nature"  =>"",
	      "objet"  =>"",
	      "message"  =>"",
	  		"panier"  =>"",
	      "demande_de_devis"  =>"",
	      "support"  =>"",
	      "format"  =>"",
	      "grammage"  =>"",
	      "quantite"  =>"",
	      "url"  =>"",
	      "produit"  =>"",
	      "message_masque"  =>"",
	      "pour_nom"  =>"",
	      "pour_ad_email"  =>"",
		];

		function Formulaireami()
		{
				$this->PluginsClassiques("Formulaireami");
		}

		function init()
		{
			  $this->ajout_desc("Formulaireami", "Formulaireami", "", 1);

				$test = new Message();

				if (! $test->charger("formulaireami")) {
						$message = new Message();
						$message->nom = "formulaireami";
						$lastid = $message->add();

						$messagedesc = new Messagedesc();
						$messagedesc->message = $lastid;
						$messagedesc->lang = 1;
						$messagedesc->intitule = "Mail du formulaire ami";
				    $messagedesc->titre = "Message du site internet";
						$messagedesc->description = "Message... __VARIABLES__";
						$messagedesc->descriptiontext = "Message... __VARIABLES__";
						$messagedesc->add();
				}
		}


		function action()
		{
			if(isset($_POST['action']) && $_POST['action'] == "formulaireami") {
					if(!function_exists('dsp_crypt')) {
				      $cryptinstall="lib/crypt/cryptographp.fct.php";
				      include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
		      }

		      if (chk_crypt($_REQUEST['txt_securite'])) {
							$emailcontact = new Variable();
							$emailcontact->charger("emailcontact");

							$nomsite = new Variable();
							$nomsite->charger("nomsite");

							$urlsite = new Variable();
							$urlsite->charger("urlsite");

							$message = new Message();
							$message->charger("formulaireami");

							$messagedesc = new Messagedesc();
							$messagedesc->charger($message->id);

			        if (isset($_POST['nomsite'])) {
			          	if ($_POST['nomsite']!="") $nomsite->valeur=$_POST['nomsite'];
			        }

			        if (isset($_POST['urlsite'])) {
			          	if ($_POST['urlsite']!="") $urlsite->valeur=$_POST['urlsite'];
			        }

							$messagedesc->descriptiontext = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->descriptiontext);
							$messagedesc->description = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->description);
							$messagedesc->descriptiontext = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->descriptiontext);
							$messagedesc->description = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->description);

							$variables="<ul>";
							$variablestext="";

							foreach($this->valeurs as $key => $value) {
					        $messagedesc->descriptiontext = str_replace("__VARIABLE_".strtoupper($key)."__",$_POST[$key], $messagedesc->descriptiontext);
					        $messagedesc->description = str_replace("__VARIABLE_".strtoupper($key)."__",$_POST[$key], $messagedesc->description);

					        if (isset($_POST[$key])) {
						          $variables.="<li><strong>".$key." : </strong> ".$_POST[$key]." </li>";
						          $variablestext.=$key."\n";
					        }
			        }
			        $variables.="</ul>";

			        $messagedesc->descriptiontext = str_replace("__VARIABLES__", $variablestext, $messagedesc->descriptiontext);
							$messagedesc->description = str_replace("__VARIABLES__", $variables, $messagedesc->description);

							if ($_POST['nom']==""&&$_POST['prenom']==""){$_POST['nom']="Nom Inconnu";}
							if ($_POST['pour_nom']==""){$_POST['pour_nom']="Nom Inconnu";}
			        if ($_POST['ad_email']==""){$_POST['ad_email']=$emailcontact->valeur;}
			        if ($_POST['pour_ad_email']==""){$_POST['pour_ad_email']=$emailcontact->valeur;}
			        $from=$_POST['ad_email'];
			        $fromname=$_POST['nom']." ".$_POST['prenom'];
			        $to=$_POST['pour_ad_email'];
			        $toname=$_POST['pour_nom'];

							$mail = new Mail();
							$mail->IsMail();
							$mail->FromName = $fromname;
							$mail->From = $from;
			        /*Ajout*/$mail->Sender = $from;
			        /*Ajout*/$mail->Hostname = substr(strrchr($from,'@'),1);
							$mail->Subject = $messagedesc->titre;
							$mail->MsgHTML($messagedesc->description);
							$mail->AltBody = $messagedesc->descriptiontext;
							$mail->AddAddress($to,$toname);
							$mail->AddReplyTo($from,$fromname);

							if ($mail->send()) $_POST['formulaireami_ok']=true;
			        else $_POST['formulaireami_erreur_envoi']=true;
						}
			      else $_POST['formulaireami_erreur_code']=true;
      	}
		}

		function post()
		{
	      global $res;

	      if (isset($_POST['formulaireami_ok'])){
	      		$res = preg_replace("`\#FORMULAIREAMI_ENVOI\[([^]]*)\]`", "\\1", $res);
	      }
	      else $res = preg_replace("`\#FORMULAIREAMI_ENVOI\[([^]]*)\]`", "", $res);

	      if (!function_exists('dsp_crypt')) {
			      $cryptinstall="lib/crypt/cryptographp.fct.php";
			      include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
	      }
	      $res = str_replace("#FORMULAIREAMI_ANTISPAM", dsp_crypt(0,0,0), $res);

	      if (isset($_POST['formulaireami_erreur_code'])){
	  				$res = preg_replace("`\#FORMULAIREAMI_ERREUR_CODE\[([^]]*)\]`", "\\1", $res);
	      }
	      else $res = preg_replace("`\#FORMULAIREAMI_ERREUR_CODE\[([^]]*)\]`", "", $res);

	      if (isset($_POST['formulaireami_erreur_envoi'])){
	  				$res = preg_replace("`\#FORMULAIREAMI_ERREUR_ENVOI\[([^]]*)\]`", "\\1", $res);
	      }
	      else $res = preg_replace("`\#FORMULAIREAMI_ERREUR_ENVOI\[([^]]*)\]`", "", $res);

	      foreach($this->valeurs as $key => $value) {
	        	$res = str_replace("#FORMULAIREAMI_".strtoupper($key),$_POST[$key], $res);
	      }
    }
}
?>
