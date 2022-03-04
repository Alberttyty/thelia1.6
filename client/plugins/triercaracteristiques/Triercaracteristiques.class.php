<?php

include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdisp.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Caracdispdesc.class.php");

class Triercaracteristiques extends PluginsClassiques{
	
	function Triercaracteristiques(){
		$this->PluginsClassiques();
	}
  
  function trier($caracteristique){
  
    $caracdisp=new Caracdisp();  
    $caracdispdesc=new Caracdispdesc();  
    
    $query="select $caracdispdesc->table.id,titre,caracdisp,lang,titre,classement from $caracdispdesc->table left join $caracdisp->table on ($caracdisp->table.id=$caracdispdesc->table.caracdisp) where $caracdisp->table.caracteristique=$caracteristique and $caracdispdesc->table.lang=1";
    $resul=$caracdisp->query($query);
    
    $valeurs=array();
    
    while($resul && $row = $caracdisp->fetch_object($resul)){
      $valeurs[$row->id]=$row->titre;
    }
    
    asort($valeurs);
    
    $classement=1;
    foreach($valeurs as $k => $v){
      $caracdispdesc=new Caracdispdesc();
      $caracdispdesc->charger($k);
      $caracdispdesc->classement=$classement;
      $caracdispdesc->maj();
      $classement=$classement+1;
    }
    
    return $classement;
  
  }

}

?>