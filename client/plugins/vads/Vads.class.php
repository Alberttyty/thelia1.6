<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsPaiements.class.php");

class Vads extends PluginsPaiements
{
		public $defalqcmd = 0;

		function Vads()
		{
				$this->PluginsPaiements("vads");
		}

		function init()
		{
				$this->ajout_desc("CyberPlus Paiement - paiement par CB", "CyberPlus Paiement - paiement par CB", "", 1);
		}

		function paiement($commande)
		{
				header("Location: client/plugins/vads/paiement.php");
		}

		function confirmation($commande)
		{
		    $module = new Modules();
		    $module->charger_id($commande->paiement);
		    if ($module->nom==$this->getNom()){
		  			if ($commande->statut == 2) parent::mail($commande);
		    }
	  }
}

?>
