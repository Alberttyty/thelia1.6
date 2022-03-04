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

	class Googlecse extends PluginsClassiques{

		function __construct(){
			parent::__construct("googlecse");
		}

		function init(){
			$this->ajout_desc("Google CSE", "", "", 1);
      $apikey = new Variable();
			if(!$apikey->charger("googlecse_api_key")){
				$apikey->nom = "googlecse_api_key";
				$apikey->valeur = 0;
				$apikey->add();
			}
      $searchengineid = new Variable();
			if(!$searchengineid->charger("googlecse_search_engine_id")){
				$searchengineid->nom = "googlecse_search_engine_id";
				$searchengineid->valeur = 0;
				$searchengineid->add();
			}
		}
    
    function callRequest($service_url){
      $curl = curl_init($service_url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $curl_response = curl_exec($curl);
      if ($curl_response === false) {
          $info = curl_getinfo($curl);
          curl_close($curl);
          die('error occured during curl exec. Additioanl info: ' . var_export($info));
      }
      curl_close($curl);
      $decoded = json_decode($curl_response);
      if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
          die('error occured: ' . $decoded->response->errormessage);
      }
      echo 'response ok!';
      var_export($decoded->response);
    }
    
    function post(){
    
      /*$searchengineid = new Variable();
      $searchengineid->charger("googlecse_search_engine_id");
      $apikey = new Variable();
      $apikey->charger("googlecse_api_key");
    
      $this->callRequest('https://www.googleapis.com/customsearch/v1?key='.$apikey->valeur.'&cx='.$searchengineid->valeur.'&q=test');
      */
      
      //$pspell_link = pspell_new("fr");

      /*if (!pspell_check($pspell_link, "testt")) {
          $suggestions = pspell_suggest($pspell_link, "testt");
      
          foreach ($suggestions as $suggestion) {
              echo "Orthographes suggérées : $suggestion<br />"; 
          }
      }
      
      exit();  */
    
    }

	}

?>
