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
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produitdesc.class.php");

class Formulairepartage extends PluginsClassiques
{
    public $valeurs=[
      "nom"  =>"",
      "prenom"  =>"",
      "ad_email"  =>"",
      "dest_nom"  =>"",
      "dest_prenom"  =>"",
      "dest_ad_email"  =>"",
      "objet"  =>"",
      "message"  =>"",
      "txt_securite"  =>""
		];

		function Formulairepartage()
		{
				$this->PluginsClassiques("Formulairepartage");
		}


		function init()
		{
			  $this->ajout_desc("Formulairepartage", "Formulairepartage", "", 1);

				$test = new Message();
				if(! $test->charger("formulairepartage")){
						$message = new Message();
						$message->nom = "formulairepartage";
						$lastid = $message->add();
						$messagedesc = new Messagedesc();
						$messagedesc->message = $lastid;
						$messagedesc->lang = 1;
						$messagedesc->intitule = "Mail du formulaire de partage";
				    $messagedesc->titre = "Message du site internet";
						$messagedesc->description = "Message... __VARIABLES__";
						$messagedesc->descriptiontext = "Message... __VARIABLES__";
						$messagedesc->add();
				}

	      $cryptograph_largeur = new Variable();
				if(!$cryptograph_largeur->charger("cryptograph_largeur")){
						$cryptograph_largeur->nom = "cryptograph_largeur";
						$cryptograph_largeur->valeur = 100;
						$cryptograph_largeur->add();
				}

	      $cryptograph_hauteur = new Variable();
				if(!$cryptograph_hauteur->charger("cryptograph_hauteur")){
						$cryptograph_hauteur->nom = "cryptograph_hauteur";
						$cryptograph_hauteur->valeur = 30;
						$cryptograph_hauteur->add();
				}

	      $cryptograph_bg = new Variable();
				if(!$cryptograph_bg->charger("cryptograph_bg")){
						$cryptograph_bg->nom = "cryptograph_bg";
						$cryptograph_bg->valeur = "#000000";
						$cryptograph_bg->add();
				}

	      $cryptograph_char = new Variable();
				if(!$cryptograph_char->charger("cryptograph_char")){
						$cryptograph_char->nom = "cryptograph_char";
						$cryptograph_char->valeur = "#FFFFFF";
						$cryptograph_char->add();
				}

	      $obligatoires = new Variable();
				if(!$obligatoires->charger("partage_obligatoires")){
						$obligatoires->nom = "partage_obligatoires";
						$obligatoires->valeur = "nom,ad_email,dest_nom,dest_ad_email,txt_securite";
						$obligatoires->add();
				}
		}

		function action()
		{
				if (isset($_POST['action']) && $_POST['action'] == "formulairepartage") {
						if(!function_exists('dsp_crypt')) {
				        $cryptinstall="lib/crypt/cryptographp.fct.php";
				        include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
			      }

			      foreach($this->valeurs as $key => $value) {
						  	$_POST[$key]=strip_tags($_POST[$key]);
			      }

			      $champs_requis=true;

			      $obligatoires = new Variable();
			      if ($obligatoires->charger("contact_obligatoires")) {
				        $obligatoires=explode(',',$obligatoires->valeur);

				        foreach ($obligatoires as $key => $value){
					          if($_POST[$value]=="") $champs_requis=false;

					          if($value=='ad_email'||$value=='dest_ad_email') {
						            if(!filter_var($_POST[$value],FILTER_VALIDATE_EMAIL)) {
							              $champs_requis=false;
							              $_POST['formulairepartage_erreur_email']=true;
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
					  				$message->charger("formulairepartage");

					  				$messagedesc = new Messagedesc();
					  				$messagedesc->charger($message->id);

					  				$messagedesc->descriptiontext = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->descriptiontext);
					  				$messagedesc->description = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->description);
					          $messagedesc->titre = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->titre);
					  				$messagedesc->descriptiontext = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->descriptiontext);
					  				$messagedesc->description = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->description);
					          $messagedesc->titre = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->titre);

					          global $ref,$id_produit;

					          $tproduit = new Produit();
					      		$tproduitdesc = new Produitdesc();
					      		$url = "";

					      		if ($ref) $tproduit->charger($ref);
					      		else if ($id_produit) $tproduit->charger_id($id_produit);

					      		if ($ref || $id_produit) {
						      			$tproduitdesc->charger($tproduit->id);
						      			$url = $tproduitdesc->getUrl();
					      		}

					          $messagedesc->descriptiontext = str_replace("__NOMPAGE__", $tproduitdesc->titre, $messagedesc->descriptiontext);
					  				$messagedesc->description = str_replace("__NOMPAGE__", $tproduitdesc->titre, $messagedesc->description);
					          $messagedesc->titre = str_replace("__NOMPAGE__", $tproduitdesc->titre, $messagedesc->titre);
					          $messagedesc->descriptiontext = str_replace("__URLPAGE__", $url, $messagedesc->descriptiontext);
					  				$messagedesc->description = str_replace("__URLPAGE__", $url, $messagedesc->description);
					          $messagedesc->titre = str_replace("__URLPAGE__", $url, $messagedesc->titre);

					  				$variables="<ul>";
					  				$variablestext="";

					  				foreach($this->valeurs as $key => $value) {
						            $messagedesc->descriptiontext = str_replace("__VARIABLE_".strtoupper($key)."__",$_POST[$key], $messagedesc->descriptiontext);
						            $messagedesc->description = str_replace("__VARIABLE_".strtoupper($key)."__",$_POST[$key], $messagedesc->description);
						            $messagedesc->titre = str_replace("__VARIABLE_".strtoupper($key)."__",$_POST[$key], $messagedesc->titre);

						            if (isset($_POST[$key])) {
							              $variables.="<li><strong>".$key." : </strong> ".$_POST[$key]." </li>";
							              $variablestext.=$key."\n";
						            }
					          }
					          $variables.="</ul>";

					          $messagedesc->descriptiontext = str_replace("__VARIABLES__", $variablestext, $messagedesc->descriptiontext);
					  				$messagedesc->description = str_replace("__VARIABLES__", $variables, $messagedesc->description);

					  				if ($_POST['nom']==""&&$_POST['prenom']==""){$_POST['nom']="Nom Inconnu";}
					          if ($_POST['ad_email']==""){$_POST['ad_email']=$emailcontact->valeur;}
					          $from=$_POST['ad_email'];
					          $fromname=$_POST['nom']." ".$_POST['prenom'];

					          if ($_POST['dest_nom']==""&&$_POST['dest_prenom']==""){$_POST['dest_nom']="Nom Inconnu";}
					          $dest=$_POST['dest_ad_email'];
					          $destname=$_POST['dest_nom']." ".$_POST['dest_prenom'];

					  				$mail = new Mail();
					  				$mail->IsMail();
					  				$mail->FromName = $nomsite->valeur;
					  				$mail->From = $emailcontact->valeur;
					          /*Ajout*/$mail->Sender = $emailcontact->valeur;
					          /*Ajout*/$mail->Hostname = substr(strrchr($emailcontact->valeur,'@'),1);
					          $mail->addReplyTo($from,$fromname);
					  				$mail->Subject = $messagedesc->titre;
					  				$mail->MsgHTML($messagedesc->description);
					  				$mail->AltBody = $messagedesc->descriptiontext;
					  				$mail->AddAddress($dest,$destname);

					          $mail->Sender = $emailcontact->valeur;
					          $mail->Hostname = substr(strrchr($emailcontact->valeur,'@'),1);

					  				if ($mail->send()) $_POST['formulairepartage_ok']=true;
					          else $_POST['formulairepartage_erreur_envoi']=true;
					  		}
					      else {
						        $_POST['txt_securite']="";
						        $_POST['formulairepartage_erreur_code']=true;
					      }
				    }
				    else $_POST['formulairepartage_erreur_obligatoires']=true;
				}
		}

		function analyse()
		{
	      global $res;

	      if (isset($_POST['formulairepartage_ok'])){
	      		$res = preg_replace("`\#FORMULAIREPARTAGE_ENVOI\[([^]]*)\]`", "\\1", $res);
	      }
	      else $res = preg_replace("`\#FORMULAIREPARTAGE_ENVOI\[([^]]*)\]`", "", $res);

	      if (!function_exists('dsp_crypt')) {
		        $cryptinstall="lib/crypt/cryptographp.fct.php";
		        include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
	      }
	      $res = str_replace("#FORMULAIREPARTAGE_ANTISPAM", dsp_crypt(0,1,0), $res);

	      if (isset($_POST['formulairepartage_erreur_code'])){
	  				$res = preg_replace("`\#FORMULAIREPARTAGE_ERREUR_CODE\[([^]]*)\]`", "\\1", $res);
	      }
	      else $res = preg_replace("`\#FORMULAIREPARTAGE_ERREUR_CODE\[([^]]*)\]`", "", $res);

	      if (isset($_POST['formulairepartage_erreur_envoi'])){
	  				$res = preg_replace("`\#FORMULAIREPARTAGE_ERREUR_ENVOI\[([^]]*)\]`", "\\1", $res);
	      }
	      else $res = preg_replace("`\#FORMULAIREPARTAGE_ERREUR_ENVOI\[([^]]*)\]`", "", $res);

	      if (isset($_POST['formulairepartage_erreur_obligatoires'])){
	  				$res = preg_replace("`\#FORMULAIREPARTAGE_ERREUR_OBLIGATOIRES\[([^]]*)\]`", "\\1", $res);
	      }
	      else $res = preg_replace("`\#FORMULAIREPARTAGE_ERREUR_OBLIGATOIRES\[([^]]*)\]`", "", $res);

	      if (isset($_POST['formulairepartage_erreur_email'])){
	      		$res = preg_replace("/\#FORMULAIREPARTAGE_AD_EMAIL\[([^]]*)\]/","\\1",$res);
	      }

	      foreach($this->valeurs as $key => $value) {
					  // #VAR[xxxxx]
		        if (isset($_POST['formulairepartage_erreur_obligatoires'])) $replace_erreur="\\1";
		        else $replace_erreur="";
		        $res = preg_replace(
		  						"/\#FORMULAIREPARTAGE_".strtoupper($key)."\[([^]]*)\]/",
		  						$_POST[$key] == "" ? $replace_erreur : '',
		  						$res
		  			);
		        // #VAR
		        $res = str_replace("#FORMULAIREPARTAGE_".strtoupper($key),$_POST[$key], $res);
	      }
    }
}
?>
