<?php
include_once(dirname(__FILE__) . '/../../../classes/PluginsClassiques.class.php');

class Commentaireclient extends PluginsClassiques
{
		const MODULE = 'commentaireclient';

		public $id;
		public $commande;
		public $commentaire;

		public $table = 'commentaireclient';

		public $bddvars = array('id', 'client', 'commentaire');

		public function __construct()
		{
				parent::__construct(self::MODULE);
		}

		public function init()
		{
				$this->query("
					CREATE TABLE `$this->table` (
						`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`client` TEXT NOT NULL ,
						`commentaire` TEXT NOT NULL
					)
				") or die('Echec en creation de la table $this->table');
		}

		public function charger($refclient = null, $var2 = null)
		{
				if ($refclient!= null) return $this->getVars("SELECT * FROM $this->table WHERE client='$refclient'");
		}

		public function charger_id($id)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE id=".intval($id));
		}

		public function boucle($texte, $args)
		{
				$res = '';
				$client = lireTag($args, 'client');

				if ($this->charger($client)) $res = str_replace('#COMMENTAIRE', $this->commentaire, $texte);

				return $res;
		}
}
?>
