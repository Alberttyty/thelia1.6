<?php
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");	
require_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php"); 
require_once(realpath(dirname(__FILE__)) . "/api/sellsytools.php"); 
require_once(realpath(dirname(__FILE__)) . "/api/sellsyconnect_curl.php"); 
	
class Sellsy extends PluginsClassiques {
		
	function Sellsy() {
		$this->PluginsClassiques();	
	}
    
    function init() {
		$this->ajout_desc("Sellsy", "Connexion avec Sellsy", "", 1);
		
      	$var = new Variable();
  		if(!$var->charger("sellsy-user_token")) {
  			$var->nom = "sellsy-user_token";
  			$var->valeur = "";
  			$var->add();
  		}
		
      	$var = new Variable();
  		if(!$var->charger("sellsy-user_secret")) {
  			$var->nom = "sellsy-user_secret";
  			$var->valeur = "";
  			$var->add();
  		}
		
      	$var = new Variable();
  		if(!$var->charger("sellsy-consumer_token")) {
  			$var->nom = "sellsy-consumer_token";
  			$var->valeur = "";
  			$var->add();
  		}
		
      	$var = new Variable();
  		if(!$var->charger("sellsy-consumer_secret")) {
  			$var->nom = "sellsy-consumer_secret";
  			$var->valeur = "";
  			$var->add();
  		}
		
      	$var = new Variable();
  		if(!$var->charger("sellsy-url_api")) {
  			$var->nom = "sellsy-url_api";
  			$var->valeur = "";
  			$var->add();
  		}
    }
    
	function action() {
    
    	if($_REQUEST['ad_email']!=""&&$_REQUEST['action']=="formulairecontact") {
      
			$email=$_REQUEST['ad_email'];
			$nom=mb_strtoupper($_REQUEST['nom']);
			$prenom=ucwords(mb_strtolower($_REQUEST['prenom']));
			$telephone=$_REQUEST['telephone'];
		
			$request =  array(
				'method' => 'Prospects.getList',
				'params' => array(
					'search' => array('email'=>$email)
				)
			);      
			
			$response = sellsyConnect_curl::load()->requestApi($request);
        
			//si le contact existe on met à jour le téléphone, mais pas le nom
			/*foreach ($response->response->result as $key => $client) {
				var_dump($client);
				  $request =  array(
					'method' => 'Prospects.update',
					'params' => array(
					  'id' => $client->id,
						'third' => array( 
						'name'=>$client->name,
						'tel'=>'0689216812'
					   ),
					  'contact' => array(
						'tel'=>'0689216812'
					  )
					)
				  );
				  $test=sellsyConnect_curl::load()->requestApi($request);
				  var_dump($test);
			}*/
			
			//si pas de contact avec cette adresse email
			if(intval($response->response->infos->nbtotal)==0) {				
				$request =  array(
					'method' => 'Prospects.create',
					'params' => array(
						'third' => array(
							'name'=>$nom.' '.$prenom,
							'type'=>'person',
							'email'=>$email,
							'tel'=>$telephone
						),
						'contact' => array(
							'name'=>$nom,
							'forename'=>$prenom,
							'email'=>$email,
							'tel'=>$telephone
						)
					)
				);			  
				sellsyConnect_curl::load()->requestApi($request);			
			}
			
		}
      
      //var_dump($response);
      
     /* if($response->response->infos->nbtotal=="0"){
        exit('pas trouvé');
      }*/
      
      /*foreach ($response->response->result as $key => $client) {
        echo $client->fullName;
      }*/
      
      //exit();
	}		         
		
}
?>