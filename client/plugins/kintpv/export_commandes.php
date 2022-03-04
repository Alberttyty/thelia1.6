<?php
require_once(realpath(dirname(__FILE__)) . "/../../../gestion/pre.php");
require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Client.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Venteprod.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Paysdesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Modules.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Promoutil.class.php");
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Modulesdesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../declibre/Declibre.class.php");
require_once(realpath(dirname(__FILE__)) . "/../declibre/classes/Declibredesc.class.php");
require_once(realpath(dirname(__FILE__)) . "/../declibre/classes/Ventedeclibre.class.php");
require_once(realpath(dirname(__FILE__)) . "/../remisefidelite/Remisefidelite.class.php");
require_once(realpath(dirname(__FILE__)) . "/Kintpv.class.php");

function clean($texte){
    return mb_convert_encoding($texte,"WINDOWS-1252","utf-8");
}

function lastremplace($search, $replace, $subject){
    $pos = strrpos($subject, $search);
    if($pos !== false){
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

autorisation("kintpv");

header("Content-Type: text/xml; charset=windows-1251");
header("Content-disposition: attachment; filename=export_commandes" . ".xml");

$var = new Variable();
$var->charger("ebp-dernier-export");
$dernier_export=$var->valeur;

$produit=new Produit();
$venteprod=new Venteprod();

$commande=new Commande();
$query = "select * from $commande->table where date>=\"$dernier_export\" and statut<5";
$resul = $commande->query($query);

echo '<'.'?xml version="1.0" encoding="Windows-1252"?'.'>';
echo "<EBP_EToE><Pieces>";

while ($resul && $row = $commande->fetch_object($resul)) {

  echo "<Piece>
          <Type>21</Type>";

  echo "<NumeroPrefixe>CC Site</NumeroPrefixe>
        <NumeroNumero>$row->id</NumeroNumero>";

  echo "<Date>".substr($row->date, 8, 2) . "/" . substr($row->date, 5, 2) . "/" . substr($row->date, 0, 4)."</Date>";

  $client=new Client();
  $client->getVars("select * from $client->table where id=\"$row->client\"");
  $paysdesc = new Paysdesc();
	$paysdesc->charger($client->pays);

  echo "<TiersCode>Client Site ".$client->ref."</TiersCode>
        <TiersRaisonSoc>".clean($client->entreprise)." ".clean($client->nom)." ".clean($client->prenom)."</TiersRaisonSoc>
        <TiersAdresse1Ligne>".clean($client->adresse1)." ".clean($client->adresse2)." ".clean($client->adresse3)."</TiersAdresse1Ligne>
  			<TiersAdresse1CodePo>$client->cpostal</TiersAdresse1CodePo>
  			<TiersAdresse1Ville>".clean($client->ville)."</TiersAdresse1Ville>
  			<TiersAdresse1CodePa>".clean($paysdesc->titre)."</TiersAdresse1CodePa>";

  echo "<TiersAdresse2Ligne>".clean($client->adresse1)." ".clean($client->adresse2)." ".clean($client->adresse3)."</TiersAdresse2Ligne>
  			<TiersAdresse2CodePo>$client->cpostal</TiersAdresse2CodePo>
  			<TiersAdresse2Ville>".clean($client->ville)."</TiersAdresse2Ville>
  			<TiersAdresse2CodePa>".clean($paysdesc->titre)."</TiersAdresse2CodePa>";

  echo "<TiersDevise>EUR</TiersDevise>";

  echo "<TABTauxTVA>";
  $tva_query = "select distinct tva from $produit->table where 1";
  $tva_resul = $produit->query($tva_query);
  $i=1;
  $taux_tva=array();
  $total_tva=array();
  while ($tva_resul && $tva_row = $produit->fetch_object($tva_resul)) {
    echo "<TauxTVA idx=\"$i\">$tva_row->tva</TauxTVA>";
    $taux_tva[$i]=$tva_row->tva;
    $total_tva[$i]=0;
    $i=$i+1;
  }
  echo "</TABTauxTVA>";

  $query_total = " SELECT
						  sum(prixu*quantite) as totalttc,
						  sum(prixu*quantite / (1 + tva / 100)) as totalht
					    FROM $venteprod->table
					    where commande='$row->id'";
  $resul_total = $commande->query($query_total);
  $row_total = $commande->fetch_object($resul_total);

  $totalht=$row_total->totalht/*-$row->remise*/;
  if($totalht<0)$totalht=0;
  $totalttc=$row_total->totalttc/*-$row->remise*/;
  if($totalttc<0)$totalttc=0;
  $totalttcavecport=$row->port+$totalttc-$row->remise;
  if($totalttcavecport<0)$totalttcavecport=0;

  echo "<BrutHT>$totalht</BrutHT>";
  echo "<TotalBrutTTC>$totalttc</TotalBrutTTC>";

  echo "<NetAPayer>$totalttcavecport</NetAPayer>
       <FraisPort>$row->port</FraisPort>";

  if($row->remise>0){

    $tva = new Variable();
    $tva->charger("tva");
    $remiseht=$row->remise/(1+($tva->valeur/100));

    //echo "<bRemiseValeur>1</bRemiseValeur>";
    echo "<MontantRemise>$remiseht</MontantRemise>
          <MontantRemiseTTC>$row->remise</MontantRemiseTTC>";

  }

  $lignes_query = " SELECT * FROM $venteprod->table where commande='$row->id'";
  $lignes_resul = $commande->query($lignes_query);

  echo "<Lignes>";

  $i=0;

  while ($lignes_resul && $lignes_row = $produit->fetch_object($lignes_resul)) {
    echo "<Ligne n=\"$i\">";

    $prixht=$lignes_row->prixu/(1+($lignes_row->tva/100));
    $codetva = array_search($lignes_row->tva,$taux_tva);
    $total_tva[$codetva]=$total_tva[$codetva]+$prixht;

    $ventedeclibre=new Ventedeclibre();
    if($ventedeclibre->charger_prod($lignes_row->id))
    {
      echo "<SLigneCodeArtMaster>$lignes_row->ref</SLigneCodeArtMaster>";
      $declibre=new Declibre();
      $declibre->charger_id($ventedeclibre->declibre);
      $lignes_row->ref=$declibre->lien;
      $declibredesc=new Declibredesc();
      $declibredesc->charger($declibre->id);
      $lignes_row->titre=lastremplace($declibredesc->declinaison,'('.$declibredesc->declinaison.')',$lignes_row->titre);
    }

    echo "<TypeLigne>1</TypeLigne>
          <CodeArt>$lignes_row->ref</CodeArt>
		      <Libelle>".clean($lignes_row->titre)."</Libelle>
          <Quantite>$lignes_row->quantite</Quantite>
          <PxUnitBrut>$prixht</PxUnitBrut>
          <PxUnitBrutTTC>$lignes_row->prixu</PxUnitBrutTTC>
          <CodeTVA>$codetva</CodeTVA>
          <Nombre>1</Nombre>";

    echo "</Ligne>";
    $i=$i+1;
  }

  echo "</Lignes>";

  echo "<NbArticles>$i</NbArticles>";

  $i=0;

  foreach($taux_tva as $k => $v){
    $montant_tva=$total_tva[$k]*($v/100);
    echo "<STVA".$i."TauxTVA>$v</STVA".$i."TauxTVA>
          <STVA".$i."BaseTVA>$total_tva[$k]</STVA".$i."BaseTVA>
          <STVA".$i."MontantTVA>$montant_tva</STVA".$i."MontantTVA>";
    $i=$i+1;
  }

  $module=new Modules();
  $moduledesc=new Modulesdesc();
  $module->charger_id($row->paiement);
  $moduledesc->charger($module->nom);

  $module->nom=strtoupper($module->nom);

  $promoutil = new Promoutil();
  $promoutil->charger_commande($row->id);

  $chequecadeau = new Chequecadeau();
  $chequecadeau_query = " SELECT * FROM $chequecadeau->table where commande_utilise='$row->id'";
  $chequecadeau_resul = $commande->query($chequecadeau_query);

  $remisefidelite = new Remisefidelite();
  $remisefidelite_query = " SELECT * FROM $remisefidelite->table where id_commande='$row->id'";
  $remisefidelite_resul = $remisefidelite->query($remisefidelite_query);

  echo "<Paiements>
          <Paiement n=\"0\">
            <ModeRegl>".clean($module->nom)."</ModeRegl>";
            if($row->statut>1){
              echo "<Date>".substr($row->datefact, 8, 2) . "/" . substr($row->datefact, 5, 2) . "/" . substr($row->datefact, 0, 4)."</Date>
                    <Montant>$totalttcavecport</Montant>
                    <MontantDevise>$totalttcavecport</MontantDevise>";
            }
            else {
              echo "<Date></Date>
                    <Montant>0</Montant>
                    <MontantDevise>0</MontantDevise>";
            }
            echo "<Commentaire xml:space=\"preserve\">";
            if($promoutil->code!="") echo "Utilisation du code promo \"".$promoutil->code."\" d'une valeur de ".$promoutil->valeur;
            if($promoutil->type==1) echo " ";
            if($promoutil->type==2) echo " %";
            while ($chequecadeau_resul && $chequecadeau_row = $chequecadeau->fetch_object($chequecadeau_resul)) {
              echo "Utilisation du chèque cadeau N°".$chequecadeau_row->id." valeur : ".$chequecadeau_row->montant." 
              ";
            }
            while ($remisefidelite_resul && $remisefidelite_row = $remisefidelite->fetch_object($remisefidelite_resul)) {
              echo " Remise fidelite : ".number_format($remisefidelite_row->remise,2,'.','')." €
              ";
            }
            echo "</Commentaire>
            <Devise>EUR</Devise>
            <DevisePiece>EUR</DevisePiece>
            <DernierCours>1</DernierCours>
          </Paiement>
        </Paiements>";

  echo "</Piece>";

}

echo "</Pieces></EBP_EToE>";

$var = new Variable();
$var->charger("ebp-dernier-export");
$var->valeur=date("Y-m-d H:i:s");
$var->maj();

?>
