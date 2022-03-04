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
require_once(__DIR__ . "/autoload.php");

/* Constante définissant le type de redimensionnement */
define("IMAGE_REDIM_RATIO", 0);
define("IMAGE_REDIM_BORDURE", 1);
define("IMAGE_REDIM_RECADRE", 2);

/**
 * Compatiblité avec php 5.5
 */
if(!defined('IMG_FLIP_VERTICAL')) {
    define('IMG_FLIP_VERTICAL', 2);
}

function get_img_quality($nomvar, $min, $max, $defaut) {
    $quality = intval(Variable::lire($nomvar, $defaut));

    if ($quality < $min || $quality > $max) $quality = $defaut;

    return $quality;
}

/*
 * Redimensionne une image, en remplaçant l'image originale
 * (semble inutilisé dans Thelia)
 *
function resize($nomorig, $width){

    if (file_exists($nomorig)) {

        $image_new = traiter_image($nomorig, $width, "", "", "", "", "", "ffffff", $type) ;

        if ($image_new) {
            switch ($type) {
                case IMAGETYPE_GIF:
                    $flag = imagegif($image_new, $nomorig);
                    break;

                case IMAGETYPE_PNG:
                    $flag = imagepng($image_new, $nomorig, get_img_quality('qualite_vignettes_png', 1, 10, 7));
                    break;

                case IMAGETYPE_JPEG:
                default:
                    $flag = imagejpeg($image_new, $nomorig, get_img_quality('qualite_vignettes_jpeg', 10, 100, 75));
                    break;
            }

            imagedestroy($image_new);
        }
    }
}
*/

/*
 * Retournement vertical d'une image
 */
function effet_miroir(&$dest, &$src) {

    $w = imagesx($src);
    $h = imagesy($src);
    $alpha = 127;
    for($y=0; $y<$h; $y++) {
        for ($x=0; $x<$w; $x++) {
            $couleur = imagecolorsforindex($src, imagecolorat($src, $x, $y));
            $couleurAlpha = imagecolorallocatealpha($dest, $couleur['red'], $couleur['green'], $couleur['blue'], $alpha);
            imagesetpixel($dest, $x, ($h-$y), $couleurAlpha);
        }
        if($alpha > 1) $alpha--;
    }
}


/*
 * Redimensionnement et traitement d'une image
 */
function redim($type, $nomorig, $dest_width="", $dest_height="", $opacite="", $nb="", $miroir="", $checktype=1, $exact=0, $couleurfond="ffffff", $convertir="") {

    if($checktype == 0 || $type == "produit" || $type =="rubrique" || $type == "contenu" || $type == "dossier") {

        $nomorig = SITE_DIR . "client/gfx/photos/$type/" . $nomorig;

        if(file_exists($nomorig) && preg_match("/([^\/]*).((jpg|gif|png|jpeg))/i", $nomorig, $nsimple)) {

            $nomcache  = FICHIER_URL . "client/cache/" . $type . "/" . $dest_width . "_" . $dest_height . "_" . $opacite . "_" . $nb . "_" . $miroir . "_" . $exact . "_" . $couleurfond . "_" . $nsimple[1] . "." . $nsimple[2];

            if($convertir == "jpg") $nomcache=preg_replace('/\.(bmp|gif|png|jpeg)$/','.jpg',$nomcache);

            $pathcache = SITE_DIR."../..".$nomcache;

            if(file_exists($pathcache)
                || traiter_et_cacher_image($nomorig, $pathcache, $dest_width, $dest_height, $opacite, $nb, $miroir, $exact, $couleurfond, $convertir)) {

                return $nomcache;
            }
        }
    }

    return "";
}

/*
 * Créer dans $fichier_cache une version retraitée de l'image $fichier_original
 */
function traiter_et_cacher_image($fichier_original, $fichier_cache, $dest_width="", $dest_height="", $opacite="", $nb="", $miroir="", $exact=0, $couleurfond="ffffff", $convertir="") {

	// Traiter le fichier
  $image_new = traiter_image_convert($fichier_original, $fichier_cache, $dest_width, $dest_height, $opacite, $nb, $miroir, $exact, $couleurfond, $type, $convertir) ;
  /*
	$image_new = traiter_image($fichier_original, $dest_width, $dest_height, $opacite, $nb, $miroir, $exact, $couleurfond, $type) ;
  */
	if($image_new) {

		/*switch ($type) {
			case IMAGETYPE_GIF:
				$flag = imagegif($image_new, $fichier_cache);
				break;

			case IMAGETYPE_PNG:
				$flag = imagepng($image_new, $fichier_cache, get_img_quality('qualite_vignettes_png', 1, 10, 7));
				break;

			case IMAGETYPE_JPEG:
			default:
				$flag = imagejpeg($image_new, $fichier_cache, get_img_quality('qualite_vignettes_jpeg', 10, 100, 75));
				break;
		}

		@imagedestroy($image_new);*/

		return true;
	}

	return false;
}

/**
 * Convert a hexa decimal color code to its RGB equivalent
 *
 * @param string $hexStr (hexadecimal color value)
 * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
 * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
 * @return array or string (depending on second parameter. Returns white color if invalid hex color value)
 * @author hafees at msn dot com
 */
function hex2RGB($hexStr) {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) {
        //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) {
        //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        // Incorrect color -> return white
        $rgbArray['red'] = 0xFF;
        $rgbArray['green'] = 0xFF;
        $rgbArray['blue'] = 0xFF;
    }
    return $rgbArray;
}


/*
 * Appliquer les divers traitements à une image
 */
function traiter_image($nomorig, $dest_width, $dest_height, $opacite, $nb, $miroir, $exact, $couleurfond, &$type) {

    list($width_orig, $height_orig, $type, $attr) = getimagesize($nomorig);

    if (!$width_orig) return false;
    if ($dest_width == NULL) $dest_width = $width_orig;
    if ($dest_height == NULL) $dest_height = $height_orig;

    $width_diff = $dest_width / $width_orig;
    $height_diff = $dest_height / $height_orig;

    $delta_x = $delta_y = $border_width = $border_height = 0;

    if ($width_diff > 1 AND $height_diff > 1) {
        $next_width = $width_orig;
        $next_height = $height_orig;
        $dest_width = (intval($exact) == 1 ? $dest_width : $next_width);
        $dest_height = (intval($exact) == 1 ? $dest_height : $next_height);
    }
    else {
        if ($width_diff > $height_diff) {

            $next_height = $dest_height;
            $next_width = intval(($width_orig * $next_height) / $height_orig);

            if ($exact == IMAGE_REDIM_RECADRE) {

                $dest_ratio = $dest_width / $width_orig;
                $ho = $dest_height / $dest_ratio;
                $delta_y = ($height_orig - $ho) / 2;

                $height_orig = $ho;

                $next_width = $dest_width;
            } else if ($exact != IMAGE_REDIM_BORDURE) {
                $dest_width = $next_width;
            }
        } else {

            $next_width = $dest_width;
            $next_height = intval($height_orig * $dest_width / $width_orig);

            if ($exact == IMAGE_REDIM_RECADRE) {

                $dest_ratio = $dest_height / $height_orig;
                $wo = $dest_width / $dest_ratio;
                $delta_x = ($width_orig - $wo) / 2;

                $width_orig = $wo;

                $next_height = $dest_height;
            } else if ($exact != IMAGE_REDIM_BORDURE) {
                $dest_height = $next_height;
            }
        }
    }

    if ($exact == IMAGE_REDIM_BORDURE) {
        $border_width = intval(($dest_width - $next_width) / 2);
        $border_height = intval(($dest_height - $next_height) / 2);
    }

    $image_new = imagecreatetruecolor($dest_width, $dest_height);

    switch ($type) {
        case IMAGETYPE_GIF:
            $image_orig= imagecreatefromgif($nomorig);
            break;

        case IMAGETYPE_JPEG:
        default:
            $image_orig= imagecreatefromjpeg($nomorig);
            break;

        case IMAGETYPE_PNG:
            $image_orig = imagecreatefrompng($nomorig);
            break;
    }

    // Preparer la couleur de fond (pour bordures, transparence, miroir)
    if ($couleurfond == '') $couleurfond = 'ffffff';
    $fondrgb = hex2RGB($couleurfond);

    // Définir la couleur de fond générale
    $bgcolor = imagecolorallocate($image_new, $fondrgb['red'], $fondrgb['green'], $fondrgb['blue']);


    // Préserver la transparence des gifs et png
    if ($type != IMAGETYPE_JPEG) {
        $trnprt_indx = imagecolortransparent($image_orig);

        // If we have a specific transparent color
        if ($trnprt_indx >= 0) {

            // Get the original image's transparent color's RGB values
            $trnprt_color = imagecolorsforindex($image_orig, $trnprt_indx);
            // Allocate the same color in the new image resource
            $trnprt_indx = imagecolorallocate($image_new, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
            // Completely fill the background of the new image with allocated color.
            imagefill($image_new, 0, 0, $trnprt_indx);
            // Set the background color for new image to transparent
            imagecolortransparent($image_new, $trnprt_indx);
        } else {
            // Turn off transparency blending (temporarily)
            imagealphablending($image_new, false);
            // Create a new transparent color for image
            $color = imagecolorallocatealpha($image_new, 0, 0, 0, 127);
            // Completely fill the background of the new image with allocated color.
            imagefill($image_new, 0, 0, $color);
            // Restore transparency blending
            imagesavealpha($image_new, true);
            // Remplir avec la couleur de fond
            imagefill($image_new, 0, 0, $bgcolor);
        }
    } else {
        // Remplir avec la couleur de fond
        imagefill($image_new, 0, 0, $bgcolor);
    }

    if($opacite != "") {

        $opac_img = imagecreatetruecolor($width_orig, $height_orig);
        imagefill($opac_img, 0, 0, $bgcolor);
        imagecopymerge($opac_img, $image_orig, 0,0,0,0, $width_orig, $height_orig, $opacite);
        $image_orig = $opac_img;
    }

    // Redimensionnement, avec ajout éventuel de bordures.
    imagecopyresampled($image_new, $image_orig, $border_width, $border_height, $delta_x, $delta_y, $next_width, $next_height, $width_orig, $height_orig);

    // Noir et blanc
    if($nb != "") {

        imagetruecolortopalette($image_new, false, 256);
        $total = ImageColorsTotal($image_new);

        for( $i=0; $i<$total; $i++) {
            $old = ImageColorsForIndex($image_new, $i);
            $commongrey = (int)(($old['red'] + $old['green'] + $old['blue']) / 3);
            ImageColorSet($image_new, $i, $commongrey, $commongrey, $commongrey);
        }
    }

    if($miroir != "") {

        $mh = intval($miroir) == 1 ? 50 : intval($miroir);

        $largeur = imagesx($image_new);
        $hauteur = imagesy($image_new);

        $temporaireUn  = imagecreatetruecolor($largeur, $mh);
        $temporaireDeux = imagecreatetruecolor($largeur, $mh);
        $resultat = imagecreatetruecolor($largeur, $hauteur+$mh);

        imagefill($resultat, 1, 1, $bgcolor);
        imagefill($temporaireDeux, 1, 1, $bgcolor);

        imagecopy ($resultat, $image_new, 0, 0, 0, 0, $largeur, $hauteur);
        imagecopy ($temporaireUn, $image_new, 0, 0, 0, $hauteur-$mh, $largeur, $mh);

        effet_miroir($temporaireDeux, $temporaireUn);

        imagecopy ($resultat, $temporaireDeux, 0, $hauteur, 0, 0, $largeur, $mh);
        $image_new = $resultat;
    }

    return $image_new;
}

function traiter_image_convert($nomorig, $nomcache, $dest_width, $dest_height, $opacite, $nb, $miroir, $exact, $couleurfond, &$type, $convertir) {
  
	  $quality="60";
    $sampling_factor="1x1";
    $alpha="";
    $grayscale="-colorspace sRGB";
    $flip="";
    $detourer="";
    $src=escapeshellcmd($nomorig);
    $dest=escapeshellcmd($nomcache);

    if($opacite != "" && $opacite) {
    	$opacite=intval($opacite);
        if(0<=$opacite && $opacite<=100)
        	$alpha='-alpha on -channel Alpha -evaluate set '.$opacite.'%';
    }

    if($nb != "") $grayscale="-colorspace Gray";

    if($exact == 2) $resize="-resize \"%widthx%height^\" -gravity Center -crop %widthx%height+0+0 +repage";
  	else $resize="-resize %widthx%height";

    $resize = str_replace(array('%width', '%height'),array($dest_width,$dest_height),$resize);

    if($miroir != "") $flip="-flip";

  	if($couleurfond != "") {
    	$detourer.='\ -background white -bordercolor white -border 1x1 \ -alpha set -channel RGBA -fuzz "10%" -trim +repage \ ';
        if($convertir=="jpg") $detourer.=' -flatten \ ';
    } elseif($convertir=="jpg") {
        $detourer='\ -background "#ffffff" -alpha remove \ ';
    }

    $commande='convert -quality %quality -sampling-factor %sampling -strip %flip %detourer %resize %alpha %grayscale %flip %src %dest';
    $commande = str_replace(
				array('%quality', '%sampling', '%width', '%height', '%alpha', '%grayscale', '%flip', '%detourer', '%src', '%dest', '%resize'),
				array(
				  	$quality,
				  	$sampling_factor,
					$dest_width,
					$dest_height,
					$alpha,
					$grayscale,
					$flip,
          			$detourer,
					$src,
					$dest,
          			$resize
				),$commande);

    exec($commande);

    $format_fichier = trim(exec('identify -format %m '.$dest));
    switch ($format_fichier) {

    	case "JPEG":
            $fsize = filesize($dest);
      		if ($fsize < (150*1024)) $commande='jpegtran -copy none -optimize -outfile %dest %dest';
      		else $commande='jpegtran -copy none -progressive -outfile %dest %dest';
            $commande = str_replace(
    					array('%dest'),
    					array(
    						$dest
    					),$commande);
            //echo "<br>".$commande."<br>";
            exec($commande);
            //echo "<br>";
          	break;

        default:
        	break;

    }

    //debug
    // echo $commande;
	return true;
}
?>
