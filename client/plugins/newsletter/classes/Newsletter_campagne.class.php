<?php
include_once(realpath(dirname(__FILE__)) . "/../../../../classes/Baseobj.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../../classes/Variable.class.php");
include_once(realpath(dirname(__FILE__)) . "/../classes/Public_api.php");

class Newsletter_campagne extends Baseobj
{
		public $id;
		public $campagne;
    public $titre;
    public $texte;
    public $css;
		public $liste;
		public $emailfrom;
		public $nomfrom;
		public $date;
		public $statut;

		public $table="newsletter_campagne";
		public $bddvars = array("id", "campagne", "titre", "texte", "css", "liste", "emailfrom", "nomfrom", "date", "statut");

		function Newsletter_campagne()
		{
				$this->Baseobj();

				include(realpath(dirname(__FILE__)) . "/../config.php");

				$this->api = new Public_api();
				$this->api->apiKey = $cle;
				$this->api->secretKey = $secret;
		}

		function charger($campagne = null, $var2 = null)
		{
				if ($campagne != null) return $this->getVars("SELECT * FROM $this->table WHERE campagne=\"$campagne\"");
    }

 		function charger_id($id)
		{
		    return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
    }

		function stat($type)
		{
				// sent, open, click, bounce, spam, total
				$this->api->sendRequest("messageList", array("custom_campaign" => $this->campagne));
				$xml = simplexml_load_string($this->api->_response);
				$this->api->sendRequest("messageStatistics", array("id" => $xml->result->item->id));
				$xml = simplexml_load_string($this->api->_response);
				return $xml->result->$type;
		}

    function allStats()
		{
				// sent, open, click, bounce, spam, total
				$this->api->sendRequest("messageList", array("custom_campaign" => $this->campagne));
				$xml = simplexml_load_string($this->api->_response);
				$this->api->sendRequest("messageStatistics", array("id" => $xml->result->item->id));
				$xml = simplexml_load_string($this->api->_response);
				return $xml->result;
		}

    function creerHtml()
		{
	      $texte=$this->texte;

	      $url = new Variable();
				$url->charger("urlsite");
	      $texte = preg_replace("`\#URL_DESINSCRIPTION`", trim($url->valeur,"/")."/?fond=newsletter_supprime", $texte);

	      $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	              <html>
	              <head>
	              <title>'.$this->titre.'</title>
	              <meta http-equiv="Content-Type" content="text/html; charset=utf-8"  />';

	      $html .= '<style type="text/css">
	               '.$this->css.'
	                </style>';

	      $html .= '</head>
	                <body>';
	      $html .= $texte;
	      $html .= '</body>
	                </html>';

	      return $html;
    }

    function reduireImages($html,$resize_width,$resize_height)
		{
		    preg_match_all("/(src|background)=\"(.*)\"/Ui", $html, $images);

		    if(isset($images[2])) {
			      foreach($images[2] as $i => $url) {
			        // do not change urls for absolute images (thanks to corvuscorax)

			        if (!preg_match('#^[A-z]+://#', $url)) {
			           $nomcache ='';

			           if (file_exists(__DIR__.'/../../../..'.urldecode($url)) && preg_match("/([^\/]*).((jpg|gif|png|jpeg))/i", urldecode($url), $nsimple)) {
				             $nomcache  = "/client/cache/newsletter/" . $resize_width . "_" . $resize_height . "______" . $nsimple[1] . "." . $nsimple[2];
				  			     $pathcache = __DIR__ . "/../../../..$nomcache";

				             if(file_exists($pathcache)) $retourcache=true;
				             else $retourcache = traiter_et_cacher_image(__DIR__.'/../../../..'.urldecode($url), $pathcache, $resize_width, $resize_height);

				             if($retourcache) $html = preg_replace("/".$images[1][$i]."=\"".preg_quote($url, '/')."\"/Ui", $images[1][$i]."=\"".$nomcache."\"", $html);
			          	}
			        }
			      }
		    }
		    return $html;
    }
}
?>
