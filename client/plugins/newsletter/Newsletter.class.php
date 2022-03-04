<?php
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Mail.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Dossier.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Dossierdesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Client.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Newsletter_liste.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Newsletter_mail.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Newsletter_mail_liste.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Newsletter_campagne.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Newsletter_desinscription.class.php");
require_once(realpath(dirname(__FILE__)) . "/classes/Newsletter_envoi.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/nettoyage.php");

class Newsletter extends PluginsClassiques
{
		public $api;

		function Newsletter()
		{
				$this->PluginsClassiques();
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////
		// INIT
		////////////////////////////////////////////////////////////////////////////////////////////////////
		function init()
		{
			$query = "CREATE TABLE IF NOT EXISTS `newsletter_liste` (
				 `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				 `nom` VARCHAR(255) NOT NULL ,
				 `actif` TINYINT NOT NULL,
				 `date` DATETIME NOT NULL
			) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
			;
			mysql_query($query, $this->link);

			$query = "CREATE TABLE IF NOT EXISTS `newsletter_mail` (
				 `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				 `client` INT UNSIGNED NOT NULL ,
				 `email` VARCHAR(255) NOT NULL ,
				 `actif` TINYINT NOT NULL,
				 `date` DATETIME NOT NULL
			) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
			;
			mysql_query($query, $this->link);

			$query = "CREATE TABLE IF NOT EXISTS `newsletter_mail_liste` (
			  	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					`email` INT UNSIGNED NOT NULL ,
					`liste` INT UNSIGNED NOT NULL
			) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
			;
			mysql_query($query, $this->link);

			$query = "CREATE TABLE IF NOT EXISTS `newsletter_campagne` (
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					`campagne` VARCHAR(255) NOT NULL ,
          `titre` TEXT NOT NULL ,
					`texte` TEXT NOT NULL ,
          `css` TEXT NOT NULL ,
					`liste` INT UNSIGNED NOT NULL ,
					`emailfrom` VARCHAR(255) NOT NULL ,
					`nomfrom` VARCHAR(255) NOT NULL ,
					`date` DATETIME NOT NULL ,
					`statut` TINYINT NOT NULL
			) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
			;
			mysql_query($query, $this->link);

			$query = "CREATE TABLE IF NOT EXISTS `newsletter_desinscription` (
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					`client` INT UNSIGNED NOT NULL ,
					`email` VARCHAR(255) NOT NULL ,
					`date` DATETIME NOT NULL
			) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
			;
			mysql_query($query, $this->link);

      $query = "CREATE TABLE IF NOT EXISTS `newsletter_envoi` (
					`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					`email` VARCHAR(255) NOT NULL ,
          `id_campagne` INT UNSIGNED NOT NULL ,
          `envoye` TINYINT NOT NULL,
					`date` DATETIME NOT NULL
			) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
			;
			mysql_query($query, $this->link);

			$emailfrom = new Variable();
			$emailfrom->charger("emailfrom");

			$nomsite = new Variable();
			$nomsite->charger("nomsite");

			$newsletterfrom = new Variable();
			if (! $newsletterfrom->charger("newsletterfrom")) {
					$newsletterfrom->nom = "newsletterfrom";
					$newsletterfrom->valeur = $emailfrom->valeur;
					$newsletterfrom->add();
			}

			$newsletternom = new Variable();
			if (! $newsletternom->charger("newsletternom")) {
					$newsletternom->nom = "newsletternom";
					$newsletternom->valeur = $nomsite->valeur;
					$newsletternom->add();
			}

      if (!is_dir("../client/cache/newsletter")) mkdir("../client/cache/newsletter", 0755);
      if (!file_exists("../template/newsletter_voir.html")) copy("../client/plugins/newsletter/template/newsletter_voir.html", "../template/newsletter_voir.html");
      if (!file_exists("../template/newsletter_ajout.html")) copy("../client/plugins/newsletter/template/newsletter_ajout.html", "../template/newsletter_ajout.html");
      if (!file_exists("../template/newsletter_supprime.html")) copy("../client/plugins/newsletter/template/newsletter_supprime.html", "../template/newsletter_supprime.html");
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////
		// MAIL
		////////////////////////////////////////////////////////////////////////////////////////////////////
		function mail($campagne = null, $email='')
		{
				include(realpath(dirname(__FILE__)) . "/config.php");

				$newsletter = new Newsletter_campagne();
				$newsletter->charger_id($campagne);

				$newsletterfrom = new Variable();
				$newsletterfrom->charger("newsletterfrom");

				$newsletternom = new Variable();
				$newsletternom->charger("newsletternom");

				$mail = new Mail(true);
				$mail->IsSMTP();
				$mail->Host = $serveur_smtp;
				$mail->Port = $port_smtp;
				$mail->SMTPAuth = $auth_smtp;
	      $mail->SMTPSecure = $secur_smtp;
	      $mail->SMTPDebug  = false;

				$mail->Username = $cle;
				$mail->Password = $secret;

				$urlsite = new Variable();
				$urlsite->charger("urlsite");

	      $html=$newsletter->creerHtml();

	      $html=$newsletter->reduireImages($html,$resize_width,$resize_height);

	      //Lien en absolue
	      $html = preg_replace("#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http)([^\"'>]+)([\"'>]+)#", '$1'.rtrim($urlsite->valeur,'/').'/$2$3', $html);

				$mail->FromName = $newsletternom->valeur;
				$mail->From = $newsletterfrom->valeur;
				$mail->Subject = $newsletter->titre;
	      $mail->ClearAttachments();
				$mail->MsgHTML($html,realpath(dirname(__FILE__))."/../../../");
				$mail->AltBody = "Pour visualiser la newsletter au format HTML : " . $urlsite->valeur . "/?fond=newsletter_voir&id=" . $newsletter->id."\n\n Se désinscrire de la newsletter : " . $urlsite->valeur . "/?fond=newsletter_supprime";
	      $mail->AddCustomHeader("X-Mailjet-Partner: thelia");

	      if ($email!='') {
		        $mail->AddAddress($email);
		    		if($mail->send()) $retourenvoi=true;
		        else $retourenvoi=false;
		    		$mail->ClearAddresses();
		        return $retourenvoi;
	      }
	      else {
		        if ($newsletter->statut==2)
						return 0;

		        if (isset($_REQUEST['debut_envoi'])) {
			          if ($_REQUEST['debut_envoi']=="oui") {
				            $liste = new Newsletter_liste();
				            $liste->charger($_REQUEST['num_liste']);
				            $tabmail=$liste->getDestinataires();

				            foreach($tabmail as $key=>$value) {
					              $envoi = new Newsletter_envoi();
					              $value=str_replace(' ','',$value);

					              if(!$envoi->charger($value,$newsletter->id)) {
						                $envoi->id_campagne=$newsletter->id;
						                $envoi->email=$value;
						                $envoi->add();
					              }
				            }

				            $newsletter->statut=1;
				            $newsletter->liste = $_REQUEST['num_liste'];
				            $newsletter->maj();
			          }
		        }

		        $mail->AddCustomHeader("X-Mailjet-Campaign: " . $newsletter->campagne);

		        $envoi = new Newsletter_envoi();

						for($i = 0; $i < $mailpenv; $i++) {
								if ($envoi->charger_next_email($newsletter->id)) {
				            $mail->AddAddress($envoi->email, " ");
				            $envoi->envoye=1;
				            $envoi->date=date("Y-m-d H:i:s");
				            $envoi->maj();
				  					$mail->send();
				  					$mail->ClearAddresses();
			          }
						}

						if ($envoi->charger_next_email($newsletter->id)) {
?>
								<script>
				        		setTimeout(function(){window.location="module.php?nom=newsletter&action_newsletter=envoyer&id=<?php echo $_REQUEST['id']; ?>&num_liste=<?php echo $_REQUEST['num_liste']; ?>";},2000);
				        </script>
<?php
						}
		       	else {
			  				$newsletter->statut = 2;
			        	$newsletter->date = date("Y-m-d H:i:s");
			  				$newsletter->maj();
		      	}
				}
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////
		// ACTION
		////////////////////////////////////////////////////////////////////////////////////////////////////
		function action()
		{
				global $action, $res, $urlerr;

	      if (isset($_REQUEST['newsletter_liste'])&&is_array($_REQUEST['newsletter_liste'])) {
	      		$_REQUEST['newsletter_liste'] = implode(',', $_REQUEST['newsletter_liste']);
	      }
	      $newsletter_liste=lireParam('newsletter_liste', 'int_list');
	      if ($newsletter_liste == "") $newsletter_liste=lireParam('newsletter_liste', 'int');

	      if (($action=="newsletter_ajout"&&$newsletter_liste=="")||($action=="newsletter_supprime"&&$newsletter_liste=="")) {
		        redirige_action($this->ajouterParam($urlerr,"errform=1&errliste=1"), urlfond("newsletter_ajout", "errform=1&errliste=1"));
		        exit();
	      }

	      if ($action!=""&&$newsletter_liste!="") {
		        switch ($action) {
				        case "newsletter_ajout":
					          if (isset($_REQUEST['newsletter_email'])) {
												$testhack=false;
												/*
						            if(!function_exists('dsp_crypt')){
						              	$cryptinstall="lib/crypt/cryptographp.fct.php";
						              	require_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
						            }
												*/
						            //if (!chk_crypt($_REQUEST['txt_securite'])) $testhack=true;
						            if(!isset($_SESSION['newsletter_token']['token'])) $testhack=true;
						        	  if(!isset($_REQUEST['newsletter_token'])) $testhack=true;
						            if ($_SESSION['newsletter_token']['token'] != $_REQUEST['newsletter_token']) $testhack=true;
						            if (time(true)-$_SESSION['newsletter_token']['time']<=10) $testhack=true;
						            if($_REQUEST['newsletter_controle']!='68920') $testhack=true;

						            if ($testhack) {
							              redirige_action($this->ajouterParam($urlerr,"errform=1&errcaptcha=1"), urlfond("newsletter_ajout", "errform=1&errcaptcha=1"));
							              exit();
						            }

						            $email = lireParam('newsletter_email', 'string');
						            $id = 0;
						            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
							              redirige_action($this->ajouterParam($urlerr,"errform=1&erremail=1"), urlfond("newsletter_ajout", "errform=1&erremail=1"));
							              exit();
						            }
					          }
					          elseif ($_SESSION['navig']->client->email!="") {
						            $email = $_SESSION['navig']->client->email;
						            $id = $_SESSION['navig']->client->id;
					          }
					          else break;

					          $mail = new Newsletter_mail();
					  				if (!$mail->charger($email)) {
						  					$mail->email = $email;
						  					$mail->client = $id;
						  					$mail->date = date("Y-m-d H:i:s");
						  					$mail->actif = 1;
						  					$mail->id=$mail->add();
					  				}
					  				else {
						            $mail->actif = 1;
						            $mail->maj();
					          }

					          foreach (explode(",",$newsletter_liste) as $key => $value) {
						            $liste = new Newsletter_mail_liste();
						            if (!$liste->charger($mail->id,$value)) {
														echo "$mail->id:$value ";
							              $liste->email=$mail->id;
							              $liste->liste=$value;
							              $liste->add();
						            }
					          }

					          global $urlok;
					          redirige_action($this->ajouterParam($urlok,"newsletter_ajout_ok=1"), urlfond("newsletter_ajout", "newsletter_ajout_ok=1"));
					          exit();
										break;

				        case "newsletter_supprime":
					          if (isset($_REQUEST['newsletter_email'])) {
						            if (!function_exists('dsp_crypt')) {
							              $cryptinstall="lib/crypt/cryptographp.fct.php";
							              include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
						            }
						            if (!chk_crypt($_REQUEST['txt_securite'])) {
							              redirige_action($urlerr, urlfond("newsletter_supprime", "errform=1&errcaptcha=1"));
							              exit();
						      		  }

						            $email = lireParam('newsletter_email', 'string');
						            $id = 0;
					          }
					          elseif ($_SESSION['navig']->client->email!="") {
						            $email = $_SESSION['navig']->client->email;
						            $id = $_SESSION['navig']->client->id;
					          }
					          else break;

					          $newsletter_des = new Newsletter_desinscription();
					  				if (!$newsletter_des->charger($email)) {
						  					$client = new Client();
						  				  if($client->charger_mail($email)) $newsletter_des->client = $client->id;
						  				  $newsletter_des->email = $email;
						  				  $newsletter_des->date = date("Y-m-d H:i:s");
						  				  $newsletter_des->add();
					  				}

					          $mail = new Newsletter_mail();
					  			  $mail->charger($email);
					  			  $mail->actif = 0;
					  		    $mail->maj();

					          foreach (explode(",",$newsletter_liste) as $key => $value) {
						            $liste = new Newsletter_mail_liste();
						            if($liste->charger($mail->id,$value))$liste->delete();
					          }

					          global $urlok;
					          redirige_action($urlok, urlfond("newsletter_supprime", "newsletter_supprime_ok=1"));
					          exit();
										break;
						}
	      }
		}

    function ajouterParam($url,$param)
		{
	      $url_parsed=parse_url($url);

	      /*$url_parsed['query'] = str_replace("&amp;", "&", $url_parsed['query']);*/

	      /*parse_str($url_parsed['query'],$arguments_depart);*/
	      parse_str($param,$arguments_ajout);

	      /*foreach($arguments_ajout as $key => $value){
	          $arguments_depart[$key]=$value;
	      }

	      $arguments="";
	      foreach($arguments_depart as $key => $value){
	          if($arguments!="") $arguments.="&";
	          $arguments.=$key.'='.$value;
	      }*/

	      $arguments="";
	      foreach($arguments_ajout as $key => $value){
	          if ($arguments!="") $arguments.="&";
	          $arguments.=$key.'='.$value;
	      }

	      $url_retour=$url_parsed['scheme'].'://'.$url_parsed['host'].$url_parsed['path'].'?'.$arguments.'#'.$url_parsed['fragment'];

	      return $url_retour;
    }

		////////////////////////////////////////////////////////////////////////////////////////////////////
		// APRES CLIENT
		////////////////////////////////////////////////////////////////////////////////////////////////////
		function apresclient($client)
		{
				if ($_REQUEST['newsletter_email'] == "true" && $_REQUEST['newsletter_liste'] != "") {
						$tmpcli = new Client();
						$tmpcli->charger_mail($client->email);
						$mail = new Newsletter_mail();

						if (! $mail->charger($tmpcli->email)) {
								$mail->email = $tmpcli->email;
								$mail->client = $tmpcli->id;
								$mail->actif = 1;
								$mail->date = date("Y-m-d H:i:s");
								$mail->id=$mail->add();
						}
						else {
			          $mail->actif = 1;
			          $mail->maj();
		        }

		        $liste = new Newsletter_mail_liste();
						if (!$liste->charger($mail->id,$_REQUEST['newsletter_liste'])){
			          $liste->email=$mail->id;
			          $liste->liste=$_REQUEST['newsletter_liste'];
			          $liste->add();
		        }
				}
		}

    function predemarrage()
		{
	      $token = md5(uniqid(microtime(), true));
	      $time = time();

	      if (!isset($_SESSION['newsletter_token'])) {
		        $_SESSION['newsletter_token'] = array();
		        $_SESSION['newsletter_token']['token']=$token;
	      }
	      if (!isset($_REQUEST['newsletter_token'])) $_SESSION['newsletter_token']['time']=$time;
    }

		function post()
		{
	      global $res;

	      if (!function_exists('dsp_crypt')) {
		        $cryptinstall="lib/crypt/cryptographp.fct.php";
		        include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
	      }
	      $res = str_replace("#CAPTCHANEWSLETTER", dsp_crypt(0,0,0), $res);

	      $token="vide";

	      if (isset($_SESSION['newsletter_token']['token'])) {
	        	if ($_SESSION['newsletter_token']['token']!="") $token=$_SESSION['newsletter_token']['token'];
	      }

	      $res = str_replace("#TOKEN_NEWSLETTER", $token, $res);
		}

    function boucle($texte, $args)
		{
				$boucle = lireTag($args, "boucle");
	      if ($boucle=="") $boucle="contenu";

	      switch ($boucle) {
			      case "contenu":
					      return $this->boucle_contenu($texte, $args);
					      break;

			      case "liste":
					      return $this->boucle_liste($texte, $args);
					      break;
	      }
		}

    function boucle_contenu($texte, $args)
		{
				// récupération des arguments
				$id = lireTag($args, "id");
				$search ="";
				$res="";

				// préparation de la requête
				if ($id!="") $search.=" and id=\"$id\"";

				$campagne = new Newsletter_campagne();

				$query_campagne = "select * from $campagne->table where 1 $search";

				$resul_campagne = $this->query($query_campagne);

				if ($resul_campagne) {
						$nbres = $this->num_rows($resul_campagne);

						if ($nbres > 0) {
								while( $row = $this->fetch_object($resul_campagne)) {
										$temp = $texte;
										$temp = str_replace("#TITRE", $row->titre, $texte);
										$temp = str_replace("#TEXTE", $row->texte, $temp);
										$temp = str_replace("#CSS", $row->css, $temp);

										$res .= $temp;
								}
						}
				}

				return $res;
		}

    function boucle_liste($texte, $args)
		{
				// récupération des arguments
				$id = lireTag($args, "id");
	      $actif = lireTag($args, "actif");
	      $pardefaut = lireTag($args, "pardefaut");

				$search ="";
				$res="";

				// préparation de la requête
				if ($id!="")  $search.=" and id=\"$id\"";
	      if ($actif!="")  $search.=" and actif=\"$actif\"";
	      else  $search.=" and actif=\"1\"";

				$liste = new Newsletter_liste();

				$query_liste = "select * from $liste->table where 1 $search";

				$resul_liste = $this->query($query_liste);

				if ($resul_liste) {
						$nbres = $this->num_rows($resul_liste);

						if ($nbres > 0) {
								while($row = $this->fetch_object($resul_liste)) {
				            $checked="";
				            if ($_SESSION['navig']->client->id!="") {
					              $inscription = new Newsletter_mail_liste();
					              $mail = new Newsletter_mail();
					              $mail->charger_client($_SESSION['navig']->client->id);
					              if ($inscription->charger($mail->id,$row->id)) $checked = "checked=\"checked\"";
				            }

										$temp = $texte;

										$temp = str_replace("#NEWSLETTER_ID", $row->id, $temp);
										$temp = str_replace("#NEWSLETTER_NOM", $row->nom, $temp);
										$temp = str_replace("#NEWSLETTER_DATE", $row->date, $temp);
				            $temp = str_replace("#NEWSLETTER_ACTIF", $row->actif, $temp);
				            $temp = str_replace("#NEWSLETTER_CHECKED", $checked, $temp);

										$res .= $temp;
								}
						}
				}

				return $res;
		}
}
?>
