<?php
	include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");

	autorisation("statsventespayes");

	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Venteadr.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Pays.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Paysdesc.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");

	set_time_limit(1200);
  
  $gentv=0;
  $gentvht=0;
  $genca=0;
  $gencaht=0;
  $gentauxtvagenerale=array();
  $genport=0;
  $genremise=0;
  $gennbventes=0;
  
  function calculCA($list,$pays="",$tva=0){
  
    $avectva_tv=0;
    $avectva_tvht=0;
    $avectva_remise=0;
    $avectva_port=0;
    $avectva_ca=0;
    $avectva_caht=0;
    $avectva_nbventes=0;
    
    $sanstva_tv=0;
    $sanstva_tvht=0;
    $sanstva_remise=0;
    $sanstva_port=0;
    $sanstva_ca=0;
    $sanstva_caht=0;
    $sanstva_nbventes=0;
    
    global $gentauxtvagenerale;
    $tauxtvagenerale=array();
    
    foreach (explode(',',$list) as $keycommande => $valuecommande)
    {
    
      $query = "SELECT venteprod.quantite,venteprod.prixu,venteprod.tva FROM venteprod where commande=$valuecommande";
      $resul = mysql_query($query);
      
      $totalitem=0;
      $totalitemht=0;
      //$sommetva=0;
      
      $tauxtva=array();
      
      while($row = mysql_fetch_object($resul)){
      
        $item = round($row->prixu,2);
        $item = $item * $row->quantite;
        $totalitem = $totalitem + $item;
        $itemht = $row->prixu/(1+($row->tva/100));
        $itemht = round($itemht,2);
        $itemht = $itemht * $row->quantite;
        $totalitemht = $totalitemht + $itemht;
        
        if(!isset($tauxtva[$row->tva]))$tauxtva[$row->tva]=0;
        $tauxtva[$row->tva]+=$item-$itemht;
        if(!isset($tauxtvagenerale[$row->tva]))$tauxtvagenerale[$row->tva]=0;
        $tauxtvagenerale[$row->tva]+=$item-$itemht;
        if(!isset($gentauxtvagenerale[$row->tva]))$gentauxtvagenerale[$row->tva]=0;
        $gentauxtvagenerale[$row->tva]+=$item-$itemht;
    
      }
      
      $query = "SELECT port as ca FROM commande where id=$valuecommande";
      $resul = mysql_query($query);
      $port = mysql_result($resul, 0, "ca");
  
      $query = "SELECT remise as ca FROM commande where id=$valuecommande";
      $resul = mysql_query($query);
      $remise = mysql_result($resul, 0, "ca");
      
      $valeurtva=new Variable;
      $valeurtva->charger('tva');
      
      $portht = $port/(1+($valeurtva->valeur/100));
      $portht = round($portht,2);
      $remiseht = $remise/(1+($valeurtva->valeur/100));
      $remiseht = round($remiseht,2);
      
      reset($tauxtva);
      $first_key = key($tauxtva);
      
      if((count($tauxtva)<=1) && ($first_key==0)){
      
        $sanstva_tv+=$totalitem;
        $sanstva_remise+=$remise;
        $sanstva_port+=$port;
        $sanstva_ca+=$totalitem+$port-$remise;
        $sanstva_nbventes+=1;
      
      }
        
      else {  
      
        $avectva_tv+=$totalitem;
        $avectva_tvht+=$totalitemht;
        $avectva_remise+=$remise;
        $avectva_port+=$port;
        $avectva_ca+=$totalitem+$port-$remise;
        $avectva_caht+=$totalitemht+$portht-$remiseht;
        $avectva_nbventes+=1;
      
      }

    }
    
    global $gensanstva_tv,$gensanstva_remise,$gensanstva_port,$gensanstva_ca,$gensanstva_nbventes; 
    global $genavectva_tv,$genavectva_tvht,$genavectva_remise,$genavectva_port,$genavectva_ca,$genavectva_caht,$genavectva_nbventes;
    
    $gensanstva_tv+=$sanstva_tv;
    $gensanstva_remise+=$sanstva_remise;
    $gensanstva_port+=$sanstva_port;
    $gensanstva_ca+=$sanstva_ca;
    $gensanstva_nbventes+=$sanstva_nbventes;
    
    $genavectva_tv+=$avectva_tv;
    $genavectva_tvht+=$avectva_tvht;
    $genavectva_remise+=$avectva_remise;
    $genavectva_port+=$avectva_port;
    $genavectva_ca+=$avectva_ca;
    $genavectva_caht+=$avectva_caht;
    $genavectva_nbventes+=$avectva_nbventes;
    
    $retour="";
    
    $retour.= '<div class="blocs_pliants_prod"><div class="entete"><div class="titre">';
    $retour.=mb_strtoupper ($pays, 'UTF-8'); 
		$retour.= '</div></div>';
    
    $retour.= '
		<ul class="lignesimple">
			<li class="cellule_designation" style="width:99%;text-align:center;">COMMANDES SOUMISES A LA TVA</li>
		</ul>
    ';
    
    $retour.= '
		<ul class="lignesimple">
			<li class="cellule_designation" style="width:330px;">Nombre de commandes payées</li>
			<li class="cellule">'.number_format($avectva_nbventes, 0, ',', ' ').'</li>
		</ul>
    ';    
    $retour.= '
     <ul class="lignesimple">
  			<li class="cellule_designation" style="width:330px;">Total articles HT</li>
  			<li class="cellule">'.number_format($avectva_tvht, 2, ',', ' ').' &euro;</li>
  	 </ul>';
     
     $sommetva=0;
     foreach($tauxtvagenerale as $keytauxtva => $valuetauxtva){
     if($valuetauxtva!=0){
     $retour.= '
     <ul class="lignesimple">
  			<li class="cellule_designation" style="width:330px;">TVA articles '.$keytauxtva.'%</li>
  			<li class="cellule">'.number_format($valuetauxtva, 2, ',', ' ').' &euro;</li>
  	 </ul>';
     $sommetva+=$valuetauxtva;
     }
     }
     
     $retour.= '
     <ul class="lignesimple">
  			<li class="cellule_designation" style="width:330px;">Total TVA articles</li>
  			<li class="cellule">'.number_format($sommetva, 2, ',', ' ').' &euro;</li>
  	 </ul>';
    
    $retour.= '
    <ul class="lignesimple">
			<li class="cellule_designation" style="width:330px;">Total articles TTC</li>
			<li class="cellule">'.number_format($avectva_tv, 2, ',', ' ').' &euro;</li>
		</ul>';
    
    $retour.= '
    <ul class="lignesimple">
			<li class="cellule_designation" style="width:330px;">Port TTC</li>
			<li class="cellule">'.number_format($avectva_port, 2, ',', ' ').' &euro;</li>
		</ul>';
    
    $retour.= '
    <ul class="lignesimple">
			<li class="cellule_designation" style="width:330px;">Remise TTC</li>
			<li class="cellule">'.number_format($avectva_remise, 2, ',', ' ').' &euro;</li>
		</ul>';
    
    $retour.= '
    <ul class="lignesimple">
			<li class="cellule_designation" style="width:330px;">CA TTC <span style="font-style:italic;">(total ttc + port ttc - remise ttc)</span></li>
			<li class="cellule">'.number_format($avectva_ca, 2, ',', ' ').' &euro;</li>
		</ul>';
    
    $retour.= '
     <ul class="lignesimple">
  			<li class="cellule_designation" style="width:330px;">CA HT <span style="font-style:italic;">(total ht + port ht - remise ht)</span></li>
  			<li class="cellule">'.number_format($avectva_caht, 2, ',', ' ').' &euro;</li>
  	 </ul>';
     
     $retour.= '
     <ul class="lignesimple">
  			<li class="cellule_designation" style="width:330px;">TOTAL TVA <span style="font-style:italic;">(ca ttc - ca ht)</span></li>
  			<li class="cellule">'.number_format($avectva_ca-$avectva_caht, 2, ',', ' ').' &euro;</li>
  	 </ul>';
     
     $retour.= '
		<ul class="lignesimple">
			<li class="cellule_designation" style="width:99%;text-align:center;">COMMANDES NON SOUMISES A LA TVA</li>
		</ul>
    ';
    
    $retour.= '
		<ul class="lignesimple">
			<li class="cellule_designation" style="width:330px;">Nombre de commandes payées</li>
			<li class="cellule">'.number_format($sanstva_nbventes, 0, ',', ' ').'</li>
		</ul>
    ';    
    $retour.= '
     <ul class="lignesimple">
  			<li class="cellule_designation" style="width:330px;">Total articles HT</li>
  			<li class="cellule">'.number_format($sanstva_tv, 2, ',', ' ').' &euro;</li>
  	 </ul>';
    
    $retour.= '
    <ul class="lignesimple">
			<li class="cellule_designation" style="width:330px;">Port HT</li>
			<li class="cellule">'.number_format($sanstva_port, 2, ',', ' ').' &euro;</li>
		</ul>';
    
    $retour.= '
    <ul class="lignesimple">
			<li class="cellule_designation" style="width:330px;">Remise HT</li>
			<li class="cellule">'.number_format($sanstva_remise, 2, ',', ' ').' &euro;</li>
		</ul>';
    
    $retour.= '
    <ul class="lignesimple">
			<li class="cellule_designation" style="width:330px;">CA HT <span style="font-style:italic;">(total ht + port ht - remise ht)</span></li>
			<li class="cellule">'.number_format($sanstva_ca, 2, ',', ' ').' &euro;</li>
		</ul>';
     
     $retour.= '</div>';
    
    return $retour;
        
}
        
?>

	<div id="contenu_int">
	<p align="left"><a href="accueil.php" class="lien04">Accueil </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="module_liste.php" class="lien04">Modules</a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="#" class="lien04">CA des ventes</a>              
	</p>
	<!--  colonne de gauche -->
<div id="bloc_description">
<!-- titre -->
	<div class="entete_liste_config">
		<div class="titre">SELECTIONNER UNE PERIODE</div>
	</div>
<!-- entete -->
	<ul class="Nav_bloc_description">
		<li style="height:25px; width:245px;">DEBUT (JJ/MM/AAAA)</li>
		<li style="height:25px; width:260px; border-left:1px solid #96A8B5;">FIN (JJ/MM/AAAA)</li>
	</ul>
		<form action="module.php?nom=statsventespayes" method="get" id="form_ns" style="float:none;display:block;margin-bottom:10px;">
		<input type="hidden" name="nom" value="statsventespayes" />
		<div class="bordure_bottom">
		<ul class="ligne_claire_BlocDescription">
		    <li style="width:55px"><input type="text" size="2" name="datedj" id="datedj" value="<?php if($_REQUEST['datedj']<10&&substr($_REQUEST['datedj'],0,1)!="0") echo "0".$_REQUEST['datedj']; else echo $_REQUEST['datedj']; ?>"/></li>
		    <li style="border-left:1px solid #C4CACE; width:55px"><input type="text" size="2" name="datedm" id="datedm" value="<?php if($_REQUEST['datedm']<10&&substr($_REQUEST['datedm'],0,1)!="0") echo "0".$_REQUEST['datedm']; else echo $_REQUEST['datedm']; ?>"/></li>
			<li style="border-left:1px solid #C4CACE; width:55px"><input type="text" size="4" name="dateda" id="dateda" value="<?php if($_REQUEST['dateda']<10&&substr($_REQUEST['dateda'],0,1)!="0") echo "0".$_REQUEST['dateda']; else echo $_REQUEST['dateda']; ?>" /></li>
			<li style="border-left:1px solid #C4CACE; width:63px">00:00:00</li>
			<li style="border-left:1px solid #C4CACE; width:55px"><input type="text" size="2" name="datefj" id="datefj" value="<?php if($_REQUEST['datefj']<10&&substr($_REQUEST['datefj'],0,1)!="0") echo "0".$_REQUEST['datefj']; else echo $_REQUEST['datefj']; ?>"/></li>
			<li style="border-left:1px solid #C4CACE; width:55px"><input type="text" size="2" name="datefm" id="datefm" value="<?php if($_REQUEST['datefm']<10&&substr($_REQUEST['datefm'],0,1)!="0") echo "0".$_REQUEST['datefm']; else echo $_REQUEST['datefm']; ?>"/></li>
			<li style="border-left:1px solid #C4CACE; width:55px"><input type="text" size="4" name="datefa" id="datefa" value="<?php if($_REQUEST['datefa']<10&&substr($_REQUEST['datefa'],0,1)!="0") echo "0".$_REQUEST['datefa']; else echo $_REQUEST['datefa']; ?>"/></li>
			<li style="border-left:1px solid #C4CACE; width:63px">23:59:00</li>
			<li style="border-left:1px solid #C4CACE; width:65px;text-align:center;"><input type="submit" value="OK" /></li>
		</ul>
		</div>
    <div style="clear:both;"></div>		
		</form>
<?php
			if($_REQUEST['datedj'] != "" && $_REQUEST['datedm'] != "" && $_REQUEST['dateda'] != "" && $_REQUEST['datefj'] != "" && $_REQUEST['datefm'] != "" && $_REQUEST['datefa'] != "")
			{
				$debut = $_REQUEST['dateda'] . "-" . $_REQUEST['datedm'] . "-" . $_REQUEST['datedj'];
				$fin = $_REQUEST['datefa'] . "-" . $_REQUEST['datefm'] . "-" . $_REQUEST['datefj'];
			
				$search = " and commande.date>='$debut' and commande.date<='$fin'";
				
		    $commande = new Commande();
        $venteadr = new Venteadr();
        $pays = new Pays();
        $paysdesc = new Paysdesc();        
                
        $query = "select $commande->table.id as 'idcommande',$pays->table.id as 'idpays',$pays->table.tva from $commande->table left join $venteadr->table on ($commande->table.adrfact=$venteadr->table.id) left join $pays->table on ($pays->table.id=$venteadr->table.pays) where statut>=2 and statut<5 and datefact>='$debut 00:00:00' and datefact<='$fin 23:59:59'";
		    $resul = mysql_query($query);
        
        $listpays=array();
        
        $listgenerale="";
        
        while($row = mysql_fetch_object($resul)){
          if(!is_array($listpays[$row->idpays]))$listpays[$row->idpays]=array('tva'=>$row->tva,'commandes'=>array());
          array_push($listpays[$row->idpays]['commandes'],$row->idcommande); 
        }
        
        foreach ($listpays as $keypays => $valuepays){
        
          $list="";
          foreach ($valuepays['commandes'] as $key => $value){
          
            $list .= "'" . $value . "'" . ",";
            $listgenerale .= "'" . $value . "'" . ",";
          
          }
          $list = substr($list, 0, strlen($list)-1);

		      if($list == "") $list="''";
          
          $paysdesc->charger($keypays);
          
          echo calculCA($list,$paysdesc->titre,$valuepays['tva']);
        
        }
        ?>
        
        <div class="blocs_pliants_prod">
        
        <?php
        
        $valeurtva=new Variable;
        $valeurtva->charger('tva');
        
        $retour="";
        
        $retour.= '<div class="entete"><div class="titre">TOUT PAYS</div></div>';
        
        $retour.= '
    		<ul class="lignesimple">
    			<li class="cellule_designation" style="width:99%;text-align:center;">COMMANDES SOUMISES A LA TVA</li>
    		</ul>
        ';
        
        $retour.= '
    		<ul class="lignesimple">
    			<li class="cellule_designation" style="width:330px;">Nombre de commandes payées</li>
    			<li class="cellule">'.number_format($genavectva_nbventes, 0, ',', ' ').'</li>
    		</ul>
        ';    
        $retour.= '
         <ul class="lignesimple">
      			<li class="cellule_designation" style="width:330px;">Total articles HT</li>
      			<li class="cellule">'.number_format($genavectva_tvht, 2, ',', ' ').' &euro;</li>
      	 </ul>';
         
         $sommetva=0;
         foreach($gentauxtvagenerale as $keytauxtva => $valuetauxtva){
         if($valuetauxtva!=0){
         $retour.= '
         <ul class="lignesimple">
      			<li class="cellule_designation" style="width:330px;">TVA articles '.$keytauxtva.'%</li>
      			<li class="cellule">'.number_format($valuetauxtva, 2, ',', ' ').' &euro;</li>
      	 </ul>';
         $sommetva+=$valuetauxtva;
         }
         }
         
         $retour.= '
         <ul class="lignesimple">
      			<li class="cellule_designation" style="width:330px;">Total TVA articles</li>
      			<li class="cellule">'.number_format($sommetva, 2, ',', ' ').' &euro;</li>
      	 </ul>';
        
        $retour.= '
        <ul class="lignesimple">
    			<li class="cellule_designation" style="width:330px;">Total articles TTC</li>
    			<li class="cellule">'.number_format($genavectva_tv, 2, ',', ' ').' &euro;</li>
    		</ul>';
        
        $retour.= '
        <ul class="lignesimple">
    			<li class="cellule_designation" style="width:330px;">Port TTC</li>
    			<li class="cellule">'.number_format($genavectva_port, 2, ',', ' ').' &euro;</li>
    		</ul>';
        
        $retour.= '
        <ul class="lignesimple">
    			<li class="cellule_designation" style="width:330px;">Remise TTC</li>
    			<li class="cellule">'.number_format($genavectva_remise, 2, ',', ' ').' &euro;</li>
    		</ul>';
        
        $retour.= '
        <ul class="lignesimple">
    			<li class="cellule_designation" style="width:330px;">CA TTC <span style="font-style:italic;">(total ttc + port ttc - remise ttc)</span></li>
    			<li class="cellule">'.number_format($genavectva_ca, 2, ',', ' ').' &euro;</li>
    		</ul>';
        
        $retour.= '
         <ul class="lignesimple">
      			<li class="cellule_designation" style="width:330px;">CA HT <span style="font-style:italic;">(total ht + port ht - remise ht)</span></li>
      			<li class="cellule">'.number_format($genavectva_caht, 2, ',', ' ').' &euro;</li>
      	 </ul>';
         
         $retour.= '
         <ul class="lignesimple">
      			<li class="cellule_designation" style="width:330px;">TOTAL TVA <span style="font-style:italic;">(ca ttc - ca ht)</span></li>
      			<li class="cellule">'.number_format($genavectva_ca-$genavectva_caht, 2, ',', ' ').' &euro;</li>
      	 </ul>';
         
         $retour.= '
    		<ul class="lignesimple">
    			<li class="cellule_designation" style="width:99%;text-align:center;">COMMANDES NON SOUMISES A LA TVA</li>
    		</ul>
        ';
        
        $retour.= '
    		<ul class="lignesimple">
    			<li class="cellule_designation" style="width:330px;">Nombre de commandes payées</li>
    			<li class="cellule">'.number_format($gensanstva_nbventes, 0, ',', ' ').'</li>
    		</ul>
        ';    
        $retour.= '
         <ul class="lignesimple">
      			<li class="cellule_designation" style="width:330px;">Total articles HT</li>
      			<li class="cellule">'.number_format($gensanstva_tv, 2, ',', ' ').' &euro;</li>
      	 </ul>';
        
        $retour.= '
        <ul class="lignesimple">
    			<li class="cellule_designation" style="width:330px;">Port HT</li>
    			<li class="cellule">'.number_format($gensanstva_port, 2, ',', ' ').' &euro;</li>
    		</ul>';
        
        $retour.= '
        <ul class="lignesimple">
    			<li class="cellule_designation" style="width:330px;">Remise HT</li>
    			<li class="cellule">'.number_format($gensanstva_remise, 2, ',', ' ').' &euro;</li>
    		</ul>';
        
        $retour.= '
        <ul class="lignesimple">
    			<li class="cellule_designation" style="width:330px;">CA HT <span style="font-style:italic;">(total ht + port ht - remise ht)</span></li>
    			<li class="cellule">'.number_format($gensanstva_ca, 2, ',', ' ').' &euro;</li>
    		</ul>';
        
        $retour.= '<div></div>';
        
        echo $retour;
        
        }

		?>
    </div>
    </div>
</div>