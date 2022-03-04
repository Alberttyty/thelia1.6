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
require_once("pre.php");
require_once("auth.php");

if(!isset($action)) $action="";

if(! est_autorise("acces_configuration")) exit;

if($action == "modifier"){

    $administrateur = new Administrateur();

    $administrateur->charger_id($id);
    $administrateur->valeur = $valeur;
    $administrateur->identifiant = $identifiant;
    $motdepasse1 = trim($motdepasse1);
    $motdepasse2 = trim($motdepasse2);

    if(trim($motdepasse1) != "" && trim($motdepasse1)==trim($motdepasse2)){
            $administrateur->motdepasse = $motdepasse1;
            $administrateur->crypter();
    }
    $administrateur->nom = $nom;
    $administrateur->prenom = $prenom;
    $administrateur->lang = $lang;

    $administrateur->maj();

    if($_SESSION["util"]->id == $administrateur->id){
            $_SESSION["util"] = $administrateur;
    }

    if(trim($motdepasse1) != "" && trim($motdepasse1)==trim($motdepasse2)){
        ?>
        <script type="text/javascript">
        alert("Mot de passe change avec succes");
        location = "gestadm.php";
        </script>
        <?php
    } else {
        redirige("gestadm.php");

    }
}

if($action == "ajouter"){

    $admin = new Administrateur();

    $admin->valeur = $valeur;
    $admin->nom = $nom;
    $admin->prenom = $prenom;
    $admin->identifiant = $identifiant;
    $motdepasse1 = trim($motdepasse1);
    $admin->motdepasse = $motdepasse1;
    $admin->profil = $_POST['profil'];
    $admin->crypter();
    $lastid = $admin->add();

    $autorisation_profil = new Autorisation_profil();
    $query = "select * from $autorisation_profil->table where profil=\"" . $_POST['profil'] . "\"";
    $resul = mysql_query($query, $autorisation_profil->link);
    while($row = mysql_fetch_object($resul)){
        $autorisation_administrateur = new Autorisation_administrateur();
        $autorisation_administrateur->administrateur = $lastid;
        $autorisation_administrateur->autorisation = $row->autorisation;
        $autorisation_administrateur->lecture = $row->lecture;
        $autorisation_administrateur->ecriture = $row->ecriture;
        $autorisation_administrateur->add();
    }

    redirige("gestadm.php");
}

if($action == "supprimer"){

    $autorisation_administrateur = new Autorisation_administrateur();
    $query = "delete from $autorisation_administrateur->table where administrateur=\"$id\"";
    $resul = mysql_query($query, $autorisation_administrateur->link);

    $admin = new Administrateur();
    $admin->charger_id($id);
    $admin->delete();

    redirige("gestadm.php");
}

?>
