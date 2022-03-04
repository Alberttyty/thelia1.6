<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Venteprod.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");

class Commandesencours extends PluginsClassiques {

    var $enregistree=0;
    var $payee=0;

    function Commandesencours() {
        $this->PluginsClassiques();
    }
    
    function calculer($ref) {
    	$venteprod=new Venteprod();
      	$commande=new Commande();

		$query = "select $commande->table.statut from $venteprod->table left join $commande->table on $venteprod->table.commande=$commande->table.id where $venteprod->table.ref=\"$ref\"";
		$resul = mysql_query($query, $venteprod->link);
      	
      	$this->enregistree=0;
      	$this->payee=0;

		while($row = mysql_fetch_object($resul)) {
        	if($row->statut==1) $this->enregistree=$this->enregistree+1;
      		if($row->statut>1 && $row->statut<5) $this->payee=$this->payee+1;
		}
    }

    function boucle($texte, $args) {
    
      	// récupération des arguments
		$ref=lireTag($args, "ref");
      	
      	if ($ref == "") return;
      	
      	$this->calculer($ref);
      	
      	$res = str_replace("#ENREGISTREE", $this->enregistree, $texte);
      	$res = str_replace("#PAYEE", $this->payee, $res);

		return $res;
       
    }
    
    function action() {
    
      	global $res,$fond;
      	
      	// PRODUIT
      	if (isset($_REQUEST['id_produit'])){
        	$produit = new Produit();
        	$produit->charger_id($_REQUEST['id_produit']);
        	$this->calculer($produit->ref);
        	$res = str_replace("#COMMANDES_ENREGISTREES", $this->enregistree, $res);
        	$res = str_replace("#COMMANDES_PAYEES", $this->payee, $res);
      	}
    
    }

}

?>