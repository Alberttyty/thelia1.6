<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");

if(isset($_GET['ref'])) {
  $produit_ref = new Produit();
  $produit_ref->charger($_GET['ref']);
  $id=$produit_ref->id;
  
}

?>
<script type="text/javascript">

$(document).ready(function() {
 
  var cible=$("#urlcourte").closest("form").find("input[name='urlreecrite']").closest("ul");
  $("#urlcourte").insertAfter(cible);
 // $("#urlcourte").remove();
  
});  

</script>
<ul class="lignesimple" id="urlcourte">
    <li class="cellule_designation" style="width:128px; padding:5px 0 0 5px;">
    URL courte
    </li>
    <li class="cellule" style="width:450px;padding: 5px 0 0 5px;">
    <?php echo 'http://'.$_SERVER['SERVER_NAME'].'/p'.$id ; ?>
    </li>
</ul>