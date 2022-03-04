<?php
// Attention, le path doit être relatif, sinon file_get_contents() interptète le code PHP
// qui se trouve dans les fichiers au lieu de retourner le contenu.

require_once(__DIR__."/../../classes/Reecriture.class.php");


query_patch("update variable set valeur='153' where nom='version'");

query_patch("insert into variable(nom, valeur, protege, cache) values('sanitize_admin', 0, 1, 1)");


/*gestion retroactivité des url reecrites des produits supprimés*/
$q = "SELECT * FROM " . Reecriture::TABLE . " WHERE actif=1 AND fond IN ('produit', 'contenu', 'rubrique', 'dossier')";
$r = mysql_query($q,$cnx->link);
while($a = mysql_fetch_object($r))
{
	switch($a->fond)
	{
		case 'produit':
			preg_match("#id_produit=([0-9]+)([^[0-9]])*#", $a->param, $match);
			$id = $match[1];
			$produit = new Produit();
			if(!$produit->charger_id($id))
			{
				$reecriture = new Reecriture();
				$reecriture->charger($a->url);
				$reecriture->actif	 = 0;
				$reecriture->maj();
				
				$reecriture_new = new Reecriture();
				$reecriture_new->url = $reecriture->url;
				$reecriture_new->fond = 'nexisteplus';
				$reecriture_new->param = $reecriture->param . '&ancienfond=' . $reecriture->fond;
				$reecriture_new->actif	 = 1;
				$reecriture_new->lang	 = $reecriture->lang;
				$reecriture_new->add();
			}
			break;
		case 'contenu':
			preg_match("#id_contenu=([0-9]+)([^[0-9]])*#", $a->param, $match);
			$id = $match[1];
			$contenu = new Contenu();
			if(!$contenu->charger($id))
			{
				$reecriture = new Reecriture();
				$reecriture->charger($a->url);
				$reecriture->actif	 = 0;
				$reecriture->maj();
				
				$reecriture_new = new Reecriture();
				$reecriture_new->url = $reecriture->url;
				$reecriture_new->fond = 'nexisteplus';
				$reecriture_new->param = $reecriture->param . '&ancienfond=' . $reecriture->fond;
				$reecriture_new->actif	 = 1;
				$reecriture_new->lang	 = $reecriture->lang;
				$reecriture_new->add();
			}
			break;
		case 'rubrique':
			preg_match("#id_rubrique=([0-9]+)([^[0-9]])*#", $a->param, $match);
			$id = $match[1];
			$rubrique = new Rubrique();
			if(!$rubrique->charger($id))
			{
				$reecriture = new Reecriture();
				$reecriture->charger($a->url);
				$reecriture->actif	 = 0;
				$reecriture->maj();
				
				$reecriture_new = new Reecriture();
				$reecriture_new->url = $reecriture->url;
				$reecriture_new->fond = 'nexisteplus';
				$reecriture_new->param = $reecriture->param . '&ancienfond=' . $reecriture->fond;
				$reecriture_new->actif	 = 1;
				$reecriture_new->lang	 = $reecriture->lang;
				$reecriture_new->add();
			}
			break;
		case 'dossier':
			preg_match("#id_dossier=([0-9]+)([^[0-9]])*#", $a->param, $match);
			$id = $match[1];
			$dossier = new Dossier();
			if(!$dossier->charger($id))
			{
				$reecriture = new Reecriture();
				$reecriture->charger($a->url);
				$reecriture->actif	 = 0;
				$reecriture->maj();
				
				$reecriture_new = new Reecriture();
				$reecriture_new->url = $reecriture->url;
				$reecriture_new->fond = 'nexisteplus';
				$reecriture_new->param = $reecriture->param . '&ancienfond=' . $reecriture->fond;
				$reecriture_new->actif	 = 1;
				$reecriture_new->lang	 = $reecriture->lang;
				$reecriture_new->add();
			}
			break;
	}
}
/*fin gestion retroactivité des url reecrites des produits supprimés*/
?>
