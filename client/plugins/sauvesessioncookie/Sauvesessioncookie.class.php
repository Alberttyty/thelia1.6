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
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Modules.class.php");

class Sauvesessioncookie extends PluginsClassiques
{
    public $id;
		public $token;
    public $session;
    public $datemodif;

    public $table="sauvesessioncookie";
    public $bddvars = ["id", "token", "session", "datemodif"];

		function __construct()
		{
				parent::__construct("sauvesessioncookie");
		}

		function init()
		{
				$this->ajout_desc("Sauvesessioncookie", "Sauvesessioncookie", "", 1);
	      $cnx = new Cnx();
				$query_sauvsession = "CREATE TABLE IF NOT EXISTS `sauvesessioncookie` (
					 `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					 `token` TEXT NOT NULL ,
					 `session` LONGTEXT NOT NULL,
		       `datemodif` datetime NOT NULL default '0000-00-00 00:00:00'
				);"
				;
				$resul_sauvsession = mysql_query($query_sauvsession, $cnx->link);
		}

    function charger($token = null, $var2 = null)
		{
       	if ($token != null) return $this->getVars("SELECT * FROM $this->table WHERE token=\"$token\"");
    }

    function sauvegarderSession()
		{
	      //48 H
	      $duree_de_vie=3600*48;
	      $session = addslashes(serialize($_SESSION['navig']));

	      if (isset($_COOKIE['Sauvesessioncookie'])) {
	          if ($this->charger($_COOKIE['Sauvesessioncookie'])) {
		   					$this->session = $session;
		            $this->datemodif = date("Y-m-d H:i:s");
		  					$this->maj();
	  				}
	          else {
		            $this->session = $session;
		            $this->token = session_id();
		            $this->datemodif = date("Y-m-d H:i:s");
		            $this->add();
	          }
	      }
				else {
						$this->session = $session;
						$this->token = session_id();
	          $this->datemodif = date("Y-m-d H:i:s");
						$this->add();
	          setcookie("Sauvesessioncookie", $this->token, time()+$duree_de_vie, "/", $_SERVER['SERVER_NAME'], 0, 1);
				}
    }

    function post()
		{
				if ($_REQUEST['action'] == "ajouter" || $_REQUEST['action'] == "supprimer" || $_REQUEST['action'] == "modifier"){
	        	$this->sauvegarderSession();
	      }
		}

    function effacerSauvesessioncookie()
		{
	      //EFFACEMENT
	      if (isset($_COOKIE['Sauvesessioncookie'])) {
		        if ($this->charger($_COOKIE['Sauvesessioncookie'])) $this->delete();
		        //setcookie("Sauvesessioncookie", '', time()-3600, "/", $_SERVER['SERVER_NAME'], 0, 1);
	      }
    }

    function apres()
		{
	      global $reset;

	      if ($reset) {
		        $this->effacerSauvesessioncookie();
		        // NETTOYAGE DE LA TABLE
		        //48 H
		        $duree_de_vie=3600*48;
		        $dateeffacer=date("Y-m-d H:i:s",time()-$duree_de_vie);
		        $query = "delete from $this->table where datemodif<=\"$dateeffacer\"";
					  $resul = mysql_query($query, $this->link);
	      }
		}

    function apresdeconnexion()
		{
      	$this->effacerSauvesessioncookie();
    }

    function apresconnexion($client)
		{
	      if (isset($_SESSION['navig']->panierrevendeur)) {
		        if ($client->type==0 && $_SESSION['navig']->panierrevendeur) $this->effacerSauvesessioncookie();
		        else $this->sauvegarderSession();
	      }
	      else $this->sauvegarderSession();
    }

    function apresmodifcompte($client)
		{
      	$this->sauvegarderSession();
    }

    function apres_creerlivraison($adresse)
		{
      	$this->sauvegarderSession();
    }

    function aprespromo($code)
		{
      	$this->sauvegarderSession();
    }

    function apresclient($client)
		{
      	$this->sauvegarderSession();
    }

    function apres_modifierlivraison($adresse)
		{
      	$this->sauvegarderSession();
    }

    function demarrage()
		{
	      if (isset($_COOKIE['Sauvesessioncookie'])&&$_SESSION['navig']->panier->nbart==0) {
		        if ($this->charger($_COOKIE['Sauvesessioncookie'])) {
			          $session = unserialize(stripcslashes($this->session));

			          //Test si module Declinaison libre utilise
			          if (property_exists($session, 'declibre')) {
			            	$_SESSION['navig']->declibre = $session->declibre;
			          }
			          //Test si module Revendeur utilise
			          if (property_exists($session, 'panierrevendeur')) {
			            	$_SESSION['navig']->panierrevendeur = $session->panierrevendeur;
			          }

			          $_SESSION['navig']->panier = $session->panier;
		        }
	      }

	      if (isset($_COOKIE['Sauvesessioncookie'])&&$_SESSION['navig']->connecte==0) {
		         if ($this->charger($_COOKIE['Sauvesessioncookie'])) {
			          $session = unserialize(stripcslashes($this->session));
			          $_SESSION['navig']->connecte = $session->connecte;
			          $_SESSION['navig']->client = $session->client;
		        }
	      }

	      if (isset($_COOKIE['Sauvesessioncookie'])&&$_SESSION['navig']->connecte==1&&$_SESSION['navig']->adresse==0) {
		         if ($this->charger($_COOKIE['Sauvesessioncookie'])) {
			          $session = unserialize(stripcslashes($this->session));
			          $_SESSION['navig']->adresse = $session->adresse;
		        }
	      }

	      if (isset($_COOKIE['Sauvesessioncookie'])&&$_SESSION['navig']->connecte==1&&$_SESSION['navig']->commande->transport=="") {
		        if ($this->charger($_COOKIE['Sauvesessioncookie'])) {
			         $session = unserialize(stripcslashes($this->session));
			         $_SESSION['navig']->commande->transport = $session->commande->transport;
		        }
	      }
    }
}
?>
