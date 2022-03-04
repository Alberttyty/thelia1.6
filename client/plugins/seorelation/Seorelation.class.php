<?php

/********************************************************************************
*							// ATTENTION ! \\									*
*	NE PAS OUBLIE LA BALISE #FIN_PAGINATION DANS LA BOUCLE PAGE DU SQUELETTE	*
*	DANS LE CAS OU PAGE SUIVANTE N'EXISTE PAS (DERNIERE PAGE)					*
*				CF : BOUCLE CONDITIONNELLE DE THELIA							*
*																				*
********************************************************************************/

class Seorelation extends PluginsClassiques {

	function Seorelation() {
		$this->PluginsClassiques();
	}
  
	function action() {
		global $res, $pagination;
		
		// ON TEST L'EXISTENCE D'UNE PAGINATION
		$pagination = strpos($res, 'type="PAGE"');
		return $pagination;
	}
	
	function analyse() {
		// ON RECUPERE LE SQUELETTE APRES LE CALCUL PAR THELIA
		global $res, $page, $pagination;
		
		// SI GET['PAGE'] N'EXISTE PAS => page = 1 (pour première page)
		if ( !isset($_GET['page']) ) { $page = 1; }
		else {
			$page = $_GET['page'];
		}
		
		if ($pagination != false) {
		// SI PAGINATION EXISTE
			$url = supprimer_deconnexion(url_page_courante());
			$url = preg_replace('`\?page=([[:digit:]]{1,})`','',$url);
			$prev = $page-1;
			$next = $page+1;
			
			if ($page > 1) {
				// On est pas à la première page
				if (strpos($res, '#FIN_PAGINATION') === false) {
					// On n'est pas à la dernière page
					$res = str_replace('</head>',
										'<link rel="prev" href="'.$url.'?page='.$prev.'"/>'.PHP_EOL.
										'<link rel="next" href="'.$url.'?page='.$next.'"/>'.PHP_EOL.'</head>'.PHP_EOL,
										$res);
				}
				else {
					// On est à la dernière page qui ne doit pas contenir de balise SEO NEXT
					$res = str_replace('</head>','<link rel="prev" href="'.$url.'?page='.$prev.'"/>'.PHP_EOL.'</head>'.PHP_EOL,$res);
				}
			}
			else {
				// On est à la première page
				$url = str_replace('index.php','',$url);
				$res = str_replace('</head>','<link rel="next" href="'.$url.'?page='.$next.'"/>'.PHP_EOL.'</head>'.PHP_EOL,$res);
			}
			
			// On nettoie le squelette de #FIN_PAGINATION
			$res = str_replace('#FIN_PAGINATION','',$res);
			
		}
		
		return $res;
	}
}

?>