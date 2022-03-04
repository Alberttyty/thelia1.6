<?php

include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("promoport");

include_once(realpath(dirname(__FILE__)) . "/../../../classes/Modules.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Zone.class.php");

$promoport = new Promoport();
if(isset($_GET['id'])) $promoport->charger($_GET['id']);

if($promoport->id!="") {
  $checked_oui='checked="checked"';
  $checked_non='';
  $class="actif";
}
else {
  $checked_oui='';
  $checked_non='checked="checked"';
}

echo '<span id="promoport_oui"><input type="radio" value="1" name="type" '.$checked_oui.' /> port gratuit</span>';

echo '<div class="bordure_bottom '.$class.'" id="promoport">
        <ul class="ligne_claire_BlocDescription transporteurs">
		      <li class="designation" style="width:280px;">Transporteurs</li>
		      <li>
          ';

          $modules = new Modules();
          $query = "select * from $modules->table where type='2' and actif='1' order by classement";
          $resul = CacheBase::getCache()->query($query);
          foreach ($resul as $row) {
            $modules = new Modules();
            $modules->charger_id($row->id);
            try {
                $instance = ActionsModules::instance()->instancier($modules->nom);
                $titre = $instance->getTitre();
            } catch (Exception $ex) {
                $titre = '';
            }
            $checked='';
            if(in_array($row->id,explode(",",$promoport->transports))) $checked='checked="checked"';
            echo '<span class="choix"><input type="checkbox" name="promoporttransport[]" id="transport'.$row->id.'" value="'.$row->id.'" '.$checked.' /> <label for="transport'.$row->id.'">'.$titre.'</label></span>';
          };          
          
echo '    </li>
	      </ul>
        <ul class="ligne_claire_BlocDescription zones">
		      <li class="designation" style="width:280px;">Zones de livraison</li>
		      <li>
          ';

          $zone = new Zone();
          $query = "select * from $zone->table where 1 order by nom";
		      $resul = CacheBase::getCache()->query($query);
          foreach ($resul as $row) {
            $checked='';
            if(in_array($row->id,explode(",",$promoport->zones))) $checked='checked="checked"';
            echo '<span class="choix"><input type="checkbox" name="promoportzone[]" id="zone'.$row->id.'" value="'.$row->id.'" '.$checked.' /> <label for="zone'.$row->id.'">'.$row->nom.'</label></span>';
          };          
          
echo '    </li>
	      </ul>
      </div>';


?>