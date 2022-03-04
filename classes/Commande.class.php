<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/
require_once __DIR__ . "/../fonctions/autoload.php";

class Commande extends Baseobj
{
		// Les status des commandes
		const NONPAYE = 1;
		const PAYE = 2;
		const TRAITEMENT = 3;
		const EXPEDIE = 4;
		const ANNULE = 5;

		public $id;
		public $client;
		public $adrfact;
		public $adrlivr;
		public $date;
		public $datefact;
		public $ref;
		public $transaction;
		public $livraison;
		public $facture;
		public $transport;
		public $port;
		public $datelivraison;
		public $remise;
		public $devise;
		public $taux;
		public $colis;
		public $paiement;
		public $statut;
		public $lang;

		public $total;

		const TABLE="commande";
		public $table=self::TABLE;

		public $bddvars = array("id", "client", "adrfact", "adrlivr", "date", "datefact", "ref", "transaction", "livraison", "facture", "transport", "port", "datelivraison", "remise", "devise", "taux", "colis", "paiement", "statut", "lang");

		public function __construct($id = 0)
		{
				parent::__construct();
				if ($id > 0) $this->charger($id);
		}

    function add()
    {
        $this->date = date("Y-m-d H:i:s");
        $this->id = parent::add();
        $this->ref = "C" . date("ymdHi") . genid($this->id, 6);
        $this->livraison = "L" . date("ymdHi") . genid($this->id, 6);
        $this->maj();
        return $this->id;
    }

		public function charger($id = null, $var2 = null)
		{
				if ($id != null) return $this->getVars("SELECT * FROM $this->table WHERE id=\"$id\"");
		}

		public function charger_ref($ref)
		{
				return $this->getVars("SELECT * FROM $this->table WHERE ref=\"$ref\"");
		}

		public function charger_trans($transaction)
		{
				$hier = date("Y-m-d H:i:s", mktime()-86400);
				return $this->getVars("SELECT * FROM $this->table WHERE transaction=\"$transaction\" and date>\"$hier\"");
		}

		public function delete()
		{
				if (! empty($this->id)) {
	          $this->delete_cascade('Venteprod', 'commande', $this->id);
	          $this->delete_cascade('Venteadr', 'id', $this->adrfact);
	          $this->delete_cascade('Venteadr', 'id', $this->adrlivr);

	          parent::delete();
	      }
		}

		public function annuler()
		{
				if ($this->statut != Commande::ANNULE) {
						// On remet le stock si il a été défalqué
	          $this->defalquer_stock(true);

	          $ancienStatut = $this->statut;

	          $this->statut = Commande::ANNULE;
	          $this->maj();

	          ActionsModules::instance()->appel_module("statut", $this, $ancienStatut);
	    	}
		}

    /**
     *
     * retourne un tableau contenant toutes les venteprod sous forme d'objet
     *
     * @return Venteprod[]
     */
    public function getProduits()
    {
        $query = "SELECT * FROM ".Venteprod::TABLE." WHERE commande='".$this->id."'";
        return $this->query_liste($query, "Venteprod");
    }

		public function genfact()
		{
				if (! empty($this->facture)) return 0;

				$this->datefact = date("Y-m-d");

				$query = "SELECT MAX(facture) AS mfact FROM $this->table";
				$resul = $this->query($query);

				$this->facture = 1000;

				if ($resul) {
						$num = $this->get_result($resul, 0, "mfact");
						if ($num > 0) $this->facture = $num + 1;
				}

				// On defalque le stock si ça n'a pas été fait
				$this->defalquer_stock();
		}

		public function defalquer_stock($retourenstock = false)
		{
				try {
						$modules = new Modules();

						if ($modules->charger_id($this->paiement)) {

	              $modpaiement = ActionsModules::instance()->instancier($modules->nom);

	              if ($retourenstock) $defalquer = $modpaiement->defalqcmd != 0 || ($modpaiement->defalqcmd == 0 && $this->statut != self::NONPAYE);
	              else $defalquer = $modpaiement->defalqcmd == 0;

	              if ($defalquer) {
										$delta = $retourenstock ? 1 : -1;

	                  $venteprod = new Venteprod();
	                  $query = "SELECT * FROM $venteprod->table WHERE commande='" . $this->id . "'";
	                  $resul = $venteprod->query($query);

	                  while ($resul && $row = $venteprod->fetch_object($resul)) {
	                      // Mise à jour du stock général
	                      $produit = new Produit($row->ref);
	                      $produit->stock += ($delta * $row->quantite);
	                      $produit->maj();

	                      $vdec = new Ventedeclidisp();

	                      $query2 = "SELECT * FROM $vdec->table WHERE venteprod='" . $row->id . "'";
	                      $resul2 = $vdec->query($query2);

	                      while($resul2 && $row2 = $vdec->fetch_object($resul2)) {

	                          $stock = new Stock();

	                          // Mise à jour du stock des declinaisons
	                          if ($stock->charger($row2->declidisp, $produit->id)) {
	                              $stock->valeur += ($delta * $row->quantite);
	                              $stock->maj();
	                          }
	                      }
	                  }
	              }
	          }
				}
				catch (Exception $ex) {
						// Rien
				}
		}

		public function total($avec_port = false, $avec_remise = false)
		{
				$total = 0;

				$query = "SELECT SUM(prixu * quantite) AS total FROM ".Venteprod::TABLE." WHERE commande=\"" . $this->id . "\"";

				$resul = $this->query($query);

				if ($resul) {
						$total = round($this->get_result($resul, 0, "total"), 2);

						if ($avec_port) $total += $this->port;
						if ($avec_remise) $total -= $this->remise;
				}

				return $total;
		}

    public function setStatutAndSave($statut)
		{
        if ($statut == Commande::ANNULE) $this->annuler();
        else $this->updateStatut($statut);
		}

    private function updateStatut($statut)
		{
        $ancienStatut = $this->statut;

        $this->statut = $statut;

        if ($statut == Commande::PAYE && $this->facture == 0) $this->genfact();
        else if($statut == Commande::EXPEDIE && $this->datelivraison == "0000-00-00") $this->datelivraison = date("Y-m-d");

        $this->maj();

        ActionsModules::instance()->appel_module("statut", $this, $ancienStatut);
    }
}
?>
