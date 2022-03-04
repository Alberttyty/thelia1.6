<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*		email : info@thelia.net                                                      */
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
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/
require_once __DIR__ . "/../fonctions/autoload.php";

class Client extends Baseobj
{
    public $id;
    public $ref;
    public $datecrea;
    public $raison;
    public $entreprise;
    public $siret;
    public $intracom;
    public $nom;
    public $prenom;
    public $telfixe;
    public $telport;
    public $email;
    public $motdepasse;
    public $adresse1;
    public $adresse2;
    public $adresse3;
    public $cpostal;
    public $ville;
    public $pays;
    public $parrain;
    public $type;
    public $pourcentage;
    public $lang;

    const TABLE = "client";
    public $table=self::TABLE;

    public $bddvars = array("id", "ref", "datecrea", "raison", "entreprise", "siret", "intracom", "nom", "prenom", "telfixe", "telport", "email", "motdepasse", "adresse1", "adresse2", "adresse3", "cpostal", "ville", "pays", "parrain", "type", "pourcentage", "lang");

    function __construct($id = 0)
    {
        parent::__construct();
        if ($id > 0) $this->charger_id($id);
    }

    function add()
    {
        $this->datecrea = date('Y-m-d H:i:s');
        $this->id = parent::add();
        $this->ref = date("ymdHi") . genid($this->id, 6);
        $this->maj();

        return $this->id;
    }

    function charger($email = null, $motdepasse = null)
    {
        if ($email != null && $motdepasse != null) {
            $query = sprintf("SELECT * FROM $this->table WHERE email='%s' AND motdepasse=PASSWORD('%s')",
            $this->escape_string($email),
            $this->escape_string($motdepasse));

            return $this->getVars($query);
        }
    }

    function charger_mail($email)
    {
        return $this->getVars("SELECT * FROM $this->table WHERE email=\"$email\"");
    }

    function existe($email)
    {
        $query = "SELECT * FROM $this->table WHERE email=\"$email\"";
        $resul = $this->query($query);

        return $this->num_rows($resul);
    }

    function crypter()
    {
        $query = "SELECT PASSWORD('$this->motdepasse') AS resultat";
        $resul = $this->query($query);
        $this->motdepasse = $this->get_result($resul, 0, "resultat");
    }

    function charger_ref($ref)
    {
        return $this->getVars("SELECT * FROM $this->table WHERE ref=\"$ref\"");
    }

    function acommande()
    {
        $commande = new Commande();
        $query = "SELECT * FROM $commande->table WHERE statut>1 AND statut<>5 AND client=\"" . $this->id . "\"";
        $resul = $commande->query($query);

        if ($commande->num_rows($resul)) return 1;
        else return 0;
    }

    function nbcommandes()
    {
        $commande = new Commande();
        $query = "SELECT * FROM $commande->table WHERE statut>1 AND statut<>5 AND client=\"" . $this->id . "\" AND statut<>5";
        $resul = $commande->query($query);

        return $commande->num_rows($resul);
    }
}

?>
