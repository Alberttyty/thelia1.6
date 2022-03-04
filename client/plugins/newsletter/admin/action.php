<?php
	if(isset($_REQUEST['action_newsletter'])){
		if($_REQUEST['action_newsletter'] == "desinscription"){
			$mail = new Newsletter_mail();
			if(!$mail->charger($_REQUEST['email']))
			return 0;
			
			$desinscription = new Newsletter_desinscription();
			if(!$desinscription->charger($_REQUEST['email'])){
				$desinscription->client = $mail->id;
				$desinscription->email = $mail->email;
				$desinscription->date = date("Y-m-d H:i:s");
				$desinscription->add();
				
				$mail->actif = 0;
				$mail->maj();
			}
		}


		if($_REQUEST['action_newsletter'] == "creer_campagne"){

			$newsletterfrom = new Variable("newsletterfrom");
			$newsletternom = new Variable("newsletternom");

			$campagne = new Newsletter_campagne();
			$campagne->campagne = $dossierdesc->description;
			$campagne->liste = 0;
			$campagne->emailfrom = $newsletterfrom->valeur;
			$campagne->nomfrom = $newsletternom->valeur;
      $campagne->titre = $_REQUEST['titre'];
      $campagne->texte = $_REQUEST['description'];
			$campagne->date = date("Y-m-d H:i:s");
			$campagne->statut = 0;
      if(file_exists("../template/css/newsletter.css"))
  		{
      $campagne->css=file_get_contents("../template/css/newsletter.css");
      }
      else
      {
      $campagne->css=file_get_contents("../client/plugins/newsletter/template/css/newsletter.css");
      }
			$lastid = $campagne->add();
			
			$campagne->charger_id($lastid);
			$campagne->campagne = "campagne-".$_SERVER['SERVER_NAME']."-".$lastid."-".time();
			$campagne->maj();		
		}		
    
    if($_REQUEST['action_newsletter'] == "modifier_campagne"){
			$campagne = new Newsletter_campagne();
      $campagne->charger_id($_REQUEST['id']);
      $campagne->titre = $_REQUEST['titre'];
      $campagne->texte = $_REQUEST['description'];
			$campagne->maj();		
		}		
		
    if($_REQUEST['action_newsletter'] == "supprimer_campagne"){
			$c = new Newsletter_campagne();
			$c->charger_id($_REQUEST['id']);
			$c->delete();
		}	

		if($_REQUEST['action_newsletter'] == "dupliquer"){
		
			$c = new Newsletter_campagne();
			$c->charger_id($_REQUEST['id']);
			$c->id = "";
			$c->statut = 0;
			$c->liste = 0;
			$c->date = date("Y-m-d H:i:s");
			$lastid = $c->add();
			$c->charger_id($lastid);
      $c->campagne = "campagne-".$_SERVER['SERVER_NAME']."-".$lastid."-".time();
			$c->maj();

		}		

		if($_REQUEST['action_newsletter'] == "creer_liste"){
			$liste = new Newsletter_liste();
			$liste->nom = $_REQUEST['nom_liste'];
			$liste->actif = 1;
			$liste->date = date("Y-m-d H:i:s");
			$liste->add();
		}		
		
		if($_REQUEST['action_newsletter'] == "importer_base"){
			$lst = new Newsletter_mail();
			$query_lst = "select * from $lst->table";
			$resul_lst = mysql_query($query_lst);
			while($row_lst = mysql_fetch_object($resul_lst)){
				$test = new Newsletter_mail_liste();
				
				if($test->charger($row_lst->id,  $_REQUEST['id']))
					continue;
				
				$mail = new Newsletter_mail_liste();
				$mail->email = $row_lst->id;
				$mail->liste = $_REQUEST['id'];
				$mail->add();
			}
		}	
    
    if($_REQUEST['action_newsletter'] == "importer_clients"){
			$client = new Client();
			$query_client = "select email,id from $client->table where 1";
			$resul_client = mysql_query($query_client);
			while($row_client = mysql_fetch_object($resul_client)){
       
        $mail = new Newsletter_mail();
        
        if(!$mail->charger($row_client->email)){
          $mail->email = $row_client->email;
  				$mail->actif = 1;
  				$mail->date = date("Y-m-d H:i:s");
          $mail->client = $row_client->id;
          $idmail = $mail->add();
        }
        else {
          $idmail = $mail->id;
        }
        
				$mail_liste = new Newsletter_mail_liste();
				
				if(!$mail_liste->charger($idmail,$_REQUEST['id']))
				{
          $mail_liste->email = $idmail;
				  $mail_liste->liste = $_REQUEST['id'];
				  $mail_liste->add();
        }
        
			}
		}	

		if($_REQUEST['action_newsletter'] == "ajouter_email"){
			$test = new Newsletter_mail();
			if(! $test->charger($_REQUEST['email'])){
				$test->email = $_REQUEST['email'];
				$test->actif = 1;
				$test->date = date("Y-m-d H:i:s");
				
				$client = new Client();
				$client->charger_mail($_REQUEST['email']);
				
				$test->client = $client->id;
				$idmail = $test->add();
			} else 
				$idmail = $test->id;
				$test = new Newsletter_mail_liste();

				if($test->charger($idmail,  $_REQUEST['id']))
					return 0;
							
				$mail = new Newsletter_mail_liste();
				$mail->email = $idmail;
				$mail->liste = $_REQUEST['id'];
				$mail->add();
		
		}						

		if($_REQUEST['action_newsletter'] == "supprimer_email"){
			$mail = new Newsletter_mail_liste();
			$mail->charger($_REQUEST['email'], $_REQUEST['id']);	
			$mail->delete();
		}	
		
		if($_REQUEST['action_newsletter'] == "supprimer_liste"){
			$mail = new Newsletter_liste();
			$mail->charger($_REQUEST['id']);	
			$mail->actif = 0;
			$mail->maj();
?>
			<script type="text/javascript">
				location="module.php?nom=newsletter&action_newsletter=liste";
			</script>
<?php
		}			

		if($_REQUEST['action_newsletter'] == "importer_csv"){
			$fichier = fopen($_FILES['fichiercsv']['tmp_name'], "r");
			$rec = fgets($fichier, 1024);
			if(! preg_match("/^[^;]*;[^;]*;[^;]*$/", $rec)){
?>
	<script type="text/javascript">
		alert("Format du fichier incorrect !");
	</script>
<?php
				
				return 0;
			}
			
			fclose($fichier);

			
			$fichier = fopen($_FILES['fichiercsv']['tmp_name'], "r");
			
			while (($data = fgetcsv($fichier, 0, ";")) !== FALSE){

				$test = new Newsletter_mail();
			if(! $test->charger($data[2])){
				$test->email = $data[2];
				$test->actif = 1;
				$test->date = date("Y-m-d H:i:s");
				
				$client = new Client();
				$client->charger_mail($data[2]);
				
				$test->client = $client->id;
				$idmail = $test->add();
			} else 
				$idmail = $test->id;
				$test = new Newsletter_mail_liste();

				if($test->charger($idmail,  $_REQUEST['id']))
					continue;
									
				$mail = new Newsletter_mail_liste();
				$mail->email = $idmail;
				$mail->liste = $_REQUEST['id'];
				$mail->add();

			}		

		}			

		if($_REQUEST['action_newsletter'] == "desinscription_annulation"){  
			$desinscription = new Newsletter_desinscription();
			$desinscription->charger_id($_REQUEST['id']);
      $mail = new Newsletter_mail();
			if ($mail->charger($desinscription->email))
      {
       $mail->actif = 1;
       $mail->maj();
      }
			$desinscription->delete();
		}	
    
    if($_REQUEST['action_newsletter'] == "tester"){
			$newsletter = new Newsletter();
			if($newsletter->mail($_REQUEST['id'],$_REQUEST['email']))
      echo '<script type="text/javascript">alert("Test envoyé à '.$_REQUEST['email'].'");</script>';
      else
      echo '<script type="text/javascript">alert("Erreur lors de l\'envoi à '.$_REQUEST['email'].'");</script>';
		}			

		if($_REQUEST['action_newsletter'] == "envoyer"){
			$newsletter = new Newsletter(); 
			$newsletter->mail($_REQUEST['id']);
		}	

	}
?>