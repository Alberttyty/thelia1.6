<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) Octolys Development		                                     */
/*		email : thelia@octolys.fr		        	                             	 */
/*      web : http://www.octolys.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Message.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Messagedesc.class.php");

class Commentairescontenu extends PluginsClassiques
{
		public $id;
		public $nom;
		public $message;
		public $id_contenu;
		public $titre;
		public $date;
		public $valide;

		public $table="commentairescontenu";
		public $bddvars = array("id", "nom", "message", "id_contenu","titre", "date", "valide");

		function Commentairescontenu()
		{
				$this->PluginsClassiques("Commentairescontenu");
		}

		function charger($id = null, $lang=null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		function init()
		{
			  $this->ajout_desc("Commentairescontenu", "Commentairescontenu", "", 1);

				$cnx = new Cnx();
				$query_commentaires = "CREATE TABLE `commentairescontenu` (
					  `id` int(11) NOT NULL auto_increment,
					  `nom` text NOT NULL,
					  `message` text NOT NULL,
					  `id_contenu` int(11) NOT NULL,
					  `date` datetime NOT NULL,
					  `titre` text NOT NULL,
					  `valide` tinyint NOT NULL,
					  PRIMARY KEY  (`id`)
				) AUTO_INCREMENT=1 ;"
				;
				$resul_commentaires = mysql_query($query_commentaires, $cnx->link);

				$test = new Message();
				if (! $test->charger("commentairescontenu")) {
						$message = new Message();
						$message->nom = "commentairescontenu";
						$lastid = $message->add();

						$messagedesc = new Messagedesc();
						$messagedesc->message = $lastid;
						$messagedesc->lang = 1;
						$messagedesc->titre = "Nouveau Commentaire";
						$messagedesc->description = "Un nouveau commentaire a été déposé";
						$messagedesc->add();
				}
		}

    function boucle($texte, $args)
		{
	      $boucle = lireTag($args, "boucle");

	      if ($boucle=="") $boucle='commentaires';

	      $res="";

	      switch ($boucle) {
		        case 'commentaires':
				        $res=$this->boucle_commentaires($texte, $args);
				        break;

		        case 'pagination':
				        $res=$this->boucle_pagination($texte, $args);
				        break;
	      }

	      return $res;
    }

    function boucle_pagination($texte, $args)
		{
	      $id_contenu= lireTag($args, "id_contenu");
				$valide= lireTag($args, "valide");
				$classement= lireTag($args, "classement");
	      $deb= lireTag($args, "deb");
	      $num= lireTag($args, "num");
	      $page= lireTag($args, "page");

	      $pagination_suivante=0;
	      $pagination_precedente=0;

	      $search ="";
				$res="";
				$order="";
	      $limit="";

				// préparation de la requête
				if($id_contenu!="")  $search.=" and id_contenu=\"$id_contenu\"";
				if($valide!="")  $search.=" and valide=\"$valide\"";
				if($classement!="")  $order.=" order by $classement";
	      if($deb!=""&&$num!="")  {$deb=intval($deb);$num=intval($num);$limit=" limit $deb,$num";}

				$commentaire = new Commentairescontenu();

				$query_commentaires = "select * from $commentaire->table where 1 $search $order $limit;";
				$resul_commentaires = mysql_query($query_commentaires, $commentaire->link);
				$nbres = mysql_numrows($resul_commentaires);

	      if ($page!="")  {
		        if (isset($_REQUEST['pagination'])) $pagination=intval($_REQUEST['pagination']);
		        else $pagination=1;
		        if ($pagination==0)$pagination=1;

		        $pagination_suivante_res=($pagination*$page);
		        $pagination_suivante=$pagination+1;
		  			if($nbres<$pagination_suivante_res) $pagination_suivante=0;

		        $pagination_precedente=$pagination-1;
		  			if($pagination_precedente<1) $pagination_precedente=0;
	      }

	      $res=$texte;

	      $res = str_replace("#PAGINATION_SUIVANTE",$pagination_suivante,$res);
	      $res = str_replace("#PAGINATION_PRECEDENTE",$pagination_precedente,$res);

				return $res;
		}

		function boucle_commentaires($texte, $args)
		{
				// récupération des arguments
				$id_contenu= lireTag($args, "id_contenu");
				$valide= lireTag($args, "valide");
				$classement= lireTag($args, "classement");
	      $deb= lireTag($args, "deb");
	      $num= lireTag($args, "num");
	      $page= lireTag($args, "page");

				$search ="";
				$res="";
				$order="";
	      $limit="";

				// préparation de la requête
				if($id_contenu!="")  $search.=" and id_contenu=\"$id_contenu\"";
				if($valide!="")  $search.=" and valide=\"$valide\"";
				if($classement!="")  $order.=" order by $classement";
	      if($deb!=""&&$num!="")  {$deb=intval($deb);$num=intval($num);$limit=" limit $deb,$num";}
	      if($page!="")  {
		        if (isset($_REQUEST['pagination'])) $pagination=intval($_REQUEST['pagination']);
		        else $pagination=1;

		        if ($pagination==0) $pagination=1;

		        $deb=($pagination*$page)-$page;
		        $limit=" limit $deb,$page";
	      }

				$commentaire = new Commentairescontenu();

				$query_commentaires = "select * from $commentaire->table where 1 $search $order $limit;";
				$resul_commentaires = mysql_query($query_commentaires, $commentaire->link);
				$nbres = mysql_numrows($resul_commentaires);
				if (!$nbres) return "";

	      $compteur=0;

				while( $row = mysql_fetch_object($resul_commentaires)) {
		        $compteur=$compteur+1;
						$temp = str_replace("#NOM", "$row->nom", $texte);
						$temp = str_replace("#MESSAGE", "$row->message", $temp);
						$temp = str_replace("#DATE", date("Y-m-d",strtotime(substr($row->date, 0, 10))), $temp);
						$temp = str_replace("#HEURE", substr($row->date, 11), $temp);
						$temp = str_replace("#TITRE",$row->titre,$temp);
						$temp = str_replace("#VALIDE",$row->valide,$temp);
		        $temp = str_replace("#COMPT",$compteur,$temp);

						$res .= $temp;
				}

	      $pagination_suivante=$pagination+1;
	      $pagination_suivante_res=($pagination_suivante*$page);
				if($nbres<$pagination_suivante_res) $pagination_suivante=0;

	      $pagination_precedente=$pagination-1;
				if($pagination_precedente<1) $pagination_precedente=0;

	      $res = str_replace("#PAGINATION_SUIVANTE",$pagination_suivante,$res);
	      $res = str_replace("#PAGINATION_PRECEDENTE",$pagination_precedente,$res);

				return $res;
		}

		function action()
		{
				if (isset($_POST['action']) && $_POST['action'] == "ajcommentairecontenu") {
						if (!function_exists('dsp_crypt')) {
					      $cryptinstall="lib/crypt/cryptographp.fct.php";
					      include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
			      }

			      if (chk_crypt($_REQUEST['txt_securite'])) {
								$commentaire = new Commentairescontenu();
								$commentaire->nom = strip_tags($_POST['nom']);
								$commentaire->message = strip_tags($_POST['message']);
								$commentaire->id_contenu = strip_tags($_POST['id_contenu']);
								$commentaire->titre = strip_tags($_POST["titre"]);
								$commentaire->date = date("Y-m-d H:i:s");
								$commentaire->valide = 0 ;

								$commentaire->add();

								$emailcontact = new Variable();
								$emailcontact->charger("emailcontact");

								$nomsite = new Variable();
								$nomsite->charger("nomsite");

								$urlsite = new Variable();
								$urlsite->charger("urlsite");

								$message = new Message();
								$message->charger("commentairescontenu");

								$messagedesc = new Messagedesc();
								$messagedesc->charger($message->id);

								$messagedesc->descriptiontext = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->descriptiontext);
								$messagedesc->description = str_replace("__URLSITE__", $urlsite->valeur, $messagedesc->description);
								$messagedesc->descriptiontext = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->descriptiontext);
								$messagedesc->description = str_replace("__NOMSITE__", $nomsite->valeur, $messagedesc->description);

				        $mail = new Mail();
				        $mail->envoyer(
				        /*to_name*/$nomsite->valeur,
				        /*to_adr*/$emailcontact->valeur,
				        /*from_name*/$nomsite->valeur,
				        /*from_adresse*/$emailcontact->valeur,
							  /*sujet*/$messagedesc->titre,
							  /*corps_html*/$messagedesc->description,
				        /*corps_texte*/$messagedesc->descriptiontext);

								$_POST['formulaire_commentaires_ok']=true;
						}
			      else $_POST['formulaire_commentaires_erreur_code']=true;
	      }
		}

		function post()
		{
	      global $res;

	      if (isset($_POST['formulaire_commentaires_ok'])) {
	      		$res = preg_replace("`\#COMMENTAIRES_ENVOI\[([^]]*)\]`", "\\1", $res);
	      }
	      else $res = preg_replace("`\#COMMENTAIRES_ENVOI\[([^]]*)\]`", "", $res);

	      if (!function_exists('dsp_crypt')) {
		      $cryptinstall="lib/crypt/cryptographp.fct.php";
		      include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
	      }
	      $res = str_replace("#COMMENTAIRES_ANTISPAM", dsp_crypt(0,1,0), $res);

	      if (isset($_POST['formulaire_commentaires_erreur_code'])){
	  				$res = preg_replace("`\#COMMENTAIRES_ERREUR_CODE\[([^]]*)\]`", "\\1", $res);
	      }
	      else $res = preg_replace("`\#COMMENTAIRES_ERREUR_CODE\[([^]]*)\]`", "", $res);

	      $valeurs=[
			      "nom"  =>"",
			      "titre"  =>"",
			      "message"  =>"",
			      "txt_securite"  =>""
				];

	      foreach($valeurs as $key => $value) {
	        	$res = str_replace("#COMMENTAIRES_".strtoupper($key),$_POST[$key], $res);
	      }
    }
}
?>
