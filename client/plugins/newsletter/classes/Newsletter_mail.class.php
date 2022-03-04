<?php
include_once(realpath(dirname(__FILE__)) . "/../../../../classes/Baseobj.class.php");

class Newsletter_mail extends Baseobj
{
		public $id;
		public $client;
		public $email;
		public $actif;
		public $date;

		public $table="newsletter_mail";
		public $bddvars = array("id", "client", "email", "actif", "date");

		function Newsletter_mail()
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

    function charger_client($client)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE client=\"$client\"");
		}

    function test_validite($email)
		{
	      if (filter_var($email, FILTER_VALIDATE_EMAIL)) return true;
	      else return false;
    }
}
?>
