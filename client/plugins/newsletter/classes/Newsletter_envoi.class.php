<?php
include_once(realpath(dirname(__FILE__)) . "/../../../../classes/Baseobj.class.php");

class Newsletter_envoi extends Baseobj
{
		public $id;
		public $email;
		public $id_campagne;
    public $envoye;
		public $date;

		public $table="newsletter_envoi";
		public $bddvars = array("id", "email", "id_campagne", "envoye", "date");

		function Newsletter_mail()
		{
				$this->Baseobj();
		}

		function charger($email = null, $id_campagne = null)
		{
				if ($email != null && $id_campagne != null) {
						return $this->getVars("SELECT * FROM $this->table WHERE email=\"$email\" AND id_campagne=".$id_campagne);
				}
		}

    function charger_next_email($id_campagne)
		{
      	return $this->getVars("SELECT * FROM $this->table WHERE id_campagne=".$id_campagne." AND envoye!=1 LIMIT 0,1");
		}

    function charger_emails($id_campagne,$envoye="",$nombre="")
		{
	      $tabmail=array();

	      if($envoye!="") $envoye = " and envoye=".$envoye;
	      if($nombre!="") $nombre = " limit 0,".$nombre;

	      $query = "select * from $this->table where id_campagne=".$id_campagne.$envoye.$nombre;
				$resul = mysql_query($query, $this->link);

	     	while($row = mysql_fetch_object($resul)) {
						$tabmail[] = $row->email;
				}

	      return $tabmail;
		}
}
?>
