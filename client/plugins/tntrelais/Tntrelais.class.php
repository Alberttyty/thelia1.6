<?php
require_once(dirname(__FILE__) . "/../../../classes/PluginsTransports.class.php");
require_once(dirname(__FILE__) . "/../../../classes/Commande.class.php");
require_once(dirname(__FILE__) . "/../../../classes/Variable.class.php");
require_once(dirname(__FILE__) . "/../../../classes/Client.class.php");
require_once(dirname(__FILE__) . "/../../../classes/Venteprod.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/mutualisation.php");

class Tntrelais extends PluginsTransports {

		const MODULE    = 'tntrelais';

		public $id;
		public $id_commande;
		public $nom;
		public $cpostal;
		public $adresse;
		public $ville;

		public $table = "commande_tnt";
		public $bddvars = array("id", "id_commande", "nom","adresse","cpostal","ville");

		public function __construct() {
			parent::__construct('Tntrelais');
		}

		function init() {
			$query = mysql_query("CREATE TABLE `".$this->table."` (
								`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
								`id_commande` INT NOT NULL ,
								`nom` TEXT NOT NULL ,
								`adresse` TEXT NOT NULL ,
								`cpostal` TEXT NOT NULL ,
								`ville` TEXT NOT NULL,
								`code` TEXT NOT NULL
								) ENGINE = InnoDB  ");

			$this->ajout_desc("Tntrelais", "Tntrelais", "", 1);

			@mkdir(SITE_DIR."client/plugins/".self::MODULE,0755);
			/*
			$variable = new Variable();
			if(! $variable->charger("tntid")){
				$variable->nom = "tntid";
				$variable->valeur = "";
				$variable->add();
			}*/
		}

		function action() {
			if($_GET["action"]== "paiment") {
				if(isset($_POST["nom"]) && isset($_POST["cpostal"]) && isset($_POST["adresse"]) && isset($_POST["ville"]) && isset($_POST["id"]) && isset($_POST["code"])) {
					$_SESSION["nomtnt"]=$_POST["nom"];
					$_SESSION["cpostaltnt"] = $_POST["cpostal"];
					$_SESSION["adressetnt"] = $_POST["adresse"];
					$_SESSION["villetnt"] = $_POST["ville"];
					$_SESSION["idtnt"] = $_POST["id"];
					$_SESSION["codetnt"] = $_POST["code"];
				}
			}
		}

		function aprescommande($commande) {

			if($this->est_module_de_transport_pour($commande))
			{
				$this->nom = $_SESSION["nomtnt"];
				$this->cpostal = $_SESSION["cpostaltnt"];
				$this->adresse = $_SESSION["adressetnt"];
				$this->ville = $_SESSION["villetnt"];
				$this->code = $_SESSION["codetnt"];

				$commandetnt = new Commande();
				$query = mysql_query("SELECT * FROM $commandetnt->table ORDER BY id DESC LIMIT 0,1");
				$row = mysql_fetch_object($query);
				$query = mysql_query("INSERT INTO $this->table VALUES(NULL,'$row->id','$this->nom','$this->adresse','$this->cpostal','$this->ville','$this->code')");
			}
		}

		function existe($id_commande){
			$query = mysql_query("SELECT * FROM $this->table WHERE id_commande=\"$id_commande\" ");
			if(mysql_num_rows($query) > 0) return true;
			else return false;
		}

		function boucle($texte, $args) {
			$cpostal= lireTag($args, "cpostal");
			$ville= lireTag($args, "ville");
			$ville = str_replace('-',' ',$ville);
			$ville=urlencode(strtoupper($ville));
			$idcommande = lireTag($args, "idcommande");

			$res="";
			$fic=fopen("http://www.tnt.fr/projet/pointrelais.nsf/searchRelaisXML?readForm&Saisie=$cpostal&ville=$ville","r");

			$i = 0;
			$j = 0;
			$k = 0;
			$l = 0;
			$m = 0;
			$nom=array();
			$cpostal=array();
			$ville=array();
			$adresse=array();
			$code = array();

			if($fic) {
					while(!feof($fic)) {
						$page = fgets($fic,4096);
						if(preg_match("#<NOM_RELAIS>(.*)</NOM_RELAIS>#",$page,$regs)){
							$nom[$i] = $regs[1];
							$i++;
						}
						if(preg_match("#<CODE_POSTAL>(.*)</CODE_POSTAL>#",$page,$regs)){
							$cpostal[$j] = $regs[1];
							$j++;
						}
						if(preg_match("#<VILLE>(.*)</VILLE>#",$page,$regs)){
							$ville[$k] = $regs[1];
							$k++;
						}
						if(preg_match("#<ADRESSE>(.*)</ADRESSE>#",$page,$regs)){
							$adresse[$l] = $regs[1];
							$l++;
						}
						if(preg_match("#<CODE_RELAIS>(.*)</CODE_RELAIS>#",$page,$regs)){
							$code[$m] = $regs[1];
							$m++;
						}
					}
			}

			if($idcommande != ""){
				$query = mysql_query("SELECT * FROM $this->table WHERE id_commande=\"$idcommande\"");
				$nbres = mysql_num_rows($query);

				if(!$nbres) return "";

				$row = mysql_fetch_object($query);
				$temp = str_replace("#NOM",$row->nom,$texte);
				$temp = str_replace("#CPOSTAL",$row->cpostal,$temp);
				$temp = str_replace("#VILLE",$row->ville,$temp);
				$temp = str_replace("#ADRESSE",$row->adresse,$temp);
				$temp = str_replace("#CODE", $row->code, $temp);
				$res .= $temp;
			} else {
				if(empty($nom)) return "";
				else {
					for($i=0; $i<count($nom); $i++) {
						$temp = str_replace("#NOM", $nom[$i], $texte);
						$temp = str_replace("#CPOSTAL", $cpostal[$i], $temp);
						$temp = str_replace("#VILLE", $ville[$i], $temp);
						$temp = str_replace("#ADRESSE", $adresse[$i], $temp);
						$temp = str_replace("#CODE", $code[$i], $temp);
						$res .= $temp;
					}
				}
			}
			return $res;
		}

		function statut($commande) {
			if($commande->statut == 2) {
				$query = mysql_query("SELECT * FROM $this->table WHERE id_commande = \" $commande->id \" ");
				$row = mysql_fetch_object($query);
				/*
				$variable = new Variable();
				$variable->charger("tntid");
				*/
				$client = new Client();
				$client->charger_id($commande->client);

				$vente = new Venteprod();
				$poids = 0;
				$query = mysql_query("SELECT * FROM $vente->table WHERE commande = \" $commande->id\" ");
				$produit = new Produit();

				while($rowvente = mysql_fetch_object($query)) {
					$produit->charger($rowvente->ref);
					$poids += ($produit->poids * $rowvente->quantite);
				}

				if(strlen($poids) == 3 ) $poids = "000".$poids;
				if(strlen($poids) == 4 ) $poids = "00".$poids;
				if(strlen($poids) == 5 ) $poids = "0".$poids;

				$nomfic = SITE_DIR."client/plugins/".self::MODULE."/fic".$commande->id.".TXT";
				$nomfictemp = SITE_DIR."client/plugins/".self::MODULE."/fic".$commande->id.".TEM";
				$fic = fopen($nomfic,"w+");
				$fictemp = fopen($nomfictemp,"w+");
				$nom = rtrim($row->nom);
				$adresse = rtrim($row->adresse);
				$ville = rtrim($row->ville);
				$contenu = "\"$variable->valeur\" ".";"."\"C\";"."\"$poids\"".";"."\"\"".";"."\"0\"".";"."\"\"".";"."\"$row->code\"".";"."\"$nom\"".";"."\"$adresse\"".";"."\"$ville\"".";"."\"FR\"".";"."\"AD\"".";"."\"$client->nom $client->prenom\"".";"."\"$client->telfixe     \"";
				fwrite($fic,$contenu);
				fwrite($fictemp,"aaa");
			}
		}

		function confirmation($commande) { $this->statut($commande); }

		function calcule() {
			/*
			if($this->poids >= 0 && $this->poids <=5) return round( ((15.35*1.196)+(1.70*1.196)+0.39),2);
			else if($this->poids > 5  && $this->poids <=10) return round( ((17.21*1.196)+(1.70*1.196)+0.39),2);
			else if($this->poids > 10  && $this->poids <=15) return round( ((20.31*1.196)+(1.70*1.196)+0.39),2);
			else if($this->poids > 15  && $this->poids <=20) return round( ((23.41*1.196)+(1.70*1.196)+0.39),2);
			else if($this->poids > 20  && $this->poids <=25) return round( ((26.51*1.196)+(1.70*1.196)+0.39),2);
			else if($this->poids > 25  && $this->poids <=30) return round( ((29.61*1.196)+(1.70*1.196)+0.39),2);
			else if($this->poids > 30) return roud( ((29.61*1.196)+(1.70*1.196)+0.39+((($this->poids-30)*0.62)*1.196)),2);
			*/
			require_once(SITE_DIR."client/plugins/".self::MODULE."/config.php");
			return tntrelais_calcule($this->zone,$this->nbart,$this->total,$this->poids);
		}
	}
?>
