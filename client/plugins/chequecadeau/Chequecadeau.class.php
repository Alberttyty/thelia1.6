<?php

include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Venteprod.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/divers.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Mail.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Pdf.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");

class Chequecadeau extends PluginsClassiques
{
		public $id;
		public $code;
		public $montant;
		public $commande;
		public $commande_utilise;
		public $utilise;
		public $date;
		public $dateutilisation;
		public $datemaj;
    public $ref;
    public $venteprod;

		public $table="chequecadeau";
		public $bddvars = array("id", "code", "montant", "commande", "commande_utilise", "utilise", "date", "dateutilisation", "datemaj", "ref", "venteprod");

		function Chequecadeau()
		{
				$this->PluginsClassiques("chequecadeau");
		}

		function charger($id = null, $lang=null)
		{
		  	if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

    function charger_commande($commande, $lang=1)
		{
		  	return $this->getVars("SELECT * FROM $this->table WHERE commande=\"$commande\" LIMIT 0,1");
		}

		function charger_code($code, $lang=1)
		{
		  	return $this->getVars("SELECT * FROM $this->table WHERE code=\"$code\" AND utilise=\"0\" LIMIT 0,1");
		}

		function init()
		{
			  $this->ajout_desc("Chèques Cadeaux", "Chèques Cadeaux", "", 1);
				$cnx = new Cnx();
				$query_chequecadeau = "CREATE TABLE `chequecadeau` (
					  `id` int(11) NOT NULL auto_increment,
					  `code` text NOT NULL,
					  `montant` float NOT NULL,
					  `commande` int(11) NOT NULL,
					  `commande_utilise` int(11) NOT NULL,
					  `utilise` tinyint NOT NULL,
					  `date` datetime,
					  `dateutilisation` datetime,
					  `datemaj` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		        `ref` text NOT NULL,
		        `venteprod` int(11) NOT NULL,
					  PRIMARY KEY  (`id`)
				) AUTO_INCREMENT=1 ;";
				$resul_chequecadeau = mysql_query($query_chequecadeau, $cnx->link);

				$rubrique = new Variable();
				if(! $rubrique->charger("chequecadeau_rubrique")) {
						$rubrique->nom = "chequecadeau_rubrique";
						$rubrique->valeur = "0";
						$rubrique->add();
				}

				$test = new Message();
				if(! $test->charger("chequecadeau")) {
						$message = new Message();
						$message->nom = "chequecadeau";
						$lastid = $message->add();

						$messagedesc = new Messagedesc();
						$messagedesc->message = $lastid;
						$messagedesc->lang = 1;
						$messagedesc->titre = "Chèque Cadeau";
						$messagedesc->description = "Votre chèque cadeau";
						$messagedesc->add();
				}

	      $cache_dir=SITE_DIR.'client/cache/chequecadeau';
	      if (is_dir($cache_dir)) mkdir($cache_dir,0755,true);
		}

		function destroy() {}

		function boucle($texte, $args)
		{
    		$commande = lireTag($args,"commande");
				$search ="";
				$res="";

				// préparation de la requête
				if ($commande!="")  $search.=" and commande_utilise=\"$commande\"";

				$chequecadeau = new Chequecadeau();
				$query_chequecadeau = "select * from $chequecadeau->table where 1 $search";

				$resul_chequecadeau = $this->query($query_chequecadeau);

				if ($resul_chequecadeau) {
						$nbres = $this->num_rows($resul_chequecadeau);

						if ($nbres > 0) {
								while($row = $this->fetch_object($resul_chequecadeau)) {
				            $temp = $texte;
				            $temp = str_replace("#ID", $row->id, $temp);
				            $temp = str_replace("#CODE", $row->code, $temp);
										$temp = str_replace("#MONTANT", $row->montant, $temp);
										$temp = str_replace("#COMMANDE", $row->commande, $temp);
										$temp = str_replace("#REF", $row->ref, $temp);
										$temp = str_replace("#VALEUR", $row->valeur, $temp);

										$res .= $temp;
								}
						}
				}

				return $res;
		}

		function verifierDate()
		{
			  $madate=date("U");
	      $dans1an=strtotime($this->date);
	      $dans1an=$dans1an+(365*24*60*60);

	      if($madate<=$dans1an)return true;
	      else return false;
    }

		function action()
		{
			  global $reset;

			  if($reset) unset($_SESSION['chequecadeau']);
			  if(!isset($_SESSION['chequecadeau'])) $_SESSION['chequecadeau'] = [];

			  if($_REQUEST['action'] == "chequecadeau") {

		  		  if($_REQUEST['cheque'] != "") {
			    		  $code_md5=md5($_REQUEST['cheque']);
			    		  $this->charger_code($code_md5);

			    		  if($this->id != 0) {
			    		  		if($this->verifierDate()) {
						    		    $port = port();
								        $total = $_SESSION['navig']->panier->total()+$port;

								        if($total<$this->montant && $_REQUEST['force'] != 1) $_POST['chequecadeau_montant_inferieur'] = true;
						            else {
							              $this->utilise=1;
							              $this->dateutilisation=date('Y-m-d H:i:s');
							              $this->maj();
							              array_push($_SESSION['chequecadeau'],array('id'=>$this->id,'montant'=>$this->montant,'ref'=>$this->ref));
						            }
										} else $_POST['chequecadeau_erreur_date'] = true;

			          } else $_POST['chequecadeau_erreur_code']=true;
		  		  }
			  }
		}

		function aprescommande($commande)
		{
				$totalcheque=0;

				foreach ($_SESSION['chequecadeau'] as $k => $v){
			      $totalcheque=$totalcheque+$v['montant'];
			      $this->charger($v['id']);
			      $this->commande_utilise=$commande->id;
			      $this->maj();
			      /*$venteprod=new Venteprod();
			      $venteprod->ref=$v['ref'];
			      $venteprod->titre="Utilisation du chèque cadeau N°".$v['id']." d'un montant de ".$v['montant']."€";
			      $venteprod->chapo="";
			      $venteprod->description="";
			      $venteprod->quantite=1;
			      $venteprod->prixu=0;
			      $venteprod->tva=0;
			      $venteprod->commande=$commande->id;
			      $venteprod->add();*/
		    }

		    $totalavant=$_SESSION['navig']->commande->total;
		    $_SESSION['navig']->commande->total = $_SESSION['navig']->commande->total-$totalcheque;

		    if($_SESSION['navig']->commande->total<0) {
			      $_SESSION['navig']->commande->total = 0;
			      $_SESSION['navig']->commande->remise=$totalavant;

		    } else $_SESSION['navig']->commande->remise = $_SESSION['navig']->commande->remise+$totalcheque;

		    $commande->total=$_SESSION['navig']->commande->total;
		    $commande->remise=$_SESSION['navig']->commande->remise;
		    $commande->maj();
    }

		function analyse()
		{
      	global $res;

    		if($_POST['chequecadeau_erreur_date']) $res = preg_replace("`\#CHEQUECADEAU_ERREUR_DATE\[([^]]*)\]`", "\\1", $res);
    		else $res = preg_replace("`\#CHEQUECADEAU_ERREUR_DATE\[([^]]*)\]`", "", $res);

        if($_POST['chequecadeau_erreur_code']) $res = preg_replace("`\#CHEQUECADEAU_ERREUR_CODE\[([^]]*)\]`", "\\1", $res);
    		else $res = preg_replace("`\#CHEQUECADEAU_ERREUR_CODE\[([^]]*)\]`", "", $res);

        if($_POST['chequecadeau_montant_inferieur']) {
	    		  $res = preg_replace("`\#CHEQUECADEAU_MONTANT_INFERIEUR\[([^]]*)\]`", "\\1", $res);
	    		  $res = str_replace("#CHEQUECADEAU_CODE", $_REQUEST['cheque'], $res);

    		} else {
	          $res = preg_replace("`\#CHEQUECADEAU_MONTANT_INFERIEUR\[([^]]*)\]`", "", $res);
	    		  $res = str_replace("#CHEQUECADEAU_CODE", "", $res);
        }

        $total=0;
    		foreach ($_SESSION['chequecadeau'] as $k => $v){
          	$total=$total+$v['montant'];
        }
        $res = str_replace("#CHEQUECADEAU_REMISE", $total, $res);
    }

		function statut($commande)
		{
				if($commande->statut == 2) {
					  //si pas encore de chèque émis pour cette commande
					  $this->charger_commande($commande->id);
					  if ($this->id == "") {
				        $venteprod = new Venteprod();
								$produit = new Produit();

								$query = 'SELECT * FROM '.$venteprod->table.' WHERE commande = '.$commande->id;
								$resul = mysql_query($query, $venteprod->link);

								$rubrique = new Variable();
								$rubrique->charger("chequecadeau_rubrique");

								while($row = mysql_fetch_object($resul)) {
										if($produit->charger($row->ref)) {
										    if($produit->rubrique == $rubrique->valeur) {
											      for ($i=0; $i < $row->quantite; $i++) {
													      $this->montant=$row->prixu;
									              $this->commande=$commande->id;
									              $this->genCode();

									              if ($this->code != "") {
											              $code=$this->code;
											              $this->code=md5($this->code);
											              $this->date=date('Y-m-d H:i:s');
											              $this->ref=$row->ref;
											              $this->venteprod=$row->id;
											              $this->id=$this->add();
											              $this->mail($row->titre);
									              }
														}
										    }
										}
								}
						}
				}
		}

    function renvoyer()
		{
	      $commande = new Commande();
	      $venteprod = new Venteprod();
	      if($commande->charger($this->commande)) {
		        $query = 'SELECT * FROM '.$venteprod->table.' WHERE id = '.$this->venteprod;
		        $resul = mysql_query($query, $venteprod->link);
		        while($row = mysql_fetch_object($resul)) {
			          $this->genCode();
			          $code=$this->code;
			          $this->code=md5($this->code);
			          $this->maj();
			          $this->mail($row->titre);
		        }
	      }
    }

		function genCode()
		{
		    $code=genpass(12);
		    if ($code != "") $this->code=$code;
    }

    function genPdf($commande, $code, $montant, $id, $mode='fichier', $perso='', $type='cheque')
		{
	      $pour="";
	      if(strpos($perso, "pour : ")) {
			      $debut = strpos($perso, "pour : ") + strlen("pour : ");
			      $fin = strpos($perso, "de : ");
			      $pour = substr($perso, $debut, $fin - $debut );
	      }
				else if(strpos($perso, "Personnalisation : ")) {
						$debut = strpos($perso, "Personnalisation : " ) + strlen("Personnalisation : ");
						if(strpos($perso, "de : ")) $fin = strpos($perso, "de : ");
						else if(strpos($perso, " - ", $debut)) $fin = strpos($perso, "-", $debut);
						else $fin = strlen($perso);
						$pour = substr($perso, $debut, $fin - $debut);
				}
				$pour = trim($pour," - ");

	      $de="";
	      if(strpos($perso, "de : " )) {
			      $debut = strpos($perso, "de : " ) + strlen( "de : " );
			      $fin = strpos($perso, "de : " );
			      $de = substr($perso, $debut);

	      } else if(strpos($perso, "Personnalisation : ")) {
						$debut = $fin;
						$fin = strlen($perso);
						$de = substr($perso, $debut);
				}
				$de = trim($de," - ");

	      if($type=='bon') $cheque = file_get_contents(SITE_DIR.'/client/plugins/chequecadeau/bon-'.$montant.'.html');
	      else $cheque = file_get_contents(SITE_DIR.'/client/plugins/chequecadeau/cheque-'.$montant.'.html');

	      $cheque = str_replace("__CHEMIN__",SITE_DIR.'/client/plugins/chequecadeau/', $cheque);
	      $cheque = str_replace("__CODE__", $code, $cheque);
	      $cheque = str_replace("__ID__", $id, $cheque);
	      $cheque = str_replace("__POUR__", $pour, $cheque);
	      $cheque = str_replace("__DE__", $de, $cheque);
	      $cheque = str_replace("__DATE__", date("d/m/y"), $cheque);
				$cheque = str_replace("__HEURE__", date("H:i:s"), $cheque);

				if($commande->ref != -1) $cheque = str_replace("__COMMANDE__", $commande->ref, $cheque);
	      else $cheque = str_replace("__COMMANDE__", "réalisée en magasin", $cheque);

	      $html2pdf = new HTML2PDF('P','A4','fr');
	      $html2pdf->WriteHTML($cheque);
	      if ($mode=='fichier') {
			      $pdf=SITE_DIR.'/client/cache/chequecadeau/cheque_'.$id.'.pdf';
			      $html2pdf->Output($pdf, 'F');
			      return $pdf;
	      }

	      if ($mode=='affichage') {
			      $html2pdf->Output('cheque_'.$id.'.pdf');
			      return true;
	      }
    }

    function mail($perso='')
		{
				$commande = new Commande();
				$commande->charger($this->commande);

        $pdf=$this->genPdf($commande,$this->code,$this->montant,$this->id,'fichier',$perso);

				$message = new Message();
				$message->charger("chequecadeau");

				$messagedesc = new Messagedesc();
				$messagedesc->charger($message->id);

				$client = new Client();
				$client->charger_id($commande->client);

				if($client->raison == "1") $raison = "Madame";
				else if($client->raison == "2") $raison = "Mademoiselle";
				else if($client->raison == "3") $raison = "Monsieur";

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
				$corps=$messagedesc->description;

				$messagedesc->descriptiontext = str_replace("__RAISON__", "$raison", $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__NOM__", $client->nom, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__PRENOM__", $client->prenom, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__COMMANDE__", $commande->ref, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__DATE__", $jour . "/" . $mois . "/" . $annee, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__HEURE__", $heure . ":" . $minute . ":" . $seconde, $messagedesc->descriptiontext);
				$messagedesc->descriptiontext = str_replace("__DATEDJ__", date("d") . "/" . date("m") . "/" . date("Y"), $messagedesc->descriptiontext);
				$corpstext=$messagedesc->descriptiontext;

				$messagedesc->titre = str_replace("__COMMANDE__", $commande->ref, $messagedesc->titre);
        $sujet=$messagedesc->titre;

				//envoi du mail
    		$mail = new Mail();
    		$mail->IsMail();
    		$mail->FromName = $nomsite->valeur;
    		$mail->From = $emailcontact->valeur;
        $mail->Sender = $emailcontact->valeur;
        $mail->Hostname = substr(strrchr($emailcontact->valeur,'@'),1);
    		$mail->Subject = $sujet;
    		$mail->MsgHTML($corps);
    		$mail->AltBody = $corpstext;
    		$mail->AddAddress($client->email);
    		$mail->AddAttachment($pdf, 'cheque_'.$this->id.'.pdf');
    		$mail->send();
    		unlink($pdf);
		}
}

?>
