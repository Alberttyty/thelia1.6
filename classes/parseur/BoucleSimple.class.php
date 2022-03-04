<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/

require_once __DIR__ . "/../../fonctions/autoload.php";

class BoucleSimple extends PexElement
{
    public $nom;
    public $args;
    public $contenu;
    public $est_vide;
    public $variables;

    private $compteur = 1;

    function __construct($nom)
    {
        $this->nom = $nom;

        $this->modules = null;
        $this->est_vide = true;
        $this->variables = array();
    }

    function type()
    {
        return PexToken::TYPE_BOUCLE_SIMPLE;
    }

    function set_args($args)
    {
        $this->args = $args;
        $type_boucle = ucfirst(strtolower(lireTag($args, 'type')));

        if (DEBUG_EVAL) Analyse::echo_debug($this->nom, ": $type_boucle");
    }

    function ajouter($data)
    {
        $this->contenu = $data;
    }

    // Evaluer la boucle en utilisant la fonction classique
    function evaluer_boucle_classique($type_boucle, $args)
    {
        $var_template = '';
        $this->compteur = 1;

        // HACK: Si la liste des variables contient une variable conditionelle, assurer le traitement
        // de ces variables. Voir aussi la methode replace() de la classe PexElement
        foreach(Parseur::$VARIABLES_CONDITIONNELLES as $varcond) {
  	        if (in_array($varcond, $this->variables)) {
  	            if ($var_template != '') $var_template .= PexToken::COUPLE_SEP;
  	            $var_template .= '__VARCOND__'.$varcond.'__' . PexToken::ASSIGN_SEP . '#' . $varcond . '[1][0]';
  	        }
        }

        usort($this->variables, array("Analyse", "strlen_cmp"));

        foreach($this->variables as $var) {
            if ($var_template != '') $var_template .= PexToken::COUPLE_SEP;
            $var_template .= $var . PexToken::ASSIGN_SEP . '#' . $var;
        }

        // PexToken::START_MARK permet de déterminer si la boucle modifie ou remplace le texte
        // indiqué dans la boucle (plugin notation et commentaires)
        $var_template = PexToken::START_MARK . $var_template . PexToken::ITER_SEP;

        if (DEBUG_EVAL) Analyse::echo_debug("appel boucle exec $type_boucle, args='$args', var_template='$var_template'");

        // Appel du boucle_exec() de base de Thélia
        $valued_text = $this->boucle_exec(strtoupper($type_boucle), $args, $var_template);

        if (DEBUG_EVAL) Analyse::echo_debug("$this->nom: valued template='$valued_text'");

        if (trim($valued_text) != '') {
            $boucle_subst = new EvalBoucle();

            // Parse $texte to extract substitutions
            $rows = explode(PexToken::ITER_SEP, $valued_text);

            // Compter le nombre de resultats
            $nbres = count($rows);
            foreach($rows as $row) if ($row == '') $nbres--;

            foreach($rows as $row) {
              	if ($row == '') continue;

              	$iteration = new IterationBoucle();

              	if (DEBUG_EVAL) Analyse::echo_debug("row: '$row'");

              	if ($row[0] != PexToken::START_MARK) {
                		$start_pos = strpos($row, PexToken::START_MARK);

                		if (DEBUG_EVAL) Analyse::echo_debug("Start mark at pos '$start_pos'");

                		if ($start_pos === false) {
                  			if (DEBUG_EVAL) Analyse::echo_debug("Texte remplacé par '$row'");

                  			$iteration->remplacement = $row;
                  			$boucle_subst->ajoutIteration($iteration);

                  			// On n'examine pas la suite
                  			continue;
                		}
                		else {
                  			if (DEBUG_EVAL) Analyse::echo_debug("Texte modifié. prefixe: ".substr($row, 0, $start_pos));
                  			// Retenir le prefixe, qui sera ajouté lors des substitutions
                  			$iteration->prefixe = substr($row, 0, $start_pos);
                  			// Continuer , et examiner le reste.
                  			$row = substr($row, $start_pos + 1);
                		}
              	}
              	else {
                		// Enlever la marque de début
                		$row = substr($row, 1);

                		// S'il ne reste plus rien, on ne va pas plus loin, ça ne sert à rien.
                		// if ($row == '') continue;
              	}

        				$vars = explode(PexToken::COUPLE_SEP, $row);
        				$line_vars = [];

        				foreach($vars as $varval) {
          					if (DEBUG_EVAL) Analyse::echo_debug("varval: '$varval'");

          					list($var, $value) = explode(PexToken::ASSIGN_SEP, $varval);

          					if (DEBUG_EVAL) Analyse::echo_debug("$var=$value");

          					$iteration->ajoutVarVal($var, $value);
        				}

        				// Ajouter le compteur
        				$iteration->ajoutVarVal('__COMPTEUR__', $this->compteur++);

        				// Ajouter le nombre de resultats total
        				$iteration->ajoutVarVal('__NOMBRE__', $nbres);

        				$boucle_subst->ajoutIteration($iteration);
            }
        }
        else {
          	// la boucle est vide
          	$boucle_subst = false;
        }

        if (DEBUG_EVAL) Analyse::echo_debug("type_boucle=$type_boucle: boucle_subst: ",$boucle_subst, $boucle_subst === false ? 'FALSE' : '');

        return $boucle_subst;
    }

    function evaluer(&$substitutions = [])
    {
        if (DEBUG_EVAL) Analyse::echo_debug("Eval boucle simple $this->nom");

       // Bug signalé par tetedelard
       // Réinitialiser l'état (la boucle peut-être placée dans une boucle qui implique des iterations,
       // et influe sur les paramètres de cette boucle, et donc sur ses résultats.
       $this->est_vide = true;

       $val = '';

       // Effectuer les substitutions dans les arguments de la boucle a exécuter
       // avec les valeurs de la présente boucle.
       $args = $this->replace($substitutions, $this->args);

       /*
       if (DEBUG_EVAL) {
            Analyse::echo_debug("Eval boucle simple $this->nom, args=$this->args, subst_args=$args, Substitutions: \n",
            $substitutions,
            'variables:,
            $this->variables);
        }
        */
        $type_boucle = ucfirst(strtolower(lireTag($args, 'type')));
        $boucle_subst = $this->evaluer_boucle_classique($type_boucle, $args);

    		if (DEBUG_EVAL) Analyse::echo_debug("Eval boucle simple $this->nom, subst: ",$boucle_subst === false ? 'FALSE' : $boucle_subst, " est vide: ",$this->est_vide === false ? 'FALSE' : "TRUE");

    		if ($boucle_subst !== false) {
      			if (DEBUG_EVAL) Analyse::echo_debug("Boucle simple $this->nom n'est plus vide.");

      			// Si boucle_subst es définie, alors la boucle n'est pas vide.
      			$this->est_vide = false;

      			// Evaluer la présente boucle.
      			foreach($boucle_subst->iterations as $iteration) {
        				if (DEBUG_EVAL) {
                    Analyse::echo_debug("eval: type=".$this->contenu->type()."\nsubst: ", $iteration, "\ncontenu: ");
                    $this->contenu->imprimer();
                }

        				if ($iteration->prefixe !== false) {
          					// La boucle a place quelque chose avant le texte.
          					// On l'ajoute nous aussi.
          					$val .= $iteration->prefixe;
          					if (DEBUG_EVAL) Analyse::echo_debug("eval: prefixe=$iteration->prefixe");
        				}

        				if ($iteration->remplacement !== false) {
          					// La boucle a remplacé le texte qu'on lui a passé par un autre
          					// -> on retourne simplement ce texte, sans faire d'autres évaluations
          					$val .= $iteration->remplacement;
          					if (DEBUG_EVAL) Analyse::echo_debug("eval: remplacement=$iteration->remplacement");
        				}
        				else {
          					if (DEBUG_EVAL) Analyse::echo_debug("eval: evaluation contenu");
          					$val .= $this->contenu->evaluer($iteration->varval);
        				}
      			}

      			// Si la boucle n'est pas vide, mais n'a retourné aucune itération,
      			// il faut tout de même évaluer son contenu.
      			// FIXME: retiré, car on ajoute toujours une iteration par tour de boucle
      			// cf. ajoutIteration()
      			// if (count($boucle_subst->iterations) == 0) $val .= $this->contenu->evaluer();
    		}

    		if (DEBUG_EVAL) Analyse::echo_debug("boucle=$type_boucle vide:", $this->est_vide ? "Oui" : "Non");

        return $val;
    }

  	function boucle_exec($type_boucle, $args, $texte, $nom_boucle = "")
    {
    		global $page;

    		$variables="";
    		$res = "";

  			$exec_boucle = 1;

  			//$param = array(&$type_boucle, &$args, &$texte, &$nom_boucle, &$exec_boucle);
  			//ActionsModules::instance()->appel_module( "avantboucle", $param);

  			if ($exec_boucle) {
    			  switch($type_boucle) {
        			 	case 'RUBRIQUE' : $res .= boucleRubrique($texte, $args); break;
        			 	case 'DOSSIER' : $res .= boucleDossier($texte, $args); break;
        			 	case 'CONTENU' : $res .= boucleContenu($texte, $args); break;
        			 	case 'CONTENUASSOC' : $res .= boucleContenuassoc($texte, $args); break;
        			 	case 'PRODUIT' : $res .= boucleProduit($texte, $args); break;
        			 	case 'PAGE' : $res .= bouclePage($texte, $args); break;
        			 	case 'PANIER' : $res .= bouclePanier($texte, $args); break;
        			 	case 'QUANTITE' : $res .= boucleQuantite($texte, $args); break;
        			 	case 'CHEMIN' : $res .= boucleChemin($texte, $args); break;
        			 	case 'CHEMINDOS' : $res .= boucleChemindos($texte, $args); break;
        			 	case 'PAIEMENT' : $res .= bouclePaiement($texte, $args); break;
        			 	case 'ADRESSE' : $res .= boucleAdresse($texte, $args); break;
        			 	case 'VENTEADR' : $res .= boucleVenteadr($texte, $args); break;
        			 	case 'COMMANDE' : $res .= boucleCommande($texte, $args); break;
        			 	case 'VENTEPROD' : $res .= boucleVenteprod($texte, $args); break;
        			 	case 'IMAGE' : $res .= boucleImage($texte, $args); break;
        			 	case 'DOCUMENT' : $res .= boucleDocument($texte, $args); break;
        			 	case 'ACCESSOIRE' : $res .= boucleAccessoire($texte, $args); break;
        			 	case 'TRANSPORT' : $res .= boucleTransport($texte, $args); break;
        			 	case 'PAYS' : $res .= bouclePays($texte, $args); break;
        			 	case 'CARACTERISTIQUE' : $res .= boucleCaracteristique($texte, $args); break;
        			 	case 'CARACDISP' : $res .= boucleCaracdisp($texte, $args); break;
        			 	case 'CARACVAL' : $res .= boucleCaracval($texte, $args); break;
        			 	case 'DEVISE' : $res .= boucleDevise($texte, $args); break;
        			 	case 'CLIENT' : $res .= boucleClient($texte, $args); break;
        			 	case 'DECLINAISON' : $res .= boucleDeclinaison($texte, $args); break;
        			 	case 'DECLIDISP' : $res .= boucleDeclidisp($texte, $args); break;
        			 	case 'DECVAL' : $res .= boucleDecval($texte, $args); break;
        	 			case 'RSS' : $res .= boucleRSS($texte, $args); break;
        	 			case 'STOCK' : $res .= boucleStock($texte, $args); break;
        	 			case 'PAGERUBRIQUE' : $res .= bouclePagerubrique($texte, $args); break;
        	 			case 'RAISON' : $res .= boucleRaison($texte, $args); break;
        	 			case 'TVA' : $res .= boucleTva($texte, $args); break;
        	 			case 'LANGUE' : $res .= boucleLangue($texte, $args); break;
        	 			case 'REPRISEPAIEMENT' : $res .= boucleReprisePaiement($texte, $args); break;
        	 			default: $res.= $this->moduleBoucle($type_boucle, $texte, $args); break;
    			  }
        }
  			else $res = $texte;

  			//$param = array(&$type_boucle, &$args, &$res, &$nom_boucle);
  			//ActionsModules::instance()->appel_module( "apresboucle", $param);

  			return $res;
  	}

  	function moduleBoucle($type_boucle, $texte, $args)
    {
    		try {
      			$modules = new Modules();

      			if ($modules->charger(strtolower($type_boucle)) && $modules->actif) {
        				$instance = ActionsModules::instance()->instancier($modules->nom);
        				if (method_exists($instance, 'boucle')) return $instance->boucle($texte, $args);
      			}
    		}
        catch (Exception $ex) {}

    		return '';
  	}

    function imprimer()
    {
        Analyse::echo_debug("[DEBUT $this->nom, args: ", $this->args, "]");
        $this->contenu->imprimer();
        Analyse::echo_debug("[FIN $this->nom]");
    }
}

?>
