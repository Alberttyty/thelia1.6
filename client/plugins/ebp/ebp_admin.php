<div id="contenu_int"> 
  <p align="left"><a href="accueil.php" class="lien04">Accueil </a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> Import / Export            
  </p>
<?php
  include_once(realpath(dirname(__FILE__)) . "/ebp_formulaire.php");
?>

<?php

  function maxClassement($idcaracteristique, $lang)
	{
		$caracdispdesc = new Caracdispdesc();
		$caracdisp = new Caracdisp();

		$query = "
			select
				max(ddd.classement) as maxClassement
			from
				$caracdispdesc->table ddd
			left join
				$caracdisp->table dd on dd.id = ddd.caracdisp
			where
				lang=$lang
			and
				dd.caracteristique=$idcaracteristique
		";

		$resul = $caracdispdesc->query($query);

     	return $resul ? intval($caracdispdesc->get_result($resul, 0, "maxClassement")) : 0;
	}
  
  $carac_ajoutee=0;

  if(isset($_POST['marques'])){
  
    $marques = explode(";",$_POST['marques']);
    $carac_marque = new Variable();
    $carac_marque->charger("ebp-caracteristique-marque");
    $lang=1;
    
    foreach($marques as $k => $marque){
      $caracdisp=new Caracdisp();
      $caracdispdesc=new Caracdispdesc();
      $carac_query="select $caracdisp->table.id from $caracdisp->table left join $caracdispdesc->table on ($caracdisp->table.id=$caracdispdesc->table.caracdisp) where $caracdisp->table.caracteristique=$carac_marque->valeur and $caracdispdesc->table.lang=1 and $caracdispdesc->table.titre=\"$marque\" order by classement limit 0,1";
      $carac_resul = $caracdisp->query($carac_query);
      $carac_row = $caracdisp->fetch_object($carac_resul);
      if(!is_object($carac_row)){
        $tcaracdisp = new Caracdisp();
    		$tcaracdisp->caracteristique = $carac_marque->valeur;
    		$lastid = $tcaracdisp->add();
    		$tcaracdisp->id = $lastid;  
    		$tcaracdispdesc = new Caracdispdesc();
    		$tcaracdispdesc->caracdisp = $lastid;
    		$tcaracdispdesc->lang = $lang;
    		$tcaracdispdesc->titre = $marque;
    		$tcaracdispdesc->classement = 1 + maxClassement($carac_marque->valeur, $lang);
    		$tcaracdispdesc->add();
        $carac_ajoutee=$carac_ajoutee+1;
      }
    }  
  
  }

?>
     
  <div> 
    
    <div class="entete_liste_client">
    	<div class="titre">IMPORT DES MARQUES</div>
    </div>
    
    <form action="module.php" method="post">
    
    <?php
    if ($carac_ajoutee>0) {
    ?>
    
    Marque(s) ajout&eacute;e(s) : <strong><?php echo $carac_ajoutee; ?></strong> <br/>
    
    <?php
    }
    ?>
    
      <input type="hidden" name="nom" value="ebp" />
      <textarea name="marques" style="width:400px;height:100px;"></textarea> 
      <input type="submit" value="OK" />
    </form>
  </div>

</div>  