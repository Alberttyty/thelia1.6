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
require_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
require_once(realpath(dirname(__FILE__)) . "/lib/cssmin-v3.0.1.php");
require_once(realpath(dirname(__FILE__)) . "/lib/jsmin.php");

class Compresseur extends PluginsClassiques {

		function Compresseur() {
				$this->PluginsClassiques("compresseur");
	  }

  	function inclusion() {
    		global $res,$reptpl,$parseur;
				if(preg_match_all('/#INCLURE[\s]*"([^"]*)"/', $res, $matches, PREG_SET_ORDER))	{

						foreach($matches as $match) {
        				$fichier = $reptpl.str_replace("template/","",$match[1]);
								$contenu = file_get_contents($fichier);

								if($contenu !== false) {
										$parseur->inclusion($contenu);
		          			$contenu=str_replace('#DOSSIER_TEMPLATE',rtrim($reptpl,'/'),$contenu);
		          			$contenu = $this->compresserCss($contenu,basename($fichier,'.html'));
		          			$contenu = $this->compresserJs($contenu,basename($fichier,'.html'));
										$res = str_replace($match[0], $contenu, $res);

								} else die("Impossible d'ouvrir le fichier inclus $fichier");
						}

				}
		}

  	function compresserJs($res,$fond_js="") {

				global $fond;
				if($fond_js=="") $fond_js=$fond;

				$cache_dir=SITE_DIR.'client/cache/js/';
				$cache_url=FICHIER_URL.'client/cache/js/';
				$cache_js = $cache_dir.$fond_js.'.cache';

				$recalculer=false;
				if($_REQUEST['var_mode']=="recalcul") $recalculer=true;
				if(!file_exists($cache_js)) $recalculer=true;

				$js_retour=array();

				$premier=true;

				if ($fond_js==$fond) preg_match('/(?:<head[^>]*>)(.*)<\/head>/isU',$res,$matches_head);
				else $matches_head[1]=$res;

				//ne pas traiter les commentaires
				$matches_head[1]=preg_replace('/<!--(.|\s)*?-->/','',$matches_head[1]);
				preg_match_all('~<script([^>]*)('.trim(FICHIER_URL,'/').')?([^>]*)type=[\'|"]text/javascript[\'|"]([^>]*)('.trim(FICHIER_URL,'/').')?(.*)></script>~isU',$matches_head[1],$matches_link);

				foreach($matches_link[0] as $key => $script) {
		   			if(!strpos($script,"data-compresseur")/*&&strpos($script,trim(FICHIER_URL,'/'))*/) {

								if($recalculer) {
			  						if(preg_match('/src=[\'|"]([^\"|\']*)[\'|"]/isU',$script,$matches_href)==1) {
				 								//$js_min = JSMin::minify(file_get_contents($matches_href[1]));
										 		$js_min = JSMin::minify(file_get_contents($this->nettoyerNom($matches_href[1])));
										 		if(preg_match('/id=[\'|"]([^\"|\']*)[\'|"]/is',$script,$matches_id)==1) {
										  			$js_retour[$matches_id[1]]['id']=$matches_id[1];
										  			$js_retour[$matches_id[1]]['js'].=$js_min;
										 		} else {
										  			$js_retour['js']['id']='';
										  			$js_retour['js']['js'].=$js_min;
										 		}
			   						}
			 					}

								if($premier) {
							  		$res=str_replace($script,"<!-- inserer js -->",$res);
							  		$premier=false;
							 	} else $res=str_replace($script,"",$res);
		   			}
				}

				if($recalculer) {
			 			if(!is_dir($cache_dir)) {
								if(mkdir($cache_dir,0755,true) === false) die('Impossible de créer le répertoire '.$cache_dir.'. Vérifiez les droits d\'accès');
			  		}
			  		$filenames=glob($cache_dir.$fond_js."-"."*.js",GLOB_NOSORT);
			  		if($filenames) {
								foreach($filenames as $filename) {
				  					unlink($filename);
								}
			  		}

				  	if(file_exists($cache_js)) unlink($cache_js);

				  	$res_js='';
				  	foreach($js_retour as $key => $script) {
								$cache_name = $fond_js.'-'.$key.'-'.md5(uniqid(rand())).'.js';
								$cache_file = $cache_dir.$cache_name;

								file_put_contents($cache_file,$script['js']);

								$res_js.='<script src="'.$cache_url.$cache_name.'" ';

								if($script['id']!="") $res_js.='id="'.$script['id'].'" ';

								$res_js.='data-compresseur="oui"></script>';
				  	}

				  	if($res_js!='') file_put_contents($cache_js,$res_js);
				}

				if(file_exists($cache_js)) $res=str_replace('<!-- inserer js -->',file_get_contents($cache_js),$res);

				return $res;
  	}

  	function nettoyerNom($fichier) {
    		return preg_replace('/(.+)\.(.+)\?(.+)/','$1.$2',$fichier);
  	}

  	function compresserCss($res,$fond_css="") {

				global $fond;
				if($fond_css=="") $fond_css=$fond;

				$cache_dir=SITE_DIR.'client/cache/css/';
				$cache_url=FICHIER_URL.'client/cache/css/';
				$cache_css = $cache_dir.$fond_css.'.cache';

				$recalculer=false;
				if($_REQUEST['var_mode']=="recalcul") $recalculer=true;
				if(!file_exists($cache_css)) $recalculer=true;

				$css_retour=array();

				$premier=true;

				if ($fond_css==$fond) preg_match('/(?:<head[^>]*>)(.*)<\/head>/isU',$res,$matches_head);
				else $matches_head[1]=$res;

				//ne pas traiter les commentaires
				$matches_head[1]=preg_replace('/<!--(.|\s)*?-->/','',$matches_head[1]);

				preg_match_all('~<link([^/>]*)('.trim(FICHIER_URL,'/').')?([^/>]*)rel=[\'|"]stylesheet[\'|"]([^/>]*)('.trim(FICHIER_URL,'/').')?(.*)/>~isU',$matches_head[1],$matches_link);

				foreach($matches_link[0] as $key => $style) {
		   			if(!strpos($style,"data-compresseur")/*&&strpos($style,trim(FICHIER_URL,'/'))*/){
								if($recalculer) {

			  						if((preg_match('/media=[\'|"]([screen|print|all|projection|tv|handheld]+)([^\"|\']*)[\'|"]/is',$style,$matches_media)==1)
										&&(preg_match('/href=[\'|"]([^\"|\']*)[\'|"]/isU',$style,$matches_href)==1)) {

				 								$css_min=CssMin::minify(file_get_contents($this->nettoyerNom($matches_href[1])));

										 		if(preg_match('/id=[\'|"]([^\"|\']*)[\'|"]/is',$style,$matches_id)==1) {
										  			$css_retour[$matches_id[1].'-'.$matches_media[1]]['id']=$matches_id[1];
										  			$css_retour[$matches_id[1].'-'.$matches_media[1]]['media']=$matches_media[1].$matches_media[2];
										  			$css_retour[$matches_id[1].'-'.$matches_media[1]]['css'].=preg_replace("/(\r\n|\n|\r)/","",str_replace('../','../../../template/',$css_min));
										 		} else {
										  			$css_retour[$matches_media[1]]['id']='';
										  			$css_retour[$matches_media[1]]['media']=$matches_media[1].$matches_media[2];
										  			$css_retour[$matches_media[1]]['css'].=preg_replace("/(\r\n|\n|\r)/","",str_replace('../','../../../template/',$css_min));
										 		}
			   						}

			 					}
							 	if($premier) {
							  		$res=str_replace($style,"<!-- inserer css -->",$res);
							  		$premier=false;
							 	} else $res=str_replace($style,"",$res);
		   			}
				}

				if($recalculer) {
		  			if(!is_dir($cache_dir)) {
								if(mkdir($cache_dir,0755,true) === false) die('Impossible de créer le répertoire '.$cache_dir.'. Vérifiez les droits d\'accès');
		  			}

						$filenames=glob($cache_dir.$fond_css."-"."*.css",GLOB_NOSORT);
		  			if($filenames) {
								foreach($filenames as $filename) {
			  						unlink($filename);
								}
		  			}

		  			if(file_exists($cache_css)) unlink($cache_css);

				  	$res_css='';
				  	foreach($css_retour as $key => $style) {
								$cache_name = $fond_css.'-'.$key.'-'.md5(uniqid(rand())).'.css';
								$cache_file = $cache_dir.$cache_name;
								file_put_contents($cache_file,$style['css']);
								$res_css.='<link rel="stylesheet" href="'.$cache_url.$cache_name.'" ';
								if($style['id']!="") $res_css.='id="'.$style['id'].'" ';
								$res_css.='media="'.$style['media'].'" data-compresseur="oui" />';
		  			}

		  			if($res_css!='') file_put_contents($cache_css,$res_css);

				}

				if(file_exists($cache_css)) $res=str_replace('<!-- inserer css -->',file_get_contents($cache_css),$res);

				return $res;
  	}

  	function compresserHTML($res) {
			
				$search = array(
					'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
					'/[^\S ]+\</s',  // strip whitespaces before tags, except space
					'/(\s)+/s'       // shorten multiple whitespace sequences
				);
				$replace = array(
					'>',
					'<',
					'\\1'
				);
				$res = preg_replace($search, $replace, $res);

				$search = array("   ","  ","\t","\n\n","\r\r");
				$replace = array(" "," "," ","","");
				$res = str_replace($search,$replace,$res);
				return $res;
  	}

  	function post() {
				global $res;
				$res=$this->compresserCss($res);
				$res=$this->compresserJs($res);
				$res=$this->compresserHTML($res);
  	}

}
?>
