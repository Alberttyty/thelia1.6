<?php
        if(!isset($_SESSION["util"]->id)) exit;

?>

<script type="text/javascript">

$(document).ready(function() {

  /*var ajout =*/ 
  
  $("#pliantsphotos form").each(function( index ) {
    
    var id_photo=$(this).find('input[name=id_photo]').val();
    
    $(this).find('input[name=titre_photo]').closest('li.lignesimple').after($('.couleurs_photo_'+id_photo));
   
  });  
  
});  

</script>

<?php
  
    include_once(realpath(dirname(__FILE__)) . "/../../../classes/Image.class.php");
    include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
    include_once(realpath(dirname(__FILE__)) . "/Googleshopping.class.php");
    include_once(realpath(dirname(__FILE__)) . "/classes/Googleshoppingimage.class.php");
    
    /*$googleshopping = new Googleshopping();
    $googleshopping->modfichier();*/
        
    $image = new Image();
    $produit = new Produit();
    
    $produit->charger($_REQUEST['ref']);

		$query = "select * from $image->table where produit='$produit->id' order by classement";

		$resul = $image->query($query);

    echo '<ul style="display:none;">';
		while($resul && $row = $image->fetch_object($resul)) {
    
      $googleshoppingimage = new Googleshoppingimage();
      $googleshoppingimage->charger_image($row->id);
      
      echo '<li class="lignesimple couleurs_photo_'.$row->id.'"><div class="cellule_designation" style="height:30px;">Couleur(s)</div><div class="cellule"><input type="text" name="couleurs_photo" style="width:219px;" class="form" value="'.$googleshoppingimage->couleurs.'" /></div></li>';
    
    }    
    echo '</ul>';

?>
