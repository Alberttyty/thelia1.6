<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("fraisdeportvariables");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Devise.class.php");
$ma_commande=new Commande();
$ma_commande->charger_ref($_GET['ref']);
$ma_devise = new Devise();
$ma_devise->charger($ma_commande->devise);
if($ma_commande->statut<2){
?>       
<div id="fraisdeportvariables">
  <form class="fraisdeportvariables" method="post" action="<?php echo $_SERVER['PHP_SELF']."?ref=".$_GET['ref']; ?>">
      <span class="edition">
          <a href="#"><?php echo trad('editer', 'admin'); ?></a>
          <span class="input">
            <input name="fraisdeportvariables" type="text" class="form_court" value="<?php echo($ma_commande->port); ?>"/>
             <?php echo $ma_devise->symbole; ?>
            <input type="submit" value="OK"/>
          </span>
          <input name="ref" type="hidden" value="<?php echo $_GET['ref']; ?>"/>
      </span>
  </form>
</div>
<?php
}
?>  