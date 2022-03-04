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
  include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/lire.php");


	class Captchacompte extends PluginsClassiques{

		function __construct(){
			parent::__construct("captchacompte");
		}

		function init(){
			$this->ajout_desc("Captcha créer compte", "Captcha créer compte", "", 1);
		}
    
    function avantclient(){
    
      if(!function_exists('dsp_crypt')){
      $cryptinstall="lib/crypt/cryptographp.fct.php";
      include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
      }
      
      if (!chk_crypt($_REQUEST['txt_securite'])) {
      global $urlerr;
      redirige_action($urlerr, urlfond("formulerr", "errform=1&errcaptchacompte=1"));
      exit();
		  }
    
    }    
    
    function post(){
    
      global $res;
      
      $res = preg_replace("/\#CAPTCHACOMPTE\[([^]]*)\]/",lireParam('errcaptchacompte') == "1" ? "\\1" : '',$res);
    
      if(!function_exists('dsp_crypt')){
        $cryptinstall="lib/crypt/cryptographp.fct.php";
        include_once realpath(dirname(__FILE__)) . "/../../../lib/crypt/cryptographp.fct.php";
      }   
      $res = str_replace("#CAPTCHACOMPTE", dsp_crypt(0,1,0), $res);
    
    }


	}

?>
