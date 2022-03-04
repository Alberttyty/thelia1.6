<?php
include_once(realpath(dirname(__FILE__)) . "/../../../../classes/Baseobj.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../../classes/Client.class.php");

class Newsletter_desinscription extends Baseobj
{
		public $id;
		public $client;
		public $email;
		public $date;

		public $table="newsletter_desinscription";
		public $bddvars = array("id", "client", "email", "date");

		function Newsletter_desinscription()
		{
				$this->Baseobj();
		}

		function charger($email = null, $var2 = null)
		{
				if ($email != null) return $this->getVars("SELECT * FROM $this->table WHERE email=\"$email\"");
    }

		function charger_id($id)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
    }
}
?>
