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

// Renvoie le chemin pour aller à une hierarchie d'objets, avec protection contre les
// références circulaires dans la hiérarchie d'objets.
function _cheminprotect($id, $nomobj, $lang = false) {

    $ids = array();

    $tab = array();

    $tobj = new $nomobj();
    $tobj->parent = $id;

    $objdesc = $nomobj . "desc";

    // On doit toujours retourner au moins un element, sinon bouclage dans l'admin...
    $tab[] = new $objdesc();

    $idx = 0;

    while (intval($tobj->parent) != 0) {
        $ids[] = $tobj->parent;

        $tobjdesc = new $objdesc();

        if (intval($tobj->parent) > 0 && $tobj->charger($tobj->parent) && $tobjdesc->charger($tobj->id, $lang)) {
            if (in_array($tobj->parent, $ids)) {
                die("Référence circulaire détectée dans la hiérarchie des ${nomobj}s à partir de l'ID=$tobj->id. Chemin: " . implode(" -&gt; ", $ids));
            }

            $tab[$idx++] = $tobjdesc;
        }
        else {
            // Chargement impossible -> arrêt
            break;
        }

    }

    return $tab;
}

// renvoie le chemin pour aller à une rubrique donnée
function chemin_rub($id, $lang = false) {
    return _cheminprotect($id, 'Rubrique', $lang);
}

// renvoie le chemin vers un dossier
function chemin_dos($id, $lang = false) {
    return _cheminprotect($id, 'Dossier', $lang);
}


// hiérarchie des rubriques
function arbreBoucle($depart, $profondeur = 0, $i = 0) {
    $rec = "";
    $i++;
    if ($i == $profondeur && $profondeur != 0)
        return;
    $trubrique = new Rubrique();

    $query = "select * from $trubrique->table where parent=\"$depart\"";
    $resul = CacheBase::getCache()->query($query);

    if ($resul == "" || count($resul) == 0)
        return "";
    foreach ($resul as $row) {
        $rec .= $row->id . ",";
        $rec .= arbreBoucle($row->id, $profondeur, $i);

    }

    return $rec;
}

// changement de rubrique
function arbreOption($depart, $niveau, $prubrique, $aenfant = 0, $lang = false) {

    $rec = "";
    $espace = "";

    $niveau++;
    $trubrique = new Rubrique();

    $query = "select * from $trubrique->table where parent=\"$depart\"";
    $resul = CacheBase::getCache()->query($query);

    for ($i = 0; $i < $niveau; $i++)
        $espace .= "&nbsp;&nbsp;&nbsp;";

    if ($resul == "" || count($resul) == 0)
        return "";

    foreach ($resul as $row) {
        $trubriquedesc = new Rubriquedesc();
        $trubriquedesc->charger($row->id, $lang);

        if (! $trubriquedesc->affichage_back_office_permis()) continue;

        $trubrique->charger($trubriquedesc->rubrique);

        if ($prubrique == $row->id)
            $selected = "selected";
        else
            $selected = "";
        if ($aenfant) {
            if (!$trubrique->aenfant()) {
                $rec .= "<option value=\"$row->id\" $selected>" . $espace . $trubriquedesc->titre . "</option>";
            }
        }
        else {
            $rec .= "<option value=\"$row->id\" $selected>" . $espace . $trubriquedesc->titre . "</option>";
        }

        $rec .= arbreOption($row->id, $niveau, $prubrique, $aenfant, $lang);

    }


    return $rec;
}

function arbreOptionRub($depart, $niveau, $prubrique, $nbprod = 0, $ok = 1, $lang = false) {

    $rec = "";
    $espace = "";

    $niveau++;
    $trubrique = new Rubrique();

    $query = "select * from $trubrique->table where parent=\"$depart\"";
    $resul = CacheBase::getCache()->query($query);

    for ($i = 0; $i < $niveau; $i++)
        $espace .= "&nbsp;&nbsp;&nbsp;";

    if ($resul == "" || count($resul) == 0)
        return "";

    foreach ($resul as $row) {
        $trubriquedesc = new Rubriquedesc();
        $trubriquedesc->charger($row->id, $lang);

        if (! $trubriquedesc->affichage_back_office_permis()) continue;

        $trubrique->charger($trubriquedesc->rubrique);
        $courante = new Rubrique();
        $courante->charger($prubrique);
        if ($courante->parent == $row->id)
            $selected = "selected";
        else
            $selected = "";


        if ($ok == 0 || ($row->id == $prubrique && $ok != -1))
            $disabled = "disabled=\"disabled\"";
        else
            $disabled = "";


        if (($nbprod && $trubrique->nbprod()) || !$nbprod)
            $rec .= "<option value=\"$row->id\" $disabled $selected>" . $espace . $trubriquedesc->titre . "</option>";

        if (($prubrique == $row->id && $ok != -1) || $ok == 0)
            $rec .= arbreOptionRub($row->id, $niveau, $prubrique, $nbprod, 0, $lang);
        else
            $rec .= arbreOptionRub($row->id, $niveau, $prubrique, $nbprod, $ok, $lang);
    }


    return $rec;
}


// hiérarchie des dossiers
function arbreBoucle_dos($depart, $profondeur = 0, $i = 0) {

    $rec = "";

    $i++;
    if ($i == $profondeur && $profondeur != 0)
        return;
    $tdossier = new Dossier();

    $query = "select * from $tdossier->table where parent=\"$depart\"";
    $resul = CacheBase::getCache()->query($query);

    if ($resul == "" || count($resul) == 0)
        return "";
    foreach ($resul as $row) {
        $rec .= $row->id . ",";
        $rec .= arbreBoucle_dos($row->id, $profondeur, $i);

    }

    return $rec;
}

// changement de dossier
function arbreOption_dos($depart, $niveau, $pdossier, $dossier, $ok = 1, $lang = false) {
    $niveau++;
    $tdossier = new Dossier();

    $query = "select * from $tdossier->table where parent=\"$depart\"";
    $resul = CacheBase::getCache()->query($query);

    for ($i = 0; $i < $niveau; $i++)
        $espace .= "&nbsp;&nbsp;&nbsp;";

    if ($resul == "" || count($resul) == 0)
        return "";
    foreach ($resul as $row) {
        $tdossierdesc = new Dossierdesc();
        $tdossierdesc->charger($row->id, $lang);

        if (! $tdossierdesc->affichage_back_office_permis()) continue;

        if ($pdossier == $row->id)
            $selected = "selected=\"selected\"";
        else
            $selected = "";

        if ($ok == 0 || ($row->id == $dossier && $ok != -1))
            $disabled = "disabled=\"disabled\"";
        else
            $disabled = "";

        $rec .= "<option value=\"$row->id\" $disabled $selected>" . $espace . $tdossierdesc->titre . "</option>";

        if (($dossier == $row->id && $ok != -1) || $ok == 0)
            $rec .= arbreOption_dos($row->id, $niveau, $pdossier, $dossier, 0, $lang);
        else
            $rec .= arbreOption_dos($row->id, $niveau, $pdossier, $dossier, $ok, $lang);

    }


    return $rec;
}

?>