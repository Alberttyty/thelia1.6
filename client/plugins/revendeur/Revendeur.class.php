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
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Panier.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Client.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Mail.class.php");

class Revendeur extends PluginsClassiques
{
    public $id;
	  public $produit;
	  public $prixrevendeur;
    public $prix2revendeur;
    public $promorevendeur;

    public $table="revendeur";
  	public $bddvars = array("id", "produit", "prixrevendeur", "prix2revendeur", "promorevendeur");

  	function __construct()
    {
  		  parent::__construct("revendeur");
  	}

  	function init()
    {
    		$this->ajout_desc("Revendeur", "Revendeur", "", 1);
    		$cnx = new Cnx();
    		$query_revendeur = "CREATE TABLE `revendeur` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `produit` INT NOT NULL ,
            `prixrevendeur` FLOAT NOT NULL DEFAULT '0',
            `prix2revendeur` FLOAT NOT NULL DEFAULT '0',
            `promorevendeur` SMALLINT NOT NULL DEFAULT '0'
            )"
        ;
    		$resul_revendeur = mysql_query($query_revendeur, $cnx->link);

        $test = new Message();
    		if(! $test->charger("revendeurclient")) {
      			$message = new Message();
      			$message->nom = "revendeurclient";
      			$lastid = $message->add();

      			$messagedesc = new Messagedesc();
      			$messagedesc->message = $lastid;
      			$messagedesc->lang = 1;
      			$messagedesc->intitule = "Compte revendeur valide";
      		    $messagedesc->titre = "Compte revendeur valide";
      			$messagedesc->description = "Message...";
      			$messagedesc->descriptiontext = "Message...";
      			$messagedesc->add();
    		}
        if(! $test->charger("revendeuradmin")) {
      			$message = new Message();
      			$message->nom = "revendeuradmin";
      			$lastid = $message->add();

      			$messagedesc = new Messagedesc();
      			$messagedesc->message = $lastid;
      			$messagedesc->lang = 1;
      			$messagedesc->intitule = "Demande de compte revendeur";
    		    $messagedesc->titre = "Demande de compte revendeur";
      			$messagedesc->description = "Message...";
      			$messagedesc->descriptiontext = "Message...";
      			$messagedesc->add();
    		}
  	}

  	function charger($id = null, $var2 = null)
    {
  		  if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
  	}

  	function charger_produit($produit)
    {
  		  return $this->getVars("SELECT * FROM $this->table WHERE produit=\"$produit\"");
  	}

    function modprod($produit)
    {
      	if (isset($_REQUEST['prixrevendeur'])) {
      			$prixrevendeur=$_REQUEST['prixrevendeur'];
      			$prixrevendeur = str_replace(",", ".", $prixrevendeur);

      			if (isset($_REQUEST['prix2revendeur'])) $prix2revendeur=$_REQUEST['prix2revendeur'];
      			else $prix2revendeur=0;

      			$prix2revendeur = str_replace(",", ".", $prix2revendeur);

      			if (isset($_REQUEST['promorevendeur'])) $promorevendeur=1;
      			else $promorevendeur=0;

      			$this->produit='';
      			$this->charger_produit($produit->id);
      			$this->prixrevendeur=$prixrevendeur;
      			$this->prix2revendeur=$prix2revendeur;
      			$this->promorevendeur=$promorevendeur;

      			if ($this->produit!='') $this->maj();
      			else {
        			  $this->produit=$produit->id;
        			  $this->add();
      			}
      	}
    }

    function predemarrage()
    {
      	if(isset($_POST['creercompterevendeur'])) {
        	$_GET['creercompterevendeur'] = $_POST['creercompterevendeur'];
      	}
      	if(isset($_GET['creercompterevendeur'])) {
        	$_SESSION['creercompterevendeur'] = $_GET['creercompterevendeur'];
      	}
      	if(isset($_SESSION['creercompterevendeur'])) {
        	$_REQUEST['creercompterevendeur'] = $_SESSION['creercompterevendeur'];
      	}
    }

    function demarrage()
    {
      	if(!isset($_SESSION['navig']->panierrevendeur)) {
        	 $_SESSION['navig']->panierrevendeur=false;
      	}
    }

    function chargerPrixRevendeur($id,$ref)
    {
        $this->charger_produit($id);

        $prixrevendeurttc=0;

        if($this->prixrevendeur!=''&&$this->prixrevendeur!=0) {
      		  if($this->promorevendeur==1&&$this->prix2revendeur!=''&&$this->prix2revendeur!=0) $prixrevendeur=floatval($this->prix2revendeur);
      		  else $prixrevendeur=floatval($this->prixrevendeur);

      		  // ON AJOUTE LA TVA
      		  $obj_produit = new Produit();
      		  $obj_produit->charger($ref);
      		  $prixrevendeurttc=$prixrevendeur*(1+($obj_produit->tva/100));
        }

        return $prixrevendeurttc;
    }

    function action()
    {
		    global $reset;
      	if ($reset) $_SESSION['navig']->panierrevendeur = false;
	  }

    function ajouterPanier($indiceAjoute)
    {
      	if (is_int($indiceAjoute)) {
          	if ($_SESSION['navig']->client->type==1) {

    				    $nb = $_SESSION['navig']->panier->nbart;
              	$prixrevendeurttc = $this->chargerPrixRevendeur($_SESSION['navig']->panier->tabarticle[$nb-1]->produit->id,$_SESSION['navig']->panier->tabarticle[$nb-1]->produit->ref);

            		if ($prixrevendeurttc!=''&&$prixrevendeurttc!=0) {
                		$_SESSION['navig']->panier->tabarticle[$nb-1]->produit->prix = $prixrevendeurttc;
                		$_SESSION['navig']->panier->tabarticle[$nb-1]->produit->prix2 = $prixrevendeurttc;
                		$_SESSION['navig']->panierrevendeur=true;
            		}
            }
      	}
    }

    function apresconnexion($client)
    {
      	if($client->type==1 && !($_SESSION['navig']->panierrevendeur)) {
        	 //$_SESSION["navig"]->panier = new Panier();
      	}
      	if($client->type==0 && $_SESSION['navig']->panierrevendeur){
        	 $_SESSION["navig"]->panier = new Panier();
      	}
    }

    function apresdeconnexion()
    {
      	$_SESSION["navig"]->panier = new Panier();
      	$_SESSION['navig']->panierrevendeur = false;
    }

    function apresclient($client)
    {
      	if(isset($_POST['creercompterevendeur'])) {
          	if($_POST['creercompterevendeur'] == "oui") {
            		$emailcontact = new Variable();
        				$emailcontact->charger("emailcontact");
        				$nomsite = new Variable();
        				$nomsite->charger("nomsite");
        				$urlsite = new Variable();
        				$urlsite->charger("urlsite");
        				$message = new Message();
        				$message->charger("revendeuradmin");
            		$messagedesc = new Messagedesc();
    				    $messagedesc->charger($message->id);

            		$messagedesc->descriptiontext = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->descriptiontext);
        				$messagedesc->description = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->description);
        				$messagedesc->descriptiontext = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->descriptiontext);
        				$messagedesc->description = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->description);

            		foreach ($client->bddvars as $key => $value) {
              			$messagedesc->descriptiontext = str_replace("__".strtoupper($value)."__", $client->$value, $messagedesc->descriptiontext);
    				  	    $messagedesc->description = str_replace("__".strtoupper($value)."__", $client->$value, $messagedesc->description);
            		}

            		$messagedesc->descriptiontext = str_replace("__MESSAGE__", strip_tags($_POST['message']), $messagedesc->descriptiontext);
    				    $messagedesc->description = str_replace("__MESSAGE__", strip_tags($_POST['message']), $messagedesc->description);

        				$from=$client->email;
        				$fromname=$client->nom." ".$client->prenom;

        				$mail = new Mail();
        				$mail->IsMail();
        				$mail->FromName = $fromname;
        				$mail->From = $from;
                /*Ajout*/$mail->Sender = $from;
                /*Ajout*/$mail->Hostname = substr(strrchr($from,'@'),1);
        				$mail->Subject = $messagedesc->titre;
        				$mail->MsgHTML($messagedesc->description);
        				$mail->AltBody = $messagedesc->descriptiontext;
        				$mail->AddAddress($emailcontact->valeur,$nomsite->valeur);

        				if(isset($_FILES['fichier1'])) {
        				  	$tempFile = $_FILES['fichier1']['tmp_name'];
                  	if (is_uploaded_file($tempFile)) {
                    		$mail->AddAttachment($tempFile, $_FILES['fichier1']['name']);
                    }
                }

           			if (isset($_FILES['fichier2'])) {
                    $tempFile = $_FILES['fichier2']['tmp_name'];
              			if (is_uploaded_file($tempFile)) {
                  			$mail->AddAttachment($tempFile, $_FILES['fichier2']['name']);
                		}
            		}

    				    if ($mail->send()) {
              			redirige(urlfond("revendeur"));
              			exit();
            		}
                else exit("Erreur lors de l'envoi du mail de confirmation.");
          	}
      	}
    }

    function modcli($client)
    {
      	if ($client->type==1) {
            $emailcontact = new Variable();
      			$emailcontact->charger("emailcontact");
      			$nomsite = new Variable();
      			$nomsite->charger("nomsite");
      			$urlsite = new Variable();
      			$urlsite->charger("urlsite");
            $message = new Message();
            $message->charger("revendeurclient");

            $messagedesc = new Messagedesc();
            $messagedesc->charger($message->id);

            $messagedesc->descriptiontext = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->descriptiontext);
      			$messagedesc->description = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->description);
      			$messagedesc->descriptiontext = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->descriptiontext);
      			$messagedesc->description = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->description);

            $mail = new Mail();
      			$mail->IsMail();
      			$mail->FromName = $nomsite->valeur;
      			$mail->From = $emailcontact->valeur;
        	  /*Ajout*/$mail->Sender = $emailcontact->valeur;
        	  /*Ajout*/$mail->Hostname = substr(strrchr($emailcontact->valeur,'@'),1);
      			$mail->Subject = $messagedesc->titre;
      			$mail->MsgHTML($messagedesc->description);
      			$mail->AltBody = $messagedesc->descriptiontext;
      			$mail->AddAddress($client->email,$client->prenom." ".$client->nom);

      			$mail->send();
        }
    }


    function boucle($texte, $args)
    {
    		// récupération des arguments
    		$produit = lireTag($args, "produit");
    		$search ="";
    		$res="";
      	//echo $_SESSION['navig']->client->type;
      	if($_SESSION['navig']->client->type==1) {
      			// préparation de la requête
      			if ($produit!="")  $search.=" and produit=\"$produit\"";

      			$revendeur = new Revendeur();

      			$query_revendeur = "select * from $revendeur->table where 1 $search";
      			$resul_revendeur = $this->query($query_revendeur);

      			if ($resul_revendeur) {
        				$nbres = $this->num_rows($resul_revendeur);
        				if ($nbres > 0) {
          					while( $row = $this->fetch_object($resul_revendeur)) {
            						if ($row->prixrevendeur!=0 && $row->prixrevendeur!= '') {
              							if ($row->promorevendeur == '1') $pourcentage = round((100 * ($row->prixrevendeur - $row->prix2revendeur) / $row->prixrevendeur), 0);
              							else $pourcentage = null;

              							$temp = $texte;
              							$temp = str_replace("#ID", $row->id, $temp);
              							$temp = str_replace("#PRODUIT", $row->produit, $temp);
              							$temp = str_replace("#PRIXREVENDEUR", formatter_somme($row->prixrevendeur), $temp);
              							$temp = str_replace("#PRIX2REVENDEUR", formatter_somme($row->prix2revendeur), $temp);
              							$temp = str_replace("#PROMOREVENDEUR", $row->promorevendeur, $temp);
              							$temp = str_replace("#POURCENTAGEREVENDEUR", $pourcentage, $temp);
              							$res .= $temp;
            						}
                    }
        				}
      			}
    		}

        return $res;
    }
}

?>
