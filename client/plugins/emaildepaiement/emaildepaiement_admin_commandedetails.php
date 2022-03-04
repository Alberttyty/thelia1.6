<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("emaildepaiement");                      
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
include_once(realpath(dirname(__FILE__)) . "/Emaildepaiement.class.php");
include_once(realpath(dirname(__FILE__)) . "/lang/".$_SESSION["util"]->lang.".php");
$ma_commande=new Commande();
$ma_commande->charger_ref($_GET['ref']);
$emaildepaiement=new Emaildepaiement(); 
if($emaildepaiement->est_module_de_paiement_pour($ma_commande)&&$ma_commande->statut<2){
$emaildepaiement->charger_commande($_GET['ref']);  
?>       
<span id="emaildepaiement">
  <a href="<?php echo $_SERVER['PHP_SELF']."?ref=".$_GET['ref']."&emaildepaiement=envoyer"; ?>"><?php echo trad('envoyer', 'emaildepaiement'); ?></a>
  <?php                         
  if($emaildepaiement->datemodif!="0000-00-00 00:00:00"&&$emaildepaiement->datemodif!=""){
    $time = strtotime($emaildepaiement->datemodif);
  	$dateaff = strftime("%d/%m/%y", $time);
  	$heureaff =  strftime("%H:%M:%S", $time);
    echo "<em>(".trad('envoye_le', 'emaildepaiement')." ".$dateaff." ".$heureaff.")</em>";
  }  
  ?>
</span>
<?php
}
?>                                                