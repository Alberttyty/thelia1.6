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
require_once(__DIR__ . "/../fonctions/divers.php");


class PluginsClassiques extends Baseobj
{
    public $nom_plugin;
    public $moduledesc;

    function __construct($nom="")
    {
        parent::__construct();
        // Pour les plugins étourdis
        if ($nom == "") $nom = strtolower(get_class($this));

        $this->nom_plugin = $nom;
        $this->modulesdesc = new Modulesdesc($this->getNom());
    }

    /* Compatibilité avec les anciens plugins */
    function PluginsClassiques($nom="")
    {
        self::__construct($nom);
     }

    function prerequis()
    {
        return true;
    }

    function init()
    {}

    function destroy()
    {}

    /*
     * Retourne le nom du module tel que présent en BD
     */
    function getNom()
    {
        // Le nom du module en base est toujours le nom du repertoire,
        // du module et pas forcément $this->nom_plugin, parfois fantaisiste.
        // On utilise le nom de classe pour retrouver, car nom de classe =
        // nom du répertoire avec la 1ere lettre en majuscules.
        return strtolower(get_class($this));
    }

    function getTitre()
    {
        return $this->modulesdesc->titre;
    }

    function getChapo()
    {
        return $this->modulesdesc->chapo;
    }

    function getDescription()
    {
        return $this->modulesdesc->description;
    }

    function ajout_desc($titre, $chapo, $description, $lang=1, $devise="")
    {
        ActionsModules::instance()->mise_a_jour_description($this->nom_plugin, $lang, $titre, $chapo, $description, $devise);
    }

    function demarrage() {}

    function inclusion() {}

    function pre() {}

    function action() {}

    function boucle($texte, $args) {}

    function post() {}

    function apres() {}

    function avantcommande() {}

    function aprescommande($commande){}

    function mail() {}

    function avantclient() {}

    function apresclient($client) {}

    function statut($commande) {}

    function confirmation($commande) {}

    function modprod($ref) {}

    function modrub($id) {}

    function clear_cache() {}

	  /**
     * point d'entrée appelé à la fin de l'appel de la méthode Panier::verifstock
     *
     * @param bool $stokok vaut TRUE si le stock est correct, FALSE sinon
     * @param array $parametres tableau des paramètres reçus par la méthode Panier::verifstock. Ce tableau a 3 clés : "refproduit", "quantite" et "perso"
     */
    function apresverifstock(&$stokok, &$parametres) {}
}

?>
