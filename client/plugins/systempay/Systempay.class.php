<?php
#####################################################################################################
#
#					Module pour la plateforme de paiement Systempay
#						Version : 1.0 (révision 37181)
#									########################
#					Développé pour Thelia
#						Version : 1.5.1
#						Compatibilité plateforme : V2
#									########################
#					Développé par Lyra Network
#						http://www.lyra-network.com/
#						10/07/2012
#						Contact : supportvad@lyra-network.com
#
#####################################################################################################
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsPaiements.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Modules.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");

class Systempay extends PluginsPaiements
{
    public $defalqcmd = 0;

  	public function Systempay()
    {
  		  parent::__construct('systempay');
  	}

  	public function init()
    {
  		  $this->ajout_desc("Systempay - paiement par CB", "Systempay - paiement par CB", "", 1);
  	}

  	public function paiement($commande)
    {
  		  header("Location: client/plugins/systempay/paiement.php");
  	}

    function confirmation($commande)
    {
        $module = new Modules();
        $module->charger_id($commande->paiement);
        if ($module->nom==$this->getNom()){
        		if ($commande->statut == 2){
        		    parent::mail($commande);
        		}
        }
  	}

  	public function est_actif()
    {
    		// calculate order amount
    		$total = $_SESSION['navig']->panier->total() + $_SESSION['navig']->commande->port;
    		$total -= $_SESSION['navig']->commande->remise;

    		// minimum and maximum amount restrictions
    		if ((defined('MODULE_PAYMENT_SYSTEMPAY_AMOUNT_MIN') && MODULE_PAYMENT_SYSTEMPAY_AMOUNT_MIN != '' && $total < MODULE_PAYMENT_SYSTEMPAY_AMOUNT_MIN)
    				|| (defined('MODULE_PAYMENT_SYSTEMPAY_AMOUNT_MAX') && MODULE_PAYMENT_SYSTEMPAY_AMOUNT_MAX != '' && $total > MODULE_PAYMENT_SYSTEMPAY_AMOUNT_MAX)) {
            return false;
    		}

    		return true;
  	}
}

?>
