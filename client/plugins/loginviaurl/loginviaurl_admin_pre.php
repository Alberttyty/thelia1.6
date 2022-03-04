<?php 

  include_once(realpath(dirname(__FILE__)) . "/Loginviaurl.class.php");

  autorisation("loginviaurl");

  if(isset($_REQUEST['nom'])){$nomtest=$_REQUEST['nom'];}
  else {$nomtest='';}   
  if($nomtest == "loginviaurl" && basename($_SERVER['PHP_SELF']) == "module.php"){
  
    if(!empty($_REQUEST['action'])) $action=$_REQUEST['action'];
    else $action="";
    
    if(!empty($_REQUEST['id'])) $id=$_REQUEST['id'];
    else $id="";
    
    if($action=="modifier"){
      $loginviaurl = new Loginviaurl();
      if(!$loginviaurl->charger_id_admin($id)) $nouveau=true;
      else $nouveau=false; 
      $loginviaurl->id_admin=$id;
      $loginviaurl->redirect=$_REQUEST['destination'];
      if($loginviaurl->login_key=="") $loginviaurl->genKey();
      if (!$nouveau) $loginviaurl->maj();
      else $loginviaurl->add();
    }
    
    if($action=="supprimer"){
      $loginviaurl = new Loginviaurl();
      $loginviaurl->charger_id_admin($id);
      $loginviaurl->delete();
    }
  
  }
        
?>