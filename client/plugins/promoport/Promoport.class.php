<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/port.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Promo.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Zone.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Pays.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Modules.class.php");

class Promoport extends PluginsClassiques
{
    public $id;
    public $id_promo;
    public $transports;
    public $zones;

    public $table="promoport";
		public $bddvars = array("id", "id_promo", "transports", "zones");

		function Promoport()
    {
			   $this->PluginsClassiques();
		}

    function init()
    {
  		  $this->ajout_desc("Promo pour les frais de port", "Code promo utilisable pour les frais de port", "", 1);
        $cnx = new Cnx();
  			$query = "CREATE TABLE `promoport` (
    			  `id` int(11) NOT NULL auto_increment,
    			  `id_promo` int(11) NOT NULL,
    			  `transports` text NOT NULL,
            `zones` text NOT NULL,
    			  PRIMARY KEY  (`id`)
  			) AUTO_INCREMENT=1 ;"
        ;
  			$resul = mysql_query($query, $cnx->link);
    }

    function charger($id_promo = null, $var2 = null)
    {
			   if ($id_promo != null) return $this->getVars("SELECT * FROM $this->table WHERE id_promo=\"$id_promo\"");
		}

    /* Remise toujours mise à zéro car on met déjà le port à zéro dans la fonction port()*/
    function calc_remise(&$remise,$total)
    {
        $promo = &$_SESSION['navig']->promo;
        // Si le code promo est fait pour les frais de port
        if ($this->charger($promo->id)&&$promo->code!="") {
            if ($this->transportUtilise(explode(",",$this->transports),$_SESSION['navig']->commande->transport)&&$this->zoneUtilisee(explode(",",$this->zones))) {
                $remise=0;
            }
        }
    }

    /* Mettre le port à zéro si on utilise le bon transporteur */
    function port(&$frais,$port)
    {
        $promo = &$_SESSION['navig']->promo;

        $modules=new Modules();
        $modules->charger($port->nom_plugin);

        // Si le code promo est fait pour les frais de port
        if ($this->charger($promo->id)&&$promo->code!="") {
            if ($this->transportUtilise(explode(",",$this->transports),$modules->id)&&$this->zoneUtilisee(explode(",",$this->zones))) {
                $frais=0;
            }
        }
    }

    /* Est-ce que le transport utilisé fait partie des transports ciblés par la promo */
    function transportUtilise($transports,$port)
    {
        foreach($transports as $key => $transport) {
            if ($port==$transport) return true;
        }
        return false;
    }

    /* Est-ce que la zone de livraison fait partie des zones ciblées par la promo */
    function zoneUtilisee($zones)
    {
        $pays = new Pays();
        $mazone = new Zone();

        if ($_SESSION['navig']->adresse!=0) {
            $adresse = new Adresse();
            $adresse->charger($_SESSION['navig']->adresse);
            $pays->charger($adresse->pays);
        }
        else $pays->charger($_SESSION['navig']->client->pays);

        $mazone->charger($pays->zone);

        foreach($zones as $key => $zone) {
            if($mazone->id==$zone) return true;
        }

        return false;
    }

    function ajoutpromo($promo)
    {
        $promo->charger($promo->code);
        $this->majpromo($promo);
    }

    function majpromo($promo)
    {
        $promoport = new Promoport();

        if ($promoport->charger($promo->id)) $nouveau=false;
        else $nouveau=true;

        $promoport->id_promo=$promo->id;

        if (isset($_REQUEST['promoporttransport'])) {
            $promoport->transports=implode(",",$_REQUEST['promoporttransport']);
        }
        else $promoport->transports="";

        if (isset($_REQUEST['promoportzone'])) {
            $promoport->zones=implode(",",$_REQUEST['promoportzone']);
        }
        else $promoport->zones="";

        if ($promoport->zones==""&&$promoport->transports=="") {
            $promoport->delete();
        }
        else {
            if ($nouveau) $promoport->add();
            else $promoport->maj();

            $promo->valeur=0;
            $promo->maj();
        }
    }

    function suppromo($promo)
    {
        $promoport = new Promoport();
        $promoport->charger($promo->id);
        $promoport->delete();
    }
}
?>
