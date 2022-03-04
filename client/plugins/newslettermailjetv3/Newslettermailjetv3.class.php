<?php
include_once(realpath(dirname(__FILE__)) . "/api/php-mailjet-v3-simple.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/nettoyage.php");
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/lire.php");

class Newslettermailjetv3 extends PluginsClassiques
{
		public $mj;
    public $listeclient;

		function Newslettermailjetv3()
		{
    		$this->PluginsClassiques();

	      $apikey = new Variable();
	      $apikey->charger("mailjetapikey");
	      $secretkey = new Variable();
	      $secretkey->charger("mailjetsecretkey");

	      $listeclient = new Variable();
	      $listeclient->charger("mailjetlisteclient");
	      $this->listeclient=$listeclient->valeur;

	      $this->mj = new Mailjet($apikey->valeur,$secretkey->valeur);

	      $debug = new Variable();
	      $debug->charger("mailjetdebug");
	      $this->mj->debug = $debug->valeur;
    }

		////////////////////////////////////////////////////////////////////////////////////////////////////
		// INIT
		////////////////////////////////////////////////////////////////////////////////////////////////////
		function init()
		{
	    	$debug = new Variable();
				if (!$debug->charger("mailjetdebug")) {
						$debug->nom = "mailjetdebug";
						$debug->valeur = 0;
						$debug->add();
				}

	      $apikey = new Variable();
				if (!$apikey->charger("mailjetapikey")) {
						$apikey->nom = "mailjetapikey";
						$apikey->valeur = 0;
						$apikey->add();
				}

	      $secretkey = new Variable();
				if (!$secretkey->charger("mailjetsecretkey")) {
						$secretkey->nom = "mailjetsecretkey";
						$secretkey->valeur = 0;
						$secretkey->add();
				}

	      $listeclient = new Variable();
				if (!$listeclient->charger("mailjetlisteclient")) {
						$listeclient->nom = "mailjetlisteclient";
						$listeclient->valeur = 0;
						$listeclient->add();
				}
		}

    function predemarrage()
		{
	      if (isset($_REQUEST['newsletter_liste'])) $_SESSION['newsletter_liste'] = $_REQUEST['newsletter_liste'];
	      if (isset($_REQUEST['newsletter_email'])) $_SESSION['newsletter_email'] = $_REQUEST['newsletter_email'];
    }

		////////////////////////////////////////////////////////////////////////////////////////////////////
		// ACTION
		////////////////////////////////////////////////////////////////////////////////////////////////////
		function action()
		{
				global $action, $res, $urlerr, $urlok;

	      if (isset($_REQUEST['newsletter_liste'])&&is_array($_REQUEST['newsletter_liste'])) {
	      		$_REQUEST['newsletter_liste'] = implode(',', $_REQUEST['newsletter_liste']);
	      }

	      $newsletter_liste=lireParam('newsletter_liste', 'int_list');

	      if ($newsletter_liste == "")$newsletter_liste=lireParam('newsletter_liste', 'int');
	      if ($newsletter_liste == "")$newsletter_liste=$this->listeclient;

	      if (($action=="newsletter_ajout"&&$newsletter_liste=="")||($action=="newsletter_supprime"&&$newsletter_liste=="")){
		        redirige_action($this->ajouterParam($urlerr,"errform=1&errliste=1"), urlfond("newsletter_ajout", "errform=1&errliste=1"));
		        exit();
	      }

	      if ($action != "" && $newsletter_liste!="") {
		        switch ($action) {

				        case "newsletter_ajout":
					          if (isset($_REQUEST['newsletter_email'])) {
						            if (!function_exists('dsp_crypt')) {
							              $cryptinstall="lib/crypt/cryptographp.fct.php";
							              include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
						            }

						            if (!chk_crypt($_REQUEST['txt_securite'])) {
							              redirige_action($this->ajouterParam($urlerr,"errform=1&errcaptcha=1"),urlfond("newsletter_ajout", "errform=1&errcaptcha=1"));
							              exit();
						      		  }

						            $email = lireParam('newsletter_email', 'string');

						            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
							              redirige_action($this->ajouterParam($urlerr,"errform=1&erremail=1"), urlfond("newsletter_ajout", "errform=1&erremail=1"));
							              exit();
						            }
					          }
					          elseif ($_SESSION['navig']->client->email!="") $email = $_SESSION['navig']->client->email;

					          if ($email!="") {
						            foreach(explode(",",$newsletter_liste) as $key => $value) {
							              $adresses=[$email];
							              $params = [
								                'method' => 'POST',
								                'Action' => 'Add',
								                'ListID' => $value,
								                'Addresses' => $adresses,
								                'Force' => true
							              ];
							              $this->mj->manycontacts($params);
						            }
						            redirige_action($this->ajouterParam($urlok,"newsletter_ajout_ok=1"), urlfond("newsletter_ajout", "newsletter_ajout_ok=1"));
						            exit();
					          }

										break;

				        case "newsletter_supprime":
					          if (isset($_REQUEST['newsletter_email'])) {
						            if (!function_exists('dsp_crypt')){
							              $cryptinstall="lib/crypt/cryptographp.fct.php";
							              include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
						            }
						            if (!chk_crypt($_REQUEST['txt_securite'])) {
							              redirige_action($urlerr, urlfond("newsletter_ajout", "errform=1&errcaptcha=1"));
							              exit();
						      		  }

						            $email = lireParam('newsletter_email', 'string');
						            $id = 0;
						            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
							              redirige_action($this->ajouterParam($urlerr,"errform=1&erremail=1"), urlfond("newsletter_ajout", "errform=1&erremail=1"));
							              exit();
						            }
					          }
					          elseif ($_SESSION['navig']->client->email!="") $email = $_SESSION['navig']->client->email;

					          if ($email!="") {
						            foreach(explode(",",$newsletter_liste) as $key => $value) {
							              $adresses=[$email];
							              $params = [
								                'method' => 'POST',
								                'Action' => 'Unsubscribe',
								                'ListID' => $value,
								                'Addresses' => $adresses
							              ];
							              $this->mj->manycontacts($params);
						            }

						            redirige_action($this->ajouterParam($urlok,"newsletter_ajout_ok=1"), urlfond("newsletter_ajout", "newsletter_ajout_ok=1"));
						            exit();
					          }

					        	break;
						}
	      }
		}

    function ajouterParam($url,$param)
		{
	      if ($url=="") return "";

	      parse_str($param,$arguments_ajout);
	      $arguments="";

	      foreach($arguments_ajout as $key => $value) {
	          if ($arguments!="") $arguments.="&";
	          $arguments.=$key.'='.$value;
	      }

	      $url_parsed=parse_url($url);
	      $url_retour=$url_parsed['scheme'].'://'.$url_parsed['host'].$url_parsed['path'].'?'.$arguments.'#'.$url_parsed['fragment'];

	      return $url_retour;
    }

    public function boucle($texte, $args)
		{
				$res = '';
				$response = $this->mj->listsAll();
		    $lists = $response->lists;

				foreach($lists as $list) {
						$tmp = $texte;
						$tmp = str_replace("#NOM", $list->label, $tmp);
						$tmp = str_replace("#ID", $list->id, $tmp);

						$res .= $tmp;
		    }

				return $res;
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////
		// APRES CLIENT
		////////////////////////////////////////////////////////////////////////////////////////////////////
		function apresclient($client)
		{
				if ($_REQUEST['newsletter_inscription'] == "oui") {
	      		if (isset($_REQUEST['newsletter_liste'])&&is_array($_REQUEST['newsletter_liste'])) {
	          		$_REQUEST['newsletter_liste'] = implode(',', $_REQUEST['newsletter_liste']);
	          }

	          $newsletter_liste=lireParam('newsletter_liste', 'int_list');

	          if ($newsletter_liste == "") $newsletter_liste=lireParam('newsletter_liste', 'int');
	          if ($newsletter_liste == "") $newsletter_liste=$this->listeclient;

	          $email = $client->email;

	          foreach(explode(",",$newsletter_liste) as $key => $value) {
		            $adresses=[$email];
		            $params = [
		              'method' => 'POST',
		              'Action' => 'Add',
		              'ListID' => $value,
		              'Addresses' => $adresses,
		              'Force' => true
		            ];
		            $this->mj->manycontacts($params);
	          }
				}
		}

		function analyse()
		{
	      global $res;

	      $res = preg_replace("/\#CAPTCHANEWSLETTER\[([^]]*)\]/", lireParam('errcaptcha') == "1" ? "\\1" : '', $res);
	      $res = preg_replace("/\#NEWSLETTER_EMAIL\[([^]]*)\]/", lireParam('erremail') == "1" ? "\\1" : '', $res);
	      $res = preg_replace("/\#NEWSLETTER_AJOUT_OK\[([^]]*)\]/", lireParam('newsletter_ajout_ok') == "1" ? "\\1" : '', $res);

	      if (!function_exists('dsp_crypt')) {
		        $cryptinstall="lib/crypt/cryptographp.fct.php";
		        include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
	      }

	      $res = str_replace("#CAPTCHANEWSLETTER", dsp_crypt(0,1,0), $res);
	      $res = str_replace("#NEWSLETTER_EMAIL", $_SESSION['newsletter_email'], $res);
	      $res = str_replace("#NEWSLETTER_LISTE", $_SESSION['newsletter_liste'], $res);
		}
}
?>
