<?php
        if(!isset($_SESSION["util"]->id)) exit;
        
        global $lang; 	
        if(!isset($lang)) $lang=$_SESSION["util"]->lang;
        if($lang=="") $lang = 1;
        
        $ref=$_REQUEST['ref'];
        
        $monclient = new Client();
        $monclient->charger_ref($ref);
        
        require_once("../client/plugins/remisefidelite/Remisefidelite.class.php");
        
        $remisefidelite = new Remisefidelite();
        $remisefidelite->id_client=$monclient->id;
        $remisefidelite->charger_remisedispo();
               
        $query = "select * from $remisefidelite->table where id_client=$monclient->id and id_commande!=0 ORDER BY date DESC";
     
        $resul = mysql_query($query, $remisefidelite->link);

?>

<div class="entete_liste_client">
			<div class="titre" >REMISE FIDELITE</div>
</div>

<table class="tabclient" cellpadding="5" cellspacing="0" width="100%">
    <tbody>
     <tr class="fonce">
       <td class="designation">Remise disponible</td>
       <td><?php echo $remisefidelite->remise ?> &euro;</td>
     </tr>
      </tbody>
</table>
<table class="tabclient" cellpadding="5" cellspacing="0" width="100%">
    <tbody>
     <tr class="claire">
       <td class="designation" colspan="2">Remise(s) utilis&eacute;e(s)</td>
     </tr>
     <?php
     while($row = mysql_fetch_object($resul)){
     $macommande = new Commande($row->id_commande);
     echo '<tr class="claire">
              <td class="designation">'.date("d/m/y H:i:s", strtotime($row->date)).' - N&deg; COMMANDE '.$macommande->ref.'</td>
              <td>'.$row->remise.' &euro;</td>
          </tr>';
     }     
     ?>
     </tbody>
</table>

