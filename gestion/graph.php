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
require_once(__DIR__ . "/pre.php");
require_once(__DIR__ . "/auth.php");

$cnx = new Cnx();

function get_result($query) {
    global $cnx;

    $resul = $cnx->query($query);

    return $resul ? $cnx->get_result($resul, 0, 0) : 0;
}

function ca_mois($mois, $annee, $jour, $pourc=100, $port=1) {
    global $cnx;

    if ($jour != "%") $jour = sprintf("%02d", $jour);
    if ($mois != "%") $mois = sprintf("%02d", $mois);
    if ($annee != "%") $annee = sprintf("%04d", $annee);

    $date = "$annee-$mois-$jour";

    $query = "select * from ".Commande::TABLE." where statut >= ".Commande::PAYE." and statut <> ".Commande::ANNULE." and datefact like '$date'";

    $resul = $cnx->query($query);

    $list = array();

    while($resul && $row = $cnx->fetch_object($resul)) {
            $list[] = $row->id;
    }

    if (count($list) == 0) $list = '0';
    else $list = implode(',', $list);

    $ca  = round(get_result("SELECT sum(quantite*prixu) as camois FROM ".Venteprod::TABLE." where commande in ($list)"), 2);
    $ca += get_result("SELECT sum(port) as port FROM ".Commande::TABLE." where id in ($list)");
    $ca -= get_result("SELECT sum(remise) as remise FROM ".Commande::TABLE." where id in ($list)");

    if (! $port) {
        $ca -= get_result("SELECT sum(port) as port FROM ".Commande::TABLE." where id in ($list)");
    }

    return round($ca * $pourc / 100, 2);
}

function getmonth($mois){
    switch($mois){
        case "1" : return trad('janvier', 'admin') . " "; break;
        case "2" : return trad('fevrier', 'admin') . " "; break;
        case "3" : return trad('mars', 'admin') . " "; break;
        case "4" : return trad('avril', 'admin') . " "; break;
        case "5" : return trad('mai', 'admin') . " "; break;
        case "6" : return trad('juin', 'admin') . " "; break;
        case "7" : return trad('juillet', 'admin') . " "; break;
        case "8" : return trad('aout', 'admin') . " "; break;
        case "9" : return trad('septembre', 'admin') . " "; break;
        case "10" : return trad('octobre', 'admin') . " "; break;
        case "11" : return trad('novembre', 'admin') . " "; break;
        case "12" : return trad('decembre', 'admin') . " "; break;
    }
}

$mois = date("m");
$annee = date("Y");
$jours = date("t");

$values = array();
$days = array();

for($i=1;$i<=$jours;$i++) {
        $values[] = ca_mois($mois, $annee, "$i", 100, 1);
        $days[] = $i;
}

require_once("../lib/artichow/LinePlot.class.php");

$graph = new archiGraph(968, 200);

$graph->border->hide();
$graph->title = new archiLabel(
    trad('progression_graph', 'admin') . " " .getmonth($mois)." : ".ca_mois($mois, $annee, "%", 100, 1)." €" ,
    new archiFileFont(ARTICHOW_FONT.'/Arial', 10)
);

$plot = new archiLinePlot($values);
$plot->setAbsSize(968, 200);
//	$plot->setBackgroundColor(new archiColor(240, 240, 240));
$plot->hideLine(TRUE);
$plot->setFillColor(new archiColor(180, 180, 180, 75));
//$plot->grid->setBackgroundColor(new archiColor(235, 235, 180, 60));
$plot->grid->hideVertical();


/*
$plot->setBackgroundGradient(
                new archiLinearGradient(
                         new archiColor(210, 210, 210),
                         new archiColor(250, 250, 250),
                         0
                )
 );
*/

$plot->yAxis->setLabelPrecision(2);
$plot->xAxis->setLabelText($days);

$plot->mark->setType(archiMark::IMAGE);
$plot->mark->setImage(new archiFileImage("gfx/point_graph.png"));

$plot->label->set($values);
$plot->label->setColor(new archiColor(236, 128, 0));
$plot->label->move(0, -12);
$plot->label->setFont(new archiFileFont(ARTICHOW_FONT.'/Arial', 8));

$plot->label->setPadding(3, 1, 1, 0);

$plot->setSpace(2, 2, NULL, NULL);

$graph->add($plot);
$graph->draw();
?>
