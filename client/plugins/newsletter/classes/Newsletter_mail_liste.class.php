<?php
include_once(realpath(dirname(__FILE__)) . "/../../../../classes/Baseobj.class.php");

class Newsletter_mail_liste extends Baseobj
{
		public $id;
		public $email;
		public $liste;

		public $table="newsletter_mail_liste";
		public $bddvars = array("id", "email", "liste");

		function Newsletter_mail_liste()
		{
				$this->Baseobj();
		}

		function charger($email = null, $liste = null)
		{
				if ($email != null && $liste != null) {
						return $this->getVars("SELECT * FROM $this->table WHERE email=\"$email\" AND liste=\"$liste\"");
				}
		}

		function nbinscription($email)
		{
	      $query_liste = "SELECT * FROM $this->table WHERE email=\"$email\"";
	      $resul_liste = $this->query($query_liste);
	      return $nbres = $this->num_rows($resul_liste);
		}
}
?>
