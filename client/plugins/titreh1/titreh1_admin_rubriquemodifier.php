<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("titreh1");

require_once(realpath(dirname(__FILE__)) . "/Titreh1.class.php");

$titreh1 = new Titreh1();
if(isset($_GET['id'])) $id=$_GET['id'];
else $id=0;
if(isset($_GET['lang'])) $lang=$_GET['lang'];
else $lang=1;
$titreh1->charger_objet('rubrique',$id,$lang);

?>
<script type="text/javascript">

$(document).ready(function() {
  
  var cible=$("#titreh1").closest("form").find("input#titre").closest("tr");
  $("#titreh1 tr").insertAfter(cible);
  $("#titreh1").remove();
  
});  

</script>
<table width="100%" cellpadding="5" cellspacing="0" id="titreh1">
    <tr class="fonce">
        <td class="designation">Titre H1<span id="titlemeta_title_nbr" class="note"></span></td>
        <td><input name="titreh1_titre" id="titreh1_titre" type="text" class="form_long"
                   value="<?php echo($titreh1->titre); ?>"/></td>
    </tr>
</table>