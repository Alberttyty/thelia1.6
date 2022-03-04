<?php

include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("promorubrique");

include_once(realpath(dirname(__FILE__)) . "/Promorubrique.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Rubrique.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Rubriquedesc.class.php");   
require_once("../fonctions/hierarchie.php");
    
function afficherArbo($promorubrique,$parent_id,$niveau=0){

  $rubrique = new Rubrique(); 

  $query = "SELECT r.id,rd.titre,r.parent FROM ".Rubrique::TABLE." as r,".Rubriquedesc::TABLE." as rd WHERE r.parent=".$parent_id." AND rd.lang=1 AND r.id=rd.rubrique ORDER BY classement";
  $resul = $rubrique->query($query);
  if($resul){                                             
    while($row = $rubrique->fetch_object($resul)){
      
      if(!$promorubrique->rubriqueAvecEnfants($row->id)){   
        $checked='';
        if(in_array($row->id,explode(",",$promorubrique->rubriques))) $checked='checked="checked"';
        echo '<span class="choix niveau'.$niveau.'"><input type="checkbox" name="promorubrique[]" id="rubrique'.$row->id.'" value="'.$row->id.'" '.$checked.' /> <label for="rubrique'.$row->id.'">'.$row->titre.'</label></span>';
        $niveau_suite=$niveau;
      }
      else {
        echo '<span class="titre_rub niveau'.$niveau.'">'.$row->titre.'</span>';
        $niveau_suite=$niveau+1;
      }
      afficherArbo($promorubrique,$row->id,$niveau_suite);
    
    }
  }

}

$promorubrique = new Promorubrique();
if(isset($_GET['id'])) $promorubrique->charger($_GET['id']);

if($promorubrique->rubriques!="") {
  $checked_oui='checked="checked"';
  $checked_non='';
  $class="actif";
}
else {
  $checked_oui='';
  $checked_non='checked="checked"';
  $class="inactif";
}

echo '<div class="bordure_bottom '.$class.'" id="promorubrique">
        <ul class="ligne_fonce_BlocDescription">
		      <li class="designation" style="width:280px;">Limiter à une ou plusieurs rubriques</li>
		      <li>
          <span class="oui_non">
            <label for="promorubrique_actif_oui">Oui</label> <input id="promorubrique_actif_oui" class="form" name="promorubrique_actif" value="oui" type="radio" '.$checked_oui.' />
            <label for="promorubrique_actif_non">Non</label> <input id="promorubrique_actif_non" class="form" name="promorubrique_actif" value="non" type="radio" '.$checked_non.' />
          </span>
          ';

          afficherArbo($promorubrique,0);             
          
echo '    </li>
	      </ul>
      </div>';


?>