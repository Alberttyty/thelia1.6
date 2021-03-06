<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produit.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Produitdesc.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Rubrique.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Rubriquedesc.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Contenu.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Contenudesc.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Dossier.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Dossierdesc.class.php");

class Titlemeta extends PluginsClassiques
{
    var $id;
    var $produit = 0;
    var $rubrique = 0;
    var $contenu = 0;
    var $dossier = 0;

    const TABLE = "titlemeta";

    var $table = self::TABLE;

    var $bddvars = array("id", "produit", "rubrique", "contenu", "dossier");

    function Titlemeta()
    {
        $this->PluginsClassiques();
    }

    function init()
    {
        $cnx = new Cnx();
        $query = "CREATE TABLE IF NOT EXISTS `" . self::TABLE . "` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `produit` INT(11) NOT NULL,
			  `rubrique` INT(11) NOT NULL,
			  `contenu` INT(11) NOT NULL,
			  `dossier` INT(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $this->query($query, $cnx->link);

        $titlemetadesc = new Titlemetadesc();
        $titlemetadesc->init();
    }

    function add()
    {
        if (empty($this->produit) && empty($this->rubrique) && empty($this->contenu) && empty($this->dossier))
            return 0;
        else
            return parent::add();
    }

    function charger_objet()
    {
        $varprod = $this->bddvars[1];
        $varrub = $this->bddvars[2];
        $varcont = $this->bddvars[3];
        $vardos = $this->bddvars[4];
        $requete = "select * from " . self::TABLE . " where $varprod=\"" . $this->$varprod . "\" AND $varrub=\"" . $this->$varrub . "\" AND $varcont=\"" . $this->$varcont . "\" AND $vardos=\"" . $this->$vardos . "\"";

        return $this->getVars($requete);
    }

    function titlemeta_action($arg)
    {
        if ($arg['titlemeta_action'] == "modifier")
        {
            if (empty($arg['titlemeta_lang']))
                return -1;

            $objet = new Titlemeta();
            $objet->produit = $arg['titlemeta_produit'];
            $objet->rubrique = $arg['titlemeta_rubrique'];
            $objet->contenu = $arg['titlemeta_contenu'];
            $objet->dossier = $arg['titlemeta_dossier'];
            $alors = $objet->charger_objet();

            if (!$alors)
                $objet->id = $objet->add();

            $objetdesc = new Titlemetadesc();
            $alors = $objetdesc->charger_desc($objet->id, $arg['titlemeta_lang']);

            if (!$alors)
            {
                $objetdesc->titlemeta = $objet->id;
                $objetdesc->lang = $arg['titlemeta_lang'];
                $objetdesc->id = $objetdesc->add();
            }

            $objetdesc->title = ($arg['titlemeta_title']);
            $objetdesc->metadesc = ($arg['titlemeta_metadesc']);
            $objetdesc->metakeyword = ($arg['titlemeta_metakeyword']);
            $objetdesc->autre = ($arg['titlemeta_autre']);

            $objetdesc->maj();
        }
    }

    function boucle($texte, $args)
    {
        $mode = lireTag($args, "mode");
        $page = strtoupper(lireTag($args, "page"));
        $id = lireTag($args, "id");

        if (isset($_SESSION["navig"]->lang))
            $lang = $_SESSION["navig"]->lang;
        else
            $lang = 1;

        $TM_produit = 0;
        $TM_rubrique = 0;
        $TM_contenu = 0;
        $TM_dossier = 0;

        $nomsite = new Variable();
        $nomsite->charger("nomsite");

        switch ($page)
        {
            case 'PRODUIT':
                $produit = new Produit();
                if ($produit->charger_id($id))
                    $TM_produit = $id;
                break;
            case 'RUBRIQUE':
                $rubrique = new Rubrique();
                if ($rubrique->charger($id))
                    $TM_rubrique = $id;
                break;
            case 'CONTENU':
                $contenu = new Contenu();
                if ($contenu->charger($id))
                    $TM_contenu = $id;
                break;
            case 'DOSSIER':
                $dossier = new Dossier();
                if ($dossier->charger($id))
                    $TM_dossier = $id;
                break;
        }

        $objet = new Titlemeta();

        $objet->rubrique = $TM_rubrique;
        $objet->produit = $TM_produit;
        $objet->dossier = $TM_dossier;
        $objet->contenu = $TM_contenu;
        $objet->charger_objet();

        $temp = $texte;

        $letitle = false;
        $lameta = false;
        $lakeyword = false;
        $unautre = true;

        $objetdesc = new Titlemetadesc();

        $objetdesc->charger_desc($objet->id, $lang);

        if ($objetdesc->title != "")
        {
            $temp = str_replace("#TITLE", "$objetdesc->title", $temp);
            $letitle = true;
        }
        if ($objetdesc->metadesc != "")
        {
            $temp = str_replace("#META", "$objetdesc->metadesc", $temp);
            $lameta = true;
        }
        if ($objetdesc->metakeyword != "")
        {
            $temp = str_replace("#KEYWORD", "$objetdesc->metakeyword", $temp);
            $lakeyword = true;
        }
        if ($objetdesc->autre != "")
        {
            $temp = str_replace("#AUTRE", "$objetdesc->autre", $temp);
            $unautre = true;
        }

        if ($mode != "solo")
        {
            //si il manque une balise et que l'on a demand?? un produit
            if ((!$letitle || !$lameta || !$unautre) && $produit->id)
            {
                $rubparent = new Titlemeta();
                $rubparent->rubrique = $produit->rubrique;
                $rubparent->charger_objet();
                $rubparentdesc = new Titlemetadesc();
                $rubparentdesc->charger_desc($rubparent->id, $lang);

                if (!$letitle && $rubparentdesc->title != "")
                {
                    $temp = str_replace("#TITLE", "$rubparentdesc->title", $temp);
                    $letitle = true;
                }
                if (!$lameta && $rubparentdesc->metadesc != "")
                {
                    $temp = str_replace("#META", "$rubparentdesc->metadesc", $temp);
                    $lameta = true;
                }
                if (!$lakeyword && $rubparentdesc->metakeyword != "")
                {
                    $temp = str_replace("#KEYWORD", "$rubparentdesc->metakeyword", $temp);
                    $lakeyword = true;
                }
                if (!$unautre && $rubparentdesc->autre != "")
                {
                    $temp = str_replace("#AUTRE", "$rubparentdesc->autre", $temp);
                    $unautre = true;
                }
            }
            if ((!$letitle || !$lameta || !$lakeyword || !$unautre) && $rubrique->id)
            {
                $racine = new Rubrique();
                $racine->charger($rubrique->id);
                while ($racine->parent && (!$letitle || !$lameta || !$lakeyword || !$unautre))
                {
                    $racine->charger($racine->parent);
                    $rubparent = new Titlemeta();
                    $rubparent->rubrique = $racine->id;
                    $rubparent->charger_objet();
                    $rubparentdesc = new Titlemetadesc();
                    $rubparentdesc->charger_desc($rubparent->id, $lang);
                    if (!$letitle && $rubparentdesc->title != "")
                    {
                        $temp = str_replace("#TITLE", "$rubparentdesc->title", $temp);
                        $letitle = true;
                    }
                    if (!$lameta && $rubparentdesc->metadesc != "")
                    {
                        $temp = str_replace("#META", "$rubparentdesc->metadesc", $temp);
                        $lameta = true;
                    }
                    if (!$lakeyword && $rubparentdesc->metakeyword != "")
                    {
                        $temp = str_replace("#KEYWORD", "$rubparentdesc->metakeyword", $temp);
                        $lakeyword = true;
                    }
                    if (!$unautre && $rubparentdesc->autre != "")
                    {
                        $temp = str_replace("#AUTRE", "$rubparentdesc->autre", $temp);
                        $unautre = true;
                    }
                    if ($letitle && $lameta && $unautre)
                    {
                        break;
                    }
                }
            }
            if ((!$letitle || !$lameta || !$lakeyword || !$unautre) && $contenu->id)
            {
                $dosparent = new Titlemeta();
                $dosparent->dossier = $contenu->dossier;
                $dosparent->charger_objet();
                $dosparentdesc = new Titlemetadesc();
                $dosparentdesc->charger_desc($dosparent->id, $lang);
                if (!$letitle && $dosparentdesc->title != "")
                {
                    $temp = str_replace("#TITLE", "$dosparentdesc->title", $temp);
                    $letitle = true;
                }
                if (!$lameta && $dosparentdesc->metadesc != "")
                {
                    $temp = str_replace("#META", "$dosparentdesc->metadesc", $temp);
                    $lameta = true;
                }
                if (!$lakeyword && $dosparentdesc->metakeyword != "")
                {
                    $temp = str_replace("#KEYWORD", "$dosparentdesc->metakeyword", $temp);
                    $lakeyword = true;
                }
                if (!$unautre && $dosparentdesc->autre != "")
                {
                    $temp = str_replace("#AUTRE", "$dosparentdesc->autre", $temp);
                    $unautre = true;
                }
            }
            if ((!$letitle || !$lameta || !$lakeyword || !$unautre) && $dossier->id)
            {
                $racine = new Dossier();
                $racine->charger($dossier->id);
                while ($racine->parent && (!$letitle || !$lameta || !$lakeyword || !$unautre))
                {
                    $racine->charger($racine->parent);
                    $dosparent = new Titlemeta();
                    $dosparent->rubrique = $racine->id;
                    $dosparent->charger_objet();
                    $dosparentdesc = new Titlemetadesc();
                    $dosparentdesc->charger_desc($dosparent->id, $lang);
                    if (!$letitle && $dosparentdesc->title != "")
                    {
                        $temp = str_replace("#TITLE", "$dosparentdesc->title", $temp);
                        $letitle = true;
                    }
                    if (!$lameta && $dosparentdesc->metadesc != "")
                    {
                        $temp = str_replace("#META", "$dosparentdesc->metadesc", $temp);
                        $lameta = true;
                    }
                    if (!$lakeyword && $dosparentdesc->metakeyword != "")
                    {
                        $temp = str_replace("#KEYWORD", "$dosparentdesc->metakeyword", $temp);
                        $lakeyword = true;
                    }
                    if (!$unautre && $dosparentdesc->autre != "")
                    {
                        $temp = str_replace("#AUTRE", "$dosparentdesc->autre", $temp);
                        $unautre = true;
                    }
                    if ($letitle && $lameta && $unautre)
                    {
                        break;
                    }
                }
            }
            /*if (!$letitle || !$lameta || !$lakeyword || !$unautre)
            {
                $rubdefaut = new Titlemeta();
                $rubdefaut->rubrique = 1;
                $rubdefaut->produit = 1;
                $rubdefaut->contenu = 1;
                $rubdefaut->dossier = 1;
                $rubdefaut->charger_objet();

                $rubdefautdesc = new Titlemetadesc();

                $titre = "";
                if ($produit->id)
                {
                    $produitDesc = new Produitdesc();
                    $produitDesc->charger($produit->id);

                    $rubriqueDesc = new Rubriquedesc();
                    $rubriqueDesc->charger($produit->rubrique);
                    $titre = $nomsite->valeur . ' - ' . $rubriqueDesc->titre . ' - ' . $produitDesc->titre;
                }
                if ($rubrique->id)
                {
                    $rubriqueDesc = new Rubriquedesc();
                    $rubriqueDesc->charger($rubrique->id);
                    $titre = $nomsite->valeur . ' - ' . $rubriqueDesc->titre;
                }
                if ($contenu)
                {
                    $contenuDesc = new Contenudesc();
                    $contenuDesc->charger($contenu->id);
                    $titre = $nomsite->valeur . ' - ' . $contenuDesc->titre;
                }
                if ($dossier)
                {
                    $dossierDesc = new Dossierdesc();
                    $dossierDesc->charger($dossier->id);
                    $titre = $nomsite->valeur . ' - ' . $dossierDesc->titre;
                }

                if (!$letitle && $titre != "")
                {
                    $temp = str_replace("#TITLE", $titre, $temp);
                    $letitle = true;
                }

                $rubdefautdesc->charger_desc($rubdefaut->id, $lang);

                if (!$letitle && $rubdefautdesc->title != "")
                {
                    $temp = str_replace("#TITLE", $rubdefautdesc->title, $temp);
                    $letitle = true;
                }
                if (!$lameta && $rubdefautdesc->metadesc != "")
                {
                    $temp = str_replace("#META", "$rubdefautdesc->metadesc", $temp);
                    $lameta = true;
                }
                if (!$lakeyword && $rubdefautdesc->metakeyword != "")
                {
                    $temp = str_replace("#KEYWORD", "$rubdefautdesc->metakeyword", $temp);
                    $lakeyword = true;
                }
                if (!$unautre && $rubdefautdesc->autre != "")
                {
                    $temp = str_replace("#AUTRE", "$rubdefautdesc->autre", $temp);
                    $unautre = true;
                }
            } */
        }
        if (!$letitle)
        {
            $temp = str_replace("#TITLE", "", $temp);
            //$letitle = true;
        }
        if (!$lameta)
        {
            $temp = str_replace("#META", "", $temp);
            //$lameta = true;
        }
        if (!$lakeyword)
        {
            $temp = str_replace("#KEYWORD", "", $temp);
            //$lakeyword = true;
        }
        if (!$unautre)
        {
            $temp = str_replace("#AUTRE", "", $temp);
            //$unautre = true;
        }

        switch ($lang)
        {
            case 1:
                $langCode = "fr";
                break;
            case 2:
                $langCode = "en";
                break;
            case 3:
                $langCode = "es";
                break;
            default:
                $langCode = "fr";
                break;
        }
        $temp = str_replace("#LNGCODE", "$langCode", $temp);
        
        if (!$letitle) return "";

        return $temp;
    }
}

class Titlemetadesc extends PluginsClassiques
{
    var $id;
    var $titlemeta;
    var $title;
    var $metadesc;
    var $metakeyword;
    var $autre;
    var $lang;

    const TABLE = "titlemetadesc";

    var $table = self::TABLE;

    var $bddvars = array("id", "titlemeta", "title", "metadesc", "metakeyword", "autre", "lang");

    function Titlemetadesc()
    {
        $this->PluginsClassiques();
    }

    function init()
    {
        $cnx = new Cnx();
        $query = "CREATE TABLE `" . self::TABLE .  "` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `titlemeta` INT(11) NOT NULL,
			  `title` TEXT NOT NULL,
			  `metadesc` TEXT NOT NULL,
			  `metakeyword` TEXT NOT NULL,
			  `autre` TEXT NOT NULL,
			  `lang` INT(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			) AUTO_INCREMENT=1 ;";
        $this->query($query, $cnx->link);
    }

    function add()
    {
        if (empty($this->titlemeta))
            return 0;
        else
            return parent::add();
    }

    function charger_desc($var, $varlang)
    {
        return $this->getVars("select * from " . self::TABLE . " where titlemeta=\"" . $var . "\" AND lang=\"" . $varlang . "\"");
    }
}