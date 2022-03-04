<?php

// -----------------------------------------------
// Cryptographp v1.4
// (c) 2006-2007 Sylvain BRISON 
//
// www.cryptographp.com 
// cryptographp@alphpa.com 
//
// Licence CeCILL modifi�e
// => Voir fichier Licence_CeCILL_V2-fr.txt)
// -----------------------------------------------


// -------------------------------------
// Configuration du fond du cryptogramme      
// -------------------------------------

require_once realpath(dirname(__FILE__)) . "/../../fonctions/mutualisation.php";
include_once realpath(dirname(__FILE__)) . "/../../classes/Variable.class.php";
include_once realpath(dirname(__FILE__)) . "/../../client/plugins/formulairecontact/formulairecontact_fonctions.php";

$cryptograph_largeur = new Variable();
$cryptograph_largeur->charger("cryptograph_largeur");
$cryptwidth  = $cryptograph_largeur->valeur;  // Largeur du cryptogramme (en pixels)
$cryptograph_hauteur = new Variable();
$cryptograph_hauteur->charger("cryptograph_hauteur");
$cryptheight = $cryptograph_hauteur->valeur;  // Hauteur du cryptogramme (en pixels)

        
$cryptograph_bg = new Variable();
$cryptograph_bg->charger("cryptograph_bg");  
$bg = explode(',',convertColor($cryptograph_bg->valeur));
$bgR  = $bg[0];       //238 Couleur du fond au format RGB: Red (0->255)
$bgG  = $bg[1];       //89 Couleur du fond au format RGB: Green (0->255)
$bgB  = $bg[2];       //7 Couleur du fond au format RGB: Blue (0->255)

$bgclear = false;     // Fond transparent (true/false)
                     // Uniquement valable pour le format PNG
                                         
$cryptograph_img_fond = new Variable();
$cryptograph_img_fond->charger("cryptograph_img_fond");
if($cryptograph_img_fond->valeur!="") $bgimg = $cryptograph_img_fond->valeur;
else $bgimg="";
// Le fond du cryptogramme peut-�tre une image  
// PNG, GIF ou JPG. Indiquer le fichier image
// Exemple: $fondimage = 'photo.gif';
// L'image sera redimensionn�e si n�cessaire
// pour tenir dans le cryptogramme.
// Si vous indiquez un r�pertoire plut�t qu'un 
// fichier l'image sera prise au hasard parmi 
// celles disponibles dans le r�pertoire

$bgframe = false;    // Ajoute un cadre de l'image (true/false)


// ----------------------------
// Configuration des caract�res
// ----------------------------

// Couleur de base des caract�res  
$cryptograph_char = new Variable();
$cryptograph_char->charger("cryptograph_char");
$char = explode(',',convertColor($cryptograph_char->valeur));
$charR = $char[0];    //255 Couleur des caract�res au format RGB: Red (0->255)
$charG = $char[1];    //255 Couleur des caract�res au format RGB: Green (0->255)
$charB = $char[2];     //255 Couleur des caract�res au format RGB: Blue (0->255)

$charcolorrnd = false;      // Choix al�atoire de la couleur.
$charcolorrndlevel = 3;    // Niveau de clart� des caract�res si choix al�atoire (0->4)
                           // 0: Aucune s�lection
                           // 1: Couleurs tr�s sombres (surtout pour les fonds clairs)
                           // 2: Couleurs sombres
                           // 3: Couleurs claires
                           // 4: Couleurs tr�s claires (surtout pour fonds sombres)

$charclear = 10;   // Intensit� de la transparence des caract�res (0->127)
                  // 0=opaques; 127=invisibles
	                // interessant si vous utilisez une image $bgimg
	                // Uniquement si PHP >=3.2.1

// Polices de caract�res

//$tfont[] = 'Alanden_.ttf';       // Les polices seront al�atoirement utilis�es.
//$tfont[] = 'bsurp___.ttf';       // Vous devez copier les fichiers correspondants
//$tfont[] = 'ELECHA__.TTF';       // sur le serveur.
$tfont[] = 'dustismo_bold.ttf';         // Ajoutez autant de lignes que vous voulez   
//$tfont[] = 'RASCAL__.TTF';       // Respectez la casse ! 
//$tfont[] = 'SCRAWL.TTF';  
//$tfont[] = 'WAVY.TTF';   


// Caracteres autoris�s
// Attention, certaines polices ne distinguent pas (ou difficilement) les majuscules 
// et les minuscules. Certains caract�res sont faciles � confondre, il est donc
// conseill� de bien choisir les caract�res utilis�s.

$charel = 'ABCEFGHKLMNPRTWXYZ';       // Caract�res autoris�s

$crypteasy = false;       // Cr�ation de cryptogrammes "faciles � lire" (true/false)
                         // compos�s alternativement de consonnes et de voyelles.

$charelc = 'BCFGHKLMNPRTVWXZ';   // Consonnes utilis�es si $crypteasy = true
$charelv = 'AEIUY';              // Voyelles utilis�es si $crypteasy = true

$difuplow = false;          // Diff�rencie les Maj/Min lors de la saisie du code (true, false)

$charnbmin = 4;         // Nb minimum de caracteres dans le cryptogramme
$charnbmax = 4;         // Nb maximum de caracteres dans le cryptogramme

$charspace = 24;        // Espace entre les caracteres (en pixels)

$cryptograph_char_min = new Variable();
$cryptograph_char_min->charger("cryptograph_char_min");
if($cryptograph_char_min->valeur!="") $charsizemin = $cryptograph_char_min->valeur;
else $charsizemin="22";

$cryptograph_char_max = new Variable();
$cryptograph_char_max->charger("cryptograph_char_max");
if($cryptograph_char_max->valeur!="") $charsizemax = $cryptograph_char_max->valeur;
else $charsizemax="24";

$charanglemax  = 20;     // Angle maximum de rotation des caracteres (0-360)
$charup   = false;        // D�placement vertical al�atoire des caract�res (true/false)

// Effets suppl�mentaires

$cryptgaussianblur = false; // Transforme l'image finale en brouillant: m�thode Gauss (true/false)
                            // uniquement si PHP >= 5.0.0
$cryptgrayscal = false;     // Transforme l'image finale en d�grad� de gris (true/false)
                            // uniquement si PHP >= 5.0.0

// ----------------------
// Configuration du bruit
// ----------------------

$noisepxmin = 0;      // Bruit: Nb minimum de pixels al�atoires
$noisepxmax = 0;      // Bruit: Nb maximum de pixels al�atoires

$noiselinemin = 0;     // Bruit: Nb minimum de lignes al�atoires
$noiselinemax = 0;     // Bruit: Nb maximum de lignes al�atoires

$nbcirclemin = 0;      // Bruit: Nb minimum de cercles al�atoires 
$nbcirclemax = 0;      // Bruit: Nb maximim de cercles al�atoires

$noisecolorchar  = 3;  // Bruit: Couleur d'ecriture des pixels, lignes, cercles: 
                       // 1: Couleur d'�criture des caract�res
                       // 2: Couleur du fond
                       // 3: Couleur al�atoire
                       
$brushsize = 1;        // Taille d'ecriture du princeaiu (en pixels) 
                       // de 1 � 25 (les valeurs plus importantes peuvent provoquer un 
                       // Internal Server Error sur certaines versions de PHP/GD)
                       // Ne fonctionne pas sur les anciennes configurations PHP/GD

$noiseup = false;      // Le bruit est-il par dessus l'ecriture (true) ou en dessous (false) 

// --------------------------------
// Configuration syst�me & s�curit�
// --------------------------------

$cryptformat = "gif";   // Format du fichier image g�n�r� "GIF", "PNG" ou "JPG"
				                // Si vous souhaitez un fond transparent, utilisez "PNG" (et non "GIF")
				                // Attention certaines versions de la bibliotheque GD ne gerent pas GIF !!!

$cryptsecure = "md5";    // M�thode de crytpage utilis�e: "md5", "sha1" ou "" (aucune)
                         // "sha1" seulement si PHP>=4.2.0
                         // Si aucune m�thode n'est indiqu�e, le code du cyptogramme est stock� 
                         // en clair dans la session.
                       
$cryptusetimer = 0;        // Temps (en seconde) avant d'avoir le droit de reg�n�rer un cryptogramme

$cryptusertimererror = 3;  // Action � r�aliser si le temps minimum n'est pas respect�:
                           // 1: Ne rien faire, ne pas renvoyer d'image.
                           // 2: L'image renvoy�e est "images/erreur2.png" (vous pouvez la modifier)
                           // 3: Le script se met en pause le temps correspondant (attention au timeout
                           //    par d�faut qui coupe les scripts PHP au bout de 30 secondes)
                           //    voir la variable "max_execution_time" de votre configuration PHP

$cryptusemax = 1000;  // Nb maximum de fois que l'utilisateur peut g�n�rer le cryptogramme
                      // Si d�passement, l'image renvoy�e est "images/erreur1.png"
                      // PS: Par d�faut, la dur�e d'une session PHP est de 180 mn, sauf si 
                      // l'hebergeur ou le d�veloppeur du site en ont d�cid� autrement... 
                      // Cette limite est effective pour toute la dur�e de la session. 
                      
$cryptoneuse = false;  // Si vous souhaitez que la page de verification ne valide qu'une seule 
                       // fois la saisie en cas de rechargement de la page indiquer "true".
                       // Sinon, le rechargement de la page confirmera toujours la saisie.                          
                      
?>
