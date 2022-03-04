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

	include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsPaiements.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Client.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Raisondesc.class.php");

	class Emaildepaiement extends PluginsPaiements{

    var $defalqcmd = 0; /*à 0 car on defalque avec genfact() dans la fonction paiement()*/
    var $id;
    var $token;
    var $commande;
    var $datemodif;
    
    var $table="emaildepaiement";
    var $bddvars = array("id", "token", "commande", "datemodif");

		function __construct(){
			parent::__construct("emaildepaiement");
		}

		function init(){
			$this->ajout_desc("Email de paiement", "Email de paiement", "", 1);
      $cnx = new Cnx();
			$query_emaildepaiement = "CREATE TABLE IF NOT EXISTS `emaildepaiement` (
			 `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			 `token` TEXT NOT NULL ,		
			 `commande` LONGTEXT NOT NULL,
       `datemodif` datetime NOT NULL default '0000-00-00 00:00:00'
			) ;";
			$resul_emaildepaiement = mysql_query($query_emaildepaiement, $cnx->link);
      $test = new Message();
			if(! $test->charger("emaildepaiement")){
				$message = new Message();
				$message->nom = "emaildepaiement";
				$lastid = $message->add();

				$messagedesc = new Messagedesc();
				$messagedesc->message = $lastid;
				$messagedesc->lang = 1;
				$messagedesc->titre = "Email de paiement";
				$messagedesc->description = "__RAISON__ __NOM__ __PRENOM__,\n\nNous vous remercions de votre commande sur notre site __URLSITE__\n\nLe paiement de votre commande __COMMANDE__ du __DATE__ __HEURE__ est possible en utilisant le lien suivant : __URLSITE__/?fond=commande_detail&commande=__COMMANDE__&token_emaildepaiement=__PAIEMENT_TOKEN__\n\nCordialement";
				$messagedesc->add();

			}
		}
    
    function charger_commande($ref){
       return $this->getVars("select * from $this->table where commande=\"$ref\"");
    }
    
    function charger_token($token){
      $token=md5($token);
      return $this->getVars("select * from $this->table where token=\"$token\"");
    }

		function paiement($commande){
			ActionsModules::instance()->appel_module("confirmation", $commande);
      $commande->genfact();
      $commande->maj();
   		header("Location: " . urlfond("enregistree"));
   		exit();
		}
    
    private function substitutions($texte, $client, $commande, $token) {

  		$datecommande = strtotime($commande->date);
  
  		$raisondesc = new Raisondesc();
  		$raisondesc->charger($client->raison, $commande->lang);
  
  		$texte = str_replace("__RAISON__", $raisondesc->long, $texte);
  		$texte = str_replace("__NOM__", $client->nom, $texte);
  		$texte = str_replace("__PRENOM__", $client->prenom, $texte);
  
  		$texte = str_replace("__URLSITE__", Variable::lire('urlsite'), $texte);
  		$texte = str_replace("__NOMSITE__", Variable::lire('nomsite'), $texte);
  
  		$texte = str_replace("__COMMANDE__", $commande->ref, $texte);
      $texte = str_replace("__PORT__", $commande->port, $texte);
  		$texte = str_replace("__DATE__", strftime("%d/%m/%Y", $datecommande), $texte);
  		$texte = str_replace("__HEURE__", strftime("%H:%M:%S", $datecommande), $texte);
  		$texte = str_replace("__DATEDJ__", strftime("%d/%m/%Y"), $texte);
  		$texte = str_replace("__PAIEMENT_TOKEN__", $token, $texte);
  
  		return $texte;
    
	  }
   
    function envoyerEmail($commande){

			if (/*$this->est_module_de_paiement_pour($commande)*/true){
      
        $emaildepaiement=new Emaildepaiement();
      
        if($emaildepaiement->charger_commande($commande->ref)) $nouveau=false;
        else $nouveau=true;
        
        $emaildepaiement->commande=$commande->ref;
        $emaildepaiement->token=md5(uniqid(rand()));
        $emaildepaiement->datemodif = date('Y-m-d H:i:s');

				$message = new Message("emaildepaiement");
  			$messagedesc = new Messagedesc($message->id, $commande->lang);
  			$client = new Client($commande->client);
        
        $sujet = $this->substitutions($messagedesc->titre, $client, $commande, $emaildepaiement->token);
  			$texte = $this->substitutions($messagedesc->descriptiontext, $client, $commande, $emaildepaiement->token);
  			$html  = $this->substitutions($messagedesc->description, $client, $commande, $emaildepaiement->token);
        
        $emaildepaiement->token=md5($emaildepaiement->token);
        
        if($nouveau) $emaildepaiement->add();
        else $emaildepaiement->maj();
				
        
				//envoi du mail
    		Mail::envoyer("$client->prenom $client->nom", $client->email,Variable::lire('nomsite'),Variable::lire('emailcontact'),$sujet,$html,$texte);
        //mail('mathieu@pixel-plurimedia.fr', 'Test', $commande->ref);
			}

		}
    
    function demarrage(){
      
      if (isset($_GET['token_emaildepaiement'])) {
        $emaildepaiement=new Emaildepaiement();
        if($emaildepaiement->charger_token($_GET['token_emaildepaiement'])){
          $commande=new Commande();
          $commande->charger_ref($emaildepaiement->commande);
          $client=new Client();
          if($client->charger_id($commande->client)){
            $_SESSION['navig']->adresse = 0;
            $_SESSION['navig']->client = $client;
		        $_SESSION['navig']->connecte = 1;
          }
        } 
      }
       
    }

	}

?>
