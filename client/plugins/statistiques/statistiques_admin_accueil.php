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

include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("statistiques");

?>
<div class="statistiques">
<?php

  require_once(__DIR__ . '/../../../fonctions/authplugins.php');
  autorisation("statistiques");
  
  if(isset($_REQUEST['date'])&&isset($_REQUEST['date_fin'])){
    $date = new DateTime($_REQUEST['date']);
    $date_fin = new DateTime($_REQUEST['date_fin']);
    if($date_fin<$date) 
    {
      $date_fin->modify($date->format('Y-m-d'));
      $date_fin->modify('+1 day');
    }
  }
  else {
    $date = new DateTime('first day of');
    $date_fin = new DateTime('last day of');
  }
  
  $valeurs="?date=".$date->format('Y-m-d');
  $valeurs.="&amp;date_fin=".$date_fin->format('Y-m-d');
  
  echo '<form action="accueil.php" method="get">Du <label for="date" class="date">'.$date->format('d/m/y').'</label> au <label for="date_fin" class="date">'.$date_fin->format('d/m/y').'</label><input type="hidden" id="date" name="date" value="'.$date->format('Y-m-d').'"/><input type="hidden" id="date_fin" name="date_fin" value="'.$date_fin->format('Y-m-d').'"/></form>';
  
  echo '<div class="graph">';
  
  $date_fleches=clone($date);
  $date_fleches->modify('+1 month');
  $date_fleches = new DateTime('first day of '.$date_fleches->format('F'));
  $date_fleches_fin = new DateTime('last day of '.$date_fleches->format('F'));
  
  echo '<a class="next" href="accueil.php?date='.$date_fleches->format('Y-m-d').'&amp;date_fin='.$date_fleches_fin->format('Y-m-d').'">&raquo;</a>';
  
  $date_fleches->modify('-2 months');
  $date_fleches = new DateTime('first day of '.$date_fleches->format('F'));
  $date_fleches_fin = new DateTime('last day of '.$date_fleches->format('F'));
  
  echo '<a class="prev" href="accueil.php?date='.$date_fleches->format('Y-m-d').'&amp;date_fin='.$date_fleches_fin->format('Y-m-d').'">&laquo;</a>';
  
  echo '<img src="../client/plugins/statistiques/graph.php'.$valeurs.'" alt="" />';
  
  echo '</div>';
  
  echo '<ul class="donnees">';
  
  $cond_commande_paye = "statut >= ".Commande::PAYE." and statut <> ".Commande::ANNULE;
  $in_commande_paye = "select id from ".Commande::TABLE." where $cond_commande_paye and datefact >= '".$date->format('Y-m-d')."' and datefact <= '".$date_fin->format('Y-m-d')."'";
  $ca_sans_port=get_result("SELECT sum(quantite*prixu) as ca FROM ".Venteprod::TABLE." where commande in ($in_commande_paye)");
  
  $ca = $ca_sans_port+get_result("SELECT sum(port) as ca FROM ".Commande::TABLE." where id in ($in_commande_paye)");
	$ca -= get_result("SELECT sum(remise) as ca FROM ".Commande::TABLE." where id in ($in_commande_paye)");
  
  $ca=number_format(round($ca,2),2,".","");
  
  echo '<li>Chiffre d\'affaire TTC : '.$ca.' &euro;</li>';
  
  $ca_sans_port=number_format(round($ca_sans_port,2),2,".","");
  
  echo '<li>Chiffre d\'affaire TTC hors frais de port : '.$ca_sans_port.' &euro;</li>';
  
  $nbCommande = get_result("SELECT count(*) as nbCommande FROM ".Commande::TABLE." where id in ($in_commande_paye)");

	echo '<li>Nombre de commande(s) : '.$nbCommande.'</li>';
  
  if($nbCommande > 0)
	$panierMoyen = round(($ca/$nbCommande),2);
	else
	$panierMoyen = 0;
  
  $panierMoyen=number_format(round($panierMoyen,2),2,".","");
  
  echo '<li>Panier moyen : '.$panierMoyen.' &euro;</li>';
  
  echo '</ul>';
  
  $produit=new Produit();
  
  $ca_produits=array();
  $rubrique_produits=array();
  $ca_rubriques=array();
  $titre_rubriques=array();
  $ca_clients=array();
  $nom_clients=array();
  
  $query = "SELECT sum(vp.quantite*vp.prixu) as ca,vp.ref as ref,p.rubrique,r.titre,c.ref as ref_client,c.nom,c.prenom,c.entreprise FROM ".Venteprod::TABLE." as vp,".Produit::TABLE." as p,".Rubriquedesc::TABLE." as r,".Commande::TABLE." as com,".Client::TABLE." as c where vp.commande in ($in_commande_paye) and vp.ref=p.ref and p.rubrique=r.rubrique and r.lang=1 and vp.commande=com.id and com.client=c.id GROUP BY vp.ref";
  $resul = $produit->query($query);
  if($resul){
    while($row = $produit->fetch_object($resul)){
      $ca_produits[$row->ref]=floatval(round($row->ca,2));
      $rubrique_produits[$row->ref]=$row->rubrique;
      if(!isset($ca_rubriques[$row->rubrique]))$ca_rubriques[$row->rubrique]=0;
      $ca_rubriques[$row->rubrique]+=floatval(round($row->ca,2));
      $titre_rubriques[$row->rubrique]=$row->titre;
      if(!isset($ca_clients[$row->ref_client]))$ca_clients[$row->ref_client]=0;
      $ca_clients[$row->ref_client]+=floatval(round($row->ca,2));
      $nom_clients[$row->ref_client]=$row->nom." ".$row->prenom." ";
      if($row->entreprise!="") $nom_clients[$row->ref_client].=" <em>(".$row->entreprise.")</em>";
    }  
  }
    
  /*
  Methode trop lente !
  $produits_commandes=$produit->query_liste("SELECT DISTINCT ref FROM ".Venteprod::TABLE." where commande in ($in_commande_paye)","Venteprod");
  
  foreach($produits_commandes as $key => $produit){
  
    if($produit->ref!=""){
      $query = "SELECT sum(vp.quantite*vp.prixu) as ca,p.rubrique,r.titre,c.id as id_client,c.nom,c.prenom,c.entreprise FROM ".Venteprod::TABLE." as vp,".Produit::TABLE." as p,".Rubriquedesc::TABLE." as r,".Commande::TABLE." as com,".Client::TABLE." as c where vp.ref='$produit->ref' and vp.commande in ($in_commande_paye) and vp.ref=p.ref and p.rubrique=r.rubrique and r.lang=1 and vp.commande=com.id and com.client=c.id";
      $resul = $produit->query($query);
      if($resul){
        $row = $produit->fetch_object($resul);
        $ca_produits[$produit->ref]=floatval(round($row->ca,2));
        if(!isset($ca_rubriques[$row->rubrique]))$ca_rubriques[$row->rubrique]=0;
        $ca_rubriques[$row->rubrique]+=floatval(round($row->ca,2));
        $titre_rubriques[$row->rubrique]=$row->titre;
        if(!isset($ca_clients[$row->id_client]))$ca_clients[$row->id_client]=0;
        $ca_clients[$row->id_client]+=floatval(round($row->ca,2));
        $nom_clients[$row->id_client]=$row->nom." ".$row->prenom." ";
        if($row->entreprise!="") $nom_clients[$row->id_client].=" (".$row->entreprise.")";
      }
    }
  }*/     
  
  arsort($ca_produits);
  arsort($ca_rubriques);
  arsort($ca_clients);
  
  $tout="";
  if(isset($_REQUEST['ca_produits'])) $tout=$_REQUEST['ca_produits'];
  if($tout=="tout") $nb=9999;
  else $nb=10;
  
  echo '<ul class="donnees donnees_33"><li><h2>CA TTC par référence</h2><ol class="donnees_liste '.$tout.'">';
  
  foreach(array_slice($ca_produits,0,$nb,true) as $key => $ca_produit){
  
    echo '<li><ul><li><a href="produit_modifier.php?ref='.$key.'&amp;rubrique='.$rubrique_produits[$key].'">'.$key.'</a></li><li>'.number_format(round($ca_produit,2),2,".","").' &euro;</li></ul></li>';
  
  }  
  
  echo '</ol>';
  if($tout!="tout") echo '<a class="suite" href="accueil.php?date='.$date->format('Y-m-d').'&amp;date_fin='.$date_fin->format('Y-m-d').'&amp;ca_produits=tout">Voir tout</a>';
  
  $tout="";
  if(isset($_REQUEST['ca_rubriques'])) $tout=$_REQUEST['ca_rubriques'];
  if($tout=="tout") $nb=9999;
  else $nb=10;
  
  echo '</li><li><h2>CA TTC par rubrique</h2><ol class="donnees_liste '.$tout.'">';
  
  foreach(array_slice($ca_rubriques,0,$nb,true) as $key => $ca_rubrique){
  
    echo '<li><ul><li><a href="parcourir.php?parent='.$key.'">'.$titre_rubriques[$key].'</a></li><li>'.number_format(round($ca_rubrique,2),2,".","").' &euro;</li></ul></li>';
  
  }  
  
  echo '</ol>';
  if($tout!="tout") echo '<a class="suite" href="accueil.php?date='.$date->format('Y-m-d').'&amp;date_fin='.$date_fin->format('Y-m-d').'&amp;ca_rubriques=tout">Voir tout</a>';
  
  $tout="";
  if(isset($_REQUEST['ca_clients'])) $tout=$_REQUEST['ca_clients'];
  if($tout=="tout") $nb=9999;
  else $nb=10;
  
  echo '</li><li><h2>CA TTC par client <em>hors port et hors remise</em></h2><ol class="donnees_liste '.$tout.'">';
  
  foreach(array_slice($ca_clients,0,$nb,true) as $key => $ca_client){
  
    echo '<li><ul><li><a href="client_visualiser.php?ref='.$key.'">'.ucwords(mb_strtolower($nom_clients[$key])).'</a></li><li>'.number_format(round($ca_client,2),2,".","").' &euro;</li></ul></li>';
  
  }  
  
  echo '</ol>';
  if($tout!="tout") echo '<a class="suite" href="accueil.php?date='.$date->format('Y-m-d').'&amp;date_fin='.$date_fin->format('Y-m-d').'&amp;ca_clients=tout">Voir tout</a>';
  
  echo '</li></ul>';
  
?>

</div>