<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) Octolys Development		                                     */
/*		email : thelia@octolys.fr		        	                             	 */
/*      web : http://www.octolys.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/
?>
<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php"); 	
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Commande.class.php"); 
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Venteprod.class.php"); 
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Client.class.php");  

class Exportgoogledrive extends PluginsClassiques{

  var $connection;
  var $table = "exportgoogledrive";
  var $id;
  var $commande;
  var $date;
	var $bddvars=array("id","commande","date");

	function Exportgoogledrive(){

		$this->PluginsClassiques("exportgoogledrive");
    
	}
  
  function init(){
    $users = new Variable();
		if(!$users->charger("exportgoogledrive_users")){
			$users->nom = "exportgoogledrive_users";
			$users->valeur = "";
			$users->add();
		}
    $query = "CREATE TABLE IF NOT EXISTS `exportgoogledrive` (
							`id` BIGINT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
							`commande` TINYTEXT NOT NULL,
              `date` DATETIME NOT NULL
						);";
		$resul = mysql_query($query, $this->link);
  }

  function statut($commande){
  
    //COMMANDE OK
		if ($commande->statut >= Commande::PAYE && $commande->statut <= Commande::EXPEDIE){
      
      $exportgoogledrive = new Exportgoogledrive();
      
      //si pas encore exportee
      if(!$exportgoogledrive->charger_commande($commande->ref)){
    
        $produits=$commande->getProduits();
        
        $liste_billets=array();
        
        foreach ($produits as $key=>$venteprod){
        
          $billet=array();
          $billet['titre']=$venteprod->titre;
          $billet['quantite']=$venteprod->quantite;
          $liste_billets[$venteprod->ref][]=$billet;
          
        }
        
        foreach ($liste_billets as $ref=>$billets){
        
          $this->addRow($ref,$commande,$billets);
        
        }
        
        $exportgoogledrive->commande=$commande->ref;
        $exportgoogledrive->date=date("Y-m-d H:i:s");
        $exportgoogledrive->add(); 
        
      }
    
    }
    
  }
  
  function charger_commande($commande){
    return $this->getVars("select * from $this->table where commande=\"$commande\"");
  }
  
  function initConnection(){
  
    if(!is_array($this->connection)){
      require_once realpath(dirname(__FILE__) . '/google-api-php-client-master/src/Google/autoload.php');
      $json_key=SITE_DIR."/client/plugins/exportgoogledrive/key_odldjes415r.json";
      
      $client = new Google_Client();
      
      try {
        $client->setApplicationName("Export Google Drive Thelia"); 
        $cred=$client->loadServiceAccountJson($json_key,array('https://www.googleapis.com/auth/drive'));
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($cred);
        }
        
        $_SESSION['service_token'] = $client->getAccessToken();
        $resultArray = json_decode($_SESSION['service_token']);
        $accessToken = $resultArray->access_token;
      }
      catch (Exception $e) {
         mail ("mathieu@pixel-plurimedia.fr","Erreur Google Drive","An error occurred: " . $e->getMessage());
      }
      
      $this->connection=array('client'=>$client,'accessToken'=>$accessToken);
    } 
  
  }
  
  function selectFirstWorksheet($fileid){
  
    try {
    
      $url = "https://spreadsheets.google.com/feeds/worksheets/$fileid/private/full?alt=json";
      $method = 'GET';
      $headers = ["Authorization" => "Bearer ".$this->connection['accessToken']];
      $req = new Google_Http_Request($url, $method, $headers);
      $curl = new Google_IO_Curl($this->connection['client']);
      $results = $curl->executeRequest($req);
      $resultats=json_decode($results[0]);
      $worksheet_array=get_object_vars($resultats->feed->entry[0]->id);
      $worksheetid=substr($worksheet_array['$t'],strrpos($worksheet_array['$t'],'/')+1);  
    }
    catch (Exception $e) {
       mail ("serveur@pixel-plurimedia.fr","Erreur Google Drive","An error occurred: " . $e->getMessage());
    }
    
    return $worksheetid;
  
  }
  
  function selectFile($ref){
                     
    $service = new Google_Service_Drive($this->connection['client']);
    
    $files=$this->retrieveAllFiles($service);
    
    $trouve=false; 
    $fileIdTemplate="";                
    
    foreach($files as $k => $file){
      //$service->files->delete($file->id);
      if($file->title==$ref)
      {
        $trouve=true;
        $fileId=$file->id;
      }
    } 
    
    //pas trouve, donc on créer le fichier de base
    if(!$trouve){ 
      $users = new Variable();
      $users->charger("exportgoogledrive_users");
      $file = new Google_Service_Drive_DriveFile();
      $file->setTitle($ref);
      $file->setDescription('Match '.$ref);
      $file->setMimeType('application/vnd.google-apps.spreadsheet');
      try {
        $data = file_get_contents(realpath(dirname(__FILE__))."/template.csv");
        $createdFile = $service->files->insert($file,array(
          'data' => $data,
          'mimeType' => 'text/csv',
          'convert' => true,
          'uploadType' => 'multipart',
        ));
        foreach(explode(',',$users->valeur) as $k => $user){
          $this->insertPermission($service,$createdFile->id,$user,'user','owner');
        }  
        $fileId=$createdFile->id;
      } catch (Exception $e) {
        mail ("serveur@pixel-plurimedia.fr","Erreur Google Drive","An error occurred: " . $e->getMessage());
      }
    }
    
    return $fileId;
  
  }
  
  function insertPermission($service,$fileId,$value,$type,$role) {
    $newPermission = new Google_Service_Drive_Permission();
    $newPermission->setValue($value);
    $newPermission->setType($type);
    $newPermission->setRole($role);
    try {
      return $service->permissions->insert($fileId, $newPermission);
    } catch (Exception $e) {
      mail ("serveur@pixel-plurimedia.fr","Erreur Google Drive","An error occurred: " . $e->getMessage());
    }
    return NULL;
  }
  
  function retrieveAllFiles($service) {
    $result = array();
    $pageToken = NULL;
  
    do {
      try {
        $parameters = array();
        if ($pageToken) {
          $parameters['pageToken'] = $pageToken;
        }
        $files = $service->files->listFiles($parameters);
  
        $result = array_merge($result, $files->getItems());
        $pageToken = $files->getNextPageToken();
      } catch (Exception $e) {
        mail ("serveur@pixel-plurimedia.fr","Erreur Google Drive","An error occurred: " . $e->getMessage());
        $pageToken = NULL;
      }
    } while ($pageToken);
    return $result;
  }
  
  function addRow($ref,$commande,$billets){
  
    $this->initConnection();
  
    //$worksheetid = $this->selectWorksheet($ref);
    $fileid = $this->selectFile($ref);
    $worksheetid = $this->selectFirstWorksheet($fileid);
    
    $client_commande=new Client();
    $client_commande->charger_id($commande->client);

    try {
      
      // $service = new Google_Service_Drive($client);
      
      // Section 1: Uncomment to get file metadata with the drive service
      // This is also the service that would be used to create a new spreadsheet file
      // $results = $service->files->get($fileId);
      // var_dump($results);
      
      // Section 2: Uncomment to get list of worksheets
      // $url = "https://spreadsheets.google.com/feeds/worksheets/$fileId/private/full";
      // $method = 'GET';
      // $headers = ["Authorization" => "Bearer $accessToken"];
      // $req = new Google_Http_Request($url, $method, $headers);
      // $curl = new Google_IO_Curl($client);
      // $results = $curl->executeRequest($req);
      // echo "$results[2]\n\n";
      // echo "$results[0]\n";
      
      // Section 3: Uncomment to get the table data
      // $url = "https://spreadsheets.google.com/feeds/list/$fileId/od6/private/full";
      // $method = 'GET';
      // $headers = ["Authorization" => "Bearer $accessToken", "GData-Version" => "3.0"];
      // $req = new Google_Http_Request($url, $method, $headers);
      // $curl = new Google_IO_Curl($client);
      // $results = $curl->executeRequest($req);
      // echo "$results[2]\n\n";
      // echo "$results[0]\n";
      
      // Section 4: Uncomment to add a row to the sheet
      $url = "https://spreadsheets.google.com/feeds/list/$fileid/$worksheetid/private/full";
      $method = "POST";
      $headers = ["Authorization" => "Bearer ".$this->connection['accessToken'], 'Content-Type' => 'application/atom+xml'];
      $postBody = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gsx="http://schemas.google.com/spreadsheets/2006/extended">
                    <gsx:nom>'.$client_commande->nom.'</gsx:nom>
                    <gsx:prenom>'.$client_commande->prenom.'</gsx:prenom>
                    <gsx:societe>'.$client_commande->entreprise.'</gsx:societe>
                    <gsx:billets>';
                    
      foreach ($billets as $key=>$billet){
        $postBody .= $billet['quantite'].'X '.$billet['titre'].'
';
      }
      
      $postBody .='</gsx:billets>
                    <gsx:numerocommande>'.$commande->ref.'</gsx:numerocommande>
                    <gsx:date>'.$commande->date.'</gsx:date>
                  </entry>
                  ';
      $req = new Google_Http_Request($url, $method, $headers, $postBody);
      $curl = new Google_IO_Curl($this->connection['client']);
      $results = $curl->executeRequest($req);
          
    }
    catch (Exception $e) {
       mail ("serveur@pixel-plurimedia.fr","Erreur Google Drive","An error occurred: " . $e->getMessage());
    }
    
  }
  
}

?>