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
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");

class Formulairecontact extends PluginsClassiques
{
    public $valeurs = [
  			"nom"  			=> "",
  			"prenom"  		=> "",
  			"entreprise"	=> "",
  			"adresse"  		=> "",
  			"code_postal"	=> "",
  			"ville"  		=> "",
  			"telephone" 	=> "",
  			"fax"  			=> "",
  			"tel"  			=> "",
  			"pays"  		=> "",
  			"ad_email"  	=> "",
  			"objet"  		=> "",
  			"message"  		=> "",
  			"txt_securite" 	=> "",
  			"voyage"  		=> "",
  			"nature"  		=> "",
  			"date_souhaitee"=> "",
  			"nb_adultes"  	=> "",
        "nb_enfants"  	=> "",
  			"recevoir_catalogue"	=> "",
        "assurance_annulation"	=> "",
  			"compensation_co2"  	=> "",
  			"numero_devis"  => "",
  			"montant"  		=> "",
  			"groupe_nom"  	=> "",
  			"groupe_type"  	=> "",
  			"voyage_type"  	=> "",
  			"duree"  		=> "",
  			"periode"  		=> "",
  			"nb_groupe"  	=> "",
  			"nb_personne_par_groupe"=> "",
  			"budget"  		=> "",
  			"lieu_depart"  	=> "",
  			"destinations"  => "",
  			"hebergement"  	=> "",
  			"affiliation"  	=> "",
	 	];

  	function Formulairecontact()
    {
  		  $this->PluginsClassiques("Formulairecontact");
  	}

  	function init()
    {
  	  	$this->ajout_desc("Formulairecontact", "Formulairecontact", "", 1);

    		$test = new Message();
    		if (! $test->charger("formulairecontact")) {
      			$message = new Message();
      			$message->nom = "formulairecontact";
      			$lastid = $message->add();
      			$messagedesc = new Messagedesc();
      			$messagedesc->message = $lastid;
      			$messagedesc->lang = 1;
      			$messagedesc->intitule = "Mail du formulaire de contact";
      		  $messagedesc->titre = "Message du site internet";
      			$messagedesc->description = "Message... __VARIABLES__";
      			$messagedesc->descriptiontext = "Message... __VARIABLES__";
      			$messagedesc->add();
    		}

        $cryptograph_largeur = new Variable();
    		if (! $cryptograph_largeur->charger("cryptograph_largeur")) {
      			$cryptograph_largeur->nom = "cryptograph_largeur";
      			$cryptograph_largeur->valeur = 100;
      			$cryptograph_largeur->add();
    		}

        $cryptograph_hauteur = new Variable();
    		if (! $cryptograph_hauteur->charger("cryptograph_hauteur")) {
      			$cryptograph_hauteur->nom = "cryptograph_hauteur";
      			$cryptograph_hauteur->valeur = 30;
      			$cryptograph_hauteur->add();
    		}

        $cryptograph_bg = new Variable();
    		if (! $cryptograph_bg->charger("cryptograph_bg")) {
      			$cryptograph_bg->nom = "cryptograph_bg";
      			$cryptograph_bg->valeur = "#000000";
      			$cryptograph_bg->add();
    		}

  	    $cryptograph_char = new Variable();
    		if (! $cryptograph_char->charger("cryptograph_char")) {
      			$cryptograph_char->nom = "cryptograph_char";
      			$cryptograph_char->valeur = "#FFFFFF";
      			$cryptograph_char->add();
    		}

        $obligatoires = new Variable();
    		if (! $obligatoires->charger("contact_obligatoires")) {
      			$obligatoires->nom = "contact_obligatoires";
      			$obligatoires->valeur = "nom,ad_email,txt_securite";
      			$obligatoires->add();
    		}

        $cryptograph_char_min = new Variable();
    		if (! $cryptograph_char_min->charger("cryptograph_char_min")) {
      			$cryptograph_char_min->nom = "cryptograph_char_min";
      			$cryptograph_char_min->valeur = "22";
      			$cryptograph_char_min->add();
    		}

        $cryptograph_char_max = new Variable();
    		if (! $cryptograph_char_max->charger("cryptograph_char_max")) {
      			$cryptograph_char_max->nom = "cryptograph_char_max";
      			$cryptograph_char_max->valeur = "22";
      			$cryptograph_char_max->add();
    		}

        $cryptograph_img_fond = new Variable();
    		if (! $cryptograph_img_fond->charger("cryptograph_img_fond")) {
      			$cryptograph_img_fond->nom = "cryptograph_img_fond";
      			$cryptograph_img_fond->valeur = "";
      			$cryptograph_img_fond->add();
    		}
  	}

  	function action()
    {
    		if (isset($_POST['action']) && $_POST['action'] == "formulairecontact") {
      			if (!function_exists('dsp_crypt')) {
        				$cryptinstall="lib/crypt/cryptographp.fct.php";
        				include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
      			}

      			foreach($this->valeurs as $key => $value) {
      			  	$_POST[$key] = strip_tags($_POST[$key]);
      			}

      			$champs_requis = true;

          	$obligatoires = new Variable();
          	if ($obligatoires->charger("contact_obligatoires")) {
        				$obligatoires=explode(',',$obligatoires->valeur);
        				foreach ($obligatoires as $key => $value) {
          					if ($_POST[$value]=="") $champs_requis=false;
          					if ($value=='ad_email') {
            						if (! filter_var($_POST[$value],FILTER_VALIDATE_EMAIL)) {
              							$champs_requis=false;
              							$_POST['formulairecontact_erreur_email']=true;
            						}
          					}
        				}
      			}

            if ($champs_requis) {
        				if (chk_crypt($_REQUEST['txt_securite'])) {
          					$emailcontact = new Variable();
          					$emailcontact->charger("emailcontact");

          					$nomsite = new Variable();
          					$nomsite->charger("nomsite");

          					$urlsite = new Variable();
          					$urlsite->charger("urlsite");

          					$message = new Message();
          					$message->charger("formulairecontact");

          					$messagedesc = new Messagedesc();
          					$messagedesc->charger($message->id);

          					$messagedesc->descriptiontext = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->descriptiontext);
          					$messagedesc->description = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->description);
          					$messagedesc->titre = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->titre);
          					$messagedesc->descriptiontext = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->descriptiontext);
          					$messagedesc->description = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->description);
          					$messagedesc->titre = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->titre);

          					$variables="<ul>";
          					$variablestext="";

          					foreach($this->valeurs as $key => $value) {
            						$messagedesc->descriptiontext = str_replace("__VARIABLE_".strtoupper($key)."__",$_POST[$key], $messagedesc->descriptiontext);
            						$messagedesc->description = str_replace("__VARIABLE_".strtoupper($key)."__",$_POST[$key], $messagedesc->description);
            						$messagedesc->titre = str_replace("__VARIABLE_".strtoupper($key)."__",$_POST[$key], $messagedesc->titre);
            						//Cf. __VARIABLES__ ci-après
            						if (isset($_POST[$key]) && $_POST[$key] != '') {
              							$variables.="<li><strong>".$key." : </strong> ".$_POST[$key]." </li>";
              							$variablestext.=$key."\n";
            						}
          					}
          					$variables.="</ul>";

          					$messagedesc->descriptiontext = str_replace("__VARIABLES__", $variablestext, $messagedesc->descriptiontext);
          					$messagedesc->description = str_replace("__VARIABLES__", $variables, $messagedesc->description);

          					if ($_POST['nom']=="" && $_POST['prenom']=="") $_POST['nom']="Nom Inconnu";
          					if ($_POST['ad_email']=="") $_POST['ad_email']=$emailcontact->valeur;

          					$from = $_POST['ad_email'];
          					$fromname = $_POST['nom']." ".$_POST['prenom'];

          					$mail = new Mail();
          					$mail->IsMail();
          					$mail->FromName = $nomsite->valeur;
          					$mail->From = $emailcontact->valeur;
          			  /*Ajout*/$mail->Sender = $emailcontact->valeur;
          			  /*Ajout*/$mail->Hostname = substr(strrchr($emailcontact->valeur,'@'),1);
          					$mail->addReplyTo($from,$fromname);
          					$mail->Subject = $messagedesc->titre.' ('.$fromname.')';
          					$mail->MsgHTML($messagedesc->description);
          					$mail->AltBody = $messagedesc->descriptiontext;
          					$mail->AddAddress($emailcontact->valeur,$nomsite->valeur);

          					$mail->Sender = $emailcontact->valeur;
          					$mail->Hostname = substr(strrchr($emailcontact->valeur,'@'),1);

          					//$mail->AddAddress('thierry@pixel-plurimedia.fr',$nomsite->valeur);

          					if (isset($_FILES['fichier1'])) {
            						$tempFile = $_FILES['fichier1']['tmp_name'];
            						if (is_uploaded_file($tempFile)) $mail->AddAttachment($tempFile, $_FILES['fichier1']['name']);
          					}

          					if (isset($_FILES['fichier2'])) {
            						$tempFile = $_FILES['fichier2']['tmp_name'];
            						if (is_uploaded_file($tempFile)) $mail->AddAttachment($tempFile, $_FILES['fichier2']['name']);
          					}

          					if ($mail->send()) $_POST['formulairecontact_ok']=true;
          					else $_POST['formulairecontact_erreur_envoi']=true;
        				}
                else {
          					$_POST['txt_securite']="";
          					$_POST['formulairecontact_erreur_code']=true;
        				}
            }
            else $_POST['formulairecontact_erreur_obligatoires']=true;
      	}
    }

  	function analyse()
    {
      	global $res;

      	if (isset($_POST['formulairecontact_ok'])) $res = preg_replace("`\#FORMULAIRECONTACT_ENVOI\[([^]]*)\]`", "\\1", $res);
      	else $res = preg_replace("`\#FORMULAIRECONTACT_ENVOI\[([^]]*)\]`", "", $res);

      	if (! function_exists('dsp_crypt')) {
          	$cryptinstall="lib/crypt/cryptographp.fct.php";
          	include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
      	}
      	$res = str_replace("#FORMULAIRECONTACT_ANTISPAM", dsp_crypt(0,1,0), $res);

      	if (isset($_POST['formulairecontact_erreur_code'])) $res = preg_replace("`\#FORMULAIRECONTACT_ERREUR_CODE\[([^]]*)\]`", "\\1", $res);
		    else $res = preg_replace("`\#FORMULAIRECONTACT_ERREUR_CODE\[([^]]*)\]`", "", $res);

      	if (isset($_POST['formulairecontact_erreur_envoi'])) $res = preg_replace("`\#FORMULAIRECONTACT_ERREUR_ENVOI\[([^]]*)\]`", "\\1", $res);
      	else $res = preg_replace("`\#FORMULAIRECONTACT_ERREUR_ENVOI\[([^]]*)\]`", "", $res);

      	if (isset($_POST['formulairecontact_erreur_obligatoires'])) $res = preg_replace("`\#FORMULAIRECONTACT_ERREUR_OBLIGATOIRES\[([^]]*)\]`", "\\1", $res);
      	else $res = preg_replace("`\#FORMULAIRECONTACT_ERREUR_OBLIGATOIRES\[([^]]*)\]`", "", $res);

      	if (isset($_POST['formulairecontact_erreur_email'])) $res = preg_replace("/\#FORMULAIRECONTACT_AD_EMAIL\[([^]]*)\]/","\\1",$res);

      	foreach($this->valeurs as $key => $value) {
			      // #VAR[xxxxx]
        	  if (isset($_POST['formulairecontact_erreur_obligatoires'])) $replace_erreur="\\1";
        	  else $replace_erreur="";

            $res = preg_replace(
    						"/\#FORMULAIRECONTACT_".strtoupper($key)."\[([^]]*)\]/",
    						$_POST[$key] == "" ? $replace_erreur : '',
    						$res
  			    );
          	// #VAR
          	$res = str_replace("#FORMULAIRECONTACT_".strtoupper($key),$_POST[$key], $res);
      	}
    }
}
?>
