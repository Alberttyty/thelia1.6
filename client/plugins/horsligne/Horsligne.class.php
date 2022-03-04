<?php

class Horsligne extends PluginsClassiques{

	function Horsligne(){
		$this->PluginsClassiques();
	} 
  
	function init(){
	  $this->ajout_desc("Hors ligne", "Redirige les éléments hors ligne", "", 1);
  }
  
  function demarrage()
  {
  	global $fond, $ref, $id_produit, $id_rubrique, $id_contenu, $id_dossier;
    
    switch($fond){
    
      case 'produit':
      
        $tproduit = new Produit();
        if($ref) $tproduit->charger($ref);
        else if($id_produit) $tproduit->charger_id($id_produit);
        
        //inexistant
        if($tproduit->id==0) $this->redirection('index');
        //hors ligne
        if($tproduit->ligne==0) $this->redirection('rubrique',$tproduit->rubrique);
      
      break;
      
      case 'rubrique':
      
        $trubrique = new Rubrique();
        $trubrique->charger($id_rubrique);
        
        //inexistant
        if($trubrique->id==0) $this->redirection('index');
        //hors ligne
        if($trubrique->ligne==0) $this->redirection('rubrique',$trubrique->parent);
      
      break;
      
      case 'contenu':
      
        $tcontenu = new Contenu();
        $tcontenu->charger($id_contenu);
        
        //inexistant
        if($tcontenu->id==0) $this->redirection('index');
        //hors ligne
        if($tcontenu->ligne==0) $this->redirection('dossier',$tcontenu->dossier);
      
      break;
      
      case 'dossier':
      
        $tdossier = new Dossier();
        $tdossier->charger($id_dossier);
        
        //inexistant
        if($tdossier->id==0) $this->redirection('index');
        //hors ligne
        if($tdossier->ligne==0) $this->redirection('dossier',$tdossier->parent);
      
      break;
    
    }
    
  }
  
  function redirection($type='index',$id=0){
  
    $url="";
    $code=302;
  
    switch($type){
    
      case 'rubrique':
      
        $trubrique = new Rubrique();
        $trubrique->charger($id);
        if($trubrique->ligne==1){
          $trubriquedesc = new Rubriquedesc();
          $trubriquedesc->charger($id);
  			  $url = $trubriquedesc->getUrl();
        }
      
      break;
      
      case 'dossier':
      
        $tdossier = new Dossier();
        $tdossier->charger($id);
        if($tdossier->ligne==1){
          $tdossierdesc = new Dossierdesc();
          $tdossierdesc->charger($id);
  			  $url = $tdossierdesc->getUrl();
        }
      
      break;
      
      case 'index':
      
        $code=301;
      
      break;
    
    }
    
    if($url=="") $url=urlfond();
  
    header('Location: '.$url,true,$code);
    exit();
  
  }

}

?>