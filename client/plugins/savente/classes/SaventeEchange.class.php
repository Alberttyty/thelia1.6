<?php
require_once(realpath(dirname(__FILE__)) . "/../../../../classes/Baseobj.class.php");
	
class SaventeEchange extends Baseobj {

	public $id;
	public $sav;
	public $ref;
	public $declidisp;
	public $taille;
	public $couleur;
	public $carac;
	public $qte;

	public $table = 'commande_sav_echange';
	public $bddvars = array('id', 'sav', 'ref', 'declidisp', 'taille', 'couleur', 'carac','qte');
	
	public function SaventeEchange() {
		$this->Baseobj();	
	}

	/**
	 * ACTIVATION DU PLUGIN
	 */
	public function init() {		
		/**
		 * Table stockant les produits demandés en échange afin de recréer une nouvelle commande (futurs Venteprod de la commande)
		 * sav 	=> ID commande_sav
		 * ref 	=> ID produit demandé en échange
		 * declidisp => ID declidisp du produit
		 * taille => Info. texte : taille du produit
		 * couleur => Info. texte : couleur du produit
		 * carac => Info. texte : autre caractéristique du produit
		 * qte	=> QUANTITE du produit
		 */
		$query_savente = 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` (
		  	`id` 	INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`sav` 	INT(11) UNSIGNED NOT NULL,
		   	`ref` 	INT(11) UNSIGNED NOT NULL,
			`declidisp` INT(11) UNSIGNED NOT NULL,
			`taille` VARCHAR(100) UNSIGNED NOT NULL,
			`couleur` VARCHAR(100) UNSIGNED NOT NULL,
			`carac` VARCHAR(100) UNSIGNED NOT NULL,
			`qte`	INT(11) UNSIGNED NOT NULL
			);';
		$resul_savente = mysql_query($query_savente);
	}
	
	/**
	 * DESACTIVATION DU PLUGIN
	 */
	public function destroy() {
		
	}
	
	/**
	 * CLASS METHODS
	 */
	public function lister_echanges($id_sav) {
		$query = 'SELECT * FROM `commande_sav_echange` WHERE `sav` = '.$id_sav;
		return $this->query_liste($query);
	}
	
}

?>