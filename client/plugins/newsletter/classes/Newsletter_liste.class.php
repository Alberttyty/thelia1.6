<?php
include_once(realpath(dirname(__FILE__)) . "/../../../../classes/Baseobj.class.php");
include_once(realpath(dirname(__FILE__)) . "/Newsletter_mail.class.php");


class Newsletter_liste extends Baseobj
{
		public $id;
		public $nom;
		public $actif;
		public $date;

		public $table="newsletter_liste";
		public $bddvars = array("id", "nom", "actif", "date");

		function Newsletter_liste()
		{
				$this->Baseobj();
		}

		function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

    function getDestinataires()
		{
	      $tabmail=array();

	      $query = "SELECT newsletter_mail.email AS email FROM newsletter_mail, newsletter_mail_liste WHERE liste=" . $this->id . " AND newsletter_mail_liste.email=newsletter_mail.id AND newsletter_mail.actif=1 ORDER BY newsletter_mail.id";
				$resul = mysql_query($query, $this->link);

	      while($row = mysql_fetch_object($resul)) {
						$info = new Newsletter_mail();
						$info->charger($row->email);
						$tabmail[] = $info->email;
				}

	      return $tabmail;
    }
}
?>
