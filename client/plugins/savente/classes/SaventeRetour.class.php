<?php
require_once(realpath(dirname(__FILE__)) . "/../../../../classes/Baseobj.class.php");
	
class SaventeRetour extends Baseobj {

	public $id;
	public $sav;
	public $venteprod;

	public $table = 'commande_sav_retour';
	public $bddvars = array('id', 'sav', 'venteprod');
	
	public function SaventeRetour() {
		$this->Baseobj();	
	}
	
	public function charger_venteprod($id) {
		return $this->getVars('SELECT * FROM '.$this->table.' WHERE venteprod = '.$id);
	}

	/**
	 * ACTIVATION DU PLUGIN
	 */
	public function init() {		
		/**
		 * Table sauvegardant les historiques d'échange (lien entre la commande d'origine et la version modifiée)
		 * sav 	=> ID commande_sav
		 * venteprod => ID produit vendu que le client souhaite retourner
		 */
		$query_savente = 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` (
		  	`id` 	INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  	`sav` 	INT(11) UNSIGNED NOT NULL,
		   	`venteprod`	INT(11) UNSIGNED NOT NULL
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
	public function lister_retours($id_sav) {
		$query = 'SELECT * FROM `commande_sav_retour` WHERE `sav` = '.$id_sav;
		return $this->query_liste($query);
	}	
	
}

?>