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
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Client.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Venteprod.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Mail.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");


	class Remisefidelite extends PluginsClassiques{

	 var $id;
	 var $id_commande;
	 var $id_client;
   var $remise=0;
   var $date;
	 var $table="remisefidelite";
	 var $bddvars = array("id", "id_client", "id_commande", "remise", "date");

	 var $indexremise=5;
   var $pourcentageremise=5;
   var $dateremise;

		function Remisefidelite(){

			$this->PluginsClassiques();

		}

		function init(){
			$cnx = new Cnx();
			$query = "CREATE TABLE IF NOT EXISTS `remisefidelite` (
			  `id` int(11) NOT NULL auto_increment,
			  `id_client` int(11) NOT NULL,
			  `id_commande` int(11) NOT NULL,
			  `remise` FLOAT NOT NULL,
			  `date` DATETIME NOT NULL,
			  PRIMARY KEY  (`id`)
			) AUTO_INCREMENT=1 ;";
			$resul = mysql_query($query, $cnx->link);

			$message = new Message();
  		if($message->charger("remisefidelite")) {}
  		else{
  		$message->nom = "remisefidelite";
  		$lastid = $message->add();
  		$messagedesc = new Messagedesc();
  		$messagedesc->message = $lastid;
  		$messagedesc->lang = 1;
  		$messagedesc->intitule = "Remise Fidelite";
  		$messagedesc->titre = "Remise Fidelite";
  		$messagedesc->chapo = "";
  		$messagedesc->description = "Bonjour __REMISEFIDELITE_PRENOM__ __REMISEFIDELITE_NOM__ __REMISEFIDELITE_ENTREPRISE__, Vous avez passé votre __REMISEFIDELITE_NBCOMMANDE__e commande. Pour votre prochaine commande vous bénéficierez automatiquement d'une remise de __REMISEFIDELITE_REMISE__ Euro.";
  		$messagedesc->descriptiontext = "Bonjour __REMISEFIDELITE_PRENOM__ __REMISEFIDELITE_NOM__ __REMISEFIDELITE_ENTREPRISE__, Vous avez passé votre __REMISEFIDELITE_NBCOMMANDE__e commande. Pour votre prochaine commande vous bénéficierez automatiquement d'une remise de __REMISEFIDELITE_REMISE__ Euro.";
  		$messagedesc->add();
  		}

		}

		function charger_remisedispo(){
		  $id_client=addslashes($this->id_client);
			$this->getVars("select * from $this->table where id_client=$id_client and id_commande=\"0\" order by id,date limit 1");
    }

    function boucle($texte, $args){

    // récupération des arguments
		$id_client = lireTag($args, "client", "int");
    $id_commande = lireTag($args, "commande", "int");
    $utilise = lireTag($args, "utilise", "int");

		$search="";
		$res="";

		// preparation de la requete
		if($id_client!="")  $search.=" and id_client=\"$id_client\"";
		if($id_commande!="")  $search.=" and id_commande=\"$id_commande\"";
		if($utilise=="1")  $search.=" and id_commande!=\"0\"";
		if($utilise=="0")  $search.=" and id_commande=\"0\"";

		$order = "order by date desc";

		$query = "select * from $this->table where 1 $search $order";

		$resul = CacheBase::getCache()->mysql_query($query, $this->link);
		if($resul=="" || count($resul)==0) return "";

		foreach($resul as $row) {

				$temp = str_replace("#ID_CLIENT", "$row->id_client", $texte);
				$temp = str_replace("#ID_COMMANDE", "$row->id_commande", $temp);
				$temp = str_replace("#REMISE", number_format($row->remise,2,'.',''), $temp);

			$res .= $temp;

		}

		return $res;

    }

		function calculremise(){

      $index=$this->calculindex();

      $modulo=$index % $this->indexremise;
      if($modulo==0&&$index!=0)$index=$this->indexremise;

      //Xe commande depuis la dernière remise ou le debut
      if($index==$this->indexremise){

        $total_client = $this->calcultotal();

        $pourcentage=$this->pourcentageremise/100;
        $this->remise=$total_client*$pourcentage;
        $this->remise=round($this->remise, 2);

      }

      else {
        $this->remise=0;
      }

    }

    function calcultotal(){

      $total_client=0;
      $commande = new Commande();
      $venteprod = new Venteprod();
      $id_client=$this->id_client;

      //si jamais eu de remise
      if($this->dateremise==""){
        $query = "select * from $commande->table where client=$id_client and statut>1 and statut<5 order by date desc limit 0,".$this->indexremise;
      }
      //si deja eu une remise
      else {
        $query = "select * from $commande->table where client=$id_client and statut>1 and statut<5 and date>\"$this->dateremise\" order by date desc limit 0,".$this->indexremise;
      }

      $resul = mysql_query($query, $commande->link);
      while($row = mysql_fetch_object($resul)){

      		$statutdesc = new Statutdesc();
      		$statutdesc->charger($row->statut);

      		$query2 = "SELECT sum(prixu*quantite) as total FROM $venteprod->table where commande='$row->id'";
      		$resul2 = mysql_query($query2, $venteprod->link);
      		$total = round(mysql_result($resul2, 0, "total"), 2);

      		$port = $row->port;
      		$total -= $row->remise;
      		$total += $port;
      		if($total<0) $total = 0;
      		$total_client=$total_client+$total;
      	}

      	return $total_client;

    }

    function datederniereremise(){
      $id_client=$this->id_client;

	  $commande = new Commande();

	  if($id_client=="")
	  {
	  if(est_autorise("acces_commandes")){
	  $ref=lireParam("ref");
	  $commande->charger_ref($ref);
	  $id_client=$commande->client;
	  }
	  }

      $query = "select $commande->table.date from $commande->table,$this->table where $commande->table.client=$id_client and $commande->table.id=$this->table.id_commande order by $commande->table.date DESC limit 1";
      $resul = mysql_query($query, $commande->link);
			$row = mysql_fetch_assoc($resul);
			$this->dateremise=$row['date'];
    }

    function calculindex(){

			$this->datederniereremise();

			$id_client=$this->id_client;
			$commande = new Commande();

			if($id_client=="") {
				  if(est_autorise("acces_commandes")) {
						  $ref=lireParam("ref");
						  $commande->charger_ref($ref);
						  $id_client=$commande->client;
				  }
			}

			//si jamais eu de remise
			if($this->dateremise==""){
      $query = "select * from $commande->table where statut>1 and statut<5 and client=$id_client";
			$resul = mysql_query($query, $commande->link);
			$index = mysql_num_rows($resul);
      }
      //si deja eu une remise
      else{
      $query = "select * from $commande->table where statut>1 and statut<5 and client=$id_client and date>\"$this->dateremise\"";
			$resul = mysql_query($query, $commande->link);
			$index = mysql_num_rows($resul);
      }

      return $index;
    }

		function aprescommande($commande){

    $this->remise=0;

    $this->id_client=$commande->client;

    $this->charger_remisedispo();

    //si une remise est utilisable
    if($this->remise!=0||$this->remise!=""){
      $commande->total = $commande->total-$this->remise;
      $commande->remise = $commande->remise+$this->remise;
      if($commande->total<0) {
	      $commande->remise = $commande->remise + $commande->total;
	      $commande->total = 0;
      }
      $commande->maj();
      $this->id_commande=$commande->id;
      $this->maj();
    }

    }

    function statut($commande){

    if($commande->statut==2){

      $this->remise=0;

      $this->id_client=$commande->client;

      $this->charger_remisedispo();

      //si pas de remise active
    	if($this->remise==0||$this->remise==""){

        $this->calculremise();

        //et si une remise est a enregistrer
    		if($this->remise!=0||$this->remise!=""){
          $this->id_commande="0";
          $this->date=date("Y-m-d H:i:s");
          $this->add();
          $this->sendEmail($commande);
        }

      }
    }

    }

    function sendEmail($commande){

  	$emailfrom = new Variable("emailfrom");
  	$nomsite = new Variable("nomsite");
    $client = new Client($commande->client);

		$message = new Message();
		$message->charger("remisefidelite");
		$messagedesc = new Messagedesc();
		$messagedesc->charger($message->id);

    $body = $this->substitutionEmail($messagedesc->description);
		$altbody = $this->substitutionEmail($messagedesc->descriptiontext);
    $titre = $this->substitutionEmail($messagedesc->titre);

    //envoi du mail a l'admin
    $mail = new Mail();
    $mail->IsMail();
    $mail->From = $emailfrom->valeur;
    $mail->FromName = $nomsite->valeur;
    /*Ajout*/$mail->Sender = $emailfrom->valeur;
    /*Ajout*/$mail->Hostname = substr(strrchr($emailfrom->valeur,'@'),1);
    $mail->Subject  = $titre;
    $mail->MsgHTML($body);
    $mail->AltBody = $altbody;
    $mail->AddAddress($client->email);
    $mail->AddReplyTo($emailfrom->valeur,$nomsite->valeur);
    $retour=$mail->Send();
    return $retour;

    }

    function substitutionEmail($texte){

      $client = new Client();
      $client->charger_id($this->id_client);

	  $var = new Variable();
	  $var->charger("urlsite");
	  $urlsite=$var->valeur;
	  $var->charger("nomsite");
	  $nomsite=$var->valeur;
    $this->remise=number_format(floatval($this->remise),2,","," ");

    $texte = str_replace("__REMISEFIDELITE_NBCOMMANDE__", $client->nbcommandes(), $texte);
    $texte = str_replace("__REMISEFIDELITE_REMISE__", $this->remise, $texte);
    $texte = str_replace("__REMISEFIDELITE_NOM__", $client->nom, $texte);
    $texte = str_replace("__REMISEFIDELITE_PRENOM__", $client->prenom, $texte);
    $texte = str_replace("__REMISEFIDELITE_ENTREPRISE__", $client->entreprise, $texte);
	  $texte = str_replace("__URLSITE__", $urlsite, $texte);
	  $texte = str_replace("__NOMSITE__", $nomsite, $texte);

      return $texte;

    }

		function analyse(){

      global $res;

      $id_client=addslashes($_SESSION['navig']->client->id);

      $remisefidelite=0;
      $remiseindex=0;
      $remisetotal=0;

      if($id_client!=""){
        $this->id_client=$id_client;
        $this->charger_remisedispo();
        $remisefidelite=$this->remise;
        $remiseindex=$this->calculindex();
        $remisetotal=$this->calcultotal();
      }

        $res = str_replace("#REMISEFIDELITE",$remisefidelite,$res);
        $res = str_replace("#REMISEINDEX",$remiseindex,$res);
        $res = str_replace("#REMISETOTAL",$remisetotal,$res);

     }

	}


?>
